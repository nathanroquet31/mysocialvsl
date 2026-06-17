<template>
  <div :style="{display:'flex',flexDirection:'column',alignItems:'center',gap:'14px',userSelect:'none'}">

    <!-- Label phase courante -->
    <div :style="{height:'26px',display:'flex',alignItems:'center',justifyContent:'center'}">
      <div :style="{
        display:'inline-flex',alignItems:'center',gap:'6px',
        padding:'4px 14px',borderRadius:'999px',fontSize:'11px',fontWeight:700,
        transition:'all 0.4s ease',
        background: phaseLabel.bg,
        color: phaseLabel.color,
        border: `1px solid ${phaseLabel.border}`,
      }">
        <span>{{ phaseLabel.icon }}</span>
        <span>{{ phaseLabel.text }}</span>
      </div>
    </div>

    <!-- Phone shell -->
    <div :style="{
      width: `${PHONE_W}px`,
      background:'#111',
      borderRadius:'44px',
      padding:'8px',
      boxShadow:'0 0 0 1px #2a2a2a, 0 32px 80px rgba(0,0,0,0.8), 0 0 60px rgba(109,78,232,0.15)',
      position:'relative',
      flexShrink:0,
    }">
      <div :style="{borderRadius:'37px',overflow:'hidden',background:'#000',position:'relative'}">

        <!-- Notch / status bar -->
        <div :style="{height:'20px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
          <div :style="{width:'48px',height:'5px',background:'#1a1a1a',borderRadius:'999px'}"></div>
        </div>

        <!-- Screen area -->
        <div :style="{position:'relative',width:`${PHONE_W - 16}px`,height:`${SCREEN_H}px`,overflow:'hidden'}">

          <!-- Screens (stacked, one visible at a time) -->
          <img v-for="(src, i) in SCREENS" :key="i" :src="src"
            :style="{
              position:'absolute',inset:0,width:'100%',height:'100%',objectFit:'cover',objectPosition:'top',
              opacity: screenOpacity(i),
              transform: screenTransform(i),
              transition:'opacity 0.35s ease, transform 0.35s ease',
            }" />

          <!-- Age gate overlay (par-dessus VSL page) -->
          <img src="/demo/agegate.png"
            :style="{
              position:'absolute',inset:0,width:'100%',height:'100%',objectFit:'cover',objectPosition:'top',
              opacity: ageGateOpacity,
              transition:'opacity 0.3s ease',
              pointerEvents:'none',
            }" />

          <!-- Mouse cursor SVG -->
          <div :style="{
            position:'absolute',
            left: cursorX + '%',
            top: cursorY + '%',
            transform:'translate(-4px,-2px)',
            transition: cursorTransition,
            zIndex:20,
            pointerEvents:'none',
            opacity: cursorVisible ? 1 : 0,
          }">
            <!-- Cursor arrow -->
            <svg width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg"
              :style="{filter:'drop-shadow(0 1px 3px rgba(0,0,0,0.8))',transform:tapping?'scale(0.85)':'scale(1)',transition:'transform 0.12s ease'}">
              <path d="M2 1L2 17L6.5 12.5L9.5 19.5L12 18.5L9 11.5L16 11.5L2 1Z" fill="white" stroke="rgba(0,0,0,0.6)" stroke-width="1.2" stroke-linejoin="round"/>
            </svg>
            <!-- Tap ripple -->
            <div v-if="tapping" :style="{
              position:'absolute',top:'-6px',left:'-6px',
              width:'30px',height:'30px',borderRadius:'50%',
              background:'rgba(255,255,255,0.35)',
              animation:'tap-ripple 0.35s ease-out forwards',
              pointerEvents:'none',
            }"></div>
          </div>

        </div>

        <!-- Home bar -->
        <div :style="{height:'18px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
          <div :style="{width:'60px',height:'4px',background:'#222',borderRadius:'999px'}"></div>
        </div>
      </div>
    </div>

    <!-- Progress dots -->
    <div :style="{display:'flex',gap:'5px',alignItems:'center'}">
      <div v-for="(_, i) in PHASE_COUNT.value" :key="i"
        :style="{
          width: phase===i ? '16px' : '5px',
          height:'5px',borderRadius:'999px',transition:'all 0.3s ease',
          background: phase===i ? '#6D4EE8' : 'rgba(255,255,255,0.15)',
        }"></div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = withDefaults(defineProps<{ mode?: 'vsl' | 'direct' }>(), { mode: 'vsl' })

const PHONE_W = 240
const SCREEN_H = Math.round(PHONE_W * 19.5 / 9)
const PHASE_COUNT = computed(() => props.mode === 'direct' ? 2 : 4)

const SCREENS = [
  '/demo/instagram.png',
  '/demo/vsl.png',
  '/demo/onlyfans.png',
]

