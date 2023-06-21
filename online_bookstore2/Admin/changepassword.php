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

    $db_pass = $row2['pswd'];

    if($_SERVER["REQUEST_METHOD"]=="POST")
    {  
        $old = $_POST['txtold_password'];
        $pass_new =  $_POST['txtnew_password'];
        $confirm_new =  $_POST['txtconfirm_password'];

        if($db_pass!=$old)
        {
            echo "<script> alert('Incorrect Old Password!!');</script>";
        }
        else if($pass_new!=$confirm_new)
        { 
            echo "<script> alert('NEW Password and CONFIRM password not Matched!!');</script>";
        } 
        else 
        {
            $sql = "update admins set `pswd`='$confirm_new' where adminID= '".$_SESSION['adminID']."'";
            $res = $conn->query($sql);

            echo "<script> alert('Password changed Successfully...');</script>";
        }
    }
?>

<!DOCTYPE html>
    <head>
        <title>Change password</title>
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
                        <h1 style='text-align:center;'><i><u>Change Password</u></i></h1>
                        <form method="POST">
                            <label for="oldPass">Old Password </label>
                            <input type="password" name="txtold_password" id="oldPass"  placeholder="Enter Old Password">
                            <br><br>
                            <label for="newPass">New Password</label>
                            <input type="password" name="txtnew_password" id="newPass" placeholder="Enter New Password">
                            <br><br>
                            <label for="confirmPass">New Password</label>
                            <input type="password" name="txtconfirm_password" id="confirmPass"  placeholder="Confirm New Password">
                            <br><br>
                            <input type="submit" name="btnpassword" value="Change Password"></input>
                        </form>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
