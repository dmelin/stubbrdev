<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ApiKeyVerificationMail;
use App\Mail\ApiKeyRecoveryMail;
use Faker\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\ApiKey;

class ApiKeyController extends Controller
{
    private $faker;

    public function __construct() {
        $this->faker = Factory::create();
    }
    public function requestKey(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $existingApiKey = $this->getKey($request->email);

        if ($existingApiKey) {
            return response()->json(['error' => 'One token per email. Those are the rules!'], 409);
        }

        $secret = $this->faker->sentence(5);
        $token = Str::uuid();

        ApiKey::create([
            'email' => $this->hashEmail($request->email),
            'token' => $token,
            'secret' => Hash::make($secret),
            'enabled' => true,
        ]);

        // Send email with code + key
        //Mail::to($request->email)->send(new ApiKeyVerificationMail($token, $secret));

        return response()->json(['message' => 'Here\'s your token!.', 'token' => $token]);
    }

    public function recoverKey(Request $request) {
        $request->validate(['email' => 'required|email']);

        $existingApiKey = $this->getKey($request->email);

        if (!$existingApiKey) {
            return response()->json(['error' => 'We seem unable to find any token at all for that email. Do not try again - we did not make a mistake here (you did, so check spelling).'], 404);
        }

        //Mail::to($request->email)->send(new ApiKeyRecoveryMail($existingApiKey->token));

        return response()->json(['message' => 'Once lost can be found! Here\'s your token.', 'token' => $existingApiKey->token]);
    }

    public function verifyKey(Request $request) {

        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'secret' => 'required',
        ]);

        $key = $this->getKey($request->email);

        if (!$key) {
            return response()->json(['error' => 'There is no token registered to this email. Did you really request one? (we do delete them if inactive for 30 days).'], 404);
        }

        if ($key->token !== $request->token) {
            return response()->json(['error' => 'We have no recollection of such token.'], 404);
        }

        if (!Hash::check($request->secret, $key->secret)) {
            return response()->json(['error' => 'Is this really your token? (wrong secret).'], 401);
        }

        if ($key->enabled) {
            return response()->json(['message' => 'This key is already enabled. But.. we enabled it, again, to make sure - just for you.']);
        }

        $key->enabled = true;
        $key->save();

        return response()->json(['message' => 'Api Key verified - welcome aboard.']);
    }

    private function getKey($email) {
        return ApiKey::where('email', '=', $this->hashEmail($email))->first();
    }

    private function hashEmail($email)
    {
        return hash_hmac('sha256', strtolower(trim($email)), env('EMAIL_HASH_SECRET'));
    }

}
