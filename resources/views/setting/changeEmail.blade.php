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
@endsection

@section('js')
<script>

    // alert close
    $(".alert").delay(15000).slideUp(500, function() {
    $(this).alert('close');
    });

</script>
@endsection
