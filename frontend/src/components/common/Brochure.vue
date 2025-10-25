<template>
    <section class="py-16 bg-white" aria-labelledby="brochure-title">
        <div class="container mx-auto px-4 text-center">
            <h2 id="brochure-title" class="lg:text-2xl md:text-xl text-lg font-bold mb-2">{{ $t('brochure.title') }}</h2>
            <a
                :href="brochureUrl"
                target="_blank"
                rel="noopener noreferrer"
                download
                class="inline-flex items-center gap-2 bg-primary text-white mt-6 px-5 py-2 rounded hover:bg-primary/90 transition"
                :aria-label="$t('brochure.download_aria')"
                @click="onDownloadClick"
            >
                <font-awesome-icon :icon="faDownload" />
                <span>{{ $t('brochure.download_label') }}</span>
            </a>
            <p class="mt-3 text-sm text-gray-600" v-if="!hasCustomUrl">
                {{ $t('brochure.note_default_path') }}
            </p>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { faDownload } from '@fortawesome/free-solid-svg-icons'

useI18n()

const envUrl = import.meta.env.VITE_BROCHURE_URL
const hasCustomUrl = computed(() => !!envUrl)
const brochureUrl = computed(() => envUrl || '/brochure.pdf')

const onDownloadClick = () => {
    // Lightweight tracking hook; replace with analytics if available
    try {
        // eslint-disable-next-line no-console
        console.log('brochure_download_clicked', { url: brochureUrl.value })
    } catch (e) {}
}
</script>