<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 25, 2023
    Description: Edit user page.

****************/

session_start();
require('connect.php');

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login page if not logged in as admin
    exit;
}

// Fetch user data
if (isset($_GET['user_id'])) {
    $edit_user_id = $_GET['user_id'];

    $userQuery = "SELECT * FROM users WHERE user_id = :user_id";
    $userStatement = $db->prepare($userQuery);
    $userStatement->bindValue(':user_id', $edit_user_id);
    $userStatement->execute();
    $edit_user = $userStatement->fetch(PDO::FETCH_ASSOC);

    // Update user
    if ($_POST && isset($_POST['update-user'])) {
        // Sanitize and filter user input
        $new_email = filter_input(INPUT_POST, 'new_email', FILTER_VALIDATE_EMAIL);

        // Update the user's email in the database
        $updateQuery = "UPDATE users SET email = :new_email WHERE user_id = :user_id";
        $updateStatement = $db->prepare($updateQuery);
        $updateStatement->bindValue(':new_email', $new_email);
        $updateStatement->bindValue(':user_id', $edit_user_id); 

        if ($updateStatement->execute()) {
            header("Location: manage_users.php");
            exit;
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $deleteQuery = "DELETE FROM users WHERE user_id = :user_id";
        $deleteStatement = $db->prepare($deleteQuery);
        $deleteStatement->bindValue(':user_id', $edit_user_id);

        if ($deleteStatement->execute()) {
            header("Location: manage_users.php");
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

    <h1>Edit "<?= $edit_user['email']; ?>"</h1>

    <form method="POST" id="edit-user" class="center">
        <ul>
            <li>
                <label for="new_email">Update Email:</label>
                <input type="email" id="new_email" name="new_email" value="<?= $edit_user['email']; ?>" required>
            </li>
            
            <li>
                <input type="submit" name="update-user" value="Update" onclick="return confirm('Are you sure you want to update this user?')">
                <input type="submit" name="delete_user" value="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
            </li>
        </ul>
    </form>

<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>