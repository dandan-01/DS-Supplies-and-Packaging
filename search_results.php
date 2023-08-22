<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 13, 2023
    Description: Show search results.

****************/

require('connect.php');

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

// Get search query and category filter
$searchQuery = $_GET['search_query'];
$categoryFilter = $_GET['category'];

// Set N number of results per page
$resultsPerPage = 100;

// Calculate current page
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate OFFSET for pagination
$offset = ($page - 1) * $resultsPerPage;

//Use LEFT JOIN to include products with NO images included (ie. box test)
$query = "SELECT packagingsupplies.*, images.filename AS image_filename 
            FROM packagingsupplies 
            LEFT JOIN images ON packagingsupplies.image_id = images.image_id 
            WHERE product_name LIKE :keyword";

// Add category filter IF selected
if (!empty($categoryFilter)) {
    $query .= " AND category_id = :category_id";
}

// Modify the query to include LIMIT and OFFSET
$query .= " LIMIT :limit OFFSET :offset";

// Prep and bind keyword, limit, offset
$statement = $db->prepare($query);
$keyword = '%' . $searchQuery . '%';
$statement->bindValue(':keyword', $keyword);
$statement->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
$statement->bindValue(':offset', $offset, PDO::PARAM_INT);

// Bind category IF selected
if (!empty($categoryFilter)) {
    $statement->bindValue(':category_id', $categoryFilter);
}

$statement->execute();
$searchResults = $statement->fetchAll(PDO::FETCH_ASSOC);

// Calculate total number of pages
$totalResults = count($searchResults);
$totalPages = ceil($totalResults / $resultsPerPage);

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

    <div id="products">
        <ul>
            <?php foreach ($searchResults as $product): ?>
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

    <!-- Display pagination links -->
    <?php if ($totalPages > 1): ?>
        <p>test</p>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="current-page">Page <?= $i ?></span>
                <?php else: ?>
                    <a href="?search_query=<?= urlencode($searchQuery) ?>&category=<?= $categoryFilter ?>&page=<?= $i ?>">Page <?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    
</body>
</html>