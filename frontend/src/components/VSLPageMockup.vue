<template>
  <div :style="{width:'100%',height:'100%',background:bg,display:'flex',flexDirection:'column',alignItems:'center',padding:'12px 8px',position:'relative',overflow:'hidden'}">

    <!-- VSL Video area -->
    <div :style="{
      width:'100%',height:'110px',borderRadius:'10px',
      background:'#000',
      border:'1px solid rgba(255,255,255,0.08)',
      display:'flex',alignItems:'center',justifyContent:'center',
      marginBottom:'10px',
      position:'relative',
      overflow:'hidden',
    }">
      <!-- Streamable thumbnail via oEmbed-style poster -->
      <img v-if="videoThumb" :src="videoThumb"
        style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.75" />
      <img v-else :src="avatar"
        style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.4" />

      <!-- Play button -->
      <div :style="{
        width:'32px',height:'32px',borderRadius:'50%',
        background:'rgba(255,255,255,0.92)',
        display:'flex',alignItems:'center',justifyContent:'center',
        position:'relative',zIndex:1,
        boxShadow:'0 2px 16px rgba(0,0,0,0.5)',
      }">
        <i class="bi bi-play-fill" style="color:#111;font-size:13px;margin-left:2px"></i>
      </div>

      <!-- VSL live badge -->
      <div style="position:absolute;top:6px;left:6px;background:rgba(0,0,0,0.75);border-radius:999px;padding:2px 6px;display:flex;align-items:center;gap:3px">
        <span style="width:5px;height:5px;border-radius:50%;background:#ef4444;display:inline-block;animation:pulse-rec 1.2s infinite"></span>
        <span style="font-size:7px;font-weight:800;color:#fff;letter-spacing:0.05em">VSL</span>
      </div>

      <!-- Online badge -->
      <div style="position:absolute;top:6px;right:6px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.4);border-radius:999px;padding:2px 5px;display:flex;align-items:center;gap:2px">
        <span style="width:4px;height:4px;border-radius:50%;background:#10b981;display:inline-block"></span>
        <span style="font-size:6px;color:#10b981;font-weight:700">Online</span>
      </div>

      <!-- Duration -->
      <span style="position:absolute;bottom:5px;right:6px;font-size:7px;color:rgba(255,255,255,0.6);background:rgba(0,0,0,0.5);padding:1px 4px;border-radius:3px">2:47</span>
    </div>

    <!-- Avatar + name -->
    <div :style="{width:'36px',height:'36px',borderRadius:'50%',overflow:'hidden',border:`2px solid ${btn}`,marginBottom:'6px',boxShadow:`0 0 12px ${btn}55`,flexShrink:0}">
      <img :src="avatar" style="width:100%;height:100%;object-fit:cover" />
    </div>
    <p :style="{fontSize:'10px',fontWeight:800,color:textColor,margin:'0 0 1px',letterSpacing:'-0.01em'}">{{ name }}</p>
    <p style="font-size:7px;color:rgba(255,255,255,0.4);margin:0 0 8px;display:flex;align-items:center;gap:2px">
      <span style="color:#60a5fa;font-size:8px">✓</span> Verified creator
    </p>

    <!-- Links -->
    <div style="width:100%;display:flex;flex-direction:column;gap:4px">
      <div v-for="(lbl, i) in links" :key="i"
        :style="{
          borderRadius:'7px',
          padding:'6px 8px',
          fontSize:'7px',
          fontWeight:700,
          color: i===0 ? '#fff' : textColor,
          background: i===0 ? btn : 'rgba(255,255,255,0.07)',
          textAlign:'center',
          boxShadow: i===0 ? `0 2px 10px ${btn}55` : 'none',
          border: i===0 ? 'none' : '1px solid rgba(255,255,255,0.1)',
          letterSpacing:'-0.01em',
        }">
        {{ lbl }}
      </div>
    </div>

    <!-- Powered by -->
    <p style="position:absolute;bottom:4px;font-size:6px;color:rgba(255,255,255,0.15);letter-spacing:0.05em">MySocialVSL</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  avatar:    { type: String,  default: 'https://i.pravatar.cc/300?img=47' },
  name:      { type: String,  default: 'Karine' },
  bg:        { type: String,  default: '#0d0d0d' },
  btn:       { type: String,  default: '#00aff0' },
  videoUrl:  { type: String,  default: 'https://streamable.com/e/0ed1q5' },
  links:     { type: Array,   default: () => ['Subscribe on OnlyFans', 'My Telegram', 'More content ↗'] },
})

// Streamable video ID → thumbnail
const videoThumb = computed(() => {
  if (!props.videoUrl) return null
  const m = props.videoUrl.match(/streamable\.com\/(?:e\/)?([a-zA-Z0-9]+)/)
  if (m) return `https://cdn-cf-east.streamable.com/image/${m[1]}.jpg`
  return null
})

const textColor = computed(() => {
  // Simple luminance check — if bg is light, use dark text
  const hex = props.bg.replace('#', '')
  if (hex.length === 6) {
    const r = parseInt(hex.slice(0,2),16)
    const g = parseInt(hex.slice(2,4),16)
    const b = parseInt(hex.slice(4,6),16)
    const lum = (0.299*r + 0.587*g + 0.114*b) / 255
    return lum > 0.5 ? '#111827' : '#fff'
  }
  return '#fff'
})
</script>
