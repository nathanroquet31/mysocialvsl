<template>
  <div :style="{marginBottom:'28px'}">

    <!-- Header + period selector -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:14px">
      <div style="display:flex;align-items:center;gap:9px">
        <i class="bi bi-diagram-3-fill" :style="{fontSize:'16px',color:'#6D4EE8'}"></i>
        <h2 :style="{margin:0,fontSize:'16px',fontWeight:700,color:text}">Network analytics</h2>
        <span :style="{fontSize:'11px',color:muted}">the whole platform, aggregated</span>
      </div>
      <div style="display:flex;gap:6px">
        <button v-for="p in presets" :key="p.key" @click="setPreset(p.key)" :style="chip(preset===p.key)">{{ p.label }}</button>
      </div>
    </div>

    <div v-if="loading" :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'40px',textAlign:'center',color:muted,fontSize:'13px'}">
      Loading network data…
    </div>

    <template v-else-if="data">
      <!-- Overview cards -->
      <div :style="{display:'grid',gridTemplateColumns:'repeat(auto-fit,minmax(150px,1fr))',gap:'12px',marginBottom:'18px'}">
        <div v-for="c in overviewCards" :key="c.label" :style="cardBox">
          <div style="display:flex;align-items:center;gap:7px;margin-bottom:5px">
            <i :class="`bi ${c.icon}`" :style="{fontSize:'13px',color:c.color}"></i>
            <span :style="{fontSize:'11px',color:sub,fontWeight:500}">{{ c.label }}</span>
          </div>
          <p :style="{fontSize:'23px',fontWeight:700,color:text,margin:0,lineHeight:1}">{{ c.value }}</p>
          <p v-if="c.hint" :style="{fontSize:'10px',color:muted,margin:'5px 0 0'}">{{ c.hint }}</p>
        </div>
      </div>

      <!-- Benchmarks + Feature adoption -->
      <div :style="{display:'grid',gridTemplateColumns:'repeat(auto-fit,minmax(300px,1fr))',gap:'14px',marginBottom:'18px'}">

        <!-- Benchmarks -->
        <div :style="cardBox">
          <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:12px">
            <h3 :style="panelTitle">Benchmarks</h3>
            <span :style="{fontSize:'10px',color:muted}">{{ data.benchmarks.sample_pages }} pages · ≥{{ data.benchmarks.min_views_threshold }} views</span>
          </div>
          <table style="width:100%;border-collapse:collapse;font-size:12px">
            <thead>
              <tr :style="{color:muted,fontSize:'10px',textTransform:'uppercase',letterSpacing:'0.04em',textAlign:'right'}">
                <th :style="{textAlign:'left',padding:'4px 0'}">Metric</th>
                <th>p25</th><th>Median</th><th>p75</th><th :style="{color:'#10B981'}">Top 10%</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in benchmarkRows" :key="r.label" :style="{borderTop:`1px solid ${divider}`}">
                <td :style="{padding:'8px 0',color:text,fontWeight:600}">{{ r.label }}</td>
                <td :style="cellR">{{ fmtPct(r.v.p25, r.suffix) }}</td>
                <td :style="cellR">{{ fmtPct(r.v.median, r.suffix) }}</td>
                <td :style="cellR">{{ fmtPct(r.v.p75, r.suffix) }}</td>
                <td :style="{...cellR, color:'#10B981', fontWeight:700}">{{ fmtPct(r.v.p90, r.suffix) }}</td>
              </tr>
            </tbody>
          </table>
          <p v-if="data.benchmarks.sample_pages === 0" :style="{fontSize:'11px',color:muted,margin:'12px 0 0'}">
            Not enough volume yet — benchmarks appear once pages cross {{ data.benchmarks.min_views_threshold }} views.
          </p>
        </div>

        <!-- Feature adoption -->
        <div :style="cardBox">
          <h3 :style="{...panelTitle, marginBottom:'12px'}">Feature adoption</h3>
          <div style="display:flex;flex-direction:column;gap:9px">
            <div v-for="f in adoptionRows" :key="f.key">
              <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
                <span :style="{color:text}">{{ f.label }}</span>
                <span :style="{color:sub,fontWeight:600}">{{ f.value }}%</span>
              </div>
              <div :style="{height:'6px',borderRadius:'999px',background:divider,overflow:'hidden'}">
                <div :style="{height:'100%',width:f.value+'%',borderRadius:'999px',background:'#6D4EE8',transition:'width 0.4s'}"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance by shape -->
      <div :style="{display:'grid',gridTemplateColumns:'repeat(auto-fit,minmax(240px,1fr))',gap:'14px',marginBottom:'18px'}">
        <div v-for="g in shapeGroups" :key="g.title" :style="cardBox">
          <h3 :style="{...panelTitle, marginBottom:'10px'}">{{ g.title }}</h3>
          <table style="width:100%;border-collapse:collapse;font-size:12px">
            <thead>
              <tr :style="{color:muted,fontSize:'10px',textTransform:'uppercase',textAlign:'right'}">
                <th :style="{textAlign:'left',padding:'3px 0'}"></th><th>Pages</th><th>CTR</th><th>Play</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in g.rows" :key="row.key" :style="{borderTop:`1px solid ${divider}`}">
                <td :style="{padding:'7px 0',color:text,fontWeight:600}">{{ shapeLabel(row.key) }}</td>
                <td :style="cellR">{{ row.pages }}</td>
                <td :style="{...cellR, color:'#A78BFA', fontWeight:600}">{{ row.ctr }}%</td>
                <td :style="cellR">{{ row.play_rate }}%</td>
              </tr>
              <tr v-if="g.rows.length === 0"><td colspan="4" :style="{padding:'8px 0',color:muted}">No data</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top pages -->
      <div :style="{...cardBox, padding:0, overflow:'hidden'}">
        <h3 :style="{...panelTitle, padding:'16px 16px 0'}">Top pages (by clicks)</h3>
        <div :style="{overflowX:'auto'}">
          <table style="width:100%;border-collapse:collapse;font-size:12px">
            <thead>
              <tr :style="{color:muted,fontSize:'10px',textTransform:'uppercase',letterSpacing:'0.04em',textAlign:'right'}">
                <th :style="{textAlign:'left',padding:'10px 16px'}">Page</th>
                <th :style="{textAlign:'left',padding:'10px 16px'}">Owner</th>
                <th :style="{padding:'10px 16px'}">Views</th>
                <th :style="{padding:'10px 16px'}">Clicks</th>
                <th :style="{padding:'10px 16px'}">CTR</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in data.top_pages" :key="p.id" :style="{borderTop:`1px solid ${divider}`}">
                <td :style="{padding:'10px 16px'}">
                  <a :href="`/${p.slug}`" target="_blank" :style="{color:text,fontWeight:600,textDecoration:'none'}">{{ p.name || p.slug }}</a>
                  <span :style="{marginLeft:'6px',fontSize:'10px',color:muted}">{{ p.template }}</span>
                </td>
                <td :style="{padding:'10px 16px',color:sub}">{{ p.owner || '—' }}</td>
                <td :style="{padding:'10px 16px',textAlign:'right',color:text}">{{ fmt(p.views) }}</td>
                <td :style="{padding:'10px 16px',textAlign:'right',color:text}">{{ fmt(p.clicks) }}</td>
                <td :style="{padding:'10px 16px',textAlign:'right',color:'#A78BFA',fontWeight:600}">{{ p.ctr }}%</td>
              </tr>
              <tr v-if="data.top_pages.length === 0">
                <td colspan="5" :style="{padding:'24px',textAlign:'center',color:muted}">No clicks in this range yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Growth (views per period) -->
      <div :style="{...cardBox, marginTop:'14px'}" v-if="growthMax > 0">
        <h3 :style="{...panelTitle, marginBottom:'12px'}">Traffic ({{ data.growth.granularity }})</h3>
        <div style="display:flex;align-items:flex-end;gap:3px;height:90px">
          <div v-for="(v, i) in data.growth.views" :key="i" :title="`${data.growth.labels[i]}: ${v} views`"
            :style="{flex:1,minWidth:'2px',borderRadius:'3px 3px 0 0',background:'#6D4EE8',opacity:0.85,height:Math.max(2, (v/growthMax)*90)+'px',transition:'height 0.4s'}"></div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'

