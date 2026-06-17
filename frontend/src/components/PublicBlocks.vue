<template>
  <div style="width:100%;display:flex;flex-direction:column" :style="{gap:gap}">
    <template v-for="link in visibleLinks" :key="link.id">

      <!-- ── Image button: large clickable image with title overlay ── -->
      <div v-if="link.type === 'image_button' && link.image_url" @click="navigate(link)"
        style="position:relative;width:100%;border-radius:14px;overflow:hidden;cursor:pointer;aspect-ratio:16/9">
        <img :src="link.image_url" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" loading="lazy" />
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.75) 0%,transparent 55%)"></div>
        <div style="position:absolute;bottom:0;left:0;right:0;padding:12px 14px">
          <p style="font-size:15px;font-weight:800;color:#fff;margin:0">{{ link.title || link.label || 'See more' }}</p>
          <p v-if="link.subtitle" style="font-size:12px;color:rgba(255,255,255,0.75);margin:2px 0 0">{{ link.subtitle }}</p>
        </div>
      </div>

      <!-- ── Banner: horizontal image + text ── -->
      <div v-else-if="link.type === 'banner'" @click="navigate(link)"
        :style="{display:'flex',alignItems:'center',gap:'12px',padding:'10px',borderRadius:'14px',cursor:'pointer',background:cardBg,border:`1px solid ${cardBorder}`}">
        <img v-if="link.image_url" :src="link.image_url" style="width:54px;height:54px;border-radius:10px;object-fit:cover;flex-shrink:0" loading="lazy" />
        <div style="flex:1;min-width:0;text-align:left">
          <p :style="{fontSize:'14px',fontWeight:700,color:textColor,margin:0,whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis'}">{{ link.title || link.label }}</p>
          <p v-if="link.subtitle" :style="{fontSize:'12px',color:subColor,margin:'2px 0 0'}">{{ link.subtitle }}</p>
        </div>
        <span :style="{color:subColor,fontSize:'15px',flexShrink:0}">›</span>
      </div>

      <!-- ── Creator card: avatar + texts + CTA ── -->
      <div v-else-if="link.type === 'creator_card'"
        :style="{padding:'16px',borderRadius:'16px',background:cardBg,border:`1px solid ${cardBorder}`,textAlign:'center'}">
        <img v-if="link.image_url" :src="link.image_url"
          style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin:0 auto 10px;display:block;border:2px solid rgba(255,255,255,0.15)" loading="lazy" />
        <p :style="{fontSize:'15px',fontWeight:800,color:textColor,margin:'0 0 2px'}">{{ link.title || link.label }}</p>
        <p v-if="link.subtitle" :style="{fontSize:'12px',color:subColor,margin:'0 0 12px',lineHeight:1.5}">{{ link.subtitle }}</p>
        <button @click="navigate(link)" :style="buttonStyle(link)" style="justify-content:center">
          {{ ctaLabel(link) }}
        </button>
      </div>

      <!-- ── Image grid: mini gallery 2 columns ── -->
      <div v-else-if="link.type === 'image_grid' && gridImages(link).length" @click="navigate(link)"
        style="display:grid;grid-template-columns:1fr 1fr;gap:8px;cursor:pointer">
        <img v-for="(img, i) in gridImages(link).slice(0, 6)" :key="i" :src="img"
          style="width:100%;aspect-ratio:1;border-radius:12px;object-fit:cover" loading="lazy" />
      </div>

      <!-- ── Carousel: horizontal scroll ── -->
      <div v-else-if="link.type === 'carousel' && gridImages(link).length"
        style="display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;-webkit-overflow-scrolling:touch;scrollbar-width:none">
        <img v-for="(img, i) in gridImages(link)" :key="i" :src="img" @click="navigate(link)"
          style="height:150px;border-radius:14px;object-fit:cover;flex-shrink:0;aspect-ratio:3/4;cursor:pointer" loading="lazy" />
      </div>

      <!-- ── Product: button with price chip ── -->
      <button v-else-if="link.type === 'product'" @click="navigate(link)" :style="buttonStyle(link)">
        <span v-if="iconHtml(link)" v-html="iconHtml(link)" style="display:flex;align-items:center;flex-shrink:0"></span>
        <span>{{ link.label || defaultLabel(link) }}</span>
        <span v-if="link.meta && link.meta.price"
          style="margin-left:auto;background:rgba(255,255,255,0.2);border-radius:999px;padding:2px 10px;font-size:13px;font-weight:800">{{ link.meta.price }}</span>
        <span v-else style="margin-left:auto;opacity:0.5;font-size:14px">›</span>
      </button>

      <!-- ── Default: classic / social / tips / countdown… button ── -->
      <button v-else @click="navigate(link)" :style="buttonStyle(link)"
        @mousedown="e => { e.currentTarget.style.transform='scale(0.97)' }"
        @mouseup="e => { e.currentTarget.style.transform='scale(1)' }"
        @mouseleave="e => { e.currentTarget.style.transform='scale(1)' }">
        <span v-if="iconHtml(link)" v-html="iconHtml(link)" style="display:flex;align-items:center;flex-shrink:0"></span>
        <span>{{ link.label || defaultLabel(link) }}</span>
        <span style="margin-left:auto;opacity:0.5;font-size:14px">›</span>
      </button>

    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  links:    { type: Array, default: () => [] },
  template: { type: String, default: 'classic' },
  btnColor: { type: String, default: '#6D4EE8' },
  gap:      { type: String, default: '12px' },
})
const emit = defineEmits(['navigate'])

