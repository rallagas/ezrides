<?php 
include_once "../../_db.php";
include_once "../../_sql_utility.php";
include_once "../_class_grocery.php";
include_once "func.php";

if (isset($_GET['approveCashout'])) {
    // Decode the wallet ID from Base64
    $walletid_encoded = $_GET['walletid'];
    $walletid = base64_decode($walletid_encoded);


    // Validate and sanitize decoded wallet ID
    if ($walletid && ctype_digit($walletid)) { // Ensure it's a numeric value
        $table = 'user_wallet';
        $set = ['wallet_txn_status' => 'C'];
        $where = ['user_wallet_id' => $walletid];

        // Update data in the database
        update_data($table, $set, $where);
        
        header("location: ?CashoutApproved");
        
    } else {
        // Handle invalid wallet ID (log or show error)
        echo "Invalid wallet ID.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .bg-purple{
            background-color:mediumpurple;
        }
    </style>
</head>
<body>
   
   <div class="container-fluid">
       <div class="row">
        <?php include_once "navbar.php";?>

           <div class="col-lg-12 col-sm-12" id="controlDashboard">
<!--               load page functions here -->
                      <?php
                            loadPage();
                        ?>
                      
           </div>
       </div>
         
       
   </div>
   
   
    
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="_loader.js"></script>
<script>
$(document).ready(function() {
    // Trigger file selection
    $('.attach-file').on('click', function() {
        $(this).siblings('.form-file').click();
    });

    // Submit the form data
    $('form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Collect form data
        let formData = new FormData(this);

        $.ajax({
            url: '_process_vehicle.php',  // Server-side script
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                let res = JSON.parse(response);
                if (res.success) {
                    alert('Vehicle registered successfully.');
                    $('form')[0].reset();
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
});

</script>
</html>