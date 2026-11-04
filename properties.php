<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

include 'includes/header.php';

$conn = new mysqli("localhost", "root", "", "apex-agent");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/*
 * URL থেকে location নেওয়া হচ্ছে
 */
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

if (empty($location)) {
    header("Location: index.php");
    exit;
}

/*
 * Database-এ যে location আছে সেটা খুঁজে বের করা
 */
$stmt = $conn->prepare("
    SELECT DISTINCT location
    FROM properties
    WHERE LOWER(location) LIKE LOWER(?)
    LIMIT 1
");

$searchLocation = '%' . $location . '%';
$stmt->bind_param("s", $searchLocation);
$stmt->execute();
$result = $stmt->get_result();
$locationRow = $result->fetch_assoc();

if (!$locationRow) {
    die("Location not found. Please go back and select a valid location.");
}

$actualLocation = $locationRow['location'];

/*
 * এই location-এর সব properties fetch করা
 */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

$stmt = $conn->prepare("
    SELECT *
    FROM properties
    WHERE location = ?
    ORDER BY id DESC
    LIMIT ?, ?
");

$stmt->bind_param("sii", $actualLocation, $offset, $perPage);
$stmt->execute();
$result = $stmt->get_result();

/*
 * Total properties count
 */
$countStmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM properties
    WHERE location = ?
");

$countStmt->bind_param("s", $actualLocation);
$countStmt->execute();
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();
$totalProperties = $countRow['total'];
$totalPages = ceil($totalProperties / $perPage);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Rental Rooms in <?= htmlspecialchars($actualLocation) ?>
        - APEX Agent
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    >

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-header p {
            font-size: 16px;
            opacity: 0.95;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .property-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .property-card:hover .property-image img {
            transform: scale(1.05);
        }

        .no-image {
            color: #bbb;
            font-size: 48px;
        }

        .property-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .property-badge.rented {
            background: #dc3545;
        }

        .property-content {
            padding: 20px;
        }

        .property-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .property-info {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }

        .property-info span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .property-description {
            font-size: 14px;
            color: #777;
            margin-bottom: 15px;
            line-height: 1.5;
            min-height: 40px;
        }

        .property-price {
            font-size: 22px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }

        .property-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn-details {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-details:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            transform: translateX(3px);
        }

        .pagination-section {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .pagination {
            gap: 5px;
        }

        .pagination .page-link {
            color: #667eea;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .pagination .page-link:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination .page-item.active .page-link {
            background: #667eea;
            border-color: #667eea;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: #764ba2;
            transform: translateX(-3px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .empty-state h2 {
            font-size: 28px;
            color: #666;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 16px;
            color: #999;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 26px;
            }

            .properties-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }

            .property-price {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>

<div class="page-header">
    <div class="container">
        <a href="index.php" class="back-button">← Back to Locations</a>

        <h1>🏠 Rental Rooms in <?= htmlspecialchars($actualLocation) ?></h1>

        <p>
            Total Properties: <strong><?= $totalProperties ?></strong>
        </p>
    </div>
</div>

<div class="container">

    <?php if ($result->num_rows > 0): ?>

        <div class="properties-grid">

            <?php while ($property = $result->fetch_assoc()): ?>

                <?php
                $imageName = basename((string)($property['image'] ?? ''));
                $status = $property['status'] ?? 'Available';
                $isRented = $status === 'Rented';
                ?>

                <div class="property-card">

                    <div class="property-image">
                        <?php if ($imageName !== ''): ?>

                            <img
                                src="uploads/<?= htmlspecialchars($imageName) ?>"
                                alt="<?= htmlspecialchars($property['title'] ?? '') ?>"
                            >

                        <?php else: ?>

                            <span class="no-image">📷</span>

                        <?php endif; ?>

                        <div class="property-badge <?= $isRented ? 'rented' : '' ?>">
                            <?= htmlspecialchars($status) ?>
                        </div>
                    </div>

                    <div class="property-content">

                        <h3 class="property-title">
                            <?= htmlspecialchars($property['title'] ?? 'Untitled') ?>
                        </h3>

                        <div class="property-info">
                            <span>
                                🛏️
                                <?= (int)($property['bedrooms'] ?? 0) ?> Bedrooms
                            </span>

                            <span>
                                🚿
                                <?= (int)($property['bathrooms'] ?? 0) ?> Bathrooms
                            </span>
                        </div>

                        <?php if (!empty($property['area'])): ?>
                            <div class="property-info">
                                <span>
                                    📐
                                    <?= htmlspecialchars($property['area']) ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <p class="property-description">
                            <?= htmlspecialchars(
                                substr($property['description'] ?? '', 0, 100)
                            ) ?>
                            <?php if (strlen($property['description'] ?? '') > 100): ?>
                                ...
                            <?php endif; ?>
                        </p>

                        <div class="property-price">
                            ¥<?= number_format($property['price'] ?? 0, 0) ?>
                            <span style="font-size: 14px; color: #999;"> / month</span>
                        </div>

                        <div class="property-footer">
                            <a
                                href="property-details.php?id=<?= (int)$property['id'] ?>"
                                class="btn-details"
                            >
                                View Details →
                            </a>
                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

        <?php if ($totalPages > 1): ?>

            <div class="pagination-section">
                <nav>
                    <ul class="pagination">

                        <?php if ($page > 1): ?>

                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="properties.php?location=<?= urlencode($location) ?>&page=1"
                                >
                                    First
                                </a>
                            </li>

                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="properties.php?location=<?= urlencode($location) ?>&page=<?= $page - 1 ?>"
                                >
                                    Previous
                                </a>
                            </li>

                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                                <a
                                    class="page-link"
                                    href="properties.php?location=<?= urlencode($location) ?>&page=<?= $i ?>"
                                >
                                    <?= $i ?>
                                </a>
                            </li>

                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>

                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="properties.php?location=<?= urlencode($location) ?>&page=<?= $page + 1 ?>"
                                >
                                    Next
                                </a>
                            </li>

                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="properties.php?location=<?= urlencode($location) ?>&page=<?= $totalPages ?>"
                                >
                                    Last
                                </a>
                            </li>

                        <?php endif; ?>

                    </ul>
                </nav>
            </div>

        <?php endif; ?>

    <?php else: ?>

        <div class="empty-state">
            <h2>No Properties Found</h2>
            <p>
                Sorry, there are no properties available in
                <?= htmlspecialchars($actualLocation) ?> at the moment.
            </p>
            <a href="index.php" class="btn-details mt-3">
                Go Back to Locations
            </a>
        </div>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>