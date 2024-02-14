@extends('layout.main')

@section('title')
WeXprez - Xprez it
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/index.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@php 
if(session()->get('page') != 'index' ){
    session()->put('page', 'index');
    session()->put('emotion', 'All');
    session()->put('dateFilter', 'All');
}
@endphp

@section('content')
<section class="index-section container">

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible d-none d-md-block w-50 mx-auto mt-3">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible d-none d-md-block w-50 mx-auto mt3">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif

    <!-- Activity Form -->
    <div class="activity-form">
        <form method="post" id="activity-form" enctype="multipart/form-data">
            @csrf
            <div class="input-area">
                <img loading="lazy" src="http://localhost/wexprez_api/uploads/user_profile/{{$profile->profile_image}}"
                    alt="{{$profile->full_name}}">
                <textarea name="content" id="activityContent" rows="5" required onkeyup="mention()"
                    placeholder="Just Xprez, {{$profile->user_name}}!" maxlength="500"></textarea>

                <ul id="mention-list">
                </ul>
            </div>
            <div id="count">
                <span id="current_count">0 </span>
                <span id="maximum_count">/ 500</span>
            </div>

            <div class="row">
                <div class="col-md-6 col-xxl-3 col-12 mt-2">
                    <select name="emotion" id="emotion" required="required">
                        <option disabled selected value="">Choose an Emotion</option>
                        <option value="Positive">Positive</option>
                        <option value="Negative">Negative</option>
                        <option value="Neutral">Neutral</option>
                    </select>
                </div>
                <div class="col-md-6 col-xxl-3 col-12 mt-2">
                    <input type="file" name="images[]" id="image" accept="image/*" multiple="multiple">
                </div>

                <div class="col-md-6 col-xxl-3 col-12 mt-3">
                    <input type="checkbox" name="anonymous" @if($anonymous) disabled @endif id="anonymous" value="1">
                    <label for="anonymous">Post as Anonymous</label>
                    @if($anonymous)
                    <small>(0 post available)</small>
                    @else
                    <small>(1 post available)</small>
                    @endif
                </div>

                <div class="col-md-6 col-xxl-3 col-12 mt-3 text-md-end">
                    <button class="btn btn-dark" type="submit">Post Update</button>
                </div>
            </div>
        </form>
    </div>

    <div id="linkPreview" class="mx-auto my-3"></div>


    <div class="score-section" id="score-card">
        <div class="score-text">
            <span>{{$profile->score}}</span>
        </div>
        <h4><a onclick="scoreDetails({{$profile->id}})" class="score-btn">Score</a></h4>
    </div>



    @if($ixprez != null)
    <!-- I Xprez -->
    <div class="mt-5">
        <h2 class="text-center">Ixprez</h2>
        <div class="activity-feed ixprez" id="activity-feed">

            <div class="activity-card">

                <?php 
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($ixprez->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                    if($diffInYears == 1)
                    {
                    $time = $diffInYears . " Year ago";
                    }
                    else{
                    $time = $diffInYears . " Years ago";
                    }
                }
                else if($diffInMonths != 0){
                    if($diffInMonths == 1)
                    {
                    $time = $diffInMonths . " Month ago";
                    }
                    else{
                    $time = $diffInMonths . " Months ago";
                    }
                }
                else if($diffInDays != 0){
                    if($diffInDays == 1){
                        $time = $diffInDays . " Day ago";
                    }
                    else
                    {
                        $time = $diffInDays . " Days ago";
                    }
                }
                else if($diffInHours != 0){
                    if($diffInHours == 1){
                    $time = $diffInHours . " hour ago";
                    }
                    else
                    {
                    $time = $diffInHours . " hours ago";
                    }
                }
                else if($diffInMinutes != 0) {
                    if($diffInMinutes == 1) {
                        $time = $diffInMinutes . " Minute ago";
                    }
                    else
                    {
                        $time = $diffInMinutes . " Minutes ago";
                    }
                }
                else
                {
                    $time = "Just now";
                }

                // link preview
                if(!function_exists("getSiteOG")) {
                    function getSiteOG( $url, $specificTags=0 ){
                        $doc = new DOMDocument();
                        @$doc->loadHTML(file_get_contents($url));
                        $res['title'] = isset($doc->getElementsByTagName('title')->item(0)->nodeValue) ?: null;
                        foreach ($doc->getElementsByTagName('meta') as $m){
                            $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
                            if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
                        }
                        return $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
                    }
                }
                if(isset($ixprez->preview_url))
                {
                    $og_details = getSiteOG($ixprez->preview_url);
                }

                // image
                $img = json_decode($ixprez->images);
                $total = 0;
                $counter = 1;
                $images = array();
                if ($ixprez->images != null) {
                    $image = json_decode($ixprez->images);
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
                }

                foreach ($images as $val) {
                    $total += 1;
                }

                ?>

                @if($ixprez->anonymous == 1)
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy" src="{{asset('')}}images/members/default-male.png" alt="default image"
                            width="40px" height="40px">
                        <h5>Anonymous</h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $ixprez->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>

                @else
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy"
                            src="http://localhost/wexprez_api/uploads/user_profile/{{$ixprez->profile_image}}"
                            alt="{{$ixprez->full_name}}" width="40px" height="40px">
                        <h5><a href="{{route('profileIxprez', $ixprez->user_name)}}">{{$ixprez->full_name}} </a>
                        </h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $ixprez->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>
                @endif

                <div class="activity-inner py-4">
                    <div class="activity-reaction">
                        @if($ixprez->emotion == 'Positive')
                        <img loading="lazy" src="{{asset('')}}images/emotion/positive.png" alt="emotion" width="40px"
                            height="40px">
                        @elseif($ixprez->emotion == 'Negative')
                        <img loading="lazy" src="{{asset('')}}images/emotion/negative.png" alt="emotion" width="40px"
                            height="40px">
                        @else
                        <img loading="lazy" src="{{asset('')}}images/emotion/neutral.png" alt="emotion" width="40px"
                            height="40px">
                        @endif
                    </div>
                    <div class="activity-content" id="activity-{{$ixprez->id}}">

                        <p id="activity-content-{{$ixprez->id}}">{!! $ixprez->content !!}</p>

                        @if($ixprez->images)
                        <div class="gallery" id="activity-image-{{$ixprez->id}}">

                            @if($images)
                            @foreach($images as $index => $item)
                            @if($total > 4)
                            @if($counter == 4)
                            <div class="see-more-img">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                <div class="text-block">{{$total - 4}}+</div>
                            </div>
                            @elseif($counter > 4)
                            <div class="d-none">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @else
                            <div class="">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @endif
                            @else
                                @if($total > 1)
                                <div class="">
                                    <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                            src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @else
                                <div  class="single-img">
                                <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @endif
                            @endif
                            @php $counter++ @endphp
                            @endforeach

                            @endif

                        </div>
                        @endif

                        <!-- Link Preview -->
                        <?php
                            if(empty($image)){
                            if(isset($og_details) && $og_details){
                                if(isset($og_details['title']) || isset($og_details['image']) || isset($og_details['description'])) {?>
                        <div class="debug-box" onclick="openLink(this);" data-link="<?php echo $ixprez->preview_url ?>">
                            <?php if(isset($og_details['image'])){ ?>
                            <img loading="lazy" src="<?php echo @$og_details['image'];?>" width="100%" height="100%">
                            <?php } ?>
                            <div class="text-wrapper">
                                <?php if(isset($og_details['title'])){ ?>
                                <p><strong><?php echo @$og_details['title'];?></strong></p>
                                <?php } 
                                if(isset($og_details['description'])){ ?>
                                <p class="description"><?php echo @$og_details['description'];?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } } }
                            $og_details = null;
                        ?>

                    </div>
                </div>

                <div class="activity-meta" id="activity-meta-{{$ixprez->id}}">
                    <div class="like">
                        <?php 
                                $up = 0;
                                $img = 'images/reaction/voteUp-off.png'; 
                            ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $ixprez->id)
                        @if($vote->type == 'up')
                        <?php $up++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteUp-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$ixprez->id}}" onclick="activityVoteUp(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-voteUp-image-{{$ixprez->id}}"></button>
                        <a class="voteUp-count" data-id="{{$ixprez->id}}" id="activity-voteUp-{{$ixprez->id}}"
                            onclick="activityVoteUpList(this)">{{$up}}</a>
                    </div>

                    <div class="dislike">

                        <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $ixprez->id)
                        @if($vote->type == 'down')
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteDown-on.png'; ?>
                        @endif
                        <?php $down++ ?>
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$ixprez->id}}" onclick="activityVoteDown(this)"><img loading="lazy"
                                src="{{asset('')}}{{$img}}" id="activity-voteDown-image-{{$ixprez->id}}"></button>
                        <a class="voteUp-count" data-id="{{$ixprez->id}}" id="activity-voteDown-{{$ixprez->id}}"
                            onclick="activityVoteDownList(this)">{{$down}}</a>
                    </div>

                    <div class="real">
                        <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $ixprez->id)
                        @if($react->type == 'real')
                        <?php $real++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/real-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$ixprez->id}}" onclick="activityReal(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-real-image-{{$ixprez->id}}"></button>
                        <a class="real-count" data-id="{{$ixprez->id}}" id="activity-real-{{$ixprez->id}}"
                            onclick="activityRealList(this)">{{$real}}</a>
                    </div>

                    <div class="fake">
                        <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $ixprez->id)
                        @if($react->type == 'fake')
                        <?php $fake++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/fake-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$ixprez->id}}" onclick="activityFake(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-fake-image-{{$ixprez->id}}"></button>
                        <a class="fake-count" data-id="{{$ixprez->id}}" id="activity-fake-{{$ixprez->id}}"
                            onclick="activityFakeList(this)">{{$fake}}</a>
                    </div>
                    <div class="comment" id="comment-{{$ixprez->id}}"><button parent-id="0" data-id="{{$ixprez->id}}"
                            onclick="commentCreate(this)">Comment</button></div>
                    @if($profile->id == $ixprez->user_id)
                    <div class="edit" id="edit-{{$ixprez->id}}"><button data-id="{{$ixprez->id}}"
                            onclick="activityEdit(this)">Edit</button></div>
                    <div class="delete" id="hide-{{$ixprez->id}}"><button data-id="{{$ixprez->id}}"
                            id="hideBtn-{{$ixprez->id}}" onclick="activityHide(this)">Hide</button></div>
                    <div class="edit" id="delete-{{$ixprez->id}}"><button data-id="{{$ixprez->id}}"
                            onclick="activityDelete(this)">Delete</button></div>
                    @endif

                    <div class="share" id="share-{{$ixprez->id}}">
                        <button data-id="{{$ixprez->id}}" onclick="share(this)"> <i class="fa-solid fa-share"></i>
                            Share</button>

                        <section id="share-option-{{$ixprez->id}}" class="share-option">
                            <a target="_blank" class="wexprez"
                                onclick="activityShare({{$ixprez->id}})">
                                <img src="{{asset('')}}images/logo/icon.png" alt="img"> wexprez</a>

                            <a target="_blank" class="facebook"
                                href="https://www.facebook.com/dialog/feed?app_id=350389333934305&display=popup&link={{route('singleActivity', $ixprez->id)}}">
                                <i class="fa-brands fa-facebook"></i> facebook</a>

                            <a target="_blank" class="twiteer"
                                href="https://twitter.com/intent/tweet?url={{route('singleActivity', $ixprez->id)}}&text={!! $ixprez->content !!}">
                                <img src="{{asset('')}}images/logo/x.png" alt="img"> X</a>

                            <a target="_blank" class="linkedin"
                                href="https://www.linkedin.com/shareArticle?url={{route('singleActivity', $ixprez->id)}}">
                                <i class="fa-brands fa-linkedin"></i> linkedin</a>

                            <a target="_blank" class="pinterest"
                                href="//pinterest.com/pin/create/link/?url={{route('singleActivity', $ixprez->id)}}&media=https://wexprez.com/images/fb-meta.png&description={!! $ixprez->content !!}">
                                <i class="fa-brands fa-pinterest"></i> pinterest</a>
                        </section>
                    </div>
                </div>
                <hr>

                <div class="comment-form" id="comment-form-{{$ixprez->id}}"></div>
                <div id="report-form-{{$ixprez->id}}"></div>


                <!-- Comments -->
                @foreach($comments as $comment)

                @php

                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($comment->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }
                @endphp


                @if($comment->activity_id == $ixprez->id)
                @if($comment->parent_id == 0)
                <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$comment->profile_image}}"
                                alt="{{$comment->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $comment->user_name)}}">{{$comment->full_name}}</a>
                            </h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$comment->id}}">
                        <p id="comment-content-{{$comment->id}}">{!! $comment->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$comment->id}}">
                        <div class="like">
                            <?php 
                                    $up = 0;
                                    $img = 'images/reaction/voteUp-off.png'; 
                                ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteUp-{{$comment->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                    $down = 0;
                                    $img = 'images/reaction/voteDown-off.png'; 
                                ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteDown-{{$comment->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$comment->id}}"></button>
                            <a class="real-count" data-id="{{$comment->id}}" id="comment-real-{{$comment->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                    $fake = 0;
                                    $img = 'images/reaction/fake-off.png';
                                ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$comment->id}}"></button>
                            <a class="fake-count" data-id="{{$comment->id}}" id="comment-fake-{{$comment->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>

                        <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                                data-id="{{$ixprez->id}}" onclick="replyCreate(this)">Reply</button></div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$comment->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$comment->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif

                        @if($profile->id != $comment->user_id)
                        <div class="comment-report" id="commentReport-{{$comment->id}}"><button
                                data-id="{{$comment->id}}" activity-id="{{$comment->activity_id}}"
                                onclick="commentReport(this)">Report</button></div>
                        @endif

                    </div>

                    <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                    <div id="comment-report-form-{{$comment->id}}" class="my-3"></div>
                </div>
                <hr>

                @foreach($comments as $reply)
                @if($reply->activity_id == $ixprez->id)
                @if($reply->parent_id == $comment->id)

                @php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($reply->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }

                @endphp

                <div class="activity-comment ps-5" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$reply->profile_image}}"
                                alt="{{$reply->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $reply->user_name)}}">{{$reply->full_name}}</a></h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$reply->id}}">
                        <p id="comment-content-{{$reply->id}}">{!! $reply->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$reply->id}}">

                        <div class="like">
                            <?php 
                                $up = 0;
                                $img = 'images/reaction/voteUp-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteUp-{{$reply->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteDown-{{$reply->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                    $real = 0;
                                    $img = 'images/reaction/real-off.png';
                                ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$reply->id}}"></button>
                            <a class="real-count" data-id="{{$reply->id}}" id="comment-real-{{$reply->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                    $fake = 0;
                                    $img = 'images/reaction/fake-off.png';
                                ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$reply->id}}"></button>
                            <a class="fake-count" data-id="{{$reply->id}}" id="comment-fake-{{$reply->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>
                        @if($profile->id == $reply->user_id)
                        <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$reply->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                    </div>
                </div>
                <hr>
                @endif
                @endif
                @endforeach


                @endif
                @endif
                @endforeach

            </div>
        </div>

        <div class="text-center mb-4">
            <a href="{{route('profileIxprez', $profile->user_name)}}" class="btn btn-dark text-center">Show More</a>
        </div>
    </div>

    @endif



    <!-- Weprez -->
    @if($wexprez != null)
    <div class="mt-5">
        <h2 class="text-center">WeXprez</h2>
        <div class="activity-feed ixprez" id="activity-feed">

            <div class="activity-card">

                <?php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($wexprez->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                    if($diffInYears == 1)
                    {
                    $time = $diffInYears . " Year ago";
                    }
                    else{
                    $time = $diffInYears . " Years ago";
                    }
                }
                else if($diffInMonths != 0){
                    if($diffInMonths == 1)
                    {
                    $time = $diffInMonths . " Month ago";
                    }
                    else{
                    $time = $diffInMonths . " Months ago";
                    }
                }
                else if($diffInDays != 0){
                    if($diffInDays == 1){
                        $time = $diffInDays . " Day ago";
                    }
                    else
                    {
                        $time = $diffInDays . " Days ago";
                    }
                }
                else if($diffInHours != 0){
                    if($diffInHours == 1){
                    $time = $diffInHours . " hour ago";
                    }
                    else
                    {
                    $time = $diffInHours . " hours ago";
                    }
                }
                else if($diffInMinutes != 0) {
                    if($diffInMinutes == 1) {
                        $time = $diffInMinutes . " Minute ago";
                    }
                    else
                    {
                        $time = $diffInMinutes . " Minutes ago";
                    }
                }
                else
                {
                    $time = "Just now";
                }

                // link preview
                if(!function_exists("getSiteOG")) {
                    function getSiteOG( $url, $specificTags=0 ){
                        $doc = new DOMDocument();
                        @$doc->loadHTML(file_get_contents($url));
                        $res['title'] = isset($doc->getElementsByTagName('title')->item(0)->nodeValue) ?: null;
                        foreach ($doc->getElementsByTagName('meta') as $m){
                            $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
                            if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
                        }
                        return $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
                    }
                }

                if(isset($wexprez->preview_url))
                {
                    $og_details = getSiteOG($wexprez->preview_url);
                }

                // image
                $img = json_decode($wexprez->images);
                $total = 0;
                $counter = 1;
                $images = array();
                if ($wexprez->images != null) {
                    $image = json_decode($wexprez->images);
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
                }

                foreach ($images as $val) {
                    $total += 1;
                }

                ?>

                @if($wexprez->anonymous == 1)
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy" src="{{asset('')}}images/members/default-male.png" alt="default image"
                            width="40px" height="40px">
                        <h5>Anonymous</h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $wexprez->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>

                @else
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy"
                            src="http://localhost/wexprez_api/uploads/user_profile/{{$wexprez->profile_image}}"
                            alt="{{$wexprez->full_name}}" width="40px" height="40px">
                        <h5><a href="{{route('profileIxprez', $wexprez->user_name)}}">{{$wexprez->full_name}} </a>
                        </h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $wexprez->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>
                @endif

                <div class="activity-inner py-4">
                    <div class="activity-reaction">
                        @if($wexprez->emotion == 'Positive')
                        <img loading="lazy" src="{{asset('')}}images/emotion/positive.png" alt="emotion" width="40px"
                            height="40px">
                        @elseif($wexprez->emotion == 'Negative')
                        <img loading="lazy" src="{{asset('')}}images/emotion/negative.png" alt="emotion" width="40px"
                            height="40px">
                        @else
                        <img loading="lazy" src="{{asset('')}}images/emotion/neutral.png" alt="emotion" width="40px"
                            height="40px">
                        @endif
                    </div>
                    <div class="activity-content" id="activity-{{$wexprez->id}}">
                        <p>{!! $wexprez->content !!}</p>

                        @if($wexprez->images)
                        <div class="gallery" id="activity-image-{{$wexprez->id}}">
                            @if($images)
                            @foreach($images as $index => $item)
                            @if($total > 4)
                            @if($counter == 4)
                            <div class="see-more-img">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                <div class="text-block">{{$total - 4}}+</div>
                            </div>
                            @elseif($counter > 4)
                            <div class="d-none">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @else
                            <div class="">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @endif
                            @else
                                @if($total > 1)
                                <div class="">
                                    <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                            src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @else
                                <div  class="single-img">
                                <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @endif
                            @endif
                            @php $counter++ @endphp
                            @endforeach

                            @endif

                        </div>
                        @endif
                        <!-- Link Preview -->
                        <?php
                            if(empty($image)){
                            if(isset($og_details) && $og_details){
                                if(isset($og_details['title']) || isset($og_details['image']) || isset($og_details['description'])) {?>
                        <div class="debug-box" onclick="openLink(this);"
                            data-link="<?php echo $wexprez->preview_url ?>">
                            <?php if(isset($og_details['image'])){ ?>
                            <img loading="lazy" src="<?php echo @$og_details['image'];?>" width="100%" height="100%">
                            <?php } ?>
                            <div class="text-wrapper">
                                <?php if(isset($og_details['title'])){ ?>
                                <p><strong><?php echo @$og_details['title'];?></strong></p>
                                <?php } 
                                if(isset($og_details['description'])){ ?>
                                <p class="description"><?php echo @$og_details['description'];?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } } }
                            $og_details = null;
                        ?>

                    </div>
                </div>

                <div class="activity-meta" id="activity-meta-{{$wexprez->id}}">
                    <div class="like">
                        <?php 
                                $up = 0;
                                $img = 'images/reaction/voteUp-off.png'; 
                            ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $wexprez->id)
                        @if($vote->type == 'up')
                        <?php $up++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteUp-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$wexprez->id}}" onclick="activityVoteUp(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-voteUp-image-{{$wexprez->id}}"></button>
                        <a class="voteUp-count" data-id="{{$wexprez->id}}" id="activity-voteUp-{{$wexprez->id}}"
                            onclick="activityVoteUpList(this)">{{$up}}</a>
                    </div>

                    <div class="dislike">

                        <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $wexprez->id)
                        @if($vote->type == 'down')
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteDown-on.png'; ?>
                        @endif
                        <?php $down++ ?>
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$wexprez->id}}" onclick="activityVoteDown(this)"><img loading="lazy"
                                src="{{asset('')}}{{$img}}" id="activity-voteDown-image-{{$wexprez->id}}"></button>
                        <a class="voteUp-count" data-id="{{$wexprez->id}}" id="activity-voteDown-{{$wexprez->id}}"
                            onclick="activityVoteDownList(this)">{{$down}}</a>
                    </div>

                    <div class="real">
                        <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $wexprez->id)
                        @if($react->type == 'real')
                        <?php $real++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/real-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$wexprez->id}}" onclick="activityReal(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-real-image-{{$wexprez->id}}"></button>
                        <a class="real-count" data-id="{{$wexprez->id}}" id="activity-real-{{$wexprez->id}}"
                            onclick="activityRealList(this)">{{$real}}</a>
                    </div>

                    <div class="fake">
                        <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $wexprez->id)
                        @if($react->type == 'fake')
                        <?php $fake++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/fake-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$wexprez->id}}" onclick="activityFake(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-fake-image-{{$wexprez->id}}"></button>
                        <a class="fake-count" data-id="{{$wexprez->id}}" id="activity-fake-{{$wexprez->id}}"
                            onclick="activityFakeList(this)">{{$fake}}</a>
                    </div>
                    <div class="comment" id="comment-{{$wexprez->id}}"><button parent-id="0" data-id="{{$wexprez->id}}"
                            onclick="commentCreate(this)">Comment</button></div>
                    @if($profile->id == $wexprez->user_id)
                    <div class="edit" id="edit-{{$wexprez->id}}"><button data-id="{{$wexprez->id}}"
                            onclick="activityEdit(this)">Edit</button></div>
                    <div class="delete" id="hide-{{$wexprez->id}}"><button data-id="{{$wexprez->id}}"
                            id="hideBtn-{{$wexprez->id}}" onclick="activityHide(this)">Hide</button></div>
                    <div class="edit" id="delete-{{$wexprez->id}}"><button data-id="{{$wexprez->id}}"
                            onclick="activityDelete(this)">Delete</button></div>
                    @endif

                    <div class="share" id="share-{{$wexprez->id}}">
                        <button data-id="{{$wexprez->id}}" onclick="share(this)"> <i class="fa-solid fa-share"></i>
                            Share</button>

                        <section id="share-option-{{$wexprez->id}}" class="share-option">
                            <a target="_blank" class="wexprez"
                                onclick="activityShare({{$wexprez->id}})">
                                <img src="{{asset('')}}images/logo/icon.png" alt="img"> wexprez</a>

                            <a target="_blank" class="facebook"
                                href="https://www.facebook.com/dialog/feed?app_id=350389333934305&display=popup&link={{route('singleActivity', $wexprez->id)}}">
                                <i class="fa-brands fa-facebook"></i> facebook</a>

                            <a target="_blank" class="twiteer"
                                href="https://twitter.com/intent/tweet?url={{route('singleActivity', $wexprez->id)}}&text={!! $wexprez->content !!}">
                                <img src="{{asset('')}}images/logo/x.png" alt="img"> X</a>

                            <a target="_blank" class="linkedin"
                                href="https://www.linkedin.com/shareArticle?url={{route('singleActivity', $wexprez->id)}}">
                                <i class="fa-brands fa-linkedin"></i> linkedin</a>

                            <a target="_blank" class="pinterest"
                                href="//pinterest.com/pin/create/link/?url={{route('singleActivity', $wexprez->id)}}&media=https://wexprez.com/images/fb-meta.png&description={!! $wexprez->content !!}">
                                <i class="fa-brands fa-pinterest"></i> pinterest</a>
                        </section>
                    </div>

                </div>
                <hr>

                <div class="comment-form" id="comment-form-{{$wexprez->id}}"></div>
                <div id="report-form-{{$wexprez->id}}"></div>


                <!-- Comments -->
                @foreach($comments as $comment)

                @php

                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($comment->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }

                @endphp


                @if($comment->activity_id == $wexprez->id)
                @if($comment->parent_id == 0)
                <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$comment->profile_image}}"
                                alt="{{$comment->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $comment->user_name)}}">{{$comment->full_name}}</a>
                            </h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$comment->id}}">
                        <p id="comment-content-{{$comment->id}}">{!! $comment->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$comment->id}}">
                        <div class="like">
                            <?php 
                                    $up = 0;
                                    $img = 'images/reaction/voteUp-off.png'; 
                                ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteUp-{{$comment->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                    $down = 0;
                                    $img = 'images/reaction/voteDown-off.png'; 
                                ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteDown-{{$comment->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$comment->id}}"></button>
                            <a class="real-count" data-id="{{$comment->id}}" id="comment-real-{{$comment->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                    $fake = 0;
                                    $img = 'images/reaction/fake-off.png';
                                ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$comment->id}}"></button>
                            <a class="fake-count" data-id="{{$comment->id}}" id="comment-fake-{{$comment->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>

                        <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                                data-id="{{$wexprez->id}}" onclick="replyCreate(this)">Reply</button></div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$comment->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$comment->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif

                        @if($profile->id != $comment->user_id)
                        <div class="comment-report" id="commentReport-{{$comment->id}}"><button
                                data-id="{{$comment->id}}" activity-id="{{$comment->activity_id}}"
                                onclick="commentReport(this)">Report</button></div>
                        @endif

                    </div>

                    <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                    <div id="comment-report-form-{{$comment->id}}" class="my-3"></div>
                </div>
                <hr>

                @foreach($comments as $reply)
                @if($reply->activity_id == $wexprez->id)
                @if($reply->parent_id == $comment->id)

                @php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($reply->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }

                @endphp

                <div class="activity-comment ps-5" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$reply->profile_image}}"
                                alt="{{$reply->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $reply->user_name)}}">{{$reply->full_name}}</a></h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$reply->id}}">
                        <p id="comment-content-{{$reply->id}}">{!! $reply->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$reply->id}}">

                        <div class="like">
                            <?php 
                                $up = 0;
                                $img = 'images/reaction/voteUp-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteUp-{{$reply->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteDown-{{$reply->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                    $real = 0;
                                    $img = 'images/reaction/real-off.png';
                                ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$reply->id}}"></button>
                            <a class="real-count" data-id="{{$reply->id}}" id="comment-real-{{$reply->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                    $fake = 0;
                                    $img = 'images/reaction/fake-off.png';
                                ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$reply->id}}"></button>
                            <a class="fake-count" data-id="{{$reply->id}}" id="comment-fake-{{$reply->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>
                        @if($profile->id == $reply->user_id)
                        <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$reply->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                    </div>
                </div>
                <hr>
                @endif
                @endif
                @endforeach


                @endif
                @endif
                @endforeach

            </div>
        </div>

        <div class="text-center mb-4">
            <a href="{{route('profileWexprez', $profile->user_name)}}" class="btn btn-dark text-center">Show More</a>
        </div>

    </div>

    @endif


    <!-- Following -->
    @if($fwing != null)
    <div class="row mt-5">
        <h2 class="text-center">Following</h2>
        <div class="activity-feed" id="activity-feed">

            <div class="activity-card">

                <?php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($fwing->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                    if($diffInYears == 1)
                    {
                    $time = $diffInYears . " Year ago";
                    }
                    else{
                    $time = $diffInYears . " Years ago";
                    }
                }
                else if($diffInMonths != 0){
                    if($diffInMonths == 1)
                    {
                    $time = $diffInMonths . " Month ago";
                    }
                    else{
                    $time = $diffInMonths . " Months ago";
                    }
                }
                else if($diffInDays != 0){
                    if($diffInDays == 1){
                        $time = $diffInDays . " Day ago";
                    }
                    else
                    {
                        $time = $diffInDays . " Days ago";
                    }
                }
                else if($diffInHours != 0){
                    if($diffInHours == 1){
                    $time = $diffInHours . " hour ago";
                    }
                    else
                    {
                    $time = $diffInHours . " hours ago";
                    }
                }
                else if($diffInMinutes != 0) {
                    if($diffInMinutes == 1) {
                        $time = $diffInMinutes . " Minute ago";
                    }
                    else
                    {
                        $time = $diffInMinutes . " Minutes ago";
                    }
                }
                else
                {
                    $time = "Just now";
                }

                // link preview
                if(!function_exists("getSiteOG")) {
                    function getSiteOG( $url, $specificTags=0 ){
                        $doc = new DOMDocument();
                        @$doc->loadHTML(file_get_contents($url));
                        $res['title'] = isset($doc->getElementsByTagName('title')->item(0)->nodeValue) ?: null;
                        foreach ($doc->getElementsByTagName('meta') as $m){
                            $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
                            if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
                        }
                        return $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
                    }
                }
                if(isset($fwing->preview_url))
                {
                    $og_details = getSiteOG($fwing->preview_url);
                }

                // image
                $img = json_decode($fwing->images);
                $total = 0;
                $counter = 1;
                $images = array();
                if ($fwing->images != null) {
                    $image = json_decode($fwing->images);
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
                }

                foreach ($images as $val) {
                    $total += 1;
                }

            ?>

                @if($fwing->anonymous == 1)
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy" src="{{asset('')}}images/members/default-male.png" alt="default image"
                            width="40px" height="40px">
                        <h5>Anonymous</h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $fwing->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>

                @else
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy"
                            src="http://localhost/wexprez_api/uploads/user_profile/{{$fwing->profile_image}}"
                            alt="{{$fwing->full_name}}" width="40px" height="40px">
                        <h5><a href="{{route('profileIxprez', $fwing->user_name)}}">{{$fwing->full_name}} </a>
                        </h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $fwing->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>
                @endif

                <div class="activity-inner py-4">
                    <div class="activity-reaction">
                        @if($fwing->emotion == 'Positive')
                        <img loading="lazy" src="{{asset('')}}images/emotion/positive.png" alt="emotion" width="40px"
                            height="40px">
                        @elseif($fwing->emotion == 'Negative')
                        <img loading="lazy" src="{{asset('')}}images/emotion/negative.png" alt="emotion" width="40px"
                            height="40px">
                        @else
                        <img loading="lazy" src="{{asset('')}}images/emotion/neutral.png" alt="emotion" width="40px"
                            height="40px">
                        @endif
                    </div>
                    <div class="activity-content" id="activity-{{$fwing->id}}">
                        <p>{!! $fwing->content !!}</p>

                        @if($fwing->images)
                        <div class="gallery" id="activity-image-{{$fwing->id}}">
                            @if($images)
                            @foreach($images as $index => $item)
                            @if($total > 4)
                            @if($counter == 4)
                            <div class="see-more-img">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                <div class="text-block">{{$total - 4}}+</div>
                            </div>
                            @elseif($counter > 4)
                            <div class="d-none">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @else
                                @if($total > 1)
                                <div class="">
                                    <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                            src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                    @else
                                    <div  class="single-img">
                                    <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                            src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @endif
                            @endif
                            @else
                                @if($total > 1)
                                <div class="">
                                    <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @else
                                <div  class="single-img">
                                <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                    src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @endif
                            @endif
                            @php $counter++ @endphp
                            @endforeach

                            @endif

                        </div>
                        @endif

                        <!-- Link Preview -->
                        <?php
                            if(empty($image)){
                            if(isset($og_details) && $og_details){
                                if(isset($og_details['title']) || isset($og_details['image']) || isset($og_details['description'])) {?>
                        <div class="debug-box" onclick="openLink(this);" data-link="<?php echo $fwing->preview_url ?>">
                            <?php if(isset($og_details['image'])){ ?>
                            <img loading="lazy" src="<?php echo @$og_details['image'];?>" width="100%" height="100%">
                            <?php } ?>
                            <div class="text-wrapper">
                                <?php if(isset($og_details['title'])){ ?>
                                <p><strong><?php echo @$og_details['title'];?></strong></p>
                                <?php } 
                                if(isset($og_details['description'])){ ?>
                                <p class="description"><?php echo @$og_details['description'];?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } } }
                            $og_details = null;
                        ?>

                    </div>
                </div>

                <div class="activity-meta" id="activity-meta-{{$fwing->id}}">
                    <div class="like">
                        <?php 
                        $up = 0;
                        $img = 'images/reaction/voteUp-off.png'; 
                        ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $fwing->id)
                        @if($vote->type == 'up')
                        <?php $up++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteUp-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$fwing->id}}" onclick="activityVoteUp(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-voteUp-image-{{$fwing->id}}"></button>
                        <a class="voteUp-count" data-id="{{$fwing->id}}" id="activity-voteUp-{{$fwing->id}}"
                            onclick="activityVoteUpList(this)">{{$up}}</a>
                    </div>

                    <div class="dislike">

                        <?php 
                        $down = 0;
                        $img = 'images/reaction/voteDown-off.png'; 
                    ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $fwing->id)
                        @if($vote->type == 'down')
                        <?php $down++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteDown-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$fwing->id}}" onclick="activityVoteDown(this)"><img loading="lazy"
                                src="{{asset('')}}{{$img}}" id="activity-voteDown-image-{{$fwing->id}}"></button>
                        <a class="voteUp-count" data-id="{{$fwing->id}}" id="activity-voteDown-{{$fwing->id}}"
                            onclick="activityVoteDownList(this)">{{$down}}</a>
                    </div>

                    <div class="real">
                        <?php 
                        $real = 0;
                        $img = 'images/reaction/real-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $fwing->id)
                        @if($react->type == 'real')
                        <?php $real++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/real-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$fwing->id}}" onclick="activityReal(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-real-image-{{$fwing->id}}"></button>
                        <a class="real-count" data-id="{{$fwing->id}}" id="activity-real-{{$fwing->id}}"
                            onclick="activityRealList(this)">{{$real}}</a>
                    </div>

                    <div class="fake">
                        <?php 
                        $fake = 0;
                        $img = 'images/reaction/fake-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $fwing->id)
                        @if($react->type == 'fake')
                        <?php $fake++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/fake-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$fwing->id}}" onclick="activityFake(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-fake-image-{{$fwing->id}}"></button>
                        <a class="fake-count" data-id="{{$fwing->id}}" id="activity-fake-{{$fwing->id}}"
                            onclick="activityFakeList(this)">{{$fake}}</a>
                    </div>
                    <div class="comment" id="comment-{{$fwing->id}}"><button parent-id="0" data-id="{{$fwing->id}}"
                            onclick="commentCreate(this)">Comment</button></div>
                    @if($profile->id == $fwing->user_id)
                    <div class="edit" id="edit-{{$fwing->id}}"><button data-id="{{$fwing->id}}"
                            onclick="activityEdit(this)">Edit</button></div>
                    <div class="delete" id="hide-{{$fwing->id}}"><button data-id="{{$fwing->id}}"
                            id="hideBtn-{{$fwing->id}}" onclick="activityHide(this)">Hide</button></div>
                    <div class="delete" id="delete-{{$fwing->id}}"><button data-id="{{$fwing->id}}"
                            onclick="activityDelete(this)">Delete</button></div>
                    @endif
                    <div class="report" id="report-{{$fwing->id}}"><button data-id="{{$fwing->id}}"
                            onclick="activityReport(this)">Report</button></div>

                    <div class="share" id="share-{{$fwing->id}}">
                        <button data-id="{{$fwing->id}}" onclick="share(this)"> <i class="fa-solid fa-share"></i>
                            Share</button>

                        <section id="share-option-{{$fwing->id}}" class="share-option">
                            <a target="_blank" class="wexprez"
                                onclick="activityShare({{$fwing->id}})">
                                <img src="{{asset('')}}images/logo/icon.png" alt="img"> wexprez</a>

                            <a target="_blank" class="facebook"
                                href="https://www.facebook.com/dialog/feed?app_id=350389333934305&display=popup&link={{route('singleActivity', $fwing->id)}}">
                                <i class="fa-brands fa-facebook"></i> facebook</a>

                            <a target="_blank" class="twiteer"
                                href="https://twitter.com/intent/tweet?url={{route('singleActivity', $fwing->id)}}&text={!! $fwing->content !!}">
                                <img src="{{asset('')}}images/logo/x.png" alt="img"> X</a>

                            <a target="_blank" class="linkedin"
                                href="https://www.linkedin.com/shareArticle?url={{route('singleActivity', $fwing->id)}}">
                                <i class="fa-brands fa-linkedin"></i> linkedin</a>

                            <a target="_blank" class="pinterest"
                                href="//pinterest.com/pin/create/link/?url={{route('singleActivity', $fwing->id)}}&media=https://wexprez.com/images/fb-meta.png&description={!! $fwing->content !!}">
                                <i class="fa-brands fa-pinterest"></i> pinterest</a>
                        </section>
                    </div>

                </div>
                <hr>

                <div class="comment-form" id="comment-form-{{$fwing->id}}"></div>
                <div id="report-form-{{$fwing->id}}"></div>


                <!-- Comments -->
                @foreach($comments as $comment)

                @php

                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($comment->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }
                @endphp


                @if($comment->activity_id == $fwing->id)
                @if($comment->parent_id == 0)
                <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$comment->profile_image}}"
                                alt="{{$comment->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $comment->user_name)}}">{{$comment->full_name}}</a>
                            </h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$comment->id}}">
                        <p id="comment-content-{{$comment->id}}">{!! $comment->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$comment->id}}">
                        <div class="like">
                            <?php 
            $up = 0;
            $img = 'images/reaction/voteUp-off.png'; 
        ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteUp-{{$comment->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteDown-{{$comment->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$comment->id}}"></button>
                            <a class="real-count" data-id="{{$comment->id}}" id="comment-real-{{$comment->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$comment->id}}"></button>
                            <a class="fake-count" data-id="{{$comment->id}}" id="comment-fake-{{$comment->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>

                        <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                                data-id="{{$fwing->id}}" onclick="replyCreate(this)">Reply</button></div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$comment->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$comment->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                        @if($profile->id != $comment->user_id)
                        <div class="comment-report" id="commentReport-{{$comment->id}}"><button
                                data-id="{{$comment->id}}" activity-id="{{$comment->activity_id}}"
                                onclick="commentReport(this)">Report</button></div>
                        @endif


                    </div>
                    <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                    <div id="comment-report-form-{{$comment->id}}" class="my-3"></div>

                </div>
                <hr>

                @foreach($comments as $reply)
                @if($reply->activity_id == $fwing->id)
                @if($reply->parent_id == $comment->id)

                @php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($reply->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }

                @endphp

                <div class="activity-comment ps-5" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$reply->profile_image}}"
                                alt="{{$reply->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $reply->user_name)}}">{{$reply->full_name}}</a></h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$reply->id}}">
                        <p id="comment-content-{{$reply->id}}">{!! $reply->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$reply->id}}">

                        <div class="like">
                            <?php 
                            $up = 0;
                            $img = 'images/reaction/voteUp-off.png'; 
                        ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteUp-{{$reply->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteDown-{{$reply->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$reply->id}}"></button>
                            <a class="real-count" data-id="{{$reply->id}}" id="comment-real-{{$reply->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$reply->id}}"></button>
                            <a class="fake-count" data-id="{{$reply->id}}" id="comment-fake-{{$reply->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$reply->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$reply->id}}"><button data-id="{{$reply->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                    </div>
                </div>
                <hr>
                @endif
                @endif
                @endforeach


                @endif
                @endif
                @endforeach


            </div>

        </div>
        <div class="text-center mb-4">
            <a href="{{route('profileFollowing', $profile->user_name)}}" class="btn btn-dark text-center">Show More</a>
        </div>
    </div>

    @endif



    <!-- Followers -->
    @if($fwers != null)
    <div class="row mt-5">
        <h2 class="text-center">Followers</h2>
        <div class="activity-feed" id="activity-feed">

            <div class="activity-card">

                <?php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($fwers->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                    if($diffInYears == 1)
                    {
                    $time = $diffInYears . " Year ago";
                    }
                    else{
                    $time = $diffInYears . " Years ago";
                    }
                }
                else if($diffInMonths != 0){
                    if($diffInMonths == 1)
                    {
                    $time = $diffInMonths . " Month ago";
                    }
                    else{
                    $time = $diffInMonths . " Months ago";
                    }
                }
                else if($diffInDays != 0){
                    if($diffInDays == 1){
                        $time = $diffInDays . " Day ago";
                    }
                    else
                    {
                        $time = $diffInDays . " Days ago";
                    }
                }
                else if($diffInHours != 0){
                    if($diffInHours == 1){
                    $time = $diffInHours . " hour ago";
                    }
                    else
                    {
                    $time = $diffInHours . " hours ago";
                    }
                }
                else if($diffInMinutes != 0) {
                    if($diffInMinutes == 1) {
                        $time = $diffInMinutes . " Minute ago";
                    }
                    else
                    {
                        $time = $diffInMinutes . " Minutes ago";
                    }
                }
                else
                {
                    $time = "Just now";
                }

                // link preview
                if(!function_exists("getSiteOG")) {
                    function getSiteOG( $url, $specificTags=0 ){
                        $doc = new DOMDocument();
                        @$doc->loadHTML(file_get_contents($url));
                        $res['title'] = isset($doc->getElementsByTagName('title')->item(0)->nodeValue) ?: null;
                        foreach ($doc->getElementsByTagName('meta') as $m){
                            $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
                            if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
                        }
                        return $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
                    }
                }
                if(isset($fwers->preview_url))
                {
                    $og_details = getSiteOG($fwers->preview_url);
                }

                // image
                $img = json_decode($fwers->images);
                $total = 0;
                $counter = 1;
                $images = array();
                if ($fwers->images != null) {
                    $image = json_decode($fwers->images);
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
                }

                foreach ($images as $val) {
                    $total += 1;
                }

            ?>

                @if($fwers->anonymous == 1)
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy" src="{{asset('')}}images/members/default-male.png" alt="default image"
                            width="40px" height="40px">
                        <h5>Anonymous</h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $fwers->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>

                @else
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy"
                            src="http://localhost/wexprez_api/uploads/user_profile/{{$fwers->profile_image}}"
                            alt="{{$fwers->full_name}}" width="40px" height="40px">
                        <h5><a href="{{route('profileIxprez', $fwers->user_name)}}">{{$fwers->full_name}} </a>
                        </h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $fwers->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>
                @endif

                <div class="activity-inner py-4">
                    <div class="activity-reaction">
                        @if($fwers->emotion == 'Positive')
                        <img loading="lazy" src="{{asset('')}}images/emotion/positive.png" alt="emotion" width="40px"
                            height="40px">
                        @elseif($fwers->emotion == 'Negative')
                        <img loading="lazy" src="{{asset('')}}images/emotion/negative.png" alt="emotion" width="40px"
                            height="40px">
                        @else
                        <img loading="lazy" src="{{asset('')}}images/emotion/neutral.png" alt="emotion" width="40px"
                            height="40px">
                        @endif
                    </div>
                    <div class="activity-content" id="activity-{{$fwers->id}}">
                        <p>{!! $fwers->content !!}</p>

                        @if($fwers->images)
                        <div class="gallery" id="activity-image-{{$fwers->id}}">

                            @if($images)
                            @foreach($images as $index => $item)
                            @if($total > 4)
                            @if($counter == 4)
                            <div class="see-more-img">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                <div class="text-block">{{$total - 4}}+</div>
                            </div>
                            @elseif($counter > 4)
                            <div class="d-none">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @else
                            <div class="">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @endif
                            @else
                                @if($total > 1)
                                <div class="">
                                    <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                            src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @else
                                <div  class="single-img">
                                <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @endif
                            @endif
                            @php $counter++ @endphp
                            @endforeach

                            @endif

                        </div>
                        @endif

                        <!-- Link Preview -->
                        <?php
                            if(empty($image)){
                            if(isset($og_details) && $og_details){
                                if(isset($og_details['title']) || isset($og_details['image']) || isset($og_details['description'])) {?>
                        <div class="debug-box" onclick="openLink(this);" data-link="<?php echo $fwers->preview_url ?>">
                            <?php if(isset($og_details['image'])){ ?>
                            <img loading="lazy" src="<?php echo @$og_details['image'];?>" width="100%" height="100%">
                            <?php } ?>
                            <div class="text-wrapper">
                                <?php if(isset($og_details['title'])){ ?>
                                <p><strong><?php echo @$og_details['title'];?></strong></p>
                                <?php } 
                                if(isset($og_details['description'])){ ?>
                                <p class="description"><?php echo @$og_details['description'];?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } } }
                            $og_details = null;
                        ?>

                    </div>
                </div>

                <div class="activity-meta" id="activity-meta-{{$fwers->id}}">
                    <div class="like">
                        <?php 
                        $up = 0;
                        $img = 'images/reaction/voteUp-off.png'; 
                        ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $fwers->id)
                        @if($vote->type == 'up')
                        <?php $up++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteUp-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$fwers->id}}" onclick="activityVoteUp(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-voteUp-image-{{$fwers->id}}"></button>
                        <a class="voteUp-count" data-id="{{$fwers->id}}" id="activity-voteUp-{{$fwers->id}}"
                            onclick="activityVoteUpList(this)">{{$up}}</a>
                    </div>

                    <div class="dislike">

                        <?php 
                        $down = 0;
                        $img = 'images/reaction/voteDown-off.png'; 
                    ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $fwers->id)
                        @if($vote->type == 'down')
                        <?php $down++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteDown-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$fwers->id}}" onclick="activityVoteDown(this)"><img loading="lazy"
                                src="{{asset('')}}{{$img}}" id="activity-voteDown-image-{{$fwers->id}}"></button>
                        <a class="voteUp-count" data-id="{{$fwers->id}}" id="activity-voteDown-{{$fwers->id}}"
                            onclick="activityVoteDownList(this)">{{$down}}</a>
                    </div>

                    <div class="real">
                        <?php 
                        $real = 0;
                        $img = 'images/reaction/real-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $fwers->id)
                        @if($react->type == 'real')
                        <?php $real++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/real-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$fwers->id}}" onclick="activityReal(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-real-image-{{$fwers->id}}"></button>
                        <a class="real-count" data-id="{{$fwers->id}}" id="activity-real-{{$fwers->id}}"
                            onclick="activityRealList(this)">{{$real}}</a>
                    </div>

                    <div class="fake">
                        <?php 
                        $fake = 0;
                        $img = 'images/reaction/fake-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $fwers->id)
                        @if($react->type == 'fake')
                        <?php $fake++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/fake-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$fwers->id}}" onclick="activityFake(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-fake-image-{{$fwers->id}}"></button>
                        <a class="fake-count" data-id="{{$fwers->id}}" id="activity-fake-{{$fwers->id}}"
                            onclick="activityFakeList(this)">{{$fake}}</a>
                    </div>
                    <div class="comment" id="comment-{{$fwers->id}}"><button parent-id="0" data-id="{{$fwers->id}}"
                            onclick="commentCreate(this)">Comment</button></div>
                    @if($profile->id == $fwers->user_id)
                    <div class="edit" id="edit-{{$fwers->id}}"><button data-id="{{$fwers->id}}"
                            onclick="activityEdit(this)">Edit</button></div>
                    <div class="delete" id="hide-{{$fwers->id}}"><button data-id="{{$fwers->id}}"
                            id="hideBtn-{{$fwers->id}}" onclick="activityHide(this)">Hide</button></div>
                    <div class="delete" id="delete-{{$fwers->id}}"><button data-id="{{$fwers->id}}"
                            onclick="activityDelete(this)">Delete</button></div>
                    @endif
                    <div class="report" id="report-{{$fwers->id}}"><button data-id="{{$fwers->id}}"
                            onclick="activityReport(this)">Report</button></div>

                    <div class="share" id="share-{{$fwers->id}}">
                        <button data-id="{{$fwers->id}}" onclick="share(this)"> <i class="fa-solid fa-share"></i>
                            Share</button>

                        <section id="share-option-{{$fwers->id}}" class="share-option">
                            <a target="_blank" class="wexprez"
                                onclick="activityShare({{$fwers->id}})">
                                <img src="{{asset('')}}images/logo/icon.png" alt="img"> wexprez</a>

                            <a target="_blank" class="facebook"
                                href="https://www.facebook.com/dialog/feed?app_id=350389333934305&display=popup&link={{route('singleActivity', $fwers->id)}}">
                                <i class="fa-brands fa-facebook"></i> facebook</a>

                            <a target="_blank" class="twiteer"
                                href="https://twitter.com/intent/tweet?url={{route('singleActivity', $fwers->id)}}&text={!! $fwers->content !!}">
                                <img src="{{asset('')}}images/logo/x.png" alt="img"> X</a>

                            <a target="_blank" class="linkedin"
                                href="https://www.linkedin.com/shareArticle?url={{route('singleActivity', $fwers->id)}}">
                                <i class="fa-brands fa-linkedin"></i> linkedin</a>

                            <a target="_blank" class="pinterest"
                                href="//pinterest.com/pin/create/link/?url={{route('singleActivity', $fwers->id)}}&media=https://wexprez.com/images/fb-meta.png&description={!! $fwers->content !!}">
                                <i class="fa-brands fa-pinterest"></i> pinterest</a>
                        </section>
                    </div>

                </div>
                <hr>

                <div class="comment-form" id="comment-form-{{$fwers->id}}"></div>
                <div id="report-form-{{$fwers->id}}"></div>


                <!-- Comments -->
                @foreach($comments as $comment)

                @php

                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($comment->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }
                @endphp


                @if($comment->activity_id == $fwers->id)
                @if($comment->parent_id == 0)
                <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$comment->profile_image}}"
                                alt="{{$comment->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $comment->user_name)}}">{{$comment->full_name}}</a>
                            </h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$comment->id}}">
                        <p id="comment-content-{{$comment->id}}">{!! $comment->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$comment->id}}">
                        <div class="like">
                            <?php 
            $up = 0;
            $img = 'images/reaction/voteUp-off.png'; 
        ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteUp-{{$comment->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteDown-{{$comment->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$comment->id}}"></button>
                            <a class="real-count" data-id="{{$comment->id}}" id="comment-real-{{$comment->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$comment->id}}"></button>
                            <a class="fake-count" data-id="{{$comment->id}}" id="comment-fake-{{$comment->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>

                        <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                                data-id="{{$fwers->id}}" onclick="replyCreate(this)">Reply</button></div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$comment->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$comment->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                        @if($profile->id != $comment->user_id)
                        <div class="comment-report" id="commentReport-{{$comment->id}}"><button
                                data-id="{{$comment->id}}" activity-id="{{$comment->activity_id}}"
                                onclick="commentReport(this)">Report</button></div>
                        @endif


                    </div>
                    <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                    <div id="comment-report-form-{{$comment->id}}" class="my-3"></div>

                </div>
                <hr>

                @foreach($comments as $reply)
                @if($reply->activity_id == $fwers->id)
                @if($reply->parent_id == $comment->id)

                @php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($reply->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }

                @endphp

                <div class="activity-comment ps-5" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$reply->profile_image}}"
                                alt="{{$reply->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $reply->user_name)}}">{{$reply->full_name}}</a></h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$reply->id}}">
                        <p id="comment-content-{{$reply->id}}">{!! $reply->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$reply->id}}">

                        <div class="like">
                            <?php 
                            $up = 0;
                            $img = 'images/reaction/voteUp-off.png'; 
                        ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteUp-{{$reply->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteDown-{{$reply->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$reply->id}}"></button>
                            <a class="real-count" data-id="{{$reply->id}}" id="comment-real-{{$reply->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$reply->id}}"></button>
                            <a class="fake-count" data-id="{{$reply->id}}" id="comment-fake-{{$reply->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$reply->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$reply->id}}"><button data-id="{{$reply->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                    </div>
                </div>
                <hr>
                @endif
                @endif
                @endforeach


                @endif
                @endif
                @endforeach


            </div>

        </div>
        <div class="text-center mb-4">
            <a href="{{route('profileFollower', $profile->user_name)}}" class="btn btn-dark text-center">Show More</a>
        </div>
    </div>

    @endif




    <!-- Other -->
    @if($other != null)
    <div class="row mt-5">
        <h2 class="text-center">Other</h2>
        <div class="activity-feed" id="activity-feed">

            <div class="activity-card">

                <?php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($other->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                    if($diffInYears == 1)
                    {
                    $time = $diffInYears . " Year ago";
                    }
                    else{
                    $time = $diffInYears . " Years ago";
                    }
                }
                else if($diffInMonths != 0){
                    if($diffInMonths == 1)
                    {
                    $time = $diffInMonths . " Month ago";
                    }
                    else{
                    $time = $diffInMonths . " Months ago";
                    }
                }
                else if($diffInDays != 0){
                    if($diffInDays == 1){
                        $time = $diffInDays . " Day ago";
                    }
                    else
                    {
                        $time = $diffInDays . " Days ago";
                    }
                }
                else if($diffInHours != 0){
                    if($diffInHours == 1){
                    $time = $diffInHours . " hour ago";
                    }
                    else
                    {
                    $time = $diffInHours . " hours ago";
                    }
                }
                else if($diffInMinutes != 0) {
                    if($diffInMinutes == 1) {
                        $time = $diffInMinutes . " Minute ago";
                    }
                    else
                    {
                        $time = $diffInMinutes . " Minutes ago";
                    }
                }
                else
                {
                    $time = "Just now";
                }

                // link preview
                if(!function_exists("getSiteOG")) {
                    function getSiteOG( $url, $specificTags=0 ){
                        $doc = new DOMDocument();
                        @$doc->loadHTML(file_get_contents($url));
                        $res['title'] = isset($doc->getElementsByTagName('title')->item(0)->nodeValue) ?: null;
                        foreach ($doc->getElementsByTagName('meta') as $m){
                            $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
                            if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
                        }
                        return $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
                    }
                }
                if(isset($other->preview_url))
                {
                    $og_details = getSiteOG($other->preview_url);
                }

                // image
                $img = json_decode($other->images);
                $total = 0;
                $counter = 1;
                $images = array();
                if ($other->images != null) {
                    $image = json_decode($other->images);
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
                }

                foreach ($images as $val) {
                    $total += 1;
                }

            ?>

                @if($other->anonymous == 1)
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy" src="{{asset('')}}images/members/default-male.png" alt="default image"
                            width="40px" height="40px">
                        <h5>Anonymous</h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $other->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>

                @else
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy"
                            src="http://localhost/wexprez_api/uploads/user_profile/{{$other->profile_image}}"
                            alt="{{$other->full_name}}" width="40px" height="40px">
                        <h5><a href="{{route('profileIxprez', $other->user_name)}}">{{$other->full_name}} </a>
                        </h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $other->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>
                @endif

                <div class="activity-inner py-4">
                    <div class="activity-reaction">
                        @if($other->emotion == 'Positive')
                        <img loading="lazy" src="{{asset('')}}images/emotion/positive.png" alt="emotion" width="40px"
                            height="40px">
                        @elseif($other->emotion == 'Negative')
                        <img loading="lazy" src="{{asset('')}}images/emotion/negative.png" alt="emotion" width="40px"
                            height="40px">
                        @else
                        <img loading="lazy" src="{{asset('')}}images/emotion/neutral.png" alt="emotion" width="40px"
                            height="40px">
                        @endif
                    </div>
                    <div class="activity-content" id="activity-{{$other->id}}">
                        <p>{!! $other->content !!}</p>

                        @if($other->images)
                        <div class="gallery" id="activity-image-{{$other->id}}">

                            @if($images)
                            @foreach($images as $index => $item)
                            @if($total > 4)
                            @if($counter == 4)
                            <div class="see-more-img">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                <div class="text-block">{{$total - 4}}+</div>
                            </div>
                            @elseif($counter > 4)
                            <div class="d-none">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @else
                            <div class="">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @endif
                            @else
                                @if($total > 1)
                                <div class="">
                                    <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                        src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @else
                                <div  class="single-img">
                                <a href="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"><img loading="lazy"
                                    src="https://www.wexprez.com/wex_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @endif
                            @endif
                            @php $counter++ @endphp
                            @endforeach

                            @endif

                        </div>
                        @endif

                        <!-- Link Preview -->
                        <?php
                            if(empty($image)){
                            if(isset($og_details) && $og_details){
                                if(isset($og_details['title']) || isset($og_details['image']) || isset($og_details['description'])) {?>
                        <div class="debug-box" onclick="openLink(this);" data-link="<?php echo $other->preview_url ?>">
                            <?php if(isset($og_details['image'])){ ?>
                            <img loading="lazy" src="<?php echo @$og_details['image'];?>" width="100%" height="100%">
                            <?php } ?>
                            <div class="text-wrapper">
                                <?php if(isset($og_details['title'])){ ?>
                                <p><strong><?php echo @$og_details['title'];?></strong></p>
                                <?php } 
                                if(isset($og_details['description'])){ ?>
                                <p class="description"><?php echo @$og_details['description'];?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } } }
                            $og_details = null;
                        ?>

                    </div>
                </div>

                <div class="activity-meta" id="activity-meta-{{$other->id}}">
                    <div class="like">
                        <?php 
                        $up = 0;
                        $img = 'images/reaction/voteUp-off.png'; 
                        ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $other->id)
                        @if($vote->type == 'up')
                        <?php $up++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteUp-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$other->id}}" onclick="activityVoteUp(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-voteUp-image-{{$other->id}}"></button>
                        <a class="voteUp-count" data-id="{{$other->id}}" id="activity-voteUp-{{$other->id}}"
                            onclick="activityVoteUpList(this)">{{$up}}</a>
                    </div>

                    <div class="dislike">

                        <?php 
                        $down = 0;
                        $img = 'images/reaction/voteDown-off.png'; 
                    ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $other->id)
                        @if($vote->type == 'down')
                        <?php $down++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/voteDown-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$other->id}}" onclick="activityVoteDown(this)"><img loading="lazy"
                                src="{{asset('')}}{{$img}}" id="activity-voteDown-image-{{$other->id}}"></button>
                        <a class="voteUp-count" data-id="{{$other->id}}" id="activity-voteDown-{{$other->id}}"
                            onclick="activityVoteDownList(this)">{{$down}}</a>
                    </div>

                    <div class="real">
                        <?php 
                        $real = 0;
                        $img = 'images/reaction/real-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $other->id)
                        @if($react->type == 'real')
                        <?php $real++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/real-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$other->id}}" onclick="activityReal(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-real-image-{{$other->id}}"></button>
                        <a class="real-count" data-id="{{$other->id}}" id="activity-real-{{$other->id}}"
                            onclick="activityRealList(this)">{{$real}}</a>
                    </div>

                    <div class="fake">
                        <?php 
                        $fake = 0;
                        $img = 'images/reaction/fake-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $other->id)
                        @if($react->type == 'fake')
                        <?php $fake++ ?>
                        @if($profile->id == $vote->user_id)
                        <?php $img = 'images/reaction/fake-on.png'; ?>
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$other->id}}" onclick="activityFake(this)">
                            <img loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-fake-image-{{$other->id}}"></button>
                        <a class="fake-count" data-id="{{$other->id}}" id="activity-fake-{{$other->id}}"
                            onclick="activityFakeList(this)">{{$fake}}</a>
                    </div>
                    <div class="comment" id="comment-{{$other->id}}"><button parent-id="0" data-id="{{$other->id}}"
                            onclick="commentCreate(this)">Comment</button></div>
                    @if($profile->id == $other->user_id)
                    <div class="edit" id="edit-{{$other->id}}"><button data-id="{{$other->id}}"
                            onclick="activityEdit(this)">Edit</button></div>
                    <div class="delete" id="hide-{{$other->id}}"><button data-id="{{$other->id}}"
                            id="hideBtn-{{$other->id}}" onclick="activityHide(this)">Hide</button></div>
                    <div class="delete" id="delete-{{$other->id}}"><button data-id="{{$other->id}}"
                            onclick="activityDelete(this)">Delete</button></div>
                    @endif
                    <div class="report" id="report-{{$other->id}}"><button data-id="{{$other->id}}"
                            onclick="activityReport(this)">Report</button></div>

                    <div class="share" id="share-{{$other->id}}">
                        <button data-id="{{$other->id}}" onclick="share(this)"> <i class="fa-solid fa-share"></i>
                            Share</button>

                        <section id="share-option-{{$other->id}}" class="share-option">
                            <a target="_blank" class="wexprez"
                                onclick="activityShare({{$other->id}})">
                                <img src="{{asset('')}}images/logo/icon.png" alt="img"> wexprez</a>

                            <a target="_blank" class="facebook"
                                href="https://www.facebook.com/dialog/feed?app_id=350389333934305&display=popup&link={{route('singleActivity', $other->id)}}">
                                <i class="fa-brands fa-facebook"></i> facebook</a>

                            <a target="_blank" class="twiteer"
                                href="https://twitter.com/intent/tweet?url={{route('singleActivity', $other->id)}}&text={!! $other->content !!}">
                                <img src="{{asset('')}}images/logo/x.png" alt="img"> X</a>

                            <a target="_blank" class="linkedin"
                                href="https://www.linkedin.com/shareArticle?url={{route('singleActivity', $other->id)}}">
                                <i class="fa-brands fa-linkedin"></i> linkedin</a>

                            <a target="_blank" class="pinterest"
                                href="//pinterest.com/pin/create/link/?url={{route('singleActivity', $other->id)}}&media=https://wexprez.com/images/fb-meta.png&description={!! $other->content !!}">
                                <i class="fa-brands fa-pinterest"></i> pinterest</a>
                        </section>
                    </div>
                </div>
                <hr>

                <div class="comment-form" id="comment-form-{{$other->id}}"></div>
                <div id="report-form-{{$other->id}}"></div>


                <!-- Comments -->
                @foreach($comments as $comment)

                @php

                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($comment->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }
                @endphp


                @if($comment->activity_id == $other->id)
                @if($comment->parent_id == 0)
                <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$comment->profile_image}}"
                                alt="{{$comment->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $comment->user_name)}}">{{$comment->full_name}}</a>
                            </h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$comment->id}}">
                        <p id="comment-content-{{$comment->id}}">{!! $comment->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$comment->id}}">
                        <div class="like">
                            <?php 
            $up = 0;
            $img = 'images/reaction/voteUp-off.png'; 
        ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteUp-{{$comment->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $comment->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteDown-{{$comment->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$comment->id}}"></button>
                            <a class="real-count" data-id="{{$comment->id}}" id="comment-real-{{$comment->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $comment->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$comment->id}}"></button>
                            <a class="fake-count" data-id="{{$comment->id}}" id="comment-fake-{{$comment->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>

                        <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                                data-id="{{$other->id}}" onclick="replyCreate(this)">Reply</button></div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$comment->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$comment->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                        @if($profile->id != $comment->user_id)
                        <div class="comment-report" id="commentReport-{{$comment->id}}"><button
                                data-id="{{$comment->id}}" activity-id="{{$comment->activity_id}}"
                                onclick="commentReport(this)">Report</button></div>
                        @endif


                    </div>
                    <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                    <div id="comment-report-form-{{$comment->id}}" class="my-3"></div>

                </div>
                <hr>

                @foreach($comments as $reply)
                @if($reply->activity_id == $other->id)
                @if($reply->parent_id == $comment->id)

                @php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($reply->created_at, new DateTimeZone('Asia/Dhaka'));
                $interval = $date1->diff($date2);

                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours = $interval->h; //8
                $diffInDays = $interval->d; //21
                $diffInMonths = $interval->m; //21
                $diffInYears = $interval->y; //21

                if($diffInYears != 0){
                if($diffInYears == 1)
                {
                $time = $diffInYears . " Year ago";
                }
                else{
                $time = $diffInYears . " Years ago";
                }
                }
                else if($diffInMonths != 0){
                if($diffInMonths == 1)
                {
                $time = $diffInMonths . " Month ago";
                }
                else{
                $time = $diffInMonths . " Months ago";
                }
                }
                else if($diffInDays != 0){
                if($diffInDays == 1){
                $time = $diffInDays . " Day ago";
                }
                else
                {
                $time = $diffInDays . " Days ago";
                }
                }
                else if($diffInHours != 0){
                if($diffInHours == 1){
                $time = $diffInHours . " hour ago";
                }
                else
                {
                $time = $diffInHours . " hours ago";
                }
                }
                else if($diffInMinutes != 0) {
                if($diffInMinutes == 1) {
                $time = $diffInMinutes . " Minute ago";
                }
                else
                {
                $time = $diffInMinutes . " Minutes ago";
                }
                }
                else
                {
                $time = "Just now";
                }

                @endphp

                <div class="activity-comment ps-5" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy"
                                src="http://localhost/wexprez_api/uploads/user_profile/{{$reply->profile_image}}"
                                alt="{{$reply->full_name}}" width="30px" height="30px">
                            <h5><a href="{{route('profileIxprez', $reply->user_name)}}">{{$reply->full_name}}</a></h5>
                        </div>
                        <small>{{ $time ?? '' }}</small>
                    </div>
                    <div class="comment-inner" id="comment-inner-{{$reply->id}}">
                        <p id="comment-content-{{$reply->id}}">{!! $reply->comment !!}</p>
                    </div>
                    <div class="comment-meta" id="comment-meta-{{$reply->id}}">

                        <div class="like">
                            <?php 
                            $up = 0;
                            $img = 'images/reaction/voteUp-off.png'; 
                        ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'up')
                            <?php $up++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteUp(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteUp-{{$reply->id}}"
                                onclick="commentVoteUpList(this)">{{$up}}</a>
                        </div>

                        <div class="dislike">

                            <?php 
                                $down = 0;
                                $img = 'images/reaction/voteDown-off.png'; 
                            ?>
                            @foreach($votes as $vote)
                            @if($vote->identifier_id == $reply->id)
                            @if($vote->identifier == 'comment')
                            @if($vote->type == 'down')
                            <?php $down++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="commentVoteDown(this)"><img loading="lazy"
                                    src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteDown-{{$reply->id}}"
                                onclick="commentVoteDownList(this)">{{$down}}</a>
                        </div>

                        <div class="real">
                            <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'real')
                            <?php $real++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentReal(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$reply->id}}"></button>
                            <a class="real-count" data-id="{{$reply->id}}" id="comment-real-{{$reply->id}}"
                                onclick="commentRealList(this)">{{$real}}</a>
                        </div>

                        <div class="fake">
                            <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                            @foreach($reacts as $react)
                            @if($react->identifier_id == $reply->id)
                            @if($react->identifier == 'comment')
                            @if($react->type == 'fake')
                            <?php $fake++ ?>
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="commentFake(this)">
                                <img loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$reply->id}}"></button>
                            <a class="fake-count" data-id="{{$reply->id}}" id="comment-fake-{{$reply->id}}"
                                onclick="commentFakeList(this)">{{$fake}}</a>
                        </div>
                        @if($profile->id == $comment->user_id)
                        <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button
                                parent-id="{{$comment->parent_id}}" data-id="{{$reply->id}}"
                                onclick="commentEdit(this)">Edit</button></div>
                        <div class="delete" id="delete-commentBtn-{{$reply->id}}"><button data-id="{{$reply->id}}"
                                onclick="commentDelete(this)">Delete</button></div>
                        @endif
                    </div>
                </div>
                <hr>
                @endif
                @endif
                @endforeach


                @endif
                @endif
                @endforeach


            </div>

        </div>
        <div class="text-center mb-4">
            <a href="{{route('activity')}}" class="btn btn-dark text-center">Show More</a>
        </div>
    </div>

    @endif

    <!-- Members -->
    <h2 class="text-center mt-5 mb-4">Xprezers</h2>
    <div class="row" id="member-list mb-5">
        @foreach($members as $member)
        <div class="col-md-6 col-xl-3 col-12">
            <div class="card member-card">
                <div class="card-head p-2">
                    <img loading="lazy"
                        src="http://localhost/wexprez_api/uploads/user_profile/{{$member->profile_image}}"
                        alt="{{$member->full_name}}" height="100%" width="100%" class="rounded-circle">
                </div>
                <div class="card-body">
                    <a href="{{route('profileIxprez', $member->user_name)}}">
                        <h3>{{$member->full_name}}</h3>
                    </a>
                    <span>Score: {{$member->score}}</span>
                    <div class="d-grid gap-2 mt-4">
                        <?php 
                        $fw = "Follow";
                        $blc = "Block";
                        ?>
                        @foreach($following as $follow)
                        @if($member->id == $follow->follow_id)
                        <?php $fw = "Unfollow" ?>
                        @endif
                        @endforeach
                        @foreach($blocking as $block)
                        @if($member->id == $block->block_id)
                        <?php $blc = "Unblock" ?>
                        @endif
                        @endforeach

                        <button class="btn btn-dark" value="{{$member->id}}" onclick="follow(this);"
                            id="follow-{{$member->id}}" data-id="{{$member->id}}"><?php echo $fw; ?></button>

                        <button class="btn btn-dark" value="{{$member->id}}" onclick="block(this);"
                            id="block-{{$member->id}}" data-id="{{$member->id}}"><?php echo $blc; ?></button>

                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center my-4">
        <a href="{{route('member')}}" class="btn btn-dark text-center">Show More</a>
    </div>


</section>
@endsection

@section('js')

<script>
    // alert close
    $(".alert").delay(3333).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endsection
