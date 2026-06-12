<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\FlagParticipantRequest;
use App\Http\Resources\ExamParticipantResource;
use App\Models\ExamParticipant;
use Illuminate\Http\Request;

class ExamParticipantController extends Controller
{
    public function show(Request $request, ExamParticipant $participant)
    {
        $isHost = $participant->session->host_user_id === $request->user()->id;
        $isSelf = $participant->user_id === $request->user()->id;
        abort_if(! $isHost && ! $isSelf, 403);

        return $this->success(new ExamParticipantResource($participant->load('user', 'attempt')));
    }

    // host tomonidan qo'lda flag qo'yish
    public function flag(FlagParticipantRequest $request, ExamParticipant $participant)
    {
        abort_if($participant->session->host_user_id !== $request->user()->id, 403, 'Faqat host');
        $data = $request->validated();

        $participant->flag($data['violation_type'], $data['extra'] ?? []);

        return $this->success(new ExamParticipantResource($participant), 'Belgilandi');
    }

    // o'quvchining qurilmasi avtomatik xabar beradi
    public function reportTabSwitch(Request $request, ExamParticipant $participant)
    {
        abort_if($participant->user_id !== $request->user()->id, 403);
        $participant->recordTabSwitch();

        return $this->success(null, 'Tab almashish qayd etildi');
    }

    public function reportExternalRequest(Request $request, ExamParticipant $participant)
    {
        abort_if($participant->user_id !== $request->user()->id, 403);
        $data = $request->validate(['url' => ['required', 'string', 'max:1024']]);
        $participant->recordExternalRequest($data['url']);

        return $this->success(null, 'Tashqi so\'rov qayd etildi');
    }
}