// ─── State ───────────────────────────────────────────────────────────────────
const phase        = ref(0)   // 0=instagram 1=vsl 2=agegate 3=onlyfans
const activeScreen = ref(0)   // which SCREENS[] index is visible
const prevScreen   = ref(-1)  // for exit transition
const ageGateOpacity = ref(0)

const cursorX      = ref(50)
const cursorY      = ref(20)
const cursorVisible = ref(false)
const tapping      = ref(false)
const cursorFast   = ref(false) // instant jump (no transition) when resetting

const cursorTransition = computed(() =>
  cursorFast.value
    ? 'opacity 0.2s ease'
    : 'left 0.9s cubic-bezier(0.25,0.46,0.45,0.94), top 0.9s cubic-bezier(0.25,0.46,0.45,0.94), opacity 0.2s ease'
)

// ─── Screen visibility helpers ───────────────────────────────────────────────
function screenOpacity(i: number) {
  return activeScreen.value === i ? 1 : 0
}
function screenTransform(i: number) {
  if (activeScreen.value === i) return 'translateX(0)'
  return 'translateX(6px)'
}

// ─── Phase labels ────────────────────────────────────────────────────────────
const phaseLabel = computed(() => {
  const all = [
    { icon: '📱', text: 'Profil Instagram',      bg: 'rgba(225,48,108,0.12)',  color:'#E1306C', border:'rgba(225,48,108,0.25)' },
    { icon: '🎬', text: 'Page VSL — Safari',     bg: 'rgba(109,78,232,0.12)', color:'#A78BFA', border:'rgba(109,78,232,0.25)' },
    { icon: '🔞', text: 'Vérification 18+',       bg: 'rgba(239,68,68,0.12)',  color:'#F87171', border:'rgba(239,68,68,0.25)' },
    { icon: '✅', text: 'OnlyFans — Accès privé', bg: 'rgba(0,175,240,0.12)', color:'#00AFF0', border:'rgba(0,175,240,0.25)' },
  ]
  const direct = [
    { icon: '📱', text: 'Profil Instagram',      bg: 'rgba(225,48,108,0.12)',  color:'#E1306C', border:'rgba(225,48,108,0.25)' },
    { icon: '✅', text: 'OnlyFans — Accès privé', bg: 'rgba(0,175,240,0.12)', color:'#00AFF0', border:'rgba(0,175,240,0.25)' },
  ]
  const labels = props.mode === 'direct' ? direct : all
  return labels[phase.value] ?? labels[0]
})

// ─── Tap helper ──────────────────────────────────────────────────────────────
function tap() {
  tapping.value = true
  setTimeout(() => { tapping.value = false }, 320)
}

// ─── Cursor teleport (instant, no animation) ─────────────────────────────────
function teleport(x: number, y: number) {
  cursorFast.value = true
  cursorX.value = x
  cursorY.value = y
  setTimeout(() => { cursorFast.value = false }, 50)
}

// ─── Main sequence ───────────────────────────────────────────────────────────
let timers: ReturnType<typeof setTimeout>[] = []
function t(fn: () => void, ms: number) {
  timers.push(setTimeout(fn, ms))
}
function clearAll() { timers.forEach(clearTimeout); timers = [] }

function runSequence() {
  clearAll()
  phase.value          = 0
  activeScreen.value   = 0
  prevScreen.value     = -1
  ageGateOpacity.value = 0
  cursorVisible.value  = false
  teleport(50, 15)

  // ── Phase 0 : Instagram (commun aux deux modes) ───────────
  t(() => { cursorVisible.value = true }, 600)
  t(() => { cursorX.value = 28; cursorY.value = 47 }, 900)
  t(() => tap(), 2300)
  t(() => { cursorVisible.value = false }, 2500)

  if (props.mode === 'direct') {
    // ── Direct : Instagram → OnlyFans directement ────────────
    t(() => { activeScreen.value = 2; phase.value = 1 }, 2800)
    t(() => runSequence(), 6000)

  } else {
    // ── VSL : Instagram → VSL → age gate → OnlyFans ──────────
    t(() => { activeScreen.value = 1; phase.value = 1 }, 2700)
    t(() => { teleport(50, 15); cursorVisible.value = true }, 3000)
    t(() => { cursorX.value = 50; cursorY.value = 84 }, 3300)
    t(() => tap(), 4900)
    t(() => { cursorVisible.value = false }, 5100)

    t(() => { ageGateOpacity.value = 1; phase.value = 2 }, 5300)
    t(() => { teleport(50, 62); cursorVisible.value = true }, 5500)
    t(() => tap(), 6800)
    t(() => { cursorVisible.value = false }, 7000)

    t(() => { ageGateOpacity.value = 0; activeScreen.value = 2; phase.value = 3 }, 7200)
    t(() => runSequence(), 10500)
  }
}

onMounted(() => { runSequence() })
onUnmounted(() => { clearAll() })
</script>

<style>
@keyframes tap-ripple {
  from { transform: scale(0.3); opacity: 0.8; }
  to   { transform: scale(1.8); opacity: 0; }
}
</style>
