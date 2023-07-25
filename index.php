<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Simple Content Management System for a DS Supplies and Packaging.

****************/

require('connect.php');

// SQL is written as a String.
$query = "  SELECT * 
            FROM blogpost 
            ORDER BY id DESC";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 

// Check if there are any rows returned
if ($statement->rowCount() === 0) {
    $error = "<p>No blog posts found.</p>";
}

function shorten200($content, $maxLength = 200) {
    if (strlen($content) > $maxLength) {
        $excerpt = substr($content, 0, $maxLength) . '...';
        return $excerpt;
    } else {
        return $content;
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
    <title>DS Supplies and Packaging</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
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

    <h2>Showing Recent Blog Posts</h2>

    <div id="blogs">
    <ul>
        <?php foreach ($statement as $blogpost): ?>
        <li>
            <div class="blog-header">
                <h2><a href="post.php?id=<?= $blogpost['id']; ?>"><?= $blogpost['title']; ?></a></h2>
                <a href="edit.php?id=<?= $blogpost['id']; ?>">edit</a>
            </div>

            <div class="blog-timestamp">
                <?= date("F d, Y, g:i a", strtotime($blogpost['timestamp'])); ?>
            </div>

            <div class="blog-content">
                <?= shorten200($blogpost['content']); ?>
                <?php if (strlen($blogpost['content']) > 200): ?>
                        <a href="post.php?id=<?= $blogpost['id']; ?>">Read Full Post</a>
                <?php endif; ?>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
    
</body>
</html>