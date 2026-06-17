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

    <!-- Categories -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px;margin-bottom:28px">
      <div v-for="cat in filteredCategories" :key="cat.title" :style="card">
        <div :style="{width:'36px',height:'36px',borderRadius:'10px',background:'#EEE9FF',display:'flex',alignItems:'center',justifyContent:'center',marginBottom:'12px'}">
          <i :class="`bi bi-${cat.icon}`" style="color:#6D4EE8;font-size:16px"></i>
        </div>
        <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 4px'}">{{ cat.title }}</p>
        <p :style="{fontSize:'12px',color:txMuted,margin:'0 0 12px',lineHeight:1.6}">{{ cat.desc }}</p>
        <div style="display:flex;flex-direction:column;gap:6px">
          <span v-for="a in cat.articles" :key="a" :style="{fontSize:'12px',color:'#6D4EE8',cursor:'pointer'}">{{ a }} →</span>
        </div>
      </div>
    </div>

    <!-- Contact -->
    <div :style="card" style="text-align:center">
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

const categories = [
  { title: 'Getting started', icon: 'rocket-takeoff', desc: 'Create your first landing page or direct link in minutes.',
    articles: ['Create your first page', 'Landing page vs Direct link', 'Publish & share your link'] },
  { title: 'VSL & video', icon: 'camera-video', desc: 'Upload, configure and optimize your Video Sales Letter.',
    articles: ['Upload a VSL', 'Best VSL script structure', 'Video display settings'] },
  { title: 'Links & components', icon: 'link-45deg', desc: 'Buttons, image blocks, carousels, countdowns and more.',
    articles: ['Add & reorder components', 'FOMO popup setup', 'Social icons & quick add'] },
  { title: 'Analytics', icon: 'graph-up', desc: 'Understand views, clicks, CTR and video engagement.',
    articles: ['Reading your dashboard', 'Tracking conversions', 'UTM parameters'] },
  { title: 'Domains', icon: 'globe', desc: 'Connect a custom domain for a professional look.',
    articles: ['Add a custom domain', 'DNS configuration', 'Verification status'] },
  { title: 'Billing & plans', icon: 'credit-card', desc: 'Subscriptions, upgrades and the Agency plan.',
    articles: ['Manage your subscription', 'Agency plan options', 'Invoices'] },
  { title: 'API & integrations', icon: 'code-slash', desc: 'REST API v3, API keys and MCP integration.',
    articles: ['Mint an API key', 'API v3 reference', 'MCP endpoint'] },
  { title: 'Account & security', icon: 'shield-check', desc: 'Profile, sessions, and account safety.',
    articles: ['Active sessions', 'Change password', 'Delete account'] },
]

const filteredCategories = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return categories
  return categories
    .map(c => ({ ...c, articles: c.articles.filter(a => a.toLowerCase().includes(q) || c.title.toLowerCase().includes(q)) }))
    .filter(c => c.articles.length || c.title.toLowerCase().includes(q))
})

const tx      = computed(() => theme.dark ? '#fff' : '#111827')
const txMuted = computed(() => theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280')
const bd      = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const card    = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.04)' : '#fff', border:`1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`, borderRadius:'12px', padding:'20px' }))
</script>
