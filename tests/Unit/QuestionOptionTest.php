<?php

use App\Models\QuestionOption;

it('getLabel order_index ni harfga aylantiradi', function () {
    expect(QuestionOption::factory()->make(['order_index' => 0])->getLabel())->toBe('A')
        ->and(QuestionOption::factory()->make(['order_index' => 1])->getLabel())->toBe('B')
        ->and(QuestionOption::factory()->make(['order_index' => 3])->getLabel())->toBe('D');
});
