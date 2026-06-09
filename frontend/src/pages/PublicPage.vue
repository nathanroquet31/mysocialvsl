<template>
  <div v-if="loading" style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0d0d0d">
    <div style="width:32px;height:32px;border:2px solid rgba(255,255,255,0.15);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite"></div>
  </div>

  <div v-else-if="!page" style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0d0d0d;color:#fff;text-align:center;padding:24px">
    <div>
      <p style="font-size:64px;font-weight:900;margin-bottom:8px">404</p>
      <p style="color:#6b7280;font-size:15px">This page doesn't exist or has been disabled.</p>
    </div>
  </div>

  <!-- ─── CINEMATIC TEMPLATE ──────────────────────────────────────────────── -->
  <div v-else-if="template === 'cinematic'"
    style="position:relative;height:100dvh;min-height:100vh;overflow:hidden;background:#000;font-family:Inter,sans-serif">

    <!-- Video background -->
    <video v-if="page.video_url && isLocalVideo"
      :src="page.video_url" autoplay muted loop playsinline
      style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0" />
    <div v-else-if="page.video_url" style="position:absolute;inset:0;z-index:0;overflow:hidden;pointer-events:none">
      <iframe :src="videoEmbedUrl" allow="autoplay"
        style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:177.8vh;height:100vh;min-width:100%;min-height:56.25vw;border:none;pointer-events:none"></iframe>
    </div>

    <!-- Gradient overlays -->
    <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, transparent 30%, transparent 40%, rgba(0,0,0,0.7) 75%, rgba(0,0,0,0.92) 100%);z-index:1;pointer-events:none"></div>

    <!-- VSL badge top-left -->
    <div v-if="page.video_url" style="position:absolute;top:20px;left:20px;z-index:10;background:rgba(0,0,0,0.5);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border-radius:999px;padding:5px 12px;display:flex;align-items:center;gap:6px;border:1px solid rgba(255,255,255,0.12)">
      <span style="font-size:11px;font-weight:800;color:#fff;letter-spacing:0.08em">▶ VSL</span>
    </div>

    <!-- Online badge top-right -->
    <div v-if="page.online_status" style="position:absolute;top:20px;right:20px;z-index:10" class="badge-online">
      <span class="online-dot"></span>
      <span>Online</span>
    </div>

    <!-- Bottom content -->
    <div style="position:absolute;bottom:0;left:0;right:0;z-index:10;padding:0 24px 44px;max-width:480px;margin:0 auto">

      <!-- Avatar + name + handle -->
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <img v-if="page.avatar_url" :src="page.avatar_url"
          style="width:48px;height:48px;border-radius:50%;border:2px solid rgba(255,255,255,0.3);object-fit:cover;flex-shrink:0" />
        <div>
          <div style="display:flex;align-items:center;gap:6px">
            <h1 style="font-size:20px;font-weight:800;color:#fff;margin:0;letter-spacing:-0.02em">{{ page.model_name }}</h1>
            <span v-if="page.verified_badge" style="color:#60a5fa;font-size:16px;font-weight:700">✓</span>
          </div>
          <p v-if="page.model_handle" style="font-size:13px;color:rgba(255,255,255,0.55);margin:2px 0 0">{{ page.model_handle }}</p>
        </div>
      </div>

      <!-- Bio -->
      <p v-if="page.bio" style="font-size:13px;color:rgba(255,255,255,0.65);line-height:1.55;margin:0 0 16px">{{ page.bio }}</p>

      <!-- Promo -->
      <div v-if="page.promo_text" :style="promoStyle" style="margin-bottom:14px">🔥 {{ page.promo_text }}</div>

      <!-- Countdown -->
      <div v-if="countdown.show" style="display:flex;gap:10px;margin-bottom:16px">
        <div v-for="(unit, i) in countdown.units" :key="i" :style="countdownUnitStyle">
          <p style="font-size:22px;font-weight:800;line-height:1;margin:0">{{ unit.value }}</p>
          <p style="font-size:9px;text-transform:uppercase;letter-spacing:0.1em;margin:4px 0 0;opacity:0.45">{{ unit.label }}</p>
        </div>
      </div>

      <!-- Links -->
      <div style="display:flex;flex-direction:column;gap:10px">
        <button v-for="link in page.links" :key="link.id"
          @click="handleLinkClick(link)"
          :style="getLinkStyle(link)"
          @mousedown="e => { e.currentTarget.style.transform='scale(0.97)'; e.currentTarget.style.opacity='0.9' }"
          @mouseup="e => { e.currentTarget.style.transform='scale(1)'; e.currentTarget.style.opacity='1' }"
          @mouseleave="e => { e.currentTarget.style.transform='scale(1)'; e.currentTarget.style.opacity='1' }">
          <span v-if="getLinkIcon(link)" v-html="getLinkIcon(link)" style="display:flex;align-items:center;flex-shrink:0"></span>
          <span>{{ link.label || getLinkDefaultLabel(link) }}</span>
          <span style="margin-left:auto;opacity:0.5;font-size:14px">›</span>
        </button>
      </div>

      <!-- Branding -->
      <a href="/" style="display:block;text-align:center;font-size:11px;color:rgba(255,255,255,0.18);margin-top:20px;text-decoration:none;letter-spacing:0.05em">
        Powered by <strong style="color:rgba(255,255,255,0.3)">MySocialVSL</strong>
      </a>
    </div>
  </div>

  <!-- ─── CLASSIC / MINIMAL / NEON TEMPLATES ──────────────────────────────── -->
  <div v-else :style="pageStyle">

    <!-- Background image overlay -->
    <div v-if="page.bg_image_url"
      :style="{position:'fixed',inset:0,backgroundImage:`url(${page.bg_image_url})`,backgroundSize:'cover',backgroundPosition:'center',opacity:0.18,zIndex:0,pointerEvents:'none'}">
    </div>

    <!-- Gradient overlay for depth -->
    <div v-if="template !== 'minimal'" style="position:fixed;inset:0;background:linear-gradient(to bottom,transparent 50%,rgba(0,0,0,0.6) 100%);z-index:0;pointer-events:none"></div>

    <!-- Content -->
    <div style="position:relative;z-index:1;max-width:480px;margin:0 auto;padding:48px 20px 80px;display:flex;flex-direction:column;align-items:center">

      <!-- Online badge -->
      <div v-if="page.online_status" class="badge-online" style="margin-bottom:18px">
        <span class="online-dot"></span>
        <span>Online now</span>
      </div>

      <!-- Avatar -->
      <div v-if="page.show_avatar !== false" style="margin-bottom:16px;position:relative">
        <div :style="avatarStyle">
          <img v-if="page.avatar_url" :src="page.avatar_url" style="width:100%;height:100%;object-fit:cover" />
          <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:36px">👤</div>
        </div>
        <!-- Online dot on avatar -->
        <div v-if="page.online_status" style="position:absolute;bottom:5px;right:5px;width:16px;height:16px;background:#10b981;border-radius:50%;border:3px solid var(--bg)"></div>
      </div>

      <!-- Name + verified -->
      <div style="display:flex;align-items:center;gap:7px;margin-bottom:4px">
        <h1 :style="nameStyle">{{ page.model_name }}</h1>
        <span v-if="page.verified_badge" :style="badgeStyle">✓</span>
      </div>

      <!-- Handle -->
      <p v-if="page.model_handle" :style="handleStyle">{{ page.model_handle }}</p>

      <!-- Location / response -->
      <div v-if="page.location || page.response_time" style="display:flex;gap:12px;margin-bottom:12px;flex-wrap:wrap;justify-content:center">
        <span v-if="page.location" :style="metaStyle">📍 {{ page.location }}</span>
        <span v-if="page.response_time" :style="metaStyle">💬 {{ page.response_time }}</span>
      </div>

      <!-- Bio -->
      <p v-if="page.bio" :style="bioStyle">{{ page.bio }}</p>

      <!-- Promo banner -->
      <div v-if="page.promo_text" :style="promoStyle">
        🔥 {{ page.promo_text }}
      </div>

      <!-- VSL Video -->
      <div v-if="page.video_url" style="width:100%;margin-bottom:24px;position:relative">
        <div :style="videoWrapStyle">
          <video v-if="isLocalVideo" :src="page.video_url" autoplay muted loop playsinline
            style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border:none"></video>
          <iframe v-else :src="videoEmbedUrl" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen
            style="position:absolute;inset:0;width:100%;height:100%;border:none"></iframe>
        </div>
        <!-- VSL pill -->
        <div style="position:absolute;top:14px;left:14px;background:rgba(0,0,0,0.65);backdrop-filter:blur(10px);border-radius:999px;padding:4px 12px;display:flex;align-items:center;gap:6px;z-index:2">
          <div style="width:7px;height:7px;border-radius:50%;background:#ef4444;animation:pulse-rec 1.4s ease-in-out infinite"></div>
          <span style="font-size:11px;font-weight:800;color:#fff;letter-spacing:0.08em">VSL</span>
        </div>
      </div>

      <!-- Countdown -->
      <div v-if="countdown.show" style="display:flex;gap:10px;margin-bottom:24px">
        <div v-for="(unit, i) in countdown.units" :key="i" :style="countdownUnitStyle">
          <p style="font-size:26px;font-weight:800;line-height:1;margin:0">{{ unit.value }}</p>
          <p style="font-size:9px;text-transform:uppercase;letter-spacing:0.1em;margin:4px 0 0;opacity:0.45">{{ unit.label }}</p>
        </div>
      </div>

      <!-- Links -->
      <div style="width:100%;display:flex;flex-direction:column;gap:12px">
        <button v-for="link in page.links" :key="link.id"
          @click="handleLinkClick(link)"
          :style="getLinkStyle(link)"
          @mousedown="e => { e.currentTarget.style.transform='scale(0.97)'; e.currentTarget.style.opacity='0.9' }"
          @mouseup="e => { e.currentTarget.style.transform='scale(1)'; e.currentTarget.style.opacity='1' }"
          @mouseleave="e => { e.currentTarget.style.transform='scale(1)'; e.currentTarget.style.opacity='1' }">
          <span v-if="getLinkIcon(link)" v-html="getLinkIcon(link)" style="display:flex;align-items:center;flex-shrink:0"></span>
          <span>{{ link.label || getLinkDefaultLabel(link) }}</span>
          <span style="margin-left:auto;opacity:0.5;font-size:14px">›</span>
        </button>
      </div>

      <!-- Branding -->
      <a href="/" style="font-size:11px;color:rgba(255,255,255,0.18);margin-top:48px;text-decoration:none;letter-spacing:0.05em">
        Powered by <strong style="color:rgba(255,255,255,0.3)">MySocialVSL</strong>
      </a>

    </div>
  </div>

  <!-- Age gate modal -->
  <Teleport to="body">
    <div v-if="ageModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.88);z-index:100;display:flex;align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(4px)">
      <div style="background:#111;border-radius:24px;padding:40px 28px;max-width:360px;width:100%;text-align:center;border:1px solid rgba(255,255,255,0.08);box-shadow:0 24px 60px rgba(0,0,0,0.6)">
        <div style="font-size:56px;margin-bottom:12px">🔞</div>
        <h2 style="font-size:22px;font-weight:800;color:#fff;margin-bottom:8px">18+ Content</h2>
        <p style="color:#6b7280;font-size:14px;line-height:1.65;margin-bottom:28px">
          This content is for adults only.<br/>Please confirm you are 18 years or older.
        </p>
        <button @click="confirmAge"
          :style="{width:'100%',padding:'15px',borderRadius:'14px',border:'none',cursor:'pointer',background:pendingLink?.btn_color||page?.btn_color||'#6D4EE8',color:'#fff',fontSize:'15px',fontWeight:700,marginBottom:'12px',fontFamily:'Inter,sans-serif',transition:'opacity 0.15s'}"
          onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
          I confirm I am 18+ ✓
        </button>
        <button @click="ageModal=false" style="background:none;border:none;color:#6b7280;font-size:13px;cursor:pointer;font-family:Inter,sans-serif;padding:6px">
          Go back
        </button>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/lib/axios'

