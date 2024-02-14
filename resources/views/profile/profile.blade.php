@extends('layout.main')

@section('title')
Profile - Wexprez
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/profile.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
@if($myProfile != null)
<section class="profile-section">
    <div class="cover-image">
        <img loading="lazy" src="{{$myProfile->cover_image}}" alt="{{$myProfile->full_name}}">
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
                <img loading="lazy" src="http://localhost/wexprez_api/uploads/user_profile/{{$myProfile->profile_image}}"
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
                        <img loading="lazy" id="member-voteUp-{{$myProfile->id}}" src="{{asset('')}}{{$img}}" alt="voteUp"
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
                    @if(isset($profile))
                        @if($profile->id == $mv->user_id)
                        <?php $img = 'images/reaction/voteDown-on.png' ?>
                        @endif
                    @endif
                    @endif
                    @endif
                    @endforeach
                    <button onclick="voteDownMember(this)" vote-id="{{$myProfile->id}}" user-id="{{$profile->id}}">
                        <img loading="lazy" id="member-voteDown-{{$myProfile->id}}" src="{{asset('')}}{{$img}}" alt="voteUp"
                            height="20px" width="30px">
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
                    <h5><a onclick="scoreDetails({{$myProfile->id}})" class="score-btn">Score</a></h5>
                </div>
            </div>

        </div>
    </div>

    <section class="profile-content">
        <nav class="navbar navbar-expand profile-nav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profileIxprez', $myProfile->user_name)}}">IXprez</a>
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
                    <a class="nav-link active" href="{{route('profile', $myProfile->user_name)}}">Profile</a>
                </li>
            </ul>
        </nav>
        <nav class="navbar navbar-expand profile-subnav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('profileIxprez', $myProfile->user_name)}}">View</a>
                </li>
                @if(isset($profile))
                    @if($profile->id == $myProfile->id)
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('profileEdit', $myProfile->user_name)}}">Edit</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('profileImage', $myProfile->user_name)}}">Change Profile
                            Picture</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('coverImage', $myProfile->user_name)}}">Change Cover Image</a>
                    </li>
                    @endif
                @endif
            </ul>
        </nav>

        <table class="table table-hover my-5">
            <tbody>
                @if($myProfile->full_name)
                <tr>
                    <td>Full Name</td>
                    <td>{{$myProfile->full_name}}</td>
                </tr>
                @endif
                @if($myProfile->sex)
                <tr>
                    <td>Gender</td>
                    <td>{{$myProfile->sex}}</td>
                </tr>
                @endif
                @if($myProfile->birth_date)
                <tr>
                    <td>Birth Date</td>
                    <td>@if($myProfile->birth_date)
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($myProfile->birth_date))->format('d M Y')}}
                        @endif
                    </td>
                </tr>
                @endif
                @if($myProfile->marital_status)
                <tr>
                    <td>Marital Status</td>
                    <td>{{$myProfile->marital_status}}</td>
                </tr>
                @endif
                @if($myProfile->country)
                <tr>
                    <td>Country</td>
                    <td>{{$myProfile->country}}</td>
                </tr>
                @endif
                @if($myProfile->interest)
                <tr>
                    <td>Interests</td>
                    <td>{{$myProfile->interest}}</td>
                </tr>
                @endif
                @if($myProfile->bio)
                <tr>
                    <td>Bio</td>
                    <td>{{$myProfile->bio}}</td>
                </tr>
                @endif
            </tbody>
        </table>

    </section>


</section>
@endsection

@section('js')
<script>
    // alert close
    $(".alert").delay(4444).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endif
@endsection
