<?php

namespace App\Listeners\Audit;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Auth\Events\Logout; // importar esta classe

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        \App\Models\Aud::create([
            'user_id' => $event->user->id, 
            'type' => 'LOGOUT',
            'description' =>'O usário  '. $event->user->name .' saiu do sistema!'
        ]);
    }
}
