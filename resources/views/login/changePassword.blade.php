@extends('layout.main')

@section('title')
Wexprez - Phone Verify
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/login.css">
@endsection

@section('content')
<section class="change-password-section">

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
        @if($token != null)
        <div class="login-card mx-auto">
            <div class="text-center">
                <img src="{{asset('')}}images/logo/logo.png" alt="wexprez logo" width="200px" height="50px">
            </div>
            <form action="" method="post" class="m-4">
                @csrf

                <div class="row mt-3">
                    <label for="password">New Password</label>
                    <div class="input-group mb-1">
                        <input type="password" required class="form-control" name="password" id="password"
                            aria-describedby="basic-addon2" minlength="5">
                        <span class="input-group-text" id="basic-addon2">
                            <i class="fa-solid fa-eye" id="show-pass"></i>
                            <i class="fa-solid fa-eye-slash" id="hide-pass"></i>
                        </span>
                        <small>Password must be at least 5 characters</small>
                    </div>
                </div>

                <div class="row mt-3">
                    <label for="password">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" required class="form-control" name="confirmPassword" id="confirmPassword"
                            aria-describedby="basic-addon2" minlength="5">
                        <span class="input-group-text" id="basic-addon2">
                            <i class="fa-solid fa-eye" id="show-confirm"></i>
                            <i class="fa-solid fa-eye-slash" id="hide-confirm"></i>
                        </span>
                    </div>
                    <small id="notMatch" class="text-danger mt-1">New Password and Confirm Password Not Matched</small>
                    <small id="match" class="text-success mt-1">Password Match</small>

                </div>

                <input type="hidden" name="token" value="{{$token}}">

                <div class="d-grid gap-4 mt-4">
                    <input type="submit" value="Save Password" id="submitBtn" class="btn btn-dark">
                </div>

                <div class="text-center mt-3">
                    <a href="{{route('login')}}">Back to Login?</a>
                </div>

            </form>
        </div>

        @else
        <h2 class="mt-5 text-center text-danger">This token is expired!</h2>
        @endif


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
    $('#show-pass').click(function () {
        $('#password').attr("type", "text");;
        $('#show-pass').hide();
        $('#hide-pass').show();
    });
    $('#hide-pass').click(function () {
        $('#password').attr("type", "password");;
        $('#hide-pass').hide();
        $('#show-pass').show();
    })

    $('#show-confirm').click(function () {
        $('#confirmPassword').attr("type", "text");;
        $('#show-confirm').hide();
        $('#hide-confirm').show();
    });
    $('#hide-confirm').click(function () {
        $('#confirmPassword').attr("type", "password");;
        $('#hide-confirm').hide();
        $('#show-confirm').show();
    })

    $('#password').keyup(function () {
        var newPass = $('#password').val();
        var conPass = $('#confirmPassword').val();
        if (conPass != '') {
            if (newPass != conPass) {
                $("#submitBtn").attr("disabled", "disabled");
                $('#notMatch').show();
                $('#match').hide();
            } else {
                $("#submitBtn").removeAttr("disabled");
                $('#match').show();
                $('#notMatch').hide();
            }
        }
    })


    $('#confirmPassword').keyup(function () {
        var newPass = $('#password').val();
        var conPass = $('#confirmPassword').val();

        if (newPass != '') {
            if (newPass === conPass) {
                $("#submitBtn").removeAttr("disabled");
                $('#match').show();
                $('#notMatch').hide();
            } else {
                $("#submitBtn").attr("disabled", "disabled");
                $('#notMatch').show();
                $('#match').hide();
            }
        }
    });

    // alert close
    $(".alert").delay(15000).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endsection
