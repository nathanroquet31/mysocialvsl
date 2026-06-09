<template>
  <DashboardLayout title="Domains">

    <div style="display:flex;flex-direction:column;gap:24px;max-width:680px">

      <!-- MySocialVSL subdomain -->
      <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
        <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 14px'}">Your MySocialVSL Domain</p>
        <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',gap:'12px',background:subtleBg,border:`1px solid ${border}`,borderRadius:'8px',padding:'12px 14px',marginBottom:'10px'}">
          <span :style="{fontSize:'14px',fontWeight:600,color:text,fontFamily:'Inter,monospace'}">mysocialvsl.com/{{ auth.user?.username || auth.user?.email?.split('@')[0] || 'you' }}</span>
          <button @click="copySubdomain"
            :style="{display:'flex',alignItems:'center',gap:'6px',background:'transparent',border:`1px solid ${border}`,borderRadius:'7px',padding:'6px 12px',fontSize:'12px',fontWeight:500,color:sub,cursor:'pointer',fontFamily:'Inter,sans-serif',transition:'all 0.15s',flexShrink:0}"
            @mouseenter="e => e.currentTarget.style.borderColor='#6D4EE8'" @mouseleave="e => e.currentTarget.style.borderColor=theme.dark?'rgba(255,255,255,0.08)':'#E5E7EB'">
            <i class="bi bi-clipboard" style="font-size:13px"></i>
            {{ copied ? 'Copied!' : 'Copy' }}
          </button>
        </div>
        <p :style="{fontSize:'12px',color:muted,margin:0}">Your free subdomain. Always active.</p>
      </div>

      <!-- Custom Domain -->
      <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:0}">Custom Domain</p>
          <span v-if="!isPro" :style="{background:theme.dark?'rgba(255,255,255,0.07)':'#F3F4F6',color:muted,border:`1px solid ${border}`,borderRadius:'999px',padding:'2px 8px',fontSize:'11px',fontWeight:600}">Pro / Agency</span>
        </div>

        <!-- Free plan upgrade prompt -->
        <div v-if="!isPro" :style="{background:subtleBg,border:`1.5px dashed ${border}`,borderRadius:'10px',padding:'24px',textAlign:'center'}">
          <div style="width:40px;height:40px;background:#EEE9FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <i class="bi bi-globe" style="font-size:18px;color:#6D4EE8"></i>
          </div>
          <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:'0 0 6px'}">Connect your own domain</p>
          <p :style="{fontSize:'13px',color:sub,margin:'0 0 16px'}">Upgrade to Pro or Agency to use a custom domain like <strong>links.yoursite.com</strong>.</p>
          <RouterLink to="/billing" style="display:inline-flex;align-items:center;gap:6px;background:#6D4EE8;color:#fff;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
            Upgrade to Pro →
          </RouterLink>
        </div>

        <!-- Pro plan domain form -->
        <div v-else>
          <!-- Current custom domain -->
          <div v-if="currentDomain" style="display:flex;align-items:center;justify-content:space-between;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:12px 14px;margin-bottom:16px">
            <div style="display:flex;align-items:center;gap:10px">
              <i class="bi bi-check-lg" style="font-size:14px;color:#16A34A"></i>
              <span style="font-size:13px;font-weight:600;color:#16A34A">{{ currentDomain }}</span>
            </div>
            <button @click="removeDomain" :style="{background:'none',border:'none',cursor:'pointer',color:muted,display:'flex',alignItems:'center',padding:'2px'}"
              @mouseenter="e => e.currentTarget.style.color='#DC2626'" @mouseleave="e => e.currentTarget.style.color=theme.dark?'rgba(255,255,255,0.35)':'#9CA3AF'">
              <i class="bi bi-x-lg" style="font-size:13px"></i>
            </button>
          </div>

          <!-- Add domain input -->
          <div style="display:flex;gap:10px;margin-bottom:20px">
            <input v-model="domainInput" type="text" placeholder="links.yoursite.com"
              :style="{border:`1px solid ${inputBorder}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:text,flex:1,outline:'none',fontFamily:'Inter,sans-serif',background:inputBg}"
              @focus="e => { e.target.style.borderColor='#6D4EE8'; e.target.style.boxShadow='0 0 0 3px rgba(109,78,232,0.12)' }"
              @blur="e => { e.target.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.target.style.boxShadow='none' }"
              @keyup.enter="connectDomain" />
            <button @click="connectDomain" :disabled="!domainInput || connecting"
              style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap;transition:opacity 0.15s"
              :style="{opacity: !domainInput || connecting ? 0.6 : 1}">
              {{ connecting ? 'Connecting…' : 'Connect Domain' }}
            </button>
          </div>

          <!-- DNS instructions -->
          <div :style="{background:subtleBg,border:`1px solid ${border}`,borderRadius:'10px',padding:'18px'}">
            <p :style="{fontSize:'12px',fontWeight:700,color:label,margin:'0 0 12px',display:'flex',alignItems:'center',gap:'6px'}">
              <i class="bi bi-info-circle" style="font-size:14px;color:#6D4EE8"></i>
              DNS Setup Instructions
            </p>
            <div v-for="(step, i) in dnsSteps" :key="i" style="display:flex;gap:12px;margin-bottom:10px;align-items:flex-start">
              <div style="width:22px;height:22px;border-radius:50%;background:#EEE9FF;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#6D4EE8;flex-shrink:0;margin-top:1px">
                {{ i + 1 }}
              </div>
              <p :style="{fontSize:'12px',color:sub,margin:0,lineHeight:1.6}" v-html="step"></p>
            </div>
          </div>
          <p v-if="connectError" style="font-size:12px;color:#DC2626;margin:10px 0 0">{{ connectError }}</p>
          <p v-if="connectSuccess" style="font-size:12px;color:#16A34A;margin:10px 0 0">Domain connected successfully.</p>
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
const domainInput = ref('')
const connecting = ref(false)
const connectError = ref('')
const connectSuccess = ref(false)
const copied = ref(false)
const currentDomain = ref('')

