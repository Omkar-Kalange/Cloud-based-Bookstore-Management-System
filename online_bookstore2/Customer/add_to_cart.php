<?php
    session_start();

    require('connect.php');

    if(empty($_SESSION['custID']))
    {
        header("Location: login.php"); 
    }

    $bookID = $_GET['bookID'];
    $custID = $_SESSION['custID'];

    $query = "Insert into `cart` (`custId`,`bookID`) values(".$custID.",".$bookID.")";
    
    try
    {
        $result = mysqli_query($conn,$query);
        echo "<script> alert('Book Added to cart');
            window.location = 'show-books.php';
            </script>";
    }
    catch(Exception $e)
    {
        echo "<script> alert('Book is present in the cart already');
        window.location = 'show-books.php';
        </script>";

    }
?>