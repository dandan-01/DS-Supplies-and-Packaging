<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 10, 2023
    Description: Edit/Delete category page.

****************/

session_start();
require('connect.php');

// Admin/user must be logged in to view this page
if (!isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    $_SESSION['login_error'] = "Please log in to access this page.";
    header("Location: login.php");
    exit();
}

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['category_id'])) {
    $edit_category_id = $_GET['category_id'];
    
    // Fetch chosen category from database:
    $categoryQuery = "SELECT * FROM categories WHERE category_id = :category_id";
    $categoryStatement = $db->prepare($categoryQuery);
    $categoryStatement->bindValue(':category_id', $edit_category_id);
    $categoryStatement->execute();
    $edit_category = $categoryStatement->fetch(PDO::FETCH_ASSOC);

    // Edit category name
    if ($_POST && isset($_POST['update-category'])) {
        // Sanitize and filter user input
        $new_category_name = filter_input(INPUT_POST, 'new_category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        // Update the category name in the database
        $updateQuery = "UPDATE categories SET category_name = :new_category_name WHERE category_id = :category_id";
        $updateStatement = $db->prepare($updateQuery);
        $updateStatement->bindValue(':new_category_name', $new_category_name);
        $updateStatement->bindValue(':category_id', $edit_category_id); 
        
        if ($updateStatement->execute()) {
            header("Location: categories.php");
            exit;
        }
    } elseif (isset($_POST['delete_category'])) {
        // Delete category
        $deleteQuery = "DELETE FROM categories WHERE category_id = :category_id";
        $deleteStatement = $db->prepare($deleteQuery);
        $deleteStatement->bindValue(':category_id', $edit_category_id);
        
        if ($deleteStatement->execute()) {
            header("Location: categories.php"); 
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
    <title>Boxed N Loaded | Edit <?= $category['category_name']; ?></title>
</head>
<body>
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

    <h1>Edit "<?= $edit_category['category_name']; ?>"</h1>

    <form method="POST" id="edit-category">
        <ul>
            <li>
                <label for="new_category_name">New Category Name:</label>
                <input type="text" id="new_category_name" name="new_category_name" value="<?= $edit_category['category_name']; ?>" required>
            </li>

            <li>
                <input type="submit" name="update-category" value="Update" onclick="return confirm('Are you sure you want to UPDATE the category name?')">
                <input type="submit" name="delete_category" value="Delete" onclick="return confirm('Are you sure you want to DELETE this category?')">
            </li>
        </ul>
    </form>

<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>