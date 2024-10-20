$(document).ready(function(){

    


// $('input#f_rent_from_date').on('change', function() {
        var selectedDate = $('input#f_rent_from_date').val();
        var minDate = new Date(selectedDate);
        var maxDate = new Date(selectedDate);
        minDate.setDate(minDate.getDate() + 2); // Add 2 days
        maxDate.setDate(maxDate.getDate() + 30); // Add 30 days
        $('input#f_rent_to_date').attr('max', maxDate.toISOString().slice(0, 10));
        $('input#f_rent_to_date').attr('min', minDate.toISOString().slice(0, 10));
  //  });
  
   
   //load Regions 
    $("select#RentSelectRegion").focus();
    $("select#RentSelectRegion").html("<option>Select Region</option>");
    $.ajax({
    url: "ajax_get_location_info.php?get_region_list",
    success: function(data) {

        $("select#RentSelectRegion").append(data);
            
    }
    });	
    
    
    
    
//Load Provinces once Region is selected
$('#RentSelectRegion').change(function(e){
    //clear municipality for every change of region
    $("select#RentSelectMunicipality").html("");
     $("select#RentSelectProvince").html("<option>Select Province</option>");
 
    
  $.post("ajax_get_location_info.php",
  {
    get_province_list: $('#RentSelectRegion').val(),
   
  },
  function(data){
      //load province list in the selection
       $("select#RentSelectProvince").append(data);
       $("select#RentSelectProvince").focus();
						
  });

	e.preventDefault();			
}); 
    
    
//Load Municipalities once Province is selected
$('#RentSelectProvince').change(function(e){
    
    
       $("select#RentSelectMunicipality").html("<option>Select City/Municipality</option>");
    
  $.post("ajax_get_location_info.php",
  {
    get_municipality_list: $('#RentSelectProvince').val(),
   
  },
  function(data){
       
       $("select#RentSelectMunicipality").html(data);
       $("select#RentSelectMunicipality").focus();
						
  });

	e.preventDefault();			
});   
    
    
$("select#RentSelectMunicipality").change(function(e){
    $("#f_rent_from_date").focus();
    e.preventDefault();
});
$("select#f_rent_from_date").change(function(e){
    $("#f_rent_to_date").focus();
    e.preventDefault();
});

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
    
    
$('#formCarRental').submit(function(e){

				$.ajax({
				type: "POST",
				url: "ajax_process_car_rental.php",
				data: $("#formCarRental").serialize(),
				success: function(data){	
			//alert(data);//return false;
                                
                                $("div#RentalAlert").addClass("alert-success mt-3").html("<div class='spinner'></div> Processing...");
                                $("div.spinner").addClass("spinner-border");
                                
                                setTimeout(function(){
                                     $("div#RentalAlert").html(data);;
                                },1000);
						
				        }
				});		
		e.preventDefault();
});
    
    
//find a car module in Rent
$('#formFindCar').submit(function(e){

				$.ajax({
				type: "POST",
				url: "ajax_process_find_car.php",
				data: $("#formFindCar").serialize(),
				success: function(data){	
                                
                           $("#queryresult").html(data);    
						
				        }
				});		
		e.preventDefault();
});
    
    

    


});

