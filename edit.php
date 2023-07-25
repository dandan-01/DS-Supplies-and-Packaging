<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Edit post page

****************/

require('connect.php');
require('authenticate.php');

// Fetch the blog post details 
$post_id = $_GET['id'];

$query = "SELECT * FROM blogpost WHERE id = :post_id";
$statement = $db->prepare($query);
$statement->bindValue(':post_id', $post_id);
$statement->execute();

$post = $statement->fetch(PDO::FETCH_ASSOC);

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Update button clicked
        if (!empty($_POST['title']) && !empty($_POST['content'])) {
            // Sanitize and filter user input
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Build SQL query and bind to the above sanitized values.
            $query = "UPDATE blogpost SET title = :title, content = :content WHERE id = :post_id";

            $statement = $db->prepare($query);

            // Bind values to the parameters
            $statement->bindValue(':title', $title);
            $statement->bindValue(':content', $content);
            $statement->bindValue(':post_id', $post_id);

            // Execute the UPDATE.
            if ($statement->execute()) {
                echo "Post updated successfully.";
                header('Location: index.php');
                exit;
            } else {
                echo "Failed to update the post.";
            }
        } else {
            echo "Please provide a title and content for the post.";
        }
    } elseif (isset($_POST['delete'])) {
        // Delete button clicked
        $query = "DELETE FROM blogpost WHERE id = :post_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':post_id', $post_id);

        if ($statement->execute()) {
            echo "Post deleted successfully.";
            header('Location: index.php');
            exit;
        } else {
            echo "Failed to delete the post.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="main.css">
    <title>DS Supplies and Packaging - Edit Blog Post</title>
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

    <h1>Edit Blog Post</h1>

    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?= $post['title'] ?? ''; ?>" required><br>

        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="4" cols="50" required><?= $post['content'] ?? ''; ?></textarea><br>

        <input type="submit" name="update" value="Update Blog">
        <input type="submit" name="delete" value="Delete Blog" onclick="return confirm('Are you sure you want to delete this post?')">
    </form>
</body>
</html>