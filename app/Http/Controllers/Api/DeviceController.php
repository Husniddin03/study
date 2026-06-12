<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreDeviceRequest;
use App\Http\Resources\UserDeviceResource;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $devices = $request->user()->devices()->active()->get();

        return $this->success(UserDeviceResource::collection($devices));
    }

    public function store(StoreDeviceRequest $request)
    {
        $data = $request->validated();

        $device = $request->user()->devices()->updateOrCreate(
            ['device_token' => $data['device_token']],
            [
                'platform'     => $data['platform'],
                'device_name'  => $data['device_name'] ?? null,
                'is_active'    => true,
                'last_used_at' => now(),
            ]
        );

        return $this->created(new UserDeviceResource($device), 'Qurilma ro\'yxatdan o\'tdi');
    }

    public function destroy(Request $request, UserDevice $device)
    {
        abort_if($device->user_id !== $request->user()->id, 403);
        $device->delete();

        return $this->success(null, 'Qurilma o\'chirildi');
    }
}
