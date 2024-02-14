<!DOCTYPE html>
<html lang="en">
<head>
  <title>Wexprez Email</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style type="text/css">
  	.container {
	    width: 100%;
	    padding-right: 15px;
	    padding-left: 15px;
	    margin-right: auto;
	    margin-left: auto;
	    color:#000;
	}

	.card {
	    background-color: #fff;
	    background-clip: border-box;
	    border: 1px solid rgba(0,0,0,.125);
	    border-radius: .25rem;
	    width:720px;
	    margin: auto;
	}

	.card-title {
	    margin-bottom: .75rem;
	    margin-top: .75rem;
	}

	.card-img, .card-img-top {
	    border-top-left-radius: calc(.25rem - 1px);
	    border-top-right-radius: calc(.25rem - 1px);
	}

	.card-body {
	    flex: 1 1 auto;
	    min-height: 1px;
	    padding: 1.25rem;
	    font-family: sans-serif;
	    font-size: 17px;
	    color:#000;
	}

	.text-center {
	    text-align: center!important;
	    margin-top: 20px;
	}

	.btn {
		text-decoration: none;
	    background-color: #0069D9;
	    padding: 10px 20px;
	    font-weight: 600;
	    border-radius: 10px;
	    color: #fff !important;
	}
	.link {
		font-weight: bolder;
		color: blue;
		text-decoration: none;
	}
  </style>
</head>
<body>
 
<div class="container">
  
  <div class="card">
  	<div style="text-align: center;padding: 10px; background-color: #efefef;">
  		<img src="https://www.wexprez.com/images/logo/logo.png" width="300px">
  	</div>
  	
    <div class="card-body">
    <img class="img-fluid" src="https://www.wexprez.com/images/fb-meta.png" width="100%" height="300px">
      <p class="card-text">
      	<p style="color: black;"><b>Dear Wexprez User,</b></p>
        <p style="color: black;"><a href="{{$url}}" class="link">Click here</a> to reset your Password.</p>
      </p>
      <p>
      	Thanks, <br>
		On behalf of<br>
		<b>Wexprez</b>
      </p>
      <hr>
      <div class="text-center">
      	<a href="https://wexprez.com/" class="btn">Visit Website</a>
      </div>
      
    </div>
  </div>
  
</div>

</body>
</html>
