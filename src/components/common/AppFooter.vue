<template>
    <footer class="bg-[#201E1E] text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <h4 class="text-xl font-bold mb-4">{{ $t('footer.company') || 'Fursa Energy' }}</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <font-awesome-icon icon="envelope" class="mt-1" />
                            <span>{{ $t('footer.contact.email') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <font-awesome-icon icon="phone" class="mt-1" />
                            <span>{{ $t('footer.contact.phone') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <font-awesome-icon icon="map-marker-alt" class="mt-1" />
                            <div>
                                <p>{{ $t('footer.contact.address_line_1') }}</p>
                                <p>{{ $t('footer.contact.address_line_2') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-xl font-bold mb-4">{{ $t('footer.quick_links') }}</h4>
                    <ul class="space-y-2">
                        <li>
                            <RouterLink to="/" class="hover:text-primary transition">{{ $t('footer.home') }}</RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/about" class="hover:text-primary transition">{{ $t('footer.about') }}</RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/contact" class="hover:text-primary transition">{{ $t('footer.contact_us') }}</RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/shop" class="hover:text-primary transition">{{ $t('footer.shop') }}</RouterLink>
                        </li>
                    </ul>
                </div>

                <!-- Help -->
                <div>
                    <h4 class="text-xl font-bold mb-4">{{ $t('footer.help') }}</h4>
                    <ul class="space-y-2">
                        <li>
                            <RouterLink to="/faq" class="hover:text-primary transition">{{ $t('footer.faq') }}</RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/terms" class="hover:text-primary transition">{{ $t('footer.terms') }}</RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/privacy" class="hover:text-primary transition">{{ $t('footer.privacy') }}</RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/refund" class="hover:text-primary transition">{{ $t('footer.refund') }}</RouterLink>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-xl font-bold mb-4">{{ $t('footer.newsletter') }}</h4>
                    <p class="mb-4">{{ $t('footer.newsletter_cta') }}</p>
                    <form @submit.prevent="subscribeNewsletter" class="space-y-3">
                        <div class="relative">
                            <input v-model="email" type="email" :placeholder="$t('footer.email_placeholder')"
                                class="w-full px-4 py-2 pr-10 rounded text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                            <button type="submit"
                                class="absolute right-1 top-1/2 -translate-y-1/2 bg-primary text-white px-3 py-1 rounded">
                                <font-awesome-icon icon="arrow-right" />
                            </button>
                        </div>
                    </form>

                    <!-- Social Links -->
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="hover:text-primary transition">
                            <font-awesome-icon :icon="['fab', 'facebook']" size="lg" />
                        </a>
                        <a href="#" class="hover:text-primary transition">
                            <font-awesome-icon :icon="['fab', 'twitter']" size="lg" />
                        </a>
                        <a href="#" class="hover:text-primary transition">
                            <font-awesome-icon :icon="['fab', 'instagram']" size="lg" />
                        </a>
                        <a href="#" class="hover:text-primary transition">
                            <font-awesome-icon :icon="['fab', 'linkedin']" size="lg" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-600 pt-6 text-center">
                <p>&copy; 2025 {{ $t('footer.company') || 'Fursa Energy' }}. {{ $t('footer.copyright') }}</p>
            </div>
        </div>
        <DashboardSidebar :showDesktop="false"/>
    </footer>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import DashboardSidebar from '../dashboard/DashboardSidebar.vue'
import { useI18n } from 'vue-i18n'

const toast = useToast()
const email = ref('')
const { t } = useI18n()

const subscribeNewsletter = () => {
    toast.success(t('footer.subscribe_success'))
    email.value = ''
}
</script>