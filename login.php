<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 21, 2023
    Description: Log in page.

****************/

require('connect.php');
require('authenticate.php');

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);
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
            <a href="login.php">Log In</a>
            <a href="new_post.php">Cart</a>
        </nav>
    </header>

    <nav id="productnav">
        <a href="index.php"><h2>HOME</h2></a>
        <a href="category_header_page.php?category_name=Boxes"><h2>BOXES</h2></a>
        <a href="category_header_page.php?category_name=Paperbags"><h2>PAPERBAGS</h2></a>
        <a href="category_header_page.php?category_name=Supplies"><h2>SUPPLIES</h2></a>
    </nav>

    <section class="center">
        <ul>
            <li>
                <h2 class="center">Login</h2>
            </li>

            <li>
                <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] === "admin"): ?>
                        <p>Currently logged in as admin</p>
                        <p>You now have CRUD privileges. Try clicking on the links at the top or editing a product.</p>
                        <form method="post" action="logout.php">
                            <input class="center" type="submit" value="Logout">
                        </form>
                    <?php elseif ($_SESSION['role'] === "user"): ?>
                        <p>Currently logged in as <?php echo $_SESSION['user_email']; ?></p>
                        <p>You now have CRUD privileges. Try clicking on the links at the top or editing a product</p>
                        <form method="post" action="logout.php">
                            <input class="center" type="submit" value="Logout">
                        </form>
                    <?php endif; ?>
                <?php elseif (isset($_SESSION['login_error'])): ?>
                    <h3 class="red_text"><?php echo $_SESSION['login_error']; ?></h3>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <?php if (!isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')): ?>
                    <form method="post" action="login.php">
                        <label for="username">Username:</label>
                        <input type="text" name="username" required><br><br>

                        <label for="password">Password:</label>
                        <input type="password" name="password" required><br><br>

                        <input type="submit" value="Login">
                    </form>
                <?php endif; ?>
            </li>
        </ul>
    </section>
    
    
</body>
</html>