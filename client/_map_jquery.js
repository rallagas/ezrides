$(document).ready(function () {

    function chkBooking() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "ajax_get_current_booking.php",
            dataType: "json",
            success: function (data) {
                let returnVal = 0;

                if (data.hasBooking) {
                    const booking = data.booking;
                    const currentDate = new Date();
                    const dateBooked = new Date(booking.date_booked); 
                    const elapsedTimeInMinutes = Math.floor((currentDate - dateBooked) / (1000 * 60));

                    $("#formFindAngkas").hide();
                    const bookingHtml = ` 
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="row">Booking #</th>
                                    <td class="text-success fw-bold">${booking.angkas_booking_reference}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Booked #</th>
                                    <td class="text-success fw-bold">${elapsedTimeInMinutes} min ago.</td>
                                </tr>
                                <tr>
                                    <th scope="row">Fare
                                            <button class="btn btn-outline-secondary">Pay</button>
                                    </th>
                                    <td class="text-secondary fw-bold">Php ${booking.form_Est_Cost} ( ${booking.payment_status_text} )
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Origin</th>
                                    <td class="fw-semibold">${booking.form_from_dest_name}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Destination</th>
                                    <td class="fw-semibold">${booking.form_to_dest_name}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td class="fw-semibold text-danger">${booking.booking_status_text}</td>
                                </tr>
                                ${booking.booking_status_text !== "Waiting for Driver" 
                                    ? `<tr>
                                           <th scope="row">Driver</th>
                                           <td class="fw-semibold">${booking.rider_firstname}, ${booking.rider_lastname}</td>
                                       </tr>`
                                    : `<tr>
                                           <th scope="row">Driver</th>
                                           <td>
                                               <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                                               <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                                               <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                                           </td>
                                       </tr>`
                                }
                            </tbody>
                        </table>
                    `;
                    $("div#currentBookingInfo").html(bookingHtml);
                    $("#infoAlert").html(bookingHtml);

                    returnVal = 1; // Set returnVal to 1 if there's a booking
                } else {
                    $("#formFindAngkas").show();
                    const noBookingHtml = `<div class="alert alert-info">${data.message}</div>`;
                    $("div#currentBookingInfo").html(noBookingHtml);
                    returnVal = 0; // Set returnVal to 0 if no booking
                }
                
                resolve(returnVal);
            },
            error: function (xhr, status, error) {
                console.error("Error loading booking:", error);
                reject(error);
            }
        });
    });
}

    // Check for booking every 1200 ms
chkBooking().then(result => {
    console.log("Booking check result:", result);
}).catch(error => {
    console.error("Error checking booking:", error);
});
    
    
    $(".add-destination-button").click(function () {
        $("#findMeARiderBTN").removeClass("d-none");
        $("#btnRideInfo").click();
        chkBooking();
    });
    
    $("#findMeARiderBTN").on("click",function(){
        let chkBookingIntervalId = setInterval(chkBooking, 1000 );
         setInterval(chkBooking, 1000 );
    });
    
    setInterval(chkBooking, 1000 );
    

});