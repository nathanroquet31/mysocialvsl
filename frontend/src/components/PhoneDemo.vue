<template>
  <div class="phone-scene">
    <div class="phone-wrap">
      <div class="phone" ref="phoneEl">

        <!-- ── Cadre titanium ── -->
        <div class="frame-top"></div>
        <div class="frame-bottom"></div>
        <div class="frame-left"></div>
        <div class="frame-right"></div>
        <div class="frame-corner tl"></div>
        <div class="frame-corner tr"></div>
        <div class="frame-corner bl"></div>
        <div class="frame-corner br"></div>

        <!-- ── Dos du téléphone (face arrière) ── -->
        <div class="phone-back">
          <div class="back-logo"></div>
          <div class="camera-module">
            <div class="cam cam1"><div class="cam-lens"></div></div>
            <div class="cam cam2"><div class="cam-lens"></div></div>
            <div class="cam cam3"><div class="cam-lens"></div></div>
            <div class="cam-flash"></div>
          </div>
        </div>

        <!-- ── Face avant ── -->
        <div class="phone-front">
          <!-- Épaisseur de l'écran (bord) -->
          <div class="screen-bezel"></div>

          <!-- Écran -->
          <div class="phone-screen">
            <div class="lp">

              <!-- Dynamic Island -->
              <div class="island">
                <div class="island-cam"></div>
                <div class="island-dot"></div>
              </div>

              <!-- Status bar -->
              <div class="status-bar">
                <span class="sb-time">9:41</span>
                <div class="sb-icons">
                  <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <rect x="0" y="3" width="2" height="5" rx="0.5" fill="white" fill-opacity="0.4"/>
                    <rect x="3" y="2" width="2" height="6" rx="0.5" fill="white" fill-opacity="0.6"/>
                    <rect x="6" y="1" width="2" height="7" rx="0.5" fill="white" fill-opacity="0.8"/>
                    <rect x="9" y="0" width="2" height="8" rx="0.5" fill="white"/>
                  </svg>
                  <svg width="10" height="8" viewBox="0 0 10 8" fill="none">
                    <path d="M5 1.5C6.7 1.5 8.2 2.2 9.2 3.3L10 2.5C8.8 1.2 7 0.5 5 0.5C3 0.5 1.2 1.2 0 2.5L0.8 3.3C1.8 2.2 3.3 1.5 5 1.5Z" fill="white" fill-opacity="0.4"/>
                    <path d="M5 3.5C6.1 3.5 7.1 4 7.8 4.8L8.6 4C7.7 3 6.4 2.5 5 2.5C3.6 2.5 2.3 3 1.4 4L2.2 4.8C2.9 4 3.9 3.5 5 3.5Z" fill="white" fill-opacity="0.7"/>
                    <circle cx="5" cy="6.5" r="1" fill="white"/>
                  </svg>
                  <div class="sb-battery">
                    <div class="sb-battery-fill"></div>
                    <div class="sb-battery-cap"></div>
                  </div>
                </div>
              </div>

              <!-- Avatar -->
              <div class="av-wrap">
                <div class="av-ring">
                  <div class="av-inner">
                    <img :src="avatar" :alt="name">
                  </div>
                </div>
                <div class="av-online"></div>
              </div>

              <div class="lp-name">{{ name }}</div>
              <div class="lp-hdl">{{ handle }}</div>
              <div class="lp-bio" v-html="bio"></div>

              <!-- Vidéo -->
              <div class="lp-vid">
                <iframe
                  :src="`https://streamable.com/e/${videoId}?autoplay=1&muted=1&loop=1`"
                  allow="autoplay; fullscreen"
                  allowfullscreen
                />
              </div>

              <button class="lp-cta">{{ cta }}</button>
              <div class="lp-trust">🔒 Sécurisé · Annulation à tout moment</div>

              <!-- Home indicator -->
              <div class="home-indicator"></div>
            </div>
          </div>

          <!-- Reflet écran -->
          <div class="screen-glare"></div>
        </div>

        <!-- Boutons latéraux -->
        <div class="btn-power"></div>
        <div class="btn-mute"></div>
        <div class="btn-vol1"></div>
        <div class="btn-vol2"></div>

      </div>

      <!-- Ombre portée sol -->
      <div class="ground-shadow"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

