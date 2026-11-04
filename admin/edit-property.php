<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "apex-agent");
$conn->set_charset("utf8mb4");

/*
 * URL থেকে property ID নেওয়া হচ্ছে
 */
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    die("Invalid property ID.");
}

/*
 * Update form submit হলে এই অংশ চলবে
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $property_type = trim($_POST['property_type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $bedrooms = (int)($_POST['bedrooms'] ?? 0);
    $bathrooms = (int)($_POST['bathrooms'] ?? 0);
    $area = trim($_POST['area'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');

    $currentImage = basename((string)($_POST['current_image'] ?? ''));
    $newImageName = $currentImage;

    /*
     * নতুন image upload করা হলে
     */
    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] === UPLOAD_ERR_OK
    ) {
        $uploadDir = __DIR__ . "/../uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = basename($_FILES['image']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowedExtensions, true)) {
            die("Only JPG, JPEG, PNG, GIF and WEBP images are allowed.");
        }

        $newImageName = time() . "_" . uniqid() . "." . $extension;
        $targetPath = $uploadDir . $newImageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            die("Image upload failed.");
        }

        /*
         * নতুন image সফলভাবে upload হলে পুরোনো image delete করা হচ্ছে
         */
        if ($currentImage !== '') {
            $oldImagePath = $uploadDir . $currentImage;

            if (
                file_exists($oldImagePath) &&
                realpath($oldImagePath) !== realpath($targetPath)
            ) {
                unlink($oldImagePath);
            }
        }
    }

    /*
     * Database update
     */
    $stmt = $conn->prepare("
        UPDATE properties
        SET
            title = ?,
            price = ?,
            location = ?,
            property_type = ?,
            description = ?,
            bedrooms = ?,
            bathrooms = ?,
            area = ?,
            status = ?,
            image = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssssiisssi",
        $title,
        $price,
        $location,
        $property_type,
        $description,
        $bedrooms,
        $bathrooms,
        $area,
        $status,
        $newImageName,
        $id
    );

    $stmt->execute();

    /*
     * আপনার আসল file-এর নাম underscore-সহ
     */
    header("Location: manage_properties.php");
    exit;
}

/*
 * এই ID-এর property-এর পুরোনো data আনা হচ্ছে
 */
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    die("Property not found.");
}

$currentImageName = basename((string)($property['image'] ?? ''));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Property</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">Edit Property</h3>
        </div>

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$property['id'] ?>"
                >

                <input
                    type="hidden"
                    name="current_image"
                    value="<?= htmlspecialchars($currentImageName, ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="row">

                    <div class="col-md-6">

                        <div class="mb-3">
                            <label class="form-label">Title</label>

                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                value="<?= htmlspecialchars($property['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price</label>

                            <input
                                type="text"
                                name="price"
                                class="form-control"
                                value="<?= htmlspecialchars($property['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>

                            <input
                                type="text"
                                name="location"
                                class="form-control"
                                value="<?= htmlspecialchars($property['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Property Type</label>

                            <input
                                type="text"
                                name="property_type"
                                class="form-control"
                                value="<?= htmlspecialchars($property['property_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>

                            <textarea
                                name="description"
                                class="form-control"
                                rows="5"
                            ><?= htmlspecialchars($property['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="mb-3">
                            <label class="form-label">Bedrooms</label>

                            <input
                                type="number"
                                name="bedrooms"
                                class="form-control"
                                value="<?= (int)($property['bedrooms'] ?? 0) ?>"
                                min="0"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bathrooms</label>

                            <input
                                type="number"
                                name="bathrooms"
                                class="form-control"
                                value="<?= (int)($property['bathrooms'] ?? 0) ?>"
                                min="0"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Area</label>

                            <input
                                type="text"
                                name="area"
                                class="form-control"
                                value="<?= htmlspecialchars($property['area'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <select name="status" class="form-select">
                                <option
                                    value="Available"
                                    <?= (($property['status'] ?? '') === 'Available') ? 'selected' : '' ?>
                                >
                                    Available
                                </option>

                                <option
                                    value="Rented"
                                    <?= (($property['status'] ?? '') === 'Rented') ? 'selected' : '' ?>
                                >
                                    Rented
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Image</label>

                            <div class="mt-2">
                                <?php if ($currentImageName !== ''): ?>

                                    <img
                                        src="../uploads/<?= htmlspecialchars($currentImageName, ENT_QUOTES, 'UTF-8') ?>"
                                        width="180"
                                        height="130"
                                        class="img-thumbnail"
                                        style="object-fit: cover;"
                                        alt="Current property image"
                                    >

                                <?php else: ?>

                                    <p class="text-muted">No image uploaded.</p>

                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload New Image</label>

                            <input
                                type="file"
                                name="image"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.gif,.webp"
                            >

                            <small class="text-muted">
                                নতুন ছবি না দিলে পুরোনো ছবি থাকবে।
                            </small>
                        </div>

                    </div>

                </div>

                <button type="submit" class="btn btn-primary">
                    Update Property
                </button>

                <a
                    href="manage_properties.php"
                    class="btn btn-secondary"
                >
                    Back
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>

<?php
$conn->close();
?>