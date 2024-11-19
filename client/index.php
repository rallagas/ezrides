<?php
require_once "../_db.php";
include_once "../_functions.php";
include_once "../_sql_utility.php";
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/css/coreui.min.css" rel="stylesheet" integrity="sha384-xtmKaCh9tfCPtb3MMyjsQVNn3GFjzZsgCVD3LUmAwbLSU3u/7fIZkIVrKyxMAdzs" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <?php include_once "XXnav-client.php";
?>
    <hr class="m-0 p-0 text-dark">
    <div class="container-fluid p-0">

        <div class="row">
            <div class="col-lg-12">
            </div>
        </div>

        <div class="row px-5" id="queryresult"></div>
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-md-12 bg-purple">

                <div class="offcanvas offcanvas-start bg-purple vh-100" tabindex="-1" id="appMenu" aria-labelledby="appMenu">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasExampleLabel">EzRides</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body container-fluid vh-75">
                        <div class="row g-1 mb-3">
                            <?php  include_once "menu.php";
?>
                        </div>

                        <div class="row g-1 mb-3 vh-50 border-1" id="MyBookings">
                            <div class="col-sm-12 col-lg-4 col-md-12">
                                <div id="BookingDetails" class="card shadow"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
        <div class="row p-0">
            <?php
$txn_cat = select_data( "txn_category", NULL, "txn_category_id", 15 );
?>

            <div class="col-12">

                <?php
                foreach ( $txn_cat as $cat ) {
                    ifPageis( $cat['page_action'], $cat['txn_link'] );
                }
                if ( !isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'home' ) ) { ?>
                <div class="container-fluid g-1">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-4 col-sm-12 col-sm-12 vh-100">
                            <?php include_once "_restaurant_finder.php";?>
                        </div>
                    </div>
                </div>

                <?php } ?>
            </div>

        </div>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/js/coreui.bundle.min.js" integrity="sha384-kwU8DU7Bx4h5xZtJ61pZ2SANPo2ukmbAwBd/1e5almQqVbLuRci4qtalMECfu9O6" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!--<script src = "../js/jquery-3.5.1.min.js"></script>-->
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<!--<script src = "https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity = "sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin = "anonymous"></script>-->

<script src="_process_ajax.js"></script>

<?php
    if ( isset( $_GET['page'] ) ) {
        switch( $_GET['page'] ) {
            case 'rent': ?>
<script src="_car_rental.js"></script>
<?php break;
            case 'angkas': ?>
<script src="_map_config.js"></script>
<script src="_map_func.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places,geometry,marker&callback=initMap&loading=async"></script>
<!--<script src = "_map_jquery.js"></script>-->
<?php break;
            //        default:
            ?>
<!--                <script src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&callback=initMap&libraries=places,geometry&solution_channel=GMP_QB_neighborhooddiscovery_v3_cADEF&loading=async" async defer></script>-->
<?php }
        }

        ?>

<script>
    $(document).ready(function() {

        var spinner = "<div class='mt-5 spinner-border spinner-border-sm'></div>";
        var grower = "<div class='mt-5 spinner-grow spinner-grow-sm'></div>";

        $("#userLogOut").click(function() {
            $("body").html("<center>" + grower + grower + grower + "</center>");
            setTimeout(function() {
                window.location.assign("../index.php?logout");
            }, 1200);

        });

    });
</script>

</html>