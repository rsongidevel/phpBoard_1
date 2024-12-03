<?php
ob_end_clean();

// CORS 헤더 추가
header("Access-Control-Allow-Origin: *");  // 모든 도메인 허용 (필요에 따라 수정 가능)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");  // 추가 헤더 설정

// OPTIONS 요청에 대한 응답 처리
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // OPTIONS 요청에 대해서는 응답하고 종료
    http_response_code(200); // 200 응답 코드
    exit;
}

// 데이터베이스 연결
$mysqli = new mysqli('localhost', 'root', '', 'community_board');

// 연결 오류 처리
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// GET 파라미터에서 게시글 ID 가져오기
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 게시글 ID가 유효한지 확인
if ($post_id <= 0) {
    echo json_encode(['error' => 'Invalid post ID']);
    $mysqli->close();
    exit;
}

// 게시글 조회 쿼리
$query = $mysqli->prepare("SELECT * FROM posts WHERE id = ?");
$query->bind_param("i", $post_id); // ID 바인딩
$query->execute();

// 결과 가져오기
$result = $query->get_result();
$post = $result->fetch_assoc();

// 게시글이 존재하는지 확인
if ($post) {
    echo json_encode(['post' => $post]);
} else {
    echo json_encode(['error' => 'Post not found']);
}

// 연결 종료
$query->close();
$mysqli->close();
?>