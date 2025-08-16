<template>
    <!-- Why Choose Us -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-6">
                Why Choose Fursa Energy as Your Trusted Lubricant Distributor
            </h2>
            <p class="text-center mb-12">
                Fursa Energy is more than a lubricant distributor, we are a trusted partner for businesses across Nigeria. Backed by MRS Lubricants, one of Africa’s leading blending plants, we deliver high-performance products that meet global standards.
            </p>
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
    import { faBuilding, faCertificate, faTruck, faHeadset } from '@fortawesome/free-solid-svg-icons'

    const features = [
        {
            icon: faBuilding,//'globe-africa' 
            title: "Backed by Africa's most advanced lubricant blending facility",
            description: 'State-of-the-art technology ensuring premium quality products'
        },
        {
            icon: faCertificate,
            title: 'Premium Quality Standards',
            description: 'All products meet international quality certifications'
        },
        {
            icon: faTruck,
            title: 'Strong Digital Infrastructure',
            description: 'Modern onboarding and competitive pricing strategies'
        },
        {
            icon: faHeadset,
            title: 'Integrated Support System',
            description: 'Complete marketing and after-sales support for partners'
        }
    ]
</script>