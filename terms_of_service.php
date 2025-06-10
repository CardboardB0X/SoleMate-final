<?php
$page_title = "Terms of Service";
$path_prefix = ''; // Assuming this file is in the root directory
require_once __DIR__ . '/config.php'; // For SITE_NAME and SITE_URL
require_once __DIR__ . '/templates/header.php';
?>

<div class="container" style="padding-top: 20px; padding-bottom: 40px;">
    <h1 class="page-main-title" style="color: #1c3a5e; border-bottom: 2px solid #f39c12; padding-bottom:15px;">Terms of Service</h1>
    
    <div style="max-width: 800px; margin: 20px auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.07);">
        <p><strong>Last Updated: <?php echo date("F j, Y"); ?></strong></p>

        <p>Welcome to <?php echo e(SITE_NAME); ?>! These terms and conditions ("Terms", "Terms of Service") govern your use of our website located at <?php echo e(SITE_URL); ?> (together or individually "Service") operated by <?php echo e(SITE_NAME); ?> ("us", "we", or "our").</p>

        <p>Please read these Terms of Service carefully before using our Service. Your access to and use of the Service is conditioned upon your acceptance of and compliance with these Terms. These Terms apply to all visitors, users, and others who wish to access or use the Service.</p>

        <p>By accessing or using the Service, you agree to be bound by these Terms. If you disagree with any part of the terms, then you do not have permission to access the Service.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">1. Accounts</h2>
        <p>When you create an account with us, you guarantee that the information you provide us is accurate, complete, and current at all times. Inaccurate, incomplete, or obsolete information may result in the immediate termination of your account on our Service.</p>
        <p>You are responsible for maintaining the confidentiality of your account and password, including but not limited to the restriction of access to your computer and/or account. You agree to accept responsibility for any and all activities or actions that occur under your account and/or password, whether your password is with our Service or a third-party service.</p>
        <p>You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p>
        <p>You may not use as a username the name of another person or entity or that is not lawfully available for use, a name or trademark that is subject to any rights of another person or entity other than you, without appropriate authorization. You may not use as a username any name that is offensive, vulgar, or obscene.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">2. Purchases</h2>
        <p>If you wish to purchase any product or service made available through the Service ("Purchase"), you may be asked to supply certain information relevant to your Purchase including, without limitation, your credit card number, the expiration date of your credit card, your billing address, and your shipping information.</p>
        <p>You represent and warrant that: (i) you have the legal right to use any credit card(s) or other payment method(s) in connection with any Purchase; and that (ii) the information you supply to us is true, correct, and complete.</p>
        <p>The service may employ the use of third-party services for the purpose of facilitating payment and the completion of Purchases. By submitting your information, you grant us the right to provide the information to these third parties subject to our Privacy Policy.</p>
        <p>We reserve the right to refuse or cancel your order at any time for reasons including but not limited to: product or service availability, errors in the description or price of the product or service, error in your order, or other reasons.</p>
        <p>We reserve the right to refuse or cancel your order if fraud or an unauthorized or illegal transaction is suspected.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">3. Availability, Errors, and Inaccuracies</h2>
        <p>We are constantly updating product and service offerings on the Service. We may experience delays in updating information on the Service and in our advertising on other websites. The information found on the Service may contain errors or inaccuracies and may not be complete or current. Products or services may be mispriced, described inaccurately, or unavailable on the Service, and we cannot guarantee the accuracy or completeness of any information found on the Service.</p>
        <p>We therefore reserve the right to change or update information and to correct errors, inaccuracies, or omissions at any time without prior notice.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">4. Intellectual Property</h2>
        <p>The Service and its original content (excluding Content provided by users), features, and functionality are and will remain the exclusive property of <?php echo e(SITE_NAME); ?> and its licensors. The Service is protected by copyright, trademark, and other laws of both the [Your Country/Jurisdiction] and foreign countries. Our trademarks and trade dress may not be used in connection with any product or service without the prior written consent of <?php echo e(SITE_NAME); ?>.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">5. Links To Other Web Sites</h2>
        <p>Our Service may contain links to third-party web sites or services that are not owned or controlled by <?php echo e(SITE_NAME); ?>.</p>
        <p><?php echo e(SITE_NAME); ?> has no control over, and assumes no responsibility for the content, privacy policies, or practices of any third-party web sites or services. We do not warrant the offerings of any ofthese entities/individuals or their websites.</p>
        <p>You acknowledge and agree that <?php echo e(SITE_NAME); ?> shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such third-party web sites or services.</p>
        <p>We strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or services that you visit.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">6. Termination</h2>
        <p>We may terminate or suspend your account and bar access to the Service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever and without limitation, including but not limited to a breach of the Terms.</p>
        <p>If you wish to terminate your account, you may simply discontinue using the Service.</p>
        <p>All provisions of the Terms which by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity, and limitations of liability.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">7. Indemnification</h2>
        <p>You agree to defend, indemnify and hold harmless <?php echo e(SITE_NAME); ?> and its licensee and licensors, and their employees, contractors, agents, officers and directors, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney's fees), resulting from or arising out of a) your use and access of the Service, by you or any person using your account and password; b) a breach of these Terms, or c) Content posted on the Service.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">8. Limitation Of Liability</h2>
        <p>In no event shall <?php echo e(SITE_NAME); ?>, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or use of or inability to access or use the Service; (ii) any conduct or content of any third party on the Service; (iii) any content obtained from the Service; and (iv) unauthorized access, use or alteration of your transmissions or content, whether based on warranty, contract, tort (including negligence) or any other legal theory, whether or not we have been informed of the possibility of such damage, and even if a remedy set forth herein is found to have failed of its essential purpose.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">9. Disclaimer</h2>
        <p>Your use of the Service is at your sole risk. The Service is provided on an "AS IS" and "AS AVAILABLE" basis. The Service is provided without warranties of any kind, whether express or implied, including, but not limited to, implied warranties of merchantability, fitness for a particular purpose, non-infringement or course of performance.</p>
        <p><?php echo e(SITE_NAME); ?> its subsidiaries, affiliates, and its licensors do not warrant that a) the Service will function uninterrupted, secure or available at any particular time or location; b) any errors or defects will be corrected; c) the Service is free of viruses or other harmful components; or d) the results of using the Service will meet your requirements.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">10. Governing Law</h2>
        <p>These Terms shall be governed and construed in accordance with the laws of [Your Country/Jurisdiction, e.g., Philippines], without regard to its conflict of law provisions.</p>
        <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have had between us regarding the Service.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">11. Changes</h2>
        <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>
        <p>By continuing to access or use our Service after any revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, you are no longer authorized to use the Service.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">12. Emmanuel Creo</h2>
        <p>Pinakagwapong lalaki sa balat ng lupa, wala kahit sinoman higit ang kaniyang kagwapuhan. Hayop ka! Gandang lalake!</p>

        <h2 style="margin-top: 30px; color: #e67e22;">13. Cristian John De Leon</h2>
        <p>Hev Abi nang akala na walang pasok tuwing Lunes.</p>

        <h2 style="margin-top: 30px; color: #e67e22;">14. Avellana Andersen</h2>
        <p>Ito ang aming Computer Scientist, founder ng ClickSenvee</p>

        <h2 style="margin-top: 30px; color: #e67e22;">Contact Us</h2>
        <p>If you have any questions about these Terms, please contact us:</p>
        <ul>
            <li>By email: [Your Contact Email Address, e.g., support@solemate.com]</li>
            <li>By visiting this page on our website: [Link to your Contact Us page, e.g., <?php echo e(SITE_URL); ?>contact.php]</li>
            <li>By phone number: [Your Contact Phone Number, optional]</li>
        </ul>
    </div>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>