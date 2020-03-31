<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /* TODO: NOTIFICAÇÃO */
    /* https://www.youtube.com/watch?v=fwxvMsgyrvw&list=PLVSNL1PHDWvSOFpHtRi1-oZjBll69lehn&index=11 */
    public function __construct()
	{
		$this->middleware('auth');
    }
    
    public function notification(Request $request)
    {
        $notifications = $request->user()->notification;

        return response()->json(compact('notifications'));
    }
}
