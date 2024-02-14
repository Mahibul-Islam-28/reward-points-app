@extends('layout.main')

@section('title')
Xprezers
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/members.css">
@endsection

@section('content')
<section class="member-section">
    <h2 class="text-center">Followers</h2>
    <nav class="navbar navbar-expand my-4">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('member')}}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('following')}}">Following</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('followers')}}">Followers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('blockList')}}">Block List</a>
            </li>
        </ul>
    </nav>

    <div id="member-search">
        <input type="search" class="form-control" name="follower-search" id="follower-search" onkeyup="followerSearch()"
            placeholder="Search here..">
    </div>

    <div class="row" id="follower-list">
        @foreach($followers as $follower)
        @if($profile->id != $follower->user_id)
        <div class="col-md-4 col-xl-3 col-12">
            <div class="card member-card">
                <div class="card-head p-2">
                    <img src="{{asset('')}}{{$follower->profile_image}}" alt="{{$follower->full_name}}" height="100%"
                        width="100%" class="rounded-circle">
                </div>
                <div class="card-body">
                    <a href="{{route('profileWexprez', $follower->user_name)}}">
                        <h3>{{$follower->full_name}}</h3>
                    </a>
                    <span>Score: {{$follower->score}}</span>
                    <div class="d-grid gap-2 mt-4">
                        <?php 
                        $fw = "Follow";
                        $blc = "Block";
                        ?>
                        @foreach($following as $follow)
                        @if($follower->user_id == $follow->follow_id)
                        <?php $fw = "Unfollow" ?>
                        @endif
                        @endforeach
                        @foreach($blocking as $block)
                        @if($follower->user_id == $block->block_id)
                        <?php $blc = "Unblock" ?>
                        @endif
                        @endforeach

                        <button class="btn btn-dark" value="{{$follower->user_id}}" onclick="follow(this);"
                            id="follow-{{$follower->user_id}}"
                            data-id="{{$follower->user_id}}"><?php echo $fw; ?></button>

                        <button class="btn btn-dark" value="{{$follower->user_id}}" onclick="block(this);"
                            id="block-{{$follower->user_id}}"
                            data-id="{{$follower->user_id}}"><?php echo $blc; ?></button>

                    </div>

                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <div id="search-list" class="my-3">
    </div>

</section>
@endsection
