<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use Illuminate\Support\Facades\Http;

class OpenaiService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;
    protected $apiKey;
    protected $orgId;

    public function __construct()
    {
        $this->baseUri = config('services.openai.base_url');
        $this->apiKey = config('services.openai.api_key');
        $this->orgId = config('services.openai.org_id');
    }

    public function CreateCompletion(string $prompt)
    {

        $response = $this->performRequest('POST', '/v1/completions', [
            "model" => 'text-davinci-003',
            "prompt" =>  $prompt,
            "max_tokens" => 1500,
            "temperature" => 0.6,
            "top_p" => 1,
            "best_of" => 1,
            "frequency_penalty" => 0,
            "presence_penalty" => 0,
        ]);

        return json_decode((string) $response);
    }
}
