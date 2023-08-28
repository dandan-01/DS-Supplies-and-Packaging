<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 8, 2023
    Description: Create/view category page.

****************/

session_start(); // Don't use require('authenticate.php') else won't work

require('connect.php');

// Admin/user must be logged in to view this page
if (!isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    $_SESSION['login_error'] = "Please log in to access this page.";
    header("Location: login.php");
    exit();
}

// Handle form submission to add a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['category_name'])) {
        // Sanitize and filter user input
        $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Build SQL query and bind the sanitized values
        $query = "INSERT INTO categories (category_name) VALUES (:category_name)";
        $statement = $db->prepare($query);

        // Bind values to parameters
        $statement->bindValue(':category_name', $category_name);

        // Execute the INSERT
        if ($statement->execute()) {
            // Redirect to the same page to avoid form resubmission
            header('Location: categories.php');
            exit();
        } else {
            echo "Error adding category.";
        }
    }
}

// Fetch current categories from database
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

    <section id="create-category">
        <h1>Create Category</h1>

        <form class="center" method="POST" >
            <ul>
                <li>
                    <label for="category_name">Category name:</label><br>
                    <input type="text" id="category_name" name="category_name" required size="50"><br>
                </li>
            
                <li>
                    <input id="submitbtn" type="submit" value="Submit">
                </li>
        </form>
    </section>

    <section id="current-categories" class="padding-bottom">
        <ul>
            <li>
                <h1 class="center">Current Categories</h1>
                <p class="center red-text"><i>***Important! Please don't delete or update <u>Boxes, Paperbags, or Supplies</u> categories. <br>
                They are needed for the HOME - BOXES - PAPERBAGS - SUPPLIES header! ***</i></p>

                <p class="center">To check the update/delete feature please create a new category or use a test object down below, thanks!</p>
            </li>

            <li>
                <table class="center">
                    <tbody>
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Update</th>
                        </tr>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['category_id']; ?></td>
                                <td><?= $category['category_name']; ?></td>
                                <td><a href="edit_category.php?category_id=<?= $category['category_id']; ?>">Edit/Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="center"><i>Note: There will be gaps between IDs due to deletion testing</i></p>
            </li>
        </ul>
    </section>

<footer>
    <h5>Copyright &copy; 2023 Danilyn Sanchez. All rights reserved.</h5>
</footer>

</body>
</html>