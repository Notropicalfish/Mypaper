        </main>

        <footer class="app-footer">
             <nav class="main-nav">
                <a href="index.php?page=home" class="<?php echo ($page === 'home' ? 'active' : ''); ?>"><i class="fas fa-home"></i> Wallpapers</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=upload" class="<?php echo ($page === 'upload' ? 'active' : ''); ?>"><i class="fas fa-upload"></i> Upload</a>
                    <a href="index.php?page=profile" class="<?php echo ($page === 'profile' ? 'active' : ''); ?>"><i class="fas fa-user"></i> Profile</a>
                     <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="index.php?page=admin" class="<?php echo ($page === 'admin' ? 'active' : ''); ?>"><i class="fas fa-cogs"></i> Admin</a>
                    <?php endif; ?>
                    <a href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="<?php echo ($page === 'login' ? 'active' : ''); ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
                <?php endif; ?>
            </nav>
        </footer>
    </div>
    <script>
    function filterWallpapers() {
        let input = document.getElementById('searchInput');
        let filter = input.value.toLowerCase();
        let grid = document.querySelector('.wallpaper-grid');
        let cards = grid.getElementsByClassName('wallpaper-card');

        for (let i = 0; i < cards.length; i++) {
            let tags = cards[i].getAttribute('data-tags');
            if (tags.toLowerCase().indexOf(filter) > -1) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        const downloadBtn = document.getElementById('downloadBtn');
        if(downloadBtn) {
            downloadBtn.addEventListener('click', (e) => {
                const wallpaperId = e.target.closest('a').dataset.id; // Correctly get data-id from the link
                fetch('index.php?action=update_download_count', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: wallpaperId })
                });
            });
        }
    });
    </script>
</body>
</html>
