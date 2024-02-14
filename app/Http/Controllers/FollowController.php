<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\User;
use App\Models\Login;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;


class FollowController extends Controller
{
    function follow(Request $request){

        if($request->ajax())
        {
            
            $output = '';
            $followId = $request->get('follow_id');

            $session = session('user');
            $userId = $session->id;

            if($followId != $userId)
            {
                $oldData = Follow::where('follow_id', '=', $followId)
                                    ->where('user_id', '=', $userId)
                                    ->first();
                
                if($oldData){
                    
                    if($oldData->status == 1)
                    {
                        $oldData->status = 0;
                        $oldData->update();
                        $output = "Follow";
                        $this->score($userId, -1, 'unfollow', $followId);
                    }
                    else{
                        $oldData->status = 1;
                        $oldData->update();
                        $output = "Unfollow";
                        $this->score($userId, 1, 'follow', $followId);

                        // Create Notification
                        $notify = new Notification;
                        $notify->sender_id = $userId;
                        $notify->receiver_id = $followId;
                        $notify->notify_type = 'follow';
                        $notify->save();

                        // Mongo notify
                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                            'sender_id' => $userId,
                            'receiver_id' => $followId,
                            'notify_type' => 'follow',
                        ]);
                        $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                            'user_id' => $followId
                        ]);
                    }
                    echo json_encode($output);
                    
                }
                else{
                    $follow = new Follow;

                    $follow->user_id = $userId;
                    $follow->follow_id = $followId;
                    $follow->save();
        
                    if($follow){
                        $output = "Unfollow";
                        $this->score($userId, 1, 'follow', $followId);
                        
                        // Create Notification
                        $notify = new Notification;
                        $notify->sender_id = $userId;
                        $notify->receiver_id = $followId;
                        $notify->notify_type = 'follow';
                        $notify->save();

                        // Mongo notify
                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                            'sender_id' => $userId,
                            'receiver_id' => $followId,
                            'notify_type' => 'follow',
                        ]);
                        $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                            'user_id' => $followId
                        ]);
                    }
                    else{
                        $output = "failed";
                    }
                    echo json_encode($output);
                }
              
                
            }
        }

    }

    
}
