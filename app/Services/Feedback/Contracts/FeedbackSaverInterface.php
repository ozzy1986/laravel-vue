<?php

declare(strict_types=1);

namespace App\Services\Feedback\Contracts;

use App\Services\Feedback\Data\FeedbackData;

/**
 * Common contract every concrete feedback saver must implement.
 *
 * Keeps drivers interchangeable and lets the factory stay agnostic
 * about their inner workings (Dependency Inversion Principle).
 */
interface FeedbackSaverInterface
{
    /**
     * Persist the given feedback payload.
     *
     * @return bool true when the payload has been accepted by the storage
     */
    public function save(FeedbackData $data): bool;
}
