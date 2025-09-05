<?php
if (!isset($_SESSION['user_id'])) { header("Location: index.php?page=login"); exit(); }
$user_id = $_SESSION['user_id'];

$uploads_stmt = $conn->prepare("SELECT COUNT(*) as count FROM wallpapers WHERE user_id = ?");
$uploads_stmt->bind_param("i", $user_id);
$uploads_stmt->execute();
$uploads_count = $uploads_stmt->get_result()->fetch_assoc()['count'];

$downloads_stmt = $conn->prepare("SELECT SUM(downloads) as sum FROM wallpapers WHERE user_id = ?");
$downloads_stmt->bind_param("i", $user_id);
$downloads_stmt->execute();
$downloads_sum = $downloads_stmt->get_result()->fetch_assoc()['sum'] ?? 0;

$views_stmt = $conn->prepare("SELECT SUM(views) as sum FROM wallpapers WHERE user_id = ?");
$views_stmt->bind_param("i", $user_id);
$views_stmt->execute();
$views_sum = $views_stmt->get_result()->fetch_assoc()['sum'] ?? 0;

$tab = $_GET['tab'] ?? 'uploads';
?>
<div class="container profile-page">
    <div class="profile-header">
        <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?> <?php echo display_badge($_SESSION['user_badge'], true); ?></span>
    </div>
    <div class="profile-stats">
        <div class="stat-item"><span class="stat-number"><?php echo $uploads_count; ?></span><span class="stat-label">Uploads</span></div>
        <div class="stat-item"><span class="stat-number"><?php echo number_format($downloads_sum); ?></span><span class="stat-label">Downloads</span></div>
        <div class="stat-item"><span class="stat-number"><?php echo number_format($views_sum); ?></span><span class="stat-label">Views</span></div>
    </div>
    <div class="profile-tabs">
        <a href="index.php?page=profile&tab=uploads" class="<?php echo $tab === 'uploads' ? 'active' : ''; ?>">My Uploads</a>
    </div>
    <div class="wallpaper-grid">
        <?php
        $my_uploads_stmt = $conn->prepare("SELECT * FROM wallpapers WHERE user_id = ? ORDER BY uploaded_at DESC");
        $my_uploads_stmt->bind_param("i", $user_id);
        $my_uploads_stmt->execute();
        $my_uploads_result = $my_uploads_stmt->get_result();
        if ($my_uploads_result->num_rows > 0) {
            while($row = $my_uploads_result->fetch_assoc()) {
                 echo '<div class="wallpaper-card"><a href="index.php?page=wallpaper&id='.$row['id'].'"><img src="'.htmlspecialchars($row['file_path']).'" alt="'.htmlspecialchars($row['title']).'"><div class="wallpaper-card-info"><span>'.htmlspecialchars($row['title']).'</span></div></a></div>';
            }
        } else {
            echo '<p class="no-results" style="grid-column: 1 / -1;">You haven\'t uploaded any wallpapers yet.</p>';
        }
        ?>
    </div>
</div>
