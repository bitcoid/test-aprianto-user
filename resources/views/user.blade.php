<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Web Laravel - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{ url('sbadmin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <!-- Custom styles for this template-->
    <link href=" {{ mix('css/app.css') }}" rel="stylesheet">
	<script src="{{ mix('js/app.js') }}"></script>
	
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-lg-12">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
					   
						<div class="m-3"> 
							<a class="btn btn-success text-light" data-toggle="modal" onclick="tambah()" data-target="#mediumModal"
								style="margin-bottom:30px;"
								data-attr="/newuser" title="Create a user"> Register New User
							</a>
							<div class="alert alert-danger alert-dismissible fade show"> 
								<div class="alertData"></div>
							</div>
							<table class="table table-striped yajra-datatable" style="padding-top:10px;">
								<thead class=""> 
									<tr>
										<th>No</th>
										<th>image</th> 
										<th>Name</th> 
										<th>Email</th> 
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table> 
						</div>
                    </div>
                </div>

            </div> 
        </div> 
    </div>
	
	
	<!-- Modal -->
	<!-- medium modal -->
    <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content"> 
                <div class="modal-body" id="mediumBody">
                    <div>
                        <!-- the result to be displayed apply here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
  $(function () {
		$(".alert-danger").hide();
		var table = $('.yajra-datatable').DataTable({
			processing: true,
			serverSide: true, 
			ajax: {
				url : "/api/get_user",
				headers:{
					"Authorization": "Bearer " + localStorage.getItem("token"),
				},
			},
			"pageLength": 10,
			columns: [
				{data: 'id', name: 'id'},
				{data: 'image', name: 'images'},
				{data: 'name', name: 'name'},
				{data: 'email', name: 'email'},
				{
					data: 'action', 
					name: 'action', 
					orderable: true, 
					searchable: true
				},
			],
			
		});
	
		tambah = function(id){
			event.preventDefault();
            let url = ''; 
			if(id === null){
				url= '/newuser';
			}else{
				url= '/newedit/' + id;	
			}
			
            $.ajax({
                url: url,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#mediumModal').modal("show");
                    $('#mediumBody').html(result).show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 2000
            })
		}		

		deletes = function(id){ 
			$.ajax({
				url: '/api/hapus', 
				data:{ 'id' : id },
				headers:{
						"Authorization": "Bearer " + localStorage.getItem("token"),
				},
				type: 'POST',
				dataType: "json", 
				success: function(response) {  
					if(response.success == true){ 
						$(".alert-danger").show();
						$(".alertData").append('<div>'+ response.message +'</div>');
						setTimeout(function() { 
							window.location.href = "/user";		
						}, 2000);						
					}else{
						$(".alert-danger").show();
						$.each(response.error, function(index, item) {  
							$(".alertData").append('<div>'+ item +'</div>'); 
						});
					} 				
				},
				error: function(e) { },
			}); 
		}				
            
  });
</script> 
</html>