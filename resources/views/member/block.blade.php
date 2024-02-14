@extends('layout.main')

@section('title')
Xprezers - Block List
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/members.css">
@endsection
@section('content')
<section class="member-section">

    <h2 class="text-center">Block List</h2>
    <nav class="navbar navbar-expand my-4">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('member')}}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('following')}}">Following</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('followers')}}">Followers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('blockList')}}">Block List</a>
            </li>
        </ul>
    </nav>


    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block w-50">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block w-50">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif

    <div id="member-search">
        <input type="search" class="form-control" name="block-search" id="block-search" onkeyup="blockSearch()"
            placeholder="Search here..">
    </div>

    <div class="row" id="block-list">
        @foreach($blocks as $block)
        <div class="col-md-4 col-xl-3 col-12">
            <div class="card member-card">
                <div class="card-head p-2">
                    <img src="{{asset('')}}{{$block->profile_image}}" alt="{{$block->full_name}}" height="100%"
                        width="100%" class="rounded-circle">
                </div>
                <div class="card-body">
                    <a href="{{route('profileIxprez', $block->user_name)}}">
                        <h3>{{$block->full_name}}</h3>
                    </a>
                    <div class="d-grid gap-2 mt-4">

                        <button class="btn btn-dark" onclick="unblock(this);" id="block-{{$block->block_id}}"
                            data-id="{{$block->block_id}}">Unblock</button>

                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="search-list" class="my-3">

    </div>

</section>
@endsection
