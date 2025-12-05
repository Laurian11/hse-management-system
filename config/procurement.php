<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Procurement Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configure email addresses that should receive notifications for
    | procurement requests. You can specify multiple emails separated by commas.
    |
    */

    'notification_emails' => env('PROCUREMENT_NOTIFICATION_EMAILS', ''),

    /*
    |--------------------------------------------------------------------------
    | Auto-send Notifications
    |--------------------------------------------------------------------------
    |
    | When set to true, notifications will be automatically sent when:
    | - A procurement request is created
    | - A procurement request is submitted
    | - A procurement request status changes to 'submitted'
    |
    */

    'auto_send_notifications' => env('PROCUREMENT_AUTO_SEND_NOTIFICATIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Notification Actions
    |--------------------------------------------------------------------------
    |
    | Specify which actions should trigger notifications:
    | - 'created': Send when request is created
    | - 'submitted': Send when request is submitted
    | - 'updated': Send when request is updated
    |
    */

    'notify_on' => [
        'created' => env('PROCUREMENT_NOTIFY_ON_CREATED', false), // Send when request is created (default: false, only if status is not 'draft')
        'submitted' => env('PROCUREMENT_NOTIFY_ON_SUBMITTED', true), // Send when status changes to 'submitted' (default: true)
        'updated' => env('PROCUREMENT_NOTIFY_ON_UPDATED', false), // Send when request is updated (default: false)
    ],
];