const isPro = computed(() => auth.user?.plan === 'pro' || auth.user?.plan === 'agency')

// Theme tokens
const card       = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#fff')
const border     = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const text       = computed(() => theme.dark ? '#fff' : '#111827')
const sub        = computed(() => theme.dark ? 'rgba(255,255,255,0.55)' : '#6B7280')
const muted      = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
const label      = computed(() => theme.dark ? 'rgba(255,255,255,0.7)' : '#374151')
const subtleBg   = computed(() => theme.dark ? 'rgba(255,255,255,0.03)' : '#F9FAFB')
const inputBg    = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#fff')
const inputBorder = computed(() => theme.dark ? 'rgba(255,255,255,0.12)' : '#E5E7EB')

const dnsSteps = [
  'Add a <strong>CNAME</strong> record pointing to <code style="background:#EEE9FF;color:#6D4EE8;padding:1px 5px;border-radius:4px;font-size:11px">pages.mysocialvsl.com</code>',
  'Wait for DNS propagation (up to 48 hours)',
  'Click "Connect Domain" above to verify',
]

async function copySubdomain() {
  const url = `mysocialvsl.com/${auth.user?.username || auth.user?.email?.split('@')[0] || 'you'}`
  try {
    await navigator.clipboard.writeText(url)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {}
}

async function connectDomain() {
  if (!domainInput.value) return
  connecting.value = true
  connectError.value = ''
  connectSuccess.value = false
  try {
    await api.patch('/user', { custom_domain: domainInput.value })
    currentDomain.value = domainInput.value
    domainInput.value = ''
    connectSuccess.value = true
    await auth.fetchMe()
    setTimeout(() => { connectSuccess.value = false }, 3000)
  } catch {
    connectError.value = 'Could not connect domain. Please check your DNS settings and try again.'
  }
  connecting.value = false
}

async function removeDomain() {
  if (!confirm('Remove custom domain?')) return
  try {
    await api.patch('/user', { custom_domain: null })
    currentDomain.value = ''
    await auth.fetchMe()
  } catch {}
}

onMounted(async () => {
  await auth.fetchMe()
  currentDomain.value = auth.user?.custom_domain || ''
})
</script>
