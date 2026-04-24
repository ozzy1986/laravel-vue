<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Feedback;
use App\Services\Feedback\Contracts\FeedbackSaverInterface;
use App\Services\Feedback\Data\FeedbackData;
use App\Services\Feedback\Drivers\DatabaseFeedbackSaver;
use App\Services\Feedback\FeedbackSaverFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_feedback_with_database_driver_and_returns_payload(): void
    {
        $response = $this->postJson('/api/feedbacks', [
            'name' => 'Ivan',
            'message' => 'Nice job on the assignment!',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => ['name', 'message', 'driver', 'accepted_at'],
            ])
            ->assertJsonPath('data.name', 'Ivan')
            ->assertJsonPath('data.message', 'Nice job on the assignment!')
            ->assertJsonPath('data.driver', FeedbackSaverFactory::DRIVER_DATABASE);

        $this->assertDatabaseHas('feedbacks', [
            'name' => 'Ivan',
            'message' => 'Nice job on the assignment!',
        ]);
    }

    #[Test]
    public function it_trims_whitespace_around_input(): void
    {
        $this->postJson('/api/feedbacks', [
            'name' => '   Ivan   ',
            'message' => '   Hi there   ',
        ])->assertCreated();

        $this->assertDatabaseHas('feedbacks', [
            'name' => 'Ivan',
            'message' => 'Hi there',
        ]);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $this->postJson('/api/feedbacks', [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name', 'message']);
    }

    #[Test]
    public function it_rejects_too_short_name(): void
    {
        $this->postJson('/api/feedbacks', ['name' => 'A', 'message' => 'Hello there'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function it_rejects_oversized_message(): void
    {
        $this->postJson('/api/feedbacks', [
            'name' => 'Ivan',
            'message' => str_repeat('a', 5001),
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['message']);
    }

    #[Test]
    public function it_returns_503_when_saver_cannot_persist(): void
    {
        $this->app->bind(DatabaseFeedbackSaver::class, function () {
            return new class implements FeedbackSaverInterface
            {
                public function save(FeedbackData $data): bool
                {
                    return false;
                }
            };
        });

        $this->postJson('/api/feedbacks', ['name' => 'Ivan', 'message' => 'Hello'])
            ->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE)
            ->assertJsonStructure(['message']);

        self::assertSame(0, Feedback::query()->count());
    }

    #[Test]
    public function spa_route_returns_html_for_any_non_api_path(): void
    {
        $this->withoutVite();

        $this->get('/')->assertOk()->assertSee('id="app"', false);
        $this->get('/feedbacks')->assertOk()->assertSee('id="app"', false);
        $this->get('/anything/nested')->assertOk()->assertSee('id="app"', false);
    }
}
