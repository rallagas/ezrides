<nav class="navbar navbar-expand-lg bg-purple text-white m-0">
  <div class="container-fluid">
    <a class="navbar-brand text-white" href="index.php">
         <img src="../icons/ride-hailing.png" alt="" class="d-inline-block align-text-top" width="30" height="24">
            <span class="fw-bold">EZ Rides</span>
    </a>
     
     
    <form class="d-flex mt-2 pt-2" role="search">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-light" type="submit"><i class="fi fi-rr-search"></i></button>
    </form>
    
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link text-white" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="#">My Wallet</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="#">Order Something</a>
        </li>
        <li class="nav-item">
         <?php
            if ($_SESSION['t_rider_status'] == 'R'){ ?>
                <a class="btn btn-warning float-end" href="#">Rider</a>
            <?php }
            else {  ?>
                <a class="btn btn-outline-warning" href="?activate_rider=1">Become a Rider</a>               
            <?php } ?>
          
        </li>
        <li class="nav-item">
           <?php
            if(isset($_GET['logout'])){
                session_destroy();
                header("location: ../index.php");
            }
            ?>
            <a id="userLogOut" href="?logout" class="nav-link">Logout</a>
        </li>
        
         
      </ul>
    </div>
    

   
    
  </div>
</nav>