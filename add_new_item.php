<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: New post page.

****************/

require('connect.php');
require('authenticate.php');

//Testing for image-ness
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type        = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

if ($_POST && !empty($_POST['product_name']) && !empty($_POST['product_description'])) {
    // Sanitize and filter user input
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $product_description = filter_input(INPUT_POST, 'product_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $image = '';

    // Handle image upload if an image is detected
    $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

    if ($image_upload_detected) {
        $image = $_FILES['image']['name'];
        $temporary_image_path = $_FILES['image']['tmp_name'];
        $new_image_path = 'uploads/' . $image;

        if (file_is_an_image($temporary_image_path, $new_image_path)) {
            move_uploaded_file($temporary_image_path, $new_image_path);

            // Insert image filename into the images table
            $insertImageQuery = "INSERT INTO images (filename) VALUES (:filename)";
            $insertImageStatement = $db->prepare($insertImageQuery);
            $insertImageStatement->bindValue(':filename', $image);
            $insertImageStatement->execute();

            // Get the image ID from the last inserted row in the "images" table
            $imageId = $db->lastInsertId();

            // Associate the image ID with the new product entry
            $query = "INSERT INTO packagingsupplies (product_name, product_description, image_id) 
                      VALUES (:product_name, :product_description, :image_id)";
            $statement = $db->prepare($query);

            // Bind values to the parameters
            $statement->bindValue(':product_name', $product_name);
            $statement->bindValue(':product_description', $product_description);
            $statement->bindValue(':image_id', $imageId);

            // Execute the INSERT.
            // execute() will check for possible SQL injection and remove if necessary
            if ($statement->execute()) {
                echo "Success";
                header('Location: index.php');
                exit;
            }
        } else {
            // Invalid image type
            echo "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
            exit; // or handle the error appropriately
        }
    } else {
        // If no image is uploaded, proceed with inserting other product data without an image
        $query = "INSERT INTO packagingsupplies (product_name, product_description) 
                  VALUES (:product_name, :product_description)";
        $statement = $db->prepare($query);

        // Bind values to the parameters
        $statement->bindValue(':product_name', $product_name);
        $statement->bindValue(':product_description', $product_description);

        // Execute the INSERT.
        // execute() will check for possible SQL injection and remove if necessary
        if ($statement->execute()) {
            echo "Success";
            header('Location: index.php');
            exit;
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
    <link rel="stylesheet" href="main.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Boxed N Loaded</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <nav id="adminnav">
        <h3>Welcome User!</h3>
        <a href="add_new_item.php">Add new item</a>
    </nav>

    <header>
        <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>Boxed N' Loaded</h1>
                <br>
                <h4><i>"Expertly Packed N' Safely Delivered to You!"</i></h4>
            </a>
        </div>

        <div class="searchNav">
            <input type="search" name="search" id="search" placeholder="Search...">
            <button type="submit"><i class="fa fa-search"></i></button>
        </div>

        <nav id="topright">
            <button>Register</button>
            <a href="index.php">Log In</a>
            <a href="new_post.php">Cart</a>
        </nav>
    </header>

    <nav id="productnav">
        <a href="index.php"><h2>HOME</h2></a>
        <a href=""><h2>BOXES</h2></a>
        <a href=""><h2>PAPERBAGS</h2></a>
        <a href=""><h2>SUPPLIES</h2></a>
    </nav>


    <h1>New Blog Post</h1>

    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="product_name">Name of product:</label><br>
        <input type="text" id="product_name" name="product_name" required><br>

        <label for="product_description">Description:</label><br>
        <textarea id="product_description" name="product_description" rows="4" cols="50" required></textarea><br>

        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image">

        <input type="submit" value="Submit">
    </form>
</body>
</html>