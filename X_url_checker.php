<?php
if(!isset($_GET['page'])){
    header("location: index.php?page=loguser");
}

if(isset($_GET['logout'])){
    session_destroy();
}