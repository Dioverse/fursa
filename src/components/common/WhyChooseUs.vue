<template>
    <!-- Why Choose Us -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-6">{{ $t('home.why_choose.title') }}</h2>
            <p class="text-center mb-12">{{ $t('home.why_choose.subtitle') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="feature in features" :key="feature.title" class="text-center">
                    <div
                        class="w-20 h-20 mx-auto mb-4 bg-primary bg-opacity-10 rounded-full flex items-center justify-center">
                        <font-awesome-icon :icon="feature.icon" size="2x" class="text-primary" />
                    </div>
                    <h3 class="font-semibold mb-2">{{ feature.title }}</h3>
                    <p class="text-gray-600 text-sm">{{ feature.description }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { computed } from 'vue'
    import { useI18n } from 'vue-i18n'
    import { faBuilding, faCertificate, faTruck, faHeadset } from '@fortawesome/free-solid-svg-icons'

    const { t } = useI18n()

    const features = computed(() => ([
        {
            icon: faBuilding,
            title: t('home.why_choose.items.facility.title'),
            description: t('home.why_choose.items.facility.desc')
        },
        {
            icon: faCertificate,
            title: t('home.why_choose.items.quality.title'),
            description: t('home.why_choose.items.quality.desc')
        },
        {
            icon: faTruck,
            title: t('home.why_choose.items.infrastructure.title'),
            description: t('home.why_choose.items.infrastructure.desc')
        },
        {
            icon: faHeadset,
            title: t('home.why_choose.items.support.title'),
            description: t('home.why_choose.items.support.desc')
        }
    ]))
</script>