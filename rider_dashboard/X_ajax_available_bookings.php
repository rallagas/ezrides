<?php

$list_of_need_rider = select_data("angkas_bookings","angkas_rider_user_id=NULL AND DATE(date_booked)=CURRENT_DATE and booking_status='P' ","angkas_booking_id");

if(empty($list_of_need_rider)){
    echo "";
}
else{
    foreach($list_of_need_rider as $cx){ ?>
        
        <div class="card col-3">
            
            
        </div>
        
    <?php }
}

?>
