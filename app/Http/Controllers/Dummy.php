<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\RequestCache;

class Dummy extends Controller
{
    protected $faker;
    protected $hasPagination = false;
    protected $perPage = false;
    protected $currentPage = false;
    protected $maxPages = false;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function hello(Request $request)
    {
        $bodyRaw = $request->getContent();
        if (empty($bodyRaw)) {
            return response()->noContent();
        }
        if (strlen($bodyRaw) > 1024 * 100) {
            return response("Nope! That's WAY too much for me to handle!!", 413);
        }

        try {
            $body = json_decode($bodyRaw, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        $query = $request->query();
        ksort($query);

        $token = $request->bearerToken() ?? $request->header('X-API-Token');

        $fingerPrint = [
            $token,
            $request->method(),
            $request->path(),
            $query,
            $bodyRaw,
        ];
        $fingerPrint = hash('sha256', json_encode($fingerPrint));

        $hasCache = RequestCache::where('fingerprint', $fingerPrint)->first() ?? false;

        if ($hasCache) {
            $cacheContent = json_decode($hasCache->content);
            $cacheContent->headers['__from_cache'] = true;
            return response()->json($cacheContent->body, $cacheContent->status, $cacheContent->headers);
        }

        $instructions = isset($body->__instructions) && is_object($body->__instructions)
            ? $body->__instructions
            : null;

        $responseStatus = 200;
        $responseHeaders = [];

        $returnBody = $body;

        if (!empty($instructions)) {
            if (!empty($instructions->delay)) {
                $delay = min((int) $instructions->delay, 5000);
                usleep($delay * 1000);
            }

            if (!empty($instructions->status)) {
                $responseStatus = $instructions->status;
            }

            if (!empty($instructions->headers) && is_array($instructions->headers)) {
                foreach ($instructions->headers as $key => $value) {
                    if (is_scalar($value)) {
                        $responseHeaders[$key] = $value;
                    }
                }
            }

            if (!empty($instructions->body)) {
                $returnBody = $instructions->body;
            }

            if (!empty($instructions->max_pages)) {
                $this->hasPagination = true;
                $this->maxPages = $instructions->max_pages;
            }
        }

        if (isset($_GET['page'])) {
            $this->hasPagination = true;
        }

        $returnBody = $this->applyRepeats($returnBody);

        if ($this->hasPagination) {
            $this->currentPage = (isset($_GET['page'])) ? $_GET['page'] : 1;

            if (!$this->perPage) {
                $this->perPage = 20;
            }
            $returnBody->meta = [
                "page" => $this->currentPage,
                "per_page" => $this->perPage,
                "total_items" => $this->maxPages * $this->perPage,
                "total_pages" => $this->maxPages,
                "from" => ($this->currentPage - 1) * $this->perPage + 1,
                "to" => $this->currentPage * $this->perPage,
            ];
        }

        if (empty($instructions->no_cache)) {
            $cacheContent = [
                'body' => $returnBody,
                'status' => $responseStatus,
                'headers' => $responseHeaders,
            ];

            RequestCache::upsert(
                [
                    [
                        'token' => $token,
                        'fingerprint' => $fingerPrint,
                        'content' => json_encode($cacheContent),
                    ]
                ],
                uniqueBy: ['fingerprint'],
                update: ['content']
            );
        }

        return response()->json($returnBody, $responseStatus, $responseHeaders);
    }

    protected function applyRepeats($data, $repeatCount = 0, ?string $parentKey = null)
    {
        if (is_array($data)) {
            $data = (object) $data;
        }

        if (is_object($data)) {
            foreach (get_object_vars($data) as $k => $v) {
                if (is_object($v) && property_exists($v, '__repeat')) {
                    $nextDepth = $repeatCount + 1;
                    if ($nextDepth > 2) {
                        $data->$k = 'Nesting repeats deeper than 2 is not allowed';
                        continue;
                    }

                    $n = $this->normalizeRepeat($v->__repeat);

                    if (!$this->perPage) {
                        $this->perPage = $n;
                    }

                    $targetKey = Str::plural($k);

                    $template = $this->stripInlineMeta($v);
                    $items = [];
                    $index = 0;
                    for (
                        $i = 0;
                        $i < $n;
                        $i++
                    ) {
                        $item = $this->cloneValue($template);
                        $item = $this->applyRepeats($item, $nextDepth, $k);

                        $item = $this->applyFaker($item, $index);

                        $items[] = $item;
                    }

                    unset($data->$k);
                    $data->$targetKey = $items;
                    continue;
                }

                $data->$k = $this->applyRepeats($v, 0, $k);
            }

            return $data;
        }

        return $data;
    }

    private
    function normalizeRepeat($val): int
    {
        if (!is_numeric($val)) {
            return 0;
        }
        $n = (int) $val;
        if ($n < 0) {
            $n = 0;
        }

        return min($n, 20);
    }

    private
    function stripInlineMeta($obj)
    {
        // Return a copy of $obj without __repeat / __as
        $copy = $this->cloneValue($obj);
        if (is_object($copy)) {
            if (property_exists($copy, '__repeat')) {
                unset($copy->__repeat);
            }
            if (property_exists($copy, '__as')) {
                unset($copy->__as);
            }
        }

        return $copy;
    }

    private
    function cloneValue($v)
    {
        // Simple deep copy for stdClass/arrays
        return json_decode(json_encode($v));
    }

    protected
    function generateUuidFromIndex(int $index): string
    {
        // Deterministic UUID-like string based on index
        return sprintf('00000000-0000-0000-0000-%012d', $index);
    }

    protected
    function applyFaker($data, &$index = 0)
    {
        if (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = $this->applyFaker($value, $index);
            }
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->applyFaker($value, $index);
            }
        } elseif (is_string($data) && str_starts_with($data, '?')) {
            $type = substr($data, 1);

            if ($type === 'counter') {
                $return = $index;
                $index++;
            } elseif ($type === 'counterUuid') {
                $return = $this->generateUuidFromIndex($index);
                $index++;
            } else {
                $return = $this->getFakerValue($type);
            }

            return $return;
        }

        return $data;
    }

