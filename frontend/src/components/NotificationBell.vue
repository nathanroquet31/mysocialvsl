<template>
  <div style="position:relative" ref="root">
    <!-- Bell button -->
    <button
      @click="toggle"
      :title="'Notifications'"
      :style="{
        position:'relative', width:'38px', height:'38px', borderRadius:'10px',
        border:`1px solid ${border}`, background: open ? hoverBg : 'transparent',
        color: textSecondary, cursor:'pointer', display:'flex', alignItems:'center',
        justifyContent:'center', fontSize:'17px', transition:'all 0.12s', flexShrink:0
      }"
      @mouseenter="e => e.currentTarget.style.background = hoverBg"
      @mouseleave="e => { if(!open) e.currentTarget.style.background='transparent' }">
      <i class="bi bi-bell"></i>
      <span v-if="unreadCount > 0"
        :style="{
          position:'absolute', top:'-4px', right:'-4px', minWidth:'18px', height:'18px',
          padding:'0 5px', borderRadius:'999px', background:'#EF4444', color:'#fff',
          fontSize:'10px', fontWeight:700, display:'flex', alignItems:'center',
          justifyContent:'center', border:`2px solid ${theme.dark ? '#100e22' : '#fff'}`,
          fontFamily:'Inter,sans-serif', lineHeight:1
        }">{{ unreadCount > 9 ? '9+' : unreadCount }}</span>
    </button>

    <!-- Dropdown -->
    <Transition name="notif-pop">
      <div v-if="open"
        :style="{
          position:'absolute', right:0, top:'46px', width:'360px', maxHeight:'460px',
          background: dropdownBg, border:`1px solid ${border}`, borderRadius:'14px',
          boxShadow:'0 12px 40px rgba(0,0,0,0.28)', zIndex:50, overflow:'hidden',
          display:'flex', flexDirection:'column', fontFamily:'Inter,sans-serif'
        }">

        <!-- Header -->
        <div :style="{padding:'14px 16px', borderBottom:`1px solid ${border}`, display:'flex', alignItems:'center', justifyContent:'space-between', flexShrink:0}">
          <h3 :style="{fontSize:'14px', fontWeight:600, color:textPrimary, margin:0}">Notifications</h3>
          <button @click="markAllRead" :disabled="unreadCount === 0"
            :style="{
              background:'none', border:'none', fontSize:'12px', fontWeight:600,
              color: unreadCount === 0 ? textMuted : '#6D4EE8',
              cursor: unreadCount === 0 ? 'default' : 'pointer', padding:0
            }">Mark all read</button>
        </div>

        <!-- Category tabs -->
        <div :style="{display:'flex', gap:'6px', padding:'10px 12px', borderBottom:`1px solid ${border}`, flexShrink:0, overflowX:'auto'}">
          <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
            :style="{
              display:'flex', alignItems:'center', gap:'5px', whiteSpace:'nowrap',
              padding:'4px 10px', borderRadius:'999px', fontSize:'12px', fontWeight:600,
              cursor:'pointer', transition:'all 0.12s',
              border: activeTab === tab.key ? '1px solid transparent' : `1px solid ${border}`,
              background: activeTab === tab.key ? '#6D4EE8' : 'transparent',
              color: activeTab === tab.key ? '#fff' : textSecondary
            }">
            {{ tab.label }}
            <span v-if="tab.count > 0"
              :style="{
                fontSize:'10px', fontWeight:700, borderRadius:'999px', padding:'0 5px',
                background: activeTab === tab.key ? 'rgba(255,255,255,0.25)' : hoverBg,
                color: activeTab === tab.key ? '#fff' : textMuted
              }">{{ tab.count }}</span>
          </button>
        </div>

        <!-- List -->
        <div :style="{overflowY:'auto', flex:1}">
          <div v-if="loading" :style="{padding:'32px', textAlign:'center', color:textMuted, fontSize:'13px'}">
            <i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;display:inline-block"></i>
          </div>

          <div v-else-if="filtered.length === 0" :style="{padding:'40px 20px', textAlign:'center', color:textMuted}">
            <i class="bi bi-bell-slash" style="font-size:24px;opacity:0.5"></i>
            <p :style="{fontSize:'13px', margin:'10px 0 0'}">You're all caught up.</p>
          </div>

          <template v-else>
            <div v-for="group in grouped" :key="group.label">
              <div v-if="group.items.length"
                :style="{padding:'10px 16px 4px', fontSize:'11px', fontWeight:700, letterSpacing:'0.04em', textTransform:'uppercase', color:textMuted}">
                {{ group.label }}
              </div>
              <div v-for="(n, i) in group.items" :key="n.id"
                @click="markRead(n)"
                :style="{
                  display:'flex', gap:'11px', padding:'11px 16px', cursor:'pointer',
                  transition:'background 0.12s', alignItems:'flex-start',
                  background: n.read ? 'transparent' : unreadBg
                }"
                @mouseenter="e => e.currentTarget.style.background = hoverBg"
                @mouseleave="e => e.currentTarget.style.background = n.read ? 'transparent' : unreadBg">
                <!-- Category icon -->
                <span :style="{
                  flexShrink:0, width:'30px', height:'30px', borderRadius:'8px',
                  display:'flex', alignItems:'center', justifyContent:'center', fontSize:'14px',
                  background: catColor(n.category) + '22', color: catColor(n.category)
                }">
                  <i :class="'bi ' + n.icon"></i>
                </span>
                <div style="flex:1;min-width:0">
                  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
                    <h4 :style="{fontSize:'13px', fontWeight:600, color:textPrimary, margin:0, lineHeight:1.35}">{{ n.title }}</h4>
                    <span v-if="!n.read" :style="{flexShrink:0, marginTop:'5px', width:'7px', height:'7px', borderRadius:'999px', background:'#6D4EE8'}"></span>
                  </div>
                  <p :style="{fontSize:'12px', color:textSecondary, margin:'2px 0 0', lineHeight:1.4}">{{ n.description }}</p>
                  <span :style="{fontSize:'11px', color:textMuted, marginTop:'4px', display:'inline-block'}">{{ timeAgo(n.timestamp) }}</span>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'

