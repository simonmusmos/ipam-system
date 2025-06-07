<?php

namespace App\Http\Controllers\Proxy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyAuthController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.auth.base_uri') . '/api';
    }

    public function register(Request $request)
    {
        $response = Http::acceptJson()->post("{$this->baseUrl}/register", $request->all());

        return response($response->json(), $response->status());
    }

    public function login(Request $request)
    {
        $response = Http::acceptJson()->post("{$this->baseUrl}/login", $request->all());
        
        return response($response->json(), $response->status());
    }

    public function logout(Request $request)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->post("{$this->baseUrl}/logout", $request->all());
        
        return response($response->json(), $response->status());
    }

    public function me(Request $request)
    {
        return json_decode(Http::acceptJson()->withToken($request->bearerToken())->get("{$this->baseUrl}/me"));
    }

    public function users(Request $request)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->get("{$this->baseUrl}/users", $request->all());
        
        return response($response->json(), $response->status());
    }

    public function dashboard(Request $request)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->get("{$this->baseUrl}/dashboard", $request->all());
        return response($response->json(), $response->status());
    }

    public function refresh(Request $request)
    {
        $response = Http::acceptJson()->withToken($request->bearerToken())->get("{$this->baseUrl}/refresh", $request->all());
        return response($response->json(), $response->status());
    }
}
