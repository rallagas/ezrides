<?php
include_once "_db.php";
include_once "_functions.php";
include_once "url_checker.php";
?>


<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
   <div class="container-fluid">
       <div class="row">
           <div id="header" class="col-2 offset-1">
              <img src="icons/ezrides.png" alt="" class="img-fluid mt-4">
           </div>
           
           <div class="col-2 offset-4">
               <a href="" class="btn btn-outline-secondary mt-5 float-end">Join Us</a>
           </div>
           <div class="col-2">
               <a  class="btn btn-secondary bg-purple mt-5 float-start" role="button" data-bs-toggle="collapse" href="#collapseLogin" aria-expanded="false" aria-controls="collapseLogin">Sign In</a>
           </div>
       </div>
       <div class="row">
          <div class="col-4 offset-2">
              <h1 class="fw-bold display-3 mt-5"> Receive orders, dispatch drivers, and track deliveries easily </h1>
              <p class="fw-light">Manage your courier business and provide better customer service without the hassle.</p>
          </div>
          
           <div class="col-3 card mt-0 collapse px-0 offset-2" id="collapseLogin">
             
                  <form id="formUserLog">
                   <div class="card-header bg-purple text-white">
                      <div id="logstatus"></div>
                       <h6 class="card-title mt-2">Sign In as Customer</h6>
                     
                   </div>
                   <div class="card-body">
                       
                           <div class="mb-3">
                               <label for="" class="form-label"> Username or Email
                                   <input type="text" name="log_username" class="form-control w-100">
                               </label>
                           </div>
                           <div class="mb-3">
                               <label for="" class="form-label"> Password
                                   <input type="password" name="log_password" class="form-control">
                               </label>
                           </div>
                   
                       <button id="loginButton" class="btn btn-secondary mb-0" type="submit">Login</button>
                     
                           <a class="btn btn-link" role="button" data-bs-toggle="collapse" href="#collapseRegister" aria-expanded="false" aria-controls="collapseRegister">Create Account</a>
                     
                   </div>
                 </form>
           </div>
           
           <div class="col-8 offset-2 collapse" id="collapseRegister">
               <?php include "_registration.php";?>
           </div>
       </div>
       
       
       <div class="row bg-purple mt-5 text-white">
           <div class="col-8 offset-2">
             <div class="p-5">
               <h3 class="text-center m-5">
                   Offer an easy way for customers to order and track their deliveries
               </h3>
               
               
             </div>
           </div>
           <div class="col-4 offset-4">
               <a href="" class="btn rounded text-white">
                   <img src="icons/take-away.png" alt="" class="rounded-circle img-fluid w-25">
               </a>
           </div>
       </div>
       
   </div>
    
</body>
  
   <script src="js/bootstrap.min.js"></script>
   <script src="js/jquery-3.5.1.min.js"></script>
   <script>
    $(document).ready(function(){
        
        var spinner="<div class='spinner-border spinner-border-sm'></div>";
        var grower="<div class='spinner-grow spinner-grow-sm'></div>";
         
        
        $('form#formRegistration').submit(function(e){

				$.ajax({
				type: "POST",
				url: "_action_register_user.php",
				data: $("form#formRegistration").serialize(),
				success: function(data){	
			//alert(data);//return false;
                            if(data){
                              
                                $("button.reset-button").click();
                              $("div.status").addClass("alert alert-success").html(data);
                            }
				        }
				});		
		e.preventDefault();
        });
         $('form#formUserLog').submit(function(e){

				$.ajax({
				type: "POST",
				url: "_action_log_user.php",
				data: $("form#formUserLog").serialize(),
				success: function(data){
                            if(data=1){
                                  $("#loginButton").removeClass("btn-secondary").addClass("btn-success").html("Loading..."+spinner);
                                setTimeout(function(){
                                    location.assign("client/?page=user");
                                },2500);
                            }
				        }
				});		
		e.preventDefault();
        });
        
        
    });
    </script>
</html>