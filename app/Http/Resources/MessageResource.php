<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'chat_id'           => $this->chat_id,
            'sender_id'         => $this->sender_id,
            'type'              => $this->type,
            'content'           => $this->content,
            'file_url'          => $this->file_url,
            'file_name'         => $this->file_name,
            'file_size'         => $this->file_size,
            'test_id'           => $this->test_id,
            'test_access_id'    => $this->test_access_id,
            'reply_to_id'       => $this->reply_to_id,
            'forwarded_from_id' => $this->forwarded_from_id,
            'is_pinned'         => $this->is_pinned,
            'is_deleted'        => $this->is_deleted,
            'reactions'         => $this->reactions,
            'read_by'           => $this->read_by,
            'sender'            => new UserResource($this->whenLoaded('sender')),
            'reply_to'          => new MessageResource($this->whenLoaded('replyTo')),
            'test'              => new TestResource($this->whenLoaded('test')),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
