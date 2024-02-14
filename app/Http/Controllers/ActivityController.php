<?php

namespace App\Http\Controllers;
use Path\To\DOMDocument;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use File;
use Str;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\User;
use App\Models\Login;
use App\Models\Vote;
use App\Models\React;
use App\Models\Mention;
use App\Models\Notification;
use App\Models\Follow;
use App\Models\Block;
use App\Models\ScoreLog;
use Illuminate\Support\Facades\Http;

class ActivityController extends Controller
{
    function index()
    {
        $session = session('user');
        $userId = $session->id;
        $preview_url = '';

        $block = Block::where('user_id', '=', $userId)
                ->where('status', '=', 1)
                ->select('block_id')
                ->get();

        $blocker = Block::where('block_id', '=', $userId)
                ->where('status', '=', 1)
                ->select('user_id')
                ->get();

        $fwer = Follow::where('user_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('follow_id')
                        ->get();
                        
        $fwing = Follow::where('follow_id', '=', $userId)
                        ->where('status', '=', 1)
                        ->select('user_id')
                        ->get();
        
        $activitys = DB::table('wx_activity')
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
                    ->paginate(5);

        $allActivity = DB::table('wx_activity')
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

        return view('activity.activity')
                ->withActivitys($activitys)
                ->withAllActivity($allActivity);
    }
    
    // function wexprez()
    // {

    //     $mentionActivity = [];

    //     $activitys = DB::table('wx_activity')
    //                     ->where('wx_activity.status', '=', '1')
    //                     ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
    //                     ->orderBy('wx_activity.id', 'desc')
    //                     ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
    //                     ->get();

    //     foreach($activitys as $act)
    //     {
    //         $str_arr =  explode(" ",$act->content);
    //         foreach($str_arr as $value){
    //             if(substr($value,0,1) == "@"){
    //                 array_push($mentionActivity, $act);
    //             }

    //         }
    //     }
      
    //     return view('activity.mentionActivity')
    //             ->withActivitys($mentionActivity);
    // }

    function activityStore(Request $request)
    {
        if($request->ajax())
        {
            $session = session('user');
            $userId = $session->id;

            $data = [];

            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'emotion' => 'required',
                'images.*' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
              ]);
         

            if ($validator->passes()) {

                $content = $request->content;
                if(strlen($content) > 500)
                {
                    
                    return false;
                }
                $emotion = $request->emotion;
                $activity = new Activity;

                // if ($request->hasFile('images')) {
                //     $image = $request->file('images');
                //     foreach ($image as $files) {
                //         $destinationPath = 'images/activity/';
                //         $file_name = $userId . '_' . random_int (0,1000000) ."." . $files->getClientOriginalExtension();
                //         $files->move($destinationPath, $file_name);
                        
                //         $data[] = $file_name;
                //     }
                //     $activity->images = json_encode($data);
                // }

                if($request->hasfile('images'))
                {
                    $img = [];
                    $image = [];
                   foreach($request->file('images') as $key => $file)
                    {
                        $name = $userId. '_'.random_int (0,10000000).'.'.$file->extension();
                        // $file->move('E:\xampp\htdocs\wexprez_api\uploads\activity', $name);  
                        $file->move(base_path('wex_api\uploads\activity'), $name);  
                        $imgPath = 'images/activity/'.$name;
                        //$data = $imgPath.': [],';
                       
                        $img[$name] = [];
                    } 
                   
                   $activity->images = json_encode($img);
                }


                if(isset($request->anonymous)){

                    $anonymous = Activity::where('user_id', '=', $userId)
                                ->where('status', '=', 1)
                                ->where('anonymous', '=', 1)
                                ->whereDate('created_at', Carbon::today())
                                ->first();

                    if($anonymous)
                    {
                        return false;
                    }
                    else
                    {
                        $activity->anonymous = $request->anonymous;
                    }
                }

                $activity->user_id = $userId;
                $activity->emotion = $emotion;
                $activity->type = 'activity_update';
                $activity->content = $content;
                $activity->save();

                return true;

                // if($activity){

                //     $this->score($userId, 1, 'activity_create', $activity->id);
                //     // Creating user from Post
                //     $str_arr =  explode(" ",$content);
                //     foreach($str_arr as $value){
                //         if(substr($value,0,1) == "@"){
                //             $userName = substr($value,1);
                //             $user = User::where('user_name', '=', $userName)->first();

                //             if(!$user){

                //                 $login = new Login;
                //                 $user = new User;

                //                 $verifyToken = md5(time().rand());

                //                 $login->user_name = $userName;
                //                 $login->email = $userName."@wexprez.com";
                //                 $login->mobile_no = "0123456789";
                //                 $login->password = md5('wxp321');
                //                 $login->verify_token = $verifyToken;
                //                 $login->save();

                                
                //                 $loginId = $login->id;

                //                 $user->user_name = $userName;
                //                 $user->full_name = $userName;
                //                 $user->login_id = $loginId;
                //                 $user->save();

                //             }

                //             $mention = new Mention;
                //             $mention->mention_id = $user->id;
                //             $mention->user_id = $userId;
                //             $mention->activity_id = $activity->id;
                //             $mention->save();
                            
                //             $content = $activity->content;
                //             $replace = '@:'.$user->id.':';
                //             $content = str_replace($value, $replace, $content);
                //             $activity->content = $content;
                //             $activity->update();

                //             if($userId != $user->id)
                //             {
                //                 // Create Notification
                //                 $notify = new Notification;
                //                 $notify->sender_id = $userId;
                //                 $notify->receiver_id = $user->id;
                //                 $notify->identifier_id = $activity->id;
                //                 $notify->notify_type = 'activity_mention';
                //                 $notify->save();

                //                 // Mongo notify
                //                 $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                //                     'sender_id' => $userId,
                //                     'receiver_id' => $user->id,
                //                     'identifier_id' => $activity->id,
                //                     'notify_type' => 'activity_mention',
                //                 ]);
                //                 $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                //                     'user_id' => $user->id
                //                 ]);
                //             }

                //         }	
                //     }

                //     return true;

                // }
            }
            else{
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            
        }
    }



