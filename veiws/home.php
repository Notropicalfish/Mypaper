<?php
$sql = "SELECT w.*, u.username, u.badge as user_badge FROM wallpapers w JOIN users u ON w.user_id = u.id WHERE w.status = 'approved' ORDER BY w.uploaded_at DESC";
$result = $conn->query($sql);
?>
<div class="container">
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" onkeyup="filterWallpapers()" placeholder="Search People, Moods, Fashion">
    </div>
    <h2 class="section-title">My Favourites</h2>
    <div class="favourites-grid">
        <div class="favourite-card tall" style="background-image: url('https://placehold.co/400x600/a3c1c4/ffffff?text=MyPaper');"></div>
        <div class="favourite-card" style="background-image: url('https://placehold.co/400x400/e8a798/ffffff?text=MyPaper');"></div>
        <div class="favourite-card" style="background-image: url('https://placehold.co/400x400/d1d1d1/ffffff?text=MyPaper');"></div>

    </div>
    <div class="tag-filters">
        <a href="#" class="active tag-filter" data-tag="all">Popular</a>
        <a href="#" class="tag-filter" data-tag="abstract">Abstract</a>
        <a href="#" class="tag-filter" data-tag="architecture">Architecture</a>
        <a href="#" class="tag-filter" data-tag="art">Art</a>
        <a href="#" class="tag-filter" data-tag="nature">Nature</a>
    </div>
    <div class="wallpaper-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="wallpaper-card" data-tags="<?php echo htmlspecialchars(strtolower($row['tags'] . ',' . $row['title'] . ',' . $row['username'])); ?>">
                    <a href="index.php?page=wallpaper&id=<?php echo $row['id']; ?>">
                        <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="wallpaper-card-info">
                            <span><?php echo htmlspecialchars($row['title']); ?> <?php echo display_badge($row['user_badge']); ?></span>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-results">No wallpapers have been uploaded yet.</p>
        <?php endif; ?>
    </div>
</div>
