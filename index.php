<?php
// index.php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle CRUD operations for posts
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $userId = $_SESSION['user_id'];
        
        // Create a new post
        if ($_POST['action'] === 'create') {
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);

            if (!empty($title)) {
                $query = "INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':title'   => $title,
                    ':content' => $content
                ]);
            }
        }

        // Update a post
        if ($_POST['action'] === 'update') {
            $postId = $_POST['post_id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);

            // Verify the post belongs to the current user
            $query = "SELECT * FROM posts WHERE id = :id AND user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $postId, ':user_id' => $userId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post) {
                $query = "UPDATE posts SET title = :title, content = :content WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':title'   => $title,
                    ':content' => $content,
                    ':id'      => $postId
                ]);
            }
        }

        // Delete a post
        if ($_POST['action'] === 'delete') {
            $postId = $_POST['post_id'];

            // Verify the post belongs to the current user
            $query = "SELECT * FROM posts WHERE id = :id AND user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $postId, ':user_id' => $userId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post) {
                $query = "DELETE FROM posts WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->execute([':id' => $postId]);
            }
        }
    }
}

// Fetch posts for the logged-in user
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $userId]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome &amp; Posts</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
      <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
      <a href="logout.php" class="logout-link">Logout</a>
      
      <h3>Create a New Post</h3>
      <form method="POST" action="index.php" id="create-post-form" class="post-form create-post-form">
          <input type="hidden" name="action" value="create">
          <label for="title">Title:</label>
          <input type="text" id="title" name="title" required>
          
          <label for="content">Content:</label>
          <textarea id="content" name="content"></textarea>
          
          <button type="submit">Create Post</button>
      </form>

      <h3>Your Posts</h3>
      <?php if (count($posts) > 0): ?>
          <?php foreach ($posts as $post): ?>
              <div class="post">
                  <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                  <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                  <small>Created at: <?php echo $post['created_at']; ?></small>
                  
                  <!-- Edit Form -->
                  <form method="POST" action="index.php" class="post-form edit-post-form">
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                      <label for="edit-title-<?php echo $post['id']; ?>">Title:</label>
                      <input type="text" id="edit-title-<?php echo $post['id']; ?>" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                      <label for="edit-content-<?php echo $post['id']; ?>">Content:</label>
                      <textarea id="edit-content-<?php echo $post['id']; ?>" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
                      <button type="submit">Update</button>
                  </form>
                  
                  <!-- Delete Form -->
                  <form method="POST" action="index.php" class="post-form delete-post-form" onsubmit="return confirm('Are you sure you want to delete this post?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                      <button type="submit">Delete</button>
                  </form>
              </div>
          <?php endforeach; ?>
      <?php else: ?>
          <p>No posts found.</p>
      <?php endif; ?>
  </div>
  <script src="script.js"></script>
</body>
</html>
