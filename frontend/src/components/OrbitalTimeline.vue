<template>
  <section style="padding:96px 24px;border-top:1px solid var(--border);overflow:hidden">

    <div class="ot-layout">

      <!-- Left column: text + steps -->
      <div data-aos="fade-right" class="ot-text-col">
        <p style="color:#A78BFA;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">How it works</p>
        <h2 style="font-weight:700;color:var(--text);letter-spacing:-0.03em;line-height:1.1;font-size:clamp(1.8rem,3vw,2.8rem);margin-bottom:20px">
          From your bio link<br/>to a signed subscription
        </h2>
        <p style="color:var(--text-dim);font-size:0.95rem;margin-bottom:40px;line-height:1.6">
          Click a step to learn more
        </p>
        <!-- List of clickable steps -->
        <div style="display:flex;flex-direction:column;gap:10px">
          <button
            v-for="step in steps"
            :key="step.id"
            @click="toggle(step.id)"
            class="ot-step-row"
            :class="{ 'ot-step-row--on': expandedItems[step.id] }"
          >
            <div class="ot-step-icon">
              <i :class="'bi ' + step.icon" style="font-size:14px"></i>
            </div>
            <div style="text-align:left">
              <div style="font-size:10px;color:var(--text-dim);font-weight:600;letter-spacing:0.06em;margin-bottom:1px">{{ step.num }}</div>
              <div style="font-size:13px;font-weight:600;color:var(--text)">{{ step.title }}</div>
            </div>
            <div class="ot-step-badge" :class="'ot-badge--' + step.status">{{ statusLabel(step.status) }}</div>
          </button>
        </div>
      </div>

      <!-- Right column: orbital -->
      <div class="ot-wrap" @click="handleBgClick">
        <!-- Decorative orbs -->
        <div class="ot-orb ot-orb--1"></div>
        <div class="ot-orb ot-orb--2"></div>
        <div class="ot-orb ot-orb--3"></div>
      <div class="ot-scene">

        <!-- Orbit ring -->
        <div class="ot-ring"></div>

        <!-- Center orb -->
        <div class="ot-center">
          <div class="ot-ping p1"></div>
          <div class="ot-ping p2"></div>
          <div class="ot-core"></div>
        </div>

        <!-- Nodes -->
        <div
          v-for="(step, i) in steps"
          :key="step.id"
          class="ot-node"
          :style="nodeStyle(i)"
          @click.stop="toggle(step.id)"
        >
          <div
            class="ot-aura"
            :class="{ 'ot-aura--pulse': pulseEffect[step.id] }"
            :style="auraStyle(step)"
          ></div>

          <div
            class="ot-btn"
            :class="{
              'ot-btn--on':      expandedItems[step.id],
              'ot-btn--related': isRelated(step.id) && !expandedItems[step.id],
            }"
          >
            <i :class="'bi ' + step.icon" style="font-size:15px"></i>
          </div>

          <div class="ot-label" :class="{ 'ot-label--on': expandedItems[step.id] }">
            {{ step.title }}
          </div>

          <Transition name="ot-card">
            <div v-if="expandedItems[step.id]" class="ot-card">
              <div class="ot-card__line"></div>
              <div class="ot-card__top">
                <span class="ot-badge" :class="'ot-badge--' + step.status">{{ statusLabel(step.status) }}</span>
                <span class="ot-card__num">{{ step.num }}</span>
              </div>
              <h4 class="ot-card__title">{{ step.title }}</h4>
              <p class="ot-card__body">{{ step.content }}</p>
              <div class="ot-bar-wrap">
                <div class="ot-bar-top">
                  <span><i class="bi bi-lightning-charge-fill"></i> Effectiveness</span>
                  <span>{{ step.energy }}%</span>
                </div>
                <div class="ot-bar-track">
                  <div class="ot-bar-fill" :style="{ width: step.energy + '%' }"></div>
                </div>
              </div>
              <div v-if="step.relatedIds.length" class="ot-related">
                <div class="ot-related__title"><i class="bi bi-link-45deg"></i> Related steps</div>
                <div class="ot-related__btns">
                  <button
                    v-for="relId in step.relatedIds"
                    :key="relId"
                    class="ot-related__btn"
                    @click.stop="toggle(relId)"
                  >
                    {{ getStep(relId)?.title }}<i class="bi bi-arrow-right-short"></i>
                  </button>
                </div>
              </div>
            </div>
          </Transition>
        </div>

      </div>
    </div>

    </div><!-- /ot-layout -->

  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const steps = [
  {
    id: 1, num: 'Step 1', title: 'Create your page',
    content: "Upload your video, pick your theme, add your links. In under 2 minutes, your VSL page is ready to convert.",
    icon: 'bi-pencil-square', relatedIds: [2], status: 'completed', energy: 100,
  },
  {
    id: 2, num: 'Step 2', title: 'Publish your link',
    content: "Paste your MySocialVSL link in your bio on Instagram, TikTok, Twitter. One link to manage everything.",
    icon: 'bi-share', relatedIds: [1, 3], status: 'completed', energy: 95,
  },
  {
    id: 3, num: 'Step 3', title: 'The fan arrives',
    content: "The fan taps from their feed. Our system detects the in-app browser and bypasses the WebView instantly.",
    icon: 'bi-phone', relatedIds: [2, 4], status: 'completed', energy: 88,
  },
  {
    id: 4, num: 'Step 4', title: 'She sees the VSL',
    content: "Your video plays automatically, muted. She sees you, she trusts you, she's ready to act.",
    icon: 'bi-play-circle-fill', relatedIds: [3, 5], status: 'in-progress', energy: 92,
  },
  {
    id: 5, num: 'Step 5', title: 'She converts',
    content: "She clicks — the native app opens directly. Zero friction. Subscription signed. Revenue collected.",
    icon: 'bi-check-circle-fill', relatedIds: [4], status: 'pending', energy: 80,
  },
]

