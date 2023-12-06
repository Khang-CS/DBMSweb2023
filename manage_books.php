<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
};
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

//SUPPORT FUNCTION

function insertBelongs_to($Genre_name, $Book_ID, $options, $conn)
{
   $checkGenre = sqlsrv_query($conn, "SELECT Genre_ID FROM GENRE WHERE Genre_name = ?", array($Genre_name), $options);
   $message = "";

   if (sqlsrv_num_rows($checkGenre) > 0) {
      $get_Genre_ID = sqlsrv_fetch_array($checkGenre, SQLSRV_FETCH_ASSOC);
      $get_Genre_ID = $get_Genre_ID['Genre_ID'];

      $sql = sqlsrv_query($conn, "INSERT INTO BELONGS_TO (Genre_ID, Book_ID) VALUES (?,?)", array($get_Genre_ID, $Book_ID), $options) or die("INSERT_ACTION BELONGS_TO FAILED !");
      sqlsrv_free_stmt($sql);

      $message = "GENRE UPDATED !";
   } else {
      $insertNewGenre = sqlsrv_query($conn, "INSERT INTO GENRE (Genre_name) VALUES (?)", array($Genre_name), $options) or die("INSERT_NEW GENRE FAILED");
      $get_Genre_ID = sqlsrv_query($conn, "SELECT TOP 1 Genre_ID FROM GENRE ORDER BY Genre_ID DESC", array(), $options) or die("GET GENRE ID FAILED");

      $get_Genre_ID = sqlsrv_fetch_array($get_Genre_ID, SQLSRV_FETCH_ASSOC);
      $get_Genre_ID = $get_Genre_ID['Genre_ID'];

      $sql = sqlsrv_query($conn, "INSERT INTO BELONGS_TO (Genre_ID, Book_ID) VALUES (?,?)", array($get_Genre_ID, $Book_ID), $options) or die("INSERT_ACTION BELONGS_TO FAILED !");


      $message = "New Genre: " . $Genre_name . " is added !";
   }

   return $message;
}

function insertWrite($Author_name, $Book_ID, $options, $conn)
{
   $checkAuthor = sqlsrv_query($conn, "SELECT Author_ID FROM AUTHOR WHERE Author_name = ?", array($Author_name), $options);
   $message = "";

   if (sqlsrv_num_rows($checkAuthor) > 0) {
      $get_Author_ID = sqlsrv_fetch_array($checkAuthor, SQLSRV_FETCH_ASSOC);
      $get_Author_ID = $get_Author_ID['Author_ID'];

      $sql = sqlsrv_query($conn, "INSERT INTO WRITE (Author_ID, Book_ID) VALUES (?,?)", array($get_Author_ID, $Book_ID), $options) or die("INSERT_ACTION WRITE FAILED !");
      sqlsrv_free_stmt($sql);

      $message = "AUTHOR UPDATED !";
   } else {
      $insertNewAuthor = sqlsrv_query($conn, "INSERT INTO AUTHOR (Author_name) VALUES (?)", array($Author_name), $options) or die("INSERT_NEW AUTHOR FAILED");
      $get_Author_ID = sqlsrv_query($conn, "SELECT TOP 1 Author_ID FROM AUTHOR ORDER BY Author_ID DESC", array(), $options) or die("GET AUTHOR ID FAILED");

      $get_Author_ID = sqlsrv_fetch_array($get_Author_ID, SQLSRV_FETCH_ASSOC);
      $get_Author_ID = $get_Author_ID['Author_ID'];

      $sql = sqlsrv_query($conn, "INSERT INTO WRITE (Author_ID, Book_ID) VALUES (?,?)", array($get_Author_ID, $Book_ID), $options) or die("INSERT_ACTION WRITE FAILED !");


      $message = "New Author: " . $Author_name . " is added !";
   }

   return $message;
}



