<?php

namespace Tests\Unit;

use App\Services\LessonProgressService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class FinalSubjectQuizProgressionTest extends TestCase
{
    public function test_final_subject_quiz_target_count_is_thirty(): void
    {
        $this->assertSame(30, LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT);
    }

    public function test_proportional_allocation_distributes_exactly_to_target(): void
    {
        $service = new LessonProgressService(
            new \App\Services\LessonSlidePresenter()
        );

        $method = new ReflectionMethod(LessonProgressService::class, 'proportionalQuestionAllocation');
        $method->setAccessible(true);

        $weights = [1 => 10, 2 => 20, 3 => 30];
        $allocations = $method->invoke($service, $weights, 30);

        $this->assertSame(30, array_sum($allocations));
        $this->assertSame(5, $allocations[1]);
        $this->assertSame(10, $allocations[2]);
        $this->assertSame(15, $allocations[3]);
    }
}
