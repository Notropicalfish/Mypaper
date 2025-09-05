<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: index.php"); exit(); }
$wallpaper_id = intval($_GET['id']);

$conn->query("UPDATE wallpapers SET views = views + 1 WHERE id = $wallpaper_id");

$stmt = $conn->prepare("SELECT w.*, u.username, u.badge as user_badge FROM wallpapers w JOIN users u ON w.user_id = u.id WHERE w.id = ? AND w.status = 'approved'");
$stmt->bind_param("i", $wallpaper_id);
$stmt->execute();
$result = $stmt->get_result();
if ($wallpaper = $result->fetch_assoc()) {
    // This is a bit of a trick to update the page title from within a view.
    echo "<script>document.title = '".htmlspecialchars($wallpaper['title'])." - MyPaper';</script>";
    echo "<script>document.querySelector('.app-header h1').textContent = '".htmlspecialchars($wallpaper['title'])."';</script>";
    ?>
     <div class="wallpaper-details-container">
        <img src="<?php echo htmlspecialchars($wallpaper['file_path']); ?>" alt="<?php echo htmlspecialchars($wallpaper['title']); ?>" class="wallpaper-full-image">
        <div class="wallpaper-info-panel">
            <div class="uploader-info">
                <span class="username"><?php echo htmlspecialchars($wallpaper['username']); ?></span>
                <?php echo display_badge($wallpaper['user_badge']); ?>
            </div>
            <div class="wallpaper-tags">
                <?php foreach(explode(',', $wallpaper['tags']) as $tag): ?>
                    <span><?php echo htmlspecialchars(trim($tag)); ?></span>
                <?php endforeach; ?>
            </div>
            <a href="<?php echo htmlspecialchars($wallpaper['file_path']); ?>" download class="btn btn-primary" id="downloadBtn" data-id="<?php echo $wallpaper['id']; ?>">
                <i class="fas fa-download"></i> Download
            </a>
        </div>
    </div>
    <?php
} else {
    echo "<div class='container'><p class='no-results'>Wallpaper not found or pending approval.</p></div>";
}
?>
