<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\AddMemberRequest;
use App\Http\Requests\Chat\UpdateMemberRequest;
use App\Http\Resources\ChatMemberResource;
use App\Models\Chat;
use App\Models\ChatMember;
use Illuminate\Http\Request;

class ChatMemberController extends Controller
{
    public function index(Request $request, Chat $chat)
    {
        $this->ensureMember($request, $chat);

        $members = $chat->chatMembers()->with('user')->paginate(50);

        return $this->success([
            'items' => ChatMemberResource::collection($members),
            'meta'  => ['total' => $members->total()],
        ]);
    }

    public function store(AddMemberRequest $request, Chat $chat)
    {
        $this->ensureCanManage($request, $chat);
        $data = $request->validated();

        if ($chat->chatMembers()->where('user_id', $data['user_id'])->exists()) {
            return $this->error('Foydalanuvchi allaqachon a\'zo', 422);
        }

        $member = $chat->chatMembers()->create(array_merge($data, [
            'role'       => $data['role'] ?? 'member',
            'invited_by' => $request->user()->id,
            'joined_at'  => now(),
        ]));
        $chat->increment('member_count');

        return $this->created(new ChatMemberResource($member->load('user')), 'A\'zo qo\'shildi');
    }

    public function update(UpdateMemberRequest $request, Chat $chat, ChatMember $member)
    {
        $this->ensureCanManage($request, $chat);
        abort_if($member->chat_id !== $chat->id, 404);
        abort_if($member->isCreator(), 422, 'Yaratuvchini o\'zgartirib bo\'lmaydi');

        $member->update($request->validated());

        return $this->success(new ChatMemberResource($member->load('user')), 'A\'zo yangilandi');
    }

    public function destroy(Request $request, Chat $chat, ChatMember $member)
    {
        $this->ensureCanManage($request, $chat);
        abort_if($member->chat_id !== $chat->id, 404);
        abort_if($member->isCreator(), 422, 'Yaratuvchini o\'chirib bo\'lmaydi');

        $member->delete();
        $chat->decrement('member_count');

        return $this->success(null, 'A\'zo chiqarildi');
    }

    protected function ensureMember(Request $request, Chat $chat): void
    {
        $isMember = $chat->chatMembers()->where('user_id', $request->user()->id)->exists();
        abort_if(! $isMember && ! $chat->is_public, 403, 'Ruxsat yo\'q');
    }

    protected function ensureCanManage(Request $request, Chat $chat): void
    {
        $member = $chat->chatMembers()->where('user_id', $request->user()->id)->first();
        abort_if(! $member || ! $member->canModerate(), 403, 'Boshqaruv huquqi yo\'q');
    }
}
