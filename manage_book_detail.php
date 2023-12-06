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

function getGenreStringFromDB($Book_ID, $options, $conn)
{
    $Genre_name = "";
    $GetGenre = sqlsrv_query($conn, "SELECT GENRE.Genre_name FROM GENRE, BOOK, BELONGS_TO WHERE BOOK.Book_ID = ? AND BOOK.Book_ID = BELONGS_TO.Book_ID AND GENRE.Genre_ID = BELONGS_TO.Genre_ID", array($Book_ID), $options);
    if (sqlsrv_num_rows($GetGenre) > 0) {
        while ($fetch_Genre_name = sqlsrv_fetch_array($GetGenre, SQLSRV_FETCH_ASSOC)) {
            $Genre_name = $Genre_name . $fetch_Genre_name['Genre_name'] . ", ";
        }
    }
    return substr($Genre_name, 0, strlen($Genre_name) - 2);
}

function getAuthorStringFromDB($Book_ID, $options, $conn)
{
    $Author_name = "";
    $GetAuthor = sqlsrv_query($conn, "SELECT AUTHOR.Author_name FROM AUTHOR, BOOK, WRITE WHERE BOOK.Book_ID = ? AND BOOK.Book_ID = WRITE.Book_ID AND AUTHOR.Author_ID = WRITE.Author_ID", array($Book_ID), $options);
    if (sqlsrv_num_rows($GetAuthor) > 0) {
        while ($fetch_Author_name = sqlsrv_fetch_array($GetAuthor, SQLSRV_FETCH_ASSOC)) {
            $Author_name = $Author_name . $fetch_Author_name['Author_name'] . ", ";
        }
    }
    return substr($Author_name, 0, strlen($Author_name) - 2);
}

if (isset($_POST['update_book'])) {

    $Book_ID = $_POST['Book_ID'];
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

    //IMAGE
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/' . $update_image;
    $update_old_image = $_POST['update_old_image'];
    //END.

    //UPDATE INTERNAL FIELD: Book_name, O_Price, Discount, Publish_year, Quantity, Description
    $sql_update = sqlsrv_query($conn, "UPDATE BOOK SET Book_name = ?, O_Price = ?, Discount = ?, Publish_year = ?, Quantity = ?,Description = ? WHERE Book_ID = ?", array($Book_name, $O_Price, $Discount, $Publish_year, $Quantity, $Description, $Book_ID), $options) or die("Update internal field failed");

    //THUMBNAIL HANDLING
    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'image file size is too large';
        } else {
            sqlsrv_query($conn, "UPDATE BOOK SET Thumbnail = ? WHERE Book_ID = ?", array($update_image, $Book_ID), $options) or die('query failed');
            move_uploaded_file($update_image_tmp_name, $update_folder);
            if ($update_old_image !== $update_image) {
                unlink('uploaded_img/' . $update_old_image);
            }
        }
    }
    //END.

    //GENRE HANDLING
    $delete_Old_Genre_In_Belongs_to = sqlsrv_query($conn, "DELETE FROM BELONGS_TO WHERE Book_ID = ?", array($Book_ID), $options) or die("Delete old genre belongs_to failed !");
    $position = 0;
    while (true) {
        $position = strpos($Genre_string, ',');
        if ($position !== false) {

            $GenreGet = substr($Genre_string, 0, $position); //Genre_name
            $Genre_string = substr($Genre_string, $position + 2);

            $message[] = insertBelongs_to($GenreGet, $Book_ID, $options, $conn);
        } else {
            $message[] = insertBelongs_to($Genre_string, $Book_ID, $options, $conn); //Genre_name
            break;
        }
    }
    //END.

    //AUTHOR HANDLING
    $delete_Old_Author_Write = sqlsrv_query($conn, "DELETE FROM WRITE WHERE Book_ID = ?", array($Book_ID), $options) or die("Delete old Author Write failed !");
    $position = 0;
    while (true) {
        $position = strpos($Author_string, ',');
        if ($position !== false) {

            $AuthorGet = substr($Author_string, 0, $position); //Author_name
            $Author_string = substr($Author_string, $position + 2);

            $message[] = insertWrite($AuthorGet, $Book_ID, $options, $conn);
        } else {
            $message[] = insertWrite($Author_string, $Book_ID, $options, $conn); //Author_name
            break;
        }
    }
    //END.

    //PUBLISHER HANDLING
    $update_publish = sqlsrv_query($conn, "UPDATE PUBLISH SET Publisher_ID = ? WHERE Book_ID = ?", array($Publisher_ID, $Book_ID), $options) or die("Update publisher failed");
}


