SELECT
    ab.transaction_category_id AS transaction_category_id,
    tc.txn_prefix AS txn_prefix,
    ab.angkas_booking_id AS angkas_booking_id,
    ab.angkas_booking_reference AS angkas_booking_reference,
    ab.user_id AS customer_user_id,
    ab.angkas_rider_user_id AS rider_user_id,
    ab.form_from_dest_name AS form_from_dest_name,
    ab.user_currentLoc_lat AS user_currentLoc_lat,
    ab.user_currentLoc_long AS user_currentLoc_long,
    ab.form_to_dest_name AS form_to_dest_name,
    ab.formToDest_long AS formToDest_long,
    ab.formToDest_lat AS formToDest_lat,
    ab.form_ETA_duration AS form_ETA_duration,
    ab.form_TotalDistance AS form_TotalDistance,
    ab.form_Est_Cost AS form_Est_Cost,
    ab.date_booked AS date_booked,
    ab.booking_status AS booking_status,
    ab.payment_status AS payment_status,
    ab.rating AS rating,
    CASE WHEN ab.payment_status = 'P' THEN 'Pending Payment' WHEN ab.payment_status = 'D' THEN 'Payment Declined' WHEN ab.payment_status = 'C' THEN 'Paid' END AS payment_status_text,
up.user_firstname AS customer_firstname,
up.user_lastname AS customer_lastname,
up.user_mi AS customer_mi,
up.user_gender AS customer_gender,
up.user_contact_no AS customer_contact_no,
up.user_email_address AS customer_email_address,
up.user_profile_image AS customer_profile,
rp.user_firstname AS rider_firstname,
rp.user_lastname AS rider_lastname,
rp.user_id AS rider_user_id,
ru.t_username as rider_username
CASE WHEN ab.booking_status = 'P' THEN 'Waiting for Driver' WHEN ab.booking_status = 'A' THEN 'Driver Found' WHEN ab.booking_status = 'R' THEN 'Driver Arrived in Your Location' WHEN ab.booking_status = 'I' THEN 'In Transit' WHEN ab.booking_status = 'C' THEN 'Completed' WHEN ab.booking_status = 'F' THEN 'Pending Payment' WHEN ab.booking_status = 'D' THEN 'Done'
END AS booking_status_text
FROM
    (
        (
            (
                (
                    angkas_bookings as ab
                JOIN user_profile as up
                ON
                    (ab.user_id = up.user_id)
                )
            JOIN users as u
            ON
                (up.user_id = u.user_id)
            )
        JOIN txn_category as tc
        ON
            (
                ab.transaction_category_id = tc.txn_category_id
            )
        )
    LEFT JOIN user_profile as rp
    ON
        (
            ab.angkas_rider_user_id = rp.user_id
        )
    LEFT JOIN users as ru
    ON (
            rp.user_id = ru.user_id
        )
    )
    
ORDER BY
    ab.angkas_booking_id
DESC
    