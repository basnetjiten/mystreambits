<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->string('key');
            $table->primary('key');
            $table->longText('value')->nullable();
        });

        /*
         *  Default data
         */

        /* App */ {
            // Name
            $insert[] = [
                'key' => 'app.name',
                'value' => 'OP Streamers'
            ];
            // Title
            $insert[] = [
                'key' => 'app.title',
                'value' => 'OPstreamers'
            ];
            // TimeZone
            $insert[] = [
                'key' => 'app.timezone',
                'value' => 'America/New_York'
            ];
            // Currency
            $insert[] = [
                'key' => 'app.currency',
                'value' => 'usd'
            ];
            // Currency Icon
            $insert[] = [
                'key' => 'app.currency_icon',
                'value' => '<i class="fa fa-usd" aria-hidden="true"></i>'
            ];
            // Email
            $insert[] = [
                'key' => 'app.contact_email',
                'value' => 'you@email.com'
            ];
            // YANDEX_API_KEY
            $insert[] = [
                'key'   => 'app.yandex.api_key',
                'value' => 'b847c958-7f94-492b-8c06-49c4fd0f9cab'
            ];
        }
        
        /* PayPal */ {
            // Mode: live or sandbox
            $insert[] = [
                'key'   => 'paypal.mode',
                'value' => 'sandbox'
            ];
            // Commission
            $insert[] = [
                'key' => 'paypal.commission',
                'value' => '15'
            ];
            // Status
            $insert[] = [
                'key' => 'paypal.status',
                'value' => 'enabled'
            ];
            // Sandbox: email (For commission)
            $insert[] = [
                'key'   => 'paypal.sandbox.email',
                'value' => ''
            ];
            // Sandbox: username
            $insert[] = [
                'key'   => 'paypal.sandbox.username',
                'value' => ''
            ];
            // Sandbox: password
            $insert[] = [
                'key'   => 'paypal.sandbox.password',
                'value' => ''
            ];
            // Sandbox: secret
            $insert[] = [
                'key'   => 'paypal.sandbox.secret',
                'value' => ''
            ];
            // Live: email (For commission)
            $insert[] = [
                'key'   => 'paypal.live.email',
                'value' => ''
            ];
            // Live: username
            $insert[] = [
                'key'   => 'paypal.live.username',
                'value' => ''
            ];
            // Live: password
            $insert[] = [
                'key'   => 'paypal.live.password',
                'value' => ''
            ];
            // Live: app_id
            $insert[] = [
                'key'   => 'paypal.live.app_id',
                'value' => ''
            ];
            // Currency
            $insert[] = [
                'key'   => 'paypal.currency',
                'value' => 'USD'
            ];
            // Notify URL
            $insert[] = [
                'key'   => 'paypal.notify_url',
                'value' => 'https://you.domain/payments/paypal/notify'
            ];
            
        }



        /* Esewa */ {
        // Mode: live or sandbox
        $insert[] = [
            'key'   => 'esewa.mode',
            'value' => 'sandbox'
        ];
        // Commission
        $insert[] = [
            'key' => 'esewa.commission',
            'value' => '15'
        ];
        // Status
        $insert[] = [
            'key' => 'esewa.status',
            'value' => 'enabled'
        ];
        // Sandbox: email (For commission)
        $insert[] = [
            'key'   => 'esewa.sandbox.email',
            'value' => ''
        ];
        // Sandbox: username
        $insert[] = [
            'key'   => 'esewa.sandbox.username',
            'value' => ''
        ];
        // Sandbox: password
        $insert[] = [
            'key'   => 'esewa.sandbox.password',
            'value' => ''
        ];
        // Sandbox: secret
        $insert[] = [
            'key'   => 'esewa.sandbox.secret',
            'value' => ''
        ];
        // Live: email (For commission)
        $insert[] = [
            'key'   => 'esewa.live.email',
            'value' => ''
        ];
        // Live: username
        $insert[] = [
            'key'   => 'esewa.live.username',
            'value' => ''
        ];
        // Live: password
        $insert[] = [
            'key'   => 'esewa.live.password',
            'value' => ''
        ];
        // Live: app_id
        $insert[] = [
            'key'   => 'esewa.live.app_id',
            'value' => ''
        ];
        // Currency
        $insert[] = [
            'key'   => 'esewa.currency',
            'value' => 'RS'
        ];
        // Notify URL
        $insert[] = [
            'key'   => 'esewa.notify_url',
            'value' => 'https://you.domain/payments/paypal/notify'
        ];

    }




        /* KHALTI */ {
        // Mode: live or sandbox
        $insert[] = [
            'key'   => 'khalti.mode',
            'value' => 'sandbox'
        ];
        // Commission
        $insert[] = [
            'key' => 'khalti.commission',
            'value' => '15'
        ];
        // Status
        $insert[] = [
            'key' => 'khalti.status',
            'value' => 'enabled'
        ];
        // Sandbox: email (For commission)
        $insert[] = [
            'key'   => 'khalti.sandbox.email',
            'value' => ''
        ];
        // Sandbox: username
        $insert[] = [
            'key'   => 'khalti.sandbox.username',
            'value' => ''
        ];
        // Sandbox: password
        $insert[] = [
            'key'   => 'khalti.sandbox.password',
            'value' => ''
        ];
        // Sandbox: secret
        $insert[] = [
            'key'   => 'khalti.sandbox.secret',
            'value' => ''
        ];
        // Live: email (For commission)
        $insert[] = [
            'key'   => 'khalti.live.email',
            'value' => ''
        ];
        // Live: username
        $insert[] = [
            'key'   => 'khalti.live.username',
            'value' => ''
        ];
        // Live: password
        $insert[] = [
            'key'   => 'khalti.live.password',
            'value' => ''
        ];
        // Live: app_id
        $insert[] = [
            'key'   => 'khalti.live.app_id',
            'value' => ''
        ];
        // Currency
        $insert[] = [
            'key'   => 'khalti.currency',
            'value' => 'RS'
        ];
        // Notify URL
        $insert[] = [
            'key'   => 'khalti.notify_url',
            'value' => 'https://you.domain/payments/paypal/notify'
        ];

    }



        /* IMEPAY */ {
        // Mode: live or sandbox
        $insert[] = [
            'key'   => 'imepay.mode',
            'value' => 'sandbox'
        ];
        // Commission
        $insert[] = [
            'key' => 'imepay.commission',
            'value' => '15'
        ];
        // Status
        $insert[] = [
            'key' => 'imepay.status',
            'value' => 'enabled'
        ];
        // Sandbox: email (For commission)
        $insert[] = [
            'key'   => 'imepay.sandbox.email',
            'value' => ''
        ];
        // Sandbox: username
        $insert[] = [
            'key'   => 'imepay.sandbox.username',
            'value' => ''
        ];
        // Sandbox: password
        $insert[] = [
            'key'   => 'imepay.sandbox.password',
            'value' => ''
        ];
        // Sandbox: secret
        $insert[] = [
            'key'   => 'imepay.sandbox.secret',
            'value' => ''
        ];
        // Live: email (For commission)
        $insert[] = [
            'key'   => 'imepay.live.email',
            'value' => ''
        ];
        // Live: username
        $insert[] = [
            'key'   => 'imepay.live.username',
            'value' => ''
        ];
        // Live: password
        $insert[] = [
            'key'   => 'imepay.live.password',
            'value' => ''
        ];
        // Live: app_id
        $insert[] = [
            'key'   => 'imepay.live.app_id',
            'value' => ''
        ];
        // Currency
        $insert[] = [
            'key'   => 'imepay.currency',
            'value' => 'RS'
        ];
        // Notify URL
        $insert[] = [
            'key'   => 'imepay.notify_url',
            'value' => 'https://you.domain/payments/paypal/notify'
        ];

    }

        /* Auth */ {
            // Default Avatar
            $insert[] = [
                'key'   => 'auth.default_avatar',
                'value' => 'https://api.adorable.io/avatars/285/abott@adorable.png'
            ];
            // Youtube Status
            $insert[] = [
                'key'   => 'auth.youtube.status',
                'value' => 'enabled'
            ];
            // Twitch Status
            $insert[] = [
                'key'   => 'auth.twitch.status',
                'value' => 'enabled'
            ];
            // Mixer Status
            $insert[] = [
                'key'   => 'auth.mixer.status',
                'value' => 'enabled'
            ];
        }

        DB::table('configurations')->insert($insert);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
}
