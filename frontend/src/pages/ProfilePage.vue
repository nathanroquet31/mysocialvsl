<template>
  <DashboardLayout title="My Profile">

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start">

      <!-- Left column -->
      <div style="display:flex;flex-direction:column;gap:20px">

        <!-- Profile picture card -->
        <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 16px'}">Profile Picture</p>
          <div style="display:flex;align-items:center;gap:20px">
            <div style="position:relative;cursor:pointer" @click="triggerUpload">
              <div style="width:80px;height:80px;border-radius:50%;background:#EEE9FF;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:#6D4EE8;overflow:hidden;flex-shrink:0;position:relative">
                <img v-if="avatarPreview || auth.user?.avatar_url" :src="avatarPreview || auth.user?.avatar_url" style="width:100%;height:100%;object-fit:cover" />
                <span v-else>{{ initials }}</span>
                <div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.15s;border-radius:50%"
                  @mouseenter="e => e.currentTarget.style.opacity='1'"
                  @mouseleave="e => e.currentTarget.style.opacity='0'">
                  <i class="bi bi-upload" style="font-size:20px;color:#fff"></i>
                </div>
              </div>
            </div>
            <div>
              <p :style="{fontSize:'15px',fontWeight:700,color:text,margin:'0 0 2px'}">{{ auth.user?.name || '—' }}</p>
              <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                <span :style="{fontSize:'11px',color:muted}">@{{ auth.user?.username || auth.user?.email?.split('@')[0] || 'username' }}</span>
                <span style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:999px;padding:1px 8px;font-size:10px;color:#EA580C;font-weight:600">Unverified</span>
              </div>
              <div style="display:flex;gap:8px">
                <button @click="triggerUpload" style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif">Upload Photo</button>
                <button @click="removeAvatar" :style="{background:'transparent',color:sub,border:`1px solid ${border}`,borderRadius:'8px',padding:'7px 14px',fontSize:'12px',fontWeight:500,cursor:'pointer',fontFamily:'Inter,sans-serif'}">Remove</button>
              </div>
            </div>
          </div>
          <input ref="fileInput" type="file" accept="image/*" style="display:none" @change="handleFileChange" />
        </div>

        <!-- Personal information card -->
        <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 16px'}">Personal Information</p>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
              <label :style="{fontSize:'13px',fontWeight:500,color:label,marginBottom:'6px',display:'block'}">First Name</label>
              <input v-model="form.first_name" type="text" placeholder="First name"
                :style="{border:`1px solid ${inputBorder}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:text,width:'100%',outline:'none',fontFamily:'Inter,sans-serif',boxSizing:'border-box',background:inputBg}"
                @focus="e => { e.target.style.borderColor='#6D4EE8'; e.target.style.boxShadow='0 0 0 3px rgba(109,78,232,0.12)' }"
                @blur="e => { e.target.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.target.style.boxShadow='none' }" />
            </div>
            <div>
              <label :style="{fontSize:'13px',fontWeight:500,color:label,marginBottom:'6px',display:'block'}">Last Name</label>
              <input v-model="form.last_name" type="text" placeholder="Last name"
                :style="{border:`1px solid ${inputBorder}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:text,width:'100%',outline:'none',fontFamily:'Inter,sans-serif',boxSizing:'border-box',background:inputBg}"
                @focus="e => { e.target.style.borderColor='#6D4EE8'; e.target.style.boxShadow='0 0 0 3px rgba(109,78,232,0.12)' }"
                @blur="e => { e.target.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.target.style.boxShadow='none' }" />
            </div>
          </div>
          <div style="margin-bottom:18px">
            <label :style="{fontSize:'13px',fontWeight:500,color:label,marginBottom:'6px',display:'block'}">Username</label>
            <input :value="auth.user?.username || auth.user?.email?.split('@')[0]" disabled type="text"
              :style="{border:`1px solid ${border}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:muted,width:'100%',outline:'none',fontFamily:'Inter,sans-serif',boxSizing:'border-box',background:subtleBg,cursor:'not-allowed'}" />
            <p :style="{fontSize:'11px',color:muted,margin:'5px 0 0'}">Username cannot be changed.</p>
          </div>
          <div style="display:flex;justify-content:flex-end">
            <button @click="saveProfile" :disabled="saving"
              style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:opacity 0.15s"
              :style="{opacity: saving ? 0.7 : 1}">
              {{ saving ? 'Saving…' : 'Save Profile' }}
            </button>
          </div>
          <p v-if="saveSuccess" style="font-size:12px;color:#16A34A;margin:8px 0 0;text-align:right">Profile saved successfully.</p>
        </div>

        <!-- Email card -->
        <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 16px'}">Email Address</p>
          <div style="display:flex;gap:10px;align-items:flex-end;margin-bottom:10px">
            <div style="flex:1">
              <label :style="{fontSize:'13px',fontWeight:500,color:label,marginBottom:'6px',display:'block'}">Email</label>
              <input v-model="form.email" type="email"
                :style="{border:`1px solid ${inputBorder}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:text,width:'100%',outline:'none',fontFamily:'Inter,sans-serif',boxSizing:'border-box',background:inputBg}"
                @focus="e => { e.target.style.borderColor='#6D4EE8'; e.target.style.boxShadow='0 0 0 3px rgba(109,78,232,0.12)' }"
                @blur="e => { e.target.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.target.style.boxShadow='none' }" />
            </div>
            <button @click="saveEmail" style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap">Change</button>
          </div>
          <p :style="{fontSize:'11px',color:muted,margin:'0 0 8px'}">Changing your email requires verification.</p>
          <button style="background:transparent;color:#6D4EE8;border:none;font-size:12px;font-weight:500;cursor:pointer;padding:0;font-family:'Inter',sans-serif">Resend Verification Email</button>
        </div>

        <!-- Security card -->
        <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 16px'}">Security</p>
          <div style="display:flex;gap:10px;align-items:flex-end">
            <div style="flex:1">
              <label :style="{fontSize:'13px',fontWeight:500,color:label,marginBottom:'6px',display:'block'}">Password</label>
              <input v-model="form.password" type="password" placeholder="••••••••"
                :style="{border:`1px solid ${inputBorder}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:text,width:'100%',outline:'none',fontFamily:'Inter,sans-serif',boxSizing:'border-box',background:inputBg}"
                @focus="e => { e.target.style.borderColor='#6D4EE8'; e.target.style.boxShadow='0 0 0 3px rgba(109,78,232,0.12)' }"
                @blur="e => { e.target.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.target.style.boxShadow='none' }" />
            </div>
            <button @click="changePassword" style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif">Change</button>
          </div>
          <p v-if="passwordSuccess" style="font-size:12px;color:#16A34A;margin:8px 0 0">Password updated successfully.</p>
        </div>

      </div>

      <!-- Right column -->
      <div style="display:flex;flex-direction:column;gap:20px">

        <!-- Current plan card -->
        <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 14px'}">Current Plan</p>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <span :style="{fontSize:'15px',fontWeight:700,color:text,textTransform:'capitalize'}">{{ auth.user?.plan || 'Free' }}</span>
            <span style="background:#F0FDF4;border:1px solid #BBF7D0;color:#16A34A;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">Active</span>
          </div>
          <p :style="{fontSize:'12px',color:sub,margin:'0 0 16px'}">
            Subscription Renews On: <strong :style="{color:label}">{{ renewalDate }}</strong>
          </p>
          <div style="display:flex;flex-direction:column;gap:8px">
            <RouterLink to="/billing" style="font-size:13px;font-weight:600;color:#6D4EE8;text-decoration:none">Manage Subscription →</RouterLink>
            <button :style="{background:'none',border:'none',fontSize:'12px',color:muted,cursor:'pointer',textAlign:'left',padding:0,fontFamily:'Inter,sans-serif'}">Cancel Plan</button>
          </div>
        </div>

        <!-- Usage card -->
        <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 16px'}">Usage</p>
          <div style="margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
              <span :style="{fontSize:'13px',fontWeight:500,color:label}">Direct Links</span>
              <span :style="{fontSize:'12px',color:sub}">{{ auth.user?.direct_count ?? 0 }} / {{ directLimit }}</span>
            </div>
            <div :style="{height:'5px',background:quotaBg,borderRadius:'999px',overflow:'hidden'}">
              <div :style="{height:'100%',borderRadius:'999px',background:'#6D4EE8',width:quotaPct(auth.user?.direct_count ?? 0, directLimitNum)+'%'}"></div>
            </div>
          </div>
          <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
              <span :style="{fontSize:'13px',fontWeight:500,color:label}">Landing Pages</span>
              <span :style="{fontSize:'12px',color:sub}">{{ auth.user?.pages_count ?? 0 }} / {{ pagesLimit }}</span>
            </div>
            <div :style="{height:'5px',background:quotaBg,borderRadius:'999px',overflow:'hidden'}">
              <div :style="{height:'100%',borderRadius:'999px',background:'#6D4EE8',width:quotaPct(auth.user?.pages_count ?? 0, pagesLimitNum)+'%'}"></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'

