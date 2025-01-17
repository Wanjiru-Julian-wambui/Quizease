<?php
$host="localhost";
$user="root";
$password="";
$dbname="quizease";
$conn=new mysqli($host,$user,$password,$dbname);
if($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
    
}
?>