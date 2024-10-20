 <form id="formCarRental">
   <div class=" alert-info mb-3"><b >Reminder: </b> Cars have atleast 36 hours notice for rental. Please plan accordingly. and can only be rented for a maximum of 30 days. Rental Price is rated per day.</div> <br>
    <h3 class="display-3"><?php echo $carmodel; ?></h3>
    <input type="hidden" name="f_car_id" value="<?php echo $car_id;?>">
    <div class="input-group border border-primary rounded border-4 mb-4" style="--bs-border-opacity: .5;">
        <span class="input-group-text">Where to? </span>
        <select required name="f_region" id="RentSelectRegion" class="form-select">
                <option value="" class="text-disabled">Region</option>
        </select>
        <select required name="f_province" id="RentSelectProvince" class="form-select">
                <option value="" class="text-disabled">Province</option>
        </select>
        <select required name="f_municipality" id="RentSelectMunicipality" class="form-select">
                <option value="" class="text-disabled">City/Municipality</option>
        </select>
    </div>
    
    
    <div class="input-group border border-primary rounded border-4" style="--bs-border-opacity: .5;">
        <span class="input-group-text">From </span>
        <?php
        $today = new DateTime();
            // Add 5 days to today's date
            $fiveDaysLater = $today->modify('+4 days');
            // Format the date as "YYYY-MM-DD"
            $formattedDate5 = $fiveDaysLater->format('Y-m-d');
            // Add 5 days to today's date

        ?>
        <input id="f_rent_from_date" name="f_rent_from_date" type="date" class="form-control" min="<?php echo  $formattedDate5;?>" value="<?php echo  $formattedDate5;?>" >
        <span class="input-group-text">To </span>
        <input id="f_rent_to_date" name="f_rent_to_date" type="date" class="form-control">
        <button type="submit" class="btn btn-primary">Book Rental</button>
    </div>
    
    <div id="RentalAlert" class="alert"></div>
</form>



    <div class="container">
        <?php include_once "_map.php";?>
    </div>