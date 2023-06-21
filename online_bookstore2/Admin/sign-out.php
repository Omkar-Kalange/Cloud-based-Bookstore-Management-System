<?php
    session_start(); //to ensure you are using same session

    require('connect.php');

    $ID = $_GET['ID'];

    $sql = "DELETE FROM admins WHERE adminID=".$ID;
    $conn->query($sql);

    session_destroy(); //destroy the session
?>
<script>
    window.location="login.php";
</script>