const rotAngle      = ref(0)
const expandedItems = ref({})
const pulseEffect   = ref({})
const activeId      = ref(null)
let   rotTimer      = null

const startRot = () => {
  rotTimer = setInterval(() => {
    rotAngle.value = (rotAngle.value + 0.3) % 360
  }, 50)
}
const stopRot = () => { clearInterval(rotTimer); rotTimer = null }

onMounted(startRot)
onUnmounted(stopRot)

const nodeStyle = (i) => {
  const total   = steps.length
  const angle   = ((i / total) * 360 + rotAngle.value) % 360
  const rad     = (angle * Math.PI) / 180
  const r       = 200
  const x       = (r * Math.cos(rad)).toFixed(2)
  const y       = (r * Math.sin(rad)).toFixed(2)
  const zIdx    = Math.round(100 + 50 * Math.cos(rad))
  const opacity = Math.max(0.4, Math.min(1, 0.4 + 0.6 * ((1 + Math.sin(rad)) / 2)))
  const isOpen  = expandedItems.value[steps[i].id]
  return {
    transform: `translate(calc(-50% + ${x}px), calc(-50% + ${y}px))`,
    zIndex:    isOpen ? 200 : zIdx,
    opacity:   isOpen ? 1 : opacity,
  }
}

const auraStyle = (step) => {
  const s = step.energy * 0.5 + 40
  return { width: s + 'px', height: s + 'px', left: `-${(s - 40) / 2}px`, top: `-${(s - 40) / 2}px` }
}

const toggle = (id) => {
  const wasOpen = expandedItems.value[id]
  expandedItems.value = {}
  pulseEffect.value   = {}

  if (!wasOpen) {
    expandedItems.value = { [id]: true }
    activeId.value = id
    stopRot()
    const idx = steps.findIndex(s => s.id === id)
    rotAngle.value = ((270 - (idx / steps.length) * 360) % 360 + 360) % 360
    steps.find(s => s.id === id)?.relatedIds.forEach(relId => {
      pulseEffect.value[relId] = true
    })
  } else {
    activeId.value = null
    startRot()
  }
}

