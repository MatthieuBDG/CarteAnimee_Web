<?php

session_start();

if (isset($_SESSION['ID_User'])) {
  $_SESSION = array();
  session_destroy();
  header("location:" .  $_SERVER['HTTP_REFERER']);
} else {
  header('Location:../');
}
