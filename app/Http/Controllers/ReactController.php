<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\React;
use App\Models\Notification;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Block;
use Illuminate\Support\Facades\Http;

class ReactController extends Controller
{
    function real(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $activityId = $request->get('activity_id');

            $session = session('user');
            $userId = $session->id;
            

            $oldData = React::where('identifier_id', '=', $activityId)
                                ->where('identifier', '=', 'activity')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'fake'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'activity_real', $activityId);
                    }
                    $oldData->type = 'real';
                    $oldData->status = 1;
                    $img = 'images/reaction/real-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'activity_real_off', $activityId);
                    $img = 'images/reaction/real-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'activity_real', $activityId);
                    $img = 'images/reaction/real-on.png';
                }
                $oldData->update();
            }
            else{
                $react = new React;

                $react->user_id = $userId;
                $react->identifier_id = $activityId;
                $react->identifier = 'activity';
                $react->type = 'real';
                $react->status = 1;
                $react->save();

                $this->score($userId, 1, 'activity_real', $activityId);
                $img = 'images/reaction/real-on.png';

                // Create Notification
                $activity = Activity::find($activityId);
                if($activity)
                {
                    if($activity->user_id != $userId)
                    {
                        $notify = new Notification;
                        $notify->sender_id = $userId;
                        $notify->receiver_id = $activity->user_id;
                        $notify->identifier_id = $activityId;
                        $notify->notify_type = 'activity_real';
                        $notify->save();

                        // Mongo notify
                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                            'sender_id' => $userId,
                            'receiver_id' => $activity->user_id,
                            'identifier_id' => $activityId,
                            'notify_type' => 'activity_real',
                        ]);
                        $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                            'user_id' => $activity->user_id
                        ]);
                    }
                }
            }

            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $real = React::where('identifier_id', '=', $activityId)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'real')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = React::where('identifier_id', '=', $activityId)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'fake')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = count($fake);
            $real = count($real);

            $result = [];
            $result['real'] =  $real;
            $result['fake'] =  $fake;
            $result['img'] =  $img;

            return $result;
        }
    }

    function fake(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $activityId = $request->get('activity_id');

            $session = session('user');
            $userId = $session->id;

            $oldData = React::where('identifier_id', '=', $activityId)
                                ->where('identifier', '=', 'activity')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'real'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'activity_fake', $activityId);
                    }
                    $oldData->type = 'fake';
                    $oldData->status = 1;
                    $img = 'images/reaction/fake-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'activity_fake_off', $activityId);
                    $img = 'images/reaction/fake-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'activity_fake', $activityId);
                    $img = 'images/reaction/fake-on.png';
                }
                $oldData->update();
            }
            else{
                $react = new React;

                $react->user_id = $userId;
                $react->identifier_id = $activityId;
                $react->identifier = 'activity';
                $react->type = 'fake';
                $react->status = 1;
                $react->save();

                $this->score($userId, 1, 'activity_fake', $activityId);
                $img = 'images/reaction/fake-on.png';

                // Create Notification
                $activity = Activity::find($activityId);
                if($activity)
                {
                    if($activity->user_id != $userId)
                    {
                        $notify = new Notification;
                        $notify->sender_id = $userId;
                        $notify->receiver_id = $activity->user_id;
                        $notify->identifier_id = $activityId;
                        $notify->notify_type = 'activity_fake';
                        $notify->save();

                        // Mongo notify
                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                            'sender_id' => $userId,
                            'receiver_id' => $activity->user_id,
                            'identifier_id' => $activityId,
                            'notify_type' => 'activity_fake',
                        ]);
                        $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                            'user_id' => $activity->user_id
                        ]);
                    }
                }
            }

            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $real = React::where('identifier_id', '=', $activityId)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'real')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = React::where('identifier_id', '=', $activityId)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'fake')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = count($fake);
            $real = count($real);

            $result = [];
            $result['real'] =  $real;
            $result['fake'] =  $fake;
            $result['img'] =  $img;

            return $result;
        }
    }
    function realList(Request $request)
    {
        if($request->ajax())
        {
            $activityId = $request->get('activity_id');
            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $react = DB::table('wx_react')
                        ->where('wx_react.status', '=', '1')
                        ->where('wx_react.identifier_id', '=', $activityId)
                        ->where('wx_react.identifier', '=', 'activity')
                        ->where('wx_react.type', '=', 'real')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_react.user_id', '=', 'wx_users.id')
                        ->select('wx_react.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($react)
            {
                return $react;
            }
        }
    }
    function fakeList(Request $request)
    {
        if($request->ajax())
        {
            $activityId = $request->get('activity_id');
            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $react = DB::table('wx_react')
                        ->where('wx_react.status', '=', '1')
                        ->where('wx_react.identifier_id', '=', $activityId)
                        ->where('wx_react.identifier', '=', 'activity')
                        ->where('wx_react.type', '=', 'fake')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_react.user_id', '=', 'wx_users.id')
                        ->select('wx_react.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($react)
            {
                return $react;
            }
        }
    }

    function commentReal(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $commentId = $request->get('comment_id');

            $session = session('user');
            $userId = $session->id;
            

            $oldData = React::where('identifier_id', '=', $commentId)
                                ->where('identifier', '=', 'comment')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'fake'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'comment_real', $commentId);
                    }
                    $oldData->type = 'real';
                    $oldData->status = 1;
                    $img = 'images/reaction/real-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'comment_real_off', $commentId);
                    $img = 'images/reaction/real-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'comment_real', $commentId);
                    $img = 'images/reaction/real-on.png';
                }
                $oldData->update();
            }
            else{
                $react = new React;

                $react->user_id = $userId;
                $react->identifier_id = $commentId;
                $react->identifier = 'comment';
                $react->type = 'real';
                $react->status = 1;
                $react->save();

                $this->score($userId, 1, 'comment_real', $commentId);
                $img = 'images/reaction/real-on.png';

                // Create Notification
                $comment = Comment::find($commentId);
                if($comment)
                {
                    $activity = Activity::find($comment->activity_id);
                    if($activity)
                    {
                        if($comment->user_id != $userId)
                        {
                            $notify = new Notification;
                            $notify->sender_id = $userId;
                            $notify->receiver_id = $comment->user_id;
                            $notify->identifier_id = $commentId;
                            $notify->notify_type = 'comment_real';
                            $notify->save();

                            // Mongo notify
                            $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                'sender_id' => $userId,
                                'receiver_id' => $comment->user_id,
                                'identifier_id' => $commentId,
                                'notify_type' => 'comment_real',
                            ]);
                            $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                'user_id' => $comment->user_id
                            ]);
                        }
                    }
                }
            }

            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $real = React::where('identifier_id', '=', $commentId)
                        ->where('identifier', '=', 'comment')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'real')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = React::where('identifier_id', '=', $commentId)
                        ->where('identifier', '=', 'comment')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'fake')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = count($fake);
            $real = count($real);

            $result = [];
            $result['real'] =  $real;
            $result['fake'] =  $fake;
            $result['img'] =  $img;

            return $result;
        }
    }

    function commentFake(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $commentId = $request->get('comment_id');

            $session = session('user');
            $userId = $session->id;

            $oldData = React::where('identifier_id', '=', $commentId)
                                ->where('identifier', '=', 'comment')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'real'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'comment_fake', $commentId);
                    }
                    $oldData->type = 'fake';
                    $oldData->status = 1;
                    $img = 'images/reaction/fake-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'comment_fake_off', $commentId);
                    $img = 'images/reaction/fake-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'comment_fake', $commentId);
                    $img = 'images/reaction/fake-on.png';
                }
                $oldData->update();
            }
            else{
                $react = new React;

                $react->user_id = $userId;
                $react->identifier_id = $commentId;
                $react->identifier = 'comment';
                $react->type = 'fake';
                $react->status = 1;
                $react->save();

                $this->score($userId, 1, 'comment_fake', $commentId);
                $img = 'images/reaction/fake-on.png';

                // Create Notification
                $comment = Comment::find($commentId);
                if($comment)
                {
                    $activity = Activity::find($comment->activity_id);
                    if($activity)
                    {
                        if($comment->user_id != $userId)
                        {
                            $notify = new Notification;
                            $notify->sender_id = $userId;
                            $notify->receiver_id = $comment->user_id;
                            $notify->identifier_id = $commentId;
                            $notify->notify_type = 'comment_fake';
                            $notify->save();

                            // Mongo notify
                            $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                'sender_id' => $userId,
                                'receiver_id' => $comment->user_id,
                                'identifier_id' => $commentId,
                                'notify_type' => 'comment_fake',
                            ]);
                            $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                'user_id' => $comment->user_id
                            ]);
                        }
                    }
                }
            }

            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $real = React::where('identifier_id', '=', $commentId)
                        ->where('identifier', '=', 'comment')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'real')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = React::where('identifier_id', '=', $commentId)
                        ->where('identifier', '=', 'comment')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'fake')
                        ->whereNotIn('user_id', $blocking)
                        ->whereNotIn('user_id', $blocker)
                        ->get();

            $fake = count($fake);
            $real = count($real);

            $result = [];
            $result['real'] =  $real;
            $result['fake'] =  $fake;
            $result['img'] =  $img;

            return $result;
        }
    }

    // list
    function commentRealList(Request $request)
    {
        if($request->ajax())
        {
            $commentId = $request->get('comment_id');
            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $react = DB::table('wx_react')
                        ->where('wx_react.status', '=', '1')
                        ->where('wx_react.identifier_id', '=', $commentId)
                        ->where('wx_react.identifier', '=', 'comment')
                        ->where('wx_react.type', '=', 'real')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_react.user_id', '=', 'wx_users.id')
                        ->select('wx_react.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($react)
            {
                return $react;
            }
        }
    }
    function commentFakeList(Request $request)
    {
        if($request->ajax())
        {
            $commentId = $request->get('comment_id');
            $session = session('user');
            $sessionId = $session->id;
            $blocking = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

            $react = DB::table('wx_react')
                        ->where('wx_react.status', '=', '1')
                        ->where('wx_react.identifier_id', '=', $commentId)
                        ->where('wx_react.identifier', '=', 'comment')
                        ->where('wx_react.type', '=', 'fake')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_react.user_id', '=', 'wx_users.id')
                        ->select('wx_react.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($react)
            {
                return $react;
            }
        }
    }

}
