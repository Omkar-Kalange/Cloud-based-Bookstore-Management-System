<?php
    session_start();

    include('connect.php');

    if(isset($_POST['Proceed']))
    {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $mob = $_POST['mob'];
        $pswd = $_POST['pswd'];
        $confirmPswd = $_POST['confirmPswd'];
        $country = $_POST['country'];
        $address = $_POST['address'];

        if($pswd != $confirmPswd)
        {
            echo "<script> alert('Entered password and confirmed passwords don\'t match!');</script>";
        }
        else
        {
            try
            {
                $query = "INSERT INTO `customers` (`firstName`, `lastName`, `emailID`, `mob`, `pswd`, `country`, `address`) VALUES ('$fname','$lname','$email','$mob','$pswd','$country','$address');";;
                mysqli_query($conn,$query);
                
                $query = "SELECT * from customers where emailID='$email';";
                $result = $conn->query($query);
                $row = mysqli_fetch_array($result);

                $_SESSION["custID"] = $row['customerID'];

                header("Location: index.php");
            }
            catch(Exception $e)
            {
                echo "<script> alert('A DB operation failed!');</script>";
            }
        }
    }
?>
<!DOCTYPE html>
    <head>
        <title>The Big Bookshelf</title>
        <style>
            body {
                background-color: #9f9da7;
                font-size: 1.6rem;
                font-family: "Open Sans", sans-serif;
                color: #2b3e51;
            }

            h2 {
                font-weight: 300;
                text-align: center;
            }

            p {
                position: relative;
            }

            a,
            a:link,
            a:visited,
            a:active {
                color: #3ca9e2;
                -webkit-transition: all 0.2s ease;
                transition: all 0.2s ease;
            }
            a:focus, a:hover,
            a:link:focus,
            a:link:hover,
            a:visited:focus,
            a:visited:hover,
            a:active:focus,
            a:active:hover {
                color: #329dd5;
                -webkit-transition: all 0.2s ease;
                transition: all 0.2s ease;
            }

            #login-form-wrap {
                background-color: #fff;
                width: 35%;
                margin: 30px auto;
                text-align: center;
                padding: 20px 0 0 0;
                border-radius: 4px;
                box-shadow: 0px 30px 50px 0px rgba(0, 0, 0, 0.2);
            }

            #login-form {
                padding: 0 60px;
            }

            input {
                padding-left: 15px;
                display: block;
                box-sizing: border-box;
                width: 100%;
                outline: none;
                height: 60px;
                line-height: 60px;
                border-radius: 4px;
            }

            input[type="text"],
            input[type="pswd"] {
                width: 100%;
                padding: 0 0 0 10px;
                margin: 0;
                color: #8a8b8e;
                border: 1px solid #c2c0ca;
                font-style: normal;
                font-size: 16px;
                -webkit-appearance: none;
                    -moz-appearance: none;
                        appearance: none;
                position: relative;
                display: inline-block;
                background: none;
            }
            input[type="text"]:focus,
            input[type="pswd"]:focus {
                border-color: #3ca9e2;
            }
            input[type="text"]:focus:invalid,
            input[type="pswd"]:focus:invalid {
                color: #cc1e2b;
                border-color: #cc1e2b;
            }
            input[type="text"]:valid ~ .validation,
            input[type="pswd"]:valid ~ .validation {
                display: block;
                border-color: #0C0;
            }
            input[type="text"]:valid ~ .validation span,
            input[type="pswd"]:valid ~ .validation span {
                background: #0C0;
                position: absolute;
                border-radius: 6px;
            }
            input[type="text"]:valid ~ .validation span:first-child,
            input[type="email"]:valid ~ .validation span:first-child {
                top: 30px;
                left: 14px;
                width: 20px;
                height: 3px;
                -webkit-transform: rotate(-45deg);
                        transform: rotate(-45deg);
            }
            input[type="text"]:valid ~ .validation span:last-child,
            input[type="email"]:valid ~ .validation span:last-child {
                top: 35px;
                left: 8px;
                width: 11px;
                height: 3px;
                -webkit-transform: rotate(45deg);
                        transform: rotate(45deg);
            }

            .validation {
                display: none;
                position: absolute;
                content: " ";
                height: 60px;
                width: 30px;
                right: 15px;
                top: 0px;
            }

            form textarea
            {
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                color: #8a8b8e;
                border: 2px solid black;
                font-style: normal;
                font-size: 16px;
            }

            input[type="submit"] {
                border: none;
                display: block;
                background-color: #3ca9e2;
                color: #fff;
                font-weight: bold;
                text-transform: uppercase;
                cursor: pointer;
                -webkit-transition: all 0.2s ease;
                transition: all 0.2s ease;
                font-size: 18px;
                position: relative;
                display: inline-block;
                cursor: pointer;
                text-align: center;
            }
            input[type="submit"]:hover {
                background-color: #329dd5;
                -webkit-transition: all 0.2s ease;
                transition: all 0.2s ease;
            }

            #create-account-wrap {
                background-color: #eeedf1;
                color: #8a8b8e;
                font-size: 14px;
                width: 100%;
                padding: 10px 0;
                border-radius: 0 0 4px 4px;
            }
        </style>

    </head>
    <body>
        <div id="login-form-wrap">
            <h2>Sign-in/Register</h2>
            <form id="login-form" method="POST">
                <label>First name</label>
                <input name='fname' required>
                <br>
                <label>Last name</label>
                <input name='lname' required>
                <br>
                <label>Email ID</label>
                <input type="email" name='email' required>
                <br>
                <label>Mob.:</label>
                <input name='mob' required>
                <br>
                <label>Country.:</label>
                <input name='country' value="India" required>
                <br>
                <label>Address.:</label>
                <textarea name='address'></textarea>
                <br>
                <label>Password.:</label>
                <input type="password" name='pswd' required>
                <br>
                <label>Confirm Password.:</label>
                <input type="password" name='confirmPswd' required>
                <br>
                <input type="submit" id="login" name="Proceed" value="Sign-in">
                <br>
                <br>
            </form>
        </div>
    </body>
</html>
