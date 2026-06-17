<template>
  <DashboardLayout title="Settings">

    <!-- Tabs -->
    <div :style="{display:'flex',gap:'4px',background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',borderRadius:'10px',padding:'3px',width:'fit-content',marginBottom:'24px'}">
      <button v-for="tab in tabs" :key="tab.key" @click="activeTab=tab.key"
        :style="{
          padding:'7px 16px', borderRadius:'8px', border:'none', fontSize:'13px',
          fontWeight: activeTab===tab.key ? '600' : '500', cursor:'pointer',
          background: activeTab===tab.key ? (theme.dark ? 'rgba(255,255,255,0.12)' : '#fff') : 'transparent',
          color: activeTab===tab.key ? (theme.dark ? '#fff' : '#111827') : (theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280'),
          boxShadow: activeTab===tab.key ? '0 1px 3px rgba(0,0,0,0.08)' : 'none',
          transition:'all 0.12s', fontFamily:'Inter,sans-serif'
        }">
        {{ tab.label }}
      </button>
    </div>

    <!-- General tab -->
    <div v-if="activeTab==='general'" style="display:flex;flex-direction:column;gap:20px">

      <!-- Notification preferences -->
      <div :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1px solid ${theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'}`,borderRadius:'12px',padding:'24px'}">
        <p :style="{fontSize:'11px',fontWeight:600,color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 16px'}">Notifications</p>

        <!-- Email digest frequency -->
        <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',padding:'12px 0',borderBottom:`1px solid ${theme.dark?'rgba(255,255,255,0.06)':'#F3F4F6'}`}">
          <div>
            <p :style="{fontSize:'13px',fontWeight:500,color:theme.dark?'#fff':'#111827',margin:'0 0 2px'}">Email digest</p>
            <p :style="{fontSize:'12px',color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',margin:0}">How often we email you a summary of your stats</p>
          </div>
          <div :style="{display:'flex',gap:'4px',background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',borderRadius:'8px',padding:'3px',flexShrink:0}">
            <button v-for="f in [{id:'daily',label:'Daily'},{id:'weekly',label:'Weekly'},{id:'disabled',label:'Off'}]" :key="f.id"
              @click="prefs.email_frequency = f.id"
              :style="{padding:'5px 12px',borderRadius:'6px',border:'none',fontSize:'12px',cursor:'pointer',fontFamily:'Inter,sans-serif',
                fontWeight: prefs.email_frequency===f.id ? '600' : '500',
                background: prefs.email_frequency===f.id ? (theme.dark?'rgba(255,255,255,0.12)':'#fff') : 'transparent',
                color: prefs.email_frequency===f.id ? (theme.dark?'#fff':'#111827') : (theme.dark?'rgba(255,255,255,0.45)':'#6B7280')}">
              {{ f.label }}
            </button>
          </div>
        </div>

        <div v-for="notif in notifications" :key="notif.key"
          :style="{display:'flex',alignItems:'center',justifyContent:'space-between',padding:'12px 0',borderBottom:`1px solid ${theme.dark?'rgba(255,255,255,0.06)':'#F3F4F6'}`}">
          <div>
            <p :style="{fontSize:'13px',fontWeight:500,color:theme.dark?'#fff':'#111827',margin:'0 0 2px'}">{{ notif.label }}</p>
            <p :style="{fontSize:'12px',color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',margin:0}">{{ notif.desc }}</p>
          </div>
          <div @click="notifPrefs[notif.key] = !notifPrefs[notif.key]"
            :style="{
              width:'40px', height:'22px', borderRadius:'999px',
              background: notifPrefs[notif.key] ? '#6D4EE8' : (theme.dark?'rgba(255,255,255,0.12)':'#D1D5DB'),
              cursor:'pointer', position:'relative', transition:'background 0.2s', flexShrink:0
            }">
            <div :style="{
              width:'16px', height:'16px', borderRadius:'50%', background:'#fff',
              position:'absolute', top:'3px',
              left: notifPrefs[notif.key] ? '21px' : '3px',
              transition:'left 0.2s', boxShadow:'0 1px 3px rgba(0,0,0,0.2)'
            }"></div>
          </div>
        </div>
      </div>

      <!-- Language & Timezone -->
      <div :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1px solid ${theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'}`,borderRadius:'12px',padding:'24px'}">
        <p :style="{fontSize:'11px',fontWeight:600,color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 16px'}">Preferences</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div>
            <label :style="{fontSize:'13px',fontWeight:500,color:theme.dark?'rgba(255,255,255,0.7)':'#374151',marginBottom:'6px',display:'block'}">Language</label>
            <select v-model="prefs.language"
              :style="{border:`1px solid ${theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:theme.dark?'#fff':'#111827',width:'100%',outline:'none',fontFamily:'Inter,sans-serif',background:theme.dark?'rgba(255,255,255,0.07)':'#fff',cursor:'pointer'}">
              <option value="en">English</option>
              <option value="fr">French</option>
            </select>
          </div>
          <div>
            <label :style="{fontSize:'13px',fontWeight:500,color:theme.dark?'rgba(255,255,255,0.7)':'#374151',marginBottom:'6px',display:'block'}">Timezone</label>
            <select v-model="prefs.timezone"
              :style="{border:`1px solid ${theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:theme.dark?'#fff':'#111827',width:'100%',outline:'none',fontFamily:'Inter,sans-serif',background:theme.dark?'rgba(255,255,255,0.07)':'#fff',cursor:'pointer'}">
              <option value="UTC">UTC</option>
              <option value="Europe/Paris">Europe/Paris (UTC+1/+2)</option>
              <option value="America/New_York">America/New_York (UTC-5/-4)</option>
              <option value="America/Los_Angeles">America/Los_Angeles (UTC-8/-7)</option>
              <option value="Asia/Tokyo">Asia/Tokyo (UTC+9)</option>
            </select>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;align-items:center;gap:12px;margin-top:16px">
          <span v-if="prefsSaved" style="font-size:12px;color:#059669;font-weight:600"><i class="bi bi-check-circle"></i> Saved</span>
          <button @click="savePrefs" style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:opacity 0.15s,transform 0.15s"
            @mouseenter="e => { e.currentTarget.style.opacity='0.9'; e.currentTarget.style.transform='translateY(-1px)' }"
            @mouseleave="e => { e.currentTarget.style.opacity='1'; e.currentTarget.style.transform='translateY(0)' }">
            Save Preferences
          </button>
        </div>
      </div>
    </div>

    <!-- Security tab -->
    <div v-else-if="activeTab==='security'" style="display:flex;flex-direction:column;gap:20px">

      <!-- 2FA -->
      <div :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1px solid ${theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'}`,borderRadius:'12px',padding:'24px'}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
          <div>
            <p :style="{fontSize:'14px',fontWeight:600,color:theme.dark?'#fff':'#111827',margin:'0 0 6px'}">Two-Factor Authentication</p>
            <p :style="{fontSize:'13px',color:theme.dark?'rgba(255,255,255,0.5)':'#6B7280',margin:0}">Add an extra layer of security to your account.</p>
          </div>
          <span :style="{background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',border:`1px solid ${theme.dark?'rgba(255,255,255,0.1)':'#E5E7EB'}`,borderRadius:'999px',padding:'2px 10px',fontSize:'11px',fontWeight:600,whiteSpace:'nowrap',flexShrink:0}">Coming Soon</span>
        </div>
      </div>

      <!-- Active sessions -->
      <div :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1px solid ${theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'}`,borderRadius:'12px',padding:'24px'}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px">
          <div>
            <p :style="{fontSize:'14px',fontWeight:600,color:theme.dark?'#fff':'#111827',margin:'0 0 6px'}">Active Sessions</p>
            <p :style="{fontSize:'13px',color:theme.dark?'rgba(255,255,255,0.5)':'#6B7280',margin:0}">Devices currently logged into your account. If you don't recognize a session, revoke it and change your password.</p>
          </div>
          <button v-if="sessions.length > 1" @click="revokeOthers"
            style="background:none;border:1px solid #EF4444;color:#EF4444;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:Inter,sans-serif;white-space:nowrap;flex-shrink:0">
            Revoke all others
          </button>
        </div>
        <div v-if="sessionsLoading" :style="{fontSize:'13px',color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF'}">Loading…</div>
        <div v-else style="display:flex;flex-direction:column;gap:10px">
          <div v-for="s in sessions" :key="s.id"
            :style="{display:'flex',alignItems:'center',gap:'14px',padding:'12px 14px',border:`1px solid ${theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'}`,borderRadius:'10px',flexWrap:'wrap'}">
            <div :style="{width:'34px',height:'34px',borderRadius:'9px',background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
              <i :class="deviceIcon(s.user_agent)" :style="{fontSize:'15px',color:theme.dark?'rgba(255,255,255,0.6)':'#6B7280'}"></i>
            </div>
            <div style="flex:1;min-width:160px">
              <p :style="{fontSize:'13px',fontWeight:600,color:theme.dark?'#fff':'#111827',margin:0}">
                {{ describeUA(s.user_agent) }}
                <span v-if="s.is_current" style="background:#ECFDF5;color:#059669;border-radius:999px;padding:1px 8px;font-size:10px;font-weight:700;margin-left:6px">This device</span>
              </p>
              <p :style="{fontSize:'11px',color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',margin:'2px 0 0'}">
                {{ s.ip_address || 'Unknown IP' }} · {{ s.last_used_at ? 'Active ' + relTime(s.last_used_at) : 'Created ' + relTime(s.created_at) }}
              </p>
            </div>
            <button v-if="!s.is_current" @click="revokeSession(s)"
              style="background:none;border:none;color:#EF4444;font-size:12px;font-weight:600;cursor:pointer;font-family:Inter,sans-serif;padding:4px 8px;flex-shrink:0">
              Revoke
            </button>
          </div>
          <p v-if="sessions.length === 0" :style="{textAlign:'center',padding:'16px',color:theme.dark?'rgba(255,255,255,0.3)':'#9CA3AF',fontSize:'13px',margin:0}">No active sessions.</p>
        </div>
      </div>
    </div>

    <!-- Danger Zone tab -->
    <div v-else-if="activeTab==='danger'">
      <div :style="{background:theme.dark?'rgba(239,68,68,0.07)':'#FFF5F5',border:`1px solid ${theme.dark?'rgba(239,68,68,0.25)':'#FCA5A5'}`,borderRadius:'12px',padding:'24px'}">
        <p style="font-size:14px;font-weight:700;color:#DC2626;margin:0 0 8px">Delete Account</p>
        <p :style="{fontSize:'13px',color:theme.dark?'rgba(255,255,255,0.5)':'#6B7280',margin:'0 0 16px',lineHeight:1.65}">
          This action is permanent and cannot be undone. All your pages and data will be deleted immediately and cannot be recovered.
        </p>
        <button v-if="!confirmDelete" @click="confirmDelete=true"
          style="background:#DC2626;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif">
          Delete my account
        </button>
        <div v-else :style="{background:theme.dark?'rgba(255,255,255,0.04)':'#fff',border:`1px solid ${theme.dark?'rgba(239,68,68,0.2)':'#FCA5A5'}`,borderRadius:'8px',padding:'16px'}">
          <p :style="{fontSize:'13px',fontWeight:600,color:theme.dark?'#fff':'#111827',margin:'0 0 12px'}">Are you absolutely sure?</p>
          <p :style="{fontSize:'12px',color:theme.dark?'rgba(255,255,255,0.5)':'#6B7280',margin:'0 0 16px'}">Type <strong>DELETE</strong> to confirm.</p>
          <input v-model="deleteConfirmText" type="text" placeholder="Type DELETE here"
            :style="{border:`1px solid ${theme.dark?'rgba(239,68,68,0.3)':'#FCA5A5'}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:theme.dark?'#fff':'#111827',width:'100%',outline:'none',fontFamily:'Inter,sans-serif',boxSizing:'border-box',marginBottom:'12px',background:theme.dark?'rgba(255,255,255,0.05)':'#fff'}" />
          <div style="display:flex;gap:8px">
            <button @click="deleteAccount" :disabled="deleteConfirmText !== 'DELETE' || deleting"
              style="background:#DC2626;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:opacity 0.15s"
              :style="{opacity: deleteConfirmText !== 'DELETE' || deleting ? 0.5 : 1, cursor: deleteConfirmText !== 'DELETE' ? 'not-allowed' : 'pointer'}">
              {{ deleting ? 'Deleting…' : 'Confirm Delete' }}
            </button>
            <button @click="confirmDelete=false;deleteConfirmText=''"
              :style="{background:'transparent',color:theme.dark?'rgba(255,255,255,0.6)':'#4B5563',border:`1px solid ${theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'}`,borderRadius:'8px',padding:'9px 16px',fontSize:'13px',fontWeight:500,cursor:'pointer',fontFamily:'Inter,sans-serif'}">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'

const auth = useAuthStore()
const theme = useThemeStore()
const router = useRouter()

const activeTab = ref('general')
const confirmDelete = ref(false)
const deleteConfirmText = ref('')
const deleting = ref(false)

const tabs = [
  { key: 'general', label: 'General' },
  { key: 'security', label: 'Security' },
  { key: 'danger', label: 'Danger Zone' },
]

const notifications = [
  { key: 'email_analytics', label: 'Analytics Reports', desc: 'Receive weekly analytics summaries by email' },
  { key: 'email_marketing', label: 'Product Updates', desc: 'News and feature announcements' },
  { key: 'email_billing', label: 'Billing Notifications', desc: 'Invoices and subscription updates' },
]

const notifPrefs = reactive({
  email_analytics: true,
  email_marketing: false,
  email_billing: true,
})

const prefs = reactive({
  language: 'en',
  timezone: 'UTC',
  email_frequency: 'weekly',
})

const prefsSaved = ref(false)

function savePrefs() {
  api.patch('/user', {
    timezone: prefs.timezone,
    preferences: { language: prefs.language, email_frequency: prefs.email_frequency, notifications: { ...notifPrefs } },
  }).then(() => {
    prefsSaved.value = true
    setTimeout(() => prefsSaved.value = false, 2500)
  }).catch(() => {})
}

// ─── Sessions ──────────────────────────────────────────────────────────────────
const sessions = ref([])
const sessionsLoading = ref(true)

async function loadSessions() {
  sessionsLoading.value = true
  try { sessions.value = (await api.get('/sessions')).data } catch {}
  sessionsLoading.value = false
}

async function revokeSession(s) {
  try { await api.delete(`/sessions/${s.id}`); await loadSessions() } catch {}
}

async function revokeOthers() {
  if (!confirm('Revoke all other sessions? Other devices will be logged out.')) return
  try { await api.delete('/sessions'); await loadSessions() } catch {}
}

function describeUA(ua) {
  if (!ua) return 'Unknown device'
  const browser =
    /Edg\//.test(ua) ? 'Edge' :
    /OPR\//.test(ua) ? 'Opera' :
    /Chrome\//.test(ua) ? 'Chrome' :
    /Safari\//.test(ua) && /Version\//.test(ua) ? 'Safari' :
    /Firefox\//.test(ua) ? 'Firefox' : 'Browser'
  const os =
    /Windows/.test(ua) ? 'Windows' :
    /Mac OS X/.test(ua) && !/iPhone|iPad/.test(ua) ? 'macOS' :
    /iPhone|iPad/.test(ua) ? 'iOS' :
    /Android/.test(ua) ? 'Android' :
    /Linux/.test(ua) ? 'Linux' : 'Unknown OS'
  return `${browser} on ${os}`
}

function deviceIcon(ua) {
  if (/iPhone|Android.*Mobile/.test(ua || '')) return 'bi bi-phone'
  if (/iPad|Tablet/.test(ua || '')) return 'bi bi-tablet'
  return 'bi bi-laptop'
}

function relTime(d) {
  const diff = Date.now() - new Date(d).getTime()
  const mins = Math.round(diff / 60000)
  if (mins < 2) return 'just now'
  if (mins < 60) return `${mins} min ago`
  const hours = Math.round(mins / 60)
  if (hours < 24) return `${hours}h ago`
  return `${Math.round(hours / 24)}d ago`
}

async function deleteAccount() {
  if (deleteConfirmText.value !== 'DELETE') return
  deleting.value = true
  try {
    await api.delete('/user')
    await auth.logout()
    router.push('/login')
  } catch {
    deleting.value = false
  }
}

// Auto-save when notification toggles or email frequency change
let notifSaveTimer = null
let hydrated = false
watch([notifPrefs, () => prefs.email_frequency], () => {
  if (!hydrated) return
  clearTimeout(notifSaveTimer)
  notifSaveTimer = setTimeout(savePrefs, 600)
})

onMounted(async () => {
  await auth.fetchMe()
  // Hydrate prefs from the server
  const u = auth.user || {}
  if (u.timezone) prefs.timezone = u.timezone
  const p = u.preferences || {}
  if (p.language) prefs.language = p.language
  if (p.email_frequency) prefs.email_frequency = p.email_frequency
  Object.assign(notifPrefs, p.notifications || {})
  setTimeout(() => hydrated = true, 0)
  loadSessions()
})
</script>
