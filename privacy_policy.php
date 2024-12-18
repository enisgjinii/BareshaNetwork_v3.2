<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baresha Privacy Policy</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <!-- Custom Styles for Animations -->
    <style>
        /* Fade-in Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 1s ease-out forwards;
        }

        /* Delay Animations for Staggered Effect */
        .delay-1 {
            animation-delay: 0.2s;
        }

        .delay-2 {
            animation-delay: 0.4s;
        }

        .delay-3 {
            animation-delay: 0.6s;
        }

        .delay-4 {
            animation-delay: 0.8s;
        }

        .delay-5 {
            animation-delay: 1s;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100 font-sans">
    <header class="text-center py-10 border-b border-gray-700">
        <h1 class="text-5xl font-extrabold tracking-wide mb-2 animate-fade-in">Baresha</h1>
        <p class="text-xl text-gray-400 animate-fade-in delay-1">Privacy Policy</p>
    </header>
    <div class="container mx-auto max-w-4xl px-4 py-8">
        <p class="text-sm text-gray-400 mb-8 animate-fade-in delay-2">Effective Date: September 30, 2024</p>

        <section class="mb-12 animate-fade-in delay-3">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">1. Introduction</h2>
            <p class="mb-6 leading-relaxed">
                Welcome to <span class="font-semibold text-blue-400">Baresha</span> ("we," "us," or "our"). We are committed to protecting your privacy
                and ensuring the security of your personal information. This Privacy Policy outlines how we collect,
                use, disclose, and safeguard your information when you use our application.
            </p>
        </section>

        <section class="mb-12 animate-fade-in delay-4">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">2. Information We Collect</h2>

            <div class="mb-6">
                <h3 class="text-2xl font-semibold mb-2">2.1 Personal Data</h3>
                <p class="mb-4 leading-relaxed">
                    While using our service, we may ask you to provide us with certain personally identifiable information
                    that can be used to contact or identify you, such as:
                </p>
                <ul class="list-disc list-inside mb-6 space-y-1">
                    <li>Primary Google Account email address (<code class="text-sm bg-gray-800 rounded px-1">.../auth/userinfo.email</code>)</li>
                    <li>Personal profile information (<code class="text-sm bg-gray-800 rounded px-1">.../auth/userinfo.profile</code>)</li>
                    <li>OpenID identifier (<code class="text-sm bg-gray-800 rounded px-1">openid</code>)</li>
                </ul>
            </div>

            <div>
                <h3 class="text-2xl font-semibold mb-2">2.2 YouTube Data</h3>
                <p class="mb-4 leading-relaxed">
                    We also access various YouTube APIs to provide enhanced features, including:
                </p>
                <ul class="list-disc list-inside mb-8 space-y-1">
                    <li>YouTube Analytics (<code class="text-sm bg-gray-800 rounded px-1">.../auth/yt-analytics.readonly</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/yt-analytics-monetary.readonly</code>)</li>
                    <li>YouTube Data API (<code class="text-sm bg-gray-800 rounded px-1">.../auth/youtube.download</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtube.readonly</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtube</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtube.force-ssl</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtubepartner</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtubepartner-channel-audit</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtube.upload</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtube.channel-memberships.creator</code>, <code class="text-sm bg-gray-800 rounded px-1">.../auth/youtube.third-party-link.creator</code>)</li>
                </ul>
            </div>
        </section>

        <section class="mb-12 animate-fade-in delay-5">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">3. How We Use Your Information</h2>
            <p class="mb-4 leading-relaxed">We use the collected data to:</p>
            <ul class="list-disc list-inside mb-8 space-y-1">
                <li>Provide and maintain our service</li>
                <li>Enhance and personalize your user experience</li>
                <li>Manage your YouTube account activities through our app</li>
                <li>Generate detailed analytics and reports</li>
                <li>Communicate with you regarding updates, support, and offers</li>
            </ul>
        </section>

        <section class="mb-12 animate-fade-in delay-1">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">4. Data Sharing and Disclosure</h2>
            <p class="mb-4 leading-relaxed">We do not share your personal data with third parties except in the following circumstances:</p>
            <ul class="list-disc list-inside mb-8 space-y-1">
                <li>With your explicit consent</li>
                <li>To comply with legal obligations</li>
                <li>To protect and defend our rights and property</li>
                <li>To prevent or investigate possible wrongdoing in connection with the service</li>
            </ul>
        </section>

        <section class="mb-12 animate-fade-in delay-2">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">5. Data Security</h2>
            <p class="mb-4 leading-relaxed">
                We implement a variety of security measures to maintain the safety of your personal information. These include:
            </p>
            <ul class="list-disc list-inside mb-4 space-y-1">
                <li>Encryption of data in transit and at rest</li>
                <li>Access controls to restrict data access to authorized personnel</li>
                <li>Regular security audits and assessments</li>
            </ul>
            <p class="mb-8 leading-relaxed">
                However, no method of transmission over the Internet or electronic storage is 100% secure,
                and we cannot guarantee absolute security.
            </p>
        </section>

        <section class="mb-12 animate-fade-in delay-3">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">6. User Rights</h2>
            <p class="mb-4 leading-relaxed">
                Depending on your location, you may have the following rights regarding your personal data:
            </p>
            <ul class="list-disc list-inside mb-8 space-y-1">
                <li><span class="font-semibold">Right to access</span> – You can request copies of your personal data.</li>
                <li><span class="font-semibold">Right to rectification</span> – You can request correction of any information you believe is inaccurate.</li>
                <li><span class="font-semibold">Right to erasure</span> – You can request deletion of your personal data under certain conditions.</li>
                <li><span class="font-semibold">Right to restrict processing</span> – You can request that we limit the processing of your personal data.</li>
                <li><span class="font-semibold">Right to object to processing</span> – You can object to our processing of your personal data.</li>
                <li><span class="font-semibold">Right to data portability</span> – You can request transfer of your data to another organization.</li>
            </ul>
            <p class="mb-8 leading-relaxed">
                To exercise these rights, please contact us using the information provided below.
            </p>
        </section>

        <section class="mb-12 animate-fade-in delay-4">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">7. Changes to This Privacy Policy</h2>
            <p class="mb-8 leading-relaxed">
                We may update our Privacy Policy from time to time. We will notify you of any changes by
                posting the new Privacy Policy on this page. Changes are effective immediately upon posting.
            </p>
        </section>

        <section class="mb-12 animate-fade-in delay-5">
            <h2 class="text-3xl font-semibold mb-4 border-b border-gray-700 pb-2">8. Contact Us</h2>
            <p class="mb-4 leading-relaxed">If you have any questions about this Privacy Policy, please contact us:</p>
            <ul class="list-none space-y-2 mb-8">
                <li>
                    By email:
                    <a href="mailto:info@bareshaoffice.com" class="text-blue-400 hover:text-blue-300 underline transition-colors duration-300">
                        info@bareshaoffice.com
                    </a>
                </li>
                <li>
                    By visiting this page on our website:
                    <a href="https://www.panel.bareshaoffice.com/privacy_policy.php"
                        class="text-blue-400 hover:text-blue-300 underline transition-colors duration-300">
                        https://www.panel.bareshaoffice.com/privacy_policy.php
                    </a>
                </li>
            </ul>
        </section>
    </div>
    <footer class="text-center py-6 border-t border-gray-700 mt-10 animate-fade-in delay-1">
        <p class="text-sm text-gray-400">&copy; 2024 Baresha. All rights reserved.</p>
    </footer>

    <!-- Optional JavaScript for Animation Trigger -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const elements = document.querySelectorAll('.animate-fade-in');
            elements.forEach((el) => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'opacity 1s ease-out, transform 1s ease-out';
                    el.style.opacity = 1;
                    el.style.transform = 'translateY(0)';
                }, 100);
            });
        });
    </script>
</body>

</html>