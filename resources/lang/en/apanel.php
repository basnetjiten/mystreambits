<?php
return [
    'configurations' => [
        'title' => 'Configurations',
        'enter' => 'Enter :what',
        'success' => 'Configuration successfully updated',
        'application' => [
            'title' => 'Application',
        ],
        'auth' => [
            'title' => 'Authorization'
        ],
        'payments' => [
            'title' => 'Payments'
        ],
        'other' => [
            'title' => 'Other'
        ],
        'keys' => [
            'app_name' => 'Application Name',
            'app_title' => 'Application Title',
            'app_timezone' => 'Default TimeZone',
            'app_currency_icon' => 'Currency Icon',
            'app_currency' => 'Currency Code',
            'app_yandex_api_key' => 'Yandex.SpeechKit Api Key',
            'app_yandex_api_key_info' => 'Create Account (https://passport.yandex.com/registration/mail), Create Api key (https://developer.tech.yandex.ru/keys/)',
            'app_contact_email' => 'Contact form Email',
            
            'auth_default_avatar' => 'Default Avatar',
            'auth_twitch_status' => 'Twitch',
            'auth_youtube_status' => 'Youtube',
            'auth_mixer_status' => 'Mixer',
    
            'paypal_commission' => 'Commission for each donation (%)',
            'paypal_info' => '<a href="https://github.com/srmklive/laravel-paypal">GitHub repository</a>. ' . 
                             '<a href="https://www.paypal-apps.com/user/my-account/applications">Applications</a>. ' . 
                             '<a href="https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNIntro/">IPN Instruction</a> (<a href="https://developer.paypal.com/developer/ipnSimulator/">Simulator</a>).' . 
                             '<a href="https://developer.paypal.com/docs/classic/api/apiCredentials/#create-an-api-signature">How to get Username, Password, Secret (API)</a>',
            'paypal_sandbox' => 'Sandbox',
            'paypal_live' => 'Live',
            'paypal_basic' => 'Basic Settings',
            'paypal_status' => 'Status',
            'paypal_mode' => 'Mode',
            'paypal_currency' => 'Currency',
            'paypal_notify_url' => 'Notify URL (For IPN)',
            'paypal_sandbox_username' => 'Username (API)',
            'paypal_sandbox_password' => 'Password (API)',
            'paypal_sandbox_secret' => 'Secret (API)',
            'paypal_sandbox_email' => 'Account for commission',
            'paypal_live_username' => 'Username (API)',
            'paypal_live_password' => 'Password (API)',
            'paypal_live_secret' => 'Secret (API)',
            'paypal_live_email' => 'Account for commission',
            'paypal_live_app_id' => 'Application ID',

            'esewa_commission' => 'Commission for each donation (%)',
            'esewa_sandbox' => 'Sandbox',
            'esewa_live' => 'Live',
            'esewa_basic' => 'Basic Settings',
            'esewa_status' => 'Status',
            'esewa_mode' => 'Mode',
            'esewa_currency' => 'Currency',
            'esewa_notify_url' => 'Notify URL (For IPN)',
            'esewa_sandbox_username' => 'Username (API)',
            'esewa_sandbox_password' => 'Password (API)',
            'esewa_sandbox_secret' => 'Secret (API)',
            'esewa_sandbox_email' => 'Account for commission',
            'esewa_live_username' => 'Username (API)',
            'esewa_live_password' => 'Password (API)',
            'esewa_live_secret' => 'Secret (API)',
            'esewa_live_email' => 'Account for commission',
            'esewa_live_app_id' => 'Application ID',



            'khalti_commission' => 'Commission for each donation (%)',

            'khalti_sandbox' => 'Sandbox',
            'khalti_live' => 'Live',
            'khalti_basic' => 'Basic Settings',
            'khalti_status' => 'Status',
            'khalti_mode' => 'Mode',
            'khalti_currency' => 'Currency',
            'khalti_notify_url' => 'Notify URL (For IPN)',
            'khalti_sandbox_username' => 'Username (API)',
            'khalti_sandbox_password' => 'Password (API)',
            'khalti_sandbox_secret' => 'Secret (API)',
            'khalti_sandbox_email' => 'Account for commission',
            'khalti_live_username' => 'Username (API)',
            'khalti_live_password' => 'Password (API)',
            'khalti_live_secret' => 'Secret (API)',
            'khalti_live_email' => 'Account for commission',
            'khalti_live_app_id' => 'Application ID',


            'imepay_commission' => 'Commission for each donation (%)',

            'imepay_sandbox' => 'Sandbox',
            'imepay_live' => 'Live',
            'imepay_basic' => 'Basic Settings',
            'imepay_status' => 'Status',
            'imepay_mode' => 'Mode',
            'imepay_currency' => 'Currency',
            'imepay_notify_url' => 'Notify URL (For IPN)',
            'imepay_sandbox_username' => 'Username (API)',
            'imepay_sandbox_password' => 'Password (API)',
            'imepay_sandbox_secret' => 'Secret (API)',
            'imepay_sandbox_email' => 'Account for commission',
            'imepay_live_username' => 'Username (API)',
            'imepay_live_password' => 'Password (API)',
            'imepay_live_secret' => 'Secret (API)',
            'imepay_live_email' => 'Account for commission',
            'imepay_live_app_id' => 'Application ID'






            
            
    
        ]
    ],
    'statistics' => [
        'title' => 'Statistics',
        'message_statistics' => 'Statistics of the last 7 days',
        'amount' => 'Amount',
        'amount_info' => 'Amount of donations (Last 7 days)',
        'commission' => 'Commission',
        'commission_info' => 'Amount of commissions (Last 7 days)',
        'counters' => [
            'messages' => 'Total messages',
            'paid_messages' => 'Total Paid Messages',
            'users' => 'Total Users',
            'today_users' => 'Today New Users',
            'amount' => 'Amount of donations',
            'commission' => 'Amount of commission',
            'refunds' => 'Number of refunds',
            'amount_refunds' => 'Refund amount'
            ]
        ],
    'donations' => [
        'title' => 'Donations',
        'user_id' => 'User'
    ],
    'users' => [
        'title' => 'Users',
        'id' => 'ID',
        'balance' => 'Balance',
        'email' => 'Email',
        'name' => 'Username',
        'stream_name' => 'Streamer',
        'timezone' => 'Timezone',
        'token' => 'Token', 
        'created_at' => 'Date of registration',
        'avatar' => 'Avatar',
        'level' => [
            'title' => 'Rights',
            'user' => 'User',
            'admin' => 'Admin'
            ],
        'edit' => [
            'title' => 'Edit User #:id'
            ]
        ]
];