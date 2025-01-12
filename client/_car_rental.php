<?php
include_once "../_db.php";
include_once "_class_userWallet.php";
if(isset($_GET['txn_cat'])){
    $_SESSION['txn_category'] = $_GET['txn_cat'];
    $txn_cat = $_SESSION['txn_category'];
}


?>
    

    <div class="container">
     <div class="row">
         <div class="col-sm-12 px-5">
             <form id="formFindCar">

                 <span class="fs-6 fw-light">Looking for a Car Rental Unit?</span> <br>
                 <span class="fs-1 fw-bold">Let's Find you one.</span>

                 <div class="input-group mb-2">

                     <?php
                        $today = new DateTime();
                            // Add 5 days to today's date
                            $fiveDaysLater = $today->modify('+4 days');
                            // Format the date as "YYYY-MM-DD"
                            $formattedDate5 = $fiveDaysLater->format('Y-m-d');
                            // Add 5 days to today's date

                        ?>
                     <input id="f_rent_from_date" name="f_rent_from_date" type="date" class="form-control" min="<?php echo  $formattedDate5;?>" value="<?php echo  $formattedDate5;?>">
                     <span class="input-group-text">To </span>
                     <input id="f_rent_to_date" name="f_rent_to_date" type="date" class="form-control">
                 </div>

                 <div class="mb-2">
                     <select required name="f_region" id="RentSelectRegion" class="form-select">
                         <option value="" class="text-disabled">Region</option>
                     </select>
                 </div>
                 <div class="mb-2">
                     <select required name="f_province" id="RentSelectProvince" class="form-select">
                         <option value="" class="text-disabled">Province</option>
                     </select>
                 </div>
                 <div class="mb-2">
                     <select required name="f_municipality" id="RentSelectMunicipality" class="form-select ">
                         <option value="" class="text-disabled">City/Municipality</option>
                     </select>
                 </div>

                 <button type="submit" class="btn btn-primary d-flex">Look for Available Cars</button>

                 <div id="RentalAlert" class="alert"></div>

             </form>
         </div>
         <div class="col-lg-12">
         <div class="card border-0 shadow w-100">
                    <div class="card-header bg-purple text-light">
                        <h6 class="card-title fw-bold">MY CAR RENTALS</h6>
                    </div>
                    <div class="card-body overflow-scroll" style="height:40vh;">
                        
                        <?php
                        $sql_rental = "SELECT  a.app_txn_id,
                                            a.amount_to_pay,
                                            a.user_id as user,
                                            up.user_firstname,
                                            up.user_lastname,
                                            up.user_mi,
                                            up.user_contact_no,
                                            ii.item_description,
                                            ii.item_price,
                                            DATE_FORMAT(a.book_start_dte, '%m/%d/%Y') AS book_start_dte, -- Format start date
                                            DATE_FORMAT(a.book_end_dte, '%m/%d/%Y') AS book_end_dte,     -- Format end date
                                            DATEDIFF(a.book_end_dte, a.book_start_dte) AS elapseDay,     -- Use DATEDIFF for clarity
                                            a.book_location_id,
                                            c.cityMunDesc AS mun,
                                            p.provDesc AS prov,
                                            r.regDesc AS reg,
                                            a.payment_status as payment_status,
                                            a.txn_status as app_txn_status
                                        FROM  `app_transactions` AS a
                                        JOIN  `user_profile` AS up
                                            ON a.user_id = up.user_id
                                        JOIN  `items_inventory` AS ii
                                            ON a.book_item_inventory_id = ii.items_inventory_id
                                        JOIN  refcitymun AS c
                                            ON SUBSTR(a.book_location_id, 9, 6) = c.citymunCode
                                        JOIN  refprovince AS p
                                            ON SUBSTR(a.book_location_id, 4, 4) = p.provCode
                                        JOIN  refregion AS r
                                            ON SUBSTR(a.book_location_id, 1, 2) = r.regCode
                                       WHERE a.user_id = ? ";
                        
                        $sql_rental_query = query($sql_rental,[USER_LOGGED]); ?>

                        <table class="table table-striped table-responsive overflow-y-scroll" style="height:10vh">
                            <thead>
                                
                                <th class="align-middle d-none d-lg-block d-md-block">Model</th>
                                <th>Plate No.</th>
                                <th>Payment Status</th>
                                <th>Rental Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </thead>
                            
                            <?php 

                            foreach($sql_rental_query as $r){ 
                                extract($r);
                                $userWalletInstance = new UserWallet($user);
                            $userWallet = $userWalletInstance->getBalance();
                            
                            $car = explode(':',$item_description);
                            ?>
                                <tr>
                                   
                                    <td class="d-none d-lg-block d-md-block align-middle"><?php echo $car[2] ; ?></td>
                                    <td class="align-middle"><?php echo $car[4] ;?></td>
                                    <td class="align-middle"> <span class="small <?php echo ((($userWallet < $amount_to_pay) && $payment_status == 'P') || $payment_status == 'P' || $app_txn_status == 'D') ? "text-danger":"text-success" ; ?>">
                                                            <?php  echo ($app_txn_status == 'D') ? "Declined" :  (($payment_status == 'D') ? "Paid" : "Pending $amount_to_pay") ;  ?>  
                                                            </span> 
                                                </td>
                                    <td class="align-middle">
                                            <?php switch($app_txn_status) {
                                                    case 'D': echo "Declined";
                                                    break;
                                                    case 'C': echo "Approved";
                                                    break;
                                                    default: echo "Pending";
                                            }?>
                                    </td>
                                    <td class="align-middle"><?php echo $book_start_dte; ?></td>
                                    <td class="align-middle"><?php echo $book_end_dte; ?></td>
                                    <td class="d-none d-lg-block d-md-block align-middle"><?php echo $elapseDay . " days";?></td>
                                    <td class="align-middle"><span class="d-none d-lg-block d-md-block"><?php echo $reg . ","; ?></span> <?php echo $prov . ", " . $mun ;?></td>
                                   
                                </tr>
                                    
                            <?php }
                        ?>
                            
                        </table>
                    </div>
                </div>

         </div>
     </div>
 </div>