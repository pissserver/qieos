<?php
    session_start();
    session_destroy();
    header("Location: ../sessions/sign-in.php?success=logout");
    exit();
