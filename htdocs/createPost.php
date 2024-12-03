<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if data is not empty
    if ($data) {
        error_log(print_r($data, true));  // Log the data for debugging

        // Extract title, body, and id from the POST data
        $title = $data['title'];
        $body = $data['body'];
        $id = isset($data['id']) ? $data['id'] : null; // Check if id is provided

        // Database connection
        $mysqli = new mysqli('localhost', 'root', '', 'community_board');
        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }

        // Check if title or body is empty
        if (empty($title) || empty($body)) {
            echo json_encode(["message" => "Title and Body cannot be empty"]);
            exit;
        }

        if ($id) {
            // If id exists, perform UPDATE
            $stmt = $mysqli->prepare("UPDATE posts SET title = ?, body = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $body, $id);  // "ssi" means string, string, integer

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Post updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error updating post: " . $stmt->error]);
            }

        } else {
            // If no id, perform INSERT (create new post)
            $stmt = $mysqli->prepare("INSERT INTO posts (title, body) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $body);  // "ss" means two strings

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Post created successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error creating post: " . $stmt->error]);
            }
        }

        $stmt->close();
        $mysqli->close();
    } else {
        // If no data received
        error_log("No data received");
        echo json_encode(["success" => false, "message" => "No data received"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
