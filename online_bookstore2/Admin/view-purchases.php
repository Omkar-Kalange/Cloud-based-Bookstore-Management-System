<?php
    session_start();

    require('connect.php');
    require('s3-config.php');

    if(empty($_SESSION['adminID']))
    {
        header("Location: login.php"); 
    }
        
    $adminID = $_SESSION["adminID"];

    $sql = "select * from admins where adminID ='$adminID'"; 
    $result = $conn->query($sql);
    $row2 = mysqli_fetch_array($result);
    $name = $row2['firstName'].' '.$row2['lastName'];

    $profPic = '0.png'; 
    if($row2['profilePic']=='Y')
    {
        $profPic = $adminID.'.png';
    }

    $cmd = $s3->getCommand('GetObject', [
        'Bucket' => 'onlinebookstoreservice',
        'Key'    => 'ProfilePics/Admin/'.$profPic
    ]);
    
    //The period of availability
    $request = $s3->createPresignedRequest($cmd, '+1 minutes');
    
    //Get the pre-signed URL
    $signedUrl = (string) $request->getUri();
?>

<!DOCTYPE html>
    <head>
        <title>View purchases</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
        <link rel="stylesheet" href="Style.css?v=<?php echo time(); ?>">
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
                    include('adminOptions.php');
                    ?>
                </td>
                <td>
                    <div class="main">
                        <h1 style='text-align:center;'><i><u>View Purchases</u></i></h1>
                        <table class="purchase">
                            <thead>
                                <th><br>Title<br><br></th>
                                <th><br>Type</th>
                                <th><br>Purchased by</th>
                                <th><br>Books sold</th>
                                <th><br>Amount received (Rs.)</th>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT bookName, bookType, COUNT(bookNAME), SUM(quantity), SUM(cost) FROM `transactions` GROUP by bookName, bookType ORDER by cost DESC";
                                    $result = $conn->query($sql);
                                    $num = mysqli_num_rows ( $result );
                                    for ($x = 0; $x < $num; $x++) 
                                    { 
                                        $row = mysqli_fetch_array($result);
                                    ?>
                    
                                    <tr>
                                        <td style="text-align: left; padding-left:20px;"><br><?php echo $row['bookName']; ?><br><br></td>
                                        <td><br><?php echo $row['bookType']; ?></td>
                                        <td><br><?php echo $row['COUNT(bookNAME)'].' customers'; ?></td>
                                        <td><br><?php if ($row['SUM(quantity)']=='') echo 'N.A.'; else echo $row['SUM(quantity)']; ?></td>
                                        <td><br><?php echo $row['SUM(cost)']; ?></td>
                                    </tr>
                                <?php } ?>

                                <?php
                                    $sql = "SELECT COUNT(DISTINCT(bookName)) as name, COUNT(*) cust, SUM(quantity), SUM(cost) from transactions";
                                    $result = $conn->query($sql);
                                    $num = mysqli_num_rows ( $result );
                                    $row = mysqli_fetch_array($result);
                                ?>
                    
                                <tr>
                                    <td><br><b>Total : <?php echo $row['name']; ?></b><br><br></td>
                                    <td><br><b></b></td>
                                    <td><br><b><?php echo $row['cust']." customers"; ?></b></td>
                                    <td><br><b><?php echo $row['SUM(quantity)']; ?></b></td>
                                    <td><br><b><?php echo $row['SUM(cost)']; ?></b></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
