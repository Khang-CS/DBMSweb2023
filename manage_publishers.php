<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}
;

$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

//ADD AUTHOR
if (isset($_POST['add_publisher'])) {

   $publisherName = $_POST['name'];
   $sqlCheck = "SELECT Publisher_ID FROM PUBLISHER WHERE Publisher_name = ?";
   $sqlCheck = sqlsrv_query($conn, $sqlCheck, array($publisherName), $options) or die("Check Publisher exist failed");

   if (sqlsrv_num_rows($sqlCheck) > 0) {
      $message[] = "Publisher was already added ! Please check again !";
   } else {
      $sqlAddPublisher = "INSERT INTO PUBLISHER (Publisher_name) VALUES (?)";
      $sqlAddPublisher = sqlsrv_query($conn, $sqlAddPublisher, array($publisherName), $options);
      $message[] = "Publisher added successfully !";
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $deletePublisher = sqlsrv_query($conn, "DELETE FROM PUBLISHER WHERE Publisher_ID = ?",array($delete_id),$options) or die('delete genre failed !');
   header('location:manage_publishers.php');
}

if (isset($_POST['update_publisher'])) {   

   $update_id = $_POST['update_publisher_id'];
   $update_name = $_POST['update_name'];
   $updatePublisher = sqlsrv_query($conn,"UPDATE PUBLISHER SET Publisher_name = ? WHERE Publisher_ID = ?",array($update_name,$update_id),$options) or die ("update publisher failed !");

   header('location:manage_publishers.php');
}

if(isset($_POST['cancel_update'])) {
   header('location:manage_publishers.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publisher</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

    <?php include 'admin_header.php'; ?>

    <!-- product CRUD section starts  -->

    <section class="add-products">

        <h1 class="title">Publishers</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <h3>add publisher</h3>
            <input type="text" name="name" class="box" placeholder="enter publisher" required>
            <input type="submit" value="add publisher" name="add_publisher" class="btn">
        </form>

    </section>

    <!-- product CRUD section ends -->

    <!-- show products  -->

    <section class="show-products">

        <div class="box-container">

            <?php
            
         $select_publishers = sqlsrv_query($conn, "SELECT * FROM PUBLISHER", array(), $options);

         if (sqlsrv_num_rows($select_publishers) > 0) {
            while ($fetch_publisher = sqlsrv_fetch_array($select_publishers, SQLSRV_FETCH_ASSOC)) {
               ?>
            <div class="box">
                <div class="name">
                    <?php echo $fetch_publisher['Publisher_name']; ?>
                </div>
                <a href="manage_publishers.php?update=<?php echo $fetch_publisher['Publisher_ID']; ?>"
                    class="option-btn">update</a>
                <a href="manage_publishers.php?delete=<?php echo $fetch_publisher['Publisher_ID']; ?>"
                    class="delete-btn" onclick="return confirm('delete this publisher?');">delete</a>
            </div>
            <?php
            }
         } else {
            echo '<p class="empty">no products added yet!</p>';
         }
         ?>
        </div>

    </section>

    <section class="edit-product-form">

        <?php
      if (isset($_GET['update'])) {
         $update_id = $_GET['update'];
         $update_query = sqlsrv_query($conn, "SELECT * FROM PUBLISHER WHERE Publisher_ID = ?",array($update_id),$options) or die('get info failed');
         if (sqlsrv_num_rows($update_query) > 0) {
            while ($fetch_update = sqlsrv_fetch_array($update_query,SQLSRV_FETCH_ASSOC)) {
               ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_publisher_id" value="<?php echo $fetch_update['Publisher_ID']; ?>">
            <input type="text" name="update_name" value="<?php echo $fetch_update['Publisher_name']; ?>" class="box"
                required placeholder="enter publisher name">
            <input type="submit" value="update" name="update_publisher" class="btn">
            <input type="submit" value="cancel" name="cancel_update" class="option-btn">
        </form>
        <?php
            }
         }
      } else {
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
      ?>

    </section>







    <!-- custom admin js file link  -->
    <script src="js/admin_script.js"></script>

</body>

</html>