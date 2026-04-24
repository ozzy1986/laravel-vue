<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Feedback\Drivers\EmailFeedbackSaver;
use App\Services\Feedback\FeedbackSaverFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class FeedbackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmailFeedbackSaver::class, function (Application $app): EmailFeedbackSaver {
            /** @var array{recipient: string, subject: string} $config */
            $config = $app['config']->get('feedback.email', []);

            return new EmailFeedbackSaver(
                mailer: $app->make(Mailer::class),
                logger: $app->make(LoggerInterface::class),
                recipient: (string) ($config['recipient'] ?? 'feedback@example.com'),
                subject: (string) ($config['subject'] ?? 'New feedback'),
            );
        });

        $this->app->bind(FeedbackSaverFactory::class, function (Application $app): FeedbackSaverFactory {
            $driver = (string) $app['config']->get('feedback.default_driver', FeedbackSaverFactory::DRIVER_DATABASE);

            return new FeedbackSaverFactory($driver, $app->make(ContainerInterface::class));
        });
    }
}
