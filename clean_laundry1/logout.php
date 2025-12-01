<?php
session_start();
session_unset();
session_destroy();
header('Location: /clean_laundry1/login.php');
