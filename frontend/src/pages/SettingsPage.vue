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
        <div style="display:flex;justify-content:flex-end;margin-top:16px">
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
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
          <div>
            <p :style="{fontSize:'14px',fontWeight:600,color:theme.dark?'#fff':'#111827',margin:'0 0 6px'}">Active Sessions</p>
            <p :style="{fontSize:'13px',color:theme.dark?'rgba(255,255,255,0.5)':'#6B7280',margin:0}">Manage devices that are currently logged into your account.</p>
          </div>
          <span :style="{background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',color:theme.dark?'rgba(255,255,255,0.4)':'#9CA3AF',border:`1px solid ${theme.dark?'rgba(255,255,255,0.1)':'#E5E7EB'}`,borderRadius:'999px',padding:'2px 10px',fontSize:'11px',fontWeight:600,whiteSpace:'nowrap',flexShrink:0}">Coming Soon</span>
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
import { ref, reactive, onMounted } from 'vue'
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
})

function savePrefs() {
  api.patch('/user', { preferences: { ...prefs, notifications: { ...notifPrefs } } }).catch(() => {})
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

onMounted(async () => {
  await auth.fetchMe()
})
</script>