const theme = useThemeStore()

const presets = [
  { key: '7d',  label: '7d'  },
  { key: '30d', label: '30d' },
  { key: '90d', label: '90d' },
  { key: '12m', label: '12m' },
  { key: 'all', label: 'All' },
]

const preset  = ref('30d')
const data    = ref(null)
const loading = ref(false)

async function load() {
  loading.value = true
  try {
    const { data: d } = await api.get('/admin/network', { params: { preset: preset.value } })
    data.value = d
  } finally {
    loading.value = false
  }
}
function setPreset(p) {
  if (preset.value === p) return
  preset.value = p
  load()
}

onMounted(load)

const fmt = (n) => (n ?? 0).toLocaleString()
const fmtPct = (v, suffix) => v === null || v === undefined ? '—' : v + suffix

const overviewCards = computed(() => {
  const o = data.value?.overview ?? {}
  return [
    { label: 'Active pages',   value: fmt(o.active_pages),    icon: 'bi-file-earmark-text-fill', color: '#6D4EE8', hint: `${fmt(o.total_users)} users` },
    { label: 'Unique visitors', value: fmt(o.unique_visitors), icon: 'bi-people-fill',            color: '#3B82F6', hint: `${fmt(o.page_views)} visits` },
    { label: 'Link clicks',    value: fmt(o.link_clicks),     icon: 'bi-cursor-fill',            color: '#10B981' },
    { label: 'Network CTR',    value: (o.ctr ?? 0) + '%',     icon: 'bi-graph-up-arrow',         color: '#A78BFA' },
    { label: 'Engaged CTR',    value: (o.engaged_ctr ?? 0) + '%', icon: 'bi-fire',               color: '#F97316', hint: 'clicks among watchers' },
    { label: 'Play rate',      value: (o.play_rate ?? 0) + '%', icon: 'bi-play-circle-fill',     color: '#EC4899' },
    { label: 'Paying',         value: fmt(o.paying_users),    icon: 'bi-credit-card-fill',       color: '#059669' },
    { label: 'Countries',      value: fmt(o.countries),       icon: 'bi-globe',                  color: '#6B7280', hint: `${fmt(o.total_events)} events` },
  ]
})

