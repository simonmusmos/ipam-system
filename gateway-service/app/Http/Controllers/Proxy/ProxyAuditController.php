<?php

namespace App\Http\Controllers\Proxy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyAuditController extends Controller
{
    protected $auditUrl;

    public function __construct()
    {
        $this->auditUrl = config('services.audit.base_uri') . '/api';
    }

    public function index(Request $request)
    {
        $response = Http::acceptJson()->withHeaders(['X-Internal-Key' => config('services.audit.internal_key')])->withToken($request->bearerToken())->get("{$this->auditUrl}/audit", $request->all());
        return response($response->json(), $response->status());
    }

    public function dashboard(Request $request)
    {
        $response = Http::acceptJson()->withHeaders(['X-Internal-Key' => config('services.audit.internal_key')])->withToken($request->bearerToken())->get("{$this->auditUrl}/dashboard", $request->all());
        return response($response->json(), $response->status());
    }
}
