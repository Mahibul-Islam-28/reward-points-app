@extends('layout.main')

@section('title')
404
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/index.css">
@endsection

@section('content')
<section class="not-found-section">
    <div class="container py-5 w-50 mx-auto">
        <h1>404</h1>
        <h2>Not Found!</h2>
    </div>
</section>
@endsection
