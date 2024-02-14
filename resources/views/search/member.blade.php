@extends('layout.main')

@section('title')
Search Results
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/search.css">
@endsection

@section('content')
<section class="search-section">
    <div class="container py-5">

    @if(count($members) > 0)
    <nav class="navbar navbar-expand">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('search', $searchValue)}}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('xprezerSearch', $searchValue)}}">Xprezers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('activitySearch', $searchValue)}}">Activity</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('commentSearch', $searchValue)}}">Comment</a>
            </li>
        </ul>
    </nav>

        <div class="member-result mt-4">
            <div class="row">
                @foreach($members as $member)
                <div class="col-md-4 col-6">
                    <div class="card mb-3">
                        <div class="row no-gutters member-card">
                            <div class="col-md-4 col-lg-3 col-12 align-self-center">
                                <a class="ms-2" href="{{route('profileIxprez', $member->user_name)}}">
                                    <img src="{{asset('')}}{{$member->profile_image}}" alt="{{$profile->full_name}}"
                                        height="70px" width="70px" class="rounded-circle">
                                </a>
                            </div>
                            <div class="col-md-8 col-lg-9 col-12">
                                <div class="card-body">
                                    <a href="{{route('profileIxprez', $member->user_name)}}">
                                        <h3>{{$member->full_name}}</h3>
                                    </a>
                                    <span>Score: {{$member->score}}</span>
                                    <br> <br>
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

                                    @if($member->id != $profile->id)
                                    <button class="btn btn-dark" value="{{$member->id}}" onclick="follow(this);"
                                        id="follow-{{$member->id}}"
                                        data-id="{{$member->id}}"><?php echo $fw; ?></button>

                                    <button class="btn btn-dark" value="{{$member->id}}" onclick="block(this);"
                                        id="block-{{$member->id}}"
                                        data-id="{{$member->id}}"><?php echo $blc; ?></button>
                                   @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
               <h3 class="text-center py-4">No Xprezer Found!</h3>
        @endif




</section>
@endsection
