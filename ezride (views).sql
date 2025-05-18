
CREATE OR REPLACE VIEW `booking_shop_combined` AS 
SELECT 
  `so`.`order_id` AS `shop_order_id`, 
  `so`.`shop_order_ref_num` AS `shop_order_reference_number`, 
  `so`.`voucher_code` AS `shop_order_voucher_code`, 
  `so`.`Shipping_fee` AS `shop_order_shipping_fee`, 
  `so`.`shipping_name` AS `shop_order_shipping_name`, 
  `so`.`shipping_address` AS `shop_order_shipping_address`, 
  `so`.`shipping_address_coor` AS `shop_order_shipping_address_coordinates`, 
  `so`.`shipping_phone` AS `shop_order_shipping_phone`, 
  `so`.`user_id` AS `shop_order_user_id`, 
  `so`.`rider_id` AS `shop_order_rider_id`, 
  `so`.`item_id` AS `shop_order_item_id`, 
  `so`.`quantity` AS `shop_order_quantity`, 
  `so`.`amount_to_pay` AS `shop_order_amount_to_pay`, 
  `so`.`order_date` AS `shop_order_date`, 
  `so`.`delivery_status` AS `shop_order_delivery_status`, 
  `so`.`payment_status` AS `shop_order_payment_status`, 
  `so`.`order_state_ind` AS `shop_order_state_indicator`, 
  `so`.`order_special_instructions` AS `shop_order_special_instructions`, 
  `ab`.`angkas_booking_id` AS `angkas_booking_id`, 
  `ab`.`angkas_booking_reference` AS `angkas_booking_reference`, 
  `ab`.`transaction_category_id` AS `angkas_booking_transaction_category_id`, 
  `ab`.`angkas_rider_user_id` AS `angkas_booking_rider_user_id`, 
  `ab`.`form_from_dest_name` AS `angkas_booking_from_destination_name`, 
  `ab`.`user_currentLoc_lat` AS `angkas_booking_user_current_location_latitude`, 
  `ab`.`user_currentLoc_long` AS `angkas_booking_user_current_location_longitude`, 
  `ab`.`form_to_dest_name` AS `angkas_booking_to_destination_name`, 
  `ab`.`formToDest_long` AS `angkas_booking_to_destination_longitude`, 
  `ab`.`formToDest_lat` AS `angkas_booking_to_destination_latitude`, 
  `ab`.`form_ETA_duration` AS `angkas_booking_eta_duration`, 
  `ab`.`form_TotalDistance` AS `angkas_booking_total_distance`, 
  `ab`.`form_Est_Cost` AS `angkas_booking_estimated_cost`, 
  `ab`.`date_booked` AS `angkas_booking_date_booked`, 
  `ab`.`booking_status` AS `angkas_booking_status`, 
  `ab`.`payment_status` AS `angkas_booking_payment_status`, 
  `ab`.`rating` AS `angkas_booking_rating`, 
  `up`.`user_profile_id` AS `user_profile_id`, 
  `up`.`user_firstname` AS `user_firstname`, 
  `up`.`user_lastname` AS `user_lastname`, 
  `up`.`user_mi` AS `user_middle_initial`, 
  `up`.`user_contact_no` AS `user_contact_number`, 
  `up`.`user_gender` AS `user_gender`, 
  `up`.`user_email_address` AS `user_email_address`, 
  `up`.`user_profile_image` AS `user_profile_image`, 
  `up`.`rider_plate_no` AS `user_rider_plate_number`, 
  `up`.`rider_license_no` AS `user_rider_license_number` 
FROM 
  (
    (
      (
        (
          `shop_orders` `so` 
          left join `angkas_bookings` `ab` on(
            `so`.`shop_order_ref_num` = `ab`.`shop_order_reference_number`
          )
        ) 
        left join `users` `u` on(`ab`.`user_id` = `u`.`user_id`)
      ) 
      left join `users` `ur` on(
        `ab`.`angkas_rider_user_id` = `ur`.`user_id` 
        and `ur`.`t_rider_status` = 1
      )
    ) 
    left join `user_profile` `up` on(`u`.`user_id` = `up`.`user_id`)
  );


