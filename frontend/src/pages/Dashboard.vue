<template>
  <DashboardLayout
    title="My Pages"
    :pagesCount="pages.length"
    :pagesLimit="auth.user?.plan === 'agency' ? 999 : auth.user?.plan === 'pro' ? 5 : 1"
  >
    <template #header-actions>
      <div style="display:flex;align-items:center;gap:10px">
        <!-- View toggle -->
        <div :style="{display:'flex',gap:'2px',background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',borderRadius:'8px',padding:'3px'}">
          <button @click="viewMode='grid'"
            :style="{
              padding:'5px 8px', borderRadius:'6px', border:'none', cursor:'pointer',
              background: viewMode==='grid' ? (theme.dark?'rgba(255,255,255,0.12)':'#fff') : 'transparent',
              color: viewMode==='grid' ? (theme.dark?'#fff':'#111827') : (theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF'),
              boxShadow: viewMode==='grid' ? '0 1px 3px rgba(0,0,0,0.08)' : 'none',
              display:'flex', alignItems:'center', transition:'all 0.12s', fontSize:'14px'
            }">
            <i class="bi bi-grid"></i>
          </button>
          <button @click="viewMode='list'"
            :style="{
              padding:'5px 8px', borderRadius:'6px', border:'none', cursor:'pointer',
              background: viewMode==='list' ? (theme.dark?'rgba(255,255,255,0.12)':'#fff') : 'transparent',
              color: viewMode==='list' ? (theme.dark?'#fff':'#111827') : (theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF'),
              boxShadow: viewMode==='list' ? '0 1px 3px rgba(0,0,0,0.08)' : 'none',
              display:'flex', alignItems:'center', transition:'all 0.12s', fontSize:'14px'
            }">
            <i class="bi bi-list-ul"></i>
          </button>
        </div>
        <!-- Period toggle -->
        <div :style="{display:'flex',gap:'2px',background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',borderRadius:'8px',padding:'3px'}">
          <button v-for="p in [7,30,90]" :key="p" @click="setPeriod(p)"
            :style="{
              padding:'4px 12px', borderRadius:'6px', border:'none', fontSize:'12px',
              fontWeight:600, cursor:'pointer', transition:'all 0.12s',
              background: period===p ? (theme.dark?'rgba(255,255,255,0.12)':'#fff') : 'transparent',
              color: period===p ? (theme.dark?'#fff':'#111827') : (theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF'),
              boxShadow: period===p ? '0 1px 3px rgba(0,0,0,0.08)' : 'none',
              fontFamily:'Inter,sans-serif'
            }">
            {{ p }}d
          </button>
        </div>
        <RouterLink to="/pages/new" class="btn-create">
          <i class="bi bi-plus-lg" style="font-size:14px"></i>
          New page
        </RouterLink>
      </div>
    </template>

    <!-- Loading -->
    <div v-if="loading" style="display:flex;align-items:center;justify-content:center;padding:80px;color:#9CA3AF;font-size:13px">
      <div style="width:20px;height:20px;border:2px solid #E5E7EB;border-top-color:#6D4EE8;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:10px"></div>
      Loading...
    </div>

    <template v-else>
      <!-- Stats row -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px">
        <StatCard label="VSL Pages" :value="pages.length" />
        <StatCard label="Page views" :value="totalViews" trend="+0" />
        <StatCard label="Total clicks" :value="totalClicks" trend="+0" />
        <StatCard label="Avg. CTR" :value="avgCtr + '%'" accent />
      </div>

      <!-- Section header -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <h2 :style="{fontSize:'14px',fontWeight:600,color:theme.dark?'#fff':'#111827',margin:0}">Your pages</h2>
      </div>

      <!-- Empty state -->
      <div v-if="pages.length === 0"
        :style="{background:theme.dark?'rgba(255,255,255,0.03)':'#fff',border:`1.5px dashed ${theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'}`,borderRadius:'16px',padding:'72px 32px',textAlign:'center'}">
        <div style="width:56px;height:56px;background:#EEE9FF;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6D4EE8" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </div>
        <h3 :style="{fontSize:'17px',fontWeight:700,color:theme.dark?'#fff':'#111827',margin:'0 0 10px'}">Your digital footprint starts here</h3>
        <p :style="{fontSize:'13px',color:theme.dark?'rgba(255,255,255,0.5)':'#6B7280',margin:'0 auto 24px',maxWidth:'380px',lineHeight:1.65}">
          Create your first smart link to unlock real-time analytics, audience retargeting, and organized growth.
        </p>
        <RouterLink to="/pages/new" class="btn-create">
          <i class="bi bi-plus-lg" style="font-size:14px"></i>
          Create your first page
        </RouterLink>
        <p :style="{fontSize:'12px',color:theme.dark?'rgba(255,255,255,0.3)':'#9CA3AF',margin:'12px 0 0'}">Takes less than 30s</p>
        <!-- Feature highlights -->
        <div style="display:flex;justify-content:center;gap:24px;margin-top:32px">
          <div v-for="f in emptyFeatures" :key="f.label" style="text-align:center">
            <div :style="{width:'36px',height:'36px',background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',borderRadius:'10px',display:'flex',alignItems:'center',justifyContent:'center',margin:'0 auto 8px'}" v-html="f.icon"></div>
            <p :style="{fontSize:'12px',fontWeight:600,color:theme.dark?'rgba(255,255,255,0.8)':'#374151',margin:'0 0 2px'}">{{ f.label }}</p>
            <p :style="{fontSize:'11px',color:theme.dark?'rgba(255,255,255,0.35)':'#9CA3AF',margin:0}">{{ f.sub }}</p>
          </div>
        </div>
      </div>

      <!-- Pages list -->
      <div v-else style="display:flex;flex-direction:column;gap:8px">
        <div v-for="page in pages" :key="page.id"
          :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1px solid ${theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'}`,borderRadius:'14px',overflow:'hidden',transition:'box-shadow 0.15s,border-color 0.15s,transform 0.15s'}"
          @mouseenter="e => { e.currentTarget.style.boxShadow=theme.dark?'0 4px 20px rgba(0,0,0,0.3)':'0 4px 16px rgba(0,0,0,0.06)'; e.currentTarget.style.borderColor=theme.dark?'rgba(109,78,232,0.4)':'#D1D5DB'; e.currentTarget.style.transform='translateY(-1px)' }"
          @mouseleave="e => { e.currentTarget.style.boxShadow='none'; e.currentTarget.style.borderColor=theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'; e.currentTarget.style.transform='translateY(0)' }">

          <div style="display:flex;align-items:center;gap:14px;padding:16px 20px">
            <!-- Thumb -->
            <div :style="{
              width:'44px', height:'44px', borderRadius:'10px', flexShrink:0,
              background: page.bg_color || '#0F0A3C',
              display:'flex', alignItems:'center', justifyContent:'center',
              overflow:'hidden'
            }">
              <img v-if="page.avatar_url" :src="page.avatar_url" style="width:100%;height:100%;object-fit:cover" />
              <i v-else class="bi bi-play-btn" style="font-size:18px;color:rgba(255,255,255,0.5)"></i>
            </div>

            <!-- Info -->
            <div style="flex:1;min-width:0">
              <div style="display:flex;align-items:center;gap:7px;margin-bottom:3px;flex-wrap:wrap">
                <p :style="{fontSize:'14px',fontWeight:600,color:theme.dark?'#fff':'#111827',margin:0}">{{ page.model_name }}</p>
                <span v-if="page.is_active" class="badge-active">
                  <span class="badge-active-dot"></span>
                  <span style="font-size:10px;color:#16A34A;font-weight:600">Active</span>
                </span>
                <span v-else :style="{display:'inline-flex',alignItems:'center',gap:'3px',background:theme.dark?'rgba(255,255,255,0.06)':'#F9FAFB',border:`1px solid ${theme.dark?'rgba(255,255,255,0.1)':'#E5E7EB'}`,borderRadius:'999px',padding:'1px 8px'}">
                  <span :style="{fontSize:'10px',color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',fontWeight:600}">Inactive</span>
                </span>
                <span v-if="page.video_url"
                  style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:999px;padding:1px 8px;font-size:10px;color:#EA580C;font-weight:600">
                  VSL
                </span>
                <span v-if="page.deep_link_enabled"
                  style="background:#EEE9FF;border:1px solid #C7BBFF;border-radius:999px;padding:1px 8px;font-size:10px;color:#6D4EE8;font-weight:600">
                  Deep link
                </span>
              </div>
              <p :style="{fontSize:'12px',color:theme.dark?'rgba(255,255,255,0.35)':'#9CA3AF',margin:0}">mysocialvsl.com/{{ page.slug }}</p>
            </div>

            <!-- Actions -->
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0">
              <a :href="`/p/${page.slug}`" target="_blank"
                :style="{display:'inline-flex',alignItems:'center',gap:'4px',padding:'6px 12px',border:`1px solid ${theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'}`,borderRadius:'7px',fontSize:'12px',fontWeight:500,color:theme.dark?'rgba(255,255,255,0.6)':'#4B5563',textDecoration:'none',background:theme.dark?'rgba(255,255,255,0.05)':'#fff',transition:'all 0.12s'}"
                @mouseenter="e => { e.currentTarget.style.borderColor='#6D4EE8'; e.currentTarget.style.color='#6D4EE8' }"
                @mouseleave="e => { e.currentTarget.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.currentTarget.style.color=theme.dark?'rgba(255,255,255,0.6)':'#4B5563' }">
                <i class="bi bi-box-arrow-up-right" style="font-size:12px"></i>
                View
              </a>
              <button @click="duplicatePage(page)"
                :style="{padding:'6px 12px',border:`1px solid ${theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'}`,borderRadius:'7px',fontSize:'12px',fontWeight:500,color:theme.dark?'rgba(255,255,255,0.6)':'#4B5563',background:theme.dark?'rgba(255,255,255,0.05)':'#fff',cursor:'pointer',transition:'all 0.12s',fontFamily:'Inter,sans-serif'}"
                @mouseenter="e => { e.currentTarget.style.borderColor='#6D4EE8'; e.currentTarget.style.color='#6D4EE8' }"
                @mouseleave="e => { e.currentTarget.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.currentTarget.style.color=theme.dark?'rgba(255,255,255,0.6)':'#4B5563' }">
                Duplicate
              </button>
              <RouterLink :to="`/pages/${page.id}/edit`" class="btn-edit">
                Edit
              </RouterLink>
              <button @click="deletePage(page.id)"
                :style="{padding:'6px 8px',border:`1px solid ${theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'}`,borderRadius:'7px',fontSize:'12px',color:theme.dark?'rgba(255,255,255,0.35)':'#9CA3AF',background:theme.dark?'rgba(255,255,255,0.05)':'#fff',cursor:'pointer',display:'flex',alignItems:'center',transition:'all 0.12s',fontFamily:'Inter,sans-serif'}"
                @mouseenter="e => { e.currentTarget.style.borderColor='#FCA5A5'; e.currentTarget.style.color='#EF4444' }"
                @mouseleave="e => { e.currentTarget.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.currentTarget.style.color=theme.dark?'rgba(255,255,255,0.35)':'#9CA3AF' }">
                <i class="bi bi-trash" style="font-size:13px"></i>
              </button>
            </div>
          </div>

          <!-- Stats bar -->
          <div :style="{display:'grid',gridTemplateColumns:'repeat(4,1fr)',borderTop:`1px solid ${theme.dark?'rgba(255,255,255,0.06)':'#F3F4F6'}`,background:theme.dark?'rgba(255,255,255,0.02)':'#FAFAFA'}">
            <div v-for="(stat, i) in getPageStats(page.id)" :key="i"
              :style="{
                padding:'10px 16px', textAlign:'center',
                borderRight: i < 3 ? `1px solid ${theme.dark?'rgba(255,255,255,0.06)':'#F3F4F6'}` : 'none',
              }">
              <p :style="{fontSize:'10px',color:theme.dark?'rgba(255,255,255,0.35)':'#9CA3AF',fontWeight:500,margin:'0 0 3px',textTransform:'uppercase',letterSpacing:'0.06em'}">{{ stat.label }}</p>
              <p :style="{fontSize:'16px',fontWeight:700,letterSpacing:'-0.02em',color:stat.highlight?'#6D4EE8':(theme.dark?'#fff':'#111827'),margin:0}">{{ stat.value }}</p>
            </div>
          </div>
        </div>
      </div>
    </template>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'
