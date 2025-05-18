<?php
$customerListSQL="SELECT * FROM `user_profile` where rider_plate_no is NULL";
$cusList=query($customerListSQL);

    
$riderListSQL="SELECT * FROM `user_profile` where rider_plate_no is NOT NULL";
$riderList=query($riderListSQL);


$allUserSQL="SELECT * FROM `user_profile` where user_id > 0 order by `user_lastname`";
$all=query($allUserSQL);



?>
<div class="row pt-3">
    <div class="col-3 shadow">
          <div class="mb-3" style="height:5vh">
            <form id="searchForm">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        <div id="loadingIndicator" class="text-muted mb-2" style="display: none;">
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Searching...
        </div>
        <div class="overflow-y-scroll" id="searchUser" style="height:87vh">
            <?php 
            foreach($all as $u){ ?>
            <a id="<?php echo $u['user_id'];?>" class="user-log-trigger btn <?php echo $u['rider_plate_no'] == NULL ? 'btn-primary' : 'btn-warning'; ?> w-100 text-start p-2 mb-2" >
                <?php echo strtoupper($u['user_lastname'] . ", " . $u['user_firstname']); ?>
            </a>
            <?php }
            ?>
        </div>

    </div>
    <div class="col-9">
        <div class="container-fluid overflow_y-scroll" style="height:97vh;" id="userActivity">
            <div class="row p-2">
                <div class="col-lg-2 col-12" >
                    <div class="card shadow">
                        <div class="card-header pb-0">
                            <h5 class="fw-bold">USERS</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="userPieChart" style="max-height:100vh"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-12">
                    <div class="card shadow">
                        <div class="card-header pb-0">
                            <h5 class="fw-bold">RIDERS</h5>
                        </div>
                        <div class="card-body">
                             <canvas id="newRiderTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-12">
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