if (isset($_POST['return'])) {
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
    <?php
    if (isset($_GET['update'])) {
        $Book_ID = $_GET['update'];
        $update_query = sqlsrv_query($conn, "SELECT * FROM BOOK WHERE Book_ID = ?", array($Book_ID), $options) or die("GET BOOK INFO FAILED");
        if (sqlsrv_num_rows($update_query) > 0) {
            while ($fetch_update = sqlsrv_fetch_array($update_query, SQLSRV_FETCH_ASSOC)) {
    ?>
                <section class="add-products">

                    <h1 class="title">EDIT BOOK</h1>

                    <form action="" method="post" enctype="multipart/form-data">
                        <h3><?php echo $fetch_update['Book_name']; ?></h3>
                        <input type="hidden" name="Book_ID" value="<?php echo $Book_ID ?>">
                        <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['Thumbnail']; ?>">
                        <img style="width: 300px;" src="uploaded_img/<?php echo $fetch_update['Thumbnail']; ?>" alt="#">
                        <br>

                        <label style="width: 100%; " for="bookName">Enter book name:</label>
                        <input id="bookName" type="text" name="Book_name" class="box" value="<?php echo $fetch_update['Book_name']; ?>" placeholder="enter book name" required>
                        <br>

                        <label style="width: 100%; " for="genreName">Enter book genre:</label>
                        <input id="genreName" type="text" name="Genre_name" class="box" value="<?php echo getGenreStringFromDB($Book_ID, $options, $conn) ?>" placeholder="enter book genre" required>

                        <label style="width: 100%; " for="authorName">Enter book author:</label>
                        <input id="authorName" type="text" name="Author_name" class="box" value="<?php echo getAuthorStringFromDB($Book_ID, $options, $conn); ?>" placeholder="enter book author" required>

                        <label style="width: 50%; float: left;" for="authorName">Enter original price ($):</label>
                        <label style="width: 50%;" for="authorName">Enter discount:</label>
                        <input style="width: 50%; float: left;" type="number" min="0" name="O_Price" class="box" value="<?php echo $fetch_update['O_Price']; ?>" placeholder="enter original price" required>
                        <input style="width: 50%;" type="number" min="0" max="100" name="Discount" class="box" value="<?php echo $fetch_update['Discount']; ?>" placeholder="enter discount">


                        <label style="width: 50%; float: left;" for="authorName">Enter publish year:</label>
                        <label style="width: 50%;" for="authorName">Enter Quantity:</label>
                        <input style="width: 50%; float: left;" type="number" min="1000" max="<?php echo date("Y"); ?>" name="Publish_year" class="box" value="<?php echo $fetch_update['Publish_year']; ?>" placeholder="enter publish year" required>
                        <input style="width: 50%;" type="number" min="0" name="Quantity" class="box" value="<?php echo $fetch_update['Quantity']; ?>" placeholder="enter quantity" required>


                        <div style="width: 50%; height: 45px; float: left;" class="box">
                            <label for="Publisher">Publisher:</label>
                            <select id="Publisher" name="Publisher_ID">
                                <?php
                                $get_Publisher_ID = sqlsrv_query($conn, "SELECT Publisher_ID FROM PUBLISH WHERE Book_ID = ?", array($Book_ID), $options);
                                $get_Publisher_ID = sqlsrv_fetch_array($get_Publisher_ID, SQLSRV_FETCH_ASSOC);
                                $get_Publisher_ID = $get_Publisher_ID['Publisher_ID'];

                                $select_publishers = sqlsrv_query($conn, "SELECT* FROM PUBLISHER", array(), $options);
                                if (sqlsrv_num_rows($select_publishers) > 0) {
                                    while ($fetch_publisher = sqlsrv_fetch_array($select_publishers, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                        <?php if ($fetch_publisher['Publisher_ID'] === $get_Publisher_ID) { ?>
                                            <option value="<?php echo $fetch_publisher['Publisher_ID']; ?>" selected>
                                                <?php echo $fetch_publisher['Publisher_name']; ?></option>

                                        <?php
                                        } else {
                                        ?>
                                            <option value="<?php echo $fetch_publisher['Publisher_ID']; ?>">
                                                <?php echo $fetch_publisher['Publisher_name']; ?></option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <input style="width: 50%; height: 45px;" type="file" name="update_image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">

                        <div class="box">
                            <label for="descriptionInput">Enter description:</label>
                            <textarea name="Description" id="descriptionInput" cols="50" rows="10"><?php echo $fetch_update['Description']; ?></textarea>
                        </div>
                        <input type="submit" value="Go to Books page" name="return" class="btn">
                        <input class="option-btn" type="submit" value="update book" name="update_book" class="btn">

                    </form>

                </section>
    <?php
            }
        }
    } ?>



    <!-- product CRUD section ends -->

    <!-- show products  -->









    <!-- custom admin js file link  -->
    <script src="js/admin_script.js"></script>


</body>

</html>