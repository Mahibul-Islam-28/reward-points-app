<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;
use App\Models\Login;
use App\Models\Mention;
use Illuminate\Support\Facades\Http;

class CommentController extends Controller
{
    function comment(Request $request)
    {
        if(session('user')){
            if($request->ajax())
            {
                $output = '';
                $id = $request->get('id');
                $parentId = $request->get('parent_id');
    
                $activity = Activity::find($id);
                
                if($activity){
    
                    if($parentId == 0){
                        $output = '<form method="post" class="commentForm" id="commentForm-'.$activity->id.'">
                        <textarea name="comment-'.$activity->id.'" rows="3" ></textarea>';
                        echo json_encode($output);
                    }
                    else{
                        $output = '<form method="post" class="replyForm" id="replyForm-'.$parentId.'">
                        <textarea name="comment-'.$activity->id.'" rows="3" ></textarea>';
                        echo json_encode($output);
                    }
                }
            }
        } 
    }
    
    function save(Request $request)
    {
        if(session('user')){
            if($request->ajax())
            {
                $session = session('user');
                $userId = $session->id;
                
                $activityId = $request->post('activity_id');
                $parentId = $request->post('parent_id');
                $cmnt = $request->post('comment');

                $comment = new Comment;
                $comment->comment = $cmnt;
                $comment->activity_id = $activityId;
                $comment->parent_id = $parentId;
                $comment->user_id = $userId;
                $comment->save();
    
                if($comment)
                {
                    $this->score($userId, 1, 'comment_create', $comment->id);

                    // Create Notification
                    $activity = Activity::find($activityId);
                    if($activity)
                    {
                        if($activity->user_id != $userId)
                        {
                            $notify = new Notification;
                            $notify->sender_id = $userId;
                            $notify->receiver_id = $activity->user_id;
                            $notify->identifier_id = $comment->id;
                            $notify->notify_type = 'comment';
                            $notify->save();

                            // Mongo notify
                            $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                'user_id' => $activity->user_id
                            ]);
                            $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                'sender_id' => $userId,
                                'receiver_id' => $activity->user_id,
                                'identifier_id' => $activityId,
                                'notify_type' => 'comment',
                            ]);
                        }

                        

                        // Creating user from Post
                        $str_arr =  explode(" ",$cmnt);
                        foreach($str_arr as $value){
                            if(substr($value,0,1) == "@"){
                                $userName = substr($value,1);
                                $user = User::where('user_name', '=', $userName)->first();

                                if(!$user){

                                    $login = new Login;
                                    $user = new User;

                                    $verifyToken = md5(time().rand());

                                    $login->user_name = $userName;
                                    $login->email = $userName."@wexprez.com";
                                    $login->mobile_no = "0123456789";
                                    $login->password = md5('wxp321');
                                    $login->verify_token = $verifyToken;
                                    $login->save();

                                    
                                    $loginId = $login->id;

                                    $user->user_name = $userName;
                                    $user->full_name = $userName;
                                    $user->login_id = $loginId;
                                    $user->status = 0;
                                    $user->save();

                                }

                                $mention = new Mention;
                                $mention->mention_id = $user->id;
                                $mention->user_id = $userId;
                                $mention->activity_id = $activityId;
                                $mention->save();

                                $cmnt = $comment->comment;
                                $replace = '@:'.$user->id.':';
                                $cmnt = str_replace($value, $replace, $cmnt);
                                $comment->comment = $cmnt;
                                $comment->update();
                                
                                // Create Notification
                                if($userId != $user->id)
                                {
                                    $notify = new Notification;
                                    $notify->sender_id = $userId;
                                    $notify->receiver_id = $user->id;
                                    $notify->identifier_id = $comment->id;
                                    $notify->notify_type = 'comment_mention';
                                    $notify->save();
    
                                    // Mongo notify
                                    $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                        'user_id' => $user->id
                                    ]);
                                    $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                        'sender_id' => $userId,
                                        'receiver_id' => $user->id,
                                        'identifier_id' => $comment->id,
                                        'notify_type' => 'comment_mention',
                                    ]);
                                } 

                            }	
                        }

                    }

                    return $comment;
                }
            }
        }

        else
        {
            return false;
        }
    }

    function edit(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $output = '';
                $id = $request->get('id');
    
                $comment = Comment::find($id);
                
                if($comment){

                    $activity = Activity::find($comment->activity_id);

                    $str_arr =  explode(" ",$comment->comment);
                    foreach($str_arr as $value){
                        if(substr($value,0,2) == "@:"){
        
                            $user = substr($value, 2);
                            $position = strpos($user, ':');
                            $mentionId = substr($user, 0, $position);
                            $user = User::find($mentionId);
        
                            if($user)
                            {
                                $route = route('profileWexprez', $user->user_name);                       
                                $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                                $comment->comment = str_replace($value, $link, $comment->comment);
                            }
                        }
                    }
                   
    
                    $output = '<form method="post" id="edit-commentForm-'.$comment->id.'">
                                <textarea name="edit-comment">'.strip_tags($comment->comment).'</textarea>';
                                echo json_encode($output);
                }
                    
            }
        } 
    }

    function update(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $output = '';
                $id = $request->post('id');
                $content = $request->post('comment');
    
                $comment = Comment::find($id);

                if($comment)
                {
                    $comment->comment = $content;
                    $comment->update();

                    $output = $comment->comment;

                    // Creating user from Post
                    $str_arr =  explode(" ",$content);
                    foreach($str_arr as $value){
                        if(substr($value,0,1) == "@"){
                            $userName = substr($value,1);
                            $user = User::where('user_name', '=', $userName)->first();

                            if(!$user){

                                $login = new Login;
                                $user = new User;

                                $verifyToken = md5(time().rand());

                                $login->user_name = $userName;
                                $login->email = $userName."@wexprez.com";
                                $login->mobile_no = "0123456789";
                                $login->password = md5('wxp321');
                                $login->verify_token = $verifyToken;
                                $login->save();

                                
                                $loginId = $login->id;

                                $user->user_name = $userName;
                                $user->full_name = $userName;
                                $user->login_id = $loginId;
                                $user->status = 0;
                                $user->save();

                            }

                            else{

                                $cmnt = $comment->comment;
                                $replace = '@:'.$user->id.':';
                                $cmnt = str_replace($value, $replace, $cmnt);
                                $comment->comment = $cmnt;
                                $comment->update();

                                $oldMention = Mention::where('user_id', '=', $comment->user_id)
                                                ->where('mention_id', '=', $user->id)
                                                ->where('activity_id', '=', $comment->activity_id)
                                                ->first();
            
                                if(!$oldMention)
                                {
                                    $mention = new Mention;
                                    $mention->mention_id = $user->id;
                                    $mention->user_id = $comment->user_id;
                                    $mention->activity_id = $comment->activity_id;
                                    $mention->save();
        
                                    // Create Notification
                                    if($comment->user_id != $user->id)
                                    {
                                        $notify = new Notification;
                                        $notify->sender_id = $comment->user_id;
                                        $notify->receiver_id = $user->id;
                                        $notify->identifier_id = $id;
                                        $notify->notify_type = 'comment_mention';
                                        $notify->save();
    
                                        // Mongo notify
                                        $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                            'user_id' => $user->id
                                        ]);
                                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                            'sender_id' => $comment->user_id,
                                            'receiver_id' => $user->id,
                                            'identifier_id' => $id,
                                            'notify_type' => 'comment_mention',
                                        ]);
                                    }
                                }

                            }

                        }

                    }

                    echo json_encode($output);

                }
                    
            }
        } 
    }

    function delete(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $commentId = $request->get('comment_id');

                $comment = Comment::find($commentId);

                if($comment)
                {
                    $comment->status = 0;
                    $comment->update();

                    $this->score($comment->user_id, -1, 'comment_delete', $commentId);
                    return $comment;
                }
        
            }
        } 
    }

}
