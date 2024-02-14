<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Follow;
use App\Models\MemberVote;
use App\Models\Notification;
use App\Models\Block;
use Illuminate\Support\Facades\Http;

class MemberController extends Controller
{
    function index()
    {
        
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
                
        $members = User::where('status', '=', '1')
                        ->whereNotIn('id', $blocking)
                        ->whereNotIn('id', $blocker)
                        ->get();

        $follow = Follow::where('user_id', '=', $userId)->get();

        return view('member.members')
                ->withMembers($members)
                ->withFollow($follow);
    }
    function followers()
    {
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

        $followers = DB::table('wx_follow')
                        ->where('wx_follow.status', '=', '1')
                        ->where('wx_users.status', '=', '1')
                        ->where('wx_follow.follow_id', '=', $userId)
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_follow.user_id', '=', 'wx_users.id')
                        ->select('wx_follow.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name', 'wx_users.score')
                        ->get();

        return view('member.followers')
                ->withFollowers($followers);
    }
    function following()
    {
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

        $followings = DB::table('wx_follow')
                        ->where('wx_follow.status', '=', '1')
                        ->where('wx_users.status', '=', '1')
                        ->where('wx_follow.user_id', '=', $userId)
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_follow.follow_id', '=', 'wx_users.id')
                        ->select('wx_follow.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name', 'wx_users.score')
                        ->get();

        return view('member.following')
                ->withFollowings($followings);
    }