const route = useRoute()
const page = ref(null)
const loading = ref(true)
const ageModal = ref(false)
const pendingLink = ref(null)
let countdownTimer = null
const now = ref(new Date())

// ─── Template helpers ──────────────────────────────────────────────────────────
const template = computed(() => page.value?.template || 'classic')

const pageStyle = computed(() => {
  const t = template.value
  const bg = page.value?.bg_color || '#0d0d0d'
  if (t === 'minimal') return { minHeight:'100vh', background:'#F9FAFB', fontFamily:'Inter,sans-serif', position:'relative' }
  if (t === 'neon')    return { minHeight:'100vh', background:bg, fontFamily:'Inter,sans-serif', position:'relative' }
  return { minHeight:'100vh', background:bg, fontFamily:'Inter,sans-serif', position:'relative' }
})

const avatarStyle = computed(() => {
  const t = template.value
  const base = { width:'96px', height:'96px', borderRadius:'50%', overflow:'hidden', background:'#222' }
  if (t === 'minimal') return { ...base, border:'3px solid #E5E7EB', background:'#F3F4F6' }
  if (t === 'neon')    return { ...base, border:'3px solid', borderColor: page.value?.btn_color||'#A78BFA', boxShadow:`0 0 20px ${page.value?.btn_color||'#A78BFA'}55` }
  return { ...base, border:'3px solid rgba(255,255,255,0.12)' }
})

