<template>
  <DashboardLayout title="Admin">

    <!-- Metrics cards -->
    <div :style="{display:'grid',gridTemplateColumns:'repeat(auto-fit,minmax(160px,1fr))',gap:'14px',marginBottom:'24px'}">
      <div v-for="m in metricCards" :key="m.label"
        :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'16px 18px'}">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
          <i :class="`bi ${m.icon}`" :style="{fontSize:'14px',color:m.color}"></i>
          <span :style="{fontSize:'12px',color:sub,fontWeight:500}">{{ m.label }}</span>
        </div>
        <p :style="{fontSize:'26px',fontWeight:700,color:text,margin:0,lineHeight:1}">{{ m.value }}</p>
        <p v-if="m.hint" :style="{fontSize:'11px',color:muted,margin:'6px 0 0'}">{{ m.hint }}</p>
      </div>
    </div>

    <!-- Search -->
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
      <div :style="{flex:1,maxWidth:'340px',display:'flex',alignItems:'center',gap:'8px',background:inputBg,border:`1px solid ${inputBorder}`,borderRadius:'10px',padding:'8px 12px'}">
        <i class="bi bi-search" :style="{fontSize:'13px',color:muted}"></i>
        <input v-model="search" @input="onSearch" placeholder="Search by email…"
          :style="{flex:1,border:'none',outline:'none',background:'transparent',color:text,fontSize:'13px',fontFamily:'Inter,sans-serif'}" />
      </div>
      <span :style="{fontSize:'12px',color:muted}">{{ total }} user{{ total === 1 ? '' : 's' }}</span>
    </div>

    <!-- Users table -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',overflow:'hidden'}">
      <div :style="{overflowX:'auto'}">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <thead>
            <tr :style="{textAlign:'left',color:muted,fontSize:'11px',textTransform:'uppercase',letterSpacing:'0.05em'}">
              <th :style="thStyle">User</th>
              <th :style="thStyle">Plan</th>
              <th :style="thStyle">Trial</th>
              <th :style="thStyle">Referred by</th>
              <th :style="thStyle">Joined</th>
              <th :style="thStyle">Activity</th>
              <th :style="{...thStyle, textAlign:'right'}">Beta partner</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="u in users" :key="u.id">
            <tr :style="{borderTop:`1px solid ${divider}`}">
              <td :style="tdStyle">
                <div @click="toggleExpand(u)" style="cursor:pointer;display:flex;align-items:center;gap:9px">
                  <i :class="expandedId===u.id ? 'bi bi-chevron-down' : 'bi bi-chevron-right'" :style="{fontSize:'10px',color:muted,flexShrink:0}"></i>
                  <div>
                    <div :style="{fontWeight:600,color:text,display:'flex',alignItems:'center',gap:'6px'}">
                      {{ u.name || '—' }}
                      <span v-if="u.is_admin" :style="badge('#6D4EE8')">Admin</span>
                      <span v-if="u.is_beta" :style="badge('#059669')">Beta</span>
                    </div>
                    <div :style="{color:sub,fontSize:'12px'}">{{ u.email }}</div>
                  </div>
                </div>
              </td>
              <td :style="tdStyle">
                <span :style="planBadge(u.plan)">{{ planLabel(u.plan) }}</span>
              </td>
              <td :style="tdStyle">
                <span v-if="u.on_trial" :style="{color: u.trial_days_left <= 7 ? '#F59E0B' : text}">
                  {{ u.trial_days_left }}d left
                </span>
                <span v-else :style="{color:muted}">—</span>
              </td>
              <td :style="{...tdStyle, color:sub}">{{ u.referred_by ?? '—' }}</td>
              <td :style="{...tdStyle, color:sub}">{{ fmtDate(u.created_at) }}</td>
              <td :style="tdStyle">
                <div v-if="u.pages_count > 0" :style="{display:'flex',flexDirection:'column',gap:'1px'}">
                  <span :style="{color:text,fontWeight:600}">{{ u.pages_count }} page{{ u.pages_count > 1 ? 's' : '' }}</span>
                  <span :style="{color:sub,fontSize:'11px'}">{{ u.views }} views · {{ u.clicks }} clicks{{ u.views > 0 ? ' · ' + u.ctr + '%' : '' }}</span>
                </div>
                <span v-else :style="{color:muted}">— not activated</span>
              </td>
              <td :style="{...tdStyle, textAlign:'right'}">
                <div style="display:flex;gap:8px;justify-content:flex-end;align-items:center">
                  <button @click="openGrant(u)"
                    :style="{cursor:'pointer',fontFamily:'Inter,sans-serif',fontSize:'12px',fontWeight:600,padding:'5px 12px',borderRadius:'999px',border:`1px solid ${inputBorder}`,background:'transparent',color:sub,transition:'all 0.12s'}">
                    <i class="bi bi-gift" style="margin-right:5px;font-size:11px"></i>Grant
                  </button>
                  <button @click="toggleBeta(u)" :disabled="u._saving"
                    :style="{
                      cursor: u._saving ? 'wait' : 'pointer', fontFamily:'Inter,sans-serif',
                      fontSize:'12px', fontWeight:600, padding:'5px 12px', borderRadius:'999px',
                      border: u.is_beta_partner ? '1px solid #059669' : `1px solid ${inputBorder}`,
                      background: u.is_beta_partner ? 'rgba(5,150,105,0.12)' : 'transparent',
                      color: u.is_beta_partner ? '#10B981' : sub, opacity: u._saving ? 0.6 : 1,
                      transition:'all 0.12s'
                    }">
                    <i :class="u.is_beta_partner ? 'bi bi-check-circle-fill' : 'bi bi-circle'" style="margin-right:5px;font-size:11px"></i>
                    {{ u.is_beta_partner ? 'Partner' : 'Make partner' }}
                  </button>
                </div>
              </td>
            </tr>
            <!-- Expandable detail: the user's pages -->
            <tr v-if="expandedId===u.id" :style="{background: theme.dark ? 'rgba(255,255,255,0.02)' : '#FAFAFB'}">
              <td colspan="7" :style="{padding:'4px 16px 14px 41px'}">
                <div v-if="u._pagesLoading" :style="{color:muted,fontSize:'12px',padding:'8px 0'}">Loading pages…</div>
                <div v-else-if="(userPages[u.id]||[]).length===0" :style="{color:muted,fontSize:'12px',padding:'8px 0'}">No pages created.</div>
                <div v-else style="display:flex;flex-direction:column">
                  <div v-for="p in userPages[u.id]" :key="p.id"
                    :style="{display:'flex',alignItems:'center',gap:'12px',fontSize:'12px',padding:'7px 0',borderTop:`1px solid ${divider}`}">
                    <span :style="{fontWeight:600,color:text,minWidth:'130px'}">{{ p.model_name || p.slug }}</span>
                    <span :style="badge(p.page_type==='direct' ? '#D97706' : '#6D4EE8')">{{ p.page_type==='direct' ? 'Direct' : 'VSL' }}</span>
                    <span :style="{color: p.is_active ? '#10B981' : muted, fontWeight:600}">{{ p.is_active ? 'Live' : 'Off' }}</span>
                    <span :style="{color:sub}">{{ p.views }} views · {{ p.clicks }} clicks · {{ p.ctr }}%</span>
                    <a :href="`/p/${p.slug}`" target="_blank" :style="{marginLeft:'auto',color:'#A78BFA',textDecoration:'none',fontWeight:600}">/{{ p.slug }} ↗</a>
                  </div>
                </div>
              </td>
            </tr>
            </template>
            <tr v-if="!loading && users.length === 0">
              <td colspan="7" :style="{...tdStyle, textAlign:'center', color:muted, padding:'32px'}">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage > 1" style="display:flex;align-items:center;justify-content:center;gap:12px;margin-top:16px">
      <button @click="go(page - 1)" :disabled="page <= 1" :style="pagerBtn(page <= 1)">Prev</button>
      <span :style="{fontSize:'12px',color:sub}">Page {{ page }} / {{ lastPage }}</span>
      <button @click="go(page + 1)" :disabled="page >= lastPage" :style="pagerBtn(page >= lastPage)">Next</button>
    </div>

    <!-- Grant access modal -->
    <div v-if="grantUser" @click.self="grantUser=null"
      :style="{position:'fixed',inset:0,background:'rgba(0,0,0,0.6)',zIndex:1000,display:'flex',alignItems:'center',justifyContent:'center',padding:'24px'}">
      <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'16px',padding:'24px',width:'380px',maxWidth:'100%'}">
        <h3 :style="{margin:'0 0 2px',fontSize:'16px',fontWeight:700,color:text}">Grant access</h3>
        <p :style="{margin:'0 0 18px',fontSize:'12px',color:sub}">{{ grantUser.email }}</p>

        <p :style="modalLabel">Plan</p>
        <div style="display:flex;gap:6px;margin-bottom:16px">
          <button v-for="pl in ['free','pro','agency']" :key="pl" @click="grantForm.plan=pl" :style="chip(grantForm.plan===pl)">{{ planLabel(pl) }}</button>
        </div>

        <p :style="modalLabel">Duration</p>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px">
          <button v-for="d in durations" :key="d.label" @click="grantForm.days=d.days" :style="chip(grantForm.days===d.days)">{{ d.label }}</button>
        </div>

        <label v-if="grantForm.plan==='agency'" style="display:flex;align-items:center;gap:8px;margin-bottom:18px;cursor:pointer">
          <input type="checkbox" v-model="grantForm.unlimited" />
          <span :style="{fontSize:'13px',color:text}">Unlimited pages &amp; links (∞)</span>
        </label>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:4px">
          <button @click="grantUser=null" :style="pagerBtn(false)">Cancel</button>
          <button @click="submitGrant" :disabled="granting"
            :style="{cursor:granting?'wait':'pointer',fontFamily:'Inter,sans-serif',fontSize:'13px',fontWeight:700,padding:'8px 18px',borderRadius:'8px',border:'none',background:'#6D4EE8',color:'#fff',opacity:granting?0.6:1}">
            {{ granting ? 'Granting…' : 'Grant access' }}
          </button>
        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()

