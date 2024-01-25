<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CompanyService
{
    public function getCompany(string $uuid)
    {
        $token = config('services.micro_02.token');

        $endpoint = config('services.micro_02.baseUrl')."/api/evaluations/{$uuid}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => $token
        ])->get($endpoint);

        if (!$response->ok()) {

            return response()->json([
                'message' => 'Invalid Evaluation'
            ]);
        }

        return $response->json();
    }
}
