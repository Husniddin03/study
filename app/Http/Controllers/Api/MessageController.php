<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\ReactionRequest;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, Chat $chat)
    {
        $this->ensureMember($request, $chat);

        $messages = $chat->messages()
            ->visible()
            ->with(['sender', 'replyTo', 'test'])
            ->orderByDesc('created_at')
            ->paginate(50);

        return $this->success([
            'items' => MessageResource::collection($messages),
            'meta'  => [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'total'        => $messages->total(),
            ],
        ]);
    }

    public function store(StoreMessageRequest $request, Chat $chat)
    {
        $member = $this->ensureMember($request, $chat);
        abort_if($member && ! $member->can_send_messages, 403, 'Xabar yuborish taqiqlangan');
        abort_if($member && $member->isMutedNow(), 403, 'Siz vaqtincha ovozsizlantirilgansiz');

        $message = $chat->messages()->create(array_merge($request->validated(), [
            'sender_id'  => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        $chat->touch();

        return $this->created(new MessageResource($message->load('sender')), 'Xabar yuborildi');
    }

    public function show(Request $request, Chat $chat, Message $message)
    {
        $this->ensureMember($request, $chat);
        abort_if($message->chat_id !== $chat->id, 404);

        return $this->success(new MessageResource($message->load(['sender', 'replyTo', 'test'])));
    }

    public function update(UpdateMessageRequest $request, Chat $chat, Message $message)
    {
        abort_if($message->sender_id !== $request->user()->id, 403, 'Faqat o\'z xabaringizni tahrirlaysiz');

        $message->update(array_merge($request->validated(), ['updated_at' => now()]));

        return $this->success(new MessageResource($message), 'Xabar tahrirlandi');
    }

    public function destroy(Request $request, Chat $chat, Message $message)
    {
        $member = $chat->chatMembers()->where('user_id', $request->user()->id)->first();
        $canDelete = $message->sender_id === $request->user()->id || ($member && $member->canModerate());
        abort_if(! $canDelete, 403);

        $message->update(['is_deleted' => true, 'deleted_at' => now()]);

        return $this->success(null, 'Xabar o\'chirildi');
    }

    public function pin(Request $request, Chat $chat, Message $message)
    {
        $this->ensureCanManage($request, $chat);
        $message->update(['is_pinned' => ! $message->is_pinned]);

        return $this->success(new MessageResource($message), $message->is_pinned ? 'Mahkamlandi' : 'Mahkamlash bekor qilindi');
    }

    public function read(Request $request, Chat $chat, Message $message)
    {
        $this->ensureMember($request, $chat);
        $message->markReadBy($request->user()->id);

        return $this->success(null, 'O\'qildi deb belgilandi');
    }

    public function react(ReactionRequest $request, Chat $chat, Message $message)
    {
        $this->ensureMember($request, $chat);

        $emoji      = $request->validated()['emoji'];
        $userId     = $request->user()->id;
        $reactions  = $message->reactions ?? [];

        // toggle: bor bo'lsa olib tashlash
        $existing = $reactions[$emoji] ?? [];
        if (in_array($userId, $existing)) {
            $existing = array_values(array_diff($existing, [$userId]));
        } else {
            $existing[] = $userId;
        }

        if (empty($existing)) {
            unset($reactions[$emoji]);
        } else {
            $reactions[$emoji] = $existing;
        }

        $message->update(['reactions' => $reactions]);

        return $this->success(new MessageResource($message), 'Reaksiya yangilandi');
    }

    protected function ensureMember(Request $request, Chat $chat)
    {
        $member = $chat->chatMembers()->where('user_id', $request->user()->id)->first();
        abort_if(! $member && ! $chat->is_public, 403, 'Ruxsat yo\'q');

        return $member;
    }

    protected function ensureCanManage(Request $request, Chat $chat): void
    {
        $member = $chat->chatMembers()->where('user_id', $request->user()->id)->first();
        abort_if(! $member || ! $member->canModerate(), 403, 'Boshqaruv huquqi yo\'q');
    }
}
