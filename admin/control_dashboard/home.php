
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Reports</title>
</head>
<body class="bg-purple">
<?php
$sql = null;
$sql = "SELECT SUM(wallet_txn_amt) as income FROM `user_wallet` WHERE wallet_action LIKE '%Admin' ";
$sales_data = query($sql);
$sales = $sales_data[0]['income'];

$sql = "SELECT SUM(wallet_txn_amt) as total_wallet_pool from `user_wallet` ";
$wallet_pool_data = query($sql);
$wallet_pool = $wallet_pool_data[0]['total_wallet_pool'];


?>

<div class="container mt-4">
    <div class="row g-2">
        <div class="col-lg-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                <h6 class="card-title fw-bold">INCOME</h6>
                    <h1 class="display-4"><?php echo number_format($sales,2);?></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
        <div class="card border-0 shadow">
                <div class="card-body">
                <h6 class="card-title fw-bold">WALLET POOL</h6>
                    <h1 class="display-4"><?php echo number_format($wallet_pool,2);?></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-4"></div>
    </div>
</div>
    
</body>
</html>