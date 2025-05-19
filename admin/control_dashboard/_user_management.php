<?php
$customerListSQL="SELECT DATE(u.date_joined) date_joined, u.t_username, u.t_status, u.t_user_type, u.t_rider_status, up.*
                    FROM `user_profile` up 
                    join `users` u
                    on up.user_id = u.user_id where up.rider_plate_no is NULL or u.t_rider_status = 0";
$cusList=query($customerListSQL);

    
$riderListSQL="SELECT DATE(u.date_joined) date_joined, u.t_username, u.t_status, u.t_user_type, u.t_rider_status, up.*
                    FROM `user_profile` up 
                    join `users` u
                    on up.user_id = u.user_id where rider_plate_no is NOT NULL or u.t_rider_status = 1";
$riderList=query($riderListSQL);


$allUserSQL="SELECT * FROM `user_profile` where user_id > 0 order by `user_lastname`";
$all=query($allUserSQL);

// Assumes you have a PDO or MySQLi connection already established.
// Example: $conn = new mysqli(...);

// Function to get total amount paid by a user
function getUserTotalPaid($user_id) {
    $stmt = query("SELECT SUM(wallet_txn_amt) * -1 as total_paid FROM user_wallet 
            WHERE ( payFrom = ? or payTo = ? ) AND wallet_txn_status = 'C'",[$user_id,$user_id]);
    foreach($stmt as $result){
        return $result['total_paid'];
    }
}

// Function to get total number of transactions by a user
function getUserTotalNumberOfTransactions($user_id) {
    $sql = query("SELECT COUNT(*) as total_transactions FROM user_wallet 
    WHERE ( payFrom = ? or payTo = ? ) AND wallet_txn_status = 'C'",[$user_id,$user_id]);
    foreach($sql as $result){
        return $result['total_transactions'] ?? 0;    
    }
}

// Function to get total number of transactions by a user
function getUserTotalContribution($user_id) {
    $sql = query("SELECT SUM(wallet_txn_amt) as total_paid FROM user_wallet WHERE payFrom = ? AND payTo = -1 AND wallet_txn_status = 'C'",[$user_id]);
    foreach($sql as $result){
        return $result['total_paid'] ?? 0;    
    }
}



?>
<div class="row pt-3">
    <div class="col-12 col-lg-3 shadow">
          <div class="mb-3" style="height:5vh">
            <form id="searchForm">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search">
                  
                </div>
            </form>
        </div>
        <div id="loadingIndicator" class="text-muted mb-2" style="display: none;">
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Searching...
        </div>
        <div id="searchUser" class="mb-3"></div>
        <div class="overflow-y-scroll d-none d-lg-block">
            <?php 
            foreach($all as $u){ ?>
            <a id="<?php echo $u['user_id'];?>" class="user-log-trigger btn <?php echo $u['rider_plate_no'] == NULL ? 'btn-primary' : 'btn-warning'; ?> w-100 text-start p-2 mb-2" >
                <?php echo strtoupper($u['user_lastname'] . ", " . $u['user_firstname']); ?>
            </a>
            <?php }
            ?>
        </div>


    </div>
    <div class="col-12 col-lg-9 m-0 p-0" id="totalSummary">
       <button id="downloadPDF" class="btn btn-danger mb-3 float-end"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
  <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
</svg> 
       
       </button>
<br>
        <div class="container-fluid overflow_y-scroll" style="height:97vh;" id="userActivity">
            <div class="row p-2">
                <div class="col-lg-2 col-12 mb-3" >
                    <div class="card shadow">
                        <div class="card-header pb-0">
                            <h5 class="fw-bold">USERS</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="userPieChart" style="max-height:100vh"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-12  mb-3">
                    <div class="card shadow">
                        <div class="card-header pb-0">
                            <h5 class="fw-bold">RIDERS</h5>
                        </div>
                        <div class="card-body">
                             <canvas id="newRiderTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-12  mb-3">
                    <div class="card shadow">
                        <div class="card-header pb-0">
                            <h5 class="fw-bold">CUSTOMERS</h5>
                        </div>
                        <div class="card-body">
                             <canvas id="newCustomerTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
<!--            User List Tabular-->
       
           <div class="row gx-2 mb-3">
               <div class="col-lg-12 card">
                  <h3 class="card-header">Customers</h3>
                   <table class="table table-responsive card-body">
                           <thead>
                               <tr>
                                   <td class="fw-bold">Name</td>
                                   <td class="fw-bold">Date Joined</td>
                                   <td class="fw-bold">Total Transactions</td>
                                   <td class="fw-bold">Total Paid</td>
                                   <td class="fw-bold">Contribution to System Income</td>
                               </tr>
                           </thead>
                           <tbody class="overflow-y-scroll" style="max-height:20vh">
                       <?php foreach($cusList as $cus){ ?>
                           <tr>
                            <td><?php echo $cus['user_firstname'] . ", " . $cus['user_lastname'];?></td>
                            <td><?php echo $cus['date_joined'];?></td>
                            <td><?php echo number_format(getUserTotalNumberOfTransactions($cus['user_id']),2);?></td>
                            <td><?php echo number_format(getUserTotalPaid($cus['user_id']) == NULL ? 0.00 : getUserTotalPaid($cus['user_id']),2);?></td>
                            <td><?php echo number_format(getUserTotalContribution($cus['user_id']) == NULL ? 0.00 : getUserTotalContribution($cus['user_id']),2);?></td>

                            </tr>
                        <?php } ?>
                            </tbody>
                   </table>
               </div>
           </div>
           <div class="row gx-2">
               <div class="col-lg-12 card">
                  <h3 class="card-header">Riders</h3>
                   <table class="table table-responsive card-body">
                           <thead>
                               <tr>
                                   <td>Name</td>
                                   <td>Date Joined</td>
                                   <td>Total Transactions</td>
                                   <td>Total Earnings</td>
                               </tr>
                           </thead>
                       <?php foreach($riderList as $cus){ ?>
                           <tr>
                            <td><?php echo $cus['user_firstname'] . ", " . $cus['user_lastname'];?></td>
                            <td><?php echo $cus['date_joined'];?></td>
                            <td><?php echo getUserTotalNumberOfTransactions($cus['user_id']);?></td>
                            <td><?php echo number_format(getUserTotalPaid($cus['user_id']) == NULL ? 0.00 : getUserTotalPaid($cus['user_id']),2);?></td>

                            </tr>
                        <?php } ?>
                   </table>
               </div>
           </div>
        </div>

       

    </div>
</div>


