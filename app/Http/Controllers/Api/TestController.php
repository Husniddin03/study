<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Test\StoreTestRequest;
use App\Http\Requests\Test\UpdateTestRequest;
use App\Http\Resources\TestAttemptResource;
use App\Http\Resources\TestResource;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $query = Test::query()->withCount('questions')->with('creator');

        // filtrlar
        if ($request->filled('type')) {
            $query->ofType($request->string('type'));
        }
        if ($request->boolean('mine')) {
            $query->where('creator_id', $request->user()->id);
        } else {
            // boshqalarnikidan faqat public + published
            $query->where(function ($q) use ($request) {
                $q->where('creator_id', $request->user()->id)
                  ->orWhere(fn ($s) => $s->public()->published());
            });
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->string('search') . '%');
        }

        $tests = $query->orderByDesc('created_at')->paginate(20);

        return $this->success([
            'items' => TestResource::collection($tests),
            'meta'  => [
                'current_page' => $tests->currentPage(),
                'last_page'    => $tests->lastPage(),
                'total'        => $tests->total(),
            ],
        ]);
    }

    public function store(StoreTestRequest $request)
    {
        $test = Test::create(array_merge($request->validated(), [
            'creator_id' => $request->user()->id,
        ]));

        return $this->created(new TestResource($test), 'Test yaratildi');
    }

    public function show(Request $request, Test $test)
    {
        // egasiga to'liq, boshqalarga published+public bo'lsa
        $isOwner = $test->creator_id === $request->user()->id;
        abort_if(! $isOwner && ! ($test->is_published && $test->visibility !== 'private'), 403);

        $test->load(['creator', 'questions.options'])->loadCount('questions');

        // egasi bo'lmasa to'g'ri javoblarni yashiramiz
        $resource = (new TestResource($test));
        if (! $isOwner) {
            $test->setRelation('questions', $test->questions->each->setRelation('options', $test->questions->pluck('options')->flatten()));
        }

        return $this->success(new TestResource($test));
    }

    public function update(UpdateTestRequest $request, Test $test)
    {
        $this->ensureOwner($request, $test);
        $test->update($request->validated());

        return $this->success(new TestResource($test), 'Test yangilandi');
    }

    public function destroy(Request $request, Test $test)
    {
        $this->ensureOwner($request, $test);
        $test->delete();

        return $this->success(null, 'Test o\'chirildi');
    }

    public function publish(Request $request, Test $test)
    {
        $this->ensureOwner($request, $test);
        abort_if($test->questions()->count() === 0, 422, 'Savolsiz testni e\'lon qilib bo\'lmaydi');

        $test->update(['is_published' => true]);

        return $this->success(new TestResource($test), 'Test e\'lon qilindi');
    }

    public function unpublish(Request $request, Test $test)
    {
        $this->ensureOwner($request, $test);
        $test->update(['is_published' => false]);

        return $this->success(new TestResource($test), 'E\'lon to\'xtatildi');
    }

    public function duplicate(Request $request, Test $test)
    {
        $this->ensureOwner($request, $test);

        $copy = $test->replicate();
        $copy->title = $test->title . ' (nusxa)';
        $copy->is_published = false;
        $copy->attempt_count = 0;
        $copy->avg_score = 0;
        $copy->save();

        foreach ($test->questions()->with('options')->get() as $q) {
            $newQ = $q->replicate();
            $newQ->test_id = $copy->id;
            $newQ->save();
            foreach ($q->options as $opt) {
                $newOpt = $opt->replicate();
                $newOpt->question_id = $newQ->id;
                $newOpt->save();
            }
        }

        return $this->created(new TestResource($copy->load('questions.options')), 'Test nusxalandi');
    }

    public function leaderboard(Request $request, Test $test)
    {
        $limit = (int) $request->integer('limit', 10);
        $board = $test->getLeaderboard($limit);

        return $this->success(TestAttemptResource::collection($board));
    }

    protected function ensureOwner(Request $request, Test $test): void
    {
        abort_if($test->creator_id !== $request->user()->id, 403, 'Faqat egasi boshqaradi');
    }
}
