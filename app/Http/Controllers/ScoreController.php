<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ScoreLog;
use App\Models\User;

class ScoreController extends Controller
{
    function details(Request $request)
    {
        if(session('user')){
            if($request->ajax())
            {
                $id = $request->get('id');
                $details = array();
                $score = 0;
                $follow = 0;
                $activity = 0;
                $comment = 0;
                $registration = 0;
                $mVote = 0;
                $aVote = 0;
                $aReact = 0;
                $cVote = 0;
                $cReact = 0;

                $log = ScoreLog::where('user_id', '=', $id)->get();

                if($log)
                {
                    foreach($log as $key => $value)
                    {
                        if($value->identifier == 'registration')
                        {
                            $registration += $value->score;
                        }
                        else if($value->identifier == 'follow' || $value->identifier == 'unfollow')
                        {
                            $follow += $value->score;
                        }
                        else if($value->identifier == 'activity_create' || $value->identifier == 'activity_delete')
                        {
                            $activity += $value->score;
                        }
                        else if($value->identifier == 'comment_create' || $value->identifier == 'comment_delete')
                        {
                            $comment += $value->score;
                        }
                        else if($value->identifier == 'member_voteUp' || $value->identifier == 'member_voteDown' || $value->identifier == 'member_voteUp_off' || $value->identifier == 'member_voteDown_off')
                        {
                            $mVote += $value->score;
                        }
                        else if($value->identifier == 'activity_voteUp' || $value->identifier == 'activity_voteDown' || $value->identifier == 'activity_voteUp_off' || $value->identifier == 'activity_voteDown_off')
                        {
                            $aVote += $value->score;
                        }
                        else if($value->identifier == 'activity_real' || $value->identifier == 'activity_fake' || $value->identifier == 'activity_real_off' || $value->identifier == 'activity_fake_off')
                        {
                            $aReact += $value->score;
                        }
                        else if($value->identifier == 'comment_voteUp' || $value->identifier == 'comment_voteDown' || $value->identifier == 'comment_voteUp_off' || $value->identifier == 'comment_voteDown_off')
                        {
                            $cVote += $value->score;
                        }
                        else if($value->identifier == 'comment_real' || $value->identifier == 'comment_fake' || $value->identifier == 'comment_real_off' || $value->identifier == 'comment_fake_off')
                        {
                            $cReact += $value->score;
                        }
            
                    }
                }

                $user = User::where('id', '=', $id)->first();
                if($user)
                {
                    $score = $user->score;
                }

                
                $details['score'] = $score;
                $details['registration'] = $registration;
                $details['member_vote'] = $mVote;
                $details['follow'] = $follow;
                $details['activity'] = $activity;
                $details['comment'] = $comment;
                $details['activity_vote'] = $aVote;
                $details['activity_react'] = $aReact;
                $details['comment_vote'] = $cVote;
                $details['comment_react'] = $cReact;
        
                return $details;

            }
        }
    }
}