CREATE OR REPLACE VIEW `booking_shop_header_view` AS 
SELECT 
  `ab`.`angkas_booking_id` AS `angkas_booking_id`, 
  `ab`.`angkas_booking_reference` AS `angkas_booking_reference`, 
  `ab`.`shop_order_reference_number` AS `shop_order_reference_number`, 
  `ab`.`user_id` AS `customer_user_id`, 
  `ab`.`angkas_rider_user_id` AS `rider_user_id`, 
  sum(`so`.`amount_to_pay`) AS `total_amount_to_pay`, 
  max(`ab`.`form_ETA_duration`) AS `angkas_booking_eta_duration`, 
  max(`ab`.`form_TotalDistance`) AS `angkas_booking_total_distance`, 
  max(`ab`.`form_Est_Cost`) AS `angkas_booking_estimated_cost`, 
  avg(`ab`.`rating`) AS `angkas_booking_avg_rating`, 
  `u`.`t_username` AS `customer_username`, 
  `u`.`t_user_type` AS `customer_user_type`, 
  `up`.`user_firstname` AS `customer_firstname`, 
  `up`.`user_lastname` AS `customer_lastname`, 
  `ab`.`booking_status` AS `booking_status`, 
  `ab`.`payment_status` AS `payment_status` 
FROM 
  (
    (
      (
        `angkas_bookings` `ab` 
        join `shop_orders` `so` on(
          `ab`.`shop_order_reference_number` = `so`.`shop_order_ref_num`
        )
      ) 
      join `users` `u` on(`ab`.`user_id` = `u`.`user_id`)
    ) 
    join `user_profile` `up` on(`ab`.`user_id` = `up`.`user_id`)
  ) 
GROUP BY 
  `ab`.`angkas_booking_reference`, 
  `ab`.`shop_order_reference_number`, 
  `ab`.`user_id`, 
  `ab`.`angkas_rider_user_id`, 
  `u`.`t_username`, 
  `u`.`t_user_type`, 
  `up`.`user_firstname`, 
  `up`.`user_lastname`, 
  `ab`.`booking_status`, 
  `ab`.`payment_status`;


