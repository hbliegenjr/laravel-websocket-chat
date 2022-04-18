<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use App\Events\MessageSent;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();
       /* Broadcast::routes(['middleware' => ['auth:admin']]);
        */
       require base_path('routes/channels.php');
        
    }
}
