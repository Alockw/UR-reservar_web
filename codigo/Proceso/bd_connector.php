<?php
function conectarDB()
{
    $servername = "localhost"; 
    $username_db = "raspberry"; 
    $password_db = "Empanadas2045/"; 
    $dbname = "parkease_bd"; 

    
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

   
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    return $conn;
}