CREATE OR REPLACE VIEW `shop_booking_header_view` AS 
SELECT 
  `so`.`shop_order_ref_num` AS `shop_order_reference_number`, 
  `ab`.`angkas_booking_reference` AS `angkas_booking_reference`, 
  `so`.`user_id` AS `customer_user_id`, 
  `ab`.`angkas_rider_user_id` AS `rider_user_id`, 
  concat(
    coalesce(`ri`.`user_lastname`, ''), 
    ', ', 
    coalesce(`ri`.`user_firstname`, '')
  ) AS `rider_name`, 
  sum(`so`.`amount_to_pay`) AS `shop_cost`, 
  max(`ab`.`form_ETA_duration`) AS `angkas_booking_eta_duration`, 
  max(`ab`.`form_TotalDistance`) AS `angkas_booking_total_distance`, 
  max(`ab`.`form_Est_Cost`) AS `angkas_booking_estimated_cost`, 
  avg(`ab`.`rating`) AS `angkas_booking_avg_rating`, 
  `u`.`t_username` AS `customer_username`, 
  `u`.`t_user_type` AS `customer_user_type`, 
  `up`.`user_firstname` AS `customer_firstname`, 
  `up`.`user_lastname` AS `customer_lastname`, 
  `ab`.`booking_status` AS `booking_status`, 
  `ab`.`payment_status` AS `booking_payment_status`, 
  `so`.`order_state_ind` AS `order_state_ind`, 
  `sm`.`name` AS `merchant_name`, 
  max(
    date_format(
      `so`.`order_date`, '%m-%d-%Y %H:%i'
    )
  ) AS `order_date`, 
  max(`so`.`payment_status`) AS `shop_payment_status`, 
  CASE WHEN timestampdiff(
    SECOND, 
    `so`.`order_date`, 
    current_timestamp()
  ) < 60 THEN concat(
    timestampdiff(
      SECOND, 
      `so`.`order_date`, 
      current_timestamp()
    ), 
    ' second', 
    case when timestampdiff(
      SECOND, 
      `so`.`order_date`, 
      current_timestamp()
    ) > 1 then 's' else '' end, 
    ' ago'
  ) WHEN timestampdiff(
    MINUTE, 
    `so`.`order_date`, 
    current_timestamp()
  ) < 60 THEN concat(
    timestampdiff(
      MINUTE, 
      `so`.`order_date`, 
      current_timestamp()
    ), 
    ' minute', 
    case when timestampdiff(
      MINUTE, 
      `so`.`order_date`, 
      current_timestamp()
    ) > 1 then 's' else '' end, 
    ' ago'
  ) WHEN timestampdiff(
    HOUR, 
    `so`.`order_date`, 
    current_timestamp()
  ) < 24 THEN concat(
    timestampdiff(
      HOUR, 
      `so`.`order_date`, 
      current_timestamp()
    ), 
    ' hour', 
    case when timestampdiff(
      HOUR, 
      `so`.`order_date`, 
      current_timestamp()
    ) > 1 then 's' else '' end, 
    ' ago'
  ) WHEN timestampdiff(
    DAY, 
    `so`.`order_date`, 
    current_timestamp()
  ) < 30 THEN concat(
    timestampdiff(
      DAY, 
      `so`.`order_date`, 
      current_timestamp()
    ), 
    ' day', 
    case when timestampdiff(
      DAY, 
      `so`.`order_date`, 
      current_timestamp()
    ) > 1 then 's' else '' end, 
    ' ago'
  ) WHEN timestampdiff(
    MONTH, 
    `so`.`order_date`, 
    current_timestamp()
  ) < 12 THEN concat(
    timestampdiff(
      MONTH, 
      `so`.`order_date`, 
      current_timestamp()
    ), 
    ' month', 
    case when timestampdiff(
      MONTH, 
      `so`.`order_date`, 
      current_timestamp()
    ) > 1 then 's' else '' end, 
    ' ago'
  ) ELSE concat(
    timestampdiff(
      YEAR, 
      `so`.`order_date`, 
      current_timestamp()
    ), 
    ' year', 
    case when timestampdiff(
      YEAR, 
      `so`.`order_date`, 
      current_timestamp()
    ) > 1 then 's' else '' end, 
    ' ago'
  ) END AS `elapsed_time` 
FROM 
  (
    (
      (
        (
          (
            (
              `shop_orders` `so` 
              left join `shop_items` `si` on(`so`.`item_id` = `si`.`item_id`)
            ) 
            left join `shop_merchants` `sm` on(
              `si`.`merchant_id` = `sm`.`merchant_id`
            )
          ) 
          left join `angkas_bookings` `ab` on(
            `ab`.`shop_order_reference_number` = `so`.`shop_order_ref_num`
          )
        ) 
        left join `users` `u` on(`ab`.`user_id` = `u`.`user_id`)
      ) 
      left join `user_profile` `up` on(`ab`.`user_id` = `up`.`user_id`)
    ) 
    left join `user_profile` `ri` on(
      `ab`.`angkas_rider_user_id` = `ri`.`user_id`
    )
  ) 
GROUP BY 
  `so`.`shop_order_ref_num`, 
  `so`.`order_state_ind`, 
  `ab`.`angkas_booking_reference`, 
  `so`.`user_id`, 
  `ab`.`angkas_rider_user_id`, 
  `u`.`t_username`, 
  `u`.`t_user_type`, 
  `up`.`user_firstname`, 
  `up`.`user_lastname`, 
  `ab`.`booking_status`, 
  `ab`.`payment_status`, 
  concat(
    coalesce(`ri`.`user_lastname`, ''), 
    ', ', 
    coalesce(`ri`.`user_firstname`, '')
  );


CREATE OR REPLACE VIEW `shop_item_merchant_view` AS 
SELECT 
  `so`.`shop_order_ref_num` AS `shop_order_ref_num`, 
  `si`.`item_name` AS `item_name`, 
  `si`.`quantity` AS `quantity`, 
  `si`.`price` AS `price`, 
  `sm`.`name` AS `merchant_name`, 
  `sm`.`address` AS `merchant_address`, 
  `so`.`user_id` AS `customer_user_id` 
