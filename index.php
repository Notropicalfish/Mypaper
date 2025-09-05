<?php
// MyPaper - Main Router v3.1 by Peelish Studios (with Absolute Paths)

// --- 0. ERROR REPORTING & BOOTSTRAP ---
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Using __DIR__ makes the path absolute and more reliable.
require_once __DIR__ . '/db.php'; 

// --- 2. ACTION HANDLER ---
$action = $_GET['action'] ?? '';
$page = $_GET['page'] ?? 'home';

// Handle all POST submissions before rendering any HTML
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        if ($action === 'delete_wallpaper' && isset($_POST['wallpaper_id'])) {
            $wallpaper_id = intval($_POST['wallpaper_id']);
            $stmt = $conn->prepare("SELECT file_path FROM wallpapers WHERE id = ?");
            $stmt->bind_param("i", $wallpaper_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                // Use absolute path for file operations too
                if (file_exists(__DIR__ . '/' . $row['file_path'])) unlink(__DIR__ . '/' . $row['file_path']);
            }
            $stmt->close();
            $delete_stmt = $conn->prepare("DELETE FROM wallpapers WHERE id = ?");
            $delete_stmt->bind_param("i", $wallpaper_id);
            $delete_stmt->execute();
            $delete_stmt->close();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        if ($action === 'approve_wallpaper' && isset($_POST['wallpaper_id'])) {
            $wallpaper_id = intval($_POST['wallpaper_id']);
            $stmt = $conn->prepare("UPDATE wallpapers SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $wallpaper_id);
            $stmt->execute();
            $stmt->close();
            header("Location: index.php?page=admin&tab=approvals");
            exit();
        }
        if ($action === 'reject_wallpaper' && isset($_POST['wallpaper_id'])) {
            $wallpaper_id = intval($_POST['wallpaper_id']);
            $stmt = $conn->prepare("SELECT file_path FROM wallpapers WHERE id = ?");
            $stmt->bind_param("i", $wallpaper_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (file_exists(__DIR__ . '/' . $row['file_path'])) unlink(__DIR__ . '/' . $row['file_path']);
            }
            $stmt->close();
            $delete_stmt = $conn->prepare("DELETE FROM wallpapers WHERE id = ?");
            $delete_stmt->bind_param("i", $wallpaper_id);
            $delete_stmt->execute();
            $delete_stmt->close();
            header("Location: index.php?page=admin&tab=approvals");
            exit();
        }
        if ($action === 'update_badge' && isset($_POST['user_id'], $_POST['new_badge'])) {
             $user_id_to_update = intval($_POST['user_id']);
             $new_badge = $_POST['new_badge'];
             $allowed_badges = ['user', 'verified', 'professional', 'admin'];
             if ($user_id_to_update !== $_SESSION['user_id'] && in_array($new_badge, $allowed_badges)) {
                 $stmt = $conn->prepare("UPDATE users SET badge = ? WHERE id = ?");
                 $stmt->bind_param("si", $new_badge, $user_id_to_update);
                 $stmt->execute();
                 $stmt->close();
                 header("Location: index.php?page=admin&tab=users&badge_updated=true");
                 exit();
             }
        }
    }
}


if ($action === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php");
    exit();
}

if ($action === 'update_download_count') {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (isset($data['id']) && is_numeric($data['id'])) {
            $wallpaper_id = intval($data['id']);
            $stmt = $conn->prepare("UPDATE wallpapers SET downloads = downloads + 1 WHERE id = ?");
            $stmt->bind_param("i", $wallpaper_id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => true]);
        }
    }
    exit();
}

// --- 3. RENDER PAGE ---

$pageTitle = ucfirst($page);
if ($page === 'home') $pageTitle = "Wallpapers";

// Use absolute path to include the header partial
require_once __DIR__ . '/partials/header.php';

// Use absolute path to build the view path
$view_path = __DIR__ . '/views/' . $page . '.php';

if (file_exists($view_path)) {
    require_once $view_path;
} else {
    // Fallback to the homepage if the page doesn't exist
    require_once __DIR__ . '/views/home.php';
}

// Use absolute path to include the footer partial
require_once __DIR__ . '/partials/footer.php';
?>

