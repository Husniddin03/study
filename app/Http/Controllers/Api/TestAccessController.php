<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\StoreAccessRequest;
use App\Http\Requests\Access\UpdateAccessRequest;
use App\Http\Resources\TestAccessResource;
use App\Models\Test;
use App\Models\TestAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestAccessController extends Controller
{
    public function index(Request $request)
    {
        $accesses = TestAccess::query()
            ->where('granted_by', $request->user()->id)
            ->with('test')
            ->active()
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success([
            'items' => TestAccessResource::collection($accesses),
            'meta'  => ['total' => $accesses->total()],
        ]);
    }

    public function store(StoreAccessRequest $request)
    {
        $data = $request->validated();
        $test = Test::findOrFail($data['test_id']);
        abort_if($test->creator_id !== $request->user()->id, 403, 'Faqat test egasi ruxsat bera oladi');

        $access = TestAccess::create(array_merge($data, [
            'granted_by'  => $request->user()->id,
            'is_active'   => true,
            'invite_code' => strtoupper(Str::random(8)),
        ]));

        return $this->created(new TestAccessResource($access->load('test')), 'Ruxsat berildi');
    }

    public function show(Request $request, TestAccess $access)
    {
        return $this->success(new TestAccessResource($access->load('test')));
    }

    public function byCode(Request $request, string $code)
    {
        $access = TestAccess::where('invite_code', strtoupper($code))->active()->firstOrFail();

        return $this->success(new TestAccessResource($access->load('test')));
    }

    public function update(UpdateAccessRequest $request, TestAccess $access)
    {
        abort_if($access->granted_by !== $request->user()->id, 403);
        $access->update($request->validated());

        return $this->success(new TestAccessResource($access), 'Ruxsat yangilandi');
    }

    public function destroy(Request $request, TestAccess $access)
    {
        abort_if($access->granted_by !== $request->user()->id, 403);
        $access->delete();

        return $this->success(null, 'Ruxsat bekor qilindi');
    }

    public function deactivate(Request $request, TestAccess $access)
    {
        abort_if($access->granted_by !== $request->user()->id, 403);
        $access->update(['is_active' => false]);

        return $this->success(new TestAccessResource($access), 'Ruxsat to\'xtatildi');
    }
}
