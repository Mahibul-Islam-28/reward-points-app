@extends('layout.main')

@section('title')
Search Results
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/search.css">
@endsection

@section('content')
<section class="search-section">
    <div class="container py-5">

        @if(count($commentActivitys) > 0)
        <nav class="navbar navbar-expand">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('search', $searchValue)}}">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('xprezerSearch', $searchValue)}}">Xprezers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('activitySearch', $searchValue)}}">Activity</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('commentSearch', $searchValue)}}">Comment</a>
                </li>
            </ul>
        </nav>

        <!-- Comment -->
        <div class="comment-result">
            <div class="row">

                <div class="activity-feed">

                    @foreach($commentActivitys as $key => $value)
                    @if (isset($value[$key]))
                    <div class="activity-card">

                        <?php
                            $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                            $date2 = new DateTime($value[$key]->created_at, new DateTimeZone('Asia/Dhaka'));
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
                            if(isset($value[$key]->preview_url))
                            {
                                $og_details = getSiteOG($value[$key]->preview_url);
                            }

                            // image
                            $img = json_decode($value[$key]->images);
                            $total = 0;
                            $counter = 1;
                            $images = array();
                            if ($value[$key]->images != null) {
                                $image = json_decode($value[$key]->images);
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

                        @if($value[$key]->anonymous == 1)
                        <div class="activity-top">
                            <div class="profile-image">
                                <img loading="lazy" src="{{asset('')}}images/members/default-male.png"
                                    alt="default image" width="40px" height="40px">
                                <h5>Anonymous</h5>
                                <p> posted an activity</p>
                            </div>
                            <small class="time"><a href="{{route('singleActivity', $value[$key]->id)}}">
                                    {{ $time ?? '' }}</a></small>
                        </div>

                        @else
                        <div class="activity-top">
                            <div class="profile-image">
                                <img loading="lazy" src="{{asset('')}}{{$value[$key]->profile_image}}"
                                    alt="{{$value[$key]->full_name}}" width="40px" height="40px">
                                <h5><a href="{{route('profileIxprez', $value[$key]->user_name)}}">{{$value[$key]->full_name}}
                                    </a>
                                </h5>
                                <p> posted an activity</p>
                            </div>
                            <small class="time"><a href="{{route('singleActivity', $value[$key]->id)}}">
                                    {{ $time ?? '' }}</a></small>
                        </div>
                        @endif

                        <div class="activity-inner py-4">
                            <div class="activity-reaction">
                                @if($value[$key]->emotion == 'Positive')
                                <img loading="lazy" src="{{asset('')}}images/emotion/positive.png" alt="emotion"
                                    width="40px" height="40px">
                                @elseif($value[$key]->emotion == 'Negative')
                                <img loading="lazy" src="{{asset('')}}images/emotion/negative.png" alt="emotion"
                                    width="40px" height="40px">
                                @else
                                <img loading="lazy" src="{{asset('')}}images/emotion/neutral.png" alt="emotion"
                                    width="40px" height="40px">
                                @endif
                            </div>
                            <div class="activity-content" id="activity-{{$value[$key]->id}}">
                                <p>{!! $value[$key]->content !!}</p>

                                @if($value[$key]->images)
                                <div class="gallery" id="activity-image-{{$value[$key]->id}}">

                                    @if($images)
                                    @foreach($images as $index => $item)
                                    @if($total > 4)
                                    @if($counter == 4)
                                    <div class="see-more-img">
                                        <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img
                                                loading="lazy"
                                                src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                        <div class="text-block">{{$total - 4}}+</div>
                                    </div>
                                    @elseif($counter > 4)
                                    <div class="d-none">
                                        <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img
                                                loading="lazy"
                                                src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                    </div>
                                    @else
                                    <div class="">
                                        <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img
                                                loading="lazy"
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
                                    data-link="<?php echo $value[$key]->preview_url ?>">
                                    <?php if(isset($og_details['image'])){ ?>
                                    <img loading="lazy" src="<?php echo @$og_details['image'];?>" width="100%"
                                        height="100%">
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

                        <div class="activity-meta" id="activity-meta-{{$value[$key]->id}}">
                            <div class="like">
                                <?php 
                        $up = 0;
                        $img = 'images/reaction/voteUp-off.png'; 
                        ?>
                                @foreach($votes as $vote)
                                @if($vote->identifier == 'activity')
                                @if($vote->identifier_id == $value[$key]->id)
                                @if($vote->type == 'up')
                                <?php $up++ ?>
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/voteUp-on.png'; ?>
                                @endif
                                @endif
                                @endif
                                @endif
                                @endforeach

                                <button data-id="{{$value[$key]->id}}" onclick="activityVoteUp(this)">
                                    <img loading="lazy" src="{{asset('')}}{{$img}}"
                                        id="activity-voteUp-image-{{$value[$key]->id}}"></button>
                                <a class="voteUp-count" data-id="{{$value[$key]->id}}"
                                    id="activity-voteUp-{{$value[$key]->id}}"
                                    onclick="activityVoteUpList(this)">{{$up}}</a>
                            </div>

                            <div class="dislike">

                                <?php 
                        $down = 0;
                        $img = 'images/reaction/voteDown-off.png'; 
                    ?>
                                @foreach($votes as $vote)
                                @if($vote->identifier == 'activity')
                                @if($vote->identifier_id == $value[$key]->id)
                                @if($vote->type == 'down')
                                <?php $down++ ?>
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/voteDown-on.png'; ?>
                                @endif
                                @endif
                                @endif
                                @endif
                                @endforeach

                                <button data-id="{{$value[$key]->id}}" onclick="activityVoteDown(this)"><img
                                        loading="lazy" src="{{asset('')}}{{$img}}"
                                        id="activity-voteDown-image-{{$value[$key]->id}}"></button>
                                <a class="voteUp-count" data-id="{{$value[$key]->id}}"
                                    id="activity-voteDown-{{$value[$key]->id}}"
                                    onclick="activityVoteDownList(this)">{{$down}}</a>
                            </div>

                            <div class="real">
                                <?php 
                        $real = 0;
                        $img = 'images/reaction/real-off.png';
                     ?>
                                @foreach($reacts as $react)
                                @if($react->identifier == 'activity')
                                @if($react->identifier_id == $value[$key]->id)
                                @if($react->type == 'real')
                                <?php $real++ ?>
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/real-on.png'; ?>
                                @endif
                                @endif
                                @endif
                                @endif
                                @endforeach
                                <button data-id="{{$value[$key]->id}}" onclick="activityReal(this)">
                                    <img loading="lazy" src="{{asset('')}}{{$img}}"
                                        id="activity-real-image-{{$value[$key]->id}}"></button>
                                <a class="real-count" data-id="{{$value[$key]->id}}"
                                    id="activity-real-{{$value[$key]->id}}"
                                    onclick="activityRealList(this)">{{$real}}</a>
                            </div>

                            <div class="fake">
                                <?php 
                        $fake = 0;
                        $img = 'images/reaction/fake-off.png';
                     ?>
                                @foreach($reacts as $react)
                                @if($react->identifier == 'activity')
                                @if($react->identifier_id == $value[$key]->id)
                                @if($react->type == 'fake')
                                <?php $fake++ ?>
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/fake-on.png'; ?>
                                @endif
                                @endif
                                @endif
                                @endif
                                @endforeach
                                <button data-id="{{$value[$key]->id}}" onclick="activityFake(this)">
                                    <img loading="lazy" src="{{asset('')}}{{$img}}"
                                        id="activity-fake-image-{{$value[$key]->id}}"></button>
                                <a class="fake-count" data-id="{{$value[$key]->id}}"
                                    id="activity-fake-{{$value[$key]->id}}"
                                    onclick="activityFakeList(this)">{{$fake}}</a>
                            </div>
                            <div class="comment" id="comment-{{$value[$key]->id}}"><button parent-id="0"
                                    data-id="{{$value[$key]->id}}" onclick="commentCreate(this)">Comment</button></div>
                            @if($profile->id == $value[$key]->user_id)
                            <div class="edit" id="edit-{{$value[$key]->id}}"><button data-id="{{$value[$key]->id}}"
                                    onclick="activityEdit(this)">Edit</button></div>
                            <div class="delete" id="hide-{{$value[$key]->id}}"><button data-id="{{$value[$key]->id}}"
                                    id="hideBtn-{{$value[$key]->id}}" onclick="activityHide(this)">Hide</button></div>
                            <div class="delete" id="delete-{{$value[$key]->id}}"><button data-id="{{$value[$key]->id}}"
                                    onclick="activityDelete(this)">Delete</button></div>
                            @endif
                            <div class="report" id="report-{{$value[$key]->id}}"><button data-id="{{$value[$key]->id}}"
                                    onclick="activityReport(this)">Report</button></div>

                            <div class="share" id="share-{{$value[$key]->id}}">
                                <button data-id="{{$value[$key]->id}}" onclick="share(this)"> <i
                                        class="fa-solid fa-share"></i> Share</button>

                                <section id="share-option-{{$value[$key]->id}}" class="share-option">
                                    <a target="_blank" class="wexprez" onclick="activityShare({{$value[$key]->id}})">
                                        <img src="{{asset('')}}images/logo/icon.png" alt="img"> wexprez</a>

                                    <a target="_blank" class="facebook"
                                        href="https://www.facebook.com/dialog/feed?app_id=350389333934305&display=popup&link={{route('singleActivity', $value[$key]->id)}}">
                                        <i class="fa-brands fa-facebook"></i> facebook</a>

                                    <a target="_blank" class="twiteer"
                                        href="https://twitter.com/intent/tweet?url={{route('singleActivity', $value[$key]->id)}}&text={!! $value[$key]->content !!}">
                                        <img src="{{asset('')}}images/logo/x.png" alt="img"> X</a>

                                    <a target="_blank" class="linkedin"
                                        href="https://www.linkedin.com/shareArticle?url={{route('singleActivity', $value[$key]->id)}}">
                                        <i class="fa-brands fa-linkedin"></i> linkedin</a>

                                    <a target="_blank" class="pinterest"
                                        href="//pinterest.com/pin/create/link/?url={{route('singleActivity', $value[$key]->id)}}&media=https://wexprez.com/images/fb-meta.png&description={!! $value[$key]->content !!}">
                                        <i class="fa-brands fa-pinterest"></i> pinterest</a>
                                </section>
                            </div>

                        </div>
                        <hr>

                        <div class="comment-form" id="comment-form-{{$value[$key]->id}}"></div>
                        <div id="report-form-{{$value[$key]->id}}"></div>


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


                        @if($comment->activity_id == $value[$key]->id)
                        @if($comment->parent_id == 0)
                        <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                            <div class="comment-header">
                                <div class="comment-info">
                                    <img loading="lazy" src="{{asset('')}}{{$comment->profile_image}}"
                                        alt="{{$comment->full_name}}" width="30px" height="30px">
                                    <h5><a
                                            href="{{route('profileIxprez', $comment->user_name)}}">{{$comment->full_name}}</a>
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
                                    <span class="voteUp-count" id="comment-voteUp-{{$comment->id}}">{{$up}}</span>
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

                                    <button data-id="{{$comment->id}}" onclick="commentVoteDown(this)"><img
                                            loading="lazy" src="{{asset('')}}{{$img}}"
                                            id="comment-voteDown-image-{{$comment->id}}"></button>
                                    <span class="voteUp-count" id="comment-voteDown-{{$comment->id}}">{{$down}}</span>
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
                                    <span class="real-count" id="comment-real-{{$comment->id}}">{{$real}}</span>
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
                                    <span class="fake-count" id="comment-fake-{{$comment->id}}">{{$fake}}</span>
                                </div>

                                <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                                        data-id="{{$value[$key]->id}}" onclick="replyCreate(this)">Reply</button></div>
                                @if($profile->id == $comment->user_id)
                                <div class="edit" id="edit-commentBtn-{{$comment->id}}"><button
                                        parent-id="{{$comment->parent_id}}" data-id="{{$comment->id}}"
                                        onclick="commentEdit(this)">Edit</button></div>
                                <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button
                                        data-id="{{$comment->id}}" onclick="commentDelete(this)">Delete</button></div>
                                @endif
                            </div>
                            <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                        </div>
                        <hr>

                        @foreach($comments as $reply)
                        @if($reply->activity_id == $value[$key]->id)
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
                                    <img loading="lazy" src="{{asset('')}}{{$reply->profile_image}}"
                                        alt="{{$reply->full_name}}" width="30px" height="30px">
                                    <h5><a
                                            href="{{route('profileIxprez', $reply->user_name)}}">{{$reply->full_name}}</a>
                                    </h5>
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
                                    <span class="voteUp-count" id="comment-voteUp-{{$reply->id}}">{{$up}}</span>
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
                                            src="{{asset('')}}{{$img}}"
                                            id="comment-voteDown-image-{{$reply->id}}"></button>
                                    <span class="voteUp-count" id="comment-voteDown-{{$reply->id}}">{{$down}}</span>
                                </div>

                                <div class="real">
                                    <?php 
                                $real = 0;
                                $img = 'images/reaction/real-off.png';
                            ?>
                                    @foreach($reacts as $react)
                                    @if($react->identifier_id == $reply->id)
                                    @if($vote->identifier == 'comment')
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
                                    <span class="real-count" id="comment-real-{{$reply->id}}">{{$real}}</span>
                                </div>

                                <div class="fake">
                                    <?php 
                                $fake = 0;
                                $img = 'images/reaction/fake-off.png';
                            ?>
                                    @foreach($reacts as $react)
                                    @if($react->identifier_id == $reply->id)
                                    @if($vote->identifier == 'comment')
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
                                    <span class="fake-count" id="comment-fake-{{$reply->id}}">{{$fake}}</span>
                                </div>
                                @if($profile->id == $comment->user_id)
                                <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button
                                        parent-id="{{$comment->parent_id}}" data-id="{{$reply->id}}"
                                        onclick="commentEdit(this)">Edit</button></div>
                                <div class="delete" id="delete-commentBtn-{{$reply->id}}"><button
                                        data-id="{{$reply->id}}" onclick="commentDelete(this)">Delete</button></div>
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
                    @endif
                    @endforeach
                    

                </div>

                @else
                <h3 class="text-center py-4">No Comment Found!</h3>
                @endif
            </div>

</section>
@endsection
