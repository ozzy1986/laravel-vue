<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Feedback;

use App\Services\Feedback\Data\FeedbackData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FeedbackDataTest extends TestCase
{
    #[Test]
    public function it_trims_whitespace_when_built_from_array(): void
    {
        $data = FeedbackData::fromArray([
            'name' => "  Ivan \n",
            'message' => "  Hello there  \t",
        ]);

        self::assertSame('Ivan', $data->name);
        self::assertSame('Hello there', $data->message);
    }

    #[Test]
    public function to_array_round_trips_the_payload(): void
    {
        $payload = ['name' => 'Ivan', 'message' => 'Hello'];

        self::assertSame($payload, FeedbackData::fromArray($payload)->toArray());
    }
}
