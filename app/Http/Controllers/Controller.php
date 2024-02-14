<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use App\Models\User;
use App\Models\ScoreLog;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function score($userId, $score, $identifier, $identifierId)
    {
        $log = new ScoreLog;
        $log->user_id = $userId;
        $log->score = $score;
        $log->identifier = $identifier;
        $log->identifier_id = $identifierId;
        $log->save();

        $user = User::find($userId);

        $user->score = $user->score + $score;
        $user->save();
    }
}
