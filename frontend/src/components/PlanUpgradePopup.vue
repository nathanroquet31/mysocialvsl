<template>
  <Transition name="plan-pop">
    <div v-if="show"
      :style="{
        position:'fixed', inset:0, zIndex:1000, display:'flex', alignItems:'center',
        justifyContent:'center', background:'rgba(8,6,22,0.55)', backdropFilter:'blur(4px)',
        fontFamily:'Inter,sans-serif', padding:'20px'
      }"
      @click.self="dismiss">
      <div :style="{
        width:'100%', maxWidth:'380px', background: cardBg, borderRadius:'18px',
        border:`1px solid ${border}`, boxShadow:'0 24px 60px rgba(0,0,0,0.4)',
        padding:'30px 26px 24px', textAlign:'center', position:'relative', overflow:'hidden'
      }">
        <!-- Glow accent -->
        <div :style="{position:'absolute', top:'-60px', left:'50%', transform:'translateX(-50%)', width:'220px', height:'120px', background:'radial-gradient(closest-side, rgba(109,78,232,0.45), transparent)', pointerEvents:'none'}"></div>

        <!-- Icon badge -->
        <div :style="{
          width:'62px', height:'62px', margin:'0 auto 16px', borderRadius:'16px',
          background:'linear-gradient(135deg,#6D4EE8,#A78BFA)', display:'flex',
          alignItems:'center', justifyContent:'center', fontSize:'28px', color:'#fff',
          boxShadow:'0 8px 24px rgba(109,78,232,0.45)', position:'relative'
        }">
          <i class="bi bi-stars"></i>
        </div>

        <h2 :style="{fontSize:'20px', fontWeight:700, color:textPrimary, margin:'0 0 8px', letterSpacing:'-0.02em'}">
          {{ title }}
        </h2>
        <p :style="{fontSize:'13.5px', color:textSecondary, lineHeight:1.5, margin:'0 0 22px'}">
          {{ message }}
        </p>

        <button @click="dismiss"
          :style="{
            width:'100%', padding:'11px', borderRadius:'10px', border:'none', cursor:'pointer',
            background:'linear-gradient(135deg,#6D4EE8,#8B6CF0)', color:'#fff',
            fontSize:'14px', fontWeight:600, fontFamily:'Inter,sans-serif'
          }">
          Let's go
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'

const theme = useThemeStore()

const show = ref(false)
const shownPlan = ref(null)
let pollTimer = null

const CELEBRATED_KEY = 'celebrated_upgrades'
const LABELS = { pro: 'Pro', agency: 'Agency', free: 'Free' }

const cardBg        = computed(() => theme.dark ? '#1a1733' : '#fff')
const border        = computed(() => theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB')
const textPrimary   = computed(() => theme.dark ? '#fff' : '#111827')
const textSecondary = computed(() => theme.dark ? 'rgba(255,255,255,0.6)' : '#4B5563')

const title = computed(() => `You're now on ${LABELS[shownPlan.value] || 'a new plan'} 🎉`)
const message = computed(() =>
  shownPlan.value === 'agency'
    ? 'Welcome to Agency — white-label, deeplink bypass and all premium features are unlocked.'
    : "Welcome aboard — your Pro features are unlocked. Time to make those links convert."
)

function celebrated() {
  try { return JSON.parse(localStorage.getItem(CELEBRATED_KEY) || '[]') } catch (e) { return [] }
}
function markCelebrated(id) {
  const list = celebrated()
  if (!list.includes(id)) {
    list.push(id)
    // keep the list bounded
    localStorage.setItem(CELEBRATED_KEY, JSON.stringify(list.slice(-30)))
  }
}

// Server-driven: an unread plan-upgrade notification means a grant happened.
// Works whether the user was already connected (polling picks it up) or just
// logged in (first poll on mount finds it). Each id is celebrated once.
async function poll() {
  if (show.value) return
  try {
    const { data } = await api.get('/notifications/plan-upgrade')
    if (data.id && !celebrated().includes(data.id)) {
      shownPlan.value = data.plan
      show.value = true
      markCelebrated(data.id)
    }
  } catch (e) { /* silent — no popup */ }
}

function dismiss() {
  show.value = false
}

onMounted(() => {
  poll()
  pollTimer = setInterval(poll, 45000)
})

onBeforeUnmount(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>

<style scoped>
.plan-pop-enter-active, .plan-pop-leave-active { transition: opacity 0.22s ease; }
.plan-pop-enter-active > div, .plan-pop-leave-active > div { transition: transform 0.26s cubic-bezier(0.34,1.56,0.64,1), opacity 0.22s ease; }
.plan-pop-enter-from, .plan-pop-leave-to { opacity: 0; }
.plan-pop-enter-from > div, .plan-pop-leave-to > div { opacity: 0; transform: translateY(16px) scale(0.94); }
</style>