import StatCard from '@/components/StatCard.vue'

const auth = useAuthStore()
const theme = useThemeStore()
const pages = ref([])
const stats = ref({})
const loading = ref(true)
const period = ref(30)
const viewMode = ref('list')

const emptyFeatures = [
  { label: 'Click Tracking', sub: 'Every click logged',  icon: '<i class="bi bi-cursor" style="font-size:16px;color:#6B7280"></i>' },
  { label: 'Deep Analytics', sub: 'Audience insights',   icon: '<i class="bi bi-graph-up" style="font-size:16px;color:#6B7280"></i>' },
  { label: 'Custom Domains', sub: 'Your brand URL',       icon: '<i class="bi bi-globe" style="font-size:16px;color:#6B7280"></i>' },
]

async function setPeriod(p) {
  period.value = p
  await Promise.all(pages.value.map(async page => {
    try {
      const { data: s } = await api.get(`/pages/${page.id}/analytics?period=${p}`)
      stats.value[page.id] = s
    } catch {
      stats.value[page.id] = { page_views: 0, link_clicks: 0, age_confirmed: 0, ctr: 0 }
    }
  }))
}

const totalViews  = computed(() => Object.values(stats.value).reduce((s, p) => s + (p.page_views  || 0), 0))
const totalClicks = computed(() => Object.values(stats.value).reduce((s, p) => s + (p.link_clicks || 0), 0))
const avgCtr = computed(() => {
  const values = Object.values(stats.value).map(p => p.ctr || 0)
  if (!values.length) return 0
  return Math.round(values.reduce((s, v) => s + v, 0) / values.length * 10) / 10
})

