@extends('layout.main')

@section('title')
Settings
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/setting.css">
@endsection
@section('content')
<section class="setting-section">
    <div class="container p-5">

        <nav class="navbar navbar-expand mb-4">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('setting')}}">Change Password</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('settingEmail')}}">Change Email</a>
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

        <h5>Change Password</h5>
        <form method="post" class="w-50">
            @csrf

            <div class="row mt-3">
                <label for="oldPassword" class="form-label">Current Password</label>
                <div class="input-group mb-2">
                    <input type="password" required class="form-control" name="oldPassword" id="oldPassword"
                        aria-describedby="basic-addon2">
                    <span class="input-group-text" id="basic-addon2">
                        <i class="fa-solid fa-eye" id="show-oldPass"></i>
                        <i class="fa-solid fa-eye-slash" id="hide-oldPass"></i>
                    </span>
                </div>
            </div>

            <div class="row mt-3">
                <label for="newPassword" class="form-label">New Password</label>
                <div class="input-group mb-1">
                    <input type="password" required class="form-control" name="newPassword" id="newPassword"
                        aria-describedby="basic-addon2" minlength="5">
                    <span class="input-group-text" id="basic-addon2">
                        <i class="fa-solid fa-eye" id="show-newPass"></i>
                        <i class="fa-solid fa-eye-slash" id="hide-newPass"></i>
                    </span>

                </div>
                <small>Password must be at least 5 characters</small>
            </div>

            <div class="row mt-4">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <div class="input-group mb-3">
                    <input type="password" required class="form-control" name="confirmPassword" id="confirmPassword"
                        aria-describedby="basic-addon2" minlength="5">
                    <span class="input-group-text" id="basic-addon2">
                        <i class="fa-solid fa-eye" id="show-conPass"></i>
                        <i class="fa-solid fa-eye-slash" id="hide-conPass"></i>
                    </span>
                </div>
                <small id="notMatch" class="text-danger">New Password and Confirm Password Not Matched</small>
                <small id="match" class="text-success">Password Match</small>
            </div>

            <div class="mb-3 mt-3">
                <button class="btn btn-dark" id="submitBtn" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.4/jquery.validate.min.js"
    integrity="sha512-FOhq9HThdn7ltbK8abmGn60A/EMtEzIzv1rvuh+DqzJtSGq8BRdEN0U+j0iKEIffiw/yEtVuladk6rsG4X6Uqg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

    $('#show-oldPass').click(function () {
        $('#oldPassword').attr("type", "text");;
        $('#show-oldPass').hide();
        $('#hide-oldPass').show();
    });
    $('#hide-oldPass').click(function () {
        $('#oldPassword').attr("type", "password");;
        $('#hide-oldPass').hide();
        $('#show-oldPass').show();
    })


    $('#show-newPass').click(function () {
        $('#newPassword').attr("type", "text");;
        $('#show-newPass').hide();
        $('#hide-newPass').show();
    });
    $('#hide-newPass').click(function () {
        $('#newPassword').attr("type", "password");;
        $('#hide-newPass').hide();
        $('#show-newPass').show();
    })

    $('#show-conPass').click(function () {
        $('#confirmPassword').attr("type", "text");;
        $('#show-conPass').hide();
        $('#hide-conPass').show();
    });
    $('#hide-conPass').click(function () {
        $('#confirmPassword').attr("type", "password");;
        $('#hide-conPass').hide();
        $('#show-conPass').show();
    })

    $('#newPassword').keyup(function () {
        var newPass = $('#newPassword').val();
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
        var newPass = $('#newPassword').val();
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

</script>
@endsection