const nameStyle = computed(() => {
  const t = template.value
  const base = { fontSize:'24px', fontWeight:800, textAlign:'center', margin:0, letterSpacing:'-0.02em' }
  if (t === 'minimal') return { ...base, color:'#111827' }
  return { ...base, color:'#fff' }
})

const handleStyle = computed(() => {
  const t = template.value
  const base = { fontSize:'14px', marginBottom:'10px' }
  if (t === 'minimal') return { ...base, color:'#6B7280' }
  return { ...base, color:'rgba(255,255,255,0.4)' }
})

const bioStyle = computed(() => {
  const t = template.value
  const base = { fontSize:'14px', textAlign:'center', maxWidth:'320px', lineHeight:1.65, marginBottom:'16px' }
  if (t === 'minimal') return { ...base, color:'#4B5563' }
  return { ...base, color:'rgba(255,255,255,0.55)' }
})

const metaStyle = computed(() => {
  const t = template.value
  if (t === 'minimal') return { fontSize:'12px', color:'#9CA3AF' }
  return { fontSize:'12px', color:'rgba(255,255,255,0.35)' }
})

const badgeStyle = computed(() => {
  const t = template.value
  const btn = page.value?.btn_color || '#6D4EE8'
  return { color: t === 'minimal' ? btn : '#60a5fa', fontSize:'16px', fontWeight:700 }
})

