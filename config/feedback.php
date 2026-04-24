<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default feedback saver driver
    |--------------------------------------------------------------------------
    |
    | Driver used by the FeedbackSaverFactory when no explicit driver
    | is supplied. Supported out of the box: "database", "email".
    |
    */

    'default_driver' => env('FEEDBACK_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Email driver configuration
    |--------------------------------------------------------------------------
    |
    | Recipient and subject used by the EmailFeedbackSaver.
    |
    */

    'email' => [
        'recipient' => env('FEEDBACK_EMAIL_RECIPIENT', 'feedback@example.com'),
        'subject' => env('FEEDBACK_EMAIL_SUBJECT', 'New feedback from the contact form'),
    ],

];
