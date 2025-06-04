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
        
        $this->logAudit($request, [
            'action' => 'delete',
            'model' => class_basename($address),
            'model_id' => $ip->id,
        ]);
        
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

        $ips = $query->paginate($perPage);
        
        $ips->getCollection()->transform(function ($ip) use ($request) {
            $ip->can_manage = $ip->user_id == $request->user_id || $request->role === "superadmin";
            return $ip;
        });

        return $ips;
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

        $original = $address->getOriginal();

        $address->fill($request->all());

        $changedFields = $address->getDirty();
        $oldValues = collect($original)->only(array_keys($changedFields))->except(['updated_at', 'created_at']);
        $newValues = collect($changedFields)->except(['updated_at', 'created_at']);

        $address->save();

        $this->logAudit($request, [
            'action' => 'update',
            'model' => class_basename($address),
            'model_id' => $address->id,
            'old_values' => $oldValues->toArray(),
            'new_values' => $newValues->toArray(),
        ]);

        return $address;
    }

    public function delete(Request $request, IpAddress $address)
    {
        if ($request->role !== 'superadmin') {
            throw new \Exception('Unauthorized');
        }

        $original = $address->getOriginal();

        $this->logAudit($request, [
            'action' => 'delete',
            'model' => class_basename($address),
            'model_id' => $address->id,
            'old_values' => $oldValues->toArray(),
        ]);

        $address->delete();
    }
}