const promoStyle = computed(() => ({
  background: 'rgba(239,68,68,0.12)',
  border: '1px solid rgba(239,68,68,0.25)',
  borderRadius: '12px',
  padding: '10px 18px',
  marginBottom: '20px',
  fontSize: '13px',
  color: '#f87171',
  fontWeight: 600,
  textAlign: 'center',
}))

const videoWrapStyle = computed(() => ({
  borderRadius: template.value === 'minimal' ? '16px' : '20px',
  overflow: 'hidden',
  background: '#000',
  boxShadow: template.value === 'neon'
    ? `0 8px 40px ${page.value?.btn_color||'#A78BFA'}44`
    : '0 8px 40px rgba(0,0,0,0.5)',
  position: 'relative',
  aspectRatio: '9/16',
  maxHeight: '520px',
}))

const countdownUnitStyle = computed(() => {
  const t = template.value
  if (t === 'minimal') return { background:'#F3F4F6', borderRadius:'12px', padding:'12px 16px', textAlign:'center', minWidth:'56px', color:'#111827' }
  return { background:'rgba(255,255,255,0.08)', border:'1px solid rgba(255,255,255,0.06)', borderRadius:'12px', padding:'12px 16px', textAlign:'center', minWidth:'56px', color:'#fff' }
})

