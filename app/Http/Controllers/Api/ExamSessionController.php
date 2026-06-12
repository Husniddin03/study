<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\JoinSessionRequest;
use App\Http\Requests\Exam\StoreSessionRequest;
use App\Http\Resources\ExamParticipantResource;
use App\Http\Resources\ExamSessionResource;
use App\Models\ExamSession;
use App\Models\TestAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = ExamSession::query()
            ->where('host_user_id', $request->user()->id)
            ->withCount('participants')
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success([
            'items' => ExamSessionResource::collection($sessions),
            'meta'  => ['total' => $sessions->total()],
        ]);
    }

    public function store(StoreSessionRequest $request)
    {
        $data   = $request->validated();
        $access = TestAccess::findOrFail($data['access_id']);
        abort_if($access->granted_by !== $request->user()->id, 403, 'Faqat ruxsat egasi sessiya ochadi');

        $session = ExamSession::create([
            'access_id'        => $access->id,
            'host_user_id'     => $request->user()->id,
            'session_code'     => strtoupper(Str::random(6)),
            'network_ssid'     => $data['network_ssid'] ?? null,
            'network_ip_range' => $data['network_ip_range'] ?? null,
            'status'           => 'waiting',
            'connected_count'  => 0,
            'max_allowed'      => $data['max_allowed'] ?? $access->max_participants,
        ]);

        return $this->created(new ExamSessionResource($session), 'Imtihon sessiyasi yaratildi');
    }

    public function show(Request $request, ExamSession $session)
    {
        $this->ensureHost($request, $session);

        return $this->success(new ExamSessionResource(
            $session->load(['participants.user', 'host'])
        ));
    }

    public function start(Request $request, ExamSession $session)
    {
        $this->ensureHost($request, $session);
        $session->start();

        return $this->success(new ExamSessionResource($session), 'Imtihon boshlandi');
    }

    public function finish(Request $request, ExamSession $session)
    {
        $this->ensureHost($request, $session);
        $session->finish();

        return $this->success(new ExamSessionResource($session), 'Imtihon yakunlandi');
    }

    public function join(JoinSessionRequest $request)
    {
        $data    = $request->validated();
        $session = ExamSession::where('session_code', strtoupper($data['session_code']))->firstOrFail();

        abort_if($session->isFinished(), 422, 'Sessiya yakunlangan');

        if ($session->max_allowed && $session->connected_count >= $session->max_allowed) {
            return $this->error('Sessiya to\'lgan', 422);
        }

        $participant = $session->participants()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'device_ip'    => $request->ip(),
                'device_info'  => $data['device_info'] ?? $request->userAgent(),
                'status'       => 'connected',
                'connected_at' => now(),
            ]
        );

        $session->update(['connected_count' => $session->participants()->where('status', 'connected')->count()]);
        $session->logEvent('participant_joined', ['user_id' => $request->user()->id]);

        return $this->created(new ExamParticipantResource($participant), 'Sessiyaga qo\'shildingiz');
    }

    public function leave(Request $request, ExamSession $session)
    {
        $participant = $session->participants()->where('user_id', $request->user()->id)->firstOrFail();
        $participant->update(['status' => 'disconnected', 'disconnected_at' => now()]);
        $session->update(['connected_count' => $session->activeParticipants()->count()]);

        return $this->success(null, 'Sessiyadan chiqdingiz');
    }

    public function participants(Request $request, ExamSession $session)
    {
        $this->ensureHost($request, $session);

        return $this->success(
            ExamParticipantResource::collection($session->participants()->with('user', 'attempt')->get())
        );
    }

    public function flagged(Request $request, ExamSession $session)
    {
        $this->ensureHost($request, $session);

        return $this->success(
            ExamParticipantResource::collection($session->flaggedParticipants()->with('user')->get())
        );
    }

    protected function ensureHost(Request $request, ExamSession $session): void
    {
        abort_if($session->host_user_id !== $request->user()->id, 403, 'Faqat imtihon hosti');
    }
}
