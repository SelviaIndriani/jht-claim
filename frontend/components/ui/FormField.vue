<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="form-label">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>
    <slot />
    <p v-if="error" class="form-error">
      <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      {{ error }}
    </p>
    <p v-if="hint && !error" class="text-xs text-gray-400 mt-1">{{ hint }}</p>
  </div>
</template>

<script setup>
defineProps({
  id: String,
  label: String,
  error: String,
  hint: String,
  required: Boolean,
})
</script>
