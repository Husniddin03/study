<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Test\StoreQuestionRequest;
use App\Http\Requests\Test\UpdateQuestionRequest;
use App\Http\Resources\TestQuestionResource;
use App\Models\Test;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestQuestionController extends Controller
{
    public function index(Request $request, Test $test)
    {
        $questions = $test->questions()->with('options')->get();

        return $this->success(TestQuestionResource::collection($questions));
    }

    public function store(StoreQuestionRequest $request, Test $test)
    {
        $this->ensureOwner($request, $test);
        $data = $request->validated();

        $question = DB::transaction(function () use ($test, $data) {
            $orderIndex = $data['order_index'] ?? ($test->questions()->max('order_index') + 1);

            $question = $test->questions()->create([
                'subject'       => $data['subject'] ?? null,
                'block_name'    => $data['block_name'] ?? null,
                'content_type'  => $data['content_type'],
                'content'       => $data['content'],
                'image_url'     => $data['image_url'] ?? null,
                'formula'       => $data['formula'] ?? null,
                'extra_content' => $data['extra_content'] ?? null,
                'answer_type'   => $data['answer_type'],
                'order_index'   => $orderIndex,
                'points'        => $data['points'] ?? 1,
                'explanation'   => $data['explanation'] ?? null,
            ]);

            foreach ($data['options'] ?? [] as $i => $opt) {
                $question->options()->create([
                    'content'     => $opt['content'],
                    'image_url'   => $opt['image_url'] ?? null,
                    'formula'     => $opt['formula'] ?? null,
                    'is_correct'  => $opt['is_correct'] ?? false,
                    'order_index' => $opt['order_index'] ?? $i,
                ]);
            }

            return $question;
        });

        return $this->created(new TestQuestionResource($question->load('options')), 'Savol qo\'shildi');
    }

    public function show(Request $request, Test $test, TestQuestion $question)
    {
        abort_if($question->test_id !== $test->id, 404);

        return $this->success(new TestQuestionResource($question->load('options')));
    }

    public function update(UpdateQuestionRequest $request, Test $test, TestQuestion $question)
    {
        $this->ensureOwner($request, $test);
        abort_if($question->test_id !== $test->id, 404);

        $question->update($request->validated());

        return $this->success(new TestQuestionResource($question->load('options')), 'Savol yangilandi');
    }

    public function destroy(Request $request, Test $test, TestQuestion $question)
    {
        $this->ensureOwner($request, $test);
        abort_if($question->test_id !== $test->id, 404);

        $question->options()->delete();
        $question->delete();

        return $this->success(null, 'Savol o\'chirildi');
    }

    public function reorder(Request $request, Test $test)
    {
        $this->ensureOwner($request, $test);
        $data = $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['uuid', 'exists:test_questions,id'],
        ]);

        foreach ($data['order'] as $index => $questionId) {
            TestQuestion::where('id', $questionId)
                ->where('test_id', $test->id)
                ->update(['order_index' => $index]);
        }

        return $this->success(null, 'Tartib yangilandi');
    }

    protected function ensureOwner(Request $request, Test $test): void
    {
        abort_if($test->creator_id !== $request->user()->id, 403, 'Faqat egasi boshqaradi');
    }
}
