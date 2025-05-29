<?php

namespace App\Services;

use App\Models\IpAddress;
use Illuminate\Http\Request;

class IpAddressService
{
    public function create($userId, array $data)
    {
        return IpAddress::create([
            'user_id' => $userId,
            'address' => $data['address'],
            'label' => $data['label'],
            'comment' => $data['comment'] ?? null,
        ]);
    }

    public function get(Request $request)
    {
        $query = IpAddress::query()
            ->filter($request->only(IpAddress::ALLOWED_FILTERS))
            ->sort(
                $request->input('sort_by'),
                $request->input('sort_order', 'desc')
            );

        $perPage = $request->input('per_page', config('pagination.per_page'));

        return $query->paginate($perPage);
    }

    public function getDetails(IpAddress $address)
    {
        return $address;
    }
}