<?php
require_once "../../_db.php";
require_once "../../_functions.php";
require_once "../../_sql_utility.php";?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Rider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
        <?php 
        include_once "profile-nav.php";
        include_once "menu.php";
        include_once "page-check.php";

        $profile = query("SELECT u.*, up.*
                            FROM `users` u
                            JOIN `user_profile` up
                              ON u.user_id = up.user_id
                        WHERE u.user_id = ?", [USER_LOGGED]) ;


       extract($profile[0]);
        ?>

        <div class="container">
            


            <div class="row">
                 <!-- Profile Header -->
        <div class="card">
            <div class="card-body text-center">
                <img src="../../i" alt="Profile Picture" class="rounded-circle mb-3" style="width: 150px; height: 150px;">
                <h3 class="card-title"><?php echo $user_firstname . ", " . $user_lastname . ", " . $user_mi;?></h3>
                <p class="text-muted"><?php echo $user_email_address;?></p>
                <button class="btn btn-primary btn-sm">Edit Profile</button>
                <button id="userLogOut" class="btn btn-outline-danger btn-sm" id="">Logout</button>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mt-4" id="profileTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Settings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">Activity</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-4" id="profileTabContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Profile Overview</h5>
                        <p><strong>Full Name:</strong> <?php echo $user_firstname . " " . $user_lastname . ", " . "$user_mi"?></p>
                        <p><strong>Email:</strong><?php echo $user_email_address; ?></p>
                        <p><strong>Phone:</strong> <?php echo $user_contact_no; ?></p>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Profile</h5>
                        <form method="POST" action="update_profile.php">
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="firstname" value="<?php echo $user_firstname;?>" require>
                                    <input type="text" class="form-control" name="lastname" value="<?php echo $user_lastname;?>">
                                    <input type="text" class="form-control" name="mi" value="<?php echo $user_mi;?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $user_email_address;?>">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" value="<?php echo $user_contact_no;?>">
                            </div>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity Tab -->
            <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Activity</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Logged in at 10:00 AM, Nov 27, 2024</li>
                            <li class="list-group-item">Changed password at 8:30 PM, Nov 26, 2024</li>
                            <li class="list-group-item">Updated profile information at 6:00 PM, Nov 25, 2024</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>





        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/js/coreui.bundle.min.js" integrity="sha384-kwU8DU7Bx4h5xZtJ61pZ2SANPo2ukmbAwBd/1e5almQqVbLuRci4qtalMECfu9O6" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>



    
</body>
</html>
