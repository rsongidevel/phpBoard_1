<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$mysqli = new mysqli('localhost', 'root', '', 'community_board');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Pagination parameters
$postsPerPage = 10; // Number of posts per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Get current page from URL query (default to 1)
$offset = ($page - 1) * $postsPerPage; // Calculate the OFFSET for the SQL query

// Get total number of posts (for calculating total pages)
$totalPostsResult = $mysqli->query("SELECT COUNT(*) AS total FROM posts");
$totalPosts = $totalPostsResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $postsPerPage); // Calculate total number of pages

// Get posts for the current page
$sql = "SELECT * FROM posts LIMIT $postsPerPage OFFSET $offset";
$result = $mysqli->query($sql);

// Fetch the posts as an associative array
$posts = $result->fetch_all(MYSQLI_ASSOC);

// Send JSON response with posts, current page, and total pages
echo json_encode([
    'posts' => $posts,
    'currentPage' => $page,
    'totalPages' => $totalPages,
]);

// Close the database connection
$mysqli->close();
?>