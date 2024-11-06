<?php 
include_once "../_db.php";
include_once "../_functions.php";
include_once "../_sql_utility.php";
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>EZ Rides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-lg-12">
                <?php include_once "nav-client.php"; ?>
            </div>
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg bg-body-secondary">
                    <div class="container-fluid">
                        <button class="navbar-toggler bg-purple text-light btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="toggle menu">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ui-radios-grid" viewBox="0 0 16 16">
                                <path d="M3.5 15a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5m9-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5m0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5M16 3.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-9 9a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m5.5 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m-9-11a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0 2a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>
                        </button>
                        <div class="collapse navbar-collapse p-4" id="mainMenu">
                            <?php  include_once "menu.php"; ?>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="row p-5">
            <?php 
            $txn_cat = select_data(CONN,"txn_category",NULL,"txn_category_id",15); ?>

            <div class="col-12">
                <?php  foreach($txn_cat as $cat){
                            ifPageis($cat['page_action'],$cat['txn_link']);
                        } ?>
            </div>

        </div>
        <div class="row px-5" id="queryresult"></div>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../js/jquery-3.5.1.min.js"></script>
<!--<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>-->
<script src="_process_ajax.js"></script>

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