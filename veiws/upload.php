<?php
if (!isset($_SESSION['user_id'])) { header("Location: index.php?page=login"); exit(); }
$errors = []; $success_msg = ""; $title = $tags = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action']) && $_GET['action'] == 'upload') {
    $title = trim($_POST['title']);
    $tags = trim($_POST['tags']);
    $file = $_FILES['wallpaper'];
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($tags) || $file['error'] == 4) { $errors[] = "All fields and a file are required."; }
    else {
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($fileActualExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 10000000) { // 10MB limit
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $fileDestination = 'uploads/' . $fileNameNew;
                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $status = (isset($_SESSION['user_badge']) && in_array($_SESSION['user_badge'], ['admin', 'verified'])) ? 'approved' : 'pending';
                        $stmt = $conn->prepare("INSERT INTO wallpapers (user_id, title, tags, file_path, status) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("issss", $user_id, $title, $tags, $fileDestination, $status);
                        if ($stmt->execute()) {
                            $success_msg = ($status === 'approved') ? "Upload successful! Your wallpaper is live." : "Upload successful! Your wallpaper is pending review.";
                            $title = $tags = ""; // Clear form on success
                        } else { $errors[] = "Failed to save to database."; unlink($fileDestination); }
                        $stmt->close();
                    } else { $errors[] = "Failed to move uploaded file."; }
                } else { $errors[] = "Your file is too large (max 10MB)."; }
            } else { $errors[] = "There was an error uploading your file."; }
        } else { $errors[] = "You cannot upload files of this type (only JPG, JPEG, PNG)."; }
    }
}
?>
<div class="container upload-form">
    <?php if (!empty($errors)): ?><div class="error-messages"><?php foreach ($errors as $error) echo "<p>$error</p>"; ?></div><?php endif; ?>
    <?php if ($success_msg): ?><div class="success-message"><p><?php echo $success_msg; ?></p></div><?php endif; ?>
    <form action="index.php?page=upload&action=upload" method="post" enctype="multipart/form-data">
        <div class="form-group"><label for="title">Title</label><input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required></div>
        <div class="form-group"><label for="wallpaper">Choose File</label><input type="file" name="wallpaper" accept="image/png, image/jpeg" required></div>
        <div class="form-group"><label for="tags">Tags</label><input type="text" name="tags" value="<?php echo htmlspecialchars($tags); ?>" placeholder="e.g., 8k, landscape, animal" required><small>Separate with a comma (,).</small></div>
        <button type="submit" class="btn btn-primary">Upload Wallpaper</button>
    </form>
</div>
