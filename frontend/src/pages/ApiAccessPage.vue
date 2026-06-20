<template>
  <DashboardLayout title="API Access">

    <!-- Agency-only gate -->
    <div v-if="!isAgency" :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1.5px dashed ${bd}`,borderRadius:'12px',padding:'48px 24px',textAlign:'center',maxWidth:'460px',margin:'40px auto'}">
      <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#6D4EE8,#A78BFA);display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
        <i class="bi bi-key" style="color:#fff;font-size:22px"></i>
      </div>
      <p :style="{fontSize:'17px',fontWeight:700,color:tx,margin:'0 0 6px'}">API Access is an Agency feature</p>
      <p :style="{fontSize:'13px',color:txMuted,margin:'0 0 20px',lineHeight:1.6}">Programmatic access to your links and analytics (REST API v3 + MCP). Upgrade to the Agency plan to mint keys.</p>
      <button @click="router.push('/billing')" :style="btnPrimary">Upgrade to Agency <i class="bi bi-arrow-right"></i></button>
    </div>

    <template v-else>

    <!-- Intro -->
    <div :style="card" style="margin-bottom:20px">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
        <div>
          <p :style="{fontSize:'15px',fontWeight:700,color:tx,margin:'0 0 6px'}">REST API v3</p>
          <p :style="{fontSize:'13px',color:txMuted,margin:0,lineHeight:1.65}">
            Programmatic access to your links and analytics. Authenticate with a Bearer token (JSON responses).
            Your active key also works with the MCP integration.
          </p>
        </div>
        <span style="background:#ECFDF5;color:#059669;border-radius:999px;padding:3px 12px;font-size:11px;font-weight:700;white-space:nowrap;flex-shrink:0">v3 — current</span>
      </div>
      <div :style="{background:theme.dark?'rgba(255,255,255,0.05)':'#F9FAFB',border:`1px solid ${bd}`,borderRadius:'10px',padding:'14px 16px',marginTop:'16px',fontFamily:'ui-monospace,monospace',fontSize:'12px',color:txSub,lineHeight:1.9,overflowX:'auto'}">
        <div>GET {{ apiBase }}/api/v3/me</div>
        <div>GET {{ apiBase }}/api/v3/links</div>
        <div>GET {{ apiBase }}/api/v3/links/{id}</div>
        <div>GET {{ apiBase }}/api/v3/links/{id}/analytics?period=30</div>
        <div style="margin-top:6px;opacity:0.7"># MCP endpoint</div>
        <div>{{ apiBase }}/api/v3/mcp</div>
      </div>
      <p :style="{fontSize:'12px',color:txMuted,margin:'12px 0 0'}">
        Header: <code :style="codeStyle">Authorization: Bearer &lt;your-key&gt;</code>
      </p>
    </div>

    <!-- Freshly minted key (shown once) -->
    <div v-if="mintedKey" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.3);border-radius:12px;padding:20px;margin-bottom:20px">
      <p style="font-size:13px;font-weight:700;color:#059669;margin:0 0 6px"><i class="bi bi-check-circle-fill"></i> Key created — copy it now</p>
      <p :style="{fontSize:'12px',color:txMuted,margin:'0 0 12px'}">For security reasons this key is shown only once. Store it somewhere safe.</p>
      <div style="display:flex;gap:8px;align-items:center">
        <code :style="{flex:1,background:theme.dark?'rgba(0,0,0,0.3)':'#fff',border:`1px solid ${bd}`,borderRadius:'8px',padding:'10px 14px',fontSize:'12px',color:tx,fontFamily:'ui-monospace,monospace',wordBreak:'break-all'}">{{ mintedKey }}</code>
        <button @click="copyKey" :style="btnPrimary" style="white-space:nowrap">{{ copied ? 'Copied ✓' : 'Copy' }}</button>
      </div>
    </div>

    <!-- Mint new key -->
    <div :style="card" style="margin-bottom:20px">
      <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 12px'}">Mint a v3 key</p>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <input v-model="newKeyName" placeholder="Key name (e.g. Zapier, my-script)" maxlength="100"
          :style="{flex:1,minWidth:'220px',border:`1px solid ${bd}`,borderRadius:'8px',padding:'10px 14px',fontSize:'13px',color:tx,outline:'none',background:theme.dark?'rgba(255,255,255,0.06)':'#fff',fontFamily:'Inter,sans-serif',boxSizing:'border-box'}" />
        <button @click="mintKey" :disabled="!newKeyName.trim() || minting" :style="[btnPrimary, {opacity: !newKeyName.trim() || minting ? 0.5 : 1}]">
          {{ minting ? 'Minting…' : '+ Mint v3 key' }}
        </button>
      </div>
      <p v-if="error" style="font-size:12px;color:#EF4444;margin:10px 0 0">{{ error }}</p>
    </div>

    <!-- Keys list -->
    <div :style="card">
      <p :style="{fontSize:'14px',fontWeight:700,color:tx,margin:'0 0 16px'}">Your API keys</p>
      <div v-if="loading" :style="{fontSize:'13px',color:txMuted}">Loading…</div>
      <div v-else-if="keys.length === 0" :style="{textAlign:'center',padding:'28px',color:txMuted,fontSize:'13px'}">
        No API keys yet. Mint one above to get programmatic access.
      </div>
      <div v-else style="display:flex;flex-direction:column;gap:10px">
        <div v-for="k in keys" :key="k.id"
          :style="{display:'flex',alignItems:'center',gap:'14px',padding:'14px 16px',border:`1px solid ${bd}`,borderRadius:'10px'}">
          <div :style="{width:'34px',height:'34px',borderRadius:'9px',background:'#EEE9FF',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
            <i class="bi bi-key" style="color:#6D4EE8;font-size:15px"></i>
          </div>
          <div style="flex:1;min-width:0">
            <p :style="{fontSize:'13px',fontWeight:600,color:tx,margin:0}">{{ k.name }}</p>
            <p :style="{fontSize:'11px',color:txMuted,margin:'2px 0 0'}">
              Created {{ formatDate(k.created_at) }} · {{ k.last_used_at ? 'Last used ' + formatDate(k.last_used_at) : 'Never used' }}
            </p>
          </div>
          <span style="background:#ECFDF5;color:#059669;border-radius:999px;padding:2px 10px;font-size:11px;font-weight:600;flex-shrink:0">Active</span>
          <button @click="revoke(k)" style="background:none;border:none;color:#EF4444;font-size:12px;font-weight:600;cursor:pointer;font-family:Inter,sans-serif;padding:4px 8px;flex-shrink:0">Revoke</button>
        </div>
      </div>
    </div>

    <!-- Legacy notice -->
    <div :style="{background:theme.dark?'rgba(245,158,11,0.07)':'#FFFBEB',border:`1px solid ${theme.dark?'rgba(245,158,11,0.25)':'#FDE68A'}`,borderRadius:'12px',padding:'16px 20px',marginTop:'20px'}">
      <p style="font-size:13px;font-weight:700;color:#D97706;margin:0 0 4px"><i class="bi bi-exclamation-triangle"></i> Legacy API key deprecated</p>
      <p :style="{fontSize:'12px',color:txMuted,margin:0}">The legacy single API key has been replaced by revocable v3 keys above. See <RouterLink to="/dashboard/legacy" style="color:#6D4EE8">Legacy</RouterLink> for migration details.</p>
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

const keys = ref([])
const loading = ref(true)
const newKeyName = ref('')
const minting = ref(false)
const mintedKey = ref('')
const copied = ref(false)
const error = ref('')

const apiBase = window.location.origin

// Theme tokens
const tx      = computed(() => theme.dark ? '#fff' : '#111827')
const txSub   = computed(() => theme.dark ? 'rgba(255,255,255,0.7)' : '#374151')
const txMuted = computed(() => theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280')
const bd      = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const card    = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.04)' : '#fff', border: `1px solid ${theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB'}`, borderRadius: '12px', padding: '24px' }))
const btnPrimary = { background:'#6D4EE8', color:'#fff', border:'none', borderRadius:'8px', padding:'10px 18px', fontSize:'13px', fontWeight:600, cursor:'pointer', fontFamily:'Inter,sans-serif' }
const codeStyle = computed(() => ({ background: theme.dark ? 'rgba(255,255,255,0.08)' : '#F3F4F6', borderRadius:'5px', padding:'2px 7px', fontSize:'11px', color: txSub.value }))

async function load() {
  loading.value = true
  try { keys.value = (await api.get('/api-keys')).data } catch {}
  loading.value = false
}

async function mintKey() {
  if (!newKeyName.value.trim()) return
  minting.value = true
  error.value = ''
  try {
    const { data } = await api.post('/api-keys', { name: newKeyName.value.trim() })
    mintedKey.value = data.plain_text_key
    copied.value = false
    newKeyName.value = ''
    await load()
  } catch {
    error.value = 'Failed to mint key.'
  } finally {
    minting.value = false
  }
}

async function revoke(k) {
  if (!confirm(`Revoke "${k.name}"? Apps using this key will lose access immediately.`)) return
  try { await api.delete(`/api-keys/${k.id}`); await load() } catch {}
}

function copyKey() {
  navigator.clipboard?.writeText(mintedKey.value)
  copied.value = true
  setTimeout(() => copied.value = false, 2000)
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString(undefined, { day:'numeric', month:'short', year:'numeric' })
}

onMounted(load)
</script>
