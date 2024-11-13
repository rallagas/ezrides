<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("location: ../../index.php?nopermission");
}else{
    header("location: ../index.php");
}