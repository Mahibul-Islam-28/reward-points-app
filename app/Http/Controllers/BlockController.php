<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Login;
use App\Models\Block;
use App\Models\Follow;


class BlockController extends Controller
{
    function block(Request $request){

        if($request->ajax())
        {
            $output = '';
            $blockId = $request->get('block_id');

            $session = session('user');
            $userId = $session->id;

            if($userId != $blockId)
            {

                $oldData = Block::where('block_id', '=', $blockId)
                                    ->where('user_id', '=', $userId)
                                    ->first();

                $following = Follow::where('user_id', '=', $userId)
                                        ->where('status', '=', 1)
                                        ->where('follow_id', '=', $blockId)
                                        ->first();

                $follower = Follow::where('follow_id', '=', $userId)
                                        ->where('status', '=', 1)
                                        ->where('user_id', '=', $blockId)
                                        ->first();
                
                if($oldData){
                    if($oldData->status == 1)
                    {
                        $oldData->status = 0;
                        $oldData->update();
                        $output = "Block";
                    }
                    else if($oldData->status == 0)
                    {
                        $oldData->status = 1;
                        $oldData->update();

                        $blocking = Block::where('user_id', '=', $userId)
                                        ->where('status', '=', 1)
                                        ->first();
                        if($following)
                        {
                            $following->status = 0;
                            $following->update();
                        }
                        if($follower)
                        {
                            $follower->status = 0;
                            $follower->update();
                        }

                        $output = "Unblock";
                    }
                    echo json_encode($output);
                }
                else{
                    $block = new Block;

                    $block->user_id = $userId;
                    $block->block_id = $blockId;
                    $block->save();
        
                    if($block){
                        $output = "Unblock";
                        
                        if($following)
                        {
                            $following->status = 0;
                            $following->update();
                        }
                        if($follower)
                        {
                            $follower->status = 0;
                            $follower->update();
                        }
                    }
                    else{
                        $output = "failed";
                    }
                    echo json_encode($output);
                }
            }
        }
    }

    function unblock(Request $request){

        if($request->ajax())
        {
            $output = '';
            $blockId = $request->get('block_id');

            $session = session('user');
            $userId = $session->id;

            $block = Block::where('block_id', '=', $blockId)
                                ->where('user_id', '=', $userId)
                                ->first();
            
            if($block){
                $block->status = 0;
                $block->update();

                return $block;
            }
    
        }
    }
    
}
