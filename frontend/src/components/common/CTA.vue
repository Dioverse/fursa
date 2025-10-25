<template>
    <section class="py-16 bg-primary text-white" aria-label="Site trust and support callouts">
        <div class="container mx-auto px-2 sm:px-2 md:px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                <!-- Secure Payments -->
                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in" :aria-label="$t('cta.secure_payment.title')">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon :icon="faWallet" size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">{{ $t('cta.secure_payment.title') }}</h4>
                    <p class="text-sm opacity-90">{{ $t('cta.secure_payment.desc') }}</p>
                </div>

                <!-- Reviews -->
                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in" :aria-label="$t('cta.reviews.title')">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon icon="star" size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">{{ $t('cta.reviews.title') }}</h4>
                    <p class="text-sm opacity-90">{{ $t('cta.reviews.desc') }}</p>
                </div>

                <!-- Support -->
                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in" :aria-label="$t('cta.support.title')">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon :icon="faHeadset" size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">{{ $t('cta.support.title') }}</h4>
                    <p class="text-sm opacity-90">{{ $t('cta.support.desc') }}</p>
                </div>

                <!-- Returns -->
                <div class="text-center shadow-lg px-1 md:px-2 py-3 hover:scale-[1.03] rounded-lg transition-opacity duration-500 ease-in fade-in" :aria-label="$t('cta.returns.title')">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon :icon="faUndo" size="2x" />
                    </div>
                    <h4 class="font-semibold mb-2">{{ $t('cta.returns.title') }}</h4>
                    <p class="text-sm opacity-90">{{ $t('cta.returns.desc') }}</p>
                </div>
            </div>
        </div>
    </section>
  
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { faWallet, faHeadset, faUndo } from '@fortawesome/free-solid-svg-icons'

// setup i18n (no local variables required here, but ensures reactivity on locale change)
useI18n()
</script>
