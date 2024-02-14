@extends('layout.main')

@section('title')
Wexprez - Registration
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/login.css">
@endsection

@section('content')
<section class="register-section">

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


    <div class="container px-md-5 py-md-4">
        <div class="register-card mx-auto">
            <div class="text-center">
                <img src="{{asset('')}}images/logo/logo.png" alt="wexprez logo" width="200px" height="50px">
            </div>
            <form action="" method="post" id="registerForm" class="mx-4 my-2">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <div class="input-group country-code">
                        <select name="country" id="country">
                            @foreach($countrys as $country)
                            <option value="{{$country['phone']}}" {{ ($country['code'] == 'BD') ? 'selected' : '' }}>
                                {{$country['code']}} {{$country['phone']}}</option>
                            @endforeach
                        </select>
                        <input type="number" name="phone" id="phone" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="userName">User Name</label>
                    <input type="text" name="userName" id="userName" class="form-control" required>
                </div>

                <div class="row mt-2">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input type="password" required class="form-control" name="password" id="password"
                            aria-describedby="basic-addon2">
                        <span class="input-group-text" id="basic-addon2">
                            <i class="fa-solid fa-eye" id="show-pass"></i>
                            <i class="fa-solid fa-eye-slash" id="hide-pass"></i>
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <input type="submit" value="Register" class="btn btn-dark">
                </div>

                <div class="text-center mt-3">
                    <a href="{{route('login')}}">Already Registered?</a>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
 crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  
          // $('#userName').keypress(function (e) {
        //         if (e.which === 32)
        //             return false;
        // });
  
    jQuery.validator.addMethod("noSpace", function(value, element) { 
    return value.indexOf(" ") < 0 && value != ""; 
    }, "No white space allowed.");
        $("#registerForm").validate({
        rules: {
            userName: {
                required: true,
                minlength: 2,
                noSpace: true
            },
            password: {
                required: true,
                minlength: 5
            },
            email: {
                required: true,
                email: true
            }

        },
        messages: {

            userName: {
                required: "Please enter a username",
                maxlength:"max length 15 digits",
                minlength: "Your username must consist of at least 5 characters"
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
        }
    });
  
  
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })();

    $('#show-pass').click(function () {
        $('#password').attr("type", "text");;
        $('#show-pass').hide();
        $('#hide-pass').show();
    });
    $('#hide-pass').click(function () {
        $('#password').attr("type", "password");;
        $('#hide-pass').hide();
        $('#show-pass').show();
    });

    // alert close
    $(".alert").delay(15000).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endsection
