<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 13, 2023
    Description: New post page.

****************/

require('connect.php'); // Include your database connection file

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
</body>
</html>