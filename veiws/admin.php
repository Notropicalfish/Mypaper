<?php
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { header("Location: index.php"); exit(); }
$admin_tab = $_GET['tab'] ?? 'approvals';
?>
<div class="container admin-panel">
    <div class="admin-tabs">
        <a href="index.php?page=admin&tab=approvals" class="<?php echo $admin_tab === 'approvals' ? 'active' : ''; ?>">Approvals</a>
        <a href="index.php?page=admin&tab=users" class="<?php echo $admin_tab === 'users' ? 'active' : ''; ?>">Users</a>
    </div>
    <?php if ($admin_tab === 'approvals'): ?>
        <?php
        $pending_stmt = $conn->prepare("SELECT w.*, u.username FROM wallpapers w JOIN users u ON w.user_id = u.id WHERE w.status = 'pending' ORDER BY w.uploaded_at ASC");
        $pending_stmt->execute();
        $pending_result = $pending_stmt->get_result();
        ?>
        <div class="section-container">
            <h2>Pending Approvals (<?php echo $pending_result->num_rows; ?>)</h2>
            <?php if ($pending_result->num_rows > 0): ?>
                <div class="admin-approval-grid">
                <?php while ($row = $pending_result->fetch_assoc()): ?>
                    <div class="admin-card">
                        <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="admin-card-info">
                            <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                            <p>By: <?php echo htmlspecialchars($row['username']); ?></p>
                        </div>
                        <div class="admin-actions">
                            <form action="index.php?action=approve_wallpaper" method="post"><input type="hidden" name="wallpaper_id" value="<?php echo $row['id']; ?>"><button type="submit" class="btn btn-primary"><i class="fas fa-check"></i></button></form>
                            <form action="index.php?action=reject_wallpaper" method="post" onsubmit="return confirm('Rejecting will permanently delete this. Are you sure?');"><input type="hidden" name="wallpaper_id" value="<?php echo $row['id']; ?>"><button type="submit" class="btn btn-danger"><i class="fas fa-times"></i></button></form>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
            <?php else: ?><p class="no-results">No wallpapers are pending approval.</p><?php endif; ?>
        </div>
    <?php elseif ($admin_tab === 'users'): ?>
        <?php
        $users_stmt = $conn->prepare("SELECT id, username, email, badge FROM users WHERE id != ? ORDER BY username ASC");
        $users_stmt->bind_param("i", $_SESSION['user_id']);
        $users_stmt->execute();
        $users_result = $users_stmt->get_result();
        ?>
         <div class="section-container">
            <h2>User Management</h2>
            <?php if(isset($_GET['badge_updated'])): ?><div class="success-message"><p>User badge updated successfully.</p></div><?php endif; ?>
            <div class="user-management-list">
            <?php while ($user = $users_result->fetch_assoc()): ?>
                <div class="user-manage-card">
                    <div class="user-info">
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong> <?php echo display_badge($user['badge']); ?><br>
                        <small><?php echo htmlspecialchars($user['email']); ?></small>
                    </div>
                    <form action="index.php?action=update_badge" method="post" class="user-badge-form">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <select name="new_badge">
                            <option value="user" <?php echo $user['badge'] === 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="verified" <?php echo $user['badge'] === 'verified' ? 'selected' : ''; ?>>Verified</option>
                            <option value="professional" <?php echo $user['badge'] === 'professional' ? 'selected' : ''; ?>>Professional</option>
                            <option value="admin" <?php echo $user['badge'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            <?php endwhile; ?>
            </div>
         </div>
    <?php endif; ?>
</div>
