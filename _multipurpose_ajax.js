$(document).ready(function () {

    // Logout button click event
    $("#btnUserLogout").on("click", function () {
        console.log("Logout button clicked");
        $.ajax({
            url: '../_action_logout_user.php',
            type: 'POST',
            success: function (response) {
                console.log('Session ended successfully:', response);
                window.location.href = '../index.php?page=loguser';
            },
            error: function (xhr, status, error) {
                console.error('Failed to end session:', error);
                alert('An error occurred while trying to log you out. Please try again.');
            }
        });
    });

    // Validate Username
    $('#f_username').on('input', function () {
        let username = $(this).val();

        // Send AJAX request only if there's input


        if (username.length > 0) {
            if (username.length <= 4) {
                $('#f_username').addClass('is-invalid');
                $('#usernameFeedback').addClass('badge text-bg-danger').text("Must be 4-20 Characters");
            } else {
                $.ajax({
                    type: "POST",
                    url: "_ajax_check_user.php",
                    data: {
                        username: username
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.exists) {
                            $('#f_username').addClass('is-invalid');
                            $('#usernameFeedback').addClass('badge text-bg-danger').text("Username is already taken");
                        } else {
                            $('#f_username').removeClass('is-invalid');
                            $('#usernameFeedback').removeClass('badge text-bg-danger').text("");
                        }
                    }
                });
            }
        } else {
            $('#f_username').removeClass('is-invalid');
            $('#usernameFeedback').removeClass('badge text-bg-danger').text("");
        }
    });

    // Validate Email
    $('#f_emailadd').on('input', function () {
        let email = $(this).val();

        // Send AJAX request only if there's input
        if (email.length > 0) {
            if (email.length <= 4) {
                $('#f_emailadd').addClass('is-invalid');
                $('#emailFeedback').addClass('badge text-bg-danger').text("Must be atleast 5 characters");
            } else {

                $.ajax({
                    type: "POST",
                    url: "_ajax_check_user.php",
                    data: {
                        email: email
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.exists) {
                            $('#f_emailadd').addClass('is-invalid');
                            $('#emailFeedback').addClass('badge text-bg-danger').text("Email is already in use");
                        } else {
                            $('#f_emailadd').removeClass('is-invalid');
                            $('#emailFeedback').removeClass('badge text-bg-danger').text("");
                        }
                    }
                });
            }
        } else {
            $('#f_emailadd').removeClass('is-invalid');
            $('#emailFeedback').removeClass('badge text-bg-danger').text("");
        }
    });

    // Password validation and matching
    $("#f_cpassword").on('input', function () {
        let p1 = $("#f_password").val();
        let p2 = $(this).val();

        if (p1.length < 6) {
            $('#f_password').removeClass('is-valid').addClass('is-invalid');
            $('#password1Feedback').text("Password needs to be more than 5 characters!");
        } else {
            $('#f_password').removeClass('is-invalid').addClass('is-valid');
            $('#password1Feedback').empty();
        }

        if (p2.length > 0) {
            if (p1 === p2) {
                $('#f_password').addClass('is-valid');
                $(this).removeClass('is-invalid').addClass('is-valid');
                $('#passwordFeedback').text("Password Matched!");
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                $('#passwordFeedback').text("Passwords Do not Match!");
            }
        }
    });

    // Vehicle suggestions
    $('input#f_r_car_brand').on('keyup', function () {
        console.log("User typing in car brand input");
        let str = $(this).val();
        if (str.length > 1) {
            $.ajax({
                url: '_ajax_get_vehicle_models.php',
                type: 'GET',
                data: {
                    search: str
                },
                success: function (data) {
                    console.log("Suggestions received:", data);
                    $('#suggestCar').empty();
                    data.forEach(function (car) {
                        $('#suggestCar').append(`<button class="suggest-item badge btn text-secondary btn-light m-1">${car.vehicle_model}</button>`);
                    });
                },
                error: function (xhr, status, error) {
                    $('#suggestCar').empty();
                    console.error('Error fetching suggestions:', error);
                }
            });
        } else {
            $('#suggestCar').empty();
        }
    });

    // Autocomplete click event for suggestion items
    $('#suggestCar').on('click', '.suggest-item', function () {
        let selectedCar = $(this).text();
        console.log("Suggestion clicked:", selectedCar);
        $('#f_r_car_brand').val(selectedCar);
        $('#suggestCar').empty();
    });
    
    // Form submission handler with AJAX
    $('form#formRegistration').submit(function (e) {
        e.preventDefault(); // Prevent the form from submitting traditionally

        // Form validation checks for required fields
        let valid = true;

        // Check if fields are empty and if the form passes Bootstrap validation
        $(this).find(':input[required]').each(function () {
            if ($(this).val().trim() === "") {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) {
            $("div.status").removeClass("alert-success").addClass("alert alert-danger").html("Please fill in all required fields.");
            return; // Prevent form submission if not valid
        }

        $.ajax({
            type: "POST",
            url: "_action_register_user.php",
            data: $(this).serialize(),
            dataType: "json", // Expect JSON response from server
            success: function (response) {
                $("div.status").removeClass("alert-danger alert-success"); // Clear previous alert classes

                if (response.status === "success") {
                    $("button.reset-button").click(); // Reset form fields
                    $("div.status")
                        .addClass("alert alert-success")
                        .html(response.message); // Show success message
                } else if (response.status === "error") {
                    $("div.status")
                        .addClass("alert alert-danger")
                        .html(response.message); // Show error message
                }
            },
            error: function () {
                $("div.status")
                    .removeClass("alert-success")
                    .addClass("alert alert-danger")
                    .html("An unexpected error occurred. Please try again."); // Generic error message
            }
        });
    });

    // User Login Form Submit
    $('form#formUserLog').submit(function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Disable the submit button and show loading spinner
        const $loginButton = $("#loginButton");
        const loginCheck = `<span class="text-light"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                              <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                              <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg></span>`;
        const loadingIcon = "<span class='spinner-border spinner-border-sm'></span>";
        const $logUserField = $("#log_username"); 
        const $logPassField = $("#log_password"); 
        
        
        
        if($logUserField.val() === ""){
            $logUserField.addClass("is-invalid");
        }
           
        else if($logPassField.val() === ""){
            $logPassField.addClass("is-invalid");
        }
        else{
            $logPassField.removeClass("is-invalid");
            $logUserField.removeClass("is-invalid");
            $loginButton.html(loadingIcon);
            $.ajax({
                type: "POST",
                url: "_action_log_user.php",
                data: $(this).serialize(),
                dataType: "json", // Expect JSON response from server
                success: function (response) {
                    if (response.status === "success") {
                        $loginButton.empty().html(loginCheck).addClass("text-light bg-success");
                        setTimeout( ()=> {
                            location.assign(response.redirect); // Use redirect URL from server
                        }, 2500);
                    } else if (response.status === "error") {
                        $loginButton.removeClass("btn-secondary btn-success").addClass("btn-danger").html("Login Failed");
                        $("div.status")
                            .removeClass("alert-success")
                            .addClass("alert alert-danger")
                            .html(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $("div.status")
                        .removeClass("alert-success")
                        .addClass("alert alert-danger")
                        .html("An unexpected error occurred. Please try again.");
                    console.error("AJAX error:", status, error);
                },
                complete: function () {
                    $loginButton.prop("disabled", false).removeClass("btn-secondary").html("Login");
                }
            });
        }
    });

});
