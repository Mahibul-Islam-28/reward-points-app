@extends('layout.main')

@section('title')
Notification
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/notification.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<section class="profile-section">
    <div class="cover-image">
        <img src="{{$myProfile->cover_image}}" alt="{{$myProfile->full_name}}">
        @if($profile->id == $myProfile->id)
        <a href="{{route('coverImage', $myProfile->user_name)}}" title="Change Cover Image" id="coverimage-edit"><i
                class="fa-solid fa-pen-to-square"></i></a>
        @endif
    </div>
    <div class="name-section">
        <div class="row">
            <div class="col-12 col-lg-4 col-xl-3 profile-image">
                <img src="http://localhost/wexprez_api/uploads/user_profile/{{$myProfile->profile_image}}"
                    alt="{{$myProfile->full_name}}">
            </div>
            <div class="col-12 col-lg-5 col-xl-6 profile-name">
                <h2>{{$myProfile->full_name}}</h2>
                <!-- <small class="text-muted">Active 3 Days Ago.</small> -->

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
                        <img id="member-voteUp-{{$myProfile->id}}" src="{{asset('')}}{{$img}}" alt="voteUp"
                            height="20px" width="30px">
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
                        <img id="member-voteDown-{{$myProfile->id}}" src="{{asset('')}}{{$img}}" alt="voteUp"
                            height="20px" width="30px">
                    </button>
                    <a class="voteUp-count" id="member-voteDown-count-{{$myProfile->id}}"
                        onclick="memberVoteDownList({{$myProfile->id}})">{{$down}}</a>
                </div>

            </div>

            <div class="col-12 col-lg-3 col-xl-3">
                <div class="score-section" id="score-card">
                    <div class="score-text">
                        <span>{{$myProfile->score}}</span>
                    </div>
                    <h5><a onclick="scoreDetails({{$myProfile->id}})" class="score-btn">Score</a></h5>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="notification-section my-5">
    <table class="table table-bordered table-striped table-responsive p-5">
        <thead>
            <tr>
                <th>SL</th>
                <th>Notification</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notifications as $key => $value)
            <tr>
                <td>{{$key + 1}}</td>
                <td><a href="{{route('notificationReadPage',$value->id)}}">{{$value->text}}</a></td>
                <td>{{ \Carbon\Carbon::createFromTimestamp(strtotime($value->created_at))->format('d M Y')}}</td>
                <td><a onclick="notificationRead({{$value->id}})" id="read-notification-{{$value->id}}"><i
                            class="fa-solid fa-eye"></i></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>

@endsection
