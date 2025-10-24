<template>
  <div :style="{ '--editor-min-h': minHeight }">
    <QuillEditor
      theme="snow"
      :toolbar="toolbar"
      v-model:content="inner"
      contentType="html"
      class="quill"
      ref="editor"
    />
  </div>

</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const props = defineProps({
  modelValue: { type: String, default: '' },
  minHeight: { type: String, default: '320px' },
  toolbar: {
    type: [Array, String, Boolean, Object],
    default: () => [
      ['bold', 'italic', 'underline', 'strike'],
      [{ header: 1 }, { header: 2 }],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ script: 'sub' }, { script: 'super' }],
      [{ indent: '-1' }, { indent: '+1' }],
      [{ direction: 'rtl' }],
      [{ size: ['small', false, 'large', 'huge'] }],
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ color: [] }, { background: [] }],
      [{ align: [] }],
      ['blockquote', 'code-block', 'link'],
      ['clean'],
    ],
  },
})

const emit = defineEmits(['update:modelValue'])
const inner = ref(props.modelValue)
const editor = ref(null)

const applyHtml = (html) => {
  try {
    if (editor.value && typeof editor.value.getQuill === 'function') {
      const q = editor.value.getQuill()
      q.clipboard.dangerouslyPasteHTML(html || '', 'silent')
    } else {
      inner.value = html || ''
    }
  } catch {
    inner.value = html || ''
  }
}

watch(() => props.modelValue, v => {
  if (v !== inner.value) {
    inner.value = v || ''
    // also push into the editor to avoid blank content on init
    applyHtml(v || '')
  }
})
watch(inner, v => emit('update:modelValue', v || ''))

onMounted(async () => {
  await nextTick()
  applyHtml(props.modelValue || '')
})
</script>

<style scoped>
.quill :deep(.ql-container) { min-height: var(--editor-min-h, 320px); }
</style>
