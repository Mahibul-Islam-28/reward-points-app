<?php

namespace App\Http\Controllers;

// require 'C:\xampp\htdocs\wexprez_api\application\libraries\_librabry\autoload.php';
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Login;
use App\Models\User;
use App\Models\Activity;
use App\Models\Follow;
use App\Models\Country;
use App\Models\Mention;
use App\Models\Block;
use App\Models\Interest;
use App\Models\UserInterest;

class ProfileController extends Controller
{
                    
    function ixprez($userName)
    {

        $user = User::where('user_name', '=', $userName)->first();
        if(empty($user)){
            return redirect(route('404'));
        }
        
        $userId = $user->id;

        $session = session('user');
        if($session)
        {
            $sessionId = $session->id;
        }
        else{
            $sessionId = $user->id;
        }
        
        $preview_url = '';

        $block = Block::where('user_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('block_id')
                    ->get();
        $blocker = Block::where('block_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('user_id')
                    ->get();

        $profile = DB::table('wx_users')
                        ->where('wx_users.user_name', '=', $userName)
                        ->whereNotIn('wx_users.id', $block)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                        ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                        ->first();
        if(empty($profile)){
            return redirect(route('404'));
        }

        
        $votes = DB::table('wx_vote')
                    ->where('wx_vote.status', '=', '1')
                    ->whereNotIn('wx_vote.user_id', $block)
                    ->whereNotIn('wx_vote.user_id', $blocker)
                    ->join('wx_users', 'wx_vote.user_id', '=', 'wx_users.id')
                    ->select('wx_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();

        $reacts = DB::table('wx_react')
                    ->where('wx_react.status', '=', '1')
                    ->whereNotIn('wx_react.user_id', $block)
                    ->whereNotIn('wx_react.user_id', $blocker)
                    ->join('wx_users', 'wx_react.user_id', '=', 'wx_users.id')
                    ->select('wx_react.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();


        $activitys = DB::table('wx_activity')
                        ->where('wx_activity.status', '=', '1')
                        ->where('wx_activity.user_id', '=', $userId)
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->orderBy('wx_activity.id', 'desc')
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->paginate(5);

        $allActivity = DB::table('wx_activity')
                        ->where('wx_activity.status', '=', '1')
                        ->where('wx_activity.user_id', '=', $userId)
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->orderBy('wx_activity.id', 'desc')
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();

        if($activitys){
            foreach($activitys as $activity)
            {
                $string = $activity->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);

                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($activity->preview_url))
                        {
                            $activity->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                        $activity->content = str_replace($value, $link, $activity->content);
                    }

                    if(substr($value,0,2) == "@:"){
    
                        $user = substr($value, 2);
                        $position = strpos($user, ':');
                        $mentionId = substr($user, 0, $position);
                        $user = User::find($mentionId);
    
                        if($user)
                        {
                            $route = route('profileWexprez', $user->user_name);                       
                            $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
                    }
                }
            }
        }

        if($allActivity){
            foreach($allActivity as $activity)
            {
                $string = $activity->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);

                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($activity->preview_url))
                        {
                            $activity->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                        $activity->content = str_replace($value, $link, $activity->content);
                    }

                    if(substr($value,0,2) == "@:"){
    
                        $user = substr($value, 2);
                        $position = strpos($user, ':');
                        $mentionId = substr($user, 0, $position);
                        $user = User::find($mentionId);
    
                        if($user)
                        {
                            $route = route('profileWexprez', $user->user_name);                       
                            $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
                    }
                }
            }
        }

        $comments = DB::table('wx_comment')
                    ->where('wx_comment.status', '=', '1')
                    ->whereNotIn('wx_comment.user_id', $block)
                    ->whereNotIn('wx_comment.user_id', $blocker)
                    ->join('wx_users', 'wx_comment.user_id', '=', 'wx_users.id')
                    ->select('wx_comment.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();
        if($comments){
            foreach($comments as $comment)
            {
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
            }
            
        }

        return view('profile.ixprez')
                ->withMyProfile($profile)
                ->withAllActivity($allActivity)
                ->withActivitys($activitys)
                ->withComments($comments)
                ->withVotes($votes)
                ->withReacts($reacts);
    }



    function wexprez($userName)
    {

        // $result = Http::post('http://localhost/wexprez_api/api/notification/mongo_get_count', [
        //     'user_id' => 3
        // ]);
                
        $session = session('user');
        $sessionId = $session->id;
        $preview_url = '';

        $block = Block::where('user_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('block_id')
                    ->get();
        $blocker = Block::where('block_id', '=', $sessionId)
                ->where('status', '=', 1)
                ->select('user_id')
                ->get();
        
        $profile = DB::table('wx_users')
                        ->where('wx_users.user_name', '=', $userName)
                        ->whereNotIn('wx_users.id', $block)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                        ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                        ->first();
        if(empty($profile)){
            return redirect(route('404'));
        }

        $mention = Mention::where('status', '=', '1')
                        ->where('mention_id', '=', $profile->id)
                        ->orderBy('id', 'desc')
                        ->select('activity_id')
                        ->get();
        
        
        $activitys = DB::table('wx_activity')
                        ->where('wx_activity.status', '=', '1')
                        ->where('wx_users.status', '=', '1')
                        ->whereIn('wx_activity.id', $mention)
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->paginate(5);
        $allActivity = DB::table('wx_activity')
                        ->where('wx_activity.status', '=', '1')
                        ->where('wx_users.status', '=', '1')
                        ->whereIn('wx_activity.id', $mention)
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();

            if($activitys){
                foreach($activitys as $activity)
                {
                    $string = $activity->content;
                    $remove = array("\n", "\r\n", "\r");
                    $content = str_replace($remove, ' ', $string);
                    $str_arr =  explode(" ",$content);
    
                    $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                    foreach($str_arr as $value){
                        if(preg_match($pattern, $value)){
                            $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                            if(!isset($activity->preview_url))
                            {
                                $activity->preview_url = $url;
                            }
                            $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
    
                        if(substr($value,0,2) == "@:"){
        
                            $user = substr($value, 2);
                            $position = strpos($user, ':');
                            $mentionId = substr($user, 0, $position);
                            $user = User::find($mentionId);
        
                            if($user)
                            {
                                $route = route('profileWexprez', $user->user_name);                       
                                $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                                $activity->content = str_replace($value, $link, $activity->content);
                            }
                        }
                    }
                }
            }
            if($allActivity){
                foreach($allActivity as $activity)
                {
                    $string = $activity->content;
                    $remove = array("\n", "\r\n", "\r");
                    $content = str_replace($remove, ' ', $string);
                    $str_arr =  explode(" ",$content);
    
                    $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                    foreach($str_arr as $value){
                        if(preg_match($pattern, $value)){
                            $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                            if(!isset($activity->preview_url))
                            {
                                $activity->preview_url = $url;
                            }
                            $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
    
                        if(substr($value,0,2) == "@:"){
        
                            $user = substr($value, 2);
                            $position = strpos($user, ':');
                            $mentionId = substr($user, 0, $position);
                            $user = User::find($mentionId);
        
                            if($user)
                            {
                                $route = route('profileWexprez', $user->user_name);                       
                                $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                                $activity->content = str_replace($value, $link, $activity->content);
                            }
                        }
                    }
                }
            }

                
        return view('profile.wexprez')
                ->withMyProfile($profile)
                ->withAllActivity($allActivity)
                ->withActivitys($activitys);
    }

    function following($userName)
    {
        
        $session = session('user');
        $sessionId = $session->id;
        $preview_url = '';

        $block = Block::where('user_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('block_id')
                    ->get();
        $blocker = Block::where('block_id', '=', $sessionId)
                ->where('status', '=', 1)
                ->select('user_id')
                ->get();
        
        $profile = DB::table('wx_users')
                        ->where('wx_users.user_name', '=', $userName)
                        ->whereNotIn('wx_users.id', $block)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                        ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                        ->first();
        if(empty($profile)){
            return redirect(route('404'));
        }

        $follow = Follow::where('user_id', '=', $profile->id)
                        ->where('status', '=', 1)
                        ->select('follow_id')
                        ->get();
        
        $following = DB::table('wx_activity')
                    ->where('wx_activity.status', '=', '1')
                    ->where('wx_users.status', '=', '1')
                    ->whereIn('wx_activity.user_id', $follow)
                    ->whereNotIn('wx_activity.user_id', $block)
                    ->whereNotIn('wx_activity.user_id', $blocker)
                    ->orderBy('wx_activity.id', 'desc')
                    ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                    ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->paginate(5);

        $allActivity = DB::table('wx_activity')
                    ->where('wx_activity.status', '=', '1')
                    ->where('wx_users.status', '=', '1')
                    ->whereIn('wx_activity.user_id', $follow)
                    ->whereNotIn('wx_activity.user_id', $block)
                    ->whereNotIn('wx_activity.user_id', $blocker)
                    ->orderBy('wx_activity.id', 'desc')
                    ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                    ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();

        if($following){
            foreach($following as $activity)
            {
                $string = $activity->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);

                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($activity->preview_url))
                        {
                            $activity->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                        $activity->content = str_replace($value, $link, $activity->content);
                    }

                    if(substr($value,0,2) == "@:"){
    
                        $user = substr($value, 2);
                        $position = strpos($user, ':');
                        $mentionId = substr($user, 0, $position);
                        $user = User::find($mentionId);
    
                        if($user)
                        {
                            $route = route('profileWexprez', $user->user_name);                       
                            $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
                    }
                }
            }
        }
        if($allActivity){
            foreach($allActivity as $activity)
            {
                $string = $activity->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);

                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($activity->preview_url))
                        {
                            $activity->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                        $activity->content = str_replace($value, $link, $activity->content);
                    }

                    if(substr($value,0,2) == "@:"){
    
                        $user = substr($value, 2);
                        $position = strpos($user, ':');
                        $mentionId = substr($user, 0, $position);
                        $user = User::find($mentionId);
    
                        if($user)
                        {
                            $route = route('profileWexprez', $user->user_name);                       
                            $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
                    }
                }
            }
        }
        return view('profile.following')
                ->withMyProfile($profile)
                ->withActivitys($following)
                ->withAllActivity($allActivity);
    }

    function follower($userName)
    {
        
        $session = session('user');
        $sessionId = $session->id;
        $preview_url = '';

        $block = Block::where('user_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('block_id')
                    ->get();
        $blocker = Block::where('block_id', '=', $sessionId)
                ->where('status', '=', 1)
                ->select('user_id')
                ->get();
        
        $profile = DB::table('wx_users')
                        ->where('wx_users.user_name', '=', $userName)
                        ->whereNotIn('wx_users.id', $block)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                        ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                        ->first();
        if(empty($profile)){
            return redirect(route('404'));
        }

        $follow = Follow::where('follow_id', '=', $profile->id)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();
        
        
        $follower = DB::table('wx_activity')
                    ->where('wx_activity.status', '=', '1')
                    ->where('wx_users.status', '=', '1')
                    ->whereIn('wx_activity.user_id', $follow)
                    ->whereNotIn('wx_activity.user_id', $block)
                    ->whereNotIn('wx_activity.user_id', $blocker)
                    ->orderBy('wx_activity.id', 'desc')
                    ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                    ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->paginate(5);

        $allActivity = DB::table('wx_activity')
                    ->where('wx_activity.status', '=', '1')
                    ->where('wx_users.status', '=', '1')
                    ->whereIn('wx_activity.user_id', $follow)
                    ->whereNotIn('wx_activity.user_id', $block)
                    ->whereNotIn('wx_activity.user_id', $blocker)
                    ->orderBy('wx_activity.id', 'desc')
                    ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                    ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();


        if($follower){
            foreach($follower as $activity)
            {
                $string = $activity->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);
                
                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($activity->preview_url))
                        {
                            $activity->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$value.">".$value."</a>";
                        $activity->content = str_replace($value, $link, $activity->content);
                    }

                    if(substr($value,0,2) == "@:"){
    
                        $user = substr($value, 2);
                        $position = strpos($user, ':');
                        $mentionId = substr($user, 0, $position);
                        $user = User::find($mentionId);
    
                        if($user)
                        {
                            $route = route('profileWexprez', $user->user_name);                       
                            $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
                    }
                }
            }
        }
        if($allActivity){
            foreach($allActivity as $activity)
            {
                $string = $activity->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);
                
                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($activity->preview_url))
                        {
                            $activity->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$value.">".$value."</a>";
                        $activity->content = str_replace($value, $link, $activity->content);
                    }

                    if(substr($value,0,2) == "@:"){
    
                        $user = substr($value, 2);
                        $position = strpos($user, ':');
                        $mentionId = substr($user, 0, $position);
                        $user = User::find($mentionId);
    
                        if($user)
                        {
                            $route = route('profileWexprez', $user->user_name);                       
                            $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
                    }
                }
            }
        }
                
        return view('profile.follower')
                ->withMyProfile($profile)
                ->withActivitys($follower)
                ->withAllActivity($allActivity);
    }

    function archive($userName)
    {
        $session = session('user');
        $preview_url = '';
        if($session)
        {
            $sessionId = $session->id;
            $block = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('user_id')
                    ->get();

            $profile = DB::table('wx_users')
                            ->where('wx_users.user_name', '=', $userName)
                            ->where('wx_users.id', '=', $sessionId)
                            ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                            ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                            ->first();
            if(empty($profile)){
                return redirect(route('404'));
            }
    
            
            
            $activitys = DB::table('wx_activity')
                        ->where('wx_activity.user_id', '=', $sessionId)
                        ->where('wx_activity.status', '=', '2')
                        ->where('wx_users.status', '=', '1')
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->orderBy('wx_activity.id', 'desc')
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->paginate(5);

            $allActivity = DB::table('wx_activity')
                        ->where('wx_activity.user_id', '=', $sessionId)
                        ->where('wx_activity.status', '=', '2')
                        ->where('wx_users.status', '=', '1')
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->orderBy('wx_activity.id', 'desc')
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();

                        
            if($activitys){
                foreach($activitys as $activity)
                {
                    $string = $activity->content;
                    $remove = array("\n", "\r\n", "\r");
                    $content = str_replace($remove, ' ', $string);
                    $str_arr =  explode(" ",$content);
    
                    $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                    foreach($str_arr as $value){
                        if(preg_match($pattern, $value)){
                            $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                            if(!isset($activity->preview_url))
                            {
                                $activity->preview_url = $url;
                            }
                            $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
    
                        if(substr($value,0,2) == "@:"){
        
                            $user = substr($value, 2);
                            $position = strpos($user, ':');
                            $mentionId = substr($user, 0, $position);
                            $user = User::find($mentionId);
        
                            if($user)
                            {
                                $route = route('profileWexprez', $user->user_name);                       
                                $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                                $activity->content = str_replace($value, $link, $activity->content);
                            }
                        }
                    }
                }
            }
            if($allActivity){
                foreach($allActivity as $activity)
                {
                    $string = $activity->content;
                    $remove = array("\n", "\r\n", "\r");
                    $content = str_replace($remove, ' ', $string);
                    $str_arr =  explode(" ",$content);
    
                    $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                    foreach($str_arr as $value){
                        if(preg_match($pattern, $value)){
                            $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                            if(!isset($activity->preview_url))
                            {
                                $activity->preview_url = $url;
                            }
                            $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
    
                        if(substr($value,0,2) == "@:"){
        
                            $user = substr($value, 2);
                            $position = strpos($user, ':');
                            $mentionId = substr($user, 0, $position);
                            $user = User::find($mentionId);
        
                            if($user)
                            {
                                $route = route('profileWexprez', $user->user_name);                       
                                $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                                $activity->content = str_replace($value, $link, $activity->content);
                            }
                        }
                    }
                }
            }
                    
            return view('profile.archive')
                    ->withMyProfile($profile)
                    ->withActivitys($activitys)
                    ->withAllActivity($allActivity);
        }
        else{
            return redirect(route('404'));
        }
       
    }



    // profile
    function profile($userName)
    {
        $session = session('user');
        if($session)
        {
            $user = User::where('user_name', '=', $userName)->first();
            if(empty($user)){
                return redirect(route('404'));
            }
    
            
            $sessionId = $session->id;
            $inst = '';
    
            $block = Block::where('user_id', '=', $sessionId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
            $blocker = Block::where('block_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('user_id')
                    ->get();
            
            $profile = DB::table('wx_users')
                            ->where('wx_users.user_name', '=', $userName)
                            ->whereNotIn('wx_users.id', $block)
                            ->whereNotIn('wx_users.id', $blocker)
                            ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                            ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                            ->first();
            if(empty($profile)){
                return redirect(route('404'));
            }

            $userInterest = UserInterest::where('user_id', '=', $sessionId)->first();
            if($userInterest)
            {
                $interestId = explode(",",$userInterest->interest_id);

                $interest = DB::table('wx_interest')
                                ->whereIn('wx_interest.id', $interestId)
                                ->get();

                foreach($interest as $ins)
                {
                    $inst .= $ins->interest_name.', ';
                }

            }

            $profile->interest = $inst;
    
    
            return view('profile.profile')
                    ->withMyProfile($profile);
        }
        else{

            $user = User::where('user_name', '=', $userName)->first();
            if(empty($user)){
                return redirect(route('404'));
            }
            $userId = $user->id;
            $inst = '';

            
            $profile = DB::table('wx_users')
                            ->where('wx_users.user_name', '=', $userName)
                            ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                            ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                            ->first();
            if(empty($profile)){
                return redirect(route('404'));
            }

            $userInterest = UserInterest::where('user_id', '=', $userId)->first();
            if($userInterest)
            {
                $interestId = explode(",",$userInterest->interest_id);

                $interest = DB::table('wx_interest')
                                ->whereIn('wx_interest.id', $interestId)
                                ->get();

                foreach($interest as $ins)
                {
                    $inst .= $ins->interest_name.', ';
                }

            }

            $profile->interest = $inst;
    
    
            return view('profile.profile')
                    ->withMyProfile($profile);

        }
    }

    function edit($userName)
    {
        $session = session('user');
        $sessionId = $session->id;
        $interestId = '';

        $block = Block::where('user_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('block_id')
                    ->get();
        $blocker = Block::where('block_id', '=', $sessionId)
                ->where('status', '=', 1)
                ->select('user_id')
                ->get();
        
        $profile = DB::table('wx_users')
                        ->where('wx_users.user_name', '=', $userName)
                        ->whereNotIn('wx_users.id', $block)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                        ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                        ->first();
        if(empty($profile)){
            return redirect(route('404'));
        }
        
        $country = Country::all();

        $interest = Interest::all();
        $userInterest = UserInterest::where('user_id', '=', $sessionId)->first();
        if($userInterest)
        {
            $interestId = explode(",",$userInterest->interest_id);
        }
        return view('profile.edit')
                ->withMyProfile($profile)
                ->withCountrys($country)
                ->withInterests($interest)
                ->withUserInterest($interestId);
        
    }

    function update(Request $request, $userName)
    {
        $user = User::where('user_name', '=', $userName)->first();
        if(empty($user)){
            return redirect(route('404'));
        }

        $interest = $request->interest;
        $inst = '';
        if($interest != null)
        {
            foreach($interest  as $index => $item)
            {
                if($index < 10)
                {
                    $inst .= $item.',';
                }
                
            }
        }
        

        $name = $request->fullName;
        $status = $request->status;
        $birthDate = $request->birthDate;
        $sex = $request->sex;
        $bio = $request->bio;
        $country = $request->country;

        $user->full_name = $name;
        $user->marital_status = $status;
        $user->birth_date = $birthDate;
        $user->sex = $sex;
        $user->country = $country;
        $user->bio = $bio;
        $user->update();

        $userInterest = UserInterest::where('user_id', '=', $user->id)->first();
        if($userInterest)
        {
            $userInterest->interest_id = $inst;
            $userInterest->update();
        }
        else{
            $userInterest = new UserInterest;

            $userInterest->interest_id = $inst;
            $userInterest->user_id = $user->id;
            $userInterest->status = 1;
            $userInterest->save();
        }

        if($user)
        {
            return redirect(route('profile', $userName));
        }
        else{
            return back();
        }

    }

    function profileImage($userName)
    {
        $session = session('user');
        $sessionId = $session->id;

        $block = Block::where('user_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('block_id')
                    ->get();
        $blocker = Block::where('block_id', '=', $sessionId)
                ->where('status', '=', 1)
                ->select('user_id')
                ->get();
        
        $profile = DB::table('wx_users')
                        ->where('wx_users.user_name', '=', $userName)
                        ->whereNotIn('wx_users.id', $block)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                        ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                        ->first();
        if(empty($profile)){
            return redirect(route('404'));
        }

        return view('profile.profileImage')
                ->withMyProfile($profile);
        
    }
    function profileImageStore(Request $request, $userName)
    {
        $user = User::where('user_name', '=', $userName)->first();
        if(empty($user)){
            return redirect(route('404'));
        }

        if($request->ajax())
        {
            $image_data = $request->image;
            
            $image_array_1 = explode(";", $image_data);
            $image_array_2 = explode(",", $image_array_1[1]);
            $data = base64_decode($image_array_2[1]);
            $image_name = $user->id . '_' . time() . '.jpg';
            $upload_path = 'G:\\xampp\\htdocs\\wexprez_api\\uploads\\user_profile\\' . $image_name;
            file_put_contents($upload_path, $data);

            $user->profile_image = $image_name;
            $user->update();
            return response()->json(['path' => 'G:\\xampp\\htdocs\\wexprez_api\\uploads\\user_profile\\' . $image_name]);
        }

    }


    function coverImage($userName)
    {
        $session = session('user');
        $sessionId = $session->id;

        $block = Block::where('user_id', '=', $sessionId)
                    ->where('status', '=', 1)
                    ->select('block_id')
                    ->get();
        $blocker = Block::where('block_id', '=', $sessionId)
                ->where('status', '=', 1)
                ->select('user_id')
                ->get();
        
        $profile = DB::table('wx_users')
                        ->where('wx_users.user_name', '=', $userName)
                        ->whereNotIn('wx_users.id', $block)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                        ->select('wx_users.*', 'wx_login.email', 'wx_login.mobile_no')
                        ->first();
        if(empty($profile)){
            return redirect(route('404'));
        }
        
        return view('profile.coverImage')
                ->withMyProfile($profile);
        
    }
    function coverImageStore(Request $request, $userName)
    {
        $user = User::where('user_name', '=', $userName)->first();
        if(empty($user)){
            return redirect(route('404'));
        }

        if($request->ajax())
        {
            $image_data = $request->image;
            
            $image_array_1 = explode(";", $image_data);
            $image_array_2 = explode(",", $image_array_1[1]);
            $data = base64_decode($image_array_2[1]);
            $image_name = $user->id . '_' . time() . '.jpg';
            $upload_path = 'G:\\xampp\\htdocs\\wexprez_api\\uploads\\user_profile\\' . $image_name;
            file_put_contents($upload_path, $data);

            $user->cover_image = $image_name;
            $user->update();
            return response()->json(['path' => 'G:\\xampp\\htdocs\\wexprez_api\\uploads\\user_profile\\' . $image_name]);
        }

    }


}