    function singleActivity($id)
    {
        $metaImage = '';
        $metaTitle = '';
        $metaDescription = '';

        $activity = DB::table('wx_activity')
                        ->where('wx_activity.id', '=', $id)
                        ->join('wx_users', 'wx_activity.user_id', '=', 'wx_users.id')
                        ->select('wx_activity.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                        ->first();

        if(empty($activity)){
            return redirect(route('404'));
        }

        $string = $activity->content;
        $remove = array("\n", "\r\n", "\r");
        $content = str_replace($remove, ' ', $string);
        $str_arr =  explode(" ",$content);
        $preview_url = '';
        $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';
        
        foreach($str_arr as $value){
            if(preg_match($pattern, $value)){
                $url = strpos($value, 'http') !== 0 ? "https://$value" : $value;
                if(!isset($activity->preview_url))
                {
                    $activity->preview_url = $url;
                }
                
                $link = "<a style='color: blue; font-weight: bold' target='_blank' href=".$url.">".$value."</a>";
                $activity->content = str_replace($value, $link, $activity->content);
            }

            // $regex = '/https?\:\/\/[^\" ]+/i';
            // preg_match_all($regex, $value, $matches);
            // $urls =  $matches[0];
            // print_r($matches);
            // foreach ($urls as $url){
            //     if(!empty($url)){
            //         echo $url;
            //         echo '<br>';
            //         $link = "<a style='color: blue; font-weight: bold' href=".$url.">".$url."</a>";
            //         $activity->content = str_replace($url, $link, $activity->content);
            //     }

            // }

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


        $comments = DB::table('wx_comment')
                        ->where('wx_comment.activity_id', '=', $id)
                        ->where('wx_comment.status', '=', '1')
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
                    ->where('wx_vote.identifier_id', '=', $id)
                    ->join('wx_users', 'wx_vote.user_id', '=', 'wx_users.id')
                    ->select('wx_vote.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();

        $reacts = DB::table('wx_react')
                    ->where('wx_react.status', '=', '1')
                    ->where('wx_react.identifier_id', '=', $id)
                    ->join('wx_users', 'wx_react.user_id', '=', 'wx_users.id')
                    ->select('wx_react.*', 'wx_users.profile_image', 'wx_users.full_name', 'wx_users.user_name')
                    ->get();
                
        $voteUp = count(Vote::where('identifier_id', '=', $id)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'up')
                        ->get());
        $voteDown = count(Vote::where('identifier_id', '=', $id)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'down')
                        ->get());
        $real = count(React::where('identifier_id', '=', $id)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'real')
                        ->get());
        $fake = count(React::where('identifier_id', '=', $id)
                        ->where('identifier', '=', 'activity')
                        ->where('status', '=', 1)
                        ->where('type', '=', 'fake')
                        ->get());

        $metaTitle = Str::limit($activity->full_name.': '.strip_tags($activity->content), 60);
        $metaDescription = 'Post Reactions- Vote Up: '.$voteUp.' Vote Down: '.$voteDown.' Real: '.$real.' Fake: '.$fake.'.';
        $images = [];
        $image = json_decode($activity->images);
        if (is_array($image) || is_object($image))
        {
            foreach ($image as $i => $photo){
                if($images == ""){
                    $images = $i;
                } else {
                    array_push($images , $i);
                }
            }
        }

        if($images == null || $images == '[]')
        {
            $metaImage = 'http://localhost:8000/images/fb-meta.png';
        }
        else{
            $metaImage = "http://localhost/wexprez_api/uploads/activity/".$images[0];
        }


        return view('activity.singleActivity')
                ->withActivity($activity)
                ->withMyComments($comments)
                ->withVotes($votes)
                ->withReacts($reacts)
                ->withMetaTitle($metaTitle)
                ->withMetaDescription($metaDescription)
                ->withMetaImage($metaImage);
    }

