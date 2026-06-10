<template>
  <div class="w-full py-4">
    <div class="flex items-center justify-center">
      <template v-for="(step, index) in steps" :key="step.id">
        <!-- Step circle -->
        <div class="flex flex-col items-center">
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300"
            :class="{
              'bg-bpjs-500 text-white shadow-md shadow-bpjs-200': currentStep > step.id,
              'bg-bpjs-500 text-white ring-4 ring-bpjs-100 shadow-md': currentStep === step.id,
              'bg-gray-200 text-gray-400': currentStep < step.id,
            }"
          >
            <!-- Checkmark for completed steps -->
            <svg v-if="currentStep > step.id" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <span v-else>{{ step.id }}</span>
          </div>
          <span
            class="mt-2 text-xs font-medium text-center max-w-[80px] leading-tight"
            :class="{
              'text-bpjs-600': currentStep >= step.id,
              'text-gray-400': currentStep < step.id,
            }"
          >
            {{ step.label }}
          </span>
        </div>

        <!-- Connector line -->
        <div
          v-if="index < steps.length - 1"
          class="flex-1 h-0.5 mx-2 mb-5 transition-all duration-300"
          :class="currentStep > step.id ? 'bg-bpjs-500' : 'bg-gray-200'"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
defineProps({
  steps: {
    type: Array,
    required: true,
  },
  currentStep: {
    type: Number,
    required: true,
  },
})
</script>