const theme = useThemeStore()

const open = ref(false)
const loading = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
const activeTab = ref('all')
const root = ref(null)
let pollTimer = null

// ---- Theme tokens (match DashboardLayout) ----
const border        = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const dropdownBg     = computed(() => theme.dark ? '#1a1733' : '#fff')
const hoverBg        = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6')
const unreadBg       = computed(() => theme.dark ? 'rgba(109,78,232,0.08)' : 'rgba(109,78,232,0.05)')
const textPrimary    = computed(() => theme.dark ? '#fff' : '#111827')
const textSecondary  = computed(() => theme.dark ? 'rgba(255,255,255,0.6)' : '#4B5563')
const textMuted      = computed(() => theme.dark ? 'rgba(255,255,255,0.4)' : '#9CA3AF')

// ---- Category styling ----
const CATEGORIES = [
  { key: 'all',      label: 'All' },
  { key: 'security', label: 'Security' },
  { key: 'goals',    label: 'Goals' },
  { key: 'billing',  label: 'Billing' },
  { key: 'account',  label: 'Account' },
]
function catColor(cat) {
  return { security:'#EF4444', goals:'#10B981', billing:'#6D4EE8', account:'#3B82F6' }[cat] || '#6D4EE8'
}

const tabs = computed(() => CATEGORIES.map(c => ({
  ...c,
  count: c.key === 'all'
    ? notifications.value.filter(n => !n.read).length
    : notifications.value.filter(n => n.category === c.key && !n.read).length,
})))

const filtered = computed(() =>
  activeTab.value === 'all'
    ? notifications.value
    : notifications.value.filter(n => n.category === activeTab.value)
)

const grouped = computed(() => {
  const weekAgo = Date.now() - 7 * 24 * 60 * 60 * 1000
  const week = [], earlier = []
  for (const n of filtered.value) {
    (new Date(n.timestamp).getTime() >= weekAgo ? week : earlier).push(n)
  }
  return [
    { label: 'This week', items: week },
    { label: 'Earlier',   items: earlier },
  ]
})

// ---- Date formatting (relative for recent, absolute for old) ----
function timeAgo(ts) {
  const diff = Date.now() - new Date(ts).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'just now'
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return `${hrs}h ago`
  const days = Math.floor(hrs / 24)
  if (days < 7) return `${days}d ago`
  return new Date(ts).toLocaleDateString()
}

// ---- API ----
async function fetchAll() {
  loading.value = true
  try {
    const { data } = await api.get('/notifications')
    notifications.value = data.notifications
    unreadCount.value = data.unread_count
  } catch (e) {
    // silent — bell just stays empty
  } finally {
    loading.value = false
  }
}

async function fetchCount() {
  try {
    const { data } = await api.get('/notifications/unread-count')
    unreadCount.value = data.unread_count
  } catch (e) { /* silent */ }
}

async function markRead(n) {
  if (n.read) return
  n.read = true
  unreadCount.value = Math.max(0, unreadCount.value - 1)
  try { await api.patch(`/notifications/${n.id}/read`) } catch (e) { /* optimistic */ }
}

async function markAllRead() {
  if (unreadCount.value === 0) return
  notifications.value.forEach(n => (n.read = true))
  unreadCount.value = 0
  try { await api.post('/notifications/read-all') } catch (e) { /* optimistic */ }
}

function toggle() {
  open.value = !open.value
  if (open.value) fetchAll()
}

function onClickOutside(e) {
  if (open.value && root.value && !root.value.contains(e.target)) open.value = false
}

onMounted(() => {
  fetchCount()
  pollTimer = setInterval(fetchCount, 60000)
  document.addEventListener('click', onClickOutside)
})

onBeforeUnmount(() => {
  if (pollTimer) clearInterval(pollTimer)
  document.removeEventListener('click', onClickOutside)
})
</script>

<style scoped>
.notif-pop-enter-active, .notif-pop-leave-active {
  transition: opacity 0.18s ease, transform 0.18s ease;
}
.notif-pop-enter-from, .notif-pop-leave-to {
  opacity: 0;
  transform: translateY(-6px) scale(0.97);
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
