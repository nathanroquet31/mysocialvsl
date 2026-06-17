<template>
  <svg :width="size" :height="size" viewBox="0 0 100 100" fill="none"
       xmlns="http://www.w3.org/2000/svg" style="display:block;flex-shrink:0">
    <defs>
      <linearGradient :id="gradId" x1="12" y1="8" x2="86" y2="92" gradientUnits="userSpaceOnUse">
        <stop stop-color="#5468FF" />
        <stop offset="1" stop-color="#8B5CF6" />
      </linearGradient>
    </defs>
    <!-- Rounded play triangle (rounded via a thick round-joined stroke of the same gradient) -->
    <path d="M34 27 L34 73 L73 50 Z"
          :fill="`url(#${gradId})`"
          :stroke="`url(#${gradId})`"
          stroke-width="21" stroke-linejoin="round" stroke-linecap="round" />
  </svg>
</template>

<script setup>
import { useId } from 'vue'

defineProps({
  size: { type: [Number, String], default: 32 },
})

// Unique gradient id per instance so multiple logos on a page don't collide.
const gradId = 'logomark-' + useId()
</script>
