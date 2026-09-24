<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class StubResponseTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->token = (string) Str::uuid();
        ApiKey::create([
            'email' => hash('sha256', 'stub-test@example.com'),
            'token' => $this->token,
            'secret' => 'secret',
            'enabled' => true,
        ]);
    }

    // call() is used instead of postJson() so GET requests can carry a body too.
    // The per-token rate limit is reset first; these tests fire calls in bursts.
    private function stub(string $path, array $body, string $method = 'POST')
    {
        Cache::forget("api_throttle_{$this->token}");

        $server = $this->transformHeadersToServerVars([
            'Authorization' => "Bearer {$this->token}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        return $this->call($method, "/api/{$path}", [], [], [], $server, json_encode($body));
    }

    public function test_plain_stub_returns_body_and_status(): void
    {
        $response = $this->stub('orders', [
            '__instructions' => ['status' => 201, 'body' => ['ok' => true]],
        ]);

        $response->assertStatus(201)->assertExactJson(['ok' => true]);
        $this->assertNull($response->headers->get('__from_cache'));
    }

    public function test_identical_requests_are_served_from_cache(): void
    {
        $body = ['__instructions' => ['body' => ['user' => ['__repeat' => 2, 'name' => '?name']]]];

        $first = $this->stub('users', $body);
        $second = $this->stub('users', $body);

        $this->assertNull($first->headers->get('__from_cache'));
        $this->assertSame('1', $second->headers->get('__from_cache'));
        $this->assertSame($first->json(), $second->json());
    }

    public function test_cached_responses_still_honour_the_delay(): void
    {
        $body = ['__instructions' => ['delay' => 300, 'body' => ['slow' => true]]];
        $this->stub('slow', $body);

        $started = microtime(true);
        $response = $this->stub('slow', $body);
        $elapsed = microtime(true) - $started;

        $this->assertSame('1', $response->headers->get('__from_cache'));
        $this->assertGreaterThanOrEqual(0.25, $elapsed, 'cached reply returned before the delay elapsed');
    }

    public function test_flaky_true_fails_about_half_the_time_with_default_codes(): void
    {
        $body = ['__instructions' => ['flaky' => true, 'body' => ['ok' => true]]];
        $outcomes = [];

        for ($i = 0; $i < 40; $i++) {
            $response = $this->stub('coin', $body);
            $this->assertNull($response->headers->get('__from_cache'), 'flaky responses must never be cached');
            $flag = $response->headers->get('__flaky');
            $outcomes[$flag] = ($outcomes[$flag] ?? 0) + 1;

            if ($flag === 'failed') {
                $this->assertContains($response->status(), [500, 502, 503, 504, 429]);
                $this->assertSame($response->status(), $response->json('code'));
                $this->assertNotEmpty($response->json('error'));
            } else {
                $this->assertSame('passed', $flag);
                $response->assertStatus(200)->assertExactJson(['ok' => true]);
            }
        }

        $this->assertArrayHasKey('failed', $outcomes);
        $this->assertArrayHasKey('passed', $outcomes);
    }

    public function test_flaky_every_nth_fails_on_schedule_with_given_codes(): void
    {
        $body = ['__instructions' => ['flaky' => ['every' => 3, 'codes' => [500, 410]], 'body' => ['ok' => true]]];
        $statuses = [];

        for ($i = 1; $i <= 6; $i++) {
            $statuses[] = $this->stub('schedule', $body)->status();
        }

        $this->assertSame(200, $statuses[0]);
        $this->assertSame(200, $statuses[1]);
        $this->assertContains($statuses[2], [500, 410]);
        $this->assertSame(200, $statuses[3]);
        $this->assertSame(200, $statuses[4]);
        $this->assertContains($statuses[5], [500, 410]);
    }

    public function test_flaky_counter_spans_different_bodies_on_the_same_path(): void
    {
        $instructions = ['flaky' => ['every' => 2, 'codes' => [503]]];

        $this->stub('pages', ['page' => 1, '__instructions' => $instructions])->assertStatus(200);
        $this->stub('pages', ['page' => 2, '__instructions' => $instructions])->assertStatus(503);
    }

    public function test_flaky_ignores_success_codes_and_adds_location_for_redirects(): void
    {
        $response = $this->stub('moved', [
            '__instructions' => ['flaky' => ['every' => 1, 'codes' => [200, 301]], 'body' => ['ok' => true]],
        ]);

        $response->assertStatus(301);
        $this->assertSame('failed', $response->headers->get('__flaky'));
        $this->assertStringEndsWith('/api/moved', $response->headers->get('Location'));
        $this->assertSame('Moved Permanently', $response->json('error'));
    }

    public function test_flaky_failures_still_wait_for_the_delay(): void
    {
        $started = microtime(true);
        $response = $this->stub('slowfail', [
            '__instructions' => ['flaky' => ['every' => 1], 'delay' => 300],
        ]);
        $elapsed = microtime(true) - $started;

        $this->assertSame('failed', $response->headers->get('__flaky'));
        $this->assertGreaterThanOrEqual(0.25, $elapsed);
    }

    public function test_get_requests_with_a_body_are_stubbed_too(): void
    {
        $response = $this->stub('users', ['__instructions' => ['body' => ['via' => 'get']]], 'GET');

        $response->assertStatus(200)->assertExactJson(['via' => 'get']);
    }
}
