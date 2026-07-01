<template>
  <div
    :style="{
      display:'flex', width:'56px', height:'28px', padding:'3px',
      borderRadius:'999px', cursor:'pointer', transition:'background 0.3s, border-color 0.3s',
      background: theme.dark ? '#09071a' : '#fff',
      border: theme.dark ? '1px solid rgba(255,255,255,0.12)' : '1px solid #E5E7EB',
      boxSizing:'border-box', alignItems:'center', justifyContent:'space-between',
      flexShrink: 0,
    }"
    @click.stop="theme.toggle()"
    role="button"
    tabindex="0"
    @keydown.enter.stop="theme.toggle()"
    @keydown.space.stop.prevent="theme.toggle()"
  >
    <!-- Moving thumb with active icon -->
    <div :style="{
      width:'22px', height:'22px', borderRadius:'50%',
      display:'flex', alignItems:'center', justifyContent:'center',
      background: theme.dark ? 'rgba(255,255,255,0.12)' : '#E5E7EB',
      transition:'transform 0.3s ease, background 0.3s',
      transform: theme.dark ? 'translateX(0px)' : 'translateX(28px)',
      flexShrink: 0, pointerEvents: 'none',
    }">
      <i
        :class="theme.dark ? 'bi bi-moon-fill' : 'bi bi-sun-fill'"
        :style="{fontSize:'11px', color: theme.dark ? '#fff' : '#374151'}"
      ></i>
    </div>

    <!-- Passive icon (opposite side) -->
    <div :style="{
      width:'22px', height:'22px', borderRadius:'50%',
      display:'flex', alignItems:'center', justifyContent:'center',
      transition:'transform 0.3s ease',
      transform: theme.dark ? 'translateX(0px)' : 'translateX(-28px)',
      flexShrink: 0, pointerEvents: 'none',
    }">
      <i
        :class="theme.dark ? 'bi bi-sun-fill' : 'bi bi-moon-fill'"
        :style="{fontSize:'11px', color: theme.dark ? 'rgba(255,255,255,0.3)' : '#9CA3AF'}"
      ></i>
    </div>
  </div>
</template>

<script setup>
import { useThemeStore } from '@/stores/theme'
const theme = useThemeStore()
</script>
