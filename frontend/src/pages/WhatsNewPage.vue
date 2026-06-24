<template>
  <DashboardLayout title="What's New">

    <div style="max-width:760px;margin:0 auto">
      <!-- Header -->
      <div style="text-align:center;margin-bottom:26px">
        <h1 :style="{fontSize:'28px',fontWeight:800,color:tx,margin:'0 0 8px',letterSpacing:'-0.02em'}">What's New</h1>
        <p :style="{fontSize:'14px',color:txMuted,margin:0}">Every feature, improvement and fix we ship — newest first.</p>
      </div>

      <!-- Filters -->
      <div style="display:flex;justify-content:center;margin-bottom:28px">
        <div :style="{display:'inline-flex',gap:'4px',padding:'4px',borderRadius:'999px',background: theme.dark ? 'rgba(255,255,255,0.05)' : '#F3F4F6',border:`1px solid ${bd}`}">
          <button v-for="f in filters" :key="f.key" @click="active = f.key"
            :style="tabStyle(f.key)">
            {{ f.label }} <span :style="{opacity:0.6,fontWeight:600}">{{ counts[f.key] }}</span>
          </button>
        </div>
      </div>

      <!-- Timeline -->
      <div style="position:relative">
        <div v-for="(e, i) in visible" :key="i"
          style="display:grid;grid-template-columns:64px 1fr;gap:16px;align-items:start">
          <!-- Left rail: date + dot -->
          <div style="text-align:right;padding-top:18px">
            <p :style="{fontSize:'12px',fontWeight:700,color:tx,margin:0,whiteSpace:'nowrap'}">{{ e.date }}</p>
          </div>
          <!-- Card with connecting line -->
          <div :style="{position:'relative',paddingLeft:'22px',paddingBottom:'16px',borderLeft:`1px solid ${bd}`}">
            <span :style="{position:'absolute',left:'-5px',top:'22px',width:'9px',height:'9px',borderRadius:'50%',background: typeColor(e.type),boxShadow:`0 0 0 3px ${theme.dark ? '#0b0a14' : '#fff'}`}"></span>
            <div :style="card">
              <span :style="badge(e.type)">{{ e.type.toUpperCase() }}</span>
              <p :style="{fontSize:'15px',fontWeight:700,color:tx,margin:'10px 0 6px'}">{{ e.title }}</p>
              <p :style="{fontSize:'13px',color:txMuted,margin:0,lineHeight:1.6}">{{ e.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()
const active = ref('all')

const filters = [
  { key: 'all',      label: 'All' },
  { key: 'new',      label: 'New' },
  { key: 'improved', label: 'Improved' },
  { key: 'fixed',    label: 'Fixed' },
]

// Edit this list to ship a changelog entry. Newest first.
const entries = [
  { date: 'Jun 23', type: 'new',      title: 'Real visitors, not raw hits', desc: 'Analytics now count unique visitors and unique clicks with a persistent visitor ID — so your numbers reflect real people, not repeated page loads.' },
  { date: 'Jun 23', type: 'improved', title: 'Per-second VSL retention + live viewers', desc: 'See exactly where viewers drop off second by second, who is watching right now, and the true average watch time of your VSL.' },
  { date: 'Jun 23', type: 'improved', title: 'Stronger iPhone escape from the Instagram browser', desc: 'Deeplinks now break out of the in-app Instagram webview more reliably on iOS 17+, so more of your traffic lands in a real browser.' },
  { date: 'Jun 23', type: 'improved', title: 'Clean public links', desc: 'Your pages now live at a clean address (yourname) — shorter, sharper, and easier to share.' },
  { date: 'Jun 22', type: 'improved', title: 'Smarter CTA popup', desc: 'Set a 1–45s reveal delay, your popup settings now persist, and after closing it the bottom button goes straight to your link.' },
  { date: 'Jun 22', type: 'fixed',    title: 'Duplicating a page now copies its links', desc: 'Duplicate a page and the copy actually carries over all of its links, ready to tweak.' },
  { date: 'Jun 22', type: 'new',      title: 'How to film a converting VSL', desc: 'A short walkthrough built into the page builder shows you how to shoot a VSL that actually converts.' },
  { date: 'Jun 21', type: 'new',      title: 'Light mode', desc: 'Switch the whole app and landing between light and dark — your choice is remembered.' },
  { date: 'Jun 20', type: 'new',      title: 'In-app notifications', desc: 'A notification bell keeps you on top of billing, security and account events as they happen.' },
  { date: 'Jun 20', type: 'new',      title: 'VSL Watch Funnel', desc: 'A new dashboard funnel shows how far visitors get through your video — view, watch, and click in one glance.' },
  { date: 'Jun 19', type: 'improved', title: 'White-label on Agency', desc: 'Agency plans now fully remove our branding from public pages.' },
  { date: 'Jun 19', type: 'improved', title: 'Reliable video uploads', desc: 'Uploads now accept web-playable video (MP4/WebM) and tell you the real reason when something is off — no more silent failures.' },
]

const counts = computed(() => ({
  all:      entries.length,
  new:      entries.filter(e => e.type === 'new').length,
  improved: entries.filter(e => e.type === 'improved').length,
  fixed:    entries.filter(e => e.type === 'fixed').length,
}))

const visible = computed(() => active.value === 'all' ? entries : entries.filter(e => e.type === active.value))

const tx      = computed(() => theme.dark ? '#fff' : '#111827')
const txMuted = computed(() => theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280')
const bd      = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const card    = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.04)' : '#fff', border:`1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`, borderRadius:'12px', padding:'18px' }))

function typeColor(type) {
  return type === 'new' ? '#10B981' : type === 'fixed' ? '#F59E0B' : '#8B6CF0'
}
function badge(type) {
  const c = typeColor(type)
  return { display:'inline-block', fontSize:'10px', fontWeight:700, letterSpacing:'0.05em', color:c, background:`${c}22`, border:`1px solid ${c}44`, borderRadius:'999px', padding:'3px 9px' }
}
function tabStyle(key) {
  const on = active.value === key
  return {
    border:'none', cursor:'pointer', fontFamily:'Inter,sans-serif',
    fontSize:'12px', fontWeight:600, padding:'7px 14px', borderRadius:'999px',
    color: on ? '#fff' : txMuted.value,
    background: on ? '#6D4EE8' : 'transparent',
    transition:'all 0.12s',
  }
}
</script>
