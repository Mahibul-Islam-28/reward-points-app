<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\MemberBan;
use Carbon\Carbon;

class UserManageController extends Controller
{
    function index()
    {
        $users = DB::table('wx_users')
                ->join('wx_login', 'wx_users.login_id', '=', 'wx_login.id')
                ->select('wx_users.*', 'wx_login.mobile_no', 'wx_login.email', 'wx_login.otp_verify', 'wx_login.email_verify')
                ->orderBy('id', 'desc')
                ->get();

        return view('admin.userManage')
                ->withUsers($users);
    }

    function temporaryBan(Request $request)
    {
        $id = $request->get('id');

        $user = User::find($id);
        
        if($user){
            $user->status = 2;
            $user->update();

            $ban = new MemberBan;
            $ban->user_id = $id;
            $ban->type = 2;
            $ban->status = 0;
            $ban->save();
        }
        
        return $user;

    }

    function permanentBan(Request $request)
    {
        $id = $request->get('id');

        $user = User::find($id);

        if($user){
            $user->status = 3;
            $user->update();

            $ban = new MemberBan;
            $ban->user_id = $id;
            $ban->type = 3;
            $ban->status = 0;
            $ban->save();
        }

        return $user;
    }

    function unban(Request $request)
    {
        $id = $request->get('id');

        $user = User::find($id);

        if($user){
            $user->status = 1;
            $user->update();

            // $ban = MemberBan::where('user_id', '=', $id);
            // if($ban)
            // {
            //     $ban->status = 1;
            //     $ban->update();
            // }
        }

        return $user;
    }

    function unbanUser()
    {
        $result = [];
        $ban = MemberBan::where('status', '=', 0)
                        ->where('type', '=', 2)
                        ->get();
        
        if($ban)
        {
            foreach($ban as $key => $value){
                $date1 = Carbon::now();
                $date2 = $value->ban_date;
                $interval = $date1->diff($date2);
                $diffInDays = $interval->d;
                if($diffInDays >= 7){
                    $ban[$key]->status = 1;
                    $ban[$key]->update();

                    $user = User::find($value->user_id);
                    $user->status = 1;
                    $user->update();

                    echo 'user id -> ' . $ban[$key]->user_id . '   updated_at -> ' .$ban[$key]->unban_date. ' <br>';
                }
            }
        }
    }

    // Activity
    function viewActivity(Request $request)
    {
        $id = $request->get('id');

        $activity = Activity::find($id);

        if($activity)
        {
            $activity->status = 1;
            $activity->update();
        }

        return $activity;
    }
    function hideActivity(Request $request)
    {
        $id = $request->get('id');

        $activity = Activity::find($id);

        if($activity)
        {
            $activity->status = 0;
            $activity->update();
        }

        return $activity;
    }

    // Comment
    function viewComment(Request $request)
    {
        $id = $request->get('id');

        $comment = Comment::find($id);

        if($comment)
        {
            $comment->status = 1;
            $comment->update();
        }

        return $comment;
    }
    function hideComment(Request $request)
    {
        $id = $request->get('id');

        $comment = Comment::find($id);

        if($comment)
        {
            $comment->status = 0;
            $comment->update();
        }

        return $comment;
    }

    // Deleted user
    function deletedUser()
    {
        $users = DB::table('wx_deleted_users')
                ->join('wx_deleted_login', 'wx_deleted_users.login_id', '=', 'wx_deleted_login.id')
                ->select('wx_deleted_users.*', 'wx_deleted_login.mobile_no', 'wx_deleted_login.email', 'wx_deleted_login.otp_verify', 'wx_deleted_login.email_verify')
                ->orderBy('id', 'desc')
                ->get();

        return view('admin.deletedUser')
                ->withUsers($users);
    }
    
}
