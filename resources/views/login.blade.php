<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Interview - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{ url('sbadmin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href=" {{ mix('css/app.css') }}" rel="stylesheet">
	<script src="{{ mix('js/app.js') }}"></script>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-lg-6">
				<?php // echo bcrypt('12345678');?>
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row"> 
                            <div class="col-lg">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900">Welcome Back!</h1>
                                        <p>Please Login</p>
                                    </div>
									<div class="alert alert-danger alert-dismissible fade show">
										<strong>Error!</strong> A problem has been occurred while submitting your data.
										<div class="alertData"></div>
									</div>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" name="email" id="email" aria-describedby="email" placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group py-2">
                                            <input type="password"  name="password" class="form-control form-control-user" id="password" placeholder="Password" required>
                                        </div> 
                                        <a href="#" id="login" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </a>
                                    </form> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div> 

</body>
<script>
  $(".alert-danger").hide();
  $("#login").click(function(){
		let form = $('.user').serialize(); 
		$(".alertData").html('')
		$.ajax({
			url: '/api/login',
			data: form,  
			type: 'POST',
			dataType: "json",
			success: function(response) { 
				if(response.success == true){
					localStorage.setItem("token", response.token );
					window.location.href = "/user";
				}else{
					$(".alert-danger").show();
					$.each(response.error, function(index, item) { 
						$(".alertData").append('<div>'+ index +'</div>'); 
					});
				}				
			},
			error: function(e) {
				$(".alert-danger").show();
				var json = $.parseJSON(e.responseText); 
				$(".alertData").append('<div>'+ json.message +'</div>');  
			},
		}); 
  });
</script>
</html>