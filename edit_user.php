<?php
session_start();
require('connect.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login page if not logged in as admin
    exit;
}

// Process form submission for editing user
if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $new_email = $_POST['new_email'];
    $new_password = $_POST['new_password'];

    // Update user email and/or password
    $updateQuery = "UPDATE users SET email = :email, password = :password WHERE user_id = :user_id";
    $updateStatement = $db->prepare($updateQuery);
    $updateStatement->bindValue(':email', $new_email);
    $updateStatement->bindValue(':password', password_hash($new_password, PASSWORD_DEFAULT));
    $updateStatement->bindValue(':user_id', $user_id);

    if ($updateStatement->execute()) {
        $edit_success_message = "User information updated successfully.";
    } else {
        $edit_error_message = "Failed to update user information.";
    }
}

// Process form submission for deleting user
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Delete user
    $deleteQuery = "DELETE FROM users WHERE user_id = :user_id";
    $deleteStatement = $db->prepare($deleteQuery);
    $deleteStatement->bindValue(':user_id', $user_id);

    if ($deleteStatement->execute()) {
        $delete_success_message = "User deleted successfully.";
    } else {
        $delete_error_message = "Failed to delete user.";
    }
}

// Fetch user data
$query = "SELECT user_id, email FROM users";
$statement = $db->prepare($query);
$statement->execute();
$users = $statement->fetchAll(PDO::FETCH_ASSOC);
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
        <h3>User/Admin access needed -></h3>
        <a href="add_new_item.php">Add new item</a>
        <a href="categories.php">Create/Update Category</a>
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

    <h1>Edit Users</h1>

    <table class="center">
        <tr>
            <th>User ID</th>
            <th>Email</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['user_id']; ?></td>
                <td><?= $user['email']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                        <input type="text" name="new_email" placeholder="New Email" required>
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <button type="submit" name="edit_user">Edit</button>
                    </form>
                </td>
                <td>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                        <button type="submit" name="delete_user">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>