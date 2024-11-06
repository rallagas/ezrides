<?php

function loadPage($p="") {
    // Check if the 'page' GET parameter is set
    if (isset($_GET['page']) || $p != "") {
        $page = basename($_GET['page'] . basename($p)); // Get the page name and sanitize it
        
        // Define a list of allowed pages to prevent security issues
        $allowedPages = ['_buy']; // Add your allowed pages here

        // Check if the requested page is in the allowed list
        if (in_array($page, $allowedPages)) {
            // Include the requested page
            include $page . '.php';
        } else {
            // Handle invalid page request
            include '404.php'; // Optionally include a 404 page or redirect
        }
    } else {
        // Default page to load if 'page' is not set
        include 'home.php'; // Change to your default page
    }
}