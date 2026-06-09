<template>
  <div :style="{
    background: accent ? '#6D4EE8' : (theme.dark ? 'rgba(255,255,255,0.04)' : '#fff'),
    border: accent ? 'none' : `1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`,
    borderRadius:'12px',
    padding:'18px 20px',
    transition:'box-shadow 0.15s, transform 0.15s',
    cursor:'default',
  }"
    @mouseenter="e => { e.currentTarget.style.boxShadow='0 4px 20px rgba(0,0,0,0.1)'; e.currentTarget.style.transform='translateY(-1px)' }"
    @mouseleave="e => { e.currentTarget.style.boxShadow='none'; e.currentTarget.style.transform='translateY(0)' }"
  >
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px">
      <p :style="{fontSize:'11px',fontWeight:500,textTransform:'uppercase',letterSpacing:'0.07em',color: accent ? 'rgba(255,255,255,0.65)' : (theme.dark ? 'rgba(255,255,255,0.4)' : '#9CA3AF')}">
        {{ label }}
      </p>
      <span v-if="trend" :style="{fontSize:'10px',fontWeight:600,color: accent ? 'rgba(255,255,255,0.7)' : '#6D4EE8',background: accent ? 'rgba(255,255,255,0.12)' : '#EEE9FF',borderRadius:'999px',padding:'2px 7px'}">
        {{ trend }}
      </span>
    </div>
    <p :style="{fontSize:'26px',fontWeight:700,letterSpacing:'-0.03em',color: accent ? '#fff' : (theme.dark ? '#fff' : '#111827')}">
      {{ value }}
    </p>
  </div>
</template>

<script setup>
import { useThemeStore } from '@/stores/theme'
const theme = useThemeStore()
defineProps({
  label:  { type: String, required: true },
  value:  { type: [String, Number], default: '—' },
  trend:  { type: String, default: '' },
  accent: { type: Boolean, default: false },
})
</script>
