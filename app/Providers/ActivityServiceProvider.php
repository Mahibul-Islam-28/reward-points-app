<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Vote;
use App\Models\React;
use App\Models\Block;

class ActivityServiceProvider extends ServiceProvider
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

                $userId = $session->id;

                $anonymous = Activity::where('user_id', '=', $userId)
                                        ->where('status', '=', 1)
                                        ->where('anonymous', '=', 1)
                                        ->whereDate('created_at', Carbon::today())
                                        ->first();

                
                $block = Block::where('user_id', '=', $userId)
                                ->where('status', '=', 1)
                                ->select('block_id')
                                ->get();
                $blocker = Block::where('block_id', '=', $userId)
                                ->where('status', '=', 1)
                                ->select('user_id')
                                ->get();

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

                $memberVotes = DB::table('wx_member_vote')
                                ->where('wx_member_vote.status', '=', '1')
                                ->whereNotIn('wx_member_vote.user_id', $block)
                                ->whereNotIn('wx_member_vote.user_id', $blocker)
                                ->join('wx_users', 'wx_member_vote.user_id', '=', 'wx_users.id')
                                ->select('wx_member_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                                ->get();

                // $votes = Vote::where('status', '=', 1)->get();
                // $reacts = React::where('status', '=', 1)->get();

                return $view
                    ->withComments($comments)
                    ->withVotes($votes)
                    ->withReacts($reacts)
                    ->withMemberVotes($memberVotes)
                    ->withAnonymous($anonymous);
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
