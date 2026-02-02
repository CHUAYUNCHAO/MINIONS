<?php
session_start();
session_destroy(); 
header("Location: custloginandregister.php"); 
exit();
?>