<?php
// Handle form submission
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zip_file'])) {
    $zipFile = $_FILES['zip_file']['tmp_name'];
    $uploadDir = __DIR__ . '/';
    $extractDir = __DIR__ . '/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    if (!is_dir($extractDir)) mkdir($extractDir, 0755, true);

    $uploadedPath = $uploadDir . basename($_FILES['zip_file']['name']);

    if (move_uploaded_file($zipFile, $uploadedPath)) {
        $zip = new ZipArchive;
        if ($zip->open($uploadedPath) === TRUE) {
            $containsPHP = false;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if (strtolower(pathinfo($entryName, PATHINFO_EXTENSION)) === 'php') {
                    $containsPHP = true;
                    break;
                }
            }

            if ($containsPHP) {
                $zip->extractTo($extractDir);
                $message = '<span class="green-text">✅ ZIP file extracted successfully.</span>';
            } else {
                $message = '<span class="red-text">❌ No PHP file found in ZIP.</span>';
            }

            $zip->close();
        } else {
            $message = '<span class="red-text">❌ Failed to open ZIP file.</span>';
        }
    } else {
        $message = '<span class="red-text">❌ Failed to upload ZIP file.</span>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP ZIP Unzipper</title>
    <!-- MaterializeCSS for Material UI style -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
        body { padding: 40px; background: #f9f9f9; }
        .container { max-width: 600px; margin: auto; }
        .card { padding: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h4 class="center-align">PHP ZIP Unzipper</h4>

    <div class="card">
        <form method="post" enctype="multipart/form-data">
            <div class="file-field input-field">
                <div class="btn blue">
                    <span>Upload ZIP</span>
                    <input type="file" name="zip_file" accept=".zip" required>
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Choose a .zip file">
                </div>
            </div>

            <div class="center">
                <button class="btn green waves-effect" type="submit">Submit</button>
            </div>
        </form>

        <?php if ($message): ?>
            <div class="section center-align">
                <p><?= $message ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
