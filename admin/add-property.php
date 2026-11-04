<?php

session_start();

require_once "../includes/db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $price = trim($_POST["price"]);
    $location = trim($_POST["location"]);
    $property_type = trim($_POST["property_type"]);
    $description = trim($_POST["description"]);
    $bedrooms = (int) $_POST["bedrooms"];
    $bathrooms = (int) $_POST["bathrooms"];
    $area = trim($_POST["area"]);
    $status = trim($_POST["status"]);

    $imageName = null;

    // Image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {

        $uploadDir = "../uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = $_FILES["image"]["name"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );

        $allowedExtensions = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($extension, $allowedExtensions)) {

            $error = "Only JPG, JPEG, PNG and WEBP images are allowed.";

        } else {

            $imageName = uniqid("property_", true) . "." . $extension;

            $uploadPath = $uploadDir . $imageName;

            if (!move_uploaded_file($tmpName, $uploadPath)) {

                $error = "Image upload failed.";
                $imageName = null;

            }
        }
    }

    if ($error === "") {

        if (
            $title === "" ||
            $price === "" ||
            $location === "" ||
            $property_type === ""
        ) {

            $error = "Please fill in all required fields.";

        } else {

            try {

                $sql = "INSERT INTO properties
                    (
                        title,
                        price,
                        location,
                        property_type,
                        description,
                        bedrooms,
                        bathrooms,
                        area,
                        status,
                        image
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $title,
                    $price,
                    $location,
                    $property_type,
                    $description,
                    $bedrooms,
                    $bathrooms,
                    $area,
                    $status,
                    $imageName
                ]);

                $message = "Property added successfully!";

                // Clear form values
                $title = "";
                $price = "";
                $location = "";
                $property_type = "";
                $description = "";
                $bedrooms = 0;
                $bathrooms = 0;
                $area = "";

            } catch (PDOException $e) {

                $error = "Database error: " . $e->getMessage();

            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Property - APEX AGENT</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
        }

        .navbar {
            background: #111827;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 25px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .required {
            color: red;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            border: none;
            padding: 13px 22px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
        }

        .btn-save {
            background: #16a34a;
            color: white;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        @media (max-width: 700px) {

            .row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .container {
                margin: 20px auto;
            }

            .form-box {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<div class="navbar">

    <h2>APEX AGENT ADMIN</h2>

    <div>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="manage_properties.php">
            Properties
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>

<div class="container">

    <div class="form-box">

        <h1>Add New Property</h1>

        <?php if ($message): ?>

            <div class="success">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <!-- Property Title -->

            <div class="form-group">

                <label>
                    Property Title
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="title"
                    placeholder="Example: Modern 3 Bedroom House"
                    value="<?= htmlspecialchars($title ?? '') ?>"
                    required
                >

            </div>


            <!-- Price + Location -->

            <div class="row">

                <div class="form-group">

                    <label>
                        Price
                        <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        name="price"
                        placeholder="Example: 250000"
                        value="<?= htmlspecialchars($price ?? '') ?>"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Location
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="location"
                        placeholder="Example: Tokyo, Japan"
                        value="<?= htmlspecialchars($location ?? '') ?>"
                        required
                    >

                </div>

            </div>


            <!-- Property Type + Status -->

            <div class="row">

                <div class="form-group">

                    <label>
                        Property Type
                        <span class="required">*</span>
                    </label>

                    <select
                        name="property_type"
                        required
                    >

                        <option value="">
                            Select Property Type
                        </option>

                        <option value="House">
                            House
                        </option>

                        <option value="Apartment">
                            Apartment
                        </option>

                        <option value="Condo">
                            Condo
                        </option>

                        <option value="Land">
                            Land
                        </option>

                        <option value="Office">
                            Office
                        </option>

                        <option value="Shop">
                            Shop
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Status
                    </label>

                    <select name="status">

                        <option value="Available">
                            Available
                        </option>

                        <option value="Sold">
                            Sold
                        </option>

                        <option value="Rented">
                            Rented
                        </option>

                    </select>

                </div>

            </div>


            <!-- Bedrooms + Bathrooms -->

            <div class="row">

                <div class="form-group">

                    <label>
                        Bedrooms
                    </label>

                    <input
                        type="number"
                        name="bedrooms"
                        value="<?= htmlspecialchars($bedrooms ?? 0) ?>"
                        min="0"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Bathrooms
                    </label>

                    <input
                        type="number"
                        name="bathrooms"
                        value="<?= htmlspecialchars($bathrooms ?? 0) ?>"
                        min="0"
                    >

                </div>

            </div>


            <!-- Area -->

            <div class="form-group">

                <label>
                    Area
                </label>

                <input
                    type="text"
                    name="area"
                    placeholder="Example: 120 m²"
                    value="<?= htmlspecialchars($area ?? '') ?>"
                >

            </div>


            <!-- Description -->

            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    placeholder="Write property description..."
                ><?= htmlspecialchars($description ?? '') ?></textarea>

            </div>


            <!-- Image -->

            <div class="form-group">

                <label>
                    Property Image
                </label>

                <input
                    type="file"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

            </div>


            <!-- Buttons -->

            <div class="buttons">

                <button
                    type="submit"
                    class="btn btn-save"
                >
                    Save Property
                </button>

                <a
                    href="manage_properties.php"
                    class="btn btn-back"
                >
                    Back
                </a>

            </div>

        </form>

    </div>

</div>

</body>

</html>