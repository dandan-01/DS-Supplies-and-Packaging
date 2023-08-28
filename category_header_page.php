<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 13, 2023
    Description: View items according to the selected category.

****************/

require('connect.php'); 

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['category_name'])) {
    $category_name = $_GET['category_name'];
    
    // Fetch products for the specific category 
    $query = "SELECT packagingsupplies.*, images.filename AS image_filename
            FROM packagingsupplies
            JOIN categories ON packagingsupplies.category_id = categories.category_id
            LEFT JOIN images ON packagingsupplies.image_id = images.image_id
            WHERE categories.category_name = :category_name";
    $statement = $db->prepare($query);
    $statement->bindValue(':category_name', $category_name);
    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);
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

    <div id="products">
            <ul>
            <?php foreach ($products as $product): ?>
                <li>
                    <div class="center">
                        <h2><a href="product.php?id=<?= $product['product_id']; ?>"><?= $product['product_name']; ?></a></h2>
                    </div>

                    <!-- Display the image if available -->
                    <?php if ($product['image_id']): ?>
                        <div class="center">
                            <img src="imgs/<?= $product['image_filename']; ?>" alt="<?= $product['product_name']; ?>" height='200' width='250'>
                        </div>
                    <?php endif; ?>

                    <div class="products-content center">
                        <p>Price: $<?= number_format($product['price'], 2); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>

<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>