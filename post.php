<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Blogpost page.

****************/

require('connect.php');

// Function to sanitize and filter input values
function sanitizeInput($input)
{
    return filter_var(trim($input), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Sanitize and filter the 'id' parameter as an integer
    $postId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Build the SQL query to retrieve the blog post with the specified ID
    $query = "SELECT * FROM blogpost WHERE id = :id";
    $statement = $db->prepare($query);

    // Bind the sanitized ID value to the query parameter
    $statement->bindValue(':id', $postId);

    // Execute the query
    $statement->execute();

    // Fetch the blog post as an associative array
    $post = $statement->fetch(PDO::FETCH_ASSOC);

    // Check if a post was found
    if ($post) {
        $title = $post['title'];
        $timestamp = date("F d, Y, g:i a", strtotime($post['timestamp'])); // Format the timestamp
        $content = $post['content'];
    } else {
        // If the post is not found, redirect to index.php
        header('Location: index.php');
        exit;
    }
} else {
    // If 'id' parameter is missing, redirect to index.php
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="main.css">
    <title>DS Supplies and Packaging - <?= $post['title'] ?></title>
</head>
<body>
    <div id="header">
    <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>DS Supplies and Packaging</h1>
            </a>
        </div>

        <div id="nav">
            <a href="index.php">Home</a>
            <a href="new_post.php">New Post</a>
        </div>
    </div>

    <h1><?= $title; ?></h1>
    <p><?= $timestamp; ?></p>
    <p class="blog-content"><?= $content; ?></p>
    <a href="edit.php?id=<?= $postId; ?>">Edit Post</a>
</body>
</html>