<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $pass = md5($_POST['password']);

   $sql = "SELECT Account_ID, Email, FName, LName FROM ACCOUNT WHERE Email = ? AND H_Password = ?";
   $options = array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
   $select_users = sqlsrv_query($conn,$sql,array($email,$pass),$options);

   if(sqlsrv_num_rows($select_users) > 0){

      $row = sqlsrv_fetch_array($select_users,SQLSRV_FETCH_ASSOC);

      $id = $row['Account_ID'];

      $sqlCustomer = "SELECT Customer_ID FROM CUSTOMER WHERE Customer_ID = ?";
      $select_customer = sqlsrv_query($conn,$sqlCustomer,array($id),$options);

      if(sqlsrv_fetch_array($select_customer)>0) {
         $_SESSION['user_name'] = $row['LName']." ".$row['FName'];
         $_SESSION['user_email'] = $row['Email'];
         $_SESSION['user_id'] = $row['Account_ID']; 
         header('location:home.php');
      }

      else {
         $_SESSION['admin_name'] = $row['LName']." ".$row['FName'];
         $_SESSION['admin_email'] = $row['Email'];
         $_SESSION['admin_id'] = $row['Account_ID'];
         header('location:admin_page.php');
      }

      // if($row['user_type'] == 'admin'){

      //    $_SESSION['admin_name'] = $row['name'];
      //    $_SESSION['admin_email'] = $row['email'];
      //    $_SESSION['admin_id'] = $row['id'];
      //    header('location:admin_page.php');

      // }elseif($row['user_type'] == 'user'){

      //    $_SESSION['user_name'] = $row['name'];
      //    $_SESSION['user_email'] = $row['email'];
      //    $_SESSION['user_id'] = $row['id'];
      //    header('location:home.php');

      // }

      

   }else{
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

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
      <h3>login now</h3>
      <input type="email" name="email" placeholder="enter your email" required class="box">
      <input type="password" name="password" placeholder="enter your password" required class="box">
      <input type="submit" name="submit" value="login now" class="btn">
      <p>don't have an account? <a href="register.php">register now</a></p>
   </form>

</div>

</body>
</html>