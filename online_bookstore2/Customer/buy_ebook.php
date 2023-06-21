<?php
    session_start();

    require('connect.php');
    require('s3-config.php');

    if(empty($_SESSION['custID']))
    {
        header("Location: login.php"); 
    }
        
    $custID = $_SESSION["custID"];

    $sql = "select * from customers where customerID ='$custID'"; 
    $result = $conn->query($sql);
    $row2 = mysqli_fetch_array($result);
    $name = $row2['firstName'].' '.$row2['lastName'];

    $profPic = '0.png';
    if($row2['profilePic']=='Y')
    {
        $profPic = $custID.'.png';
    }

    $cmd = $s3->getCommand('GetObject', [
        'Bucket' => 'onlinebookstoreservice',
        'Key'    => 'ProfilePics/Customer/'.$profPic
    ]);
    
    //The period of availability
    $request = $s3->createPresignedRequest($cmd, '+1 minutes');
    
    //Get the pre-signed URL
    $signedUrl = (string) $request->getUri();

    $bookId = $_GET['bookID'];
    $sql = "SELECT * FROM books where bookID = ".$bookId;
    $result = $conn->query($sql);
    $row = mysqli_fetch_array($result);

    if(isset($_POST["Purchase"]))
    {
        $cost = $row['ebook_price'];

        try
        {
            $query = "INSERT INTO `eBooks` (`custID`, `bookID`) VALUES (".$custID.", ".$bookId.");";
            $result = $conn->query($query);
            $query = "INSERT INTO `transactions` (`custID`, `custName`, `email`, `mob`, `bookID`, `bookName`, `bookType`, `cost`,`purchaseDate`,`purchaseTime`) VALUES (".$custID.", '".$name."', '".$row2['emailID']."', '".$row2['mob']."',".$bookId.", '".$row['title']."', 'eBook', ".$cost.", CURDATE(), CURTIME());";
            $result = $conn->query($query);
            
            echo "<script> alert('eBook purchased for Rs.".$cost."'); window.location = 'purchase_history.php';</script>";
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
?>

<!DOCTYPE html>
    <head>
        <title>Buy eBooks</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <table>
            <tr id="top-margin">
                <td id="web-name">
                    <h2>The Big Bookshelf.com</h2>
                </td>
                <td>
                    <h3><a class="username" href="show_profile.php"><?php echo $name;  ?></a></h3>
                </td>
                <td id="userImg">
                    <img src=<?php echo $signedUrl; ?> alt="User Image" width="50" height="50"></img>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="sidebar">
                    <?php
                    include('customerOptions.php');
                    ?>
                </td>
                <td>
                    <div class="main">
                        <h1 style='text-align:center;'><i><u>Buy Book</u></i></h1>
                        <div class="page">
                            <table>
                                <tr>
                                    <br><hr>
                                    <td class="bookImage">
                                        <?php
                                            $cmd = $s3->getCommand('GetObject', [
                                                'Bucket' => 'onlinebookstoreservice',
                                                'Key'    => 'BookCover/'.$bookId.'.png'
                                            ]);
                                            
                                            //The period of availability
                                            $request = $s3->createPresignedRequest($cmd, '+1 minutes');
                                            
                                            //Get the pre-signed URL
                                            $signedUrl = (string) $request->getUri();
                                        ?>
                            
                                        <img src= <?php echo $signedUrl; ?> alt="Book Image"></img>
                                    </td>
                                    <td class="bookChar">
                                        <b>Title : </b> <?php echo $row['title']; ?>
                                        <br><br>
                                        <b>Edition:	 </b> <?php echo $row['edition']; ?>
                                        <br><br>
                                        <b>Author(s) : </b> <?php echo $row['author']; ?>
                                        <br><br>
                                        <b>Publisher : </b> <?php echo $row['publisher']; ?>
                                        <br><br>
                                        <b>Genre : </b> <?php echo $row['genre']; ?>
                                        <br><br>
                                        <b>Year : </b> <?php echo $row['publicationYear']; ?>
                                        <br><br>
                                        <b>Available: </b> <?php echo $row['quantity']; ?>
                                        <br><br>
                                        <b>eBook Price (Rs.): </b> <?php echo $row['ebook_price']; ?>
                                        <br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p><b>Abstract:</b><br><?php echo $row['synopsis']; ?></p>                                           
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <form method="post">
                                            <button class="btn" name="Purchase">Purchase</button>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
