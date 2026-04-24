<?php

declare(strict_types=1);

namespace App\Services\Feedback;

use App\Services\Feedback\Contracts\FeedbackSaverInterface;
use App\Services\Feedback\Data\FeedbackData;
use App\Services\Feedback\Drivers\DatabaseFeedbackSaver;
use App\Services\Feedback\Drivers\EmailFeedbackSaver;
use App\Services\Feedback\Exceptions\UnsupportedFeedbackDriverException;
use Psr\Container\ContainerInterface;

/**
 * Factory that picks a concrete storage strategy for feedback.
 *
 * Per task specification:
 *  - accepts "database" or "email" on creation;
 *  - exposes a single `save()` method that dispatches to the chosen driver.
 *
 * Driver classes are resolved lazily through the PSR-11 container, so
 * their own dependencies (mailer, logger, config) stay injected and testable.
 */
final class FeedbackSaverFactory
{
    public const DRIVER_DATABASE = 'database';

    public const DRIVER_EMAIL = 'email';

    /**
     * Map of driver aliases → fully qualified saver classes.
     * Adding a new driver = adding one line here, without touching callers (Open/Closed).
     *
     * @var array<string, class-string<FeedbackSaverInterface>>
     */
    private const DRIVERS = [
        self::DRIVER_DATABASE => DatabaseFeedbackSaver::class,
        self::DRIVER_EMAIL => EmailFeedbackSaver::class,
    ];

    private readonly string $driver;

    public function __construct(string $driver, private readonly ContainerInterface $container)
    {
        $driver = strtolower(trim($driver));

        if (! isset(self::DRIVERS[$driver])) {
            throw UnsupportedFeedbackDriverException::forDriver($driver);
        }

        $this->driver = $driver;
    }

    public function save(FeedbackData $data): bool
    {
        return $this->resolveSaver()->save($data);
    }

    public function driver(): string
    {
        return $this->driver;
    }

    /**
     * @return list<string>
     */
    public static function availableDrivers(): array
    {
        return array_keys(self::DRIVERS);
    }

    private function resolveSaver(): FeedbackSaverInterface
    {
        /** @var FeedbackSaverInterface $saver */
        $saver = $this->container->get(self::DRIVERS[$this->driver]);

        return $saver;
    }
}