function getPageStats(pageId) {
  const s = stats.value[pageId] || {}
  const ctr = s.ctr || 0
  return [
    { label: 'Views',      value: s.page_views   ?? '—', highlight: false },
    { label: 'Clicks',     value: s.link_clicks  ?? '—', highlight: false },
    { label: 'Conversions', value: s.age_confirmed ?? '—', highlight: false },
    { label: 'CTR',        value: ctr ? ctr + '%' : '—', highlight: ctr > 30 },
  ]
}

onMounted(async () => {
  try {
    const { data } = await api.get('/pages')
    pages.value = data
    await Promise.all(data.map(async page => {
      try {
        const { data: s } = await api.get(`/pages/${page.id}/analytics?period=${period.value}`)
        stats.value[page.id] = s
      } catch {
        stats.value[page.id] = { page_views: 0, link_clicks: 0, age_confirmed: 0, ctr: 0 }
      }
    }))
  } finally {
    loading.value = false
  }
})

async function deletePage(id) {
  if (!confirm('Delete this page?')) return
  await api.delete(`/pages/${id}`)
  pages.value = pages.value.filter(p => p.id !== id)
}

async function duplicatePage(page) {
  try {
    const { data } = await api.post('/pages', {
      model_name: page.model_name + ' (copy)',
      model_handle: page.model_handle,
      bio: page.bio,
      video_url: page.video_url,
      bg_color: page.bg_color,
      btn_color: page.btn_color,
      template: page.template,
      deep_link_enabled: page.deep_link_enabled,
      age_gate: page.age_gate,
    })
    pages.value.push(data)
    stats.value[data.id] = { page_views: 0, link_clicks: 0, age_confirmed: 0, ctr: 0 }
  } catch {}
}
</script>

