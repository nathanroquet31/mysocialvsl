<template>
  <DashboardLayout title="Affiliate Program">

    <div v-if="loading" :style="{fontSize:'13px',color:txMuted}">Loading…</div>

    <!-- Invite-only: only flagged affiliates see the dashboard. -->
    <div v-else-if="!data || !data.enrolled" :style="card" style="text-align:center;padding:48px 28px">
      <div :style="{width:'48px',height:'48px',borderRadius:'12px',background:'#EEE9FF',display:'flex',alignItems:'center',justifyContent:'center',margin:'0 auto 16px'}">
        <i class="bi bi-people" style="color:#6D4EE8;font-size:22px"></i>
      </div>
      <p :style="{fontSize:'15px',fontWeight:700,color:tx,margin:'0 0 6px'}">The affiliate program is invite-only</p>
      <p :style="{fontSize:'13px',color:txMuted,margin:0,lineHeight:1.65,maxWidth:'420px',marginInline:'auto'}">
        Earn 20% recurring on every customer you bring. Reach out to get your affiliate link.
      </p>
    </div>

    <template v-else>

      <!-- Hero: code + link -->
      <div :style="card" style="margin-bottom:20px">
        <p :style="{fontSize:'15px',fontWeight:700,color:tx,margin:'0 0 6px'}">Earn 20% recurring</p>
        <p :style="{fontSize:'13px',color:txMuted,margin:'0 0 16px',lineHeight:1.65}">
          Share your link. Every creator who subscribes through it earns you 20% of their plan, every month.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:4px">
          <span v-for="term in terms" :key="term" :style="termPill">{{ term }}</span>
        </div>
        <div style="display:grid;grid-template-columns:auto 1fr;gap:12px;align-items:center;max-width:640px">
          <span :style="{fontSize:'12px',fontWeight:600,color:txMuted}">Your code</span>
          <div style="display:flex;gap:8px;align-items:center">
            <code :style="codeBox">{{ data.affiliate_code }}</code>
            <button @click="copy(data.affiliate_code, 'code')" :style="btnGhost">{{ copiedWhat === 'code' ? '✓' : 'Copy' }}</button>
          </div>
          <span :style="{fontSize:'12px',fontWeight:600,color:txMuted}">Your link</span>
          <div style="display:flex;gap:8px;align-items:center;min-width:0">
            <code :style="codeBox" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ data.affiliate_link }}</code>
            <button @click="copy(data.affiliate_link, 'link')" :style="btnGhost">{{ copiedWhat === 'link' ? '✓' : 'Copy' }}</button>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:20px">
        <div :style="card">
          <p :style="statLabel">Referrals</p>
          <p :style="statValue">{{ data.referrals_count }}</p>
        </div>
        <div :style="card">
          <p :style="statLabel">Paying referrals</p>
          <p :style="statValue">{{ data.paying_referrals }}</p>
        </div>
        <div :style="card">
          <p :style="statLabel">Total earned</p>
          <p :style="statValue">${{ data.total_earned }}</p>
        </div>
        <div :style="card">
          <p :style="statLabel">Pending payout</p>
          <p :style="statValue">${{ data.pending_payout }}</p>
        </div>
      </div>

      <!-- Partners / Products -->
      <div :style="card">
        <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 6px'}">Partners & Products</p>
        <p :style="{fontSize:'12px',color:txMuted,margin:'0 0 16px'}">Tools and partner products for creators & agencies. More coming soon.</p>
        <div v-if="partners.length === 0" :style="{textAlign:'center',padding:'28px',color:txMuted,fontSize:'13px',border:`1px dashed ${bd}`,borderRadius:'10px'}">
          No partner products listed yet.
        </div>
        <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px">
          <a v-for="p in partners" :key="p.name" :href="p.url" target="_blank" rel="noopener"
            :style="{display:'flex',gap:'12px',alignItems:'center',padding:'14px',border:`1px solid ${bd}`,borderRadius:'10px',textDecoration:'none'}">
            <div :style="{width:'36px',height:'36px',borderRadius:'9px',background:'#EEE9FF',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
              <i :class="`bi bi-${p.icon}`" style="color:#6D4EE8;font-size:16px"></i>
            </div>
            <div>
              <p :style="{fontSize:'13px',fontWeight:600,color:tx,margin:0}">{{ p.name }}</p>
              <p :style="{fontSize:'11px',color:txMuted,margin:'2px 0 0'}">{{ p.desc }}</p>
            </div>
          </a>
        </div>
      </div>

    </template>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()
const data = ref(null)
const loading = ref(true)
const copiedWhat = ref('')

// Extensible — can be moved to the database/API later
const partners = ref([])

// The locked deal, shown up-front so affiliates know the rules at a glance.
const terms = ['20% recurring · lifetime', 'Paid on real sales only', '$50 minimum payout', 'Paid monthly · PayPal or crypto']
const termPill = computed(() => ({ fontSize:'11px', fontWeight:600, color: tx.value, background: theme.dark ? 'rgba(167,139,250,0.12)' : '#F1EDFF', border:`1px solid ${theme.dark ? 'rgba(167,139,250,0.25)' : '#E2D9FF'}`, borderRadius:'999px', padding:'5px 11px' }))

const tx      = computed(() => theme.dark ? '#fff' : '#111827')
const txMuted = computed(() => theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280')
const bd      = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const card    = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.04)' : '#fff', border:`1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`, borderRadius:'12px', padding:'22px' }))
const codeBox = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.06)' : '#F9FAFB', border:`1px solid ${bd.value}`, borderRadius:'8px', padding:'8px 14px', fontSize:'13px', color: tx.value, fontFamily:'ui-monospace,monospace', flex:1, minWidth:0 }))
const btnGhost = computed(() => ({ background:'none', border:`1px solid ${bd.value}`, borderRadius:'8px', padding:'8px 14px', fontSize:'12px', fontWeight:600, color: tx.value, cursor:'pointer', fontFamily:'Inter,sans-serif', flexShrink:0 }))
const statLabel = computed(() => ({ fontSize:'11px', fontWeight:600, color: txMuted.value, textTransform:'uppercase', letterSpacing:'0.08em', margin:'0 0 8px' }))
const statValue = computed(() => ({ fontSize:'26px', fontWeight:800, color: tx.value, margin:0, letterSpacing:'-0.02em' }))

function copy(text, what) {
  navigator.clipboard?.writeText(text)
  copiedWhat.value = what
  setTimeout(() => copiedWhat.value = '', 1800)
}

onMounted(async () => {
  try { data.value = (await api.get('/affiliate')).data } catch {}
  loading.value = false
})
</script>
