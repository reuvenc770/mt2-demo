<?php
 function audit() {
  session_start();
  $digit = $_SESSION['digit'];
  $userdigit = $_POST['userdigit']; 
  session_destroy();   
  
  if (($digit == $userdigit) && ($digit > 1)) {
    return true;
  } else {
    return false;
  }
 
}
?>