// ─── Link styles ───────────────────────────────────────────────────────────────
function getLinkStyle(link) {
  const t = template.value
  const btnColor = link.btn_color || page.value?.btn_color || '#6D4EE8'
  const base = {
    width:'100%', padding:'15px 20px', border:'none', cursor:'pointer',
    fontSize:'15px', fontWeight:700, fontFamily:'Inter,sans-serif',
    transition:'transform 0.12s, opacity 0.12s, box-shadow 0.12s',
    letterSpacing:'-0.01em', display:'flex', alignItems:'center', gap:'12px',
    position:'relative', overflow:'hidden',
  }

  if (t === 'minimal') return {
    ...base,
    borderRadius:'14px',
    background:'#fff',
    color:'#111827',
    boxShadow:'0 1px 4px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.06)',
  }

  if (t === 'neon') return {
    ...base,
    borderRadius:'14px',
    background:'transparent',
    color:'#fff',
    border:`2px solid ${btnColor}`,
    boxShadow:`0 0 16px ${btnColor}44`,
  }

  // classic (default)
  return {
    ...base,
    borderRadius:'14px',
    background: btnColor,
    color: '#fff',
    boxShadow:`0 4px 20px ${btnColor}44`,
  }
}

const socialSvgs = {
  onlyfans:  { color:'#00AFF0', svg:`<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 5.838a6.162 6.162 0 1 1 0 12.324A6.162 6.162 0 0 1 12 5.838zm0 2.456a3.706 3.706 0 1 0 0 7.412 3.706 3.706 0 0 0 0-7.412z"/></svg>` },
  mym:       { color:'#FFD700', svg:`<svg viewBox="0 0 24 24" fill="black" width="18" height="18"><text x="3" y="18" font-size="15" font-weight="900" font-family="Arial">M</text></svg>` },
  instagram: { color:'#E1306C', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="white" stroke="none"/></svg>` },
  tiktok:    { color:'#010101', svg:`<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>` },
  twitter:   { color:'#000000', svg:`<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>` },
  telegram:  { color:'#26A5E4', svg:`<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>` },
  youtube:   { color:'#FF0000', svg:`<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M23 7s-.3-2-1.2-2.7c-1.1-1.2-2.4-1.2-3-1.3C16.6 3 12 3 12 3s-4.6 0-6.8.1c-.6.1-1.9.1-3 1.3C1.3 5 1 7 1 7S.7 9.2.7 11.5v2.1c0 2.2.3 4.4.3 4.4s.3 2 1.2 2.7c1.1 1.2 2.6 1.1 3.3 1.2C7.5 22 12 22 12 22s4.6 0 6.8-.2c.6-.1 1.9-.1 3-1.3.9-.7 1.2-2.7 1.2-2.7s.3-2.2.3-4.4v-2.1C23.3 9.2 23 7 23 7zM9.7 15.5V8.4l8.1 3.6-8.1 3.5z"/></svg>` },
  twitch:    { color:'#9146FF', svg:`<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>` },
  snapchat:  { color:'#FFFC00', html:`<i class="bi bi-snapchat" style="color:#000;font-size:18px;line-height:1"></i>` },
  classic:   { color:'#6D4EE8', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="18" height="18"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>` },
  tips:      { color:'#F59E0B', svg:`<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/><circle cx="12" cy="12" r="4" fill="white"/></svg>` },
  product:   { color:'#10B981', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="18" height="18"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>` },
}

function getLinkIcon(link) {
  const s = socialSvgs[link.type]
  if (!s) return null
  const inner = s.html || s.svg
  return `<div style="width:28px;height:28px;border-radius:7px;background:${s.color};display:flex;align-items:center;justify-content:center;flex-shrink:0">${inner}</div>`
}

const linkDefaultLabels = {
  onlyfans:'Subscribe on OnlyFans', mym:'Subscribe on MYM',
  instagram:'Follow on Instagram', tiktok:'Follow on TikTok',
  twitter:'Follow on X', telegram:'Join Telegram',
  youtube:'Subscribe on YouTube', twitch:'Follow on Twitch', snapchat:'Add on Snapchat',
  classic:'Visit link', tips:'Tip me ☕', product:'Shop now',
  creator_card:'Creator card', image_grid:'Gallery',
  countdown:'Limited offer ⏱', carousel:'See more',
}
function getLinkDefaultLabel(link) {
  return linkDefaultLabels[link.type] || link.type
}

// ─── Video ─────────────────────────────────────────────────────────────────────
const isLocalVideo = computed(() => {
  const url = page.value?.video_url || ''
  return url.startsWith('/storage/') || url.includes('/storage/')
})
const videoEmbedUrl = computed(() => {
  const url = page.value?.video_url || ''
  if (url.includes('streamable.com')) {
    const id = url.split('/').pop().split('?')[0]
    return `https://streamable.com/e/${id}?autoplay=1&muted=1&loop=1`
  }
  if (url.includes('cloudflarestream.com')) return url + (url.includes('?') ? '&' : '?') + 'autoplay=true&muted=true&loop=true'
  return url
})

// ─── Countdown ─────────────────────────────────────────────────────────────────
const countdown = computed(() => {
  if (!page.value?.countdown_end) return { show:false, units:[] }
  const end = new Date(page.value.countdown_end)
  const diff = Math.max(0, end - now.value)
  if (!diff) return { show:false, units:[] }
  const h = Math.floor(diff / 3600000)
  const m = Math.floor((diff % 3600000) / 60000)
  const s = Math.floor((diff % 60000) / 1000)
  return { show:true, units:[
    { value:String(h).padStart(2,'0'), label:'Hours' },
    { value:String(m).padStart(2,'0'), label:'Min' },
    { value:String(s).padStart(2,'0'), label:'Sec' },
  ]}
})

// ─── Age gate + link click ─────────────────────────────────────────────────────
function handleLinkClick(link) {
  if (page.value?.age_gate) { pendingLink.value = link; ageModal.value = true }
  else goToLink(link)
}
function confirmAge() {
  api.post(`/p/${route.params.slug}/event`, { type:'age_confirmed' }).catch(()=>{})
  ageModal.value = false
  goToLink(pendingLink.value)
  pendingLink.value = null
}
function goToLink(link) {
  api.post(`/p/${route.params.slug}/event`, { type:'link_click', page_link_id:link.id }).catch(()=>{})
  const url = link.url
  if (!url) return
  if (page.value?.deep_link_enabled) {
    const ua = navigator.userAgent || ''
    const isAndroid = /Android/.test(ua)
    const isInApp = /Instagram|FBAN|FBAV|TikTok/.test(ua)
    if (isInApp) {
      const enc = encodeURIComponent(url)
      if (isAndroid) {
        try { const u = new URL(url); window.location.href = `intent://${u.host}${u.pathname}${u.search}#Intent;scheme=https;S.browser_fallback_url=${enc};end` }
        catch { window.location.href = url }
      } else {
        window.location.href = `instagram://extbrowser/?url=${enc}`
        setTimeout(() => { window.location.href = url }, 1500)
      }
      return
    }
  }
  window.location.href = url
}

function applyDeepLinkBypass() {
  const ua = navigator.userAgent || ''
  if (!/Instagram|FBAN|FBAV/.test(ua)) return
  const url = window.location.href
  const enc = encodeURIComponent(url)
  if (/Android/.test(ua)) {
    try { const u = new URL(url); window.location.href = `intent://${u.host}${u.pathname}${u.search}#Intent;scheme=https;S.browser_fallback_url=${enc};end` }
    catch {}
  } else {
    window.location.href = `instagram://extbrowser/?url=${enc}`
    setTimeout(() => { window.location.href = url }, 1500)
  }
}

// ===== SHIELD PROTECTION™ =====
// Stored as strings — regex literals with | get misinterpreted by Vue template parser
const META_BOT_PAT = 'facebookexternalhit|Facebot|facebookcatalog|FacebookBot|Instagram|meta-externalagent'
const CRAWL_BOT_PAT = 'bot|crawl|spider|slurp|LinkedInBot|Twitterbot|WhatsApp|Slackbot|TelegramBot|Discordbot|pinterest|Applebot|bingbot|Googlebot|YandexBot|DuckDuckBot|ia_archiver|AhrefsBot|SemrushBot|MJ12bot|DotBot|BLEXBot|DataForSeoBot|PetalBot|BaiduSpider'

function isMetaBot(): boolean {
  return new RegExp(META_BOT_PAT, 'i').test(navigator.userAgent)
}
function isBot(): boolean {
  return isMetaBot() || new RegExp(CRAWL_BOT_PAT, 'i').test(navigator.userAgent)
}

// Serve a decoy page to Meta bots — looks like a harmless personal link page
// This prevents Instagram from scanning the real destination URL
function serveDecoyPage() {
  // Replace all meta tags to prevent any content leaking
  document.querySelectorAll('meta').forEach(m => m.remove())
  const head = document.head
  const metas = [
    { name: 'robots', content: 'noindex, nofollow' },
    { property: 'og:type', content: 'website' },
    { property: 'og:title', content: 'My Links' },
    { property: 'og:description', content: 'Check out my latest content' },
  ]
  metas.forEach(m => {
    const el = document.createElement('meta')
    Object.entries(m).forEach(([k, v]) => el.setAttribute(k, v))
    head.appendChild(el)
  })
  document.title = 'My Links'
  // Render a completely neutral page — no adult keywords, no destination URLs
  document.body.innerHTML = `
    <div style="font-family:sans-serif;max-width:480px;margin:80px auto;padding:24px;text-align:center">
      <div style="width:48px;height:48px;border-radius:12px;background:#6D4EE8;margin:0 auto 16px;display:flex;align-items:center;justify-content:center">
        <span style="color:#fff;font-weight:900;font-size:14px">VSL</span>
      </div>
      <h1 style="font-size:18px;font-weight:700;margin:0 0 8px;color:#111">My Links</h1>
      <p style="color:#6b7280;font-size:14px;margin:0">Follow me on social media for my latest content.</p>
    </div>
  `
}

onMounted(async () => {
  applyDeepLinkBypass()
  try {
    const { data } = await api.get(`/p/${route.params.slug}`)
    page.value = data

    // Shield Protection™: intercept Meta bots before they can scan the destination
    if (data.bot_protection) {
      if (isMetaBot()) {
        // Meta/Instagram crawler → serve decoy, log the blocked attempt
        api.post(`/p/${route.params.slug}/event`, { type:'bot_blocked', ua: navigator.userAgent }).catch(()=>{})
        serveDecoyPage()
        return
      }
      if (isBot()) {
        // Other crawlers → silent block
        serveDecoyPage()
        return
      }
    }

    api.post(`/p/${route.params.slug}/event`, { type:'page_view' }).catch(()=>{})
    if (data.countdown_end) countdownTimer = setInterval(() => { now.value = new Date() }, 1000)
  } catch { page.value = null }
  finally { loading.value = false }
})
onUnmounted(() => { if (countdownTimer) clearInterval(countdownTimer) })
</script>

<style>
@keyframes spin { to { transform:rotate(360deg) } }
@keyframes pulse-rec { 0%,100%{opacity:1} 50%{opacity:0.3} }
@keyframes pulse-online { 0%{box-shadow:0 0 0 0 rgba(16,185,129,0.5)} 70%{box-shadow:0 0 0 8px rgba(16,185,129,0)} 100%{box-shadow:0 0 0 0 rgba(16,185,129,0)} }

.badge-online {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: rgba(16,185,129,0.12);
  border: 1px solid rgba(16,185,129,0.25);
  border-radius: 999px;
  padding: 5px 14px;
  font-size: 12px;
  color: #10b981;
  font-weight: 600;
  font-family: Inter, sans-serif;
}
.online-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10b981;
  animation: pulse-online 2s ease-out infinite;
}
</style>
