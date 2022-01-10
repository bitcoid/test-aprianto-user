 
<div class="alert alert-danger alert-dismissible fade show">
	<strong>Error!</strong> A problem has been occurred while submitting your data.
	<div class="alertData"></div>
</div>
<form class="user" enctype="multipart/form-data">
	 
	<input type="hidden" id="user_id" name="user_id" value="<?php echo $id; ?>">
	
	<div class="form-group py-2">
		<input type="text"  name="name" class="form-control form-control-user" id="name" placeholder="Enter Your Name..." required>
	</div> 
	<div class="form-group">
		<input type="email" class="form-control form-control-user" name="email" id="email" aria-describedby="email" placeholder="Enter Email Address..." required>
	</div>
	<div class="form-group py-2">
		<input type="password"  name="password" class="form-control form-control-user" id="password" placeholder="Password" required>
	</div> 
	<div class="form-group pb-5">
            <input type="file" name="file" class="form-control" id="image-input">
            <span class="text-danger" id="image-input-error"></span>
    </div>
	<img src="" id="images" style="width:200px">
	<div class="clearfix">&nbsp;</div>
	<button type="button" id="register" class="btn btn-success">Add New</button> 
</form> 

<script>
   $(".alert-danger").hide();
   $('#register').click(function(e) {
	    var form = $(".user");  
		var form_data = new FormData(form[0]);    
		let url = '';

		if( $("#user_id").val() === "undefined" ){
			url = '/api/register';
		}else{
			url = '/api/update';
		}
		
		
		$(".alertData").html('')
		$.ajax({
			url: url,
			data: form_data,  
			headers:{
					"Authorization": "Bearer " + localStorage.getItem("token"),
				},
			type: 'POST',
			dataType: "json",
			contentType: false,
            processData: false,
			success: function(response) {  
				if(response.success == true){ 
					window.location.href = "/user";
				}else{
					$(".alert-danger").show();
					$.each(response.error, function(index, item) {  
						$(".alertData").append('<div>'+ item +'</div>'); 
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
  
  loadData = function (){ 
	  if($("#user_id").val() != "undefined"){
	  $.ajax({
			url: '/api/useredit/' + $("#user_id").val(), 
			type: 'GET',
			headers:{
					"Authorization": "Bearer " + localStorage.getItem("token"),
			},
			dataType: "json",
			contentType: false,
            processData: false,
			success: function(response) {  
				console.log(response.email)
				$("#name").val(response.name);
				$("#email").val(response.email); 
				$("#images").attr("src", "<?php echo asset('storage/uploads/');?>/" + response.image);
				$("#register").html('Update');
				$("#email").prop("readonly",true);
			},
			error: function(e) {
				$(".alert-danger").show();
				var json = $.parseJSON(e.responseText); 
				$(".alertData").append('<div>'+ json.message +'</div>');  
			},
		}); 
	  }
  }
  loadData() 
   
</script>