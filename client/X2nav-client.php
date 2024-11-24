 <nav class="navbar navbar-expand-lg bg-purple bd-navbar text-white m-0">
     <div class="container">
         <a class="navbar-brand text-white ms-3" href="index.php">
             <img src="../icons/ride-hailing.png" alt="" class="d-inline-block align-text-top" width="30" height="24">
             <span class="fw-bold">EZ Rides</span>
         </a>


         <!--
    <form class="d-flex mt-2 pt-2" role="search">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-light" type="submit"><i class="fi fi-rr-search"></i></button>
    </form>
-->


         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
         </button>

         <div class="collapse navbar-collapse px-3" id="navbarTogglerDemo02">
             <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                 <li class="nav-item">
                     <a class="nav-link text-white" aria-current="page" href="#">Home</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link text-white" href="#">My Orders</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link text-white" href="#">My Booked Rides</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link text-white" href="#">My Booking</a>
                 </li>
                 <li class="nav-item">
                     <?php
            if ($_SESSION['t_rider_status'] == '1'){ ?>
                     <a class="nav-link text-warning" href="../rider_dashboard/">Rider's Dashboard</a>
                     <?php }
            else {  ?>
                     <a class="btn btn-outline-warning" href="#">Become a Rider</a>
                     <?php } ?>

                 </li>
             </ul>

             <div class="d-flex me-5">
                 <div class="dropend">
                     <button class="nav-link btn text-white dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                         <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                             <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                             <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                         </svg>
                     </button>
                     <ul id="profileDD" class="dropdown-menu">
                         <li><span class="dropdown-item"> </span></li>
                         <li><a href="#" class="dropdown-item">Profile</a></li>
                         <li><a id="userLogOut" href="#" class="dropdown-item">Logout</a></li>
                     </ul>
                 </div>

             </div>
         </div>




     </div>
 </nav>