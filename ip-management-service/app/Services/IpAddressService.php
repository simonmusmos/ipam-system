<?php

namespace App\Services;

use App\Models\IpAddress;
use App\Traits\AuditLogger;
use Illuminate\Http\Request;

class IpAddressService
{
    use AuditLogger;

    public function create(Request $request)
    {
        $ip = IpAddress::create([
            'user_id' => $request->user_id,
            'address' => $request->address,
            'label' => $request->label,
            'comment' => $request->comment ?? null,
        ]);
        
        $this->logAudit($request, 'create', 'ip_address', $ip->id);
        
        return $ip;
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

    public function update(Request $request, IpAddress $address)
    {
        if ($address->user_id !== $request->user_id && $request->role !== 'superadmin') {
            throw new \Exception('Unauthorized');
        }

        $address->update([
            'label' => $request->label,
            'comment' => $request->comment ?? null,
        ]);

        $this->logAudit($request, 'update', 'ip_address', $address->id);

        return $address;
    }

    public function delete(Request $request, IpAddress $address)
    {
        if ($request->role !== 'superadmin') {
            throw new \Exception('Unauthorized');
        }

        $this->logAudit($request, 'delete', 'ip_address', $address->id);

        $address->delete();
    }
}