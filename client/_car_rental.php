 <form id="formFindCar">
   
   <span class="fs-6 fw-light">Looking for a Car Rental Unit?</span> <br>
   <span class="fs-1 fw-bold">Let's Find you one.</span>
   
    <div class="input-group border border-primary rounded border-4 mb-4" style="--bs-border-opacity: .5;" >
        <span class="input-group-text">From </span>
        <?php
        $today = new DateTime();
            // Add 5 days to today's date
            $fiveDaysLater = $today->modify('+4 days');
            // Format the date as "YYYY-MM-DD"
            $formattedDate5 = $fiveDaysLater->format('Y-m-d');
            // Add 5 days to today's date

        ?>
        <input id="f_rent_from_date" name="f_rent_from_date" type="date" class="form-control form-control-lg" min="<?php echo  $formattedDate5;?>" value="<?php echo  $formattedDate5;?>" >
        <span class="input-group-text">To </span>
        <input id="f_rent_to_date" name="f_rent_to_date" type="date" class="form-control form-control-lg">
    </div>
   
   
   
    
    <div class="input-group border border-primary rounded border-4 mb-4" style="--bs-border-opacity: .5;">
        <span class="input-group-text">Where to? </span>
        <select required name="f_region" id="RentSelectRegion" class="form-select form-select-lg">
                <option value="" class="text-disabled">Region</option>
        </select>
        <select required name="f_province" id="RentSelectProvince" class="form-select form-select-lg">
                <option value="" class="text-disabled">Province</option>
        </select>
        <select required name="f_municipality" id="RentSelectMunicipality" class="form-select form-select-lg">
                <option value="" class="text-disabled">City/Municipality</option>
        </select>
        
        
        <button type="submit" class="btn btn-primary">Look for Available Cars</button>
    </div>
    
    
   
    
    <div id="RentalAlert" class="alert"></div>
    
</form>

