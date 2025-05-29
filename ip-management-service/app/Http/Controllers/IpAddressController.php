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

    public function getDetails(IpAddress $ip)
    {
        return response()->json($this->ipService->getDetails($ip), 200);
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

    public function update(Request $request, IpAddress $ip)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'comment' => 'nullable|string',
        ]);

        try {
            $ip = $this->ipService->update($request, $ip);
            return response()->json($ip);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function destroy(Request $request, IpAddress $ip)
    {
        try {
            $this->ipService->delete($request, $ip);
            return response()->json(['message' => 'Deleted']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
