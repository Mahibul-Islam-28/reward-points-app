<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WxAdmin</title>
    <link rel="stylesheet" href="{{asset('vendors/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/login.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
 integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" 
 crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="container">
        <div id="login-card">
             <div class="logo">
               <img src="{{asset('')}}images/logo/logo.png" alt="logo">
             </div>
            <form action="" method="post">
                 @csrf
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="userName" placeholder="User Name" aria-label="user name"
                        aria-describedby="username">
                    <div class="input-group-append">
                        <span class="input-group-text" id="username"><i class="fa-solid fa-user"></i></span>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="*****" aria-label="Password" id="password">
                    <div class="input-group-append" id="show-password">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    </div>
                </div>

                <button class="btn btn-dark" type="submit">Login</button>
            </form>
        </div>
    </div>

    <script src="{{asset('vendors/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('vendors/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('vendors/js/jquery.min.js')}}"></script>
    <script>

        $('#show-password').click(function () {
          //   $('#password').attr("type", "password");
            $('#password').attr("type",  $('#password').attr('type')== 'password'?'text':'password')
        });


    </script>

</body>

</html>
