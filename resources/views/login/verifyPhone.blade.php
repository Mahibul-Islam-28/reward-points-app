@extends('layout.main')

@section('title')
Wexprez - phone Verify
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

    <div class="container mt-5 login-card mx-auto" style="max-width: 550px">
        <div class="alert alert-danger" id="error" style="display: none;"></div>
        <!-- <h3>Add Phone Number</h3> -->
        <div class="alert alert-success" id="successAuth" style="display: none;"></div>
        <form>
            <h5>Your Number</h5>
            <input type="text" id="number" disabled class="form-control" value="{{ session()->get('phone') }}">
            <div id="recaptcha-container"></div>
            <button type="button" id="send" class="btn btn-dark mt-3 mx-auto" onclick="sendOTP();">Send
                OTP</button>
                
        </form>

        <div class="mb-5 mt-5">
            <h5>Add verification code</h5>
            <div class="alert alert-success" id="successOtpAuth" style="display: none;"></div>
            <form>
                <input type="text" id="verification" class="form-control" placeholder="Verification code">
                <button type="button" class="btn btn-dark mt-3" onclick="verify()">Verify code</button>
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
<!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.20.0/firebase.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/8.0.1/firebase.js"></script> -->

<script>
    $(document).load(function () {
        sendOTP();
    });

    // var firebaseConfig = {
    //     apiKey: "API_KEY",
    //     authDomain: "PROJECT_ID.firebaseapp.com",
    //     databaseURL: "https://PROJECT_ID.firebaseio.com",
    //     projectId: "PROJECT_ID",
    //     storageBucket: "PROJECT_ID.appspot.com",
    //     messagingSenderId: "SENDER_ID",
    //     appId: "APP_ID"
    // };


    // var firebaseConfig = {
    //     apiKey: "AIzaSyABSb-f3oApldQctB2gUUmyGJhU4al4gzI",
    //     authDomain: "wexprez-a7702.firebaseapp.com",
    //     projectId: "wexprez-a7702",
    //     storageBucket: "wexprez-a7702.appspot.com",
    //     messagingSenderId: "445067319731",
    //     appId: "1:445067319731:web:a4ebb4ef7050d8ffa093c6",
    //     measurementId: "G-CG8QNR6ZR4"
    // };
    
    // firebase.initializeApp(firebaseConfig);

    // window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
    //     'size': 'invisible',
    //     'callback': function (response) {
    //         // reCAPTCHA solved, allow signInWithPhoneNumber.
    //         console.log('recaptcha resolved');
    //     }
    // });
    
</script>
<script type="text/javascript">
    // window.onload = function () {
    //     render();
    // };

    // function render() {
    //     window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container');
    //     recaptchaVerifier.render();
    // }

    var coderesult;

    function sendOTP() {

        var firebaseConfig = {
            apiKey: "AIzaSyABSb-f3oApldQctB2gUUmyGJhU4al4gzI",
            authDomain: "wexprez-a7702.firebaseapp.com",
            projectId: "wexprez-a7702",
            storageBucket: "wexprez-a7702.appspot.com",
            messagingSenderId: "445067319731",
            appId: "1:445067319731:web:a4ebb4ef7050d8ffa093c6",
            measurementId: "G-CG8QNR6ZR4"
        };
        
        firebase.initializeApp(firebaseConfig);

        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
            'size': 'invisible',
            'callback': function (response) {
                // reCAPTCHA solved, allow signInWithPhoneNumber.
                console.log('recaptcha resolved');
            }
        });

        var number = $("#number").val();
        firebase.auth().signInWithPhoneNumber(number, window.recaptchaVerifier).then(function (confirmationResult) {
            window.confirmationResult = confirmationResult;
            coderesult = confirmationResult;
            console.log(coderesult);
            $("#successAuth").text("OTP Sent To Your Phone!");
            $("#successAuth").show();
        }).catch(function (error) {
            $("#error").text(error.message);
            $("#error").show();
        });
    }

    function verify() {

        var user_id = '{{ Session::get("user_id")}}';

        var code = $("#verification").val();
        console.log(coderesult);
        coderesult.confirm(code).then(function (result) {
            console.log("Verification Successful!");
            window.location.replace("https://www.wexprez.com/otpVerifyUrl/"+user_id+"");
            $("#successAuth").text("Auth is successful");
            $("#successAuth").show();
        }).catch(function (error) {
            $("#error").text(error.message);
            $("#error").show();
        });
        
        
    }

    // function verify() {
    //     var code = $("#verification").val();
    //     coderesult.confirm(code).then(function (result) {
    //         var user = result.user;
    //         console.log(user);
    //         $("#successOtpAuth").text("Auth is successful");
    //         $("#successOtpAuth").show();
    //     }).catch(function (error) {
    //         $("#error").text(error.message);
    //         $("#error").show();
    //     });
    // }



    // alert close
    $(".alert").delay(15000).slideUp(500, function () {
        $(this).alert('close');
    });
</script>
@endsection
