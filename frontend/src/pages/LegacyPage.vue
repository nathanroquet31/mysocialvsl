<template>
  <DashboardLayout title="Legacy">

    <div :style="{background:theme.dark?'rgba(245,158,11,0.07)':'#FFFBEB',border:`1px solid ${theme.dark?'rgba(245,158,11,0.25)':'#FDE68A'}`,borderRadius:'12px',padding:'20px 24px',marginBottom:'20px'}">
      <p style="font-size:14px;font-weight:700;color:#D97706;margin:0 0 6px"><i class="bi bi-exclamation-triangle"></i> Legacy API key — deprecated</p>
      <p :style="{fontSize:'13px',color:txMuted,margin:0,lineHeight:1.65}">
        The single legacy API key has been replaced by the new <strong>v3 API keys</strong>: revocable, scoped, and shown only once at creation.
        Existing integrations should migrate to a v3 key.
      </p>
    </div>

    <div :style="card">
      <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 14px'}">Migration guide</p>
      <ol :style="{fontSize:'13px',color:txMuted,lineHeight:2,margin:0,paddingLeft:'20px'}">
        <li>Go to <RouterLink to="/dashboard/api" style="color:#6D4EE8">API Access</RouterLink> and mint a new v3 key.</li>
        <li>Copy the key — it's only shown once.</li>
        <li>Replace your old key in your integration: <code :style="codeStyle">Authorization: Bearer &lt;v3-key&gt;</code></li>
        <li>Switch your endpoints to <code :style="codeStyle">/api/v3/…</code> (see the reference on the API Access page).</li>
        <li>Your legacy key will stop working when the legacy API is sunset.</li>
      </ol>
    </div>

    <div :style="card" style="margin-top:16px">
      <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 6px'}">Deprecated features</p>
      <p :style="{fontSize:'12px',color:txMuted,margin:'0 0 14px'}">Older features kept for backwards compatibility.</p>
      <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',padding:'12px 0',borderTop:`1px solid ${bd}`}">
        <div>
          <p :style="{fontSize:'13px',fontWeight:600,color:tx,margin:0}">Legacy API (v1/v2)</p>
          <p :style="{fontSize:'11px',color:txMuted,margin:'2px 0 0'}">Replaced by REST API v3 with Bearer tokens</p>
        </div>
        <span style="background:#FEF2F2;color:#DC2626;border-radius:999px;padding:2px 10px;font-size:11px;font-weight:600">Deprecated</span>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()
const tx      = computed(() => theme.dark ? '#fff' : '#111827')
const txMuted = computed(() => theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280')
const bd      = computed(() => theme.dark ? 'rgba(255,255,255,0.07)' : '#F3F4F6')
const card    = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.04)' : '#fff', border:`1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`, borderRadius:'12px', padding:'22px' }))
const codeStyle = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.08)' : '#F3F4F6', borderRadius:'5px', padding:'2px 7px', fontSize:'11px', color: tx.value }))
</script>
