<?php
    session_start(); //to ensure you are using same session

    require('connect.php');

    $ID = $_GET['ID'];

    $sql = "DELETE FROM customers WHERE customerID=".$ID;
    $conn->query($sql);

   /* $sql = "DELETE FROM ebooks WHERE ID='".$ID;
    $conn->query($sql);
*/
    session_destroy(); //destroy the session
?>
<script>
    window.location="login.php";
</script>
