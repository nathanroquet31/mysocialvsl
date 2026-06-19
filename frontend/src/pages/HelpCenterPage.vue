<template>
  <DashboardLayout title="Help Center">

    <!-- Search -->
    <div style="max-width:560px;margin:0 auto 28px;text-align:center">
      <p :style="{fontSize:'20px',fontWeight:800,color:tx,margin:'12px 0 16px'}">How can we help?</p>
      <div style="position:relative">
        <i class="bi bi-search" :style="{position:'absolute',left:'16px',top:'50%',transform:'translateY(-50%)',color:txMuted,fontSize:'14px'}"></i>
        <input v-model="search" placeholder="Search articles…"
          :style="{width:'100%',border:`1px solid ${bd}`,borderRadius:'12px',padding:'13px 16px 13px 42px',fontSize:'14px',color:tx,outline:'none',background:theme.dark?'rgba(255,255,255,0.06)':'#fff',fontFamily:'Inter,sans-serif',boxSizing:'border-box'}" />
      </div>
    </div>

    <!-- FAQ accordion -->
    <div style="max-width:720px;margin:0 auto 28px;display:flex;flex-direction:column;gap:10px">
      <div v-for="a in filtered" :key="a.q" :style="card">
        <div @click="toggle(a.q)" style="display:flex;align-items:center;gap:12px;cursor:pointer">
          <div :style="{width:'34px',height:'34px',borderRadius:'9px',background:'#EEE9FF',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
            <i :class="`bi bi-${a.icon}`" style="color:#6D4EE8;font-size:15px"></i>
          </div>
          <p :style="{flex:1,fontSize:'14px',fontWeight:700,color:tx,margin:0}">{{ a.q }}</p>
          <i class="bi bi-chevron-down" :style="{color:txMuted,fontSize:'13px',transition:'transform 0.2s',transform:open===a.q?'rotate(180deg)':'rotate(0)'}"></i>
        </div>
        <div v-if="open===a.q" :style="{fontSize:'13.5px',color:txMuted,lineHeight:1.75,margin:'12px 0 2px',paddingLeft:'46px'}" v-html="a.a"></div>
      </div>

      <p v-if="filtered.length === 0" :style="{textAlign:'center',color:txMuted,fontSize:'13px',padding:'24px'}">
        No article matches “{{ search }}”. Try another term or contact support below.
      </p>
    </div>

    <!-- Contact -->
    <div :style="card" style="text-align:center;max-width:720px;margin:0 auto">
      <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 6px'}">Can't find an answer?</p>
      <p :style="{fontSize:'13px',color:txMuted,margin:'0 0 14px'}">Our team usually replies within a few hours.</p>
      <RouterLink to="/dashboard/support" style="display:inline-block;background:#6D4EE8;color:#fff;border-radius:8px;padding:10px 22px;font-size:13px;font-weight:600;text-decoration:none">Contact support</RouterLink>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()
const search = ref('')
const open = ref('')

function toggle(q) { open.value = open.value === q ? '' : q }

const articles = [
  {
    icon: 'rocket-takeoff',
    q: 'Create your first VSL page',
    a: 'Go to <strong>Links → New page</strong> and pick <strong>VSL Page</strong>. Then follow the 4 steps: choose a template, upload your video, set your CTA button (paste your OnlyFans link), and publish. Your page goes live instantly at <strong>mysocialvsl.com/p/your-slug</strong> — paste that link in your Instagram bio and you’re done.',
  },
  {
    icon: 'camera-video',
    q: 'Upload your video — format & tips',
    a: 'Upload an <strong>MP4 (H.264)</strong> or WebM file — those play in every browser. iPhones record in HEVC <em>.mov</em> by default, which many browsers can’t play; switch to <strong>Settings → Camera → Formats → “Most Compatible”</strong> to record MP4-friendly video. Film <strong>vertical (9:16)</strong>, keep it short (~30–45s), and hook the viewer in the first 3 seconds — the video is what sells.',
  },
  {
    icon: 'lightning-charge',
    q: 'What is the deeplink (and why is it a paid feature)?',
    a: 'Instagram, TikTok and Snapchat open links inside their <strong>in-app browser</strong>, where fans <strong>can’t log in to OnlyFans</strong>. The deeplink detects this and forces the link to open in Safari/Chrome, so the fan lands ready to subscribe — it’s the single biggest conversion lever. The deeplink is available on <strong>paid plans</strong>; on Free, your VSL page still works, just without the in-app browser bypass.',
  },
  {
    icon: 'shield-check',
    q: 'How does my link survive Instagram? (bot protection)',
    a: 'When you share a link, Instagram sends a bot to scan it first. If it sees “OnlyFans”, it can flag the link. Our <strong>server-side cloaking</strong> serves that bot a clean decoy page (no OnlyFans), while real humans get your full VSL — so your link stays alive and the share preview looks innocent. It’s on by default; leave it on.',
  },
  {
    icon: 'graph-up',
    q: 'Read your analytics (views, clicks, CTR)',
    a: 'Your dashboard shows <strong>views</strong> (page loads), <strong>clicks</strong> (taps on your OnlyFans button), and <strong>CTR = clicks ÷ views</strong>. CTR is the number that matters: it’s the % of visitors who actually head to OnlyFans. The <strong>live</strong> view shows visitors and events in the last few minutes, so you can watch a campaign in real time.',
  },
  {
    icon: 'credit-card',
    q: 'Plans & billing',
    a: '<strong>Free</strong>: 1 VSL page, no deeplink. <strong>Pro</strong>: more pages + deeplink + analytics. <strong>Agency</strong>: 25 pages/links (scalable up to ∞), white-label (no “Powered by”), multi-model. Manage or cancel anytime from <strong>Billing</strong> — your access continues until the end of the paid period.',
  },
]

const filtered = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return articles
  return articles.filter(a => a.q.toLowerCase().includes(q) || a.a.toLowerCase().includes(q))
})

const tx      = computed(() => theme.dark ? '#fff' : '#111827')
const txMuted = computed(() => theme.dark ? 'rgba(255,255,255,0.55)' : '#6B7280')
const bd      = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const card    = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.04)' : '#fff', border:`1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`, borderRadius:'12px', padding:'18px 20px' }))
</script>
