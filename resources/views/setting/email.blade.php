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
                    <a class="nav-link" href="{{route('setting')}}">Change Password</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('settingEmail')}}">Change Email</a>
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

        <h5>Change Email</h5>
        <form method="post" class="w-50">
            @csrf

            <div class="my-3">
                    <label for="oldEmail" class="form-label">Current Email</label>
                    <input type="text" disabled class="form-control" value="{{$userData->email}}" id="oldEmail">
            </div>

            <div class="my-3">
                    <label for="email" class="form-label">New Email</label>
                    <input type="email" class="form-control" id="email" name="email">
            </div>


            <div class="mb-3 mt-3">
                <button class="btn btn-dark" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('js')

<script>
    // alert close
    $(".alert").delay(22222).slideUp(500, function () {
        $(this).alert('close');
    });

</script>
@endsection

