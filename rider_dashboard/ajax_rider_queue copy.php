 <?php include_once "../_db.php";
      include_once "../_sql_utility.php";

       $rider_logged=$_SESSION['user_id'];

        $check_queue_if_im_in = select_data(
        CONN, 
        "angkas_rider_queue", 
        "angkas_rider_id = {$rider_logged} AND DATE(queue_date) = CURRENT_DATE AND queue_status = 'A'"
    );

    if (empty($check_queue_if_im_in)) {
        $tbl = "angkas_rider_queue";
        $dta = ["angkas_rider_id" => $rider_logged];
        insert_data(CONN, $tbl, $dta);
        echo 0;
    } else {
        foreach ($check_queue_if_im_in as $q) {
            $que_id = $q['angkas_rider_queue_id'];
            $qarr = [$que_id];
            $check_queue_num = query(CONN, "SELECT COUNT(*) AS queue_num 
                                              FROM angkas_rider_queue 
                                              WHERE DATE(queue_date) = CURRENT_DATE 
                                                AND angkas_rider_queue_id <= ?", 
                                        $qarr);

            if ($check_queue_num !== false && is_array($check_queue_num)) {
                foreach ($check_queue_num as $que_num) {
                    echo $que_num['queue_num'];
                    $current_queue = $que_num['queue_num'];
                }
            } 
        }
    }
?>








<div class="booking-card">
                                        <img src="${booking.user_profile_image}" alt="${booking.user_firstname} ${booking.user_lastname}" class="profile-image" />
                                        <h3>${booking.user_firstname} ${booking.user_mi}. ${booking.user_lastname}</h3>
                                        <p><strong>Booking Reference:</strong> ${booking.angkas_booking_reference}</p>
                                        <p><strong>From:</strong> ${booking.form_from_dest_name}</p>
                                        <p><strong>To:</strong> ${booking.form_to_dest_name}</p>
                                        <p><strong>ETA Duration:</strong> ${booking.form_ETA_duration} mins</p>
                                        <p><strong>Total Distance:</strong> ${booking.form_TotalDistance} km</p>
                                        <p><strong>Estimated Cost:</strong> Php ${booking.form_Est_Cost}</p>
                                        <p><strong>Contact:</strong> ${booking.user_contact_no} (${booking.user_email_address})</p>
                                        <p><strong>Gender:</strong> ${booking.user_gender}</p>
                                        <p><strong>Date Booked:</strong> ${booking.date_booked}</p>
                                        <p><strong>Status:</strong> ${booking.booking_status}</p>
                                    </div>