<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/layout.php';

$extra_css = '
.policy-wrap{max-width:820px;margin:2.5rem auto;padding:0 1.2rem 3rem}
.policy-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);padding:2.5rem}
.policy-card h1{font-size:1.6rem;font-weight:800;color:var(--primary);margin:0 0 .3rem}
.policy-card .updated{color:var(--muted);font-size:.88rem;margin-bottom:1.5rem}
.policy-card h2{font-size:1.08rem;font-weight:700;color:var(--primary);border-bottom:2px solid var(--border);padding-bottom:.4rem;margin:2rem 0 .75rem}
.policy-card h3{font-size:.97rem;font-weight:600;margin:1.25rem 0 .4rem}
.policy-card p,.policy-card li{font-size:.93rem;line-height:1.75;color:var(--muted)}
.policy-card ul{padding-left:1.4rem;margin:.4rem 0}
.highlight-box{background:#fffbeb;border-left:4px solid var(--secondary);border-radius:8px;padding:1rem 1.2rem;margin:1rem 0}
.highlight-box p{margin:0;color:var(--text)}
.contact-box{background:var(--bg);border-radius:10px;padding:1rem 1.2rem;margin-top:.5rem}
.contact-box p{margin:0;color:var(--text)}
';

echo page_head('Privacy Policy — GamScholarship', 'How GamScholarship collects, uses and protects your personal information.', $extra_css);
echo '<body>';
echo site_header();
?>

<div class="policy-wrap">
    <div class="policy-card">

        <h1>Privacy Policy</h1>
        <p class="updated">Last updated: <?php echo date('F j, Y'); ?></p>

        <div class="highlight-box">
            <p><strong>Quick Summary:</strong> We collect minimal personal information only when you contact us or subscribe to updates. We use cookies for analytics and advertising. We never sell your data.</p>
        </div>

        <h2>1. Information We Collect</h2>
        <h3>Information You Provide</h3>
        <ul>
            <li><strong>Contact Form:</strong> Your name, email address, and message content when you get in touch with us.</li>
            <li><strong>Newsletter Subscription:</strong> Your email address when you subscribe to scholarship alerts.</li>
            <li><strong>Account Registration:</strong> Username, email and password (hashed) if you create an account.</li>
        </ul>
        <h3>Information Collected Automatically</h3>
        <ul>
            <li><strong>Analytics:</strong> We use Google Analytics to understand how visitors use our site (pages visited, time spent, device type, approximate location).</li>
            <li><strong>Cookies:</strong> Small files stored on your device to improve functionality and show relevant ads.</li>
            <li><strong>Log Data:</strong> IP address, browser type, and referring pages for security and analytics purposes.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>Respond to your enquiries and provide support</li>
            <li>Send scholarship updates and newsletters (only if you subscribed)</li>
            <li>Improve our website content and user experience</li>
            <li>Analyse site usage patterns and traffic</li>
            <li>Display relevant advertisements through Google AdSense</li>
            <li>Protect against spam and abuse</li>
        </ul>

        <h2>3. Cookies and Tracking</h2>
        <p>We use several types of cookies:</p>
        <ul>
            <li><strong>Essential Cookies:</strong> Required for basic site functionality (e.g. session management)</li>
            <li><strong>Analytics Cookies:</strong> Google Analytics — helps us understand how the site is used</li>
            <li><strong>Advertising Cookies:</strong> Google AdSense — to show relevant ads based on your interests</li>
            <li><strong>Preference Cookies:</strong> Remember your settings such as cookie consent</li>
        </ul>
        <p>You can control cookies through your browser settings. Disabling certain cookies may affect site functionality.</p>

        <h2>4. Third-Party Services</h2>
        <h3>Google Analytics</h3>
        <p>We use Google Analytics to analyse traffic. You can opt out using the <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics opt-out browser add-on</a>.</p>
        <h3>Google AdSense</h3>
        <p>We display advertisements through Google AdSense. Google may use cookies to show personalised ads based on your interests. You can manage preferences in your <a href="https://adssettings.google.com/" target="_blank" rel="noopener">Google Ads Settings</a>.</p>

        <h2>5. Data Sharing and Disclosure</h2>
        <p>We do not sell, trade, or rent your personal information. We may share information only in these circumstances:</p>
        <ul>
            <li>With your explicit consent</li>
            <li>To comply with legal obligations</li>
            <li>To protect the rights, privacy, safety or property of GamScholarship or our users</li>
            <li>With service providers who help operate our site (under strict confidentiality)</li>
        </ul>

        <h2>6. Data Security</h2>
        <p>We implement appropriate technical and organisational measures to protect your personal information. Passwords are stored as secure hashes — we never store plain-text passwords. However, no internet transmission is 100% secure, and we encourage you to use a strong, unique password.</p>

        <h2>7. Your Rights</h2>
        <p>You have the right to:</p>
        <ul>
            <li>Access the personal information we hold about you</li>
            <li>Request correction of inaccurate information</li>
            <li>Request deletion of your personal information</li>
            <li>Unsubscribe from our newsletter at any time</li>
            <li>Object to processing of your personal information</li>
        </ul>

        <h2>8. International Transfers</h2>
        <p>Your information may be transferred to and processed in countries other than The Gambia, including the United States (where Google's servers are located). We ensure appropriate safeguards are in place for all such transfers.</p>

        <h2>9. Children's Privacy</h2>
        <p>Our services are not directed to children under 13. We do not knowingly collect personal information from children under 13. If you become aware that a child has provided us with personal information, please contact us immediately.</p>

        <h2>10. Changes to This Policy</h2>
        <p>We may update this Privacy Policy periodically. Significant changes will be communicated by updating the "Last updated" date at the top of this page. Continued use of the site after changes constitutes your acceptance of the updated policy.</p>

        <h2>11. Contact Us</h2>
        <div class="contact-box">
            <p>If you have any questions about this Privacy Policy or how we handle your data, please contact us:</p>
            <ul style="margin:.5rem 0 0;padding-left:1.2rem">
                <li><strong>Email:</strong> <a href="mailto:info@gamscolaship.online">info@gamscolaship.online</a></li>
                <li><strong>Website:</strong> <a href="https://gamscolaship.online">gamscolaship.online</a></li>
            </ul>
        </div>

        <h2>12. GDPR Compliance (EU Visitors)</h2>
        <p>If you are in the European Union, you have additional rights under GDPR including the right to data portability, the right to restriction of processing, and the right to lodge a complaint with your national supervisory authority. Our lawful basis for processing analytics data is legitimate interest; for newsletter subscriptions, it is your consent.</p>

    </div>
</div>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
