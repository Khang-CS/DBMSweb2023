<section class="edit-product-form">

    <?php
    if (isset($_GET['update'])) {
        $update_id = $_GET['update'];
        $update_query = sqlsrv_query($conn, "SELECT * FROM BOOK WHERE Book_ID = ?", array($update_id), $options) or die('query failed');
        if (sqlsrv_num_rows($update_query) > 0) {
            while ($fetch_update = sqlsrv_fetch_array($update_query, SQLSRV_FETCH_ASSOC)) {
    ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="Book_ID" value="<?php echo $fetch_update['Book_ID']; ?>">
                    <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['Thumbnail']; ?>">
                    <img src="uploaded_img/<?php echo $fetch_update['Thumbnail']; ?>" alt="">
                    <input type="text" name="Book_name" value="<?php echo $fetch_update['Book_name']; ?>" class="box" required placeholder="enter product name">
                    <input type="number" name="O_Price" value="<?php echo $fetch_update['O_Price']; ?>" min="0" class="box" required placeholder="enter book price">
                    <input type="number" name="Discount" value="<?php echo $fetch_update['Discount']; ?>" min="0" class="box" required placeholder="enter discount">
                    <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
                    <input type="submit" value="update" name="update_product" class="btn">
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