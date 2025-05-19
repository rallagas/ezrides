<?php
$customerListSQL="SELECT * FROM `user_profile` where rider_plate_no is NULL";
$cusList=query($customerListSQL);

    
$riderListSQL="SELECT * FROM `user_profile` where rider_plate_no is NOT NULL";
$riderList=query($riderListSQL);


$allUserSQL="SELECT * FROM `user_profile` where user_id > 0 order by `user_lastname`";
$all=query($allUserSQL);



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
    <div class="col-12 col-lg-9 m-0 p-0">
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
        </div>

       

    </div>
</div>


