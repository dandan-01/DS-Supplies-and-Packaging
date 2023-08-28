<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Blogpost page.

****************/

require('connect.php');

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Sanitize and filter the 'id' parameter as an integer
    $product_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Build the SQL query to retrieve the blog post with the specified ID
    $query = "SELECT packagingsupplies.*, images.filename AS filename 
          FROM packagingsupplies 
          LEFT JOIN images ON packagingsupplies.image_id = images.image_id
          WHERE packagingsupplies.product_id = :product_id";
    $statement = $db->prepare($query);

    // Bind the sanitized ID value to the query parameter
    $statement->bindValue(':product_id', $product_id);

    // Execute the query
    $statement->execute();

    // Fetch the blog post as an associative array
    $post = $statement->fetch(PDO::FETCH_ASSOC);

    // Check if a post was found
    if ($post) {
        $image = $post['filename'];
        $product_name = $post['product_name'];
        $timestamp = date("F d, Y, g:i a", strtotime($post['timestamp'])); // Format the timestamp
        $product_description = $post['product_description'];
        $price = $post['price'];
    } else {
        // If the post is not found, redirect to index.php
        header('Location: index.php');
        exit;
    }
} else {
    // If 'id' parameter is missing, redirect to index.php
    header('Location: index.php');
    exit;
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
    <nav id="adminnav" >
        <h3>User/Admin access only -></h3>
        <a href="add_new_item.php">Add new item</a>
        <a href="categories.php">Create/Update Category</a>
        <a href="sorted_list.php">View Sorted list</a>
        <a href="manage_users.php">Manage Users (admin only)</a>
    </nav>

    <header>
        <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>Boxed N' Loaded</h1>
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
            <a href="register_user.php">Register</a>
            <a href="login.php">Login/Logout</a>
        </nav>
    </header>

    <nav id="productnav">
        <a href="index.php"><h2>HOME</h2></a>
        <a href="category_header_page.php?category_name=Boxes"><h2>BOXES</h2></a>
        <a href="category_header_page.php?category_name=Paperbags"><h2>PAPERBAGS</h2></a>
        <a href="category_header_page.php?category_name=Supplies"><h2>SUPPLIES</h2></a>
    </nav>

    <div id="productdiv" class="center">
        <!-- Display the image if available -->
        <?php if ($image): ?>
            <img src="imgs/<?php echo $image; ?>" alt="<?= $product['product_name']; ?>" height='350' width='450'>
        <?php endif; ?>

        <ul>
            <li>
                <h1><?= $product_name; ?></h1>
            </li>

            <li>
                <h3>Price: $<?= number_format($price, 2); ?></h3>
                <p>Description: <?= $product_description; ?></p>
                <a href="edit.php?id=<?= $product_id; ?>">Edit/Delete Post</a>
            </li>
        </ul>
    </div>

<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>