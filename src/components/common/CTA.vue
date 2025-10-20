<template>
    <section class="py-16 bg-primary text-white">
        <div class="container mx-auto px-2 sm:px-2 md:px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in">
                    <div
                        class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon :icon=faWallet size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">Secure Payment Gateways</h4>
                    <p class="text-sm opacity-90">48+ gateways to ensure your security</p>
                </div>

                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in">
                    <div
                        class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon icon="star" size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">Genuine Customer Reviews</h4>
                    <p class="text-sm opacity-90">Find verified reviews showcased on our platforms</p>
                </div>

                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in">
                    <div
                        class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon :icon="faHeadset" size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">24/7 Customer Support</h4>
                    <p class="text-sm opacity-90">Always our support team is available for you</p>
                </div>

                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in">
                    <div
                        class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon :icon="faUndo" size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">Easy Return Policy</h4>
                    <p class="text-sm opacity-90">If you're not satisfied, return it hassle-free</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import {
        faWallet, faShieldAlt, faStar, faHeadset, faUndo, faDownload
    } from '@fortawesome/free-solid-svg-icons'
</script>
