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

?>

<!DOCTYPE html>
    <head>
        <title>Purchase History</title>
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
                        <h1 style='text-align:center;'><i><u>Purchase History</u></i></h1>
                        <table class="purchase">
                            <thead>
                                <th><br>Transaction ID<br><br></th>
                                <th><br>Book</th>
                                <th><br>Type</th>
                                <th><br>Quantity</th>
                                <th><br>Cost</th>
                                <th><br>Date & Time</th>
                            </thead>
                            <tbody>
                            <?php 
                                $sql = "SELECT * FROM `transactions` where custID =".$custID." ORDER BY purchaseDate DESC, purchaseTime DESC;";
                                $result = $conn->query($sql);
                                $num = mysqli_num_rows ( $result );
                                for ($x = 0; $x < $num; $x++) 
                                { 
                                    $row = mysqli_fetch_array($result);    
                            ?>
        
                    
                                    <tr>
                                        <td><br><?php echo $row['id']; ?></td>
                                        <td style="text-align: left; padding-left:20px;"><br><?php echo $row['bookName']; ?><br><br></td>
                                        <td><br><?php echo $row['bookType']; ?></td>
                                        <td><br>
                                            <?php
                                                if($row['quantity']=='') 
                                                    echo 'N.A.';
                                                else
                                                    echo $row['quantity'];
                                            ?>
                                        </td>
                                        <td><br><?php echo $row['cost']; ?></td>
                                        <td><br><?php echo $row['purchaseDate']." ".$row['purchaseTime']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
