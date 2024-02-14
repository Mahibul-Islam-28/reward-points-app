@extends('layout.main')

@section('title')
Wexprez
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/activity.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<section class="acitivity-section">

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block w-50 mx-auto">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block w-50 mx-auto">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif

    <!-- Activity Form -->
    <div class="activity-form">
        <div class="input-area">
            <img src="{{asset('')}}{{$profile->profile_image}}" alt="{{$profile->full_name}}">
            <textarea name="content" id="activityContent" rows="5" required onkeyup="mention()"
                placeholder="Just Xprez, {{$profile->user_name}}!" maxlength="250"></textarea>
                
            <ul id="mention-list">
            </ul>
        </div>

        <div class="row">
            <div class="col-md-3 col-12 mt-2">
                <select name="emotion" id="emotion" required="required" >
                    <option disabled selected value="">Choose an Emotion</option>
                    <option value="Positive">Positive</option>
                    <option value="Negative">Negative</option>
                    <option value="Neutral">Neutral</option>
                </select>
            </div>
            <div class="col-md-3 col-12 mt-3">
                <input type="checkbox" name="anonymous" id="anonymous" value="1">
                <label for="anonymous">Post as Anonymous</label>
            </div>
            <div class="col-md-6 col-12 mt-3 text-md-end">
                <button class="btn btn-dark" onclick="activitySave()">Post Update</button>
            </div>
        </div>
    </div>

    <div class="row filter-nav mt-4">
        <div class="col-12 col-md-6">
            <nav class="navbar navbar-expand">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('activity')}}">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{route('wexprez')}}">WeXprez</a>
                    </li>
                </ul>
            </nav>

        </div>

        <div class="col-12 col-md-6">
            <div class="activity-filter">
                <div class="activity-filter-div">
                    <button class="post-filter" id="All" value="All" onclick="activityFilter(this)"><img
                            src="{{asset('')}}images/emotion/all.png"></button>
                    <span>All</span>
                </div>

                <div class="activity-filter-div">
                    <button class="post-filter" id="Positive" value="Positiive" onclick="activityFilter(this)"><img
                            src="{{asset('')}}images/emotion/positive.png"></button>
                    <span>Positive</span>
                </div>

                <div class="activity-filter-div">
                    <button class="post-filter" id="Negative" value="Negative" onclick="activityFilter(this)"><img
                            src="{{asset('')}}images/emotion/negative.png"></button>
                    <span>Negative</span>
                </div>

                <div class="activity-filter-div">
                    <button class="post-filter" id="Neutral" value="Neutral" onclick="activityFilter(this)"><img
                            src="{{asset('')}}images/emotion/neutral.png"></button>
                    <span>Neutral</span>
                </div>
            </div>
        </div>
    </div>



    <div class="activity-feed" id="activity-feed">

    @php $emotion = session()->get('emotion'); @endphp

    @foreach($activitys as $activity)
    @php
        if($activity->emotion == $emotion || $emotion == 'All'){ 
    @endphp

        <div class="activity-card" id="activity-card-{{$activity->id}}">

            @php
            //$date1 = date('Y-m-d H:i:s')->timezone('Asia/Dhaka');
            $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

            $date2 = new DateTime($activity->created_at, new DateTimeZone('Asia/Dhaka'));
            $interval = $date1->diff($date2);

            $diffInSeconds = $interval->s; //45
            $diffInMinutes = $interval->i; //23
            $diffInHours = $interval->h; //8
            $diffInDays = $interval->d; //21



            if($diffInDays != 0){
            $time = $diffInDays . " Days ago";
            }
            else if($diffInHours != 0){
            $time = $diffInHours . " hours & " . $diffInMinutes . " Minutes ago";
            }
            else if($diffInMinutes != 0) {
            $time = $diffInMinutes . " Minutes ago";
            }


            @endphp

            @if($activity->anonymous == 1)
                <div class="activity-top">
                    <div class="row">
                        <div class="profile-image col-3 col-md-1">
                            <img src="{{asset('')}}images/members/default-male.png" alt="default image" width="40px"
                                height="40px">
                        </div>

                        <div class="post-details col-9 col-md-11">
                            <h5>Anonymous</h5>
                            <p> posted an activity</p>
                            <small class="time"><a href="{{route('singleActivity', $activity->id)}}">
                            {{ $time ?? '' }}</a></small>
                        </div>
                    </div>
                </div>

                @else
                <div class="activity-top">
                    <div class="row">
                        <div class="profile-image col-3 col-md-1">
                            <img src="{{asset('')}}{{$activity->profile_image}}" alt="{{$activity->full_name}}"
                                width="40px" height="40px">
                        </div>

                        <div class="post-details col-9 col-md-11">
                            <h5><a
                                    href="{{route('profileIxprez', $activity->user_name)}}">{{$activity->full_name}}</a>
                            </h5>
                            <p> posted an activity</p>
                            <br>
                            <small class="time"><a href="{{route('singleActivity', $activity->id)}}">
                            {{ $time ?? '' }}</a></small>
                        </div>
                    </div>
                </div>
                @endif

            <div class="activity-inner py-4">
                <div class="row">
                    <div class="col-md-1 col-2">
                        @if($activity->emotion == 'Positive')
                        <img src="{{asset('')}}images/emotion/positive.png" alt="emotion" width="40px" height="40px">
                        @elseif($activity->emotion == 'Negative')
                        <img src="{{asset('')}}images/emotion/negative.png" alt="emotion" width="40px" height="40px">
                        @else
                        <img src="{{asset('')}}images/emotion/neutral.png" alt="emotion" width="40px" height="40px">
                        @endif
                    </div>
                    <div class="col-md-11 col-10">
                        <div class="activity-content" id="activity-{{$activity->id}}">
                            <a href="{{route('singleActivity', $activity->id)}}"
                                id="activity-content-{{$activity->id}}">
                                {!! $activity->content !!}
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="activity-meta" id="activity-meta-{{$activity->id}}">
                <div class="like">
                    <?php 
                        $up = 0;
                        $img = 'images/reaction/voteUp-off.png'; 
                    ?>
                    @foreach($votes as $vote)
                    @if($vote->identifier_id == $activity->id)
                    @if($vote->identifier == 'activity')
                    @if($vote->type == 'up')
                    <?php $up++ ?>
                    @if($profile->id == $vote->user_id)
                    <?php $img = 'images/reaction/voteUp-on.png'; ?>
                    @endif
                    @endif
                    @endif
                    @endif
                    @endforeach

                    <button data-id="{{$activity->id}}" onclick="activityVoteUp(this)">
                        <img src="{{asset('')}}{{$img}}" id="activity-voteUp-image-{{$activity->id}}"></button>
                    <a class="voteUp-count" data-id="{{$activity->id}}" id="activity-voteUp-{{$activity->id}}"
                        onclick="activityVoteUpList(this)">{{$up}}</a>
                </div>

                <div class="dislike">

                    <?php 
                        $down = 0;
                        $img = 'images/reaction/voteDown-off.png'; 
                    ?>
                    @foreach($votes as $vote)
                    @if($vote->identifier_id == $activity->id)
                    @if($vote->identifier == 'activity')
                    @if($vote->type == 'down')
                    @if($profile->id == $vote->user_id)
                    <?php $img = 'images/reaction/voteDown-on.png'; ?>
                    @endif
                    <?php $down++ ?>
                    @endif
                    @endif
                    @endif
                    @endforeach

                    <button data-id="{{$activity->id}}" onclick="activityVoteDown(this)"><img
                            src="{{asset('')}}{{$img}}" id="activity-voteDown-image-{{$activity->id}}"></button>
                    <a class="voteUp-count" data-id="{{$activity->id}}" id="activity-voteDown-{{$activity->id}}"
                        onclick="activityVoteDownList(this)">{{$down}}</a>
                </div>

                <div class="real">
                    <?php 
                        $real = 0;
                        $img = 'images/reaction/real-off.png';
                     ?>
                    @foreach($reacts as $react)
                    @if($react->identifier_id == $activity->id)
                    @if($vote->identifier == 'activity')
                    @if($react->type == 'real')
                    <?php $real++ ?>
                    @if($profile->id == $vote->user_id)
                    <?php $img = 'images/reaction/real-on.png'; ?>
                    @endif
                    @endif
                    @endif
                    @endif
                    @endforeach
                    <button data-id="{{$activity->id}}" onclick="activityReal(this)">
                        <img src="{{asset('')}}{{$img}}" id="activity-real-image-{{$activity->id}}"></button>
                    <a class="real-count" data-id="{{$activity->id}}" id="activity-real-{{$activity->id}}"
                    onclick="activityRealList(this)">{{$real}}</a>
                </div>

                <div class="fake">
                    <?php 
                        $fake = 0;
                        $img = 'images/reaction/fake-off.png';
                     ?>
                    @foreach($reacts as $react)
                    @if($react->identifier_id == $activity->id)
                    @if($vote->identifier == 'activity')
                    @if($react->type == 'fake')
                    <?php $fake++ ?>
                    @if($profile->id == $vote->user_id)
                    <?php $img = 'images/reaction/fake-on.png'; ?>
                    @endif
                    @endif
                    @endif
                    @endif
                    @endforeach
                    <button data-id="{{$activity->id}}" onclick="activityFake(this)">
                        <img src="{{asset('')}}{{$img}}" id="activity-fake-image-{{$activity->id}}"></button>
                    <a class="fake-count" data-id="{{$activity->id}}" id="activity-fake-{{$activity->id}}"
                    onclick="activityFakeList(this)">{{$fake}}</a>
                </div>
                <div class="comment" id="comment-{{$activity->id}}"><button parent-id="0" data-id="{{$activity->id}}"
                        onclick="commentCreate(this)">Comment</button></div>
                @if($profile->id == $activity->user_id)
                <div class="edit" id="edit-{{$activity->id}}"><button data-id="{{$activity->id}}"
                        onclick="activityEdit(this)">Edit</button></div>
                <div class="delete" id="delete-{{$activity->id}}"><button data-id="{{$activity->id}}"
                        onclick="activityDelete(this)">Delete</button></div>
                @endif
                <div class="report" id="report-{{$activity->id}}"><button data-id="{{$activity->id}}"
                        onclick="activityReport(this)">Report</button></div>
                        
                <!-- <div class="share" id=""><a href="https://www.facebook.com/sharer/sharer.php?u={{route('singleActivity', $activity->id)}}&display=popup">
                        <i class="fa-solid fa-share"></i> Share</a></div> -->
                <div class="fb-share-button" data-href="{{route('singleActivity', $activity->id)}}"
                    data-layout="button_count">
                </div>

            </div>
            <hr>
            <div class="comment-form" id="comment-form-{{$activity->id}}"></div>
            <div id="report-form-{{$activity->id}}"></div>


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



            if($diffInDays != 0){
            $time = $diffInDays . " Days ago";
            }
            else if($diffInHours != 0){
            $time = $diffInHours . " hours & " . $diffInMinutes . " Minutes ago";
            }
            else if($diffInMinutes != 0) {
            $time = $diffInMinutes . " Minutes ago";
            }


            @endphp


            @if($comment->activity_id == $activity->id)
            @if($comment->parent_id == 0)
            <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                <div class="comment-header">
                    <img src="{{asset('')}}{{$comment->profile_image}}" alt="{{$comment->full_name}}" width="30px"
                        height="30px">
                    <h5><a href="{{route('profileIxprez', $comment->user_name)}}">{{$comment->full_name}}</a></h5>
                    <p>{{ $time ?? '' }}</p>
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
                            <img src="{{asset('')}}{{$img}}" id="comment-voteUp-image-{{$comment->id}}"></button>
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
                                src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$comment->id}}"></button>
                        <span class="voteUp-count" id="comment-voteDown-{{$comment->id}}">{{$down}}</span>
                    </div>

                    <div class="real">
                        <?php 
                        $real = 0;
                        $img = 'images/reaction/real-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier_id == $comment->id)
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
                        <button data-id="{{$comment->id}}" onclick="commentReal(this)">
                            <img src="{{asset('')}}{{$img}}" id="comment-real-image-{{$comment->id}}"></button>
                        <span class="real-count" id="comment-real-{{$comment->id}}">{{$real}}</span>
                    </div>

                    <div class="fake">
                        <?php 
                        $fake = 0;
                        $img = 'images/reaction/fake-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier_id == $comment->id)
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
                        <button data-id="{{$comment->id}}" onclick="commentFake(this)">
                            <img src="{{asset('')}}{{$img}}" id="comment-fake-image-{{$comment->id}}"></button>
                        <span class="fake-count" id="comment-fake-{{$comment->id}}">{{$fake}}</span>
                    </div>

                    <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                            data-id="{{$activity->id}}" onclick="replyCreate(this)">Reply</button></div>
                    @if($profile->id == $comment->user_id)
                    <div class="edit" id="edit-commentBtn-{{$comment->id}}"><button parent-id="{{$comment->parent_id}}"
                            data-id="{{$comment->id}}" onclick="commentEdit(this)">Edit</button></div>
                    <div class="delete" id="delete-commentBtn-{{$comment->id}}"><button data-id="{{$comment->id}}"
                            onclick="commentDelete(this)">Delete</button></div>
                    @endif

                    @if($profile->id != $comment->user_id)
                    <div class="comment-report" id="commentReport-{{$comment->id}}"><button data-id="{{$comment->id}}"
                        activity-id="{{$comment->activity_id}}" onclick="commentReport(this)">Report</button></div>
                    @endif


                </div>
                <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                <div id="comment-report-form-{{$comment->id}}" class="my-3"></div>

            </div>

            @foreach($comments as $reply)
            @if($reply->activity_id == $activity->id)
            @if($reply->parent_id == $comment->id)

            @php
            $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

            $date2 = new DateTime($reply->created_at, new DateTimeZone('Asia/Dhaka'));
            $interval = $date1->diff($date2);

            $diffInSeconds = $interval->s; //45
            $diffInMinutes = $interval->i; //23
            $diffInHours = $interval->h; //8
            $diffInDays = $interval->d; //21



            if($diffInDays != 0){
            $time = $diffInDays . " Days ago";
            }
            else if($diffInHours != 0){
            $time = $diffInHours . " hours & " . $diffInMinutes . " Minutes ago";
            }
            else if($diffInMinutes != 0) {
            $time = $diffInMinutes . " Minutes ago";
            }

            @endphp

            <div class="activity-comment ps-5" id="activity-comment-{{$comment->id}}">

                <div class="comment-header">
                    <img src="{{asset('')}}{{$reply->profile_image}}" alt="{{$reply->full_name}}" width="30px"
                        height="30px">
                    <h5><a href="{{route('profileIxprez', $reply->user_name)}}">{{$reply->full_name}}</a></h5>
                    <p>{{$time}}</p>
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
                            <img src="{{asset('')}}{{$img}}" id="comment-voteUp-image-{{$reply->id}}"></button>
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

                        <button data-id="{{$reply->id}}" onclick="commentVoteDown(this)"><img
                                src="{{asset('')}}{{$img}}" id="comment-voteDown-image-{{$reply->id}}"></button>
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
                            <img src="{{asset('')}}{{$img}}" id="comment-real-image-{{$reply->id}}"></button>
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
                            <img src="{{asset('')}}{{$img}}" id="comment-fake-image-{{$reply->id}}"></button>
                        <span class="fake-count" id="comment-fake-{{$reply->id}}">{{$fake}}</span>
                    </div>
                    @if($profile->id == $reply->user_id)
                    <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button parent-id="{{$comment->parent_id}}"
                            data-id="{{$reply->id}}" onclick="commentEdit(this)">Edit</button></div>
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
        @php } @endphp
        @endforeach
        @php session()->put('emotion', 'All'); @endphp


    </div>

</section>
@endsection