if (isset($_POST['add_book'])) {

   $Book_name = $_POST['Book_name'];
   $O_Price = $_POST['O_Price'];
   $Discount = $_POST['Discount'];
   $Publish_year = $_POST['Publish_year'];
   $Quantity = $_POST['Quantity'];

   $Publisher_ID = $_POST['Publisher_ID'];
   $Description = $_POST['Description'];

   //MULTIPLE VALUE FIELD.
   $Genre_string = $_POST['Genre_name'];
   $Author_string = $_POST['Author_name'];
   //END.  


   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;

   $select_book_name = sqlsrv_query($conn, "SELECT Book_name FROM BOOK WHERE Book_name = ?", array($Book_name), $options) or die('query book_name failed');

   if (sqlsrv_num_rows($select_book_name) > 0) {
      $message[] = 'book name already added';
   } else {
      $add_book_query = sqlsrv_query($conn, "INSERT INTO BOOK (Book_name, O_Price, Discount, Publish_year, Quantity, Thumbnail, Description) VALUES(?,?,?,?,?,?,?)", array($Book_name, $O_Price, $Discount, $Publish_year, $Quantity, $image, $Description), $options) or die('query insert book failed');

      if ($add_book_query) {
         if ($image_size > 2000000) {
            $message[] = 'image size is too large';
         } else {
            move_uploaded_file($image_tmp_name, $image_folder);

            //PUBLISHER HANDLING
            $Get_Book_ID = sqlsrv_query($conn, "SELECT TOP 1 Book_ID FROM BOOK ORDER BY Book_ID DESC;", array(), $options) or die("Get book ID failed");
            $Get_Book_ID = sqlsrv_fetch_array($Get_Book_ID, SQLSRV_FETCH_ASSOC);
            $Get_Book_ID = $Get_Book_ID['Book_ID'];

            $Insert_publish = sqlsrv_query($conn, "INSERT INTO PUBLISH (Publisher_ID, Book_ID) VALUES (?,?)", array($Publisher_ID, $Get_Book_ID), $options);
            //END.

            //GENRE HANDLING
            $position = 0;
            while (true) {
               $position = strpos($Genre_string, ',');
               if ($position !== false) {

                  $GenreGet = substr($Genre_string, 0, $position); //Genre_name
                  $Genre_string = substr($Genre_string, $position + 2);

                  $message[] = insertBelongs_to($GenreGet, $Get_Book_ID, $options, $conn);
               } else {
                  $message[] = insertBelongs_to($Genre_string, $Get_Book_ID, $options, $conn); //Genre_name
                  break;
               }
            }
            //END.

            //AUTHOR HANDLING
            $position = 0;
            while (true) {
               $position = strpos($Author_string, ',');
               if ($position !== false) {

                  $AuthorGet = substr($Author_string, 0, $position); //Author_name
                  $Author_string = substr($Author_string, $position + 2);

                  $message[] = insertWrite($AuthorGet, $Get_Book_ID, $options, $conn);
               } else {
                  $message[] = insertWrite($Author_string, $Get_Book_ID, $options, $conn); //Author_name
                  break;
               }
            }


            $message[] = 'product added successfully! ';
         }
      } else {
         $message[] = 'product could not be added!';
      }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_image_query = sqlsrv_query($conn, "SELECT Thumbnail FROM BOOK WHERE Book_ID =  ?", array($delete_id), $options) or die('query failed');
   $fetch_delete_image = sqlsrv_fetch_array($delete_image_query, SQLSRV_FETCH_ASSOC);
   unlink('uploaded_img/' . $fetch_delete_image['Thumbnail']);
   sqlsrv_query($conn, "DELETE FROM BELONGS_TO WHERE Book_ID = ?", array($delete_id), $options) or die('Delete Belongs_to failed');
   sqlsrv_query($conn, "DELETE FROM WRITE WHERE Book_ID = ?", array($delete_id), $options) or die("Delete Write failed");
   sqlsrv_query($conn, "DELETE FROM PUBLISH WHERE Book_ID = ?", array($delete_id), $options) or die("Delete PUBLISH failed");
   sqlsrv_query($conn, "DELETE FROM BOOK WHERE Book_ID = ?", array($delete_id), $options) or die('query failed');

   $message[] = "Delete Successfully";
   header('location:manage_books.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>manage books</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

   <?php include 'admin_header.php'; ?>

   <!-- product CRUD section starts  -->

   <section class="add-products">

      <h1 class="title">manage books</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <h3>add book</h3>
         <input type="text" name="Book_name" class="box" placeholder="enter book name" required>
         <input type="text" name="Genre_name" class="box" placeholder="enter book genre" required>
         <input type="text" name="Author_name" class="box" placeholder="enter book author" required>
         <input style="width: 50%; float: left;" type="number" min="0" name="O_Price" class="box" placeholder="enter original price" required>
         <input style="width: 50%;" type="number" min="0" max="100" name="Discount" class="box" placeholder="enter discount">


         <input style="width: 50%; float: left;" type="number" min="1000" max="<?php echo date("Y"); ?>" name="Publish_year" class="box" placeholder="enter publish year" required>
         <input style="width: 50%;" type="number" min="0" name="Quantity" class="box" placeholder="enter quantity" required>
         <div style="width: 50%; height: 45px; float: left;" class="box">
            <label for="Publisher">Publisher:</label>
            <select id="Publisher" name="Publisher_ID">
               <?php
               $select_publishers = sqlsrv_query($conn, "SELECT* FROM PUBLISHER", array(), $options);
               if (sqlsrv_num_rows($select_publishers) > 0) {
                  while ($fetch_publisher = sqlsrv_fetch_array($select_publishers, SQLSRV_FETCH_ASSOC)) {
               ?>
                     <option value="<?php echo $fetch_publisher['Publisher_ID']; ?>">
                        <?php echo $fetch_publisher['Publisher_name']; ?></option>

               <?php
                  }
               }
               ?>
            </select>
         </div>

         <input style="width: 50%; height: 45px;" type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>

         <div class="box">
            <label for="descriptionInput">Enter description:</label>
            <textarea name="Description" id="descriptionInput" cols="50" rows="10"></textarea>
         </div>
         <input type="submit" value="add book" name="add_book" class="btn">
      </form>

   </section>



   <!-- product CRUD section ends -->

   <!-- show products  -->

   <section class="show-products">

      <div class="box-container">

         <?php
         $select_books = sqlsrv_query($conn, "SELECT * FROM BOOK", array(), $options) or die('query book list failed');
         if (sqlsrv_num_rows($select_books) > 0) {
            while ($fetch_book = sqlsrv_fetch_array($select_books, SQLSRV_FETCH_ASSOC)) {
         ?>
               <div class="box">
                  <img style="height: 200px;" src="uploaded_img/<?php echo $fetch_book['Thumbnail']; ?>" alt="">
                  <div class="name"><?php echo substr($fetch_book['Book_name'], 0, 20); ?></div>
                  <div class="price">$<?php echo $fetch_book['O_Price']; ?>/-</div>
                  <a href="manage_book_detail.php?update=<?php echo $fetch_book['Book_ID']; ?>" class="option-btn">update</a>
                  <a href="manage_books.php?delete=<?php echo $fetch_book['Book_ID']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">no products added yet!</p>';
         }
         ?>
      </div>

   </section>



   <!-- custom admin js file link  -->
   <script src="js/admin_script.js"></script>


</body>

</html>