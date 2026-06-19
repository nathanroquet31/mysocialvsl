<template>
  <RouterLink :to="href"
    :style="{
      display:'flex', alignItems:'center', gap:'10px',
      padding: collapsed ? '10px' : '8px 10px',
      borderRadius:'8px',
      fontSize:'13px', fontWeight: active ? '600' : '500',
      textDecoration:'none',
      color: active ? '#6D4EE8' : (hovered ? (dark ? 'rgba(255,255,255,0.9)' : '#111827') : (dark ? 'rgba(255,255,255,0.6)' : '#4B5563')),
      background: active ? '#EEE9FF' : (hovered ? (dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6') : 'transparent'),
      marginBottom:'2px',
      transition:'all 0.12s',
      justifyContent: collapsed ? 'center' : 'flex-start',
      position:'relative',
    }"
    @mouseenter="hovered=true" @mouseleave="hovered=false"
  >
    <span :style="{flexShrink:0, color: active ? '#6D4EE8' : (hovered ? (dark ? 'rgba(255,255,255,0.8)' : '#374151') : (dark ? 'rgba(255,255,255,0.4)' : '#6B7280')), display:'flex', alignItems:'center', fontSize:'16px', lineHeight:1}">
      <i v-if="icon==='dashboard'" class="bi bi-bar-chart-line"></i>
      <i v-else-if="icon==='links'" class="bi bi-link-45deg"></i>
      <i v-else-if="icon==='analytics'" class="bi bi-graph-up"></i>
      <i v-else-if="icon==='profile'"   class="bi bi-person"></i>
      <i v-else-if="icon==='billing'"   class="bi bi-credit-card"></i>
      <i v-else-if="icon==='domains'"   class="bi bi-globe"></i>
      <i v-else-if="icon==='settings'"  class="bi bi-gear"></i>
      <i v-else-if="icon==='support'"   class="bi bi-chat-dots"></i>
      <i v-else-if="icon==='guide'"     class="bi bi-shield-fill-check"></i>
      <i v-else-if="icon==='api'"       class="bi bi-code-slash"></i>
      <i v-else-if="icon==='affiliates'" class="bi bi-people"></i>
      <i v-else-if="icon==='help'"      class="bi bi-question-circle"></i>
      <i v-else-if="icon==='legacy'"    class="bi bi-archive"></i>
      <i v-else-if="icon==='admin'"     class="bi bi-shield-lock-fill"></i>
      <i v-else                         class="bi bi-circle"></i>
    </span>

    <span v-if="!collapsed">{{ label }}</span>

    <!-- Tooltip on collapsed -->
    <div v-if="collapsed && hovered"
      style="position:absolute;left:56px;background:#111827;color:#fff;font-size:12px;font-weight:500;padding:4px 10px;border-radius:6px;white-space:nowrap;pointer-events:none;z-index:50">
      {{ label }}
    </div>
  </RouterLink>
</template>

<script setup>
import { ref } from 'vue'
defineProps({
  href:      { type: String, required: true },
  label:     { type: String, required: true },
  icon:      { type: String, default: 'default' },
  active:    { type: Boolean, default: false },
  collapsed: { type: Boolean, default: false },
  dark:      { type: Boolean, default: false },
})
const hovered = ref(false)
</script>
