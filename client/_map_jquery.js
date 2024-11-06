$(document).ready(function(){
  
    function chkBooking(){
        // Get the booking reference (or ID) from the hidden input field
        $.ajax({
            url: "ajax_get_current_booking.php",
            success: function(data) {
                console.log(data); // Log the response from the server
                $("div#currentBookingInfo").html(data);
                $("#infoAlert").html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading booking:", error);
            }
        });
    
    }
    
    $(".add-destination-button").click(function(){
        $("#findMeARiderBTN").removeClass('d-none');
    });
    
    setInterval(chkBooking,1200);
    
});