const metrics = ref(null)
const users   = ref([])
const total   = ref(0)
const page    = ref(1)
const lastPage = ref(1)
const search  = ref('')
const loading = ref(false)
let searchTimer = null

const expandedId = ref(null)
const userPages  = ref({})

const grantUser = ref(null)
const grantForm = ref({ plan: 'agency', days: null, unlimited: false })
const granting  = ref(false)
const durations = [
  { label: 'Permanent', days: null },
  { label: '30 days', days: 30 },
  { label: '60 days', days: 60 },
  { label: '90 days', days: 90 },
]

const metricCards = computed(() => [
  { label: 'Total users',   value: metrics.value?.total_users   ?? '—', icon: 'bi-people-fill',     color: '#6D4EE8' },
  { label: 'Active trials', value: metrics.value?.active_trials ?? '—', icon: 'bi-stars',           color: '#F59E0B', hint: 'card-free, not yet converted' },
  { label: 'Paying',        value: metrics.value?.paying_users  ?? '—', icon: 'bi-credit-card-fill', color: '#10B981' },
  { label: 'Beta partners', value: metrics.value?.beta_partners ?? '—', icon: 'bi-megaphone-fill',  color: '#A78BFA', hint: 'coaches granting trials' },
])

async function loadMetrics() {
  const { data } = await api.get('/admin/metrics')
  metrics.value = data
}

