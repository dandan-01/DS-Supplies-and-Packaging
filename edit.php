<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Edit post page

****************/

require('connect.php');
require('authenticate.php');

// Fetch the product details 
$product_id = $_GET['id'];

$query = "SELECT 
              packagingsupplies.*, 
              images.filename AS filename,
              categories.category_id AS category_id,
              categories.category_name AS category_name
          FROM packagingsupplies 
          LEFT JOIN images ON packagingsupplies.image_id = images.image_id
          LEFT JOIN categories ON packagingsupplies.category_id = categories.category_id
          WHERE packagingsupplies.product_id = :product_id";

$statement = $db->prepare($query);
$statement->bindValue(':product_id', $product_id);
$statement->execute();

$post = $statement->fetch(PDO::FETCH_ASSOC);

// Fetch categories for the drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

// Function to check if the uploaded file is an image
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Update button clicked
        if (!empty($_POST['product_name']) && !empty($_POST['product_description'])) {
            // Sanitize and filter user input
            $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_description = filter_input(INPUT_POST, 'product_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            // Handle image upload
            $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

            if ($image_upload_detected) {
                $image = $_FILES['image']['name'];
                $temporary_image_path = $_FILES['image']['tmp_name'];
                $new_image_path = 'imgs/' . $image;

                if (file_is_an_image($temporary_image_path, $new_image_path)) {
                    move_uploaded_file($temporary_image_path, $new_image_path);

                    // Insert image filename into the images table (assuming you have an "images" table)
                    $insertImageQuery = "INSERT INTO images (filename) VALUES (:filename)";
                    $insertImageStatement = $db->prepare($insertImageQuery);
                    $insertImageStatement->bindValue(':filename', $image);
                    $insertImageStatement->execute();

                    // Get the image_id of the inserted image
                    $image_id = $db->lastInsertId();
                } else {
                    // Invalid image type
                    echo "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
                }
            }

            if (!empty($_FILES['image']['name'])) {
                // Build SQL query with new image and potentially category_id update
                if (!empty($_POST['category_id'])) {
                    $query = "UPDATE packagingsupplies SET product_name = :product_name, product_description = :product_description, price = :price, image_id = :image_id, category_id = :category_id WHERE product_id = :product_id";
                } else {
                    $query = "UPDATE packagingsupplies SET product_name = :product_name, product_description = :product_description, price = :price, image_id = :image_id WHERE product_id = :product_id";
                }
            } else {
                // Build SQL query with no image update and potentially category_id update
                if (!empty($_POST['category_id'])) {
                    $query = "UPDATE packagingsupplies SET product_name = :product_name, product_description = :product_description, price = :price, category_id = :category_id WHERE product_id = :product_id";
                } else {
                    $query = "UPDATE packagingsupplies SET product_name = :product_name, product_description = :product_description, price = :price WHERE product_id = :product_id";
                }
            }

            $statement = $db->prepare($query);

            // Bind values to the parameters
            $statement->bindValue(':product_name', $product_name);
            $statement->bindValue(':product_description', $product_description);
            $statement->bindValue(':price', $price);
            $statement->bindValue(':product_id', $product_id);

            // New image uploaded, bind image_id
            if (!empty($_FILES['image']['name'])) {
                $statement->bindValue(':image_id', $image_id);
            }

            // Category chosen, bind category_id
            if (!empty($_POST['category_id'])) {
                $category_id = $_POST['category_id'];
                $statement->bindValue(':category_id', $category_id);
            }

            // Execute the UPDATE.
            if ($statement->execute()) {
                // Update the filename in the images table if a new image is uploaded
                if ($image_id) {
                    $updateFilenameQuery = "UPDATE images SET filename = :filename WHERE image_id = :image_id";
                    $updateFilenameStatement = $db->prepare($updateFilenameQuery);
                    $updateFilenameStatement->bindValue(':filename', $image);
                    $updateFilenameStatement->bindValue(':image_id', $image_id);
                    $updateFilenameStatement->execute();
                }

                echo "Post updated successfully.";
                header('Location: index.php');
                exit;
            } else {
                echo "Failed to update the post.";
            }
        } else {
            echo "Please provide a name and description for the product.";
        }

        // Handle image removal
        if (isset($_POST['delete_image']) && $_POST['delete_image'] === 'on') {
            // Delete the image from the database 
            $image_id = $post['image_id'] ?? null;

            if ($image_id) {
                // Retrieve the image filename from the database
                $getImageQuery = "SELECT filename FROM images WHERE image_id = :image_id";
                $getImageStatement = $db->prepare($getImageQuery);
                $getImageStatement->bindValue(':image_id', $image_id);
                $getImageStatement->execute();
                $image = $getImageStatement->fetchColumn();

                // Delete the image record from the database
                $deleteImageQuery = "DELETE FROM images WHERE image_id = :image_id";
                $deleteImageStatement = $db->prepare($deleteImageQuery);
                $deleteImageStatement->bindValue(':image_id', $image_id);
                $deleteImageStatement->execute();

                // Remove the image_id association from the product record
                $updateProductQuery = "UPDATE packagingsupplies SET image_id = NULL WHERE product_id = :product_id";
                $updateProductStatement = $db->prepare($updateProductQuery);
                $updateProductStatement->bindValue(':product_id', $product_id);
                $updateProductStatement->execute();
            }
        }

    } elseif (isset($_POST['delete'])) {
        // Delete button clicked
        $query = "DELETE FROM packagingsupplies WHERE product_id = :product_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':product_id', $product_id);

        if ($statement->execute()) {
            echo "Post deleted successfully.";
            header('Location: index.php');
            exit;
        } else {
            echo "Failed to delete the post.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
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
        <a href="categories.php">Create/Update Category</a>
    </nav>

    <header>
        <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>Boxed N' Loaded</h1>
                <br>
                <h4><i>"Your go-to for Shipping Supplies!"</i></h4>
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
        <a href="category_header_page.php?category_name=Boxes"><h2>BOXES</h2></a>
        <a href="category_header_page.php?category_name=Paperbags"><h2>PAPERBAGS</h2></a>
        <a href="category_header_page.php?category_name=Supplies"><h2>SUPPLIES</h2></a>
    </nav>

    <h1>Edit product</h1>

    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <div class="center">
        <form method="POST" enctype="multipart/form-data" >
            <ul>
                <li>
                    <label for="product_name">Name of product:</label><br>
                    <input type="text" id="product_name" name="product_name" value="<?= $post['product_name'] ?? ''; ?>" required size="50">
                </li>

                <li>
                    <label for="product_description">Description:</label><br>
                    <textarea id="product_description" name="product_description" rows="4" cols="50" required><?= $post['product_description'] ?? ''; ?></textarea><br><br>

                    <label for="price">Price:</label><br>
                    <input type="text" id="price" name="price" pattern="^\d+(\.\d{1,2})?$" required size="50"  value="<?= $post['price'] ? number_format($post['price'], 2, '.', ',') : ''; ?>" >
                    <p class="pricemsg">Round the price to 2 decimals max</p>
                </li>

                <!-- Add drop-down category list -->
                <li>
                    <label for="category_id">Select Category:</label><br>
                    <select id="category_id" name="category_id">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['category_id']; ?>" <?= ($post['category_id'] ?? '') == $category['category_id'] ? 'selected' : '' ?>>
                                <?= $category['category_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </li>

                <li>
                    <label for="image">(optional) Upload Image:</label>
                    <input type="file" id="image" name="image">
                </li>

                <!-- Delete image IF there is one -->
                <?php if (!empty($post['image_id'])): ?>
                    <li>
                        <input type="checkbox" id="delete_image" name="delete_image">
                        <label for="delete_image">Remove Image:</label>
                        <img src="imgs/<?= $post['filename']; ?>" alt="Cardboard boxes or supplies" height='200' width='250'><br>
                    </li>
                <?php endif; ?>

                <li>
                    <input type="submit" name="update" value="Update Product" onclick="return confirm('Are you sure you want to UPDATE this post?')">
                    <input type="submit" name="delete" value="Delete Product" onclick="return confirm('Are you sure you want to DELETE this post?')">
                </li>
            </ul>
        </form>
    </div>
</body>
</html>