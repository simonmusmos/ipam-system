<?php

namespace App\Http\Controllers\Proxy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyIpController extends Controller
{
    protected $serviceUrl;

    public function __construct()
    {
        $this->serviceUrl = config('services.ip.base_uri') . '/api';
    }

    public function index(Request $request)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->get("{$this->serviceUrl}/ip-addresses", $request->all());
        return response($response->json(), $response->status());
    }

    public function create(Request $request)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->post("{$this->serviceUrl}/ip-addresses", $request->all());
        return response($response->json(), $response->status());
    }

    public function update(Request $request, $id)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->put("{$this->serviceUrl}/ip-addresses/{$id}", $request->all());
        return response($response->json(), $response->status());
    }

    public function destroy(Request $request, $id)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->delete("{$this->serviceUrl}/ip-addresses/{$id}", $request->all());
        return response($response->json(), $response->status());
    }
}
