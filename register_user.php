<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 21, 2023
    Description: Register user page.

****************/

require('connect.php');

// Fetch categories to populate AJAX search form drop-down list
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

$error = "";
$successMessage = "";

if ($_POST && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the email is already in use
    $query = "SELECT * FROM users WHERE email = :email";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();
    $existingUser = $statement->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $error = "Email already in use.";
    } elseif (!$email) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Password hashing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $hashed_password);

        if ($statement->execute()) {
            $successMessage = "Registration successful!";
        } else {
            $error = "Registration failed.";
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

    <section class="center">
        <ul>
            <li>
                <h2 class="center">User Registration</h2>
            </li>

            <li>
                <?php if (!empty($error)): ?>
                    <h2 class="red_text"><?= $error ?></h2>
                <?php endif; ?>

                <form method="post" action="register_user.php" >
                    <label for="email">Email:</label>
                    <input type="email" name="email" required>
                    <p class="register_text_grey">Please include an '@' in the email address followed by a domain (ie gmail.com).</p><br>

                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br><br>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                    <p class="register_text_grey">Please ensure that both passwords match.</p><br><br>

                    <input id="submitbtn" type="submit" value="Register">

                    <?php if (isset($successMessage)): ?>
                            <h2 class="red_text"><?= $successMessage ?></h2>
                            <a href="index.php">Go back to Home Page</a>
                    <?php endif; ?>
                </form>
            </li>


        </ul>
    </section>
    
<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>