    function edit(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $output = array();
                $image = array();
                $id = $request->get('id');
    
                $activity = Activity::find($id);
                $str_arr =  explode(" ",$activity->content);
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
                            $activity->content = str_replace($value, $link, $activity->content);
                        }
                    }
                }

                if($activity->images)
                {
                    $img = json_decode($activity->images);
                    $images = [];

                    foreach ($img as $key=>$photo){
                        if($img == ""){
                            array_push($images, $key);
                        } else {
                            array_push($images, $key);
                        }
                    }

                    foreach($images as $index => $item){
                        //$i = '<img id="deleteImage-'.$index.'" src="http://localhost/wexprez_api/uploads/activity/'.$item.'"/>';
                        array_push($image, $item);
                    }
    
                    $output['images'] = $image;
                }

                if($activity){
                   
                $output['content'] = '<form method="post" id="edit-activity-'.$activity->id.'">
                            <textarea cols="200" name="editContent" maxlength="500">'.strip_tags($activity->content).'</textarea><input type="hidden" name="activityId" id="activityId" value="'.$activity->id.'">
                            <input type="file" name="addImage[]" id="addImage" multiple="multiple">';
                            echo json_encode($output);
                }
                    
            }
        } 
    }

    function update(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $validator = Validator::make($request->all(), [
                    'addImage.*' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
                  ]);
             
                if ($validator->passes()) {
    
                    $content = $request->editContent;

                    if(strlen($content) > 500)
                    {
                        return false;
                    }
                    $id = $request->activityId;
                    $output = '';
    
        
                    $activity = Activity::find($id);

                    if($activity)
                    {
                        $images = [];
                        $img = json_decode($activity->images);
                        if($img == null)
                        {
                            $img = [];
                        }

                        if($request->hasfile('addImage'))
                        {
                            foreach($request->file('addImage') as $key => $file)
                            {
                                $name = $activity->user_id. '_'.random_int (0,10000000).'.'.$file->extension();
                                $file->move('/home/wxpadmin/public_html/wex_api/uploads/activity/', $name);  
                                if($img != null)
                                {
                                    $img->$name = [];
                                   $images =  $img;
                                }
                                else
                                {
                                    $images = json_encode($img);
                                    $img = "{".$images.": []}";
                                    $images =  $img;
                                }
                            }
                            $activity->images = json_encode($images);
                        }

                        $activity->content = $content;
                        $activity->update();

                        $output = $activity->content;

                        // Creating user from Post
                        $str_arr =  explode(" ",$content);
                        foreach($str_arr as $value){
                            if(substr($value,0,1) == "@"){
                                $userName = substr($value,1);
                                $user = User::where('user_name', '=', $userName)->first();

                                if(!$user){

                                    $login = new Login;
                                    $user = new User;
    
                                    $verifyToken = md5(time().rand());
    
                                    $login->user_name = $userName;
                                    $login->email = $userName."@wexprez.com";
                                    $login->mobile_no = "0123456789";
                                    $login->password = md5('wxp321');
                                    $login->verify_token = $verifyToken;
                                    $login->save();
    
                                    
                                    $loginId = $login->id;
    
                                    $user->user_name = $userName;
                                    $user->full_name = $userName;
                                    $user->login_id = $loginId;
                                    $user->save();

                                    // Create mention
                                    $mention = new Mention;
                                    $mention->mention_id = $user->id;
                                    $mention->user_id = $activity->user_id;
                                    $mention->activity_id = $id;
                                    $mention->save();

                                    $content = $activity->content;
                                    $replace = '@:'.$user->id.':';
                                    $content = str_replace($value, $replace, $content);
                                    $activity->content = $content;
                                    $activity->update();

                                    if($activity->user_id != $user->id)
                                    {
                                        // Create Notification
                                        $notify = new Notification;
                                        $notify->sender_id = $activity->user_id;
                                        $notify->receiver_id = $user->id;
                                        $notify->identifier_id = $id;
                                        $notify->notify_type = 'activity_mention';
                                        $notify->save();

                                        // Mongo notify
                                        $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                            'sender_id' => $activity->user_id,
                                            'receiver_id' => $user->id,
                                            'identifier_id' => $id,
                                            'notify_type' => 'activity_mention',
                                        ]);
                                        $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                            'user_id' => $user->id
                                        ]);
                                    }

                                }

                                else{

                                    $content = $activity->content;
                                    $replace = '@:'.$user->id.':';
                                    $content = str_replace($value, $replace, $content);
                                    $activity->content = $content;
                                    $activity->update();

                                    $oldMention = Mention::where('user_id', '=', $activity->user_id)
                                                ->where('mention_id', '=', $user->id)
                                                ->where('activity_id', '=', $id)
                                                ->first();
            
                                    if(!$oldMention)
                                    {
                                        $mention = new Mention;
                                        $mention->mention_id = $user->id;
                                        $mention->user_id = $activity->user_id;
                                        $mention->activity_id = $id;
                                        $mention->save();

                                        if($activity->user_id != $user->id)
                                        {
                                            // Create Notification
                                            $notify = new Notification;
                                            $notify->sender_id = $activity->user_id;
                                            $notify->receiver_id = $user->id;
                                            $notify->identifier_id = $id;
                                            $notify->notify_type = 'activity_mention';
                                            $notify->save();
                                            
                                            // Mongo notify
                                            $result = Http::post('http://localhost/wexprez_api/api/notification/notification_create', [
                                                'sender_id' => $activity->user_id,
                                                'receiver_id' => $user->id,
                                                'identifier_id' => $id,
                                                'notify_type' => 'activity_mention',
                                            ]);
                                            $result = Http::post('http://localhost/wexprez_api/api/notification/update_counter', [
                                                'user_id' => $user->id
                                            ]);
                                        }


                                    }
                                }
                            }	
                        }

                        return true;

                    }
    
                }      
            }
        } 
    }
    
    // function update(Request $request){

    //     if(session('user')){
    //         if($request->ajax())
    //         {
    //             $output = '';
    //             $id = $request->post('id');
    //             $content = $request->post('content');
    
    //             $activity = Activity::find($id);

    //             if($activity)
    //             {
    //                 $activity->content = $content;
    //                 $activity->update();

    //                 $output = $activity->content;

    //                                 // Creating user from Post
    //                 $str_arr =  explode(" ",$content);
    //                 foreach($str_arr as $value){
    //                     if(substr($value,0,1) == "@"){
    //                         $userName = substr($value,1);
    //                         $user = User::where('user_name', '=', $userName)->first();

    //                         if(!$user){

    //                             $login = new Login;
    //                             $user = new User;

    //                             $verifyToken = md5(time().rand());

    //                             $login->user_name = $userName;
    //                             $login->email = $userName."@wexprez.com";
    //                             $login->mobile_no = "0123456789";
    //                             $login->password = md5('wxp321');
    //                             $login->status = 0;
    //                             $login->verify_token = $verifyToken;
    //                             $login->save();

                                
    //                             $loginId = $login->id;

    //                             $user->user_name = $userName;
    //                             $user->full_name = $userName;
    //                             $user->login_id = $loginId;
    //                             $user->save();

    //                             // Create mention
    //                             $mention = new Mention;
    //                             $mention->mention_id = $user->id;
    //                             $mention->user_id = $activity->user_id;
    //                             $mention->activity_id = $id;
    //                             $mention->save();

    //                             $content = $activity->content;
    //                             $route = route('profileWexprez', $userName);
    //                             $link = "<a style='color: blue; font-weight: bold' href='".$route."'>$userName</a>";
    //                             $mention = str_replace($userName, $link, $content);
    //                             $activity->content = $mention;
    //                             $activity->update();
        
    //                             // Create Notification
    //                             $notify = new Notification;
    //                             $notify->sender_id = $activity->user_id;
    //                             $notify->receiver_id = $user->id;
    //                             $notify->identifier_id = $id;
    //                             $notify->notify_type = 'activity_mention';
    //                             $notify->save();

    //                         }

    //                         else{

    //                             $oldNotify = Notification::where('sender_id', '=', $activity->user_id)
    //                                         ->where('receiver_id', '=', $user->id)
    //                                         ->where('identifier_id', '=', $id)
    //                                         ->where('notify_type', '=', 'activity_mention')
    //                                         ->first();
        
    //                             if(!$oldNotify)
    //                             {
    //                                 $mention = new Mention;
    //                                 $mention->mention_id = $user->id;
    //                                 $mention->user_id = $activity->user_id;
    //                                 $mention->activity_id = $id;
    //                                 $mention->save();

    //                                 $content = $activity->content;
    //                                 $route = route('profileWexprez', $userName);
    //                                 $link = "<a style='color: blue; font-weight: bold' href='".$route."'>$userName</a>";
    //                                 $mention = str_replace($userName, $link, $content);
    //                                 $activity->content = $mention;
    //                                 $activity->update();

    //                                 // Create Notification
    //                                 $notify = new Notification;
    //                                 $notify->sender_id = $activity->user_id;
    //                                 $notify->receiver_id = $user->id;
    //                                 $notify->identifier_id = $id;
    //                                 $notify->notify_type = 'activity_mention';
    //                                 $notify->save();

    //                             }
    //                         }
    //                     }	
    //                 }

    //                 echo json_encode($output);

    //             }
    
                    
    //         }
    //     } 
    // }

    
    function delete(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $activityId = $request->get('activity_id');

    
                $activity = Activity::find($activityId);

                if($activity)
                {
                    $activity->status = 0;
                    $activity->update();

                    $this->score($activity->user_id, -1, 'activity_delete', $activityId);

                    return $activity;
                }
        
            }
        } 
    }

    public function imageDelete(Request $request) {

        if($request->ajax())
        {
            $id = $request->get('id');
            $deleteImgNames = $request->get('image');
            
            $activity = Activity::find($id);
            if(!empty($activity)){
                $gallery = json_decode($activity->images, true);

	
                $deleteImgArr = explode( ",", $deleteImgNames );

                $imageArr = [];

                if (!empty($activity->images))
                {
                    $image =  json_decode($activity->images); 
                    
                    foreach ($image as $key=>$photo)
                    {
                        array_push($imageArr, $key);
                    }
                }

                $photosArr = array_diff( $imageArr, $deleteImgArr );

                $img = [];
                foreach ($photosArr as $key => $value)
                {
                    $img[$value] = [];
                }

                $activity->images = json_encode($img);
                $activity->update();
                foreach ($deleteImgArr as $imgName) {
                    if (file_exists("G:\\xampp\\htdocs\\wexprez_api\\uploads\\activity\\".$imgName)) {
                        unlink("G:\\xampp\\htdocs\\wexprez_api\\uploads\\activity\\".$imgName);
                    }
                }

                return true;

            }

            return true;
        }
    }

    // Share
    function activityShare(Request $request)
    {
        if(session('user')){
            if($request->ajax())
            {
                $activityId = $request->get('activity_id');

                $session = session('user');
                $userId = $session->id;

                $activity = Activity::find($activityId);

                if($activity)
                {
                    $newActivity = new Activity;

                    $newActivity->images = $activity->images;
                    $newActivity->user_id = $userId;
                    $newActivity->emotion = $activity->emotion;
                    $newActivity->type = 'activity_update';
                    $newActivity->content = $activity->content;
                    $newActivity->save();
                }

                return true;

            }
        }
    }

    // Hide
    function hide(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $activityId = $request->get('activity_id');

                $activity = Activity::find($activityId);

                if($activity)
                {
                    $activity->status = 2;
                    $activity->update();

                    if($activity)
                    {
                        // hide notify
                        $notify = Notification::where('identifier_id', '=', $activityId)
                                                ->where('status', '=', 0)
                                                ->update([
                                                    'status' => 1
                                                ]);
                        return true;
                    }  
                }
        
            }
        } 
    }
    function show(Request $request){

        if(session('user')){
            if($request->ajax())
            {
                $activityId = $request->get('activity_id');

                $activity = Activity::find($activityId);

                if($activity)
                {
                    $activity->status = 1;
                    $activity->update();

                    if($activity)
                    {
                        return true;
                    }  
                }
        
            }
        } 
    }


    function mention(Request $request)
    {
        if($request->ajax())
        {
            $content = $request->get('content');
            $link = "";
            $userList = [];

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


            $str_arr =  explode(" ",$content);

            foreach($str_arr as $value){
                
                if(substr($value,0,1) == "@"){

                    $userName = substr($value,1);
                    $position = strpos($content, ' ');
                    
                    $user = DB::table('wx_users')
                                ->where('id', '!=', $userId)
                                ->where('user_name', 'like', '%'.$userName.'%')
                                ->orWhere('full_name', 'like', '%'.$userName.'%')
                                ->whereNotIn('id', $block)
                                ->whereNotIn('id', $blocker)
                                ->inRandomOrder()->limit(5)->get();

                    if($user){
                        $userList = $user;
                        foreach ($userList as $key => $value){
                            $route = route('profile', $userList[$key]->user_name);
                            $userList[$key]->link = "$route";
                        }
                    }
                    else{
                        $userList = User::whereNotIn('id', $block)
                                    ->whereNotIn('id', $blocker)->inRandomOrder()->limit(5)->get();
                        foreach ($userList as $key => $value){
                            $route = route('profile', $userList[$key]->user_name);
                            $userList[$key]->link = "$route";
                        }
                        
                    }
                    
                }
                	
            }
            return $userList;
        }
    }
    function addMention(Request $request)
    {
        if($request->ajax())
        {
            $id = $request->get('id');
            
            $user = User::find($id);

            // $route = route('profile', $user->user_name);
            // $link = "<a href='".$route."'>'".$user->user_name."'</a>";

            return $user;
        }
    }

    // Activity Filter
    function filter(Request $request)
    {
        $filter = $request->get('emotion');
        session()->put('emotion', $filter);
        return true;
    }

    function dateFilter(Request $request)
    {
        $date = $request->get('date');
        if($date)
        {
            session()->put('dateFilter', $date);
        }
        else{
            session()->put('dateFilter', "All");
        }
        
        return true;
    }

    function link(Request $request)
    {
        if($request->ajax())
        {
            $output = [];
            
            $url = $request->get('url');
            $url = strpos($url, 'http') !== 0 ? "https://$url" : $url;
            // $regex = '/https?\:\/\/[^\" ]+/i';
            // preg_match_all($regex, $string, $matches);
            // $urls =  $matches[0];
            // $url = $urls;

            if($url)
            {
                $specificTags = 0;
                $doc = new \DOMDocument();
                @$doc->loadHTML(file_get_contents($url));
                $res['title'] = $doc->getElementsByTagName('title')->item(0)->nodeValue;
                
                foreach ($doc->getElementsByTagName('meta') as $m){
                    $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
                    if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
                }
                return $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
    
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $og_details = getSiteOG($url);
                } else {
                    echo("Not a valid URL");
                }
    
                // $output = '<div class="debug-box" onclick="openLink(this);" data-link="'. $url .'">
                //     <img src="'.$og_details['image'].'" width="100%">
                //     <div class="text-wrapper">
                //         <p><strong>'.$og_details['title'].'</strong></p>
                //         <p class="description">'.$og_details['description'].'</p>
                //     </div>
                // </div>';
    
                $output['image'] = $og_details['image'];
                $output['title'] = $$og_details['title'];
                $output['description'] = $og_details['description'];
                $output['url'] = $url;
    
                echo json_encode($output);
            }

            else{
                return false;
            }
        }
    }


}
