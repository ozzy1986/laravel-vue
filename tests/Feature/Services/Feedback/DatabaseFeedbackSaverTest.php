<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Feedback;

use App\Models\Feedback;
use App\Services\Feedback\Data\FeedbackData;
use App\Services\Feedback\Drivers\DatabaseFeedbackSaver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

final class DatabaseFeedbackSaverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_persists_feedback_into_the_feedbacks_table(): void
    {
        $saver = $this->app->make(DatabaseFeedbackSaver::class);

        $saved = $saver->save(new FeedbackData(name: 'Ivan', message: 'Hello world'));

        self::assertTrue($saved);
        self::assertSame(1, Feedback::query()->count());
        self::assertDatabaseHas('feedbacks', [
            'name' => 'Ivan',
            'message' => 'Hello world',
        ]);
    }

    #[Test]
    public function it_returns_false_and_logs_when_database_write_fails(): void
    {
        DB::statement('DROP TABLE feedbacks');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error');

        $saver = new DatabaseFeedbackSaver($logger);

        self::assertFalse($saver->save(new FeedbackData(name: 'Ivan', message: 'x')));
    }
}
