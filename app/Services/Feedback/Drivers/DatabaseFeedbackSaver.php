<?php

declare(strict_types=1);

namespace App\Services\Feedback\Drivers;

use App\Models\Feedback;
use App\Services\Feedback\Contracts\FeedbackSaverInterface;
use App\Services\Feedback\Data\FeedbackData;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Persists feedback into the `feedbacks` database table via Eloquent.
 *
 * Returning a boolean (rather than the model) keeps the driver
 * interface uniform across storage strategies.
 */
final class DatabaseFeedbackSaver implements FeedbackSaverInterface
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function save(FeedbackData $data): bool
    {
        try {
            Feedback::query()->create($data->toArray());

            return true;
        } catch (Throwable $e) {
            $this->logger->error('Failed to persist feedback to database.', [
                'exception' => $e,
            ]);

            return false;
        }
    }
}
