<template>
  <div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px">
      <div v-for="p in presets" :key="p.id"
        @click="$emit('select', p)"
        :style="{
          position:'relative',
          borderRadius:'18px',
          overflow:'hidden',
          cursor:'pointer',
          border: selected===p.id ? '2px solid #6D4EE8' : '2px solid rgba(255,255,255,0.07)',
          boxShadow: selected===p.id ? '0 0 0 3px rgba(109,78,232,0.2), 0 8px 32px rgba(0,0,0,0.4)' : '0 4px 16px rgba(0,0,0,0.3)',
          transition:'all 0.15s',
          background:'#0a0914',
        }">

        <!-- Checkmark -->
        <div v-if="selected===p.id" style="position:absolute;top:10px;right:10px;z-index:20;width:22px;height:22px;border-radius:50%;background:#6D4EE8;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(109,78,232,0.6)">
          <i class="bi bi-check2" style="color:#fff;font-size:13px"></i>
        </div>

        <!-- Phone mockup -->
        <div style="padding:12px 12px 0;display:flex;justify-content:center">
          <div :style="{
            width:'100%',
            height:'220px',
            borderRadius:'14px',
            overflow:'hidden',
            position:'relative',
            background:p.bg,
            boxShadow:'0 4px 20px rgba(0,0,0,0.5)',
            border:'1px solid rgba(255,255,255,0.07)',
          }">

            <!-- DARK/CLASSIC style -->
            <template v-if="p.style==='classic'">
              <!-- Background photo with overlay -->
              <img v-if="p.photo" :src="p.photo" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.15" />
              <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;padding:16px 10px 10px;height:100%;box-sizing:border-box">
                <!-- Online badge -->
                <div v-if="p.online" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:999px;padding:2px 7px;display:flex;align-items:center;gap:3px;margin-bottom:8px">
                  <span style="width:5px;height:5px;border-radius:50%;background:#10b981;display:inline-block"></span>
                  <span style="font-size:7px;color:#10b981;font-weight:700">Online now</span>
                </div>
                <!-- Avatar -->
                <img :src="p.photo" :style="{width:'38px',height:'38px',borderRadius:'50%',objectFit:'cover',border:`2px solid rgba(255,255,255,0.2)`,marginBottom:'6px',flexShrink:0}" />
                <div style="display:flex;align-items:center;gap:3px;margin-bottom:2px">
                  <span :style="{fontSize:'9px',fontWeight:800,color:'#fff',letterSpacing:'-0.02em'}">{{ p.name }}</span>
                  <span v-if="p.verified" style="color:#60a5fa;font-size:7px">✓</span>
                </div>
                <span :style="{fontSize:'7px',color:'rgba(255,255,255,0.4)',marginBottom:'4px'}">{{ p.handle }}</span>
                <span v-if="p.location" :style="{fontSize:'6px',color:'rgba(255,255,255,0.3)',marginBottom:'6px'}">📍 {{ p.location }}</span>
                <!-- Social icons row -->
                <div style="display:flex;gap:3px;margin-bottom:8px">
                  <span v-for="ic in p.socials" :key="ic" :style="{width:'12px',height:'12px',borderRadius:'4px',background:ic.color,display:'flex',alignItems:'center',justifyContent:'center',fontSize:'6px'}">{{ ic.emoji }}</span>
                </div>
                <!-- Links -->
                <div style="width:100%;display:flex;flex-direction:column;gap:4px;margin-top:auto">
                  <div v-for="(l,i) in p.links" :key="i"
                    :style="{
                      borderRadius: p.btnStyle==='pill'?'999px':p.btnStyle==='square'?'3px':'7px',
                      padding:'4px 8px',fontSize:'7px',fontWeight:700,color:'#fff',
                      background: i===0 ? p.btnColor : `${p.btnColor}55`,
                      textAlign:'center',
                      boxShadow: i===0 ? `0 2px 8px ${p.btnColor}55` : 'none',
                    }">{{ l }}</div>
                </div>
              </div>
            </template>

            <!-- CINEMATIC style — photo full bg -->
            <template v-else-if="p.style==='cinematic'">
              <img :src="p.photo" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" />
              <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.15) 0%,transparent 35%,rgba(0,0,0,0.8) 75%,rgba(0,0,0,0.95) 100%)"></div>
              <!-- VSL badge -->
              <div style="position:absolute;top:7px;left:7px;background:rgba(0,0,0,0.55);backdrop-filter:blur(6px);border-radius:999px;padding:2px 6px;display:flex;align-items:center;gap:3px;border:1px solid rgba(255,255,255,0.1)">
                <span style="font-size:6px;font-weight:800;color:#fff">▶ VSL</span>
              </div>
              <!-- Online -->
              <div v-if="p.online" style="position:absolute;top:7px;right:7px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:999px;padding:2px 5px;display:flex;align-items:center;gap:2px">
                <span style="width:4px;height:4px;border-radius:50%;background:#10b981;display:inline-block"></span>
                <span style="font-size:5px;color:#10b981;font-weight:700">Online</span>
              </div>
              <!-- Bottom -->
              <div style="position:absolute;bottom:0;left:0;right:0;padding:6px 8px">
                <div style="display:flex;align-items:center;gap:4px;margin-bottom:5px">
                  <img :src="p.photo" style="width:14px;height:14px;border-radius:50%;border:1px solid rgba(255,255,255,0.3);object-fit:cover;flex-shrink:0" />
                  <div>
                    <span style="font-size:8px;font-weight:800;color:#fff;display:block;line-height:1">{{ p.name }}</span>
                    <span style="font-size:6px;color:rgba(255,255,255,0.5)">{{ p.handle }}</span>
                  </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:3px">
                  <div v-for="(l,i) in p.links.slice(0,2)" :key="i"
                    :style="{borderRadius:'5px',padding:'3px 6px',fontSize:'6px',fontWeight:700,color:'#fff',background:i===0?p.btnColor:`${p.btnColor}66`,textAlign:'center'}">
                    {{ l }}
                  </div>
                </div>
              </div>
            </template>

            <!-- MINIMAL/LIGHT style -->
            <template v-else-if="p.style==='minimal'">
              <div :style="{position:'relative',display:'flex',flexDirection:'column',alignItems:'center',padding:'14px 10px 10px',height:'100%',boxSizing:'border-box'}">
                <!-- Gradient header band -->
                <div :style="{position:'absolute',top:0,left:0,right:0,height:'60px',background:p.headerGradient||'#e5e7eb',borderRadius:'14px 14px 0 0'}"></div>
                <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;width:100%;height:100%">
                  <div style="height:30px"></div>
                  <img :src="p.photo" :style="{width:'36px',height:'36px',borderRadius:'50%',objectFit:'cover',border:'2px solid #fff',boxShadow:'0 2px 8px rgba(0,0,0,0.15)',marginBottom:'6px',flexShrink:0}" />
                  <span :style="{fontSize:'9px',fontWeight:800,color:'#111827',letterSpacing:'-0.02em',marginBottom:'1px'}">{{ p.name }}</span>
                  <span style="font-size:6px;color:#9CA3AF;margin-bottom:3px">{{ p.handle }}</span>
                  <span v-if="p.bio" style="font-size:6px;color:#6B7280;margin-bottom:6px;text-align:center;line-height:1.4">{{ p.bio }}</span>
                  <div style="display:flex;gap:3px;margin-bottom:7px">
                    <span v-for="ic in p.socials" :key="ic" :style="{width:'12px',height:'12px',borderRadius:'4px',background:ic.color,display:'flex',alignItems:'center',justifyContent:'center',fontSize:'6px'}">{{ ic.emoji }}</span>
                  </div>
                  <div style="width:100%;display:flex;flex-direction:column;gap:3px;margin-top:auto">
                    <div v-for="(l,i) in p.links" :key="i"
                      :style="{
                        borderRadius: p.btnStyle==='pill'?'999px':'7px',
                        padding:'4px 8px',fontSize:'7px',fontWeight:700,
                        color: i===0?'#fff':'#374151',
                        background: i===0?p.btnColor:'#F3F4F6',
                        textAlign:'center',
                        border: i===0?'none':'1px solid #E5E7EB',
                      }">{{ l }}</div>
                  </div>
                </div>
              </div>
            </template>

          </div>
        </div>

        <!-- Label -->
        <div style="padding:8px 10px 10px;text-align:center">
          <p :style="{fontSize:'11px',fontWeight:700,color:selected===p.id?'#A78BFA':'rgba(255,255,255,0.65)',margin:'0 0 1px',letterSpacing:'-0.01em'}">{{ p.name }}</p>
          <p style="font-size:9px;color:rgba(255,255,255,0.3);margin:0">{{ p.vibe }}</p>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  selected: { type: String, default: null }
})
defineEmits(['select'])

