<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreContactRequest;
use App\Http\Resources\UserContactResource;
use App\Models\UserContact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = $request->user()->contacts()
            ->with('contact')
            ->notBlocked()
            ->get();

        return $this->success(UserContactResource::collection($contacts));
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();

        if ($data['contact_id'] === $request->user()->id) {
            return $this->error('O\'zingizni kontakt qila olmaysiz', 422);
        }

        $contact = $request->user()->contacts()->updateOrCreate(
            ['contact_id' => $data['contact_id']],
            ['nickname' => $data['nickname'] ?? null, 'created_at' => now()]
        );

        return $this->created(new UserContactResource($contact->load('contact')), 'Kontakt qo\'shildi');
    }

    public function destroy(Request $request, UserContact $contact)
    {
        abort_if($contact->user_id !== $request->user()->id, 403);
        $contact->delete();

        return $this->success(null, 'Kontakt o\'chirildi');
    }

    public function block(Request $request, UserContact $contact)
    {
        abort_if($contact->user_id !== $request->user()->id, 403);
        $contact->update(['is_blocked' => true]);

        return $this->success(new UserContactResource($contact), 'Bloklandi');
    }

    public function unblock(Request $request, UserContact $contact)
    {
        abort_if($contact->user_id !== $request->user()->id, 403);
        $contact->update(['is_blocked' => false]);

        return $this->success(new UserContactResource($contact), 'Blokdan chiqarildi');
    }
}
