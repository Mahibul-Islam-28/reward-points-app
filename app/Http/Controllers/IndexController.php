<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\User;
use App\Models\Login;
use App\Models\Follow;
use App\Models\Mention;
use App\Models\Block;

class IndexController extends Controller
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
                
        $members = User::where('status', '=', '1')
                        ->whereNotIn('id', $block)
                        ->whereNotIn('id', $blocker)
                        ->orderBy('score', 'desc')
                        ->limit(4)
                        ->get();

        $wexp = [];
        $wexprez = [];
        $ixprez = [];
        $following = [];
        $followers = [];
        $others = [];

        $ixprez = DB::table('wx_activity')
                        ->where('wx_activity.user_id', '=', $userId)
                        ->where('wx_activity.status', '=', '1')
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->orderBy('wx_activity.id', 'desc')
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->first();

        if($ixprez){
            $string = $ixprez->content;
            $remove = array("\n", "\r\n", "\r");
            $content = str_replace($remove, ' ', $string);
            $str_arr =  explode(" ",$content);
            $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';

            foreach($str_arr as $value){
                if(preg_match($pattern, $value)){
                    $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                    if(!isset($ixprez->preview_url))
                    {
                        $ixprez->preview_url = $url;
                    }
                    $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                    $ixprez->content = str_replace($value, $link, $ixprez->content);
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
                        $ixprez->content = str_replace($value, $link, $ixprez->content);
                    }
                }
            }
        }


        $mention = Mention::where('status', '=', '1')
                            ->where('mention_id', '=', $userId)
                            ->orderBy('id', 'desc')
                            ->select('activity_id')
                            ->get();

        if(count($mention) > 0){

            $wexprez = DB::table('wx_activity')
                            ->where('wx_activity.status', '=', 1)
                            ->whereIn('wx_activity.id', $mention)
                            ->whereNotIn('wx_activity.user_id', $block)
                            ->whereNotIn('wx_activity.user_id', $blocker)
                            ->orderBy('wx_activity.id', 'desc')
                            ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                            ->select('wx_activity.*' , 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                            ->first();


            if($wexprez){
                $string = $wexprez->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);
                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';

                foreach($str_arr as $val){
                    if(preg_match($pattern, $val)){
                        $url = strpos($val, 'http') !== 0 ? "https://$val" : $val;
                        if(!isset($wexprez->preview_url))
                        {
                            $wexprez = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$val."</a>";
                        $wexprez->content = str_replace($val, $link, $wexprez->content);
                    }

                    if(substr($val,0,2) == "@:"){
    
                        $user = substr($val, 2);
                        $position = strpos($user, ':');
                        $mentionId = substr($user, 0, $position);
                        $user = User::find($mentionId);
    
                        if($user)
                        {
                            $route = route('profileWexprez', $user->user_name);                       
                            $link = "<a style='color: blue; font-weight: bold' href='".$route."'>@$user->user_name</a>";
                            $wexprez->content = str_replace($val, $link, $wexprez->content);
                        }
                    }
                }
                
            }


        }  
        
        $fwing = Follow::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('follow_id')
                        ->get();
        if(count($fwing) > 0)
        {

            $following = DB::table('wx_activity')
                        ->where('wx_activity.status', '=', '1')
                        ->where('wx_users.status', '=', '1')
                        ->whereIn('wx_activity.user_id', $fwing)
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->orderBy('wx_activity.id', 'desc')
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->first();
                    

            
            if($following){
                $string = $following->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);
                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($following->preview_url))
                        {
                            $following->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                        $following->content = str_replace($value, $link, $following->content);
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
                            $following->content = str_replace($value, $link, $following->content);
                        }
                    }
                }
            }


        }

        $fwer = Follow::where('follow_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();

        if(count($fwer) > 0)
        {

            $followers = DB::table('wx_activity')
                            ->where('wx_activity.status', '=', '1')
                            ->where('wx_users.status', '=', '1')
                            ->whereIn('wx_activity.user_id', $fwer)
                            ->whereNotIn('wx_activity.user_id', $block)
                            ->whereNotIn('wx_activity.user_id', $blocker)
                            ->orderBy('wx_activity.id', 'desc')
                            ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                            ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                            ->first();

            if($followers)
            {
                $string = $followers->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);
                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        if(!isset($followers->preview_url))
                        {
                            $followers->preview_url = $url;
                        }
                        $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                        $followers->content = str_replace($value, $link, $followers->content);
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
                            $followers->content = str_replace($value, $link, $followers->content);
                        }
                    }
                }
            }

        }
        if($fwer)
        {
            if($fwing)
            {
                $others = DB::table('wx_activity')
                        ->where('wx_activity.status', '=', '1')
                        ->where('wx_users.status', '=', '1')
                        ->whereNotIn('wx_activity.user_id', $fwer)
                        ->whereNotIn('wx_activity.user_id', $fwing)
                        ->whereNotIn('wx_activity.user_id', $block)
                        ->whereNotIn('wx_activity.user_id', $blocker)
                        ->whereNotIn('wx_activity.user_id', [$userId])
                        ->orderBy('wx_activity.id', 'desc')
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->first();

                if($others){
                    $string = $others->content;
                    $remove = array("\n", "\r\n", "\r");
                    $content = str_replace($remove, ' ', $string);
                    $str_arr =  explode(" ",$content);
                    $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                
                    foreach($str_arr as $value){
                        if(preg_match($pattern, $value)){
                            $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                            if(!isset($others->preview_url))
                            {
                                $others->preview_url = $url;
                            }
                            $link = "<a target='_blank' style='color: blue; font-weight: bold' href=".$url.">".$value."</a>";
                            $others->content = str_replace($value, $link, $others->content);
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
                                $others->content = str_replace($value, $link, $others->content);
                            }
                        }
                    }
                }
            }

        }
        return view('index')
                ->withIxprez($ixprez)
                ->withWexprez($wexprez)
                ->withFwing($following)
                ->withFwers($followers)
                ->withOther($others)
                ->withMembers($members);
    }

}
