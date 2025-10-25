<template>
    <DefaultLayout>
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 py-20">
            <div class="container mx-auto px-4 text-center text-white">
                <h1 class="text-5xl font-bold mb-4">{{ $t('privacy.title') }}</h1>
                <p class="text-xl opacity-90">{{ $t('privacy.last_updated') }}</p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="prose prose-lg max-w-none">
                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.introduction') }}</h2>
                        <p class="text-gray-600 mb-4">
                            Fursa Energy ("we," "our," or "us") is committed to protecting your privacy. This Privacy
                            Policy explains
                            how we collect, use, disclose, and safeguard your information when you visit our website or
                            use our services.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.information_we_collect') }}</h2>
                        <h3 class="text-xl font-semibold mb-3 text-gray-700">{{ $t('privacy.sections.personal_information') }}</h3>
                        <ul class="list-disc ml-6 text-gray-600 space-y-2 mb-4">
                            <li>Name and contact information (email, phone number, address)</li>
                            <li>Business information (company name, tax ID, business address)</li>
                            <li>Payment information (processed securely through payment providers)</li>
                            <li>Account credentials</li>
                        </ul>

                        <h3 class="text-xl font-semibold mb-3 text-gray-700">{{ $t('privacy.sections.auto_information') }}</h3>
                        <ul class="list-disc ml-6 text-gray-600 space-y-2">
                            <li>IP address and browser information</li>
                            <li>Device information</li>
                            <li>Usage data and analytics</li>
                            <li>Cookies and similar technologies</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.how_we_use') }}</h2>
                        <ul class="list-disc ml-6 text-gray-600 space-y-2">
                            <li>Process and fulfill your orders</li>
                            <li>Communicate with you about products and services</li>
                            <li>Send promotional materials (with your consent)</li>
                            <li>Improve our website and services</li>
                            <li>Comply with legal obligations</li>
                            <li>Prevent fraud and enhance security</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.sharing') }}</h2>
                        <p class="text-gray-600 mb-4">We do not sell or rent your personal information. We may share
                            your information with:</p>
                        <ul class="list-disc ml-6 text-gray-600 space-y-2">
                            <li>Service providers who assist in our operations</li>
                            <li>Payment processors for transaction processing</li>
                            <li>Delivery partners for order fulfillment</li>
                            <li>Legal authorities when required by law</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.security') }}</h2>
                        <p class="text-gray-600 mb-4">
                            We implement appropriate technical and organizational measures to protect your personal
                            information against
                            unauthorized access, alteration, disclosure, or destruction. However, no internet
                            transmission is completely
                            secure, and we cannot guarantee absolute security.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.rights') }}</h2>
                        <p class="text-gray-600 mb-4">You have the right to:</p>
                        <ul class="list-disc ml-6 text-gray-600 space-y-2">
                            <li>Access your personal information</li>
                            <li>Correct inaccurate information</li>
                            <li>Request deletion of your information</li>
                            <li>Opt-out of marketing communications</li>
                            <li>Data portability</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.cookies') }}</h2>
                        <p class="text-gray-600 mb-4">
                            We use cookies and similar tracking technologies to enhance your experience on our website.
                            You can control
                            cookies through your browser settings. Disabling cookies may affect website functionality.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.children') }}</h2>
                        <p class="text-gray-600 mb-4">
                            Our services are not directed to individuals under 18 years of age. We do not knowingly
                            collect personal
                            information from children.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.changes') }}</h2>
                        <p class="text-gray-600 mb-4">
                            We may update this Privacy Policy from time to time. We will notify you of any changes by
                            posting the new
                            Privacy Policy on this page and updating the "Last Updated" date.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4 text-gray-800">{{ $t('privacy.sections.contact') }}</h2>
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <p class="text-gray-600 mb-2">
                                {{ $t('privacy.sections.contact_intro') }}
                            </p>
                            <p class="text-gray-600 mb-2">
                                <strong>Email:</strong> privacy@fursaenergy.com
                            </p>
                            <p class="text-gray-600 mb-2">
                                <strong>Phone:</strong> +234-XXX-XXX-XXXX
                            </p>
                            <p class="text-gray-600">
                                <strong>Address:</strong> 4, Morija Close, Wuse 2, Abuja, Nigeria
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
</script>