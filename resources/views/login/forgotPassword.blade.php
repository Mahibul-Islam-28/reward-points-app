@extends('layout.main')

@section('title')
Wexprez - Forgot Password
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/login.css">
@endsection

@section('content')
<section class="forgot-section">

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

            @if(isset($emailLink))
            <h5>{{$emailLink}}</h5>
            @endif
            <form action="" method="post" class="m-4">
                @csrf
                <div class="form-group">
                    <label for="userName">Your Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="d-grid gap-4 mt-4">
                    <input type="submit" value="Reset Password" class="btn btn-dark">
                </div>

                <div class="text-center mt-4">
                    <a href="{{route('login')}}">Back to Login?</a>
                </div>
                <div class="text-center mt-4">
                    <a href="{{route('registration')}}">Not Registered yet?</a>
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
@endsection

@section('js')
<script>
    $('#hide-pass').hide();

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

    // alert close
    $(".alert").delay(17000).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endsection
