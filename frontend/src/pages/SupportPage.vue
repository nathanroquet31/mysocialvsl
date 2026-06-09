<template>
  <DashboardLayout title="Support">

    <div style="max-width:640px">
      <h2 :style="{fontSize:'22px',fontWeight:700,color:text,margin:'0 0 6px'}">Need help?</h2>
      <p :style="{fontSize:'14px',color:sub,margin:'0 0 32px'}">We're here to help. Choose how you'd like to get in touch.</p>

      <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Documentation -->
        <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'20px 24px',display:'flex',alignItems:'center',gap:'16px'}">
          <div style="width:44px;height:44px;background:#EEE9FF;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-book" style="font-size:20px;color:#6D4EE8"></i>
          </div>
          <div style="flex:1">
            <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:'0 0 3px'}">Documentation</p>
            <p :style="{fontSize:'13px',color:sub,margin:0}">Guides, tutorials and API reference.</p>
          </div>
          <span :style="{background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',color:muted,border:`1px solid ${border}`,borderRadius:'999px',padding:'2px 10px',fontSize:'11px',fontWeight:600,flexShrink:0}">Coming Soon</span>
        </div>

        <!-- Telegram Community -->
        <a href="https://t.me/mysocialvsl" target="_blank" rel="noopener"
          :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'20px 24px',display:'flex',alignItems:'center',gap:'16px',textDecoration:'none',transition:'border-color 0.15s,box-shadow 0.15s'}"
          @mouseenter="e => { e.currentTarget.style.borderColor='#6D4EE8'; e.currentTarget.style.boxShadow='0 0 0 3px rgba(109,78,232,0.08)' }"
          @mouseleave="e => { e.currentTarget.style.borderColor=theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'; e.currentTarget.style.boxShadow='none' }">
          <div style="width:44px;height:44px;background:#E0F2FE;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-telegram" style="font-size:20px;color:#0284C7"></i>
          </div>
          <div style="flex:1">
            <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:'0 0 3px'}">Telegram Community</p>
            <p :style="{fontSize:'13px',color:sub,margin:0}">Join our creator community on Telegram.</p>
          </div>
          <i class="bi bi-box-arrow-up-right" :style="{fontSize:'15px',color:muted}"></i>
        </a>

        <!-- Email Support -->
        <a href="mailto:support@mysocialvsl.com"
          :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'20px 24px',display:'flex',alignItems:'center',gap:'16px',textDecoration:'none',transition:'border-color 0.15s,box-shadow 0.15s'}"
          @mouseenter="e => { e.currentTarget.style.borderColor='#6D4EE8'; e.currentTarget.style.boxShadow='0 0 0 3px rgba(109,78,232,0.08)' }"
          @mouseleave="e => { e.currentTarget.style.borderColor=theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'; e.currentTarget.style.boxShadow='none' }">
          <div style="width:44px;height:44px;background:#F0FDF4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-envelope" style="font-size:20px;color:#16A34A"></i>
          </div>
          <div style="flex:1">
            <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:'0 0 3px'}">Email Support</p>
            <p :style="{fontSize:'13px',color:sub,margin:0}">support@mysocialvsl.com — We reply within 24h.</p>
          </div>
          <i class="bi bi-arrow-right" :style="{fontSize:'15px',color:muted}"></i>
        </a>

      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()
const card   = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#fff')
const border = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const text   = computed(() => theme.dark ? '#fff' : '#111827')
const sub    = computed(() => theme.dark ? 'rgba(255,255,255,0.55)' : '#6B7280')
const muted  = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
</script>
