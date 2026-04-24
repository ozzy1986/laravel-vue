<?php

declare(strict_types=1);

namespace App\Services\Feedback\Drivers;

use App\Services\Feedback\Contracts\FeedbackSaverInterface;
use App\Services\Feedback\Data\FeedbackData;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Delivers feedback to the configured inbox using the framework mailer.
 *
 * Uses a raw plain-text body on purpose — the goal of the task is the
 * saving strategy, not templating, and plain-text keeps the message
 * immune to HTML injection.
 */
final class EmailFeedbackSaver implements FeedbackSaverInterface
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly LoggerInterface $logger,
        private readonly string $recipient,
        private readonly string $subject,
    ) {}

    public function save(FeedbackData $data): bool
    {
        try {
            $this->mailer->raw(
                $this->buildBody($data),
                function (Message $message): void {
                    $message->to($this->recipient)->subject($this->subject);
                }
            );

            return true;
        } catch (Throwable $e) {
            $this->logger->error('Failed to deliver feedback by email.', [
                'exception' => $e,
            ]);

            return false;
        }
    }

    private function buildBody(FeedbackData $data): string
    {
        return sprintf(
            "New feedback received.\n\nFrom: %s\n\nMessage:\n%s\n",
            $data->name,
            $data->message,
        );
    }
}
