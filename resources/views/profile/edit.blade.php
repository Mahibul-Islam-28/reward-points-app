@extends('layout.main')

@section('title')
Profile - Wexprez
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/profile.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@if($profile->id == $myProfile->id)
@section('content')
<section class="profile-section">
    <div class="cover-image">
        <img loading="lazy" src="{{$myProfile->cover_image}}" alt="{{$myProfile->full_name}}">
        @if($profile->id == $myProfile->id)
        <a href="{{route('coverImage', $myProfile->user_name)}}" title="Change Cover Image" id="coverimage-edit"><i
                class="fa-solid fa-pen-to-square"></i></a>
        @endif
    </div>
    <div class="name-section">
        <div class="row">
            <div class="col-12 col-lg-4 col-xl-3 profile-image">
                <img loading="lazy"
                    src="http://localhost/wexprez_api/uploads/user_profile/{{$myProfile->profile_image}}"
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
                        <img loading="lazy" id="member-voteUp-{{$myProfile->id}}" src="{{asset('')}}{{$img}}"
                            alt="voteUp" height="20px" width="30px">
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
                        <img loading="lazy" id="member-voteDown-{{$myProfile->id}}" src="{{asset('')}}{{$img}}"
                            alt="voteUp" height="20px" width="30px">
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
                @if($profile->user_name == $myProfile->user_name)
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profileArchive', $myProfile->user_name)}}">Archive</a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('profile', $myProfile->user_name)}}">Profile</a>
                </li>
            </ul>
        </nav>
        <nav class="navbar navbar-expand profile-subnav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profile', $myProfile->user_name)}}">View</a>
                </li>
                @if($profile->id == $myProfile->id)
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('profileEdit', $myProfile->user_name)}}">Edit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profileImage', $myProfile->user_name)}}">Change Profile
                        Picture</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('coverImage', $myProfile->user_name)}}">Change Cover Image</a>
                </li>
                @endif
            </ul>
        </nav>

        @if($profile->id == $myProfile->id)

        <form method="post">
            @csrf
            <div class="mb-3 mt-3">
                <label for="fullName" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="fullName" id="fullname" value="{{$myProfile->full_name}}">
            </div>

            <div class="mb-3 mt-3">
                <label for="sex" class="form-label">Gender</label> <br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="sex" id="sex" value="Male"
                        {{($myProfile->sex == "Male")? "checked" : ""}}>
                    <label class="form-check-label" for="inlineRadio1">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="sex" id="sex" value="Female"
                        {{($myProfile->sex == "Female")? "checked" : ""}}>
                    <label class="form-check-label" for="inlineRadio2">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="sex" id="sex" value="Other"
                        {{($myProfile->sex == "other")? "checked" : ""}}>
                    <label class="form-check-label" for="inlineRadio2">Other</label>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label for="birthDate" class="form-label">Birth Date:</label> <br>
                <input type="date" class="form-control" value="{{$myProfile->birth_date}}" id="birthDate"
                    name="birthDate">
            </div>

            <div class="mb-3 mt-3">
                <label for="status" class="form-label">Marital Status</label> <br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status" value="Single"
                        {{($myProfile->marital_status == "Single")? "checked" : ""}}>
                    <label class="form-check-label" for="inlineRadio1">Single</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status" value="Married"
                        {{($myProfile->marital_status == "Married")? "checked" : ""}}>
                    <label class="form-check-label" for="inlineRadio2">Married</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status" value="In a Reletionship"
                        {{($myProfile->marital_status == "In a Reletionship")? "checked" : ""}}>
                    <label class="form-check-label" for="inlineRadio2">In a Relationship</label>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label for="country" class="form-label">Country:</label> <br>
                <select class="form-select " id="country" name="country" aria-label="Select Country">

                    <option value="-1" disabled selected>-- select one --</option>
                    @foreach($countrys as $key => $value)
                    @if($myProfile->country == $value->name)
                    <option value="{{$value->name}}">{{$value->name}}</option>
                    @endif
                    <option value="{{$value->name}}">{{$value->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 mt-3">
                <label for="interest" class="form-label">Interest:</label> <br>
                <div class="input-group">
                    @foreach($interests as $interest)
                    <div class="checkbox check-label">
                        <div class="checkbox-inline me-3">
                            <input type="checkbox" name="interest[]" value="{{$interest->id}}"
                            <?php if($userInterest){
                                foreach($userInterest as $id)
                                {
                                    if($id == $interest->id)
                                    {
                                        echo "checked='checked'";
                                    }
                            }}?>>
                            {{$interest->interest_name}}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-3 mt-3">
                <div class="form-group">
                    <label for="bio">Bio:</label>
                    <textarea class="form-control" rows="5" name="bio" id="bio" maxlength="250"
                        onkeyup="biography()">{{$myProfile->bio}}</textarea>
                </div>
                <div id="count" class="edit-count">
                    <span id="current_count">0 </span>
                    <span id="maximum_count">/ 250</span>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <button class="btn btn-dark" type="submit">Save Changes</button>
            </div>

        </form>

        @endif

    </section>


</section>
@endsection
@else
<script>
    window.location = "/404";

</script>
@endif

@section('js')
<script>
    $("input:checkbox").click(function () {
        var bol = $("input:checkbox:checked").length >= 10;
        $("input:checkbox").not(":checked").attr("disabled", bol);
    });

    var bol = $("input:checkbox:checked").length >= 10;
    $("input:checkbox").not(":checked").attr("disabled", bol);

</script>
@endsection
