<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

include 'includes/header.php';

$conn = new mysqli("localhost", "root", "", "apex-agent");
$conn->set_charset("utf8mb4");

/*
 * সব unique location fetch করা হচ্ছে
 */
$locationsResult = $conn->query("
    SELECT DISTINCT location
    FROM properties
    WHERE location IS NOT NULL AND location != ''
    ORDER BY location ASC
");

$locations = [];
while ($row = $locationsResult->fetch_assoc()) {
    $locations[] = $row['location'];
}

/*
 * প্রতিটি location-এ কয়টি property আছে
 */
$locationCounts = [];
foreach ($locations as $loc) {
    $result = $conn->query("
        SELECT COUNT(*) as count
        FROM properties
        WHERE location = '" . $conn->real_escape_string($loc) . "'
    ");
    $row = $result->fetch_assoc();
    $locationCounts[$loc] = $row['count'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - APEX Agent Rental Rooms</title>

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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .hero-section h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-section p {
            font-size: 20px;
            opacity: 0.95;
            margin-bottom: 30px;
        }

        .location-page {
            padding: 60px 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 36px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .section-title p {
            font-size: 16px;
            color: #666;
        }

        .location-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .location-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .location-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .location-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .location-card:hover::before {
            opacity: 0.05;
        }

        .location-card h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #667eea;
        }

        .location-card .property-count {
            font-size: 14px;
            color: #999;
            margin-bottom: 15px;
        }

        .location-card p {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .btn-view {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .location-card:hover .btn-view {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            color: #666;
            margin-bottom: 20px;
        }

        .empty-state p {
            font-size: 16px;
            color: #999;
        }

        .features-section {
            background: white;
            padding: 60px 20px;
            margin-top: 60px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 14px;
            color: #666;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 32px;
            }

            .hero-section p {
                font-size: 16px;
            }

            .section-title h2 {
                font-size: 28px;
            }

            .location-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!--========== HERO SECTION ==========-->
<section class="hero-section">
    <div class="container">
        <h1>🏠 APEX Agent Rental Rooms</h1>

        <p>
            Discover your perfect rental room across multiple cities in Japan.
            <br>
            Professional management, transparent pricing, quality properties.
        </p>

        <a
            href="#locations"
            class="btn btn-light btn-lg mt-3"
            style="font-weight: 600;"
        >
            Explore Now
        </a>
    </div>
</section>

<!--========== LOCATIONS SECTION ==========-->
<main class="location-page" id="locations">

    <div class="section-title">
        <h2>Select Your Location</h2>
        <p>Browse rental rooms available in your preferred city</p>
    </div>

    <?php if (count($locations) > 0): ?>

        <div class="location-grid">

            <?php foreach ($locations as $location): ?>

                <a
                    href="properties.php?location=<?= urlencode(strtolower($location)) ?>"
                    class="location-card"
                >
                    <h2><?= htmlspecialchars($location) ?></h2>

                    <div class="property-count">
                        📍 <?= $locationCounts[$location] ?> properties available
                    </div>

                    <p>
                        Explore comfortable rental rooms in
                        <?= htmlspecialchars($location) ?>
                    </p>

                    <span class="btn-view">
                        View Properties →
                    </span>
                </a>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty-state">
            <h3>No Locations Available</h3>
            <p>Properties will be listed here soon. Please check back later!</p>
        </div>

    <?php endif; ?>

</main>

<!--========== FEATURES SECTION ==========-->
<section class="features-section">

    <div class="section-title">
        <h2>Why Choose APEX Agent?</h2>
        <p>Professional rental management with your comfort in mind</p>
    </div>

    <div class="features-grid">

        <div class="feature-card">
            <div class="feature-icon">✅</div>
            <h3>Verified Properties</h3>
            <p>All our properties are carefully verified and maintained to highest standards.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">💰</div>
            <h3>Transparent Pricing</h3>
            <p>No hidden charges. Clear and competitive pricing for all rental options.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🛡️</div>
            <h3>Safe & Secure</h3>
            <p>Your safety and privacy are our top priority. Secure payment processing.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📞</div>
            <h3>24/7 Support</h3>
            <p>Our dedicated team is available round the clock to assist you.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🌍</div>
            <h3>Multiple Locations</h3>
            <p>Properties available across major cities in Japan.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Quick Process</h3>
            <p>Fast and hassle-free booking process. Get your room in days!</p>
        </div>

    </div>

</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>