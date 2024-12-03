<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if data is not empty
    if ($data) {
        // Extract the id from the POST data
        $id = isset($data['id']) ? $data['id'] : null;

        // Validate id
        if (empty($id)) {
            echo json_encode(["success" => false, "message" => "Post ID is required for deletion"]);
            exit;
        }

        // Database connection
        $mysqli = new mysqli('localhost', 'root', '', 'community_board');
        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }

        // Prepare and execute the DELETE SQL query
        $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);  // "i" means integer (id)

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Post deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error deleting post: " . $stmt->error]);
        }

        $stmt->close();
        $mysqli->close();
    } else {
        // If no data received
        echo json_encode(["success" => false, "message" => "No data received"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
