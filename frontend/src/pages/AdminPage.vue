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
              <th :style="{...thStyle, textAlign:'right'}">Beta partner</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id" :style="{borderTop:`1px solid ${divider}`}">
              <td :style="tdStyle">
                <div :style="{fontWeight:600,color:text,display:'flex',alignItems:'center',gap:'6px'}">
                  {{ u.name || '—' }}
                  <span v-if="u.is_admin" :style="badge('#6D4EE8')">Admin</span>
                  <span v-if="u.is_beta" :style="badge('#059669')">Beta</span>
                </div>
                <div :style="{color:sub,fontSize:'12px'}">{{ u.email }}</div>
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
              <td :style="{...tdStyle, textAlign:'right'}">
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
              </td>
            </tr>
            <tr v-if="!loading && users.length === 0">
              <td colspan="6" :style="{...tdStyle, textAlign:'center', color:muted, padding:'32px'}">No users found.</td>
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
    users.value   = data.data.map(u => ({ ...u, _saving: false }))
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
function pagerBtn(disabled) {
  return {
    cursor: disabled ? 'default' : 'pointer', fontFamily: 'Inter,sans-serif', fontSize: '12px', fontWeight: 600,
    padding: '6px 14px', borderRadius: '8px', border: `1px solid ${inputBorder.value}`,
    background: 'transparent', color: disabled ? muted.value : text.value, opacity: disabled ? 0.5 : 1,
  }
}
</script>