const auth = useAuthStore()
const theme = useThemeStore()
const fileInput = ref(null)
const avatarPreview = ref(null)
const saving = ref(false)
const saveSuccess = ref(false)
const passwordSuccess = ref(false)

const form = ref({ first_name: '', last_name: '', email: '', password: '' })
const initials = computed(() => {
  const name = auth.user?.name || ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || '?'
})
const renewalDate = computed(() => {
  if (!auth.user?.subscription_renews_at) return '—'
  return new Date(auth.user.subscription_renews_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
})
const pagesLimitNum = computed(() => auth.user?.plan === 'agency' ? 999 : auth.user?.plan === 'pro' ? 5 : 1)
const directLimitNum = computed(() => auth.user?.plan === 'agency' ? 999 : auth.user?.plan === 'pro' ? 2 : 1)
const pagesLimit = computed(() => auth.user?.plan === 'agency' ? '∞' : pagesLimitNum.value)
const directLimit = computed(() => auth.user?.plan === 'agency' ? '∞' : directLimitNum.value)

// Theme tokens
const card       = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#fff')
const border     = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const text       = computed(() => theme.dark ? '#fff' : '#111827')
const sub        = computed(() => theme.dark ? 'rgba(255,255,255,0.55)' : '#6B7280')
const muted      = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
const label      = computed(() => theme.dark ? 'rgba(255,255,255,0.7)' : '#374151')
const subtleBg   = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#F9FAFB')
const inputBg    = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#fff')
const inputBorder = computed(() => theme.dark ? 'rgba(255,255,255,0.12)' : '#E5E7EB')
const quotaBg    = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#F3F4F6')

function quotaPct(used, limit) {
  if (!limit || limit === 999) return Math.min(100, used > 0 ? 10 : 0)
  return Math.min(100, Math.round((used / limit) * 100))
}
function triggerUpload() { fileInput.value?.click() }
function handleFileChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  avatarPreview.value = URL.createObjectURL(file)
}
function removeAvatar() { avatarPreview.value = null }
async function saveProfile() {
  saving.value = true
  saveSuccess.value = false
  try {
    const payload = {}
    if (form.value.first_name || form.value.last_name) {
      payload.name = [form.value.first_name, form.value.last_name].filter(Boolean).join(' ')
    }
    await api.patch('/user', payload)
    await auth.fetchMe()
    saveSuccess.value = true
    setTimeout(() => { saveSuccess.value = false }, 3000)
  } catch {}
  saving.value = false
}
async function saveEmail() {
  if (!form.value.email) return
  try {
    await api.patch('/user', { email: form.value.email })
    await auth.fetchMe()
  } catch {}
}
async function changePassword() {
  if (!form.value.password) return
  try {
    await api.patch('/user/password', { password: form.value.password })
    form.value.password = ''
    passwordSuccess.value = true
    setTimeout(() => { passwordSuccess.value = false }, 3000)
  } catch {}
}
onMounted(async () => {
  await auth.fetchMe()
  const nameParts = (auth.user?.name || '').split(' ')
  form.value.first_name = nameParts[0] || ''
  form.value.last_name = nameParts.slice(1).join(' ')
  form.value.email = auth.user?.email || ''
})
</script>
