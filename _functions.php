<?php


function ifPageis($page,$include){
     if(isset($_GET['page'])){
            if($_GET['page'] == $page){
                include_once "$include";   
                return true;
            }
     }
    else{
        return false;
    }
}
function ifActionis($page,$include){
     if(isset($_GET['page_action'])){
            if($_GET['page_action'] == $page){
                include_once "$include";   
                return true;
            }
     }
    else{
        return false;
    }
}



