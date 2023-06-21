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
    if(isset($_POST["btnAdd"]))
    {

        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $mob = $_POST['mob'];

        //save fee details
        $query = "INSERT INTO `admins` (`firstName`, `lastName`, `emailID`, `mob`) VALUES ('$fname','$lname','$email','$mob');";
        
        try
        {
            $result = mysqli_query($conn,$query);
            echo "<script> alert('Admin successfully');</script>";
        }
        catch(Exception $e)
        {
            echo "<script> alert('Email already registered.');</script>";
        }
    }
?>

<!DOCTYPE html>
    <head>
        <title>Add admin</title>
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
                        <h1 style='text-align:center;'><i><u>Add New Admin</u></i></h1>

                        <form id="form" name="mainForm" method="post">
                            <label>First Name</label>
                            <input name="fname" placeholder="Enter first name" required></input>
                            <br><br>
                            <label>Last Name</label>
                            <input name="lname" placeholder="Enter last name" required></input>
                            <br><br>
                            <label>Email ID</label>
                            <input name="email" placeholder="Enter Email ID" required></input>
                            <br><br>
                            <label>Mob.:</label>
                            <input name="mob" placeholder="Enter Mob. no." required></input>
                            <br><br>
                            <input type="submit" value="Add Admin" name="btnAdd"></input>
                        </form>
                </div>
                </td>
            </tr>
        </table>
    </body>
</html>