async function loadUsers() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/users', { params: { q: search.value, page: page.value } })
    users.value   = data.data.map(u => ({ ...u, _saving: false, _pagesLoading: false }))
    total.value   = data.total
    page.value    = data.current_page
    lastPage.value = data.last_page
  } finally {
    loading.value = false
  }
}

function onSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; loadUsers() }, 300)
}

function go(p) {
  if (p < 1 || p > lastPage.value) return
  page.value = p
  loadUsers()
}

async function toggleBeta(u) {
  u._saving = true
  try {
    const { data } = await api.patch(`/admin/users/${u.id}/beta-partner`, {
      is_beta_partner: !u.is_beta_partner,
    })
    Object.assign(u, data, { _saving: false })
    loadMetrics()
  } catch {
    u._saving = false
  }
}

async function toggleExpand(u) {
  if (expandedId.value === u.id) { expandedId.value = null; return }
  expandedId.value = u.id
  if (!userPages.value[u.id]) {
    u._pagesLoading = true
    try {
      const { data } = await api.get(`/admin/users/${u.id}/pages`)
      userPages.value = { ...userPages.value, [u.id]: data }
    } finally {
      u._pagesLoading = false
    }
  }
}

function openGrant(u) {
  grantUser.value = u
  grantForm.value = { plan: u.plan && u.plan !== 'free' ? u.plan : 'agency', days: null, unlimited: false }
}

