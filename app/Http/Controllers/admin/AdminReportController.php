<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\User;
use App\Models\MemberReport;
use App\Models\CommentReport;

class AdminReportController extends Controller
{
    function userList()
    {
        $reports = MemberReport::where('status', '=', 1)
                                ->orderBy('created_at', 'desc')
                                ->get();

        foreach($reports as $key => $value)
        {
            $r = DB::table('wx_users')
                    ->where('wx_users.id', '=', $value->user_id)
                    ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                    ->select('wx_users.full_name', 'wx_users.profile_image', 'wx_login.email')
                    ->first();
            $reports[$key]['user'] = $r;
        }
        
        foreach($reports as $key => $value)
        {
            $rd = DB::table('wx_users')
                    ->where('wx_users.id', '=', $value->report_id)
                    ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                    ->select('wx_users.full_name', 'wx_users.profile_image', 'wx_users.status', 'wx_login.email')
                    ->first();
                    $reports[$key]['report'] = $rd;
        }

        return view('admin.report.userList')
                    ->withReports($reports);
    }

    function activityList()
    {
        $reports = Report::where('status', '=', 1)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        foreach($reports as $key => $value)
        {
            $r = DB::table('wx_activity')
                    ->where('wx_activity.id', '=', $value->activity_id)
                    ->join('wx_users', 'wx_users.id', '=', 'wx_activity.user_id')
                    ->select('wx_activity.content', 'wx_activity.status', 'wx_users.profile_image', 'wx_users.full_name')
                    ->first();

            if($r){
                $string = $r->content;
                $remove = array("\n", "\r\n", "\r");
                $content = str_replace($remove, ' ', $string);
                $str_arr =  explode(" ",$content);
                $preview_url = '';
                $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
                
                foreach($str_arr as $value){
                    if(preg_match($pattern, $value)){
                        $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                        $link = "<a style='color: blue; font-weight: bold' target='_blank' href=".$url.">".$value."</a>";
                        $r->content = str_replace($value, $link, $r->content);
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
                            $r->content = str_replace($value, $link, $r->content);
                        }
                    }            
                }
            }

            $reports[$key]['activity'] = $r;
        }

        foreach($reports as $key => $value)
        {
            $r = DB::table('wx_users')
                    ->where('id', '=', $value->user_id)
                    ->select('profile_image', 'full_name')
                    ->first();
            $reports[$key]['user'] = $r;
        }

        return view('admin.report.activityList')
                ->withReports($reports);
    }

    function commentList()
    {
        $reports = CommentReport::where('status', '=', 1)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        foreach($reports as $key => $value)
        {
            $r = DB::table('wx_comment')
                    ->where('wx_comment.id', '=', $value->comment_id)
                    ->join('wx_users', 'wx_users.id', '=', 'wx_comment.user_id')
                    ->select('wx_comment.comment', 'wx_comment.status', 'wx_users.profile_image', 'wx_users.full_name')
                    ->first();

            if($r)
            {
                $str_arr =  explode(" ",$r->comment);
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
                            $r->comment = str_replace($value, $link, $r->comment);
                        }
                    }
                }
            }

            $reports[$key]['comment'] = $r;
        }

        foreach($reports as $key => $value)
        {
            $r = DB::table('wx_users')
                    ->where('id', '=', $value->user_id)
                    ->select('profile_image', 'full_name')
                    ->first();
            $reports[$key]['user'] = $r;
        }

        return view('admin.report.commentList')
                ->withReports($reports);
    }
}
