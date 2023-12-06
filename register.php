<?php

include 'config.php';

if(isset($_POST['submit'])){

   //GET INPUT DATA
   $FName = $_POST['FName'];
   $LName = $_POST['LName'];
   $Email = $_POST['Email'];
   $TelephoneNum = $_POST['TelephoneNum'];
   $Birthday = $_POST['Birthday'];
   $Address_M = $_POST['Address_M'];
   $password = md5($_POST['password']);
   $cpassword = md5($_POST['cpassword']);

   $checkSql = "SELECT Email FROM ACCOUNT WHERE Email = ?";
   $options = array( "Scrollable" => SQLSRV_CURSOR_KEYSET );

   //CHECK IF USER EXISTED
   $checkUser = sqlsrv_query($conn,$checkSql,array($Email),$options) or die ("query select failed !");

   $num = sqlsrv_num_rows($checkUser);

   if ($num > 0) {
      $message[] = "An Email has been already registered !";
   }
   else {
      if($password != $cpassword) {
         // CHECK IF CONFIRM PASSWORD CORRECT
         $message[] = "Confirm Password does not match !";
      }
      else {
         // INSERT NEW ACCOUNT DATA TO DATABASE
         $sql = "INSERT INTO ACCOUNT (FName,LName,Email,TelephoneNum,H_Password,Birthday,Address_M) VALUES (?,?,?,?,?,?,?)";

         $addNewUser = sqlsrv_query($conn,$sql,array($FName,$LName,$Email,$TelephoneNum,$password,$Birthday,$Address_M)) or die ("Register failed, Email and Phone number must be unique !");

         $sql2 = "SELECT Account_ID FROM ACCOUNT WHERE Email = ?";
         $getAccID = sqlsrv_query($conn,$sql2,array($Email)) or die ("Get ID failed");

         $row = sqlsrv_fetch_array($getAccID,SQLSRV_FETCH_ASSOC);

         $Customer_ID = $row['Account_ID'];

         $sql3 = "INSERT INTO CUSTOMER (Customer_ID) VALUES (?)";

         $insertCustomerID = sqlsrv_query($conn,$sql3,array($Customer_ID));




         
         // header('location:login.php');
         $message[] = "registered successfully! please click Login";
      }
   } 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/style.css">

</head>
<body>



<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>register now</h3>
      <input type="text" name="FName" placeholder="enter your first name" required class="box">
      <input type="text" name="LName" placeholder="enter your last name" required class="box">
      <input type="email" name="Email" placeholder="enter your email" required class="box">
      <input type="tel" name="TelephoneNum" placeholder="enter your phone number" required class="box">
      <input type="date" name="Birthday" placeholder="enter your Birthday" required class="box">
      <input type="text" name="Address_M" placeholder="enter your address" required class="box">
      <input type="password" name="password" placeholder="enter your password" required class="box">
      <input type="password" name="cpassword" placeholder="confirm your password" required class="box">
      <!-- <select name="user_type" class="box">
         <option value="user">user</option>
         <option value="admin">admin</option>
      </select> -->
      <input type="submit" name="submit" value="register now" class="btn">
      <p>already have an account?<a href="login.php">login now</a></p>
   </form>

</div>

</body>
</html>