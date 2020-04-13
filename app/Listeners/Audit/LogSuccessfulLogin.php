<?php

namespace App\Listeners\Audit;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Auth\Events\Login; // importar essa classe


class LogSuccessfulLogin
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
    public function handle(Login $event)
    {
        \App\Models\Aud::create([
            'user_id' => $event->user->id, 
            'type' => 'LOGIN',
            'description' =>'O usário '. $event->user->name .' entrou no sistema!'
        ]);
    }
}
