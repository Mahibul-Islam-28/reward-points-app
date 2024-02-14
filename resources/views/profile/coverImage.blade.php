@extends('layout.main')

@section('title')
Profile || Wexprez
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/profile.css">
<link rel="stylesheet" href="{{asset('')}}vendors/css/croppie.css">
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

    <section class="cover-content">
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
                    <a class="nav-link" href="{{route('profileEdit', $myProfile->user_name)}}">Edit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profileImage', $myProfile->user_name)}}">Change Profile
                        Picture</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('coverImage', $myProfile->user_name)}}">Change Cover
                        Image</a>
                </li>
                @endif
            </ul>
        </nav>

        @if($profile->id == $myProfile->id)



        <div class="upload-section my-5">
            <div id="image-preview" class=""></div>


            <div class="w-50 mx-auto" align="center">
                <h5 class="text-center text-success mb-2" id="after-upload"></h5>

                @csrf
                <p><label>Select Image</label></p>
                <input type="file" name="upload_image" id="upload_image" />
                <br>
                <br>
                <button class="btn btn-dark crop_image">Crop & Upload Image</button>
            </div>

        </div>

        <div class="row" style="padding:75px; background-color: #333">
            <div id="uploaded_image" align="center"></div>
        </div>

        @endif
    </section>




</section>
@endsection

@section('js')
<script src="{{asset('')}}vendors/js/croppie.min.js"></script>

<script>
    $(document).ready(function () {

        $('#image-preview').hide();

        $image_crop = $('#image-preview').croppie({
            enableExif: true,
            viewport: {
                width: 90 + '%',
                height: 350,
                type: 'square'
            },
            boundary: {
                width: 90 + '%',
                height: 350
            }
        });

        $('#upload_image').change(function () {
            var reader = new FileReader();
            $('#image-preview').show();

            reader.onload = function (event) {
                $image_crop.croppie('bind', {
                    url: event.target.result
                }).then(function () {
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('.crop_image').click(function (event) {
            $image_crop.croppie('result', {
                type: 'canvas'
            }).then(function (response) {
                var _token = $('input[name=_token]').val();
                $.ajax({
                    url: '{{ route("coverImageUpload", $myProfile->user_name) }}',
                    type: 'post',
                    data: {
                        image: response,
                        _token: _token
                    },
                    dataType: "json",
                    success: function (data) {
                        var path = data.path;
                        var crop_image = '<img loading="lazy" src="' + path +
                        '" />';
                        $('#uploaded_image').html(crop_image);
                        $('#after-upload').html("Cover Photo has been Uploaded!");
                    }
                });
            });
        });

    });

</script>


@endsection

@else
<script>
    window.location = "/404";

</script>
@endif
