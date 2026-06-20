<template>
  <DashboardLayout title="Social Analytics — Instagram">

    <!-- Agency-only gate -->
    <div v-if="!isAgency" :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1.5px dashed ${bd}`,borderRadius:'12px',padding:'48px 24px',textAlign:'center',maxWidth:'460px',margin:'40px auto'}">
      <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#E1306C,#6D4EE8);display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
        <i class="bi bi-graph-up-arrow" style="color:#fff;font-size:22px"></i>
      </div>
      <p :style="{fontSize:'17px',fontWeight:700,color:tx,margin:'0 0 6px'}">Social Analytics is an Agency feature</p>
      <p :style="{fontSize:'13px',color:txMuted,margin:'0 0 20px',lineHeight:1.6}">Track followers, views and reel performance across all your accounts. Upgrade to the Agency plan to unlock it.</p>
      <button @click="router.push('/billing')" :style="btnPrimary">Upgrade to Agency <i class="bi bi-arrow-right"></i></button>
    </div>

    <template v-else>

    <!-- Connect banner -->
    <div :style="{background:'linear-gradient(135deg,rgba(225,48,108,0.1),rgba(109,78,232,0.08))',border:`1px solid ${theme.dark?'rgba(225,48,108,0.25)':'#FBCFE8'}`,borderRadius:'12px',padding:'20px 24px',marginBottom:'20px',display:'flex',alignItems:'center',gap:'16px',flexWrap:'wrap'}">
      <div style="width:42px;height:42px;border-radius:12px;background:#E1306C;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="bi bi-instagram" style="color:#fff;font-size:20px"></i>
      </div>
      <div style="flex:1;min-width:220px">
        <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 3px'}">Connect your Instagram accounts</p>
        <p :style="{fontSize:'12px',color:txMuted,margin:0,lineHeight:1.6}">
          Track followers, views and reel performance per account. Official API connection coming — add your accounts now, stats will sync automatically once enabled.
        </p>
      </div>
      <div style="display:flex;gap:8px;flex-shrink:0">
        <input v-model="newUsername" placeholder="@username" maxlength="100"
          :style="{border:`1px solid ${bd}`,borderRadius:'8px',padding:'9px 14px',fontSize:'13px',color:tx,outline:'none',background:theme.dark?'rgba(255,255,255,0.06)':'#fff',fontFamily:'Inter,sans-serif',width:'170px'}"
          @keyup.enter="addAccount" />
        <button @click="addAccount" :disabled="!newUsername.trim() || adding"
          :style="[btnPrimary, {opacity: !newUsername.trim() || adding ? 0.6 : 1}]">+ Add</button>
      </div>
    </div>

    <p v-if="syncMessage" :style="{fontSize:'12px',color:'#D97706',background:theme.dark?'rgba(245,158,11,0.08)':'#FFFBEB',border:`1px solid ${theme.dark?'rgba(245,158,11,0.25)':'#FDE68A'}`,borderRadius:'10px',padding:'10px 16px',marginBottom:'16px'}">
      <i class="bi bi-info-circle"></i> {{ syncMessage }}
    </p>

    <!-- Overview -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:20px">
      <div :style="card"><p :style="statLabel">Accounts</p><p :style="statValue">{{ accounts.length }}</p></div>
      <div :style="card"><p :style="statLabel">All followers</p><p :style="statValue">{{ totalStat('followers') }}</p></div>
      <div :style="card"><p :style="statLabel">All views</p><p :style="statValue">{{ totalStat('views') }}</p></div>
      <div :style="card"><p :style="statLabel">Posts</p><p :style="statValue">{{ totalStat('posts') }}</p></div>
    </div>

    <!-- Accounts list / Top accounts -->
    <div :style="card" style="margin-bottom:20px">
      <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 16px'}">Top accounts</p>
      <div v-if="loading" :style="{fontSize:'13px',color:txMuted}">Loading…</div>
      <div v-else-if="accounts.length === 0" :style="{textAlign:'center',padding:'28px',color:txMuted,fontSize:'13px'}">
        No accounts connected yet. Add your first Instagram account above.
      </div>
      <div v-else style="display:flex;flex-direction:column;gap:10px">
        <div v-for="acc in sortedAccounts" :key="acc.id"
          :style="{display:'flex',alignItems:'center',gap:'14px',padding:'14px 16px',border:`1px solid ${bd}`,borderRadius:'10px',flexWrap:'wrap'}">
          <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#E1306C,#F77737);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="color:#fff;font-weight:800;font-size:14px">{{ acc.username[0]?.toUpperCase() }}</span>
          </div>
          <div style="flex:1;min-width:140px">
            <p :style="{fontSize:'13px',fontWeight:600,color:tx,margin:0}">@{{ acc.username }}</p>
            <p :style="{fontSize:'11px',color:txMuted,margin:'2px 0 0'}">
              {{ acc.last_synced_at ? 'Synced ' + formatDate(acc.last_synced_at) : 'Not synced yet' }}
              <span v-for="tag in (acc.tags || [])" :key="tag" :style="{background:theme.dark?'rgba(255,255,255,0.08)':'#F3F4F6',borderRadius:'999px',padding:'1px 8px',marginLeft:'6px',fontSize:'10px'}">{{ tag }}</span>
            </p>
          </div>
          <div style="display:flex;gap:18px;flex-shrink:0">
            <div style="text-align:center"><p :style="miniLabel">Followers</p><p :style="miniValue">{{ stat(acc,'followers') }}</p></div>
            <div style="text-align:center"><p :style="miniLabel">Views</p><p :style="miniValue">{{ stat(acc,'views') }}</p></div>
            <div style="text-align:center"><p :style="miniLabel">Last reel</p><p :style="miniValue">{{ stat(acc,'last_reel_views') }}</p></div>
          </div>
          <div style="display:flex;gap:6px;flex-shrink:0">
            <button @click="sync(acc)" :style="btnGhost" title="Sync stats"><i class="bi bi-arrow-repeat"></i></button>
            <button @click="remove(acc)" style="background:none;border:none;color:#EF4444;cursor:pointer;padding:6px;font-size:13px" title="Disconnect"><i class="bi bi-x-lg"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!-- Reels by performance -->
    <div :style="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:8px">
        <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:0}">Reels by performance</p>
        <div :style="{display:'flex',gap:'4px',background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',borderRadius:'8px',padding:'3px'}">
          <button v-for="s in ['Top views','Newest','Oldest']" :key="s" @click="reelSort=s"
            :style="{padding:'5px 12px',borderRadius:'6px',border:'none',fontSize:'12px',fontWeight:reelSort===s?600:500,cursor:'pointer',fontFamily:'Inter,sans-serif',background:reelSort===s?(theme.dark?'rgba(255,255,255,0.12)':'#fff'):'transparent',color:reelSort===s?tx:txMuted}">{{ s }}</button>
        </div>
      </div>
      <div :style="{textAlign:'center',padding:'36px',color:txMuted,fontSize:'13px',border:`1px dashed ${bd}`,borderRadius:'10px',marginTop:'12px'}">
        <i class="bi bi-camera-reels" style="font-size:24px;display:block;margin-bottom:8px;opacity:0.5"></i>
        Reel analytics will appear here once the Instagram API connection is enabled and your accounts are synced.
      </div>
    </div>

    </template>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()
const auth = useAuthStore()
const router = useRouter()
const isAgency = computed(() => auth.user?.plan === 'agency')
const accounts = ref([])
const loading = ref(true)
const newUsername = ref('')
const adding = ref(false)
const syncMessage = ref('')
const reelSort = ref('Top views')

const sortedAccounts = computed(() =>
  [...accounts.value].sort((a, b) => (b.stats?.followers || 0) - (a.stats?.followers || 0))
)

function stat(acc, key) {
  const v = acc.stats?.[key]
  return v == null ? '—' : Intl.NumberFormat().format(v)
}
function totalStat(key) {
  const vals = accounts.value.map(a => a.stats?.[key]).filter(v => v != null)
  if (!vals.length) return '—'
  return Intl.NumberFormat().format(vals.reduce((s, v) => s + v, 0))
}
function formatDate(d) { return new Date(d).toLocaleDateString() }

async function load() {
  loading.value = true
  try { accounts.value = (await api.get('/social-accounts')).data } catch {}
  loading.value = false
}

async function addAccount() {
  const username = newUsername.value.trim()
  if (!username) return
  adding.value = true
  try {
    await api.post('/social-accounts', { platform: 'instagram', username })
    newUsername.value = ''
    await load()
  } catch {} finally { adding.value = false }
}

async function sync(acc) {
  try {
    const { data } = await api.post(`/social-accounts/${acc.id}/sync`)
    if (!data.synced) syncMessage.value = data.message
    else await load()
  } catch {}
}

async function remove(acc) {
  if (!confirm(`Disconnect @${acc.username}?`)) return
  try { await api.delete(`/social-accounts/${acc.id}`); await load() } catch {}
}

const tx      = computed(() => theme.dark ? '#fff' : '#111827')
const txMuted = computed(() => theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280')
const bd      = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const card    = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.04)' : '#fff', border:`1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`, borderRadius:'12px', padding:'22px' }))
const btnPrimary = { background:'#6D4EE8', color:'#fff', border:'none', borderRadius:'8px', padding:'9px 16px', fontSize:'13px', fontWeight:600, cursor:'pointer', fontFamily:'Inter,sans-serif' }
const btnGhost = computed(() => ({ background:'none', border:`1px solid ${bd.value}`, borderRadius:'8px', padding:'6px 10px', fontSize:'13px', color: tx.value, cursor:'pointer' }))
const statLabel = computed(() => ({ fontSize:'11px', fontWeight:600, color: txMuted.value, textTransform:'uppercase', letterSpacing:'0.08em', margin:'0 0 8px' }))
const statValue = computed(() => ({ fontSize:'24px', fontWeight:800, color: tx.value, margin:0, letterSpacing:'-0.02em' }))
const miniLabel = computed(() => ({ fontSize:'10px', color: txMuted.value, margin:'0 0 2px', textTransform:'uppercase', letterSpacing:'0.06em' }))
const miniValue = computed(() => ({ fontSize:'14px', fontWeight:700, color: tx.value, margin:0 }))

onMounted(load)
</script>
