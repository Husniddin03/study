<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Test\StoreOptionRequest;
use App\Http\Resources\QuestionOptionResource;
use App\Models\QuestionOption;
use App\Models\TestQuestion;
use Illuminate\Http\Request;

class QuestionOptionController extends Controller
{
    public function store(StoreOptionRequest $request, TestQuestion $question)
    {
        $this->ensureOwner($request, $question);
        $data = $request->validated();

        $option = $question->options()->create(array_merge($data, [
            'order_index' => $data['order_index'] ?? ($question->options()->max('order_index') + 1),
        ]));

        return $this->created(new QuestionOptionResource($option), 'Variant qo\'shildi');
    }

    public function update(StoreOptionRequest $request, TestQuestion $question, QuestionOption $option)
    {
        $this->ensureOwner($request, $question);
        abort_if($option->question_id !== $question->id, 404);

        $option->update($request->validated());

        return $this->success(new QuestionOptionResource($option), 'Variant yangilandi');
    }

    public function destroy(Request $request, TestQuestion $question, QuestionOption $option)
    {
        $this->ensureOwner($request, $question);
        abort_if($option->question_id !== $question->id, 404);

        $option->delete();

        return $this->success(null, 'Variant o\'chirildi');
    }

    protected function ensureOwner(Request $request, TestQuestion $question): void
    {
        abort_if($question->test->creator_id !== $request->user()->id, 403, 'Faqat egasi boshqaradi');
    }
}