defineProps({
  name:    { type: String, default: '🌸 Karine' },
  handle:  { type: String, default: '@karinefrenchwoman' },
  bio:     { type: String, default: 'Je partage ici ce qui me ressemble,<br>sans en faire trop 😌' },
  avatar:  { type: String, default: '/karine.jpg' },
  videoId: { type: String, default: '0ed1q5' },
  cta:     { type: String, default: '🔓 Accès privé — Rejoindre' },
})

const phoneEl = ref(null)
let drag = false, sx = 0, sy = 0, ry = -22, rx = 6
const BASE_RY = -22, BASE_RX = 6

function applyTransform(y, x) {
  phoneEl.value.style.transform = `rotateY(${y}deg) rotateX(${x}deg)`
}

function onDown(cx, cy) {
  drag = true; sx = cx; sy = cy
  phoneEl.value.style.animation = 'none'
  phoneEl.value.style.transition = 'none'
}
function onMove(cx, cy) {
  if (!drag) return
  ry += (cx - sx) * 0.35
  rx -= (cy - sy) * 0.15
  ry = Math.max(-50, Math.min(10, ry))
  rx = Math.max(-25, Math.min(25, rx))
  applyTransform(ry, rx)
  sx = cx; sy = cy
}
function onUp() {
  if (!drag) return
  drag = false
  // Snap back smooth
  phoneEl.value.style.transition = 'transform 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)'
  ry = BASE_RY; rx = BASE_RX
  applyTransform(ry, rx)
  setTimeout(() => {
    if (!phoneEl.value) return
    phoneEl.value.style.transition = ''
    phoneEl.value.style.animation = 'phone-float 6s ease-in-out infinite'
  }, 1200)
}

onMounted(() => {
  const el = phoneEl.value
  el.addEventListener('mousedown', e => onDown(e.clientX, e.clientY))
  document.addEventListener('mousemove', e => onMove(e.clientX, e.clientY))
  document.addEventListener('mouseup', onUp)
  el.addEventListener('touchstart', e => onDown(e.touches[0].clientX, e.touches[0].clientY), { passive: true })
  document.addEventListener('touchmove', e => onMove(e.touches[0].clientX, e.touches[0].clientY), { passive: true })
  document.addEventListener('touchend', onUp)
})
onUnmounted(() => {
  const el = phoneEl.value
  if (!el) return
  el.removeEventListener('mousedown', e => onDown(e.clientX, e.clientY))
  document.removeEventListener('mousemove', e => onMove(e.clientX, e.clientY))
  document.removeEventListener('mouseup', onUp)
  document.removeEventListener('touchmove', e => onMove(e.touches[0].clientX, e.touches[0].clientY))
  document.removeEventListener('touchend', onUp)
})
</script>

<style scoped>
/* ══════════════════════════════════════════
   SCENE
══════════════════════════════════════════ */
.phone-scene {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 40px 80px;
}
.phone-wrap {
  perspective: 1600px;
  perspective-origin: 50% 40%;
  position: relative;
  display: inline-block;
}

/* ══════════════════════════════════════════
   PHONE SHELL
══════════════════════════════════════════ */
.phone {
  position: relative;
  width: 320px;
  height: 660px;
  transform-style: preserve-3d;
  transform: rotateY(-22deg) rotateX(6deg);
  animation: phone-float 6s ease-in-out infinite;
  cursor: grab;
  border-radius: 54px;
}
.phone:active { cursor: grabbing; }

@keyframes phone-float {
  0%,100% { transform: rotateY(-22deg) rotateX(6deg) translateY(0px); }
  50%     { transform: rotateY(-22deg) rotateX(6deg) translateY(-18px); }
}

