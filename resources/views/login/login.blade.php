@extends('layout.main')

@section('title')
Wexprez - Login
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/login.css">
@endsection

@section('content')
<section class="login-section">

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
        <div class="login-card mx-auto">
            <div class="text-center">
                <img src="{{asset('')}}images/logo/logo.png" alt="wexprez logo" width="200px" height="50px">
            </div>
            <form action="" method="post" class="m-md-4 m-3">
                @csrf

                <div class="row mt-3">
                    <label for="userName">User Name or Phone</label>
                    <div class="input-group mb-2 country-code">
                        <select name="country" id="country">
                            @foreach($countrys as $country)
                            <option value="{{$country['phone']}}" {{ ($country['code'] == 'BD') ? 'selected' : '' }}>
                                {{$country['code']}} {{$country['phone']}}</option>
                            @endforeach
                        </select>
                        <input type="text" required name="userName" id="phone" class="form-control"
                            aria-describedby="basic-addon2">
                        <span class="input-group-text" id="basic-addon2">
                            <img src="{{asset('')}}images/swap1.png" id="show-phone">
                            <img src="{{asset('')}}images/swap2.png" id="show-userName">
                        </span>
                    </div>
                </div>


                <div class="row mt-3">
                    <label for="password">Password</label>
                    <div class="input-group mb-2">
                        <input type="password" required class="form-control" name="password" id="password"
                            aria-describedby="basic-addon2">
                        <span class="input-group-text" id="basic-addon2">
                            <i class="fa-solid fa-eye" id="show-pass"></i>
                            <i class="fa-solid fa-eye-slash" id="hide-pass"></i>
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-4 mt-4">
                    <input type="submit" value="Login" class="btn btn-dark">
                </div>

                <div class="text-center mt-4">
                    <a href="{{route('registration')}}">Not Registered yet?</a>
                </div>
                <div class="text-center mt-2">
                    <a href="{{route('forgotPassword')}}">Forgot Password?</a>
                </div>


            </form>
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

<div class="work-link">
    <a href="{{route('howWorks')}}">How It Works</a>
</div>

@endsection

@section('js')
<script>
    $('#show-userName').hide();
    $('#country').hide();

    $('#show-userName').click(function () {
        $('#show-phone').show();
        $('#show-userName').hide();
        $('#country').hide();
    });

    $('#show-phone').click(function () {
        $('#show-userName').show();
        $('#show-phone').hide();
        $('#country').show();
    });



    $('#show-pass').click(function () {
        $('#password').attr("type", "text");
        $('#show-pass').hide();
        $('#hide-pass').show();
    });
    $('#hide-pass').click(function () {
        $('#password').attr("type", "password");
        $('#hide-pass').hide();
        $('#show-pass').show();
    });

    // alert close
    $(".alert").delay(15000).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endsection
