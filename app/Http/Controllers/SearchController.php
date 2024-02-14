<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Block;

class SearchController extends Controller
{
    function search($value, Request $request)
    {
        if(empty($value)){
            return redirect(route('404'));
        }

        $searchVal = $value;

        $session = session('user');
        $userId = $session->id;
        
        $blocking = Block::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
        $blocker = Block::where('block_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();


        $members = DB::table('wx_users')
                ->where('status', '=', 1)
                ->where('user_name', 'like', '%'.$searchVal.'%')
                ->orWhere('full_name', 'like', '%'.$searchVal.'%')
                ->whereNotIn('id', $blocking)
                ->whereNotIn('id', $blocker)
                ->limit(3)->get();

        $activitys = DB::table('wx_activity')
                ->where('wx_activity.status', '=', 1)
                ->where('wx_activity.content', 'like', '%'.$searchVal.'%')
                ->whereNotIn('wx_activity.user_id', $blocking)
                ->whereNotIn('wx_activity.user_id', $blocker)
                ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                ->limit(1)->get();

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

        $commentActivity = [];
        $comments = DB::table('wx_comment')
                ->where('status', '=', 1)
                ->where('comment', 'like', '%'.$value.'%')
                ->limit(1)->get();

        foreach($comments as $comment)
        {
            $ca = DB::table('wx_activity')
                                ->where('wx_activity.status', '=', '1')
                                ->where('wx_users.status', '=', '1')
                                ->where('wx_activity.id', '=', $comment->activity_id)
                                ->whereNotIn('wx_activity.user_id', $blocking)
                                ->whereNotIn('wx_activity.user_id', $blocker)
                                ->orderBy('wx_activity.id', 'desc')
                                ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                                ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                                ->get();
            array_push($commentActivity, $ca);

        }

        if($commentActivity){
            foreach($commentActivity as $key => $item)
            {
                if(isset($item[$key]))
                {
                    $string = $item[$key]->content;
                    $remove = array("\n", "\r\n", "\r");
                    $content = str_replace($remove, ' ', $string);
                    $str_arr =  explode(" ",$content);
                    $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                    foreach($str_arr as $value){
                        if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                            if(!isset($item[$key]->preview_url))
                            {
                                $item[$key]->preview_url = $url;
                            }
                            $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                            $item[$key]->content = str_replace($value, $link, $item[$key]->content);
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
                                $item[$key]->content = str_replace($value, $link, $item[$key]->content);
                            }
                        }
                    }
                }

            }
            
        }
        
        
        return view('search.search')
                ->withMembers($members)
                ->withActivitys($activitys)
                ->withCommentActivitys($commentActivity)
                ->withSearchValue($searchVal);

    }
    function xprezer($value)
    {
        if(empty($value)){
            return redirect(route('404'));
        }

        $session = session('user');
        $userId = $session->id;
        
        $blocking = Block::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
        $blocker = Block::where('block_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

        $members = DB::table('wx_users')
                ->where('status', '=', 1)
                ->where('user_name', 'like', '%'.$value.'%')
                ->orWhere('full_name', 'like', '%'.$value.'%')
                ->whereNotIn('id', $blocking)
                ->whereNotIn('id', $blocker)
                ->get();

        return view('search.member')
                ->withMembers($members)
                ->withSearchValue($value);
    }

    function activity($value)
    {
        if(empty($value)){
            return redirect(route('404'));
        }

        $session = session('user');
        $userId = $session->id;
        
        $blocking = Block::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
        $blocker = Block::where('block_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

        $activitys = DB::table('wx_activity')
                ->where('wx_activity.status', '=', 1)
                ->where('wx_activity.content', 'like', '%'.$value.'%')
                ->whereNotIn('wx_activity.user_id', $blocking)
                ->whereNotIn('wx_activity.user_id', $blocker)
                ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                ->paginate(5);

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

        return view('search.activity')
                ->withActivitys($activitys)
                ->withSearchValue($value);
    }

    function comment($value)
    {
        if(empty($value)){
            return redirect(route('404'));
        }

        $session = session('user');
        $userId = $session->id;
        
        $blocking = Block::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('block_id')
                        ->get();
        $blocker = Block::where('block_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

        $commentActivity = [];
        $comments = DB::table('wx_comment')
                ->where('status', '=', 1)
                ->where('comment', 'like', '%'.$value.'%')
                ->whereNotIn('user_id', $blocking)
                ->whereNotIn('user_id', $blocker)
                ->get();

        foreach($comments as $comment)
        {
            $ca = DB::table('wx_activity')
                                ->where('wx_activity.status', '=', '1')
                                ->where('wx_users.status', '=', '1')
                                ->where('wx_activity.id', '=', $comment->activity_id)
                                ->whereNotIn('wx_activity.user_id', $blocking)
                                ->whereNotIn('wx_activity.user_id', $blocker)
                                ->orderBy('wx_activity.id', 'desc')
                                ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                                ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                                ->get();
            array_push($commentActivity, $ca);

        }

        if($commentActivity){
            foreach($commentActivity as $key => $item)
            {
                if(isset($item[$key]))
                {
                    $string = $item[$key]->content;
                    $remove = array("\n", "\r\n", "\r");
                    $content = str_replace($remove, ' ', $string);
                    $str_arr =  explode(" ",$content);
                    $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                    foreach($str_arr as $value){
                        if(preg_match($pattern, $value)){
                            $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                            if(!isset($item[$key]->preview_url))
                            {
                                $item[$key]->preview_url = $url;
                            }
                            $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                            $item[$key]->content = str_replace($value, $link, $item[$key]->content);
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
                                $item[$key]->content = str_replace($value, $link, $item[$key]->content);
                            }
                        }
                    }
                }

            }
            
        }

        return view('search.comment')
                ->withCommentActivitys($commentActivity)
                ->withSearchValue($value);
    }
}