const presets = [
  {
    id: 'simone',
    name: 'Simone',
    handle: '@simone.oficial',
    location: 'Los Angeles',
    vibe: 'Dark premium',
    style: 'classic',
    bg: '#0d0d0d',
    btnColor: '#6D4EE8',
    btnStyle: 'rounded',
    online: true,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=47',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'✕',color:'#000'},{emoji:'✈️',color:'#26A5E4'}],
    links: ['Subscribe on OnlyFans', 'My Instagram', 'My Telegram'],
    apply: { template:'classic', bg_color:'#0d0d0d', btn_color:'#6D4EE8' },
  },
  {
    id: 'jenna',
    name: 'Jenna',
    handle: '@jenna.london',
    location: 'London, UK',
    vibe: 'Neon purple',
    style: 'classic',
    bg: '#0a0118',
    btnColor: '#A78BFA',
    btnStyle: 'pill',
    online: true,
    verified: false,
    photo: 'https://i.pravatar.cc/300?img=25',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'✕',color:'#000'},{emoji:'✈️',color:'#26A5E4'}],
    links: ['My OnlyFans', 'Join my Telegram'],
    apply: { template:'neon', bg_color:'#0a0118', btn_color:'#A78BFA' },
  },
  {
    id: 'rose',
    name: 'Rose',
    handle: '@rose.milan',
    location: 'Milan',
    vibe: 'Cinematic VSL',
    style: 'cinematic',
    bg: '#000',
    btnColor: '#6D4EE8',
    btnStyle: 'rounded',
    online: true,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=32',
    socials: [],
    links: ['Subscribe on OnlyFans', 'My MYM'],
    apply: { template:'cinematic', bg_color:'#000', btn_color:'#6D4EE8' },
  },
  {
    id: 'sofia',
    name: 'Sofia',
    handle: '@sofiamodel',
    location: 'Spain & Italy',
    vibe: 'Clean minimal',
    style: 'minimal',
    bg: '#F9FAFB',
    btnColor: '#6D4EE8',
    btnStyle: 'rounded',
    online: true,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=48',
    headerGradient: 'linear-gradient(135deg,#e0d7ff,#c4b5fd)',
    bio: 'Living in Spain & Italy 🌿',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'✕',color:'#000'},{emoji:'👻',color:'#ccc'},{emoji:'✈️',color:'#26A5E4'}],
    links: ['My OnlyFans', 'My MYM'],
    apply: { template:'minimal', bg_color:'#F9FAFB', btn_color:'#6D4EE8' },
  },
  {
    id: 'bianca',
    name: 'Bianca',
    handle: '@bianca.uk',
    location: 'Manchester, UK',
    vibe: 'Bold dark',
    style: 'classic',
    bg: '#0a0a0a',
    btnColor: '#EF4444',
    btnStyle: 'rounded',
    online: true,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=44',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'✕',color:'#000'},{emoji:'✈️',color:'#26A5E4'}],
    links: ['Subscribe on OnlyFans', 'My Telegram', 'My Snap'],
    apply: { template:'classic', bg_color:'#0a0a0a', btn_color:'#EF4444' },
  },
  {
    id: 'erika',
    name: 'Erika',
    handle: '@erika.vip',
    location: 'Stockholm',
    vibe: 'Cinematic rose',
    style: 'cinematic',
    bg: '#000',
    btnColor: '#EC4899',
    btnStyle: 'pill',
    online: false,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=39',
    socials: [],
    links: ['Subscribe on OnlyFans', 'My MYM'],
    apply: { template:'cinematic', bg_color:'#000', btn_color:'#EC4899' },
  },
  {
    id: 'ambre',
    name: 'Ambre',
    handle: '@ambrefashion',
    location: 'Paris, France',
    vibe: 'Soft & warm',
    style: 'minimal',
    bg: '#FFF5F0',
    btnColor: '#F97316',
    btnStyle: 'pill',
    online: true,
    verified: false,
    photo: 'https://i.pravatar.cc/300?img=41',
    headerGradient: 'linear-gradient(135deg,#fed7aa,#fca5a5)',
    bio: 'Fashion Lifestyle 🌸',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'✕',color:'#000'},{emoji:'✈️',color:'#26A5E4'}],
    links: ['My MYM', 'My Instagram'],
    apply: { template:'minimal', bg_color:'#FFF5F0', btn_color:'#F97316' },
  },
  {
    id: 'luna',
    name: 'Luna',
    handle: '@luna.creator',
    location: 'Miami, FL',
    vibe: 'Ocean blue',
    style: 'classic',
    bg: '#001a33',
    btnColor: '#22D3EE',
    btnStyle: 'rounded',
    online: true,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=23',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'✈️',color:'#26A5E4'}],
    links: ['Subscribe on OnlyFans', 'My Telegram'],
    apply: { template:'classic', bg_color:'#001a33', btn_color:'#22D3EE' },
  },
  {
    id: 'mia',
    name: 'Mia',
    handle: '@mia.official',
    location: 'Berlin',
    vibe: 'Green terrain',
    style: 'classic',
    bg: '#1c2b1a',
    btnColor: '#4ADE80',
    btnStyle: 'rounded',
    online: false,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=35',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'▶️',color:'#FF0000'}],
    links: ['Subscribe on OnlyFans', 'My YouTube'],
    apply: { template:'classic', bg_color:'#1c2b1a', btn_color:'#4ADE80' },
  },
  {
    id: 'clara',
    name: 'Clara',
    handle: '@clara.paris',
    location: 'Paris, France',
    vibe: 'Pastel dreamy',
    style: 'minimal',
    bg: '#fce7f3',
    btnColor: '#DB2777',
    btnStyle: 'pill',
    online: true,
    verified: false,
    photo: 'https://i.pravatar.cc/300?img=49',
    headerGradient: 'linear-gradient(135deg,#fbcfe8,#f9a8d4)',
    bio: 'Créatrice de contenu 💕',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'👻',color:'#ccc'}],
    links: ['My MYM', 'My Snap'],
    apply: { template:'minimal', bg_color:'#fce7f3', btn_color:'#DB2777' },
  },
  {
    id: 'nadia',
    name: 'Nadia',
    handle: '@nadia.exclusive',
    location: 'Dubai',
    vibe: 'Cinematic gold',
    style: 'cinematic',
    bg: '#0a0800',
    btnColor: '#F59E0B',
    btnStyle: 'rounded',
    online: true,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=29',
    socials: [],
    links: ['Subscribe on OnlyFans', 'VIP Telegram'],
    apply: { template:'cinematic', bg_color:'#0a0800', btn_color:'#F59E0B' },
  },
  {
    id: 'emma',
    name: 'Emma',
    handle: '@emma.lifestyle',
    location: 'Lyon, France',
    vibe: 'Minimal fresh',
    style: 'minimal',
    bg: '#F0FDF4',
    btnColor: '#16A34A',
    btnStyle: 'rounded',
    online: false,
    verified: true,
    photo: 'https://i.pravatar.cc/300?img=45',
    headerGradient: 'linear-gradient(135deg,#bbf7d0,#86efac)',
    bio: 'Lifestyle & bien-être 🌿',
    socials: [{emoji:'📸',color:'#E1306C'},{emoji:'🎵',color:'#010101'},{emoji:'▶️',color:'#FF0000'}],
    links: ['Subscribe on MYM', 'My YouTube'],
    apply: { template:'minimal', bg_color:'#F0FDF4', btn_color:'#16A34A' },
  },
]
</script>
