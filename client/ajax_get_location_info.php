<?php
include_once "../_db.php";
include_once "../_sql_utility.php";

   
if(isset($_GET['get_region_list'])){
    $regionlist = select_data(CONN,"refRegion");
    
    foreach($regionlist as $rl){ ?>
          <option value="<?php echo $rl['regCode'];?>"><?php echo $rl['regDesc'];?></option>  
    <?php }
}

if(isset($_POST['get_province_list'])){
    $region=$_POST['get_province_list'];
    $provlist = select_data(CONN,"refprovince","regCode=$region");
    
    foreach($provlist as $pl){ ?>
          <option value="<?php echo $pl['provCode'];?>"><?php echo $pl['provDesc'];?></option>  
    <?php }
}
if(isset($_POST['get_municipality_list'])){
    $province=$_POST['get_municipality_list'];
    $munilist = select_data(CONN,"refcitymun","provCode=$province");
    
    foreach($munilist as $ml){ ?>
          <option value="<?php echo $ml['citymunCode'];?>"><?php echo $ml['citymunDesc'];?></option>  
    <?php }
}

?>