<?php

namespace App\Http\Controllers;

use App\Models\IpAddress;
use App\Services\IpAddressService;
use Illuminate\Http\Request;

class IpAddressController extends Controller
{
    protected $ipService;

    public function __construct(IpAddressService $ipService)
    {
        $this->ipService = $ipService;
    }

    public function index(Request $request)
    {
        return $this->ipService->get($request);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|ip|unique:ip_addresses,address',
            'label' => 'required|string|max:255',
            'comment' => 'nullable|string',
        ]);

        $ip = $this->ipService->create($request->user_id, $request->all());

        return response()->json($ip, 201);
    }
}