const visibleLinks = computed(() => props.links.filter(l => l.is_visible !== false))

function navigate(link) { emit('navigate', link) }

const isMinimal = computed(() => props.template === 'minimal')
const textColor = computed(() => isMinimal.value ? '#111827' : '#fff')
const subColor  = computed(() => isMinimal.value ? '#6B7280' : 'rgba(255,255,255,0.55)')
const cardBg     = computed(() => isMinimal.value ? '#fff' : 'rgba(255,255,255,0.06)')
const cardBorder = computed(() => isMinimal.value ? '#E5E7EB' : 'rgba(255,255,255,0.1)')

function buttonStyle(link) {
  const t = props.template
  const btnColor = link.btn_color || props.btnColor || '#6D4EE8'
  const base = {
    width:'100%', padding:'15px 20px', border:'none', cursor:'pointer',
    fontSize:'15px', fontWeight:700, fontFamily:'Inter,sans-serif',
    transition:'transform 0.12s, opacity 0.12s, box-shadow 0.12s',
    letterSpacing:'-0.01em', display:'flex', alignItems:'center', gap:'12px',
    position:'relative', overflow:'hidden', borderRadius:'14px',
  }
  if (t === 'minimal') return { ...base, background:'#fff', color:'#111827', boxShadow:'0 1px 4px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.06)' }
  if (t === 'neon')    return { ...base, background:'transparent', color:'#fff', border:`2px solid ${btnColor}`, boxShadow:`0 0 16px ${btnColor}44` }
  return { ...base, background: btnColor, color:'#fff', boxShadow:`0 4px 20px ${btnColor}44` }
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
  tips:      { color:'#F59E0B', html:`<i class="bi bi-cup-hot-fill" style="color:#fff;font-size:15px;line-height:1"></i>` },
  product:   { color:'#10B981', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="18" height="18"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>` },
}

function iconHtml(link) {
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
  classic:'Visit link', tips:'Tip me', product:'Shop now',
  creator_card:'Creator card', image_grid:'Gallery',
  countdown:'Limited offer', carousel:'See more', image_button:'See more', banner:'See more',
}
function defaultLabel(link) { return linkDefaultLabels[link.type] || link.type }
function ctaLabel(link) { return (link.meta && link.meta.cta) || 'Discover' }

function gridImages(link) {
  const imgs = (link.meta && Array.isArray(link.meta.images)) ? link.meta.images : []
  const urls = imgs.map(i => typeof i === 'string' ? i : i?.url).filter(Boolean)
  if (!urls.length && link.image_url) return [link.image_url]
  return urls
}
</script>
