<?php
session_start();
session_destroy();
// Redirect dengan javascript biar smooth
echo "<script>
        alert('Anda berhasil logout!');
        window.location='login.php';
      </script>";
?>