    protected
    function getFakerValue($type)
    {
        return match ($type) {
            // Personal Info
            'name' => $this->faker->name(),
            'firstName' => $this->faker->firstName(),
            'lastName' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'username' => $this->faker->userName(),
            'phone' => $this->faker->phoneNumber(),

            // Company
            'company' => $this->faker->company(),
            'jobTitle' => $this->faker->jobTitle(),

            // Address
            'address' => $this->faker->address(),
            'street' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'zip' => $this->faker->postcode(),
            'country' => $this->faker->country(),

            // Numbers
            'number' => $this->faker->numberBetween(1, 10000),
            'numberSmall' => $this->faker->numberBetween(1, 10),
            'numberLarge' => $this->faker->numberBetween(10000, 1000000),
            'decimal' => $this->faker->randomFloat(2, 0, 1000),
            'price' => $this->faker->randomFloat(2, 1, 999),

            // Text
            'word' => $this->faker->word(),
            'sentence' => $this->faker->sentence(),
            'paragraph' => $this->faker->paragraph(),
            'text' => $this->faker->text(200),

            // Internet
            'url' => $this->faker->url(),
            'domain' => $this->faker->domainName(),
            'ip' => $this->faker->ipv4(),
            'slug' => $this->faker->slug(),

            // Date/Time
            'date' => $this->faker->date(),
            'dateTime' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'stupidDateTime' => $this->faker->dateTime()->format('m/d/Y H:i:s'),
            'time' => $this->faker->time(),
            'timestamp' => $this->faker->unixTime(),

            // UUID & IDs
            'uuid' => $this->faker->uuid(),
            'id' => $this->faker->numberBetween(1, 100000),

            // Boolean
            'boolean' => $this->faker->boolean(),

            // Color
            'color' => $this->faker->hexColor(),
            'colorName' => $this->faker->colorName(),

            // Lorem
            'lorem' => $this->faker->sentence(),
            'loremShort' => $this->faker->words(3, true),
            'loremLong' => $this->faker->paragraph(3),

            // Credit Card (for testing only!)
            'creditCard' => $this->faker->creditCardNumber(),

            // Image URLs
            'image' => $this->faker->imageUrl(640, 480),
            'avatar' => $this->faker->imageUrl(200, 200, 'people'),

            default => '?' . $type // Return original if not found
        };
    }
}
