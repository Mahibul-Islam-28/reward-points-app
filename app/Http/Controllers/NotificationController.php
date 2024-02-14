<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\User;
use App\Models\Block;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    function index()
    {
        $session = session('user');
        $userId = $session->id;

        $block = Block::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
        $blocker = Block::where('block_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

        $profile = DB::table('wx_users')
                    ->where('wx_users.id', '=', $userId)
                    ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                    ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                    ->first();
        $output = [];

        $notification = Notification::where('receiver_id', '=', $userId)
                                    ->where('status', '=', 0)
                                    ->whereNotIn('sender_id', $block)
                                    ->whereNotIn('sender_id', $blocker)
                                    ->get();

        foreach($notification as $key => $value)
        {

            if($value->notify_type == 'follow')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has started following you';
                $notification[$key]['text'] = $text;
                
            }
            else if($value->notify_type == 'member_voteUp')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has liked you';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'member_voteDown')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has disliked you';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'activity_mention')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has mentioned you in a activity';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment_mention')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has mentioned you in a comment';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has commented on your activity';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'activity_voteUp')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has voted up on your activity';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'activity_voteDown')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has voted down on your activity';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'activity_real')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted real on your activity';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'activity_fake')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted fake on your activity';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'comment_voteUp')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has voted up on your comment';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'comment_voteDown')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has voted down on your comment';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'comment_real')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted real on your comment';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'comment_fake')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted fake on your comment';
                $notification[$key]['text'] = $text;
            }
            else if($value->notify_type == 'app_version')
            {
                $text = 'New app version has been released';
                $notification[$key]['text'] = $text;
            }
        }
        return view('notification.index')
                    ->withMyProfile($profile)
                    ->withNotifications($notification);
    }
  
    function readPage($id)
    {
        $notification = Notification::find($id);
        if($notification)
        {
            $notification->status = 1;
            $notification->update();
            
            if($notification->notify_type == 'member_vote' || $notification->notify_type == 'follow')
            {
                $user = User::find($notification->sender_id);
                return redirect(route('profileIxprez', $user->user_name));
            }
            else{
                return redirect(route('singleActivity', $notification->identifier_id));
            }
        }
        return view('notification.read');
    }

    // Ajax
    function list(Request $request)
    {
        $session = session('user');
        $userId = $session->id;
        $output = [];
        $block = Block::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
        $blocker = Block::where('block_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();       

        $notification = Notification::where('receiver_id', '=', $userId)
                                    ->where('status', '=', 0)
                                    ->whereNotIn('sender_id', $block)
                                    ->whereNotIn('sender_id', $blocker)
                                    ->get();

        foreach($notification as $key => $value)
        {
            if($value->notify_type == 'follow')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has started following you';
                $output[$key] = $text;
                
            }
            else if($value->notify_type == 'member_voteUp')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has liked you';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'member_voteDown')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has disliked you';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'activity_mention')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has mentioned you in a activity';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment_mention')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has mentioned you in a comment';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has commented on your activity';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'voteUp')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has liked on your activity';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'voteDown')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has disliked on your activity';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'real')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted real on your activity';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'fake')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted fake on your activity';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment_voteUp')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has liked on your comment';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment_voteDown')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has disliked on your comment';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment_real')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted real on your comment';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'comment_fake')
            {
                $user = User::find($value->sender_id);
                $text = $user->full_name . ' has reacted fake on your comment';
                $output[$key] = $text;
            }
            else if($value->notify_type == 'app_version')
            {
                $text = 'New app version has been released';
                $notification[$key]['text'] = $text;
            }
        }

        return json_encode($output);
    }

    function read(Request $request)
    {
        if($request->ajax())
        {
            $id = $request->get('id');

            $notification = Notification::find($id);

            if($notification)
            {
                $notification->status = 1;
                $notification->update();
                
            }
            return $notification;
        }
    }
    
}
