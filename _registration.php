<div class="row">
    <div class="col-2"></div>
    <div class="col-8 pt-5">
        <h6 class="fw-bold display-6">Customer Registration</h6>
        <hr>
        <div class="status"></div>
        <form id="formRegistration">
            <div class="mb-3">
                <label for="" class="form-label">E-mail Address</label>
                <input name="f_emailadd" type="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Username</label>
                <input name="f_username" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Password</label>
                <input name="f_password" type="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Confirm Password</label>
                <input name="f_cpassword" type="password" class="form-control" required>
            </div>

            <hr>

            <div class="mb-3">
                <label for="" class="form-label"> First Name</label>
                <input name="f_fname" type="text" class="form-control">
            </div>
            <div class="mb-3">
                <label for="" class="form-label"> Last Name</label>
                <input name="f_lname" type="text" class="form-control">
            </div>
            <div class="mb-3">
                <label for="" class="form-label"> Middle Initial</label>
                <input name="f_mname" type="text" class="form-control" maxlength="1">
            </div>
            <div class="mb-3">
                <label for="" class="form-label"> Contact No</label>
                <input name="f_contact" type="text" class="form-control" maxlength="1">
            </div>
            <div class="mb-3">
                <label for="" class="form-label"> Gender </label>
                <div class="form-check">
                    <input name="f_gender" id="f_gender_m" type="radio" class="form-check-input" value="M">
                    <label for="f_gender_m" class="form-check-label"> Male </label>
                </div>
                <div class="form-check">
                    <input name="f_gender" id="f_gender_f" type="radio" class="form-check-input" value="M">
                    <label for="f_gender_f" class="form-check-label"> Female </label>
                </div>
                <div class="form-check">
                    <input name="f_gender" id="f_gender_x" type="radio" class="form-check-input" value="X">
                    <label for="f_gender_X" class="form-check-label"> Rather Not Say </label>
                </div>
            </div>
            <hr>
            <div class="form-check form-switch mb-3">
                <input name="f_rider_status" class="form-check-input"  data-bs-toggle="collapse" href="#riderForm" aria-controls="riderForm" type="radio" role="switch" id="flexSwitchCheckDefault">
                <label class="form-check-label fw-bold" for="flexSwitchCheckDefault">Do you Want to register as a Rider as well?</label>
                
                <div class="collapse multi-collapse" id="riderForm">
                 
                  <div class="card card-body">
                        
                        <div class="mx-3">
                            
                            <label for="" class="form-label"> Car Brand</label>
                            <input id="f_r_car_brand" name="f_r_car_brand" type="text" class="form-control">
                            <div id="suggestCar"></div>
                            
                        </div>
                        
                  </div>
                </div>
            </div>



            <button type="submit" class="btn btn-warning">Create Account</button>
            <button class="btn btn-secondary reset-button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-controls="riderForm" type="Reset">Reset Fields</button>
            <a href="?page=loguser" class="btn btn-link">Login Here</a>
        </form>
    </div>
    <div class="col-2"></div>
</div>