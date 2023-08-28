<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 27, 2023
    Description: Sorting list of existing products in the database.

****************/

session_start();
require('connect.php');

// Admin/user must be logged in to view this page
if (!isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    $_SESSION['login_error'] = "Please log in to access this page.";
    header("Location: login.php");
    exit();
}

$orderBy = ''; // Needs to be initialized
$sortingOption = isset($_GET['sort']) ? $_GET['sort'] : ''; // Retrieve selected sorting option

// Determine the sorting based on the selected option
switch ($sortingOption) {
    case 'product_name_az':
        $orderBy = 'product_name ASC';
        break;
    case 'recently_updated':
        $orderBy = 'timestamp DESC';
        break;
    case 'price_low_high':
        $orderBy = 'price ASC';
        break;
    case 'price_high_low':
        $orderBy = 'price DESC';
        break;
    // Default case: Sort by product name A-Z
    default:
        $orderBy = 'product_name ASC';
}

$query = "SELECT * FROM packagingsupplies ORDER BY $orderBy";
$statement = $db->prepare($query);
$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);
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

    <header >
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

    <nav id="productnav" >
        <a href="index.php"><h2>HOME</h2></a>
        <a href="category_header_page.php?category_name=Boxes"><h2>BOXES</h2></a>
        <a href="category_header_page.php?category_name=Paperbags"><h2>PAPERBAGS</h2></a>
        <a href="category_header_page.php?category_name=Supplies"><h2>SUPPLIES</h2></a>
    </nav>

    <h1>View and Sort Existing Pages</h1>

    <section class="center">
        <li>
            <table class="table-border">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Timestamp</th>
                        <th>Category ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['product_name']; ?></td>
                            <td>$<?= $product['price']; ?></td>
                            <td><?= date('F j, Y g:i A', strtotime($product['timestamp'])); ?></td>
                            <td><?= $product['category_id']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </li>
    </section>

    <section class="center">
        <form action="sorted_list.php" method="get">
            <label for="sort-select">Sort:</label>
            <select id="sort-select" class="center-select" name="sort">
                <option value="product_name_az" <?php if ($sortingOption === 'product_name_az') echo 'selected'; ?>>Product name A-Z</option>
                <option value="recently_updated" <?php if ($sortingOption === 'recently_updated') echo 'selected'; ?>>Most Recently Updated</option>
                <option value="price_low_high" <?php if ($sortingOption === 'price_low_high') echo 'selected'; ?>>Price Low to High</option>
                <option value="price_high_low" <?php if ($sortingOption === 'price_high_low') echo 'selected'; ?>>Price High to Low</option>
            </select>
            <input type="submit" value="Sort">
        </form>
    </section>
    
    
<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>