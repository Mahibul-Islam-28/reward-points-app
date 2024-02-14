@extends('layout.main')

@section('title')
WeXprez
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/about.css">
@endsection

@section('content')
<section class="download-section">
    <div class="container">
        <img class="download-img mx-auto" src="{{asset('')}}images/fb-meta.png" alt="play_store" class="top_image">
        <div class="container py-5 w-50 mx-auto">
            <div class="w-75 mx-auto row mt-5">
                <div class="col-md-6 col-12">
                    <a href="https://play.google.com/store/apps/details?id=com.byvl.wexprez" target="_blank"
                        rel="noopener noreferrer">
                        <img src="{{asset('')}}images/play-store.png" alt="play_store" class="store_image">
                    </a>
                </div>
                <div class="col-md-6 col-12">
                    <a href="https://apps.apple.com/us/app/wexprez/id1606886039" target="_blank"
                        rel="noopener noreferrer">
                        <img src="{{asset('')}}images/app-store.png" alt="app_store" class="store_image">
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
