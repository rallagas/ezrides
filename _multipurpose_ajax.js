$(document).ready(function() {
    
    // Logout button click event
    $("#btnUserLogout").on("click", function() {
        console.log("Logout button clicked");
        $.ajax({
            url: '../_action_logout_user.php',
            type: 'POST',
            success: function(response) {
                console.log('Session ended successfully:', response);
                window.location.href = '../index.php?page=loguser';
            },
            error: function(xhr, status, error) {
                console.error('Failed to end session:', error);
                alert('An error occurred while trying to log you out. Please try again.');
            }
        });
    });

    // Input event for vehicle suggestions
    $('input#f_r_car_brand').on('keyup', function() {
        console.log("User typing in car brand input");
        let str = $(this).val();
        if (str.length > 1) {
            $.ajax({
                url: '_ajax_get_vehicle_models.php',
                type: 'GET',
                data: { search: str },
                success: function(data) {
                    console.log("Suggestions received:", data);
                    $('#suggestCar').empty();
                    data.forEach(function(car) {
                        $('#suggestCar').append(`<div class="suggest-item">${car.vehicle_model}</div>`);
                    });
                },
                error: function(xhr, status, error) {
                    $('#suggestCar').empty();
                    console.error('Error fetching suggestions:', error);
                }
            });
        } else {
            $('#suggestCar').empty();
        }
    });

    // Autocomplete click event for suggestion items
    $('#suggestCar').on('click', '.suggest-item', function() {
        let selectedCar = $(this).text();
        console.log("Suggestion clicked:", selectedCar);
        $('#f_r_car_brand').val(selectedCar);
        $('#suggestCar').empty();
    });
    
    
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
