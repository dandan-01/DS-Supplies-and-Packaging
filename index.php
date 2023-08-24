<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Simple Content Management System for a DS Supplies and Packaging.

****************/

require('connect.php');

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

// Sort items
$sortingOption = isset($_GET['sort']) ? $_GET['sort'] : '';

// Define the default sorting if no option is selected
$orderBy = '';

// Determine the sorting based on the selected option
switch ($sortingOption) {
    case 'newly_added':
        $orderBy = 'ORDER BY packagingsupplies.product_id DESC';
        break;
    case 'price_low_high':
        $orderBy = 'ORDER BY packagingsupplies.price ASC';
        break;
    case 'price_high_low':
        $orderBy = 'ORDER BY packagingsupplies.price DESC';
        break;
    // Default case: Sort Alphabetically
    case 'alphabetical':
    default:
        $orderBy = 'ORDER BY packagingsupplies.product_name ASC';
}

// Update SQL query to use $orderBy switchcase
$query = "SELECT packagingsupplies.*, images.filename AS image_filename 
          FROM packagingsupplies 
          LEFT JOIN images ON packagingsupplies.image_id = images.image_id
          $orderBy";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 

// Check if there are any rows returned
if ($statement->rowCount() === 0) {
    $error = "<p>No products were found.</p>";
}

function shorten200($content, $maxLength = 200) {
    if (strlen($content) > $maxLength) {
        $excerpt = substr($content, 0, $maxLength) . '...';
        return $excerpt;
    } else {
        return $content;
    } //
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
            <a href="register_user.php">Register</a>
            <a href="login.php">Login/Logout</a>
            <a href="new_post.php">Cart</a>
        </nav>
    </header>

    <nav id="productnav">
        <a href="index.php"><h2>HOME</h2></a>
        <a href="category_header_page.php?category_name=Boxes"><h2>BOXES</h2></a>
        <a href="category_header_page.php?category_name=Paperbags"><h2>PAPERBAGS</h2></a>
        <a href="category_header_page.php?category_name=Supplies"><h2>SUPPLIES</h2></a>
    </nav>

    <section id="body">
        <h1>Viewing all products</h1>

        <div id="sort" class="center">
            <form action="index.php" method="get">
                <label for="sort-select">Sort:</label>
                <select id="sort-select" name="sort" onchange="this.form.submit()">
                    <option value="alphabetical" <?php if ($sortingOption === 'alphabetical') echo 'selected'; ?>>Alphabetically</option>
                    <option value="newly_added" <?php if ($sortingOption === 'newly_added') echo 'selected'; ?>>Newly Added</option>
                    <option value="price_low_high" <?php if ($sortingOption === 'price_low_high') echo 'selected'; ?>>Price: Low to High</option>
                    <option value="price_high_low" <?php if ($sortingOption === 'price_high_low') echo 'selected'; ?>>Price: High to Low</option>
                </select>
            </form>
        </div>

        <div id="products">
            <ul>
            <?php foreach ($statement as $product): ?>
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
    </section>
</div>
    
</body>
</html>