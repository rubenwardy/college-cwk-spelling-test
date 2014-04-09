<?php
// Log user out
session_start();
session_destroy();

// Redirect to the login page
header("location: login.php");