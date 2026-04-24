<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Feedback;

use App\Services\Feedback\Data\FeedbackData;
use App\Services\Feedback\Drivers\EmailFeedbackSaver;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

final class EmailFeedbackSaverTest extends TestCase
{
    #[Test]
    public function it_sends_plain_text_email_to_configured_recipient(): void
    {
        $mailer = $this->createMock(Mailer::class);
        $messageMock = $this->createMock(Message::class);

        $messageMock->expects(self::once())
            ->method('to')
            ->with('inbox@example.com')
            ->willReturnSelf();

        $messageMock->expects(self::once())
            ->method('subject')
            ->with('Feedback!')
            ->willReturnSelf();

        $mailer->expects(self::once())
            ->method('raw')
            ->willReturnCallback(function (string $body, callable $callback) use ($messageMock): void {
                self::assertStringContainsString('From: Ivan', $body);
                self::assertStringContainsString('Hello', $body);
                $callback($messageMock);
            });

        $saver = new EmailFeedbackSaver(
            mailer: $mailer,
            logger: $this->createMock(LoggerInterface::class),
            recipient: 'inbox@example.com',
            subject: 'Feedback!',
        );

        self::assertTrue($saver->save(new FeedbackData(name: 'Ivan', message: 'Hello')));
    }

    #[Test]
    public function it_returns_false_and_logs_on_mailer_failure(): void
    {
        $mailer = $this->createMock(Mailer::class);
        $mailer->method('raw')->willThrowException(new \RuntimeException('smtp down'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error');

        $saver = new EmailFeedbackSaver(
            mailer: $mailer,
            logger: $logger,
            recipient: 'inbox@example.com',
            subject: 'Feedback!',
        );

        self::assertFalse($saver->save(new FeedbackData(name: 'Ivan', message: 'Hello')));
    }
}
