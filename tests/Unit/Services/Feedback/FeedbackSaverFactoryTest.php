<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Feedback;

use App\Services\Feedback\Contracts\FeedbackSaverInterface;
use App\Services\Feedback\Data\FeedbackData;
use App\Services\Feedback\Drivers\DatabaseFeedbackSaver;
use App\Services\Feedback\Drivers\EmailFeedbackSaver;
use App\Services\Feedback\Exceptions\UnsupportedFeedbackDriverException;
use App\Services\Feedback\FeedbackSaverFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class FeedbackSaverFactoryTest extends TestCase
{
    #[Test]
    public function it_rejects_unknown_driver_at_construction(): void
    {
        $this->expectException(UnsupportedFeedbackDriverException::class);

        new FeedbackSaverFactory('carrier-pigeon', $this->createMock(ContainerInterface::class));
    }

    #[Test]
    public function it_accepts_database_driver_case_insensitively(): void
    {
        $factory = new FeedbackSaverFactory('  DATABASE  ', $this->createMock(ContainerInterface::class));

        self::assertSame(FeedbackSaverFactory::DRIVER_DATABASE, $factory->driver());
    }

    #[Test]
    public function it_accepts_email_driver(): void
    {
        $factory = new FeedbackSaverFactory(FeedbackSaverFactory::DRIVER_EMAIL, $this->createMock(ContainerInterface::class));

        self::assertSame(FeedbackSaverFactory::DRIVER_EMAIL, $factory->driver());
    }

    #[Test]
    public function save_delegates_to_database_driver_when_selected(): void
    {
        $data = new FeedbackData(name: 'Ivan', message: 'hi');

        $saver = $this->createMock(FeedbackSaverInterface::class);
        $saver->expects(self::once())
            ->method('save')
            ->with($data)
            ->willReturn(true);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(DatabaseFeedbackSaver::class)
            ->willReturn($saver);

        $factory = new FeedbackSaverFactory(FeedbackSaverFactory::DRIVER_DATABASE, $container);

        self::assertTrue($factory->save($data));
    }

    #[Test]
    public function save_delegates_to_email_driver_when_selected(): void
    {
        $data = new FeedbackData(name: 'Ivan', message: 'hi');

        $saver = $this->createMock(FeedbackSaverInterface::class);
        $saver->expects(self::once())
            ->method('save')
            ->with($data)
            ->willReturn(true);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(EmailFeedbackSaver::class)
            ->willReturn($saver);

        $factory = new FeedbackSaverFactory(FeedbackSaverFactory::DRIVER_EMAIL, $container);

        self::assertTrue($factory->save($data));
    }

    #[Test]
    public function available_drivers_expose_database_and_email(): void
    {
        self::assertSame(
            [FeedbackSaverFactory::DRIVER_DATABASE, FeedbackSaverFactory::DRIVER_EMAIL],
            FeedbackSaverFactory::availableDrivers(),
        );
    }
}
