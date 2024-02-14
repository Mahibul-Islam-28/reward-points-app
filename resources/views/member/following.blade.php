@extends('layout.main')

@section('title')
Xprezers
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/members.css">
@endsection

@section('content')
<section class="member-section">
    <h2 class="text-center">Followings</h2>
    <nav class="navbar navbar-expand my-4">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('member')}}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('following')}}">Following</a>
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
        <input type="search" class="form-control" name="following-search" id="following-search"
            onkeyup="followingSearch()" placeholder="Search here..">
    </div>

    <div class="row" id="following-list">
        @foreach($followings as $key => $value)
        @if($profile->id != $value->follow_id)
        <div class="col-md-4 col-xl-3 col-12">
            <div class="card member-card">
                <div class="card-head p-2">
                    <img src="{{asset('')}}{{$value->profile_image}}" alt="{{$value->full_name}}" height="100%"
                        width="100%" class="rounded-circle">
                </div>
                <div class="card-body">
                    <a href="{{route('profileWexprez', $value->user_name)}}">
                        <h3>{{$value->full_name}}</h3>
                    </a>
                    <span>Score: {{$value->score}}</span>
                    <div class="d-grid gap-2 mt-4">
                        <?php 
                        $fw = "Follow";
                        $blc = "Block";
                        ?>
                        @foreach($following as $follow)
                        @if($value->follow_id == $follow->follow_id)
                        <?php $fw = "Unfollow" ?>
                        @endif
                        @endforeach
                        @foreach($blocking as $block)
                        @if($value->follow_id == $block->block_id)
                        <?php $blc = "Unblock" ?>
                        @endif
                        @endforeach

                        <button class="btn btn-dark" value="{{$value->follow_id}}" onclick="follow(this);"
                            id="follow-{{$value->follow_id}}"
                            data-id="{{$value->follow_id}}"><?php echo $fw; ?></button>

                        <button class="btn btn-dark" value="{{$value->follow_id}}" onclick="block(this);"
                            id="block-{{$value->follow_id}}"
                            data-id="{{$value->follow_id}}"><?php echo $blc; ?></button>

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
