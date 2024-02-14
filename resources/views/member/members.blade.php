@extends('layout.main')

@section('title')
Xprezers
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/members.css">
@endsection

@section('content')
<section class="member-section">
    <h2 class="text-center">Xprezers</h2>
    <nav class="navbar navbar-expand my-4">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="#{{route('member')}}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('following')}}">Following</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('followers')}}">Followers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('blockList')}}">Block List</a>
            </li>
        </ul>
    </nav>

    <div id="member-search">
        <input type="search" class="form-control" name="member-search" id="member-search" onkeyup="memberSearch()"
            placeholder="Search here..">
    </div>

    <div class="row" id="member-list">
        @foreach($members as $member)
        @if($profile->id != $member->id)
        <div class="col-md-4 col-xl-3 col-12">
            <div class="card member-card">
                <div class="card-head p-2">
                    <img loading="lazy" src="{{asset('')}}{{$member->profile_image}}" alt="{{$profile->full_name}}"
                        height="100%" width="100%" class="rounded-circle">
                </div>
                <div class="card-body">
                    <a href="{{route('profileIxprez', $member->user_name)}}">
                        <h3>{{$member->full_name}}</h3>
                    </a>
                    <span>Score: {{$member->score}}</span>
                    <div class="d-grid gap-2 mt-3">
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
        @endif
        @endforeach
    </div>

    <div id="search-list" class="my-3">

    </div>

</section>
@endsection
