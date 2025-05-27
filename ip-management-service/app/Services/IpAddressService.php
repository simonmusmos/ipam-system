<?php

namespace App\Services;

use App\Models\IpAddress;

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
}