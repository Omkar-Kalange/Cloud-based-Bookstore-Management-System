<?php 
    require('connect.php');
    require('s3-config.php');

    $title = $_GET['title'];
    $edition = $_GET['edition'];

    $sql = "SELECT bookID FROM books WHERE title='".$title."' and edition=".$edition;
    $result = $conn->query($sql);
    $row = mysqli_fetch_array($result);
    
    $s3->deleteObject([
        'Bucket'=>'onlinebookstoreservice',             //Traget bucket
        'Key' => 'BookCover/'.$row['bookID'].'.png'     //Name of the file in the target bucket
    ]);

    $sql = "DELETE FROM books WHERE title='".$title."' and edition=".$edition;
    $conn->query($sql);

    header("Location: show-books.php"); 
?>