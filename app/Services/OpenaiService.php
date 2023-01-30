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
}
