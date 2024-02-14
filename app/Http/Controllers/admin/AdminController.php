<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Activity;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    function dashboard()
    {
        return view('admin.dashboard');
    }

    function notifyCreate()
    {
        return view('admin.notifyCreate');
    }

    function notifyStore(Request $request)
    {
        $activity = new Activity;
        $content = $request->content;
        $emotion = $request->emotion;

        //$content = $content . ' Download: https://www.wexprez.com/download ';
        if(isset($request->anonymous)){
            
            $activity->anonymous = $request->anonymous;
            
        }

        $activity->user_id = 1;
        $activity->content = $content;
        $activity->emotion = $emotion;
        $activity->type = 'activity_update';
        $activity->save();

        if($activity)
        {

            $user = User::all();
            if($user)
            {
                foreach($user as $u)
                {
                    $notify = new Notification;
                    $notify->sender_id = $activity->user_id;
                    $notify->receiver_id = $u->id;
                    $notify->notify_type = 'app_version';
                    $notify->identifier_id = $activity->id;
                    $notify->status = 0;
                    $notify->save();

                    // Mongo notify
                    $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                        'sender_id' => $activity->user_id,
                        'receiver_id' => $u->id,
                        'identifier_id' => $activity->id,
                        'notify_type' => 'app_version',
                    ]);
                    $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                        'user_id' => $u->id
                    ]);
                }
            }

            return back()
                ->with('success', 'Activity created successfully.');
        }

        else
        {
            return back()
                    ->with('error', 'Unable to create activity.');
        }
    }
}
