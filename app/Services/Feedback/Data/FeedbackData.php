<?php

declare(strict_types=1);

namespace App\Services\Feedback\Data;

/**
 * Immutable transfer object carrying feedback payload
 * between the HTTP layer and persistence drivers.
 */
final class FeedbackData
{
    public function __construct(
        public readonly string $name,
        public readonly string $message,
    ) {}

    /**
     * @param  array{name: string, message: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            message: trim($data['message']),
        );
    }

    /**
     * @return array{name: string, message: string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'message' => $this->message,
        ];
    }
}