const benchmarkRows = computed(() => {
  const b = data.value?.benchmarks ?? {}
  return [
    { label: 'CTR',        v: b.ctr ?? {},               suffix: '%' },
    { label: 'Play rate',  v: b.play_rate ?? {},         suffix: '%' },
    { label: 'Watch',      v: b.avg_watch_seconds ?? {}, suffix: 's' },
  ]
})

const adoptionLabels = {
  video: 'Video (VSL)', deeplink: 'Deeplink', popup: 'Popup template', bandeau: 'Bandeau template',
  cta_reveal: 'Timed CTA reveal', bot_protection: 'Bot protection', custom_domain: 'Custom domain', white_label: 'White-label',
}
const adoptionRows = computed(() => {
  const f = data.value?.feature_adoption?.features ?? {}
  return Object.entries(f).map(([key, value]) => ({ key, value, label: adoptionLabels[key] ?? key }))
})

const shapeGroups = computed(() => {
  const s = data.value?.by_shape ?? {}
  return [
    { title: 'By template', rows: s.by_template ?? [] },
    { title: 'By video',    rows: s.by_video ?? [] },
    { title: 'By deeplink', rows: s.by_deeplink ?? [] },
    { title: 'By plan',     rows: s.by_plan ?? [] },
  ]
})

const shapeLabels = {
  with_video: 'With video', no_video: 'No video', deeplink_on: 'On', deeplink_off: 'Off',
  'vsl-bandeau': 'Bandeau', 'vsl-popup': 'Popup', 'vsl-classic': 'Classic', classic: 'Classic', direct: 'Direct link',
  free: 'Free', pro: 'Pro', agency: 'Agency',
}
const shapeLabel = (k) => shapeLabels[k] ?? k

const growthMax = computed(() => Math.max(0, ...(data.value?.growth?.views ?? [0])))

// Theme tokens (match AdminPage / DashboardLayout)
const card    = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#fff')
const border  = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const divider = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6')
const text    = computed(() => theme.dark ? '#fff' : '#111827')
const sub     = computed(() => theme.dark ? 'rgba(255,255,255,0.55)' : '#6B7280')
const muted   = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
const inputBorder = computed(() => theme.dark ? 'rgba(255,255,255,0.12)' : '#E5E7EB')

const cardBox    = computed(() => ({ background: card.value, border: `1px solid ${border.value}`, borderRadius: '12px', padding: '16px 18px' }))
const panelTitle = computed(() => ({ margin: 0, fontSize: '13px', fontWeight: 700, color: text.value }))
const cellR      = computed(() => ({ padding: '8px 0', textAlign: 'right', color: sub.value }))

function chip(active) {
  return {
    cursor: 'pointer', fontFamily: 'Inter,sans-serif', fontSize: '12px', fontWeight: 600,
    padding: '5px 12px', borderRadius: '999px',
    border: active ? '1px solid #6D4EE8' : `1px solid ${inputBorder.value}`,
    background: active ? 'rgba(109,78,232,0.12)' : 'transparent',
    color: active ? '#A78BFA' : sub.value,
  }
}
</script>
