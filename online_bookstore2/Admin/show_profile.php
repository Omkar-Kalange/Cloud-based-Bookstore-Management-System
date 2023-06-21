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

    if (isset($_POST['submit']))
    {
        move_uploaded_file($_FILES["profPic"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/i.png");
        $s3->putObject([
            'Bucket'=>'onlinebookstoreservice',            //Bucket where to put image
            'Key' => 'ProfilePics/Admin/'.$adminID.'.png', //Name of the file in the bucket
            'Body' =>fopen($_SERVER["DOCUMENT_ROOT"]."/i.png",'rb')            //File can be viewed on S3
        ]);
        unlink($_SERVER["DOCUMENT_ROOT"]."/i.png");

        $query = "UPDATE admins SET profilePic = 'Y' WHERE adminID = ".$adminID;
        try
        {
            $result = mysqli_query($conn,$query);
            echo "<script> alert('Updated profile image successfully');</script>";
        }
        catch(Exception $e)
        {
            echo "<script> alert('Problem in updating profile image.');</script>";
        }

    }
?>

<!DOCTYPE html>
    <head>
        <title>Admin Profile</title>
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
                        <h1 style='text-align:center;'><i><u>Your profile</u></i></h1>
                        <img src = <?php echo $signedUrl; ?> alt="User Image" style="margin-left: auto; margin-right: auto; display: block;" width="280" height="300"></img1>
                        <br>

                        <form method="POST" enctype="multipart/form-data" style="margin-left: 300px; margin-right: 300px;">
                            <label>Update profile image</label>
                            <input type="file" name='profPic' required>
                            <input type='submit' name='submit' value="Go"></input>
                        </form>
                        <br>
                        <table class="profile">
                            <tr>
                                <td colspan="3">
                                    <h2>Your details:</h2>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label><b>First name :</b></label>
                                </td>
                                <td>
                                    <?php echo $row2['firstName'];?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label><b>Last name :</b></label>
                                </td>
                                <td>
                                    <?php echo $row2['lastName'];?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label><b>Email ID :</b></label>
                                </td>
                                <td>
                                    <?php echo $row2['emailID'];?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label><b>Mob. :</b></label>
                                </td>
                                <td>
                                    <?php echo $row2['mob'];?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><br></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                <a href="changepassword.php">Change Password</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="#.php">Update Profile</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button class='signOut' onclick= 'return signOut(<?php echo $adminID ?>)'>Sign out</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </body>
    <script>
        function signOut(ID)
        {
            if(confirm("Signing-out will delete your login access and access to admin privileges. Are you sure you want to sign-out?"))
                {
                    window.location = "sign-out.php?ID="+ID;
                }
        }
    </script>
</html>