    function block()
    {
        $session = session('user');
        $userId = $session->id;

        $block = DB::table('wx_block')
                    ->where('wx_block.user_id', '=', $userId)
                    ->where('wx_block.status', '=', 1)
                    ->orderBy('wx_block.id', 'desc')
                    ->join('wx_users', 'wx_block.block_id', '=', 'wx_users.id')
                    ->select('wx_block.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();

        return view('member.block')
                ->withBlocks($block);
    }


    function voteUp(Request $request)
    {

        if(session('user')){
            if($request->ajax())
            {
                $voteId = $request->get('vote_id');
                $userId = $request->get('user_id');


                $oldData = MemberVote::where('vote_id', '=', $voteId)
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'down'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'member_voteUp', $voteId);
                    }
                    $oldData->type = 'up';
                    $oldData->status = 1;
                    $img = 'images/reaction/voteUp-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'member_voteUp_off', $voteId);
                    $img = 'images/reaction/voteUp-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'member_voteUp', $voteId);
                    $img = 'images/reaction/voteUp-on.png';
                }
                $oldData->update();
            }
            else{

                $vote = new MemberVote;
                $vote->vote_id = $voteId;
                $vote->user_id = $userId;
                $vote->type = 'up';
                $vote->save();

                $this->score($userId, 1, 'member_voteUp', $voteId);
                $img = 'images/reaction/voteUp-on.png';

                if($voteId != $userId)
                {
                    // Create Notification
                    $notify = new Notification;
                    $notify->sender_id = $userId;
                    $notify->receiver_id = $voteId;
                    $notify->notify_type = 'member_voteUp';
                    $notify->save();

                    // Mongo notify
                    $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                        'sender_id' => $userId,
                        'receiver_id' => $voteId,
                        'notify_type' => 'member_voteUp',
                    ]);
                    $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                        'user_id' => $voteId
                    ]);
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


            $up = MemberVote::where('vote_id', '=', $voteId)
                            ->where('status', '=', 1)
                            ->where('type', '=', 'up')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();
                            
            $down = MemberVote::where('vote_id', '=', $voteId)
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
    }
    function voteDown(Request $request)
    {

        if(session('user')){
            if($request->ajax())
            {
                $voteId = $request->get('vote_id');
                $userId = $request->get('user_id');


                $oldData = MemberVote::where('vote_id', '=', $voteId)
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($oldData){
                if($oldData->type == 'up'){
                    if($oldData->status == 0)
                    {
                        $this->score($userId, 1, 'member_voteDown', $voteId);
                    }
                    $oldData->type = 'down';
                    $oldData->status = 1;
                    $img = 'images/reaction/voteDown-on.png';
                }
                else if($oldData->status == 1){
                    $oldData->status = 0;
                    $this->score($userId, -1, 'member_voteDown_off', $voteId);
                    $img = 'images/reaction/voteDown-off.png';
                }

                else{
                    $oldData->status = 1;
                    $this->score($userId, 1, 'member_voteDown', $voteId);
                    $img = 'images/reaction/voteDown-on.png';
                }
                $oldData->update();
            }
            else{

                $vote = new MemberVote;
                $vote->vote_id = $voteId;
                $vote->user_id = $userId;
                $vote->type = 'up';
                $vote->save();

                $this->score($userId, 1, 'member_voteDown', $voteId);
                $img = 'images/reaction/voteDown-on.png';

                if($voteId != $userId)
                {
                    // Create Notification
                    $notify = new Notification;
                    $notify->sender_id = $userId;
                    $notify->receiver_id = $voteId;
                    $notify->notify_type = 'member_voteDown';
                    $notify->save();

                    $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                        'sender_id' => $userId,
                        'receiver_id' => $voteId,
                        'notify_type' => 'member_voteDown',
                    ]);
                    $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                        'user_id' => $voteId
                    ]);
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

            $up = MemberVote::where('vote_id', '=', $voteId)
                            ->where('status', '=', 1)
                            ->where('type', '=', 'up')
                            ->whereNotIn('user_id', $blocking)
                            ->whereNotIn('user_id', $blocker)
                            ->get();
            $down = MemberVote::where('vote_id', '=', $voteId)
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
    }

    function voteUpList(Request $request)
    {
        if($request->ajax())
        {
            $userId = $request->get('user_id');
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

            $votes = DB::table('wx_member_vote')
                        ->where('wx_member_vote.status', '=', '1')
                        ->where('wx_member_vote.vote_id', '=', $userId)
                        ->where('wx_member_vote.type', '=', 'up')
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_member_vote.user_id', '=', 'wx_users.id')
                        ->select('wx_member_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
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
            $userId = $request->get('user_id');
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

            $votes = DB::table('wx_member_vote')
                            ->where('wx_member_vote.status', '=', '1')
                            ->where('wx_member_vote.vote_id', '=', $userId)
                            ->where('wx_member_vote.type', '=', 'down')
                            ->whereNotIn('wx_users.id', $blocking)
                            ->whereNotIn('wx_users.id', $blocker)
                            ->join('wx_users', 'wx_member_vote.user_id', '=', 'wx_users.id')
                            ->select('wx_member_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                            ->get();
            if($votes)
            {
                return $votes;
            }
        }
    }

    //search
    function search(Request $request)
    {
        if($request->ajax())
        {
            $value = $request->get('value');

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

            $members = DB::table('wx_users')
                        ->where('user_name', 'like', '%'.$value.'%')
                        ->where('status', '=', 1)
                        ->orWhere('full_name', 'like', '%'.$value.'%')
                        ->whereNotIn('id', $blocking)
                        ->whereNotIn('id', $blocker)
                        ->get();
            if($members)
            {

                $result = [];

                foreach($members as $key => $value)
                {
                    $fw = 'Follow';

                    $follow = Follow::where('user_id', '=', $sessionId)
                                        ->where('follow_id', '=', $value->id)
                                        ->where('status', '=', 1)->first();
                    if($follow)
                    {
                        $fw = 'Unfollow';
                    }

                    $route = route('profileWexprez', $value->user_name);
                    $r = '<div class="col-md-3 col-12"><div class="card member-card"><div class="card-head p-2">
                    <img class="rounded-circle" src="'.$value->profile_image.'"></div>
                    <div class="card-body"><a href="'.$route.'"><h3>'.$value->full_name.'</h3></a></div>
                    <div class="d-grid gap-2 mb-3 mx-3">
                    <button class="btn btn-dark mx-3" value="'.$value->id.'" onclick="follow(this);"
                    id="follow-'.$value->id.'"
                    data-id="'.$value->id.'">'.$fw.'</button>
                    <button class="btn btn-dark my-2 mx-3" value="'.$value->id.'" onclick="block(this);"
                    id="block-'.$value->id.'"
                    data-id="'.$value->id.'">Block</button>
                    </div></div></div>';

                    $result[$key] = $r;
                }
                return json_encode($result);
            }
        }
    }
    function followerSearch(Request $request)
    {
        if($request->ajax())
        {
            $value = $request->get('value');

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

            $followers = DB::table('wx_follow')
                        ->where('wx_follow.status', '=', '1')
                        ->where('wx_users.full_name', 'like', '%'.$value.'%')
                        ->where('wx_users.status', '=', '1')
                        ->where('wx_follow.follow_id', '=', $sessionId)
                        ->whereNotIn('wx_users.id', $blocking)
                        ->whereNotIn('wx_users.id', $blocker)
                        ->join('wx_users', 'wx_follow.user_id', '=', 'wx_users.id')
                        ->select('wx_follow.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();

            if($followers)
            {

                $result = [];

                foreach($followers as $key => $value)
                {

                    $route = route('profileWexprez', $value->user_name);
                    $r = '<div class="col-md-3 col-12"><div class="card member-card"><div class="card-head p-2">
                    <img class="rounded-circle" src="'.$value->profile_image.'"></div>
                    <div class="card-body"><a href="'.$route.'"><h3>'.$value->full_name.'</h3></a></div>
                    <div class="d-grid gap-2 mb-3 mx-3">
                    <button class="btn btn-dark mx-3" value="'.$value->user_id.'" onclick="follow(this);"
                    id="follow-'.$value->id.'"
                    data-id="'.$value->id.'">Unfollow</button>
                    <button class="btn btn-dark my-2 mx-3" value="'.$value->user_id.'" onclick="block(this);"
                    id="block-'.$value->user_id.'"
                    data-id="'.$value->user_id.'">Block</button>
                    </div></div></div>';

                    $result[$key] = $r;
                }
                return json_encode($result);
            }
        }
    }

    function followingSearch(Request $request)
    {
        if($request->ajax())
        {
            $value = $request->get('value');

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

            $following = DB::table('wx_follow')
                            ->where('wx_users.full_name', 'like', '%'.$value.'%')
                            ->where('wx_follow.status', '=', '1')
                            ->where('wx_users.status', '=', '1')
                            ->where('wx_follow.user_id', '=', $sessionId)
                            ->whereNotIn('wx_users.id', $blocking)
                            ->whereNotIn('wx_users.id', $blocker)
                            ->join('wx_users', 'wx_follow.follow_id', '=', 'wx_users.id')
                            ->select('wx_follow.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                            ->get();
            if($following)
            {

                $result = [];

                foreach($following as $key => $value)
                {
                    $fw = 'Follow';

                    $follow = Follow::where('user_id', '=', $sessionId)
                                        ->where('follow_id', '=', $value->id)
                                        ->where('status', '=', 1)->first();
                    if($follow)
                    {
                        $fw = 'Unfollow';
                    }

                    $route = route('profileWexprez', $value->user_name);
                    $r = '<div class="col-md-3 col-12"><div class="card member-card"><div class="card-head p-2">
                    <img class="rounded-circle" src="'.$value->profile_image.'"></div>
                    <div class="card-body"><a href="'.$route.'"><h3>'.$value->full_name.'</h3></a></div>
                    <div class="d-grid gap-2 mb-3 mx-3">
                    <button class="btn btn-dark" value="'.$value->follow_id.'" onclick="follow(this);"
                    id="follow-'.$value->follow_id.'"
                    data-id="'.$value->follow_id.'">'.$fw.'</button>
                    <button class="btn btn-dark" value="'.$value->follow_id.'" onclick="block(this);"
                    id="block-'.$value->follow_id.'"
                    data-id="'.$value->follow_id.'">Block</button>
                    </div></div></div>';

                    $result[$key] = $r;
                }
                return json_encode($result);
            }
        }
    }
    function blockSearch(Request $request)
    {
        if($request->ajax())
        {
            $value = $request->get('value');

            $session = session('user');
            $sessionId = $session->id;

            $block = DB::table('wx_block')
                        ->where('wx_users.full_name', 'like', '%'.$value.'%')
                        ->where('wx_block.user_id', '=', $sessionId)
                        ->where('wx_block.status', '=', 1)
                        ->orderBy('wx_block.id', 'desc')
                        ->join('wx_users', 'wx_block.block_id', '=', 'wx_users.id')
                        ->select('wx_block.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->get();

            if($block)
            {

                $result = [];

                foreach($block as $key => $value)
                {

                    $route = route('profileWexprez', $value->user_name);
                    $r = '<div class="col-md-3 col-12"><div class="card member-card"><div class="card-head p-2">
                    <img class="rounded-circle" src="'.$value->profile_image.'"></div>
                    <div class="card-body"><a href="'.$route.'"><h3>'.$value->full_name.'</h3></a></div>
                    <div class="d-grid gap-2 mb-3 mx-3">
                    <button class="btn btn-dark" value="'.$value->block_id.'" onclick="unblock(this);"
                    id="block-'.$value->user_id.'"
                    data-id="'.$value->user_id.'">Unblock</button>
                    </div></div></div>';

                    $result[$key] = $r;
                }
                return json_encode($result);
            }
        }
    }

}
