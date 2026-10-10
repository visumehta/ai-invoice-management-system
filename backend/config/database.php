<?php
    $host = 'localhost';
    $dbname = 'ai_invoice_db';
    $username = 'root';
    $password = '';

   $conn = new mysqli($host, $username, $password, $dbname);

   if(!$conn) {
        die("Connection failed: " . mysqli_connect_error());
   }

?>