FROM 
  (
    (
      `shop_orders` `so` 
      join `shop_items` `si` on(`so`.`item_id` = `si`.`item_id`)
    ) 
    join `shop_merchants` `sm` on(
      `si`.`merchant_id` = `sm`.`merchant_id`
    )
  ) 
WHERE 
  `so`.`order_state_ind` <> 'C';
-- --------------------------------------------------------
CREATE OR REPLACE VIEW `view_angkas_bookings` AS 
SELECT 
  `ab`.`transaction_category_id` AS `transaction_category_id`, 
  `tc`.`txn_prefix` AS `txn_prefix`, 
  `ab`.`angkas_booking_id` AS `angkas_booking_id`, 
  `ab`.`angkas_booking_reference` AS `angkas_booking_reference`, 
  `ab`.`user_id` AS `customer_user_id`, 
  `ab`.`angkas_rider_user_id` AS `rider_user_id`, 
  `ab`.`form_from_dest_name` AS `form_from_dest_name`, 
  `ab`.`user_currentLoc_lat` AS `user_currentLoc_lat`, 
  `ab`.`user_currentLoc_long` AS `user_currentLoc_long`, 
  `ab`.`form_to_dest_name` AS `form_to_dest_name`, 
  `ab`.`formToDest_long` AS `formToDest_long`, 
  `ab`.`formToDest_lat` AS `formToDest_lat`, 
  `ab`.`form_ETA_duration` AS `form_ETA_duration`, 
  `ab`.`form_TotalDistance` AS `form_TotalDistance`, 
  `ab`.`form_Est_Cost` AS `form_Est_Cost`, 
  `ab`.`date_booked` AS `date_booked`, 
  `ab`.`booking_status` AS `booking_status`, 
  `ab`.`payment_status` AS `payment_status`, 
  `ab`.`rating` AS `rating`, 
  CASE WHEN `ab`.`payment_status` = 'P' THEN 'Pending Payment' WHEN `ab`.`payment_status` = 'D' THEN 'Payment Declined' WHEN `ab`.`payment_status` = 'C' THEN 'Paid' END AS `payment_status_text`, 
  `up`.`user_firstname` AS `customer_firstname`, 
  `up`.`user_lastname` AS `customer_lastname`, 
  `up`.`user_mi` AS `customer_mi`, 
  `up`.`user_gender` AS `customer_gender`, 
  `up`.`user_contact_no` AS `customer_contact_no`, 
  `up`.`user_email_address` AS `customer_email_address`, 
  `up`.`user_profile_image` AS `customer_profile`, 
  `rp`.`user_firstname` AS `rider_firstname`, 
  `rp`.`user_lastname` AS `rider_lastname`, 
  CASE WHEN `ab`.`booking_status` = 'P' THEN 'Waiting for Driver' WHEN `ab`.`booking_status` = 'A' THEN 'We Found you a Driver' WHEN `ab`.`booking_status` = 'R' THEN 'Driver Arrived in Your Location' WHEN `ab`.`booking_status` = 'I' THEN 'In Transit' WHEN `ab`.`booking_status` = 'C' THEN 'Completed' WHEN `ab`.`booking_status` = 'F' THEN 'Pending Payment' WHEN `ab`.`booking_status` = 'D' THEN 'Done' END AS `booking_status_text` 
FROM 
  (
    (
      (
        (
          `angkas_bookings` `ab` 
          join `user_profile` `up` on(`ab`.`user_id` = `up`.`user_id`)
        ) 
        join `users` `u` on(`up`.`user_id` = `u`.`user_id`)
      ) 
      join `txn_category` `tc` on(
        `ab`.`transaction_category_id` = `tc`.`txn_category_id`
      )
    ) 
    left join `user_profile` `rp` on(
      `ab`.`angkas_rider_user_id` = `rp`.`user_id`
    )
  ) 
ORDER BY 
  `ab`.`angkas_booking_id` ASC;

COMMIT;