const handleBgClick = () => {
  if (!activeId.value) return
  expandedItems.value = {}
  pulseEffect.value   = {}
  activeId.value      = null
  stopRot()
  startRot()
}

const isRelated   = (id) => activeId.value != null && steps.find(s => s.id === activeId.value)?.relatedIds.includes(id)
const getStep     = (id) => steps.find(s => s.id === id)
const statusLabel = (s)  => ({ completed: 'DONE', 'in-progress': 'IN PROGRESS', pending: 'UPCOMING' }[s])
</script>

<style scoped>
.ot-layout {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 64px;
}

.ot-text-col {
  flex: 0 0 340px;
  min-width: 0;
}

.ot-step-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  border-radius: 12px;
  border: 1px solid var(--border);
  background: var(--card-bg);
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
}
.ot-step-row:hover {
  background: var(--pill-bg);
  border-color: var(--border);
}
.ot-step-row--on {
  background: rgba(109,78,232,0.12);
  border-color: rgba(109,78,232,0.5);
}

.ot-step-icon {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: var(--pill-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text2);
  flex-shrink: 0;
}
.ot-step-row--on .ot-step-icon {
  background: #6D4EE8;
  color: #fff;
}

.ot-step-badge {
  margin-left: auto;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 2px 8px;
  border-radius: 4px;
  flex-shrink: 0;
}

