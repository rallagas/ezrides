<div class="row">
    <div class="col-12 pt-2">
        <h6 class="fw-bold display-6">Customer Registration</h6>
        <form id="formRegistration">
            <div class="mb-2">
                <input name="f_emailadd" id="f_emailadd" type="email" class="form-control" Placeholder="Email Address" required>
                <span class="opacity-25" id="emailFeedback"></span>
            </div>
            <div class="mb-2">
                <input name="f_username" id="f_username" type="text" class="form-control" Placeholder="Username" required>
                <span  class="opacity-25" id="usernameFeedback"></span>
            </div>
            <div class="mb-2">
                <input name="f_password" id=f_password type="password" class="form-control" Placeholder="Password" required>
                <span class="opacity-25" id="password1Feedback"></span>
            </div>
            <div class="mb-2">
                <input name="f_cpassword" id="f_cpassword" type="password" class="form-control" Placeholder="Confirm Password" required>
                <span class="opacity-25" id="passwordFeedback"></span>
            </div>

            <hr>

            <div class="mb-2">
                <input name="f_fname" type="text" class="form-control" Placeholder="First Name">
            </div>
            <div class="mb-2">
                <input name="f_lname" type="text" class="form-control" Placeholder="Last Name">
            </div>
            <div class="mb-2">
                <input name="f_mname" type="text" class="form-control" maxlength="1" placeholder="Middle Initial">
            </div>
            <div class="mb-2">
                <input name="f_contact" type="text" class="form-control" maxlength="11" Placeholder="Contact No (09XXXXXXXXX)">
            </div>
            <div class="mb-2 pt-2">

                <input name="f_gender" id="f_gender_m" type="radio" class="btn-check" value="M">
                <label for="f_gender_m" class="btn btn-outline-secondary me-2" data-bs-toggle="tooltip" data-bs-title="Male">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gender-male" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M9.5 2a.5.5 0 0 1 0-1h5a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0V2.707L9.871 6.836a5 5 0 1 1-.707-.707L13.293 2zM6 6a4 4 0 1 0 0 8 4 4 0 0 0 0-8" />
                    </svg>
                </label>


                <input name="f_gender" id="f_gender_f" type="radio" class="btn-check" value="F">
                <label for="f_gender_f" class="btn btn-outline-secondary me-2" data-bs-toggle="tooltip" data-bs-title="Female">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gender-female" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V12h2a.5.5 0 0 1 0 1h-2v2.5a.5.5 0 0 1-1 0V13h-2a.5.5 0 0 1 0-1h2V9.975A5 5 0 0 1 3 5" />
                    </svg>
                </label>

                <input name="f_gender" id="f_gender_2" type="radio" class="btn-check" value="2">
                <label for="f_gender_2" class="btn btn-outline-secondary me-2" data-bs-toggle="tooltip" data-bs-title="ambiguous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gender-ambiguous" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.5 1a.5.5 0 0 1 0-1h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-3.45 3.45A4 4 0 0 1 8.5 10.97V13H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V14H6a.5.5 0 0 1 0-1h1.5v-2.03a4 4 0 1 1 3.471-6.648L14.293 1zm-.997 4.346a3 3 0 1 0-5.006 3.309 3 3 0 0 0 5.006-3.31z" />
                    </svg>
                </label>
                <input name="f_gender" id="f_gender_1" type="radio" class="btn-check" value="1">
                <label for="f_gender_1" class="btn btn-outline-secondary me-2" data-bs-toggle="tooltip" data-bs-title="neutral">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gender-neuter" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V15.5a.5.5 0 0 1-1 0V9.975A5 5 0 0 1 3 5" />
                    </svg>
                </label>
                <input name="f_gender" id="f_gender_3" type="radio" class="btn-check" value="3" data-bs-toggle="tooltip" title="Transgender">
                <label for="f_gender_3" class="btn btn-outline-secondary me-2" >
                    <svg data-bs-toggle="tooltip" title="Trans" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gender-neuter" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V15.5a.5.5 0 0 1-1 0V9.975A5 5 0 0 1 3 5" />
                    </svg>
                </label>


            </div>
            <hr>
            <div class="form-check form-switch mb-2">
                <input name="f_rider_status" class="form-check-input" data-bs-toggle="collapse" href="#riderForm" aria-controls="riderForm" type="checkbox" >
                <label class="form-check-label fw-bold" for="flexSwitchCheckDefault">Do you Want to register as a Rider as well?</label>

                <div class="collapse multi-collapse mt-2" id="riderForm">

                    <div class="card card-body">

                        <div class="my-2">

                            <input id="f_r_car_brand" name="f_r_car_brand" type="text" class="form-control" Placeholder="Your Car Model">
                            <div id="suggestCar" class="p-1"></div>

                        </div>

                        <div class="mb-2">
                            <input id="f_r_plate_no" name="f_r_plate_no" type="text" class="form-control" placeholder="Plate No. (XXX-XXXX)">
                        </div>

                        <div class="mb-2">
                            <input id="f_r_license_no" name="f_r_license_no" type="text" class="form-control" placeholder="license No. (XXX-XXXX)">
                        </div>

                    </div>
                </div>
            </div>



            <div class="form-check form-switch mb-2">
                <input name="agreement_Checkbox" class="form-check-input" type="checkbox" role="switch" id="agreement_Checkbox_switch">
                <label class="form-check-label fw-bold" for="agreement_Checkbox">Do you Accept the <a data-bs-toggle="collapse" href="#agreement_Checkbox" aria-controls="agreement_Checkbox">Privacy Terms and conditions</a>?</label>

                <div class="collapse multi-collapse mt-2" id="agreement_Checkbox">

                    <div class="card card-body">

                        <span class="card-caption">

                            <?php include_once "_terms_and_conditions.html";?>

                        </span>

                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-warning">Create Account</button>
            <button class="btn btn-secondary reset-button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-controls="riderForm" type="Reset">Reset Fields</button>
            <a href="?page=loguser" class="btn btn-link">Login Here</a>
        </form>
    </div>
</div>
