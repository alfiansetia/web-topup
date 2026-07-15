<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Configuration
    |--------------------------------------------------------------------------
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),

    'chat_ids' => array_filter(
        array_map('trim', explode(',', env('TELEGRAM_CHAT_IDS', '')))
    ),

    /*
    |--------------------------------------------------------------------------
    | Notification Toggles
    |--------------------------------------------------------------------------
    */

    'notify_new_order' => env('TELEGRAM_NOTIFY_NEW_ORDER', true),
    'notify_paid'      => env('TELEGRAM_NOTIFY_PAID', true),

];
