<?php

namespace App\Providers;

use App\Events\MakePaymentEvent;
use App\Events\Product\EventCheckSubscriptionProduct;
use App\Listeners\MakePaymentListener;
use App\Listeners\Product\ListenerCheckSubscriptionProduct;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MakePaymentEvent::class=>[
            MakePaymentListener::class
        ],
        EventCheckSubscriptionProduct::class=>[
            ListenerCheckSubscriptionProduct::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