/* ── Cadre titanium ────────────────────── */
/* Utilise des pseudo-elements pour chaque face du cadre avec du depth */
.frame-top, .frame-bottom, .frame-left, .frame-right {
  position: absolute;
  background: linear-gradient(180deg, #4a4a4a 0%, #2a2a2a 40%, #3a3a3a 100%);
  z-index: 10;
}
.frame-top    { top:-3px; left:50px; right:50px; height:3px; border-radius:1px 1px 0 0; }
.frame-bottom { bottom:-3px; left:50px; right:50px; height:3px; border-radius:0 0 1px 1px; }
.frame-left   { left:-3px; top:50px; bottom:50px; width:3px; background:linear-gradient(90deg,#3a3a3a,#2a2a2a); }
.frame-right  { right:-3px; top:50px; bottom:50px; width:3px; background:linear-gradient(270deg,#3a3a3a,#2a2a2a); }

.frame-corner {
  position: absolute; width:54px; height:54px; z-index:10;
  border: 3px solid transparent;
}
.frame-corner.tl { top:-3px; left:-3px; border-top:3px solid #404040; border-left:3px solid #353535; border-radius:54px 0 0 0; }
.frame-corner.tr { top:-3px; right:-3px; border-top:3px solid #404040; border-right:3px solid #454545; border-radius:0 54px 0 0; }
.frame-corner.bl { bottom:-3px; left:-3px; border-bottom:3px solid #353535; border-left:3px solid #353535; border-radius:0 0 0 54px; }
.frame-corner.br { bottom:-3px; right:-3px; border-bottom:3px solid #353535; border-right:3px solid #454545; border-radius:0 0 54px 0; }

/* ── Face avant ────────────────────────── */
.phone-front {
  position: absolute; inset: 0; border-radius: 54px;
  background: #000;
  box-shadow:
    /* Ombre principale profonde */
    28px 50px 100px rgba(0,0,0,0.95),
    14px 25px 50px  rgba(0,0,0,0.7),
    6px  10px 20px  rgba(0,0,0,0.4),
    /* Lueur bleue subtile côté droit */
    -4px 0 40px rgba(0,175,240,0.06),
    /* Highlight bord gauche titanium */
    inset 1px 1px 0 rgba(255,255,255,0.08),
    inset -1px -1px 0 rgba(0,0,0,0.4);
}

/* Bezel très fin autour de l'écran */
.screen-bezel {
  position: absolute; inset: 1px; border-radius: 53px;
  border: 1px solid rgba(255,255,255,0.04);
  pointer-events: none; z-index: 1;
}

/* Reflet écran (glare diagonale) */
.screen-glare {
  position: absolute; inset: 0; border-radius: 54px;
  background: linear-gradient(
    135deg,
    rgba(255,255,255,0.05) 0%,
    rgba(255,255,255,0.02) 20%,
    transparent 50%
  );
  pointer-events: none; z-index: 30;
}

/* ── Dos (dos du phone, visible en 3D) ─── */
.phone-back {
  position: absolute; inset: 0; border-radius: 54px;
  background: linear-gradient(160deg,
    #1c1c1e 0%, #1a1a1c 30%, #161618 60%, #121214 100%);
  transform: translateZ(-3px);
  box-shadow: inset 0 0 40px rgba(0,0,0,0.5);
  z-index: -1;
}

/* Logo Apple */
.back-logo {
  position: absolute; top: 42%; left: 50%; transform: translate(-50%,-50%);
  width: 28px; height: 34px; opacity: 0.12;
  background: radial-gradient(circle, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
  clip-path: path('M14 0 C18 0 20 3 20 3 C20 3 24 1 26 4 C28 7 26 11 26 11 C26 11 28 14 27 17 C26 20 23 22 20 22 C18 22 16 21 14 21 C12 21 10 22 8 22 C5 22 2 20 1 17 C0 14 2 11 2 11 C2 11 0 7 2 4 C4 1 8 3 8 3 C8 3 10 0 14 0 Z');
}

/* Module caméra */
.camera-module {
  position: absolute; top: 24px; left: 24px;
  width: 110px; height: 110px;
  background: linear-gradient(135deg, #1e1e20, #141416);
  border-radius: 28px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.04);
  display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
  padding: 14px;
}
.cam {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, #0a0a0c, #050508);
  border: 1px solid rgba(255,255,255,0.06);
  display: flex; align-items: center; justify-content: center;
  position: relative;
}
.cam-lens {
  width: 22px; height: 22px; border-radius: 50%;
  background: radial-gradient(circle at 35% 35%,
    rgba(60,80,120,0.8) 0%,
    rgba(20,30,60,0.9) 50%,
    rgba(5,8,20,1) 100%);
  border: 1px solid rgba(100,120,180,0.15);
  box-shadow: inset 0 0 8px rgba(0,0,0,0.8);
}
.cam3 { grid-column: 1 / -1; justify-self: start; }
.cam3 .cam-lens { width: 22px; height: 22px; }
.cam-flash {
  position: absolute; bottom: 14px; right: 14px;
  width: 10px; height: 10px; border-radius: 50%;
  background: radial-gradient(circle, #ffd080, #e0a020);
  box-shadow: 0 0 6px rgba(255,200,50,0.3);
}

/* ── Écran ──────────────────────────────── */
.phone-screen {
  position: absolute;
  top: 1px; left: 1px; right: 1px; bottom: 1px;
  border-radius: 53px; overflow: hidden;
  background: #0d0d0d; z-index: 2;
}

/* ══════════════════════════════════════════
   LANDING PAGE CONTENT
══════════════════════════════════════════ */
.lp {
  width: 100%; height: 100%;
  background: #0d0d0d;
  display: flex; flex-direction: column; align-items: center;
  padding: 52px 14px 20px;
  font-family: -apple-system, sans-serif;
  overflow: hidden; position: relative;
}

/* Dynamic Island */
.island {
  position: absolute; top: 12px; left: 50%; transform: translateX(-50%);
  width: 120px; height: 36px;
  background: #000; border-radius: 22px; z-index: 20;
  display: flex; align-items: center; justify-content: flex-end;
  padding-right: 14px; gap: 6px;
  box-shadow: 0 0 0 1px rgba(255,255,255,0.06);
}
.island-cam {
  width: 10px; height: 10px; border-radius: 50%;
  background: radial-gradient(circle at 35% 35%, #1a3a5c, #050810);
  border: 1px solid rgba(60,100,160,0.2);
}
.island-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: #0d1117; border: 1px solid rgba(255,255,255,0.06);
}

/* Status bar */
.status-bar {
  position: absolute; top: 14px; left: 0; right: 0;
  display: flex; justify-content: space-between; align-items: center;
  padding: 0 22px; z-index: 15; pointer-events: none;
}
.sb-time { font-size: 11px; font-weight: 700; color: #fff; letter-spacing: -.3px; }
.sb-icons { display: flex; align-items: center; gap: 5px; }
.sb-battery {
  position: relative; width: 20px; height: 10px;
  border: 1.5px solid rgba(255,255,255,0.7); border-radius: 2.5px;
}
.sb-battery-fill {
  position: absolute; left: 1px; top: 1px; bottom: 1px; width: 70%;
  background: #fff; border-radius: 1px;
}
.sb-battery-cap {
  position: absolute; right: -5px; top: 50%; transform: translateY(-50%);
  width: 3px; height: 6px; background: rgba(255,255,255,0.5); border-radius: 0 2px 2px 0;
}

/* Avatar */
.av-wrap { position: relative; margin-bottom: 8px; flex-shrink: 0; }
.av-ring {
  width: 64px; height: 64px; border-radius: 50%; padding: 2.5px;
  background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
}
.av-inner {
  width: 100%; height: 100%; border-radius: 50%;
  overflow: hidden; border: 2.5px solid #0d0d0d; background: #1a1a1a;
}
.av-inner img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center 15%; display: block;
}
.av-online {
  position: absolute; bottom: 1px; right: 1px;
  width: 15px; height: 15px; border-radius: 50%;
  background: #22c55e; border: 2.5px solid #0d0d0d;
  animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
  0%,100% { box-shadow: 0 0 5px rgba(34,197,94,0.5); }
  50%     { box-shadow: 0 0 14px rgba(34,197,94,1); }
}

.lp-name { font-size: 14.5px; font-weight: 800; color: #fff; letter-spacing: -.4px; margin-bottom: 1px; }
.lp-hdl  { font-size: 9px; color: #444; margin-bottom: 5px; font-weight: 500; }
.lp-bio  { font-size: 8.5px; color: #555; text-align: center; line-height: 1.6; margin-bottom: 10px; max-width: 215px; }

/* Vidéo */
.lp-vid {
  width: 100%; flex: 1; min-height: 0;
  border-radius: 14px; overflow: hidden;
  position: relative; margin-bottom: 10px; background: #000;
}
.lp-vid iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: none; }

/* CTA */
.lp-cta {
  width: 100%; background: #00aff0; color: #fff; border: none;
  padding: 12px; border-radius: 14px;
  font-size: 10.5px; font-weight: 800; letter-spacing: .2px;
  text-align: center; cursor: pointer; flex-shrink: 0;
  box-shadow: 0 4px 20px rgba(0,175,240,0.45);
  animation: cta-pulse 3s ease-in-out infinite;
  margin-bottom: 6px;
}
@keyframes cta-pulse {
  0%,100% { box-shadow: 0 4px 16px rgba(0,175,240,0.35); }
  50%     { box-shadow: 0 6px 32px rgba(0,175,240,0.7); }
}
.lp-trust { font-size: 7px; color: #282828; text-align: center; flex-shrink: 0; letter-spacing: .2px; }

/* Home indicator */
.home-indicator {
  width: 100px; height: 4px; border-radius: 3px;
  background: rgba(255,255,255,0.18);
  margin-top: 8px; flex-shrink: 0;
}

/* ── Boutons ─────────────────────────────── */
.btn-power {
  position: absolute; right: -4px; top: 190px;
  width: 4px; height: 82px; border-radius: 0 3px 3px 0;
  background: linear-gradient(180deg, #4a4a4a 0%, #3a3a3a 40%, #4a4a4a 100%);
  box-shadow: 2px 0 4px rgba(0,0,0,0.5);
}
.btn-mute {
  position: absolute; left: -4px; top: 110px;
  width: 4px; height: 30px; border-radius: 3px 0 0 3px;
  background: linear-gradient(180deg, #4a4a4a, #3a3a3a, #4a4a4a);
  box-shadow: -2px 0 4px rgba(0,0,0,0.5);
}
.btn-vol1 {
  position: absolute; left: -4px; top: 158px;
  width: 4px; height: 50px; border-radius: 3px 0 0 3px;
  background: linear-gradient(180deg, #4a4a4a, #3a3a3a, #4a4a4a);
  box-shadow: -2px 0 4px rgba(0,0,0,0.5);
}
.btn-vol2 {
  position: absolute; left: -4px; top: 222px;
  width: 4px; height: 50px; border-radius: 3px 0 0 3px;
  background: linear-gradient(180deg, #4a4a4a, #3a3a3a, #4a4a4a);
  box-shadow: -2px 0 4px rgba(0,0,0,0.5);
}

/* ── Ombre sol ──────────────────────────── */
.ground-shadow {
  position: absolute;
  bottom: -60px; left: 5%; right: 5%;
  height: 50px;
  background: radial-gradient(ellipse, rgba(0,0,0,0.6) 0%, transparent 70%);
  filter: blur(20px);
  transform: scaleY(0.4) rotateX(80deg);
}
</style>
