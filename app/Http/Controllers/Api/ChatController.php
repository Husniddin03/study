<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreChatRequest;
use App\Http\Requests\Chat\UpdateChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = $request->user()->chats()
            ->with(['creator', 'lastMessage.sender'])
            ->orderByDesc('updated_at')
            ->paginate(30);

        return $this->success([
            'items' => ChatResource::collection($chats),
            'meta'  => [
                'current_page' => $chats->currentPage(),
                'last_page'    => $chats->lastPage(),
                'total'        => $chats->total(),
            ],
        ]);
    }

    public function store(StoreChatRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $chat = DB::transaction(function () use ($data, $user) {
            $chat = Chat::create(array_merge($data, [
                'created_by'   => $user->id,
                'member_count' => 1,
            ]));

            // Yaratuvchi — creator
            $chat->chatMembers()->create([
                'user_id'            => $user->id,
                'role'               => 'creator',
                'can_send_messages'  => true,
                'can_send_tests'     => true,
                'can_create_exam'    => true,
                'can_manage_members' => true,
                'joined_at'          => now(),
            ]);

            // private chat — ikkinchi a'zo
            if ($data['type'] === 'private' && ! empty($data['member_id'])) {
                $chat->chatMembers()->create([
                    'user_id'           => $data['member_id'],
                    'role'              => 'member',
                    'can_send_messages' => true,
                    'joined_at'         => now(),
                ]);
                $chat->increment('member_count');
            }

            return $chat;
        });

        return $this->created(new ChatResource($chat->load('creator')), 'Chat yaratildi');
    }

    public function show(Request $request, Chat $chat)
    {
        $this->ensureMember($request, $chat);

        return $this->success(new ChatResource($chat->load(['creator', 'lastMessage'])));
    }

    public function update(UpdateChatRequest $request, Chat $chat)
    {
        $this->ensureCanManage($request, $chat);
        $chat->update($request->validated());

        return $this->success(new ChatResource($chat), 'Chat yangilandi');
    }

    public function destroy(Request $request, Chat $chat)
    {
        abort_if($chat->created_by !== $request->user()->id, 403, 'Faqat yaratuvchi o\'chira oladi');
        $chat->delete();

        return $this->success(null, 'Chat o\'chirildi');
    }

    public function leave(Request $request, Chat $chat)
    {
        $member = $chat->chatMembers()->where('user_id', $request->user()->id)->first();
        abort_if(! $member, 404, 'Siz a\'zo emassiz');
        abort_if($member->isCreator(), 422, 'Yaratuvchi chatni tark eta olmaydi');

        $member->delete();
        $chat->decrement('member_count');

        return $this->success(null, 'Chatdan chiqdingiz');
    }

    // ── Internal guards ────────────────────────────────────
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
