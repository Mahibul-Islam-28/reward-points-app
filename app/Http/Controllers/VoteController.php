<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vote;
use App\Models\Notification;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Block;
use Illuminate\Support\Facades\Http;

class VoteController extends Controller
{
    function voteUp(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $activityId = $request->get('activity_id');

            $session = session('user');
            $userId = $session->id;

            $oldData = Vote::where('identifier_id', '=', $activityId)
                                ->where('identifier', '=', 'activity')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'down'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'activity_VoteUp', $activityId);
                    }
                    $oldData->type = 'up';
                    $oldData->status = 1;
                    $img = 'images/reaction/voteUp-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'activity_voteUp_off', $activityId);
                    $img = 'images/reaction/voteUp-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'activity_voteUp', $activityId);
                    $img = 'images/reaction/voteUp-on.png';
                }
                $oldData->update();
            }
            else{
                $vote = new Vote;

                $vote->user_id = $userId;
                $vote->identifier_id = $activityId;
                $vote->identifier = 'activity';
                $vote->type = 'up';
                $vote->status = 1;
                $vote->save();

                $this->score($userId, 1, 'activity_voteUp', $activityId);

                $img = 'images/reaction/voteUp-on.png';

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
                        $notify->notify_type = 'activity_voteUp';
                        $notify->save();

                        // Mongo notify
                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                            'sender_id' => $userId,
                            'receiver_id' => $activity->user_id,
                            'identifier_id' => $activityId,
                            'notify_type' => 'activity_voteUp',
                        ]);
                        $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                            'user_id' => $activity->user_id
                        ]);
                    }
                }
            }

            $result = [];

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

            $up = Vote::where('identifier_id', '=', $activityId)
                            ->where('identifier', '=', 'activity')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'up')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();
            $down = Vote::where('identifier_id', '=', $activityId)
                            ->where('identifier', '=', 'activity')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'down')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();

            $up = count($up);
            $down = count($down);
            $result['up'] =  $up;
            $result['down'] =  $down;
            $result['img'] =  $img;

            return $result;
        }
    }
    function voteDown(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $activityId = $request->get('activity_id');

            $session = session('user');
            $userId = $session->id;


            $oldData = Vote::where('identifier_id', '=', $activityId)
                                ->where('identifier', '=', 'activity')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'up'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'activity_VoteDown', $activityId);
                    }
                    $oldData->type = 'down';
                    $oldData->status = 1;
                    $img = 'images/reaction/voteDown-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'activity_voteDown_off', $activityId);
                    $img = 'images/reaction/voteDown-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'activity_voteDown', $activityId);
                    $img = 'images/reaction/voteDown-on.png';
                }
                $oldData->update();
            }
            else{
                $vote = new Vote;

                $vote->user_id = $userId;
                $vote->identifier_id = $activityId;
                $vote->identifier = 'activity';
                $vote->type = 'down';
                $vote->status = 1;
                $vote->save();

                $this->score($userId, 1, 'activity_voteDown', $activityId);

                $img = 'images/reaction/voteDown-on.png';

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
                        $notify->notify_type = 'activity_voteDown';
                        $notify->save();

                        // Mongo notify
                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                            'sender_id' => $userId,
                            'receiver_id' => $activity->user_id,
                            'identifier_id' => $activityId,
                            'notify_type' => 'activity_voteDown',
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

            $up = Vote::where('identifier_id', '=', $activityId)
                            ->where('identifier', '=', 'activity')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'up')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();
            $down = Vote::where('identifier_id', '=', $activityId)
                            ->where('identifier', '=', 'activity')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'down')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();

            $up = count($up);
            $down = count($down);
            $result = [];
            $result['up'] =  $up;
            $result['down'] =  $down;
            $result['img'] =  $img;

            return $result;
        }
    }

    function voteUpList(Request $request)
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

            $votes = DB::table('wx_vote')
                        ->where('wx_vote.status', '=', '1')
                        ->where('wx_vote.identifier_id', '=', $activityId)
                        ->where('wx_vote.identifier', '=', 'activity')
                        ->where('wx_vote.type', '=', 'up')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_vote.user_id', '=', 'wx_users.id')
                        ->select('wx_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($votes)
            {
                return $votes;
            }
        }
    }
    function voteDownList(Request $request)
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


            $votes = DB::table('wx_vote')
                        ->where('wx_vote.status', '=', '1')
                        ->where('wx_vote.identifier_id', '=', $activityId)
                        ->where('wx_vote.identifier', '=', 'activity')
                        ->where('wx_vote.type', '=', 'down')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_vote.user_id', '=', 'wx_users.id')
                        ->select('wx_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($votes)
            {
                return $votes;
            }
        }
    }

    // Comment
    function commentVoteUp(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $commentId = $request->get('comment_id');

            $session = session('user');
            $userId = $session->id;

            $oldData = Vote::where('identifier_id', '=', $commentId)
                                ->where('identifier', '=', 'comment')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'down'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'comment_VoteUp', $activityId);
                    }
                    $oldData->type = 'up';
                    $oldData->status = 1;
                    $img = 'images/reaction/voteUp-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'comment_voteUp_off', $commentId);
                    $img = 'images/reaction/voteUp-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'comment_voteUp', $commentId);
                    $img = 'images/reaction/voteUp-on.png';
                }
                $oldData->update();
            }
            else{
                $vote = new Vote;

                $vote->user_id = $userId;
                $vote->identifier_id = $commentId;
                $vote->identifier = 'comment';
                $vote->type = 'up';
                $vote->status = 1;
                $vote->save();

                $this->score($userId, 1, 'comment_voteUp', $commentId);

                $img = 'images/reaction/voteUp-on.png';

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
                            $notify->notify_type = 'comment_voteUp';
                            $notify->save();

                            // Mongo notify
                            $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                'sender_id' => $userId,
                                'receiver_id' => $comment->user_id,
                                'identifier_id' => $commentId,
                                'notify_type' => 'comment_voteUp',
                            ]);
                            $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                'user_id' => $comment->user_id
                            ]);
                        }
                    }
                }
            }

            $result = [];

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

            $up = Vote::where('identifier_id', '=', $commentId)
                            ->where('identifier', '=', 'comment')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'up')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();
            $down = Vote::where('identifier_id', '=', $commentId)
                            ->where('identifier', '=', 'comment')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'down')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();

            $up = count($up);
            $down = count($down);
            $result['up'] =  $up;
            $result['down'] =  $down;
            $result['img'] =  $img;

            return $result;
        }
    }

    function commentVoteDown(Request $request)
    {
        if($request->ajax())
        {
            $img = '';
            $commentId = $request->get('comment_id');

            $session = session('user');
            $userId = $session->id;

            $oldData = Vote::where('identifier_id', '=', $commentId)
                                ->where('identifier', '=', 'comment')
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'up'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'comment_VoteDown', $activityId);
                    }
                    $oldData->type = 'down';
                    $oldData->status = 1;
                    $img = 'images/reaction/voteDown-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'comment_voteDown_off', $commentId);
                    $img = 'images/reaction/voteDown-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'comment_voteDown', $commentId);
                    $img = 'images/reaction/voteDown-on.png';
                }
                $oldData->update();
            }
            else{
                $vote = new Vote;

                $vote->user_id = $userId;
                $vote->identifier_id = $commentId;
                $vote->identifier = 'comment';
                $vote->type = 'down';
                $vote->status = 1;
                $vote->save();
                $this->score($userId, 1, 'comment_voteDown', $commentId);
                
                $img = 'images/reaction/voteDown-on.png';

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
                            $notify->notify_type = 'comment_voteDown';
                            $notify->save();

                            // Mongo notify
                            $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                'sender_id' => $userId,
                                'receiver_id' => $comment->user_id,
                                'identifier_id' => $commentId,
                                'notify_type' => 'comment_voteDown',
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


            $up = Vote::where('identifier_id', '=', $commentId)
                            ->where('identifier', '=', 'comment')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'up')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();
            $down = Vote::where('identifier_id', '=', $commentId)
                            ->where('identifier', '=', 'comment')
                            ->where('status', '=', 1)
                            ->where('type', '=', 'down')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();

            $up = count($up);
            $down = count($down);
            $result = [];
            $result['up'] =  $up;
            $result['down'] =  $down;
            $result['img'] =  $img;

            return $result;
        }
    }

    // list
    function commentVoteUpList(Request $request)
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

            $votes = DB::table('wx_vote')
                        ->where('wx_vote.status', '=', '1')
                        ->where('wx_vote.identifier_id', '=', $commentId)
                        ->where('wx_vote.identifier', '=', 'comment')
                        ->where('wx_vote.type', '=', 'up')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_vote.user_id', '=', 'wx_users.id')
                        ->select('wx_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($votes)
            {
                return $votes;
            }
        }
    }

    function commentVoteDownList(Request $request)
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


            $votes = DB::table('wx_vote')
                        ->where('wx_vote.status', '=', '1')
                        ->where('wx_vote.identifier_id', '=', $commentId)
                        ->where('wx_vote.identifier', '=', 'comment')
                        ->where('wx_vote.type', '=', 'down')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_vote.user_id', '=', 'wx_users.id')
                        ->select('wx_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();
            if($votes)
            {
                return $votes;
            }
        }
    }

}
