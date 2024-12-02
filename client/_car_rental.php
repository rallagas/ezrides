<?php
include_once "../_db.php";
include_once "../_sql_utility.php";
if(isset($_GET['txn_cat'])){
    $_SESSION['txn_category'] = $_GET['txn_cat'];
    $txn_cat = $_SESSION['txn_category'];
}


?>
    

      <div class="container">
     <div class="row">
         <div class="col-sm-12">
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
     </div>
 </div>