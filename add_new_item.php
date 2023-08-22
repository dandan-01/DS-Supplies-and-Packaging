<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: New post page.

****************/

require('authenticate.php');

// Fetch categories to populate AJAX search form drop-down list
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

if ($_POST && !empty($_POST['product_name']) && !empty($_POST['product_description']) && !empty($_POST['price'])) {
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

    // Retrieve selected category ID
    $category_id = $_POST['category_id'];

    // Build SQL query and bind to the above sanitized values.
    $query = "INSERT INTO packagingsupplies (product_name, product_description, price, category_id) 
              VALUES (:product_name, :product_description, :price, :category_id)";

    if (isset($image_id)) {
        $query .= "; UPDATE packagingsupplies SET image_id = :image_id WHERE product_id = LAST_INSERT_ID()";
    }

    $statement = $db->prepare($query);

    // Bind values to the parameters
    $statement->bindValue(':product_name', $product_name);
    $statement->bindValue(':product_description', $product_description);
    $statement->bindValue(':price', $price);
    $statement->bindValue(':category_id', $category_id);

    if (isset($image_id)) {
        $statement->bindValue(':image_id', $image_id);
    }

    // Execute the INSERT.
    // execute() will check for possible SQL injection and remove if necessary
    if ($statement->execute()) {
        echo "Success";
        header('Location: index.php');
        exit;
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
            <form action="search_results.php" method="GET">
                <ul>
                    <li>
                        <input type="search" name="search_query" id="search" placeholder="Search...">

                        <!-- Search using specific categories -->
                        <select name="category">
                            <option value="">All</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id']; ?>"><?= $category['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>

                    <li>
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </li>
                </ul>
            </form>
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


    <h1>Add new item</h1>

    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form class="center" method="POST" enctype="multipart/form-data">
        <ul>
            <li>
                <label for="product_name">Name of product:</label><br>
                <input type="text" id="product_name" name="product_name" required size="50"><br>
            </li>
        
            <li>
                <label for="product_description">Description:</label><br>
                <textarea id="product_description" name="product_description" rows="4" cols="50" required></textarea><br>

                <label for="price">Price:</label><br>
                <input type="text" id="price" name="price" pattern="^\d+(\.\d{1,2})?$" required size="50" placeholder="eg. 29.99">
                <p class="pricemsg">Round the price to 2 decimals</p>
            </li>

            <li>
                <label for="category_id">Select Category:</label><br>
                <select id="category_id" name="category_id">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id']; ?>"><?= $category['category_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </li>
        
            <li>
                <label for="image">(optional) Upload Image:</label>
                <input type="file" id="image" name="image">
            </li>
        
            <li>
                <input id="submitbtn" type="submit" value="Submit">
            </li>
    </form>
</body>
</html>