.ot-wrap {
  flex: 1;
  min-width: 0;
  position: relative;
  height: 540px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ot-orb {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(72px);
  opacity: 0.45;
}
.ot-orb--1 {
  width: 320px;
  height: 320px;
  background: radial-gradient(circle, #1e3a8a 0%, #0f1e4a 60%, transparent 100%);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation: ot-orb-1-pulse 5s ease-in-out infinite;
}
.ot-orb--2 {
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, #1d4ed8 0%, #1e3a8a 55%, transparent 100%);
  top: 20%;
  right: 15%;
  opacity: 0.3;
  animation: ot-orb-fade 7s ease-in-out infinite 1.5s;
}
.ot-orb--3 {
  width: 160px;
  height: 160px;
  background: radial-gradient(circle, #312e81 0%, #1e1b4b 55%, transparent 100%);
  bottom: 20%;
  left: 15%;
  opacity: 0.35;
  animation: ot-orb-fade 6s ease-in-out infinite 3s;
}

@keyframes ot-orb-1-pulse {
  0%, 100% { opacity: 0.45; transform: translate(-50%, -50%) scale(1); }
  50%       { opacity: 0.6;  transform: translate(-50%, -50%) scale(1.1); }
}
@keyframes ot-orb-fade {
  0%, 100% { opacity: 0.3; }
  50%       { opacity: 0.5; }
}

@media (max-width: 768px) {
  .ot-layout {
    flex-direction: column;
    gap: 32px;
  }
  .ot-text-col {
    flex: none;
    width: 100%;
  }
  .ot-wrap {
    width: 100%;
  }
}
.ot-scene {
  position: relative;
  width: 100%;
  height: 100%;
}

.ot-ring {
  position: absolute;
  top: 50%; left: 50%;
  width: 400px; height: 400px;
  border-radius: 50%;
  border: 1px solid var(--border);
  transform: translate(-50%, -50%);
  pointer-events: none;
}

.ot-center {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  width: 64px; height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, #7C3AED, #3B82F6, #06B6D4);
  animation: ot-glow 2s ease-in-out infinite;
  display: flex; align-items: center; justify-content: center;
  z-index: 10;
  pointer-events: none;
}
.ot-ping {
  position: absolute;
  border-radius: 50%;
  border: 1px solid rgba(255,255,255,0.18);
  animation: ot-ping 2s ease-in-out infinite;
}
.p1 { width: 80px; height: 80px; }
.p2 { width: 96px; height: 96px; animation-delay: 0.5s; opacity: 0.45; }
.ot-core {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: rgba(255,255,255,0.85);
}

.ot-node {
  position: absolute;
  top: 50%; left: 50%;
  width: 40px; height: 40px;
  cursor: pointer;
  transition: opacity 0.3s;
}

.ot-aura {
  position: absolute;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255,255,255,0.14) 0%, transparent 70%);
  pointer-events: none;
}
.ot-aura--pulse { animation: ot-aura-pulse 1s ease-in-out infinite; }

.ot-btn {
  width: 40px; height: 40px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid rgba(255,255,255,0.22);
  background: rgba(8,8,8,0.92);
  color: var(--text2);
  transition: all 0.3s;
  position: relative; z-index: 1;
}
.ot-btn--on {
  background: #fff;
  color: #000;
  border-color: #fff;
  box-shadow: 0 0 22px rgba(255,255,255,0.3);
  transform: scale(1.5);
}
.ot-btn--related {
  background: rgba(255,255,255,0.3);
  color: #000;
  border-color: #fff;
  animation: ot-related-pulse 1s ease-in-out infinite;
}

.ot-label {
  position: absolute;
  top: 48px; left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: rgba(255,255,255,0.5);
  transition: all 0.3s;
  pointer-events: none;
}
.ot-label--on {
  color: #fff;
  transform: translateX(-50%) scale(1.15);
}

.ot-card {
  position: absolute;
  top: 60px; left: 50%;
  transform: translateX(-50%);
  width: 220px;
  background: rgba(5,5,5,0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.7);
}
.ot-card__line {
  position: absolute;
  top: -12px; left: 50%;
  transform: translateX(-50%);
  width: 1px; height: 12px;
  background: rgba(255,255,255,0.3);
}
.ot-card__top {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 10px;
}
.ot-badge {
  font-size: 9px; font-weight: 700; letter-spacing: 0.05em;
  padding: 2px 8px; border-radius: 4px;
}
.ot-badge--completed    { background: rgba(0,0,0,0.5); color: #fff; border: 1px solid rgba(255,255,255,0.22); }
.ot-badge--in-progress  { background: #fff; color: #000; }
.ot-badge--pending      { background: var(--pill-bg); color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.12); }
.ot-card__num   { font-size: 10px; font-family: monospace; color: rgba(255,255,255,0.3); }
.ot-card__title { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px; }
.ot-card__body  { font-size: 11px; color: rgba(255,255,255,0.65); line-height: 1.65; }

.ot-bar-wrap { margin-top: 12px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.07); }
.ot-bar-top  { display: flex; justify-content: space-between; font-size: 10px; color: rgba(255,255,255,0.38); margin-bottom: 6px; }
.ot-bar-track { height: 3px; background: var(--pill-bg); border-radius: 2px; overflow: hidden; }
.ot-bar-fill  { height: 100%; background: linear-gradient(to right, #6D4EE8, #8B6FF0); border-radius: 2px; }

.ot-related { margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.07); }
.ot-related__title { font-size: 9px; color: rgba(255,255,255,0.38); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; }
.ot-related__btns  { display: flex; flex-wrap: wrap; gap: 5px; }
.ot-related__btn {
  background: transparent;
  border: 1px solid rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.55);
  font-size: 10px;
  padding: 3px 8px; border-radius: 4px;
  cursor: pointer;
  display: flex; align-items: center;
  transition: all 0.15s;
}
.ot-related__btn:hover { background: var(--pill-bg); color: #fff; }

/* Card transition */
.ot-card-enter-active,
.ot-card-leave-active { transition: opacity 0.2s, transform 0.2s; }
.ot-card-enter-from,
.ot-card-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(-6px);
}

@keyframes ot-glow {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.65; }
}
@keyframes ot-ping {
  0%   { transform: scale(1); opacity: 0.6; }
  100% { transform: scale(1.45); opacity: 0; }
}
@keyframes ot-aura-pulse {
  0%, 100% { opacity: 0.22; }
  50%       { opacity: 0.55; }
}
@keyframes ot-related-pulse {
  0%, 100% { border-color: rgba(255,255,255,0.75); }
  50%       { border-color: rgba(255,255,255,0.25); }
}
</style>
