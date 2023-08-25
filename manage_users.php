<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 24, 2023
    Description: Manage users CRUD page.

****************/

session_start();
require('connect.php');

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

$error = "";

// ONLY ADMIN access
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    // Fetch user data
    $query = "SELECT user_id, email FROM users";
    $statement = $db->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Process the creation of a new user 
    if (isset($_POST['create_user'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if the email is already in use
        $existingQuery = "SELECT * FROM users WHERE email = :email";
        $existingStatement = $db->prepare($existingQuery);
        $existingStatement->bindValue(':email', $email);
        $existingStatement->execute();
        $existingUser = $existingStatement->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $error = "Email already in use.";
        } elseif (!$email) {
            $error = "Invalid email format.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO users (email, password) VALUES (:email, :password)";
            $insertStatement = $db->prepare($insertQuery);
            $insertStatement->bindValue(':email', $email);
            $insertStatement->bindValue(':password', $hashed_password);

            if ($insertStatement->execute()) {
                header('Location: manage_users.php'); 
                exit;
            } else {
                $create_user_error = "Failed to create user.";
            }
        }
    }
} else {
    $errorMessage = "Admin access needed, please log in as admin.";
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

    <h1>Manage Users</h1>

    <section class="center" id="current_users">
        <li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <section id="create-user">
                    <form class="center" method="POST">
                        <ul>
                            <li>
                                <h1>Create User</h1>
                            </li>

                            <?php if (!empty($error)): ?>
                                <h2 class="red_text center"><?= $error ?></h2>
                            <?php endif; ?>

                            <li>
                                <label for="email">Email:</label><br>
                                <input type="email" id="email" name="email" required size="60">
                                <p class="register_text_grey">Please include an '@' in the email address followed by a domain (ie gmail.com).</p>
                            </li>

                            <li>
                                <label for="password">Password:</label><br>
                                <input type="password" id="password" name="password" required size="60"><br>
                            </li>

                            <li>
                                <label for="confirm_password">Confirm Password:</label><br>
                                <input type="password" id="confirm_password" name="confirm_password" required size="60">
                                <p class="register_text_grey">Please ensure that both passwords match.</p>
                            </li>

                            <li>
                                <input id="submitbtn" type="submit" name="create_user" value="Create User">
                            </li>
                        </ul>
                    </form>
                </section>

                <h1>User Management</h1>

                <table class="center">
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>

                    <!-- Obtain users data from db -->
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['user_id']; ?></td>
                        <td><?= $user['email']; ?></td>
                        <td><a href="edit_user.php?user_id=<?= $user['user_id']; ?>">Edit/Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <!-- Display error message if not an admin -->
            <?php if (isset($errorMessage)): ?>
                <p><?= $errorMessage; ?></p>
                <a href="login.php"><button class="blue-btn">Go back to Login Page</button></a>
            <?php endif; ?>
        </li>
        
    </section>
    

<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>