<style>
@keyframes spin { to { transform: rotate(360deg) } }

@keyframes shimmer-btn {
  0%   { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes pulse-ring {
  0%   { box-shadow: 0 0 0 0 rgba(109,78,232,0.4); }
  70%  { box-shadow: 0 0 0 8px rgba(109,78,232,0); }
  100% { box-shadow: 0 0 0 0 rgba(109,78,232,0); }
}

@keyframes float-icon {
  0%, 100% { transform: translateY(0px); }
  50%       { transform: translateY(-2px); }
}

.btn-create {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  color: #fff;
  position: relative;
  overflow: hidden;
  background: linear-gradient(90deg, #6D4EE8, #A78BFA, #6D4EE8);
  background-size: 200% auto;
  animation: shimmer-btn 3s linear infinite;
  transition: transform 0.15s, box-shadow 0.15s;
}
.btn-create:hover {
  transform: translateY(-1px) scale(1.02);
  box-shadow: 0 4px 20px rgba(109,78,232,0.45);
  animation: shimmer-btn 1.5s linear infinite;
}
.btn-create svg {
  animation: float-icon 2s ease-in-out infinite;
}

.btn-edit {
  padding: 6px 14px;
  background: #6D4EE8;
  color: #fff;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.15s;
  position: relative;
  overflow: hidden;
}
.btn-edit::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.25) 50%, transparent 70%);
  transform: translateX(-100%);
  transition: transform 0.4s;
}
.btn-edit:hover::after {
  transform: translateX(100%);
}
.btn-edit:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(109,78,232,0.4);
}

.badge-active {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  border-radius: 999px;
  padding: 1px 8px;
}
.badge-active-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #16A34A;
  display: inline-block;
  animation: pulse-ring 2s ease-out infinite;
}
</style>
