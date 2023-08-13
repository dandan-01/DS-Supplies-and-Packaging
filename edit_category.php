<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 10, 2023
    Description: Edit/Delete category page.

****************/

require('connect.php');

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    // Fetch chosen category from database:
    $categoryQuery = "SELECT * FROM categories WHERE category_id = :category_id";
    $categoryStatement = $db->prepare($categoryQuery);
    $categoryStatement->bindValue(':category_id', $category_id);
    $categoryStatement->execute();
    $category = $categoryStatement->fetch(PDO::FETCH_ASSOC);

    // Edit category name
    if ($_POST && isset($_POST['update'])) {
        // Sanitize and filter user input
        $new_category_name = filter_input(INPUT_POST, 'new_category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        // Update the category name in the database
        $updateQuery = "UPDATE categories SET category_name = :new_category_name WHERE category_id = :category_id";
        $updateStatement = $db->prepare($updateQuery);
        $updateStatement->bindValue(':new_category_name', $new_category_name);
        $updateStatement->bindValue(':category_id', $category_id);
        
        if ($updateStatement->execute()) {
            header("Location: categories.php");
            exit;
        }
    } elseif (isset($_POST['delete_category'])) {
        // Delete category
        $deleteQuery = "DELETE FROM categories WHERE category_id = :category_id";
        $deleteStatement = $db->prepare($deleteQuery);
        $deleteStatement->bindValue(':category_id', $category_id);
        
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

    <h1>Edit "<?= $category['category_name']; ?>"</h1>

    <form method="POST" id="edit-category">
        <ul>
            <li>
                <label for="new_category_name">New Category Name:</label>
                <input type="text" id="new_category_name" name="new_category_name" value="<?= $category['category_name']; ?>" required>
            </li>

            <li>
                <input type="submit" value="Update Name" onclick="return confirm('Are you sure you want to CHANGE the name of this category?')">
                <input type="submit" name="delete_category" value="Delete" onclick="return confirm('Are you sure you want to DELETE this category?')">
            </li>
        </ul>
    </form>
</body>
</html>