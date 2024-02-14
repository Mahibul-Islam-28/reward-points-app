<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Models\Login;
use App\Models\User;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Block;

class ProfileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        view()->composer('*',function($view){
            $session = session('user');
            
            if($session){
                $userName = $session->user_name;
                $user = User::where('user_name', '=', $userName)->first();
                $userId = $user->id;

                $blocking = Block::where('user_id', '=', $userId)
                                ->where('status', '=', 1)
                                ->select('block_id')
                                ->get();
                $blocker = Block::where('block_id', '=', $userId)
                                ->where('status', '=', 1)
                                ->select('user_id')
                                ->get();

                $profile = DB::table('wx_users')
                                ->where('wx_users.user_name', '=', $userName)
                                ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                                ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                                ->first();
                $follow = DB::table('wx_follow')
                                ->where('user_id', '=', $userId)
                                ->where('status', '=', 1)
                                 ->get();
                $block = DB::table('wx_block')
                                ->where('user_id', '=', $userId)
                                ->where('status', '=', 1)
                                 ->get();
                $report = DB::table('wx_member_report')
                                ->where('user_id', '=', $userId)
                                ->where('status', '=', 1)
                                 ->get();
                $count = DB::table('wx_notification')
                                ->where('receiver_id', '=', $userId)
                                ->where('status', '=', 0)
                                ->get();
                $count = count($count);

                $notification = Notification::where('receiver_id', '=', $userId)
                                    ->where('status', '=', 0)
                                    ->whereNotIn('sender_id', $blocking)
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
                    else if($value->notify_type == 'mention')
                    {
                        $user = User::find($value->sender_id);
                        $text = $user->full_name . ' has mentioned you';
                        $notification[$key]['text'] = $text;
                    }
                    else if($value->notify_type == 'comment')
                    {
                        $user = User::find($value->sender_id);
                        $text = $user->full_name . ' has commented on your activity';
                        $notification[$key]['text'] = $text;
                    }
                    else if($value->notify_type == 'activity_mention')
                    {
                        $user = User::find($value->sender_id);
                        $text = $user->full_name . ' has mentioned you in a activity';
                        $notification[$key]['text'] = $text;
                    }
                    else if($value->notify_type == 'comment_mention')
                    {
                        $user = User::find($value->sender_id);
                        $text = $user->full_name . ' has mentioned you in a comment';
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
                
                return $view
                    ->withProfile($profile)
                    ->withFollowing($follow)
                    ->withReporting($report)
                    ->withBlocking($block)
                    ->withNotifyCount($count)
                    ->withNotifyList($notification);
            }
            
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
