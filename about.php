<?php include 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - APEX Agent Rental Rooms</title>

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
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .page-header p {
            font-size: 18px;
            opacity: 0.95;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        section {
            background: white;
            margin: 40px auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        section h2 {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        section h3 {
            font-size: 22px;
            font-weight: 600;
            color: #667eea;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        section p {
            font-size: 16px;
            line-height: 1.8;
            color: #666;
            margin-bottom: 15px;
        }

        .features-list {
            list-style: none;
            padding: 0;
        }

        .features-list li {
            padding: 12px 0;
            padding-left: 30px;
            position: relative;
            font-size: 16px;
            color: #666;
            line-height: 1.6;
        }

        .features-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
            font-size: 18px;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .mission-card,
        .vision-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 30px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .mission-card h4,
        .vision-card h4 {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }

        .mission-card p,
        .vision-card p {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .team-member {
            text-align: center;
            padding: 25px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .team-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }

        .team-member h4 {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .team-member p {
            font-size: 14px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .team-member .description {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .contact-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .contact-info h3 {
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 15px;
        }

        .contact-item {
            margin: 20px 0;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .contact-item-icon {
            font-size: 24px;
            min-width: 30px;
        }

        .contact-item-content p {
            margin: 0;
            color: rgba(255, 255, 255, 0.95);
        }

        .btn-primary-custom {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 28px;
            }

            section {
                padding: 25px;
                margin: 20px auto;
            }

            section h2 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<!--========== PAGE HEADER ==========-->
<div class="page-header">
    <h1>About APEX Agent</h1>
    <p>Professional Rental Property Management Services</p>
</div>

<!--========== MAIN CONTENT ==========-->
<div class="container">

    <!--========== ABOUT US SECTION ==========-->
    <section>
        <h2>Who We Are</h2>

        <p>
            APEX Agent is a leading property rental management company dedicated to providing
            high-quality rental accommodations across major cities in Japan. With years of experience
            in the real estate industry, we pride ourselves on offering reliable, affordable, and
            comfortable living spaces for individuals and families.
        </p>

        <p>
            Our mission is to make finding your perfect rental home simple, transparent, and hassle-free.
            We believe that everyone deserves access to safe, well-maintained properties with professional
            management support.
        </p>

        <h3>Our Core Values</h3>

        <ul class="features-list">
            <li><strong>Integrity:</strong> We conduct all business with honesty and transparency</li>
            <li><strong>Quality:</strong> All our properties meet strict quality and safety standards</li>
            <li><strong>Customer First:</strong> Your satisfaction is our top priority</li>
            <li><strong>Professional Service:</strong> Dedicated support 24/7 for all our clients</li>
            <li><strong>Innovation:</strong> Continuously improving our services and technology</li>
        </ul>
    </section>

    <!--========== MISSION & VISION ==========-->
    <section>
        <h2>Our Mission & Vision</h2>

        <div class="mission-vision">
            <div class="mission-card">
                <h4>🎯 Our Mission</h4>
                <p>
                    To provide affordable, quality rental properties with exceptional customer service,
                    making it easy for people to find their ideal home across Japan.
                </p>
            </div>

            <div class="vision-card">
                <h4>🌟 Our Vision</h4>
                <p>
                    To become Japan's most trusted rental property management company, known for
                    quality properties, transparent pricing, and outstanding customer care.
                </p>
            </div>
        </div>
    </section>

    <!--========== STATISTICS ==========-->
    <section>
        <h2>Our Impact</h2>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Properties Listed</div>
            </div>

            <div class="stat-card">
                <div class="stat-number">5000+</div>
                <div class="stat-label">Happy Clients</div>
            </div>

            <div class="stat-card">
                <div class="stat-number">8</div>
                <div class="stat-label">Cities Covered</div>
            </div>

            <div class="stat-card">
                <div class="stat-number">10+</div>
                <div class="stat-label">Years Experience</div>
            </div>
        </div>
    </section>

    <!--========== WHY CHOOSE US ==========-->
    <section>
        <h2>Why Choose APEX Agent?</h2>

        <div class="mission-vision">
            <div class="mission-card">
                <h4>✅ Verified Properties</h4>
                <p>
                    Every property in our portfolio is carefully inspected and verified to meet
                    our high standards of quality and safety.
                </p>
            </div>

            <div class="mission-card">
                <h4>💰 Transparent Pricing</h4>
                <p>
                    No hidden charges. All pricing is clearly displayed upfront so you know
                    exactly what you're paying for.
                </p>
            </div>

            <div class="mission-card">
                <h4>📞 24/7 Support</h4>
                <p>
                    Our dedicated customer service team is available round the clock to assist
                    you with any questions or concerns.
                </p>
            </div>

            <div class="mission-card">
                <h4>🛡️ Safe & Secure</h4>
                <p>
                    Your safety and privacy are paramount. We use secure payment processing
                    and protect all client information.
                </p>
            </div>

            <div class="mission-card">
                <h4>⚡ Quick Process</h4>
                <p>
                    Fast and efficient booking process. Get your room in days, not weeks.
                    Simple documentation and smooth transactions.
                </p>
            </div>

            <div class="mission-card">
                <h4>🌍 Multiple Locations</h4>
                <p>
                    Properties available across major cities including Tokyo, Fukushima,
                    Niigata, and more.
                </p>
            </div>
        </div>
    </section>

    <!--========== TEAM SECTION ==========-->
    <section>
        <h2>Our Team</h2>

        <p>
            Our experienced team brings decades of combined experience in property management,
            customer service, and real estate. We're passionate about providing you with the best
            rental experience possible.
        </p>

        <div class="team-grid">
            <div class="team-member">
                <div class="team-avatar">👨‍💼</div>
                <h4>Takeshi Yamamoto</h4>
                <p>Founder & CEO</p>
                <div class="description">
                    20+ years of experience in real estate management and property development.
                </div>
            </div>

            <div class="team-member">
                <div class="team-avatar">👩‍💼</div>
                <h4>Yuki Tanaka</h4>
                <p>Operations Manager</p>
                <div class="description">
                    Expert in customer service and property maintenance with 15+ years experience.
                </div>
            </div>

            <div class="team-member">
                <div class="team-avatar">👨‍💻</div>
                <h4>Haruto Suzuki</h4>
                <p>Technical Lead</p>
                <div class="description">
                    Experienced in web development and system management for real estate platforms.
                </div>
            </div>

            <div class="team-member">
                <div class="team-avatar">👩‍🤝‍👨</div>
                <h4>Support Team</h4>
                <p>24/7 Customer Care</p>
                <div class="description">
                    Dedicated team always ready to help with your inquiries and concerns.
                </div>
            </div>
        </div>
    </section>

    <!--========== CONTACT SECTION ==========-->
    <section>
        <h2>Get in Touch</h2>

        <p>
            Have questions or need assistance? Our team is here to help. Contact us through any
            of the channels below, and we'll get back to you as soon as possible.
        </p>

        <div class="contact-info">
            <h3>📞 Contact Information</h3>

            <div class="contact-item">
                <div class="contact-item-icon">📍</div>
                <div class="contact-item-content">
                    <p><strong>Address:</strong></p>
                    <p>APEX Agent Headquarters<br>Tokyo, Japan</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-item-icon">📧</div>
                <div class="contact-item-content">
                    <p><strong>Email:</strong></p>
                    <p>support@apexagent.jp</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-item-icon">📞</div>
                <div class="contact-item-content">
                    <p><strong>Phone:</strong></p>
                    <p>+81 (0) 90-1234-5678</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-item-icon">🕐</div>
                <div class="contact-item-content">
                    <p><strong>Business Hours:</strong></p>
                    <p>Monday - Sunday: 9:00 AM - 6:00 PM JST<br>
                    24/7 Emergency Support Available</p>
                </div>
            </div>
        </div>

        <a href="index.php" class="btn-primary-custom">
            Back to Home
        </a>
    </section>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>