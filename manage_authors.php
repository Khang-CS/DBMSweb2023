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
if (isset($_POST['add_author'])) {

   $authorName = $_POST['name'];
   $sqlCheck = "SELECT Author_ID FROM AUTHOR WHERE Author_name = ?";
   $sqlCheck = sqlsrv_query($conn, $sqlCheck, array($authorName), $options) or die("Check author exist failed");

   if (sqlsrv_num_rows($sqlCheck) > 0) {
      $message[] = "Author was already added ! Please check again !";
   } else {
      $sqlAddAuthor = "INSERT INTO AUTHOR (Author_name) VALUES (?)";
      $sqlAddAuthor = sqlsrv_query($conn, $sqlAddAuthor, array($authorName), $options);
      $message[] = "Author added successfully !";
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $deleteAuthor = sqlsrv_query($conn, "DELETE FROM AUTHOR WHERE Author_ID = ?",array($delete_id),$options) or die('delete author failed !');
   header('location:manage_authors.php');
}

if (isset($_POST['update_author'])) {   

   $update_id = $_POST['update_author_id'];
   $update_name = $_POST['update_name'];
   $updateAuthor = sqlsrv_query($conn,"UPDATE AUTHOR SET Author_name = ? WHERE Author_ID = ?",array($update_name,$update_id),$options) or die ("update author failed !");

   header('location:manage_authors.php');

}

if(isset($_POST['cancel_update'])) {
   header('location:manage_authors.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

    <?php include 'admin_header.php'; ?>

    <!-- product CRUD section starts  -->

    <section class="add-products">

        <h1 class="title">Authors</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <h3>add author</h3>
            <input type="text" name="name" class="box" placeholder="enter author name" required>
            <input type="submit" value="add author" name="add_author" class="btn">
        </form>

    </section>

    <!-- product CRUD section ends -->

    <!-- show products  -->

    <section class="show-products">

        <div class="box-container">

            <?php
         // $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
         $select_authors = sqlsrv_query($conn, "SELECT * FROM AUTHOR", array(), $options);

         if (sqlsrv_num_rows($select_authors) > 0) {
            while ($fetch_author = sqlsrv_fetch_array($select_authors, SQLSRV_FETCH_ASSOC)) {
               ?>
            <div class="box">
                <div class="name">
                    <?php echo $fetch_author['Author_name']; ?>
                </div>
                <a href="manage_authors.php?update=<?php echo $fetch_author['Author_ID']; ?>"
                    class="option-btn">update</a>
                <a href="manage_authors.php?delete=<?php echo $fetch_author['Author_ID']; ?>" class="delete-btn"
                    onclick="return confirm('delete this product?');">delete</a>
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
         $update_query = sqlsrv_query($conn, "SELECT * FROM AUTHOR WHERE Author_ID = ?",array($update_id),$options) or die('get info failed');
         if (sqlsrv_num_rows($update_query) > 0) {
            while ($fetch_update = sqlsrv_fetch_array($update_query,SQLSRV_FETCH_ASSOC)) {
               ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_author_id" value="<?php echo $fetch_update['Author_ID']; ?>">
            <input type="text" name="update_name" value="<?php echo $fetch_update['Author_name']; ?>" class="box"
                required placeholder="enter author name">
            <input type="submit" value="update" name="update_author" class="btn">
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