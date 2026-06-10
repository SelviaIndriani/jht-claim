<template>
  <div>
    <div
      class="relative border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all duration-200"
      :class="{
        'border-bpjs-400 bg-bpjs-50': isDragging,
        'border-red-400 bg-red-50': error,
        'border-gray-300 bg-gray-50 hover:border-bpjs-400 hover:bg-bpjs-50': !isDragging && !error && !preview,
        'border-bpjs-300 bg-bpjs-50': preview && !error,
      }"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="onDrop"
      @click="triggerInput"
    >
      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/jpg,image/png"
        class="hidden"
        @change="onFileSelect"
      />

      <!-- Preview state -->
      <div v-if="preview" class="relative">
        <img
          :src="preview"
          :alt="label"
          class="max-h-40 mx-auto rounded-lg object-cover shadow-sm"
        />
        <button
          type="button"
          class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-md"
          @click.stop="removeFile"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <p class="mt-2 text-xs text-gray-500 truncate max-w-full">{{ fileName }}</p>
      </div>

      <!-- Empty state -->
      <div v-else>
        <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center" :class="error ? 'bg-red-100' : 'bg-gray-100'">
          <svg class="w-6 h-6" :class="error ? 'text-red-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <p class="text-sm font-medium text-gray-600">{{ label }}</p>
        <p class="text-xs text-gray-400 mt-1">Klik atau drag & drop foto ke sini</p>
        <p class="text-xs text-gray-400">JPG, PNG • Maks. 2MB</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { validateFile } from '~/utils/validators'

const props = defineProps({
  label: { type: String, default: 'Upload Foto' },
  error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue', 'error'])

const fileInput = ref(null)
const preview = ref(null)
const fileName = ref('')
const isDragging = ref(false)

function triggerInput() {
  fileInput.value?.click()
}

function onFileSelect(event) {
  const file = event.target.files[0]
  if (file) processFile(file)
}

function onDrop(event) {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  if (file) processFile(file)
}

function processFile(file) {
  const validation = validateFile(file, props.label)
  if (!validation.valid) {
    emit('error', validation.message)
    return
  }

  fileName.value = file.name
  const reader = new FileReader()
  reader.onload = (e) => { preview.value = e.target.result }
  reader.readAsDataURL(file)
  emit('update:modelValue', file)
  emit('error', '')
}

function removeFile() {
  preview.value = null
  fileName.value = ''
  if (fileInput.value) fileInput.value.value = ''
  emit('update:modelValue', null)
}
</script>