async function submitGrant() {
  if (!grantUser.value) return
  granting.value = true
  try {
    const { data } = await api.post(`/admin/users/${grantUser.value.id}/grant-plan`, {
      plan: grantForm.value.plan,
      days: grantForm.value.days || undefined,
      unlimited: grantForm.value.unlimited,
    })
    Object.assign(grantUser.value, data)
    grantUser.value = null
    loadMetrics()
  } finally {
    granting.value = false
  }
}

function fmtDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
}

const planLabel = (p) => p === 'pro' ? 'Pro' : p === 'agency' ? 'Agency' : 'Free'

onMounted(() => { loadMetrics(); loadUsers() })

// Theme tokens (match Billing.vue / DashboardLayout)
const card        = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#fff')
const border      = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const divider     = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6')
const text        = computed(() => theme.dark ? '#fff' : '#111827')
const sub         = computed(() => theme.dark ? 'rgba(255,255,255,0.55)' : '#6B7280')
const muted       = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
const inputBg     = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#fff')
const inputBorder = computed(() => theme.dark ? 'rgba(255,255,255,0.12)' : '#E5E7EB')

const thStyle = computed(() => ({ padding: '12px 16px', fontWeight: 600, whiteSpace: 'nowrap' }))
const tdStyle = computed(() => ({ padding: '12px 16px', verticalAlign: 'middle', whiteSpace: 'nowrap' }))

function badge(color) {
  return { fontSize: '10px', fontWeight: 700, color, background: `${color}1f`, borderRadius: '999px', padding: '1px 7px' }
}
function planBadge(plan) {
  const color = plan === 'pro' ? '#D97706' : plan === 'agency' ? '#059669' : '#6D4EE8'
  return { fontSize: '11px', fontWeight: 600, color, background: `${color}1f`, borderRadius: '999px', padding: '2px 9px' }
}
const modalLabel = computed(() => ({ fontSize: '11px', fontWeight: 700, color: sub.value, textTransform: 'uppercase', letterSpacing: '0.05em', margin: '0 0 8px' }))
function chip(active) {
  return {
    cursor: 'pointer', fontFamily: 'Inter,sans-serif', fontSize: '12px', fontWeight: 600,
    padding: '6px 14px', borderRadius: '999px',
    border: active ? '1px solid #6D4EE8' : `1px solid ${inputBorder.value}`,
    background: active ? 'rgba(109,78,232,0.12)' : 'transparent',
    color: active ? '#A78BFA' : sub.value,
  }
}
function pagerBtn(disabled) {
  return {
    cursor: disabled ? 'default' : 'pointer', fontFamily: 'Inter,sans-serif', fontSize: '12px', fontWeight: 600,
    padding: '6px 14px', borderRadius: '8px', border: `1px solid ${inputBorder.value}`,
    background: 'transparent', color: disabled ? muted.value : text.value, opacity: disabled ? 0.5 : 1,
  }
}
</script>
