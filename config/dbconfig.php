<?php 

 // Connect to the database
 $servername = "localhost"; 
 $username = "root";        
 $password = "";            
 $database = "emurray46";  

 // Create a connection
 $conn = new mysqli($servername, $username, $password, $database);

 if ($conn->connect_error) {
     die( "Connection failed: " . $conn->connect_error);
 } 


?>