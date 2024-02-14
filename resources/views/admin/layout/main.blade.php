<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>@yield('title')</title>

    @include('admin.layout.links')

    @yield('css')
</head>

<body>
    <div class="row">
        <div class="col-2">
            @include('admin.layout.navbar')
        </div>

        <div class="col-8">
            @yield('content')
        </div>
    </div>

    @include('admin.layout.footer')

    @include('admin.layout.script')

    @yield('js')
</body>

</html>
