@extends('layout.main')

@section('title')
Profile - Ixprez
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/profile.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@php 
if(session()->get('page') != 'ixprez' ){
    session()->put('page', 'ixprez');
    session()->put('emotion', 'All');
    session()->put('dateFilter', 'All');
}
@endphp

@section('content')

@if($myProfile != null)
<section class="ixprez-section">
    <div class="cover-image">
        <img loading="lazy" loading="lazy" src="{{$myProfile->cover_image}}" alt="{{$myProfile->full_name}}">
        @if(isset($profile))
            @if($profile->id == $myProfile->id)
            <a href="{{route('coverImage', $myProfile->user_name)}}" title="Change Cover Image" id="coverimage-edit"><i
                class="fa-solid fa-pen-to-square"></i></a>
            @endif
        @endif
    </div>
    <div class="name-section">
        <div class="row">
            <div class="col-12 col-lg-4 col-xl-3 profile-image">
                <img loading="lazy" loading="lazy"
                    src="http://localhost:8080/wexprez_api/uploads/user_profile/{{$myProfile->profile_image}}"
                    alt="{{$myProfile->full_name}}">
            </div>
            <div class="col-12 col-lg-5 col-xl-6 profile-name">
                <h2>{{$myProfile->full_name}}</h2>
                <!-- <small class="text-muted">Active 3 Days Ago.</small> -->

                @if(isset($profile))
                @if($profile->id != $myProfile->id)
                <?php 
                        $fw = "Follow";
                        $blc = "Block";
                        $rp = "Report";
                        ?>
                @foreach($following as $follow)
                @if($myProfile->id == $follow->follow_id)
                <?php $fw = "Unfollow" ?>
                @endif
                @endforeach
                @foreach($blocking as $block)
                @if($myProfile->id == $block->block_id)
                <?php $blc = "Unblock" ?>
                @endif
                @endforeach

                @foreach($reporting as $report)
                @if($myProfile->id == $report->report_id)
                <?php $rp = "Reported" ?>
                @endif
                @endforeach

                <div class="button-group">
                    <button class="btn btn-dark" value="{{$myProfile->id}}" onclick="follow(this);"
                        id="follow-{{$myProfile->id}}" data-id="{{$myProfile->id}}"><?php echo $fw; ?></button>

                    <button class="btn btn-dark" value="{{$myProfile->id}}" onclick="block(this);"
                        id="block-{{$myProfile->id}}" data-id="{{$myProfile->id}}"><?php echo $blc; ?></button>

                    <button class="btn btn-dark" value="{{$myProfile->id}}" onclick="report(this);"
                        id="report-{{$myProfile->id}}" data-id="{{$myProfile->id}}"><?php echo $rp; ?></button>
                </div>
                @endif

                <div class="like-section">
                    <?php
                        $up = 0;
                        $img = 'images/reaction/voteUp-off.png'
                    ?>

                    @foreach($memberVotes as $mv)
                    @if($mv->vote_id == $myProfile->id)
                    @if($mv->type == 'up')
                    <?php $up++ ?>
                    @if($profile->id == $mv->user_id)
                    <?php $img = 'images/reaction/voteUp-on.png' ?>
                    @endif
                    @endif
                    @endif
                    @endforeach

                    <button onclick="voteUpMember(this)" vote-id="{{$myProfile->id}}" user-id="{{$profile->id}}">
                        <img loading="lazy" loading="lazy" id="member-voteUp-{{$myProfile->id}}"
                            src="{{asset('')}}{{$img}}" alt="voteUp" height="20px" width="30px">
                    </button>
                    <a class="voteUp-count" id="member-voteUp-count-{{$myProfile->id}}"
                        onclick="memberVoteUpList({{$myProfile->id}})">{{$up}}</a>

                    <?php
                        $down = 0;
                        $img = 'images/reaction/voteDown-off.png'
                    ?>
                    @foreach($memberVotes as $mv)
                    @if($mv->vote_id == $myProfile->id)
                    @if($mv->type == 'down')
                    <?php $down++ ?>
                    @if($profile->id == $mv->user_id)
                    <?php $img = 'images/reaction/voteDown-on.png' ?>
                    @endif
                    @endif
                    @endif
                    @endforeach
                    <button onclick="voteDownMember(this)" vote-id="{{$myProfile->id}}" user-id="{{$profile->id}}">
                        <img loading="lazy" loading="lazy" id="member-voteDown-{{$myProfile->id}}"
                            src="{{asset('')}}{{$img}}" alt="voteUp" height="20px" width="30px">
                    </button>
                    <a class="voteUp-count" id="member-voteDown-count-{{$myProfile->id}}"
                        onclick="memberVoteDownList({{$myProfile->id}})">{{$down}}</a>
                </div>
                @endif
            </div>

            <div class="col-12 col-lg-3 col-xl-3">
                <div class="score-section" id="score-card">
                    <div class="score-text">
                        <span>{{$myProfile->score}}</span>
                    </div>
                    <h5><a onclick="@if(isset($profile)) scoreDetails({{$myProfile->id}}) @else guestUser() @endif" class="score-btn">Score</a></h5>
                </div>
            </div>

        </div>
    </div>

    <div class="acitivity-section">
        <nav class="navbar navbar-expand">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('profileIxprez', $myProfile->user_name)}}">IXprez</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profileWexprez', $myProfile->user_name)}}">WeXprez</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profileFollowing', $myProfile->user_name)}}">Following</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profileFollower', $myProfile->user_name)}}">Follower</a>
                </li>
                @if(isset($profile))
                    @if($profile->user_name == $myProfile->user_name)
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('profileArchive', $myProfile->user_name)}}">Archive</a>
                    </li>
                    @endif
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profile', $myProfile->user_name)}}">Profile</a>
                </li>
            </ul>
        </nav>

        @if(isset($profile))
            @if($profile->user_name == $myProfile->user_name)
            <!-- Activity Form -->
            <div class="activity-form">
                <form method="post" id="activity-form" enctype="multipart/form-data">
                    @csrf
                    <div class="input-area">
                        <img loading="lazy" loading="lazy"
                            src="http://localhost/wexprez_api/uploads/user_profile/{{$profile->profile_image}}"
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
                            <input type="checkbox" name="anonymous" @if($anonymous) disabled @endif id="anonymous"
                                value="1">
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
            @endif
        @endif

        <div class="row filter-nav mt-4">
            <div class="col-12 col-lg-6">
                <div class="date-filter">
                    <label for="date-filter">Search by Date:</label>
                    <input type="date" id="date-filter" onkeydown="return false" data-date-inline-picker="true"
                        name="date-filter" min="2022-01-01" onchange="dateFilter()">
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="activity-filter">
                    <div class="activity-filter-div">
                        <button class="post-filter" id="All" value="All" onclick="@if(isset($profile)) activityFilter(this) @else guestUser() @endif"><img
                                loading="lazy" loading="lazy" src="{{asset('')}}images/emotion/all.png"></button>
                    </div>
                    <div class="activity-filter-div">
                        <button class="post-filter" id="Positive" value="Positive" onclick="@if(isset($profile)) activityFilter(this) @else guestUser() @endif"><img
                                loading="lazy" loading="lazy" src="{{asset('')}}images/emotion/positive.png"></button>
                    </div>
                    <div class="activity-filter-div">
                        <button class="post-filter" id="Negative" value="Negative" onclick="@if(isset($profile)) activityFilter(this) @else guestUser() @endif"><img
                                loading="lazy" loading="lazy" src="{{asset('')}}images/emotion/negative.png"></button>
                    </div>
                    <div class="activity-filter-div">
                        <button class="post-filter" id="Neutral" value="Neutral" onclick="@if(isset($profile)) activityFilter(this) @else guestUser() @endif"><img
                                loading="lazy" loading="lazy" src="{{asset('')}}images/emotion/neutral.png"></button>
                    </div>
                    <div class="activity-filter-text">
                        <span>All</span>
                        <span>Positive</span>
                        <span>Negative</span>
                        <span>Neutral</span>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="activity-feed" id="activity-feed">

            @php 
            $emotion = session()->get('emotion');
            $dateFilter = session()->get('dateFilter');
            @endphp

            @php
            $activities = array ();
            if($dateFilter != 'All' || $emotion != 'All'){
            $activities = $allActivity;
            }
            else{
            $activities = $activitys;
            }

            @endphp

            @foreach($activities as $activity)

            @php
            $date = date('Y-m-d', strtotime($activity->created_at));
            if($dateFilter == $date || $dateFilter == 'All'){
            if($activity->emotion == $emotion || $emotion == 'All'){
            @endphp


            <div class="activity-card" id="activity-card-{{$activity->id}}">

                <?php
                $date1 = new DateTime("now", new DateTimeZone('Asia/Dhaka') );

                $date2 = new DateTime($activity->created_at, new DateTimeZone('Asia/Dhaka'));
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
                if(isset($activity->preview_url))
                {
                    $og_details = getSiteOG($activity->preview_url);
                }

                // image
                $img = json_decode($activity->images);
                $total = 0;
                $counter = 1;
                $images = array();
                if ($activity->images != null) {
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
                }

                foreach ($images as $val) {
                    $total += 1;
                }

            ?>

                @if($activity->anonymous == 1)
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy" loading="lazy" src="{{asset('')}}images/members/default-male.png"
                            alt="default image" width="40px" height="40px">
                        <h5>Anonymous</h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $activity->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>

                @else
                <div class="activity-top">
                    <div class="profile-image">
                        <img loading="lazy" loading="lazy"
                            src="http://localhost/wexprez_api/uploads/user_profile/{{$activity->profile_image}}"
                            alt="{{$activity->full_name}}" width="40px" height="40px">
                        <h5><a href="{{route('profileIxprez', $activity->user_name)}}">{{$activity->full_name}} </a>
                        </h5>
                        <p> posted an activity</p>
                    </div>
                    <small class="time"><a href="{{route('singleActivity', $activity->id)}}">
                            {{ $time ?? '' }}</a></small>
                </div>
                @endif

                <div class="activity-inner py-4">
                    <div class="activity-reaction">
                        @if($activity->emotion == 'Positive')
                        <img loading="lazy" loading="lazy" src="{{asset('')}}images/emotion/positive.png" alt="emotion"
                            width="40px" height="40px">
                        @elseif($activity->emotion == 'Negative')
                        <img loading="lazy" loading="lazy" src="{{asset('')}}images/emotion/negative.png" alt="emotion"
                            width="40px" height="40px">
                        @else
                        <img loading="lazy" loading="lazy" src="{{asset('')}}images/emotion/neutral.png" alt="emotion"
                            width="40px" height="40px">
                        @endif
                    </div>
                    <div class="activity-content" id="activity-{{$activity->id}}">
                        <p id="activity-content-{{$activity->id}}">{!! $activity->content !!}</p>

                        @if($activity->images)
                        <div class="gallery" id="activity-image-{{$activity->id}}">

                            @if($images)
                            @foreach($images as $index => $item)
                            @if($total > 4)
                            @if($counter == 4)
                            <div class="see-more-img">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy" src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                                <div class="text-block">{{$total - 4}}+</div>
                            </div>
                            @elseif($counter > 4)
                            <div class="d-none">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy" src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @else
                            <div class="">
                                <a href="http://localhost/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy" loading="lazy" src="http://localhost/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @endif
                            @else
                            @if($total > 1)
                            <div class="">
                                <a href="http://localhost:8080/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy" src="http://localhost:8080/wexprez_api/uploads/activity/{{$item}}"></a>
                            </div>
                            @else
                                @if($total > 1)
                                <div class="">
                                    <a href="http://localhost:8080/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy" src="http://localhost:8080/wexprez_api/uploads/activity/{{$item}}"></a>
                                </div>
                                    @else
                                    <div  class="single-img">
                                    <a href="http://localhost:8080/wexprez_api/uploads/activity/{{$item}}"><img loading="lazy" src="http://localhost:8080/wexprez_api/uploads/activity/{{$item}}"></a>
                                </div>
                                @endif
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
                        <div class="debugBox" id="debugBox-{{$activity->id}}">
                            <button id="x" data-id="{{$activity->id}}" onclick="closePreview(this)">
                                X
                            </button>
                            <div class="debug-box" onclick="openLink(this);"
                                data-link="<?php echo $activity->preview_url ?>">

                                <?php if(isset($og_details['image'])){ ?>
                                <img loading="lazy" loading="lazy" src="<?php echo @$og_details['image'];?>"
                                    width="100%" height="100%">
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
                        </div>
                        <?php } } } 
                            $og_details = null;
                        ?>

                    </div>
                </div>

                <div class="activity-meta" id="activity-meta-{{$activity->id}}">
                    <div class="like">
                        <?php 
                        $up = 0;
                        $img = 'images/reaction/voteUp-off.png'; 
                    ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $activity->id)
                        @if($vote->type == 'up')
                        <?php $up++ ?>
                        @if(isset($profile))
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteUp-on.png'; ?>
                            @endif
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$activity->id}}" onclick="@if(isset($profile)) activityVoteUp(this) @else guestUser() @endif">
                            <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-voteUp-image-{{$activity->id}}"></button>
                        <a class="voteUp-count" data-id="{{$activity->id}}" id="activity-voteUp-{{$activity->id}}"
                            onclick="@if(isset($profile)) activityVoteUpList(this) @else guestUser() @endif">{{$up}}</a>
                    </div>

                    <div class="dislike">

                        <?php 
                        $down = 0;
                        $img = 'images/reaction/voteDown-off.png'; 
                    ?>
                        @foreach($votes as $vote)
                        @if($vote->identifier == 'activity')
                        @if($vote->identifier_id == $activity->id)
                        @if($vote->type == 'down')
                        @if(isset($profile))
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/voteDown-on.png'; ?>
                            @endif
                        @endif
                        <?php $down++ ?>
                        @endif
                        @endif
                        @endif
                        @endforeach

                        <button data-id="{{$activity->id}}" onclick="@if(isset($profile)) activityVoteDown(this) @else guestUser() @endif"><img loading="lazy"
                                loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-voteDown-image-{{$activity->id}}"></button>
                        <a class="voteUp-count" data-id="{{$activity->id}}" id="activity-voteDown-{{$activity->id}}"
                            onclick="@if(isset($profile)) activityVoteDownList(this)" @else guestUser() @endif >{{$down}}</a>
                    </div>

                    <div class="real">
                        <?php 
                        $real = 0;
                        $img = 'images/reaction/real-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $activity->id)
                        @if($react->type == 'real')
                        <?php $real++ ?>
                        @if(isset($profile))
                            @if($profile->id == $vote->user_id)
                            <?php $img = 'images/reaction/real-on.png'; ?>
                            @endif
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$activity->id}}" onclick="@if(isset($profile)) activityReal(this) @else guestUser() @endif">
                            <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-real-image-{{$activity->id}}"></button>
                        <a class="real-count" data-id="{{$activity->id}}" id="activity-real-{{$activity->id}}"
                            onclick="@if(isset($profile)) activityRealList(this) @else guestUser() @endif">{{$real}}</a>
                    </div>

                    <div class="fake">
                        <?php 
                        $fake = 0;
                        $img = 'images/reaction/fake-off.png';
                     ?>
                        @foreach($reacts as $react)
                        @if($react->identifier == 'activity')
                        @if($react->identifier_id == $activity->id)
                        @if($react->type == 'fake')
                        <?php $fake++ ?>
                        @if(isset($profile))
                            @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/fake-on.png'; ?>
                            @endif
                        @endif
                        @endif
                        @endif
                        @endif
                        @endforeach
                        <button data-id="{{$activity->id}}" onclick="@if(isset($profile)) activityFake(this) @else guestUser() @endif">
                            <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                id="activity-fake-image-{{$activity->id}}"></button>
                        <a class="fake-count" data-id="{{$activity->id}}" id="activity-fake-{{$activity->id}}"
                            onclick="@if(isset($profile)) activityFakeList(this) @else guestUser() @endif">{{$fake}}</a>
                    </div>
                    <div class="comment" id="comment-{{$activity->id}}"><button parent-id="0"
                            data-id="{{$activity->id}}" onclick="@if(isset($profile)) commentCreate(this) @else guestUser() @endif">Comment</button></div>
                    @if(isset($profile))
                        @if($profile->id == $activity->user_id)
                        <div class="edit" id="edit-{{$activity->id}}"><button data-id="{{$activity->id}}"
                                onclick="activityEdit(this)">Edit</button></div>
                        <div class="delete" id="hide-{{$activity->id}}"><button data-id="{{$activity->id}}"
                                id="hideBtn-{{$activity->id}}" onclick="activityHide(this)">Hide</button></div>
                        <div class="delete" id="delete-{{$activity->id}}"><button data-id="{{$activity->id}}"
                                onclick="activityDelete(this)">Delete</button></div>
                        @endif
   
                        @if($profile->id != $myProfile->id)
                        <div class="report" id="report-{{$activity->id}}"><button data-id="{{$activity->id}}"
                                onclick="activityReport(this)">Report</button></div>
                        @endif
                    @endif

                    <div class="share" id="share-{{$activity->id}}">
                        <button data-id="{{$activity->id}}" onclick="share(this)"> <i class="fa-solid fa-share"></i>
                            Share</button>

                        <section id="share-option-{{$activity->id}}" class="share-option">
                            <a target="_blank" class="wexprez" onclick="activityShare({{$activity->id}})">
                                <img src="{{asset('')}}images/logo/icon.png" alt="img"> wexprez</a>

                            <a target="_blank" class="facebook"
                                href="https://www.facebook.com/dialog/feed?app_id=350389333934305&display=popup&link={{route('singleActivity', $activity->id)}}">
                                <i class="fa-brands fa-facebook"></i> facebook</a>

                            <a target="_blank" class="twiteer"
                                href="https://twitter.com/intent/tweet?url={{route('singleActivity', $activity->id)}}&text={!! $activity->content !!}">
                                <img src="{{asset('')}}images/logo/x.png" alt="img"> X</a>

                            <a target="_blank" class="linkedin"
                                href="https://www.linkedin.com/shareArticle?url={{route('singleActivity', $activity->id)}}">
                                <i class="fa-brands fa-linkedin"></i> linkedin</a>

                            <a target="_blank" class="pinterest"
                                href="//pinterest.com/pin/create/link/?url={{route('singleActivity', $activity->id)}}&media=https://wexprez.com/images/fb-meta.png&description={!! $activity->content !!}">
                                <i class="fa-brands fa-pinterest"></i> pinterest</a>
                        </section>
                    </div>

                </div>



                <hr>

                <div class="comment-form" id="comment-form-{{$activity->id}}"></div>
                <div id="report-form-{{$activity->id}}"></div>

                <!-- Comments -->
                @foreach($comments as $comment)

                @php

                $date1 = new DateTime("now");

                $date2 = new DateTime($comment->created_at);
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


                @if($comment->activity_id == $activity->id)
                @if($comment->parent_id == 0)
                <div class="activity-comment" id="activity-comment-{{$comment->id}}">

                    <div class="comment-header">
                        <div class="comment-info">
                            <img loading="lazy" loading="lazy" src="{{asset('')}}{{$comment->profile_image}}"
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/voteUp-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="@if(isset($profile)) commentVoteUp(this) @else guestUser() @endif"><img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteUp-{{$comment->id}}"
                                onclick="@if(isset($profile)) commentVoteUpList(this) @else guestUser() @endif">{{$up}}</a>
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/voteDown-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$comment->id}}" onclick="@if(isset($profile)) commentVoteDown(this) @else guestUser() @endif"><img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteDown-image-{{$comment->id}}"></button>
                            <a class="voteUp-count" data-id="{{$comment->id}}" id="comment-voteDown-{{$comment->id}}"
                                onclick="@if(isset($profile)) commentVoteDownList(this) @else guestUser() @endif">{{$down}}</a>
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/real-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="@if(isset($profile)) commentReal(this) @else guestUser() @endif">
                                <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$comment->id}}"></button>
                            <a class="real-count" data-id="{{$comment->id}}" id="comment-real-{{$comment->id}}"
                                onclick="@if(isset($profile)) commentRealList(this) @else guestUser() @endif">{{$real}}</a>
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/fake-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$comment->id}}" onclick="@if(isset($profile)) commentFake(this) @else guestUser() @endif"> <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$comment->id}}"></button>
                            <a class="fake-count" data-id="{{$comment->id}}" id="comment-fake-{{$comment->id}}"
                                onclick="@if(isset($profile)) activityReal(this) @else guestUser() @endif commentFakeList(this)">{{$fake}}</a>
                        </div>

                        <div class="comment" id="reply-{{$comment->id}}"><button parent-id="{{$comment->id}}"
                                data-id="{{$activity->id}}" onclick="@if(isset($profile)) replyCreate(this) @else guestUser() @endif">Reply</button></div>
                        @if(isset($profile))
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
                        @endif

                    </div>

                    <div class="comment-form" id="reply-form-{{$comment->id}}"></div>
                    <div id="comment-report-form-{{$comment->id}}" class="my-3"></div>

                </div>

                <hr>

                @foreach($comments as $reply)
                @if($reply->activity_id == $activity->id)
                @if($reply->parent_id == $comment->id)

                @php
                $date1 = new DateTime("now");

                $date2 = new DateTime($reply->created_at);
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
                            <img loading="lazy" loading="lazy" src="{{asset('')}}{{$reply->profile_image}}"
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/voteUp-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="@if(isset($profile)) commentVoteUp(this) @else guestUser() @endif">
                                <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteUp-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteUp-{{$reply->id}}"
                                onclick="@if(isset($profile)) commentVoteUpList(this) @else guestUser() @endif">{{$up}}</a>
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/voteDown-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach

                            <button data-id="{{$reply->id}}" onclick="@if(isset($profile)) commentVoteDown(this) @else guestUser() @endif"><img loading="lazy"
                                    loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-voteDown-image-{{$reply->id}}"></button>
                            <a class="voteUp-count" data-id="{{$reply->id}}" id="comment-voteDown-{{$reply->id}}"
                                onclick="@if(isset($profile)) commentVoteDownList(this) @else guestUser() @endif">{{$down}}</a>
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/real-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="@if(isset($profile)) commentReal(this) @else guestUser() @endif">
                                <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-real-image-{{$reply->id}}"></button>
                            <a class="real-count" data-id="{{$reply->id}}" id="comment-real-{{$reply->id}}"
                                onclick="@if(isset($profile)) commentRealList(this) @else guestUser() @endif">{{$real}}</a>
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
                            @if(isset($profile))
                                @if($profile->id == $vote->user_id)
                                <?php $img = 'images/reaction/fake-on.png'; ?>
                                @endif
                            @endif
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <button data-id="{{$reply->id}}" onclick="@if(isset($profile)) commentFake(this) @else guestUser() @endif">
                                <img loading="lazy" loading="lazy" src="{{asset('')}}{{$img}}"
                                    id="comment-fake-image-{{$reply->id}}"></button>
                            <a class="fake-count" data-id="{{$reply->id}}" id="comment-fake-{{$reply->id}}"
                                onclick="@if(isset($profile)) commentFakeList(this) @else guestUser() @endif">{{$fake}}</a>
                        </div>
                        @if(isset($profile))
                            @if($profile->id == $reply->user_id)
                            <div class="edit" id="edit-commentBtn-{{$reply->id}}"><button
                                    parent-id="{{$comment->parent_id}}" data-id="{{$reply->id}}"
                                    onclick="commentEdit(this)">Edit</button></div>
                            <div class="delete" id="delete-commentBtn-{{$reply->id}}"><button data-id="{{$reply->id}}"
                                    onclick="commentDelete(this)">Delete</button></div>
                            @endif
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
            @php } } @endphp
            @endforeach


            @if($emotion == 'All' && $dateFilter == 'All')
            <div class="my-3 mx-auto">
                {{ $activitys->links() }}
            </div>
            @endif

        </div>
    </div>
</section>
@endif
@endsection

@section('js')
<script type="text/javascript">

    var page = 1;
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
            page++;
            loadMoreData(page);
        }
    });


    function loadMoreData(page) {
        $.ajax({
                url: '?page=' + page,
                type: "get",
                beforeSend: function () {
                    $('.ajax-load').show();
                }
            })
            .done(function (data) {
                if (data.html == " ") {
                    $('.ajax-load').html("No more records found");
                    return;
                }
                $('.ajax-load').hide();
                $("#activity-feed").append(data.html);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('server not responding...');
            });
    }

</script>

<script>
    // alert close
    $(".alert").delay(3333).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endsection
