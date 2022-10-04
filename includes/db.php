<?php
function dbConnect() {
    $con = mysqli_connect("localhost", "root", "", "loan_management");

    return $con;
}