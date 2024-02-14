@extends('layout.main')

@section('title')
Wexprez - Change Password
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/login.css">
@endsection

@section('content')
<section class="verify-section">

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block w-50 mx-auto mt-3">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block w-50 mx-auto mt-3">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{ $message }}</strong>
    </div>
    @endif

    <div class="container p-md-5">
        <h2 class="text-center text-success fw-bold mt-5">{{$text}}</h2>

        <div class="mt-5 mx-auto text-center">
            <a href="{{route('login')}}" class="btn mt-5 btn-dark">Back To Login</a>
        </div>
    </div>


</section>

<section class="store-section">
    <div class="mx-auto row">
        <div class="col-md-6 col-12 text-md-end">
            <a href="https://play.google.com/store/apps/details?id=com.byvl.wexprez" target="_blank"
                rel="noopener noreferrer">
                <img src="{{asset('')}}images/play-store.png" alt="play_store" class="store_image">
            </a>
        </div>
        <div class="col-md-6 col-12 text-md-start">
            <a href="https://apps.apple.com/us/app/wexprez/id1606886039" target="_blank" rel="noopener noreferrer">
                <img src="{{asset('')}}images/app-store.png" alt="app_store" class="store_image">
            </a>
        </div>
    </div>
</section>

@endsection

@section('js')
<script>

    window.setTimeout(function() {
        var url = "https://wexprez.com/guest";
        $(location).attr('href',url);
    }, 5000);

    // alert close
    $(".alert").delay(15000).slideUp(500, function () {
        $(this).alert('close');
    });
</script>
@endsection
