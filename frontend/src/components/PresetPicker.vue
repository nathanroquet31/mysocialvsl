<template>
  <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:28px">
    <div v-for="p in presets" :key="p.id"
      @click="$emit('select', p)"
      :style="{
        position:'relative', cursor:'pointer', borderRadius:'16px', overflow:'hidden',
        border: selected===p.id ? '2px solid #6D4EE8' : '2px solid rgba(255,255,255,0.07)',
        background:'#080610', transition:'all 0.15s',
        boxShadow: selected===p.id
          ? '0 0 0 3px rgba(109,78,232,0.25), 0 8px 28px rgba(0,0,0,0.6)'
          : '0 4px 16px rgba(0,0,0,0.4)',
      }">

      <!-- Checkmark -->
      <div v-if="selected===p.id"
        style="position:absolute;top:7px;right:7px;z-index:40;width:18px;height:18px;border-radius:50%;background:#6D4EE8;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(109,78,232,0.7)">
        <i class="bi bi-check2" style="color:#fff;font-size:10px"></i>
      </div>

      <!-- Phone shell -->
      <div style="padding:8px 8px 0">
        <div :style="{
          aspectRatio:'9/16',
          background:'#0a0a0a',
          borderRadius:'20px',
          padding:'5px',
          boxShadow:'0 0 0 1px rgba(255,255,255,0.05), 0 12px 30px rgba(0,0,0,0.8)',
          position:'relative',
        }">
          <!-- Side buttons (decorative) -->
          <div style="position:absolute;right:-2px;top:30%;width:2px;height:16%;background:rgba(255,255,255,0.08);border-radius:0 2px 2px 0"></div>
          <div style="position:absolute;left:-2px;top:25%;width:2px;height:8%;background:rgba(255,255,255,0.08);border-radius:2px 0 0 2px"></div>
          <div style="position:absolute;left:-2px;top:37%;width:2px;height:12%;background:rgba(255,255,255,0.08);border-radius:2px 0 0 2px"></div>

          <!-- Screen -->
          <div :style="{
            width:'100%', height:'100%',
            borderRadius:'15px',
            overflow:'hidden',
            position:'relative',
            background: p.bg || '#000',
          }">
            <!-- Dynamic Island -->
            <div style="position:absolute;top:5px;left:50%;transform:translateX(-50%);width:38%;height:13px;background:#000;border-radius:8px;z-index:30"></div>

            <!-- Status bar -->
            <div style="position:absolute;top:0;left:0;right:0;height:22px;display:flex;align-items:center;justify-content:space-between;padding:4px 10px 0;z-index:20;pointer-events:none">
              <span style="font-size:6px;font-weight:700;color:#fff;opacity:0.9">9:41</span>
              <div style="display:flex;align-items:center;gap:2px">
                <svg width="8" height="5" viewBox="0 0 12 8" fill="none" style="opacity:0.8">
                  <rect x="0" y="3" width="2" height="5" rx="0.5" fill="white" fill-opacity="0.5"/>
                  <rect x="3" y="2" width="2" height="6" rx="0.5" fill="white" fill-opacity="0.7"/>
                  <rect x="6" y="1" width="2" height="7" rx="0.5" fill="white" fill-opacity="0.9"/>
                  <rect x="9" y="0" width="2" height="8" rx="0.5" fill="white"/>
                </svg>
                <div style="width:12px;height:6px;border:1px solid rgba(255,255,255,0.6);border-radius:1.5px;position:relative">
                  <div style="position:absolute;left:1px;top:1px;bottom:1px;width:70%;background:#fff;border-radius:0.5px"></div>
                </div>
              </div>
            </div>

            <!-- ====== STYLE: vsl ====== -->
            <template v-if="p.style==='vsl'">
              <div style="position:absolute;inset:0;display:flex;flex-direction:column">
                <!-- Full-screen photo top 60% -->
                <div style="flex:0 0 60%;position:relative;overflow:hidden">
                  <img :src="p.photo" style="width:100%;height:100%;object-fit:cover;object-position:top" />
                  <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.05) 30%,rgba(0,0,0,0.75) 100%)"></div>
                  <!-- Name overlay at bottom of photo -->
                  <div style="position:absolute;bottom:6px;left:8px">
                    <div style="display:flex;align-items:center;gap:3px;margin-bottom:2px">
                      <span style="width:4px;height:4px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                      <span style="font-size:4px;color:#22c55e;font-weight:700">Active now</span>
                      <span style="font-size:4px;color:rgba(255,255,255,0.5)"> • {{ p.location }}</span>
                    </div>
                    <p style="font-size:10px;font-weight:800;color:#fff;margin:0;text-shadow:0 1px 6px rgba(0,0,0,0.6);line-height:1.1">{{ p.name }}</p>
                    <p v-if="p.tagline" style="font-size:4px;color:rgba(255,255,255,0.6);margin:2px 0 0">{{ p.tagline }}</p>
                  </div>
                </div>
                <!-- Bottom section -->
                <div :style="{flex:1,background:p.bottomBg||'#0d0b1e',padding:'5px 7px 6px',display:'flex',flexDirection:'column',gap:'4px'}">
                  <!-- Social icons -->
                  <div style="display:flex;gap:3px">
                    <span v-for="s in p.socials" :key="s.c"
                      :style="{width:'12px',height:'12px',borderRadius:'3px',background:s.c,display:'inline-flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                      <i :class="`bi bi-${s.i}`" :style="{fontSize:'7px',color:s.dark?'#000':'#fff'}"></i>
                    </span>
                  </div>
                  <!-- Grid of 3 photo tiles -->
                  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:2px;flex:1;min-height:0">
                    <div v-for="(t,ti) in p.thumbs" :key="ti" style="border-radius:4px;overflow:hidden;position:relative">
                      <img :src="t" style="width:100%;height:100%;object-fit:cover;display:block" />
                      <div style="position:absolute;bottom:0;left:0;right:0;height:12px;background:linear-gradient(to top,rgba(0,0,0,0.65),transparent);display:flex;align-items:flex-end;padding:0 3px 3px">
                        <div style="height:2px;background:rgba(255,255,255,0.35);border-radius:1px;width:70%"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <!-- ====== STYLE: banner ====== -->
            <template v-else-if="p.style==='banner'">
              <div :style="{position:'absolute',inset:0,display:'flex',flexDirection:'column',background:p.bg}">
                <!-- Photo banner top 40% -->
                <div style="flex:0 0 40%;position:relative;overflow:hidden">
                  <img :src="p.photo" style="width:100%;height:100%;object-fit:cover;object-position:top" />
                  <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 20%,rgba(0,0,0,0.82) 100%)"></div>
                  <div style="position:absolute;bottom:5px;left:7px">
                    <div style="display:flex;align-items:center;gap:2px;margin-bottom:1px">
                      <span style="width:3px;height:3px;border-radius:50%;background:#22c55e"></span>
                      <span style="font-size:4px;color:#22c55e;font-weight:700">Active now</span>
                      <span style="font-size:4px;color:rgba(255,255,255,0.5)"> • {{ p.location }}</span>
                    </div>
                    <p style="font-size:9px;font-weight:800;color:#fff;margin:0;text-shadow:0 1px 4px rgba(0,0,0,0.7)">{{ p.name }}</p>
                  </div>
                </div>
                <!-- Bottom content 60% -->
                <div style="flex:1;padding:5px 6px;display:flex;flex-direction:column;gap:3px;overflow:hidden">
                  <p v-if="p.bio" style="font-size:4.5px;color:rgba(255,255,255,0.45);margin:0;line-height:1.4">{{ p.bio }}</p>
                  <div style="display:flex;gap:2px">
                    <span v-for="s in p.socials" :key="s.c"
                      :style="{width:'11px',height:'11px',borderRadius:'3px',background:s.c,display:'inline-flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                      <i :class="`bi bi-${s.i}`" :style="{fontSize:'6px',color:s.dark?'#000':'#fff'}"></i>
                    </span>
                  </div>
                  <!-- 2×2 grid -->
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2px;flex:1;min-height:0">
                    <div v-for="(t,ti) in p.thumbs.slice(0,4)" :key="ti" style="border-radius:3px;overflow:hidden;position:relative">
                      <img :src="t" style="width:100%;height:100%;object-fit:cover;display:block" />
                      <div style="position:absolute;bottom:0;left:0;right:0;height:12px;background:rgba(0,0,0,0.5);display:flex;align-items:center;padding:0 3px">
                        <div style="height:2px;background:rgba(255,255,255,0.3);border-radius:1px;width:65%"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <!-- ====== STYLE: carousel ====== -->
            <template v-else-if="p.style==='carousel'">
              <div :style="{position:'absolute',inset:0,display:'flex',flexDirection:'column',background:p.bg,padding:'22px 8px 6px',boxSizing:'border-box'}">
                <!-- Compact profile -->
                <div style="display:flex;align-items:center;gap:5px;margin-bottom:4px;flex-shrink:0">
                  <div :style="{width:'20px',height:'20px',borderRadius:'50%',overflow:'hidden',border:`2px solid ${p.btnColor}55`,flexShrink:0}">
                    <img :src="p.photo" style="width:100%;height:100%;object-fit:cover" />
                  </div>
                  <div>
                    <p :style="{fontSize:'7px',fontWeight:800,color:p.textColor||'#fff',margin:'0 0 0.5px'}">{{ p.name }}</p>
                    <div style="display:flex;align-items:center;gap:2px">
                      <span style="width:3px;height:3px;border-radius:50%;background:#22c55e"></span>
                      <span :style="{fontSize:'4px',color:'#22c55e',fontWeight:700}">Active now</span>
                      <span :style="{fontSize:'4px',color:p.textColor==='#111827'?'rgba(0,0,0,0.4)':'rgba(255,255,255,0.4)'}"> • {{ p.location }}</span>
                    </div>
                  </div>
                </div>
                <p v-if="p.bio" :style="{fontSize:'4.5px',color:p.textColor==='#111827'?'rgba(0,0,0,0.5)':'rgba(255,255,255,0.45)',margin:'0 0 5px',lineHeight:1.4,flexShrink:0}">{{ p.bio }}</p>
                <!-- Social icons -->
                <div style="display:flex;gap:2px;margin-bottom:6px;flex-shrink:0">
                  <span v-for="s in p.socials" :key="s.c"
                    :style="{width:'11px',height:'11px',borderRadius:'3px',background:s.c,display:'inline-flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                    <i :class="`bi bi-${s.i}`" :style="{fontSize:'6px',color:s.dark?'#000':'#fff'}"></i>
                  </span>
                </div>
                <!-- Large carousel photo -->
                <div :style="{flex:1,borderRadius:'8px',overflow:'hidden',position:'relative',border:`1px solid ${p.textColor==='#111827'?'rgba(0,0,0,0.07)':'rgba(255,255,255,0.08)'}`}">
                  <img :src="p.photo" style="width:100%;height:100%;object-fit:cover;object-position:top" />
                  <!-- Arrows -->
                  <div style="position:absolute;left:4px;top:50%;transform:translateY(-50%);width:13px;height:13px;border-radius:50%;background:rgba(255,255,255,0.9);display:flex;align-items:center;justify-content:center;box-shadow:0 1px 5px rgba(0,0,0,0.3)">
                    <i class="bi bi-chevron-left" style="font-size:5px;color:#111"></i>
                  </div>
                  <div style="position:absolute;right:4px;top:50%;transform:translateY(-50%);width:13px;height:13px;border-radius:50%;background:rgba(255,255,255,0.9);display:flex;align-items:center;justify-content:center;box-shadow:0 1px 5px rgba(0,0,0,0.3)">
                    <i class="bi bi-chevron-right" style="font-size:5px;color:#111"></i>
                  </div>
                  <!-- Bottom button bar -->
                  <div style="position:absolute;bottom:0;left:0;right:0;height:16px;background:rgba(0,0,0,0.3);backdrop-filter:blur(2px);display:flex;align-items:center;padding:0 6px">
                    <div style="height:2.5px;background:rgba(255,255,255,0.4);border-radius:2px;width:60%"></div>
                  </div>
                </div>
              </div>
            </template>

            <!-- ====== STYLE: classic ====== -->
            <template v-else>
              <div :style="{position:'absolute',inset:0,display:'flex',flexDirection:'column',alignItems:'center',background:p.bg,padding:'22px 8px 6px',boxSizing:'border-box'}">
                <!-- Avatar with ring -->
                <div :style="{width:'36px',height:'36px',borderRadius:'50%',overflow:'hidden',border:`2.5px solid ${p.btnColor}`,marginBottom:'5px',flexShrink:0,boxShadow:`0 0 12px ${p.btnColor}55`}">
                  <img :src="p.photo" style="width:100%;height:100%;object-fit:cover" />
                </div>
                <p :style="{fontSize:'8px',fontWeight:800,color:p.textColor||'#fff',margin:'0 0 2px',letterSpacing:'-0.01em'}">{{ p.name }}</p>
                <div style="display:flex;align-items:center;gap:2px;margin-bottom:3px">
                  <span style="width:4px;height:4px;border-radius:50%;background:#22c55e"></span>
                  <span :style="{fontSize:'4px',color:'#22c55e',fontWeight:700}">Active now</span>
                  <span :style="{fontSize:'4px',color:p.textColor==='#111827'?'rgba(0,0,0,0.4)':'rgba(255,255,255,0.4)'}"> • {{ p.location }}</span>
                </div>
                <p v-if="p.bio" :style="{fontSize:'4px',color:p.textColor==='#111827'?'rgba(0,0,0,0.45)':'rgba(255,255,255,0.4)',margin:'0 0 5px',textAlign:'center',lineHeight:1.5,maxWidth:'90%'}">{{ p.bio }}</p>
                <!-- Social icons -->
                <div style="display:flex;gap:2px;margin-bottom:8px;flex-wrap:wrap;justify-content:center">
                  <span v-for="s in p.socials" :key="s.c"
                    :style="{width:'11px',height:'11px',borderRadius:'3px',background:s.c,display:'inline-flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                    <i :class="`bi bi-${s.i}`" :style="{fontSize:'6px',color:s.dark?'#000':'#fff'}"></i>
                  </span>
                </div>
                <!-- Boutons -->
                <div style="width:100%;display:flex;flex-direction:column;gap:3px">
                  <div v-for="(l,i) in p.links" :key="i"
                    :style="{
                      padding:'5px 6px',
                      borderRadius:p.btnStyle==='pill'?'999px':'5px',
                      fontSize:'5.5px',fontWeight:700,textAlign:'center',
                      color:'#fff', background:p.btnColor,
                      opacity:i===0?1:i===1?0.8:i===2?0.6:0.45,
                    }">{{ l }}</div>
                </div>
              </div>
            </template>

            <!-- Home indicator -->
            <div style="position:absolute;bottom:4px;left:50%;transform:translateX(-50%);width:35%;height:3px;background:rgba(255,255,255,0.2);border-radius:2px;z-index:20"></div>

          </div>
        </div>
      </div>

      <!-- Label -->
      <div style="padding:6px 8px 8px;text-align:center">
        <p :style="{fontSize:'10px',fontWeight:700,color:selected===p.id?'#A78BFA':'rgba(255,255,255,0.7)',margin:'0 0 1px'}">{{ p.label }}</p>
        <p style="font-size:8px;color:rgba(255,255,255,0.25);margin:0">{{ p.vibe }}</p>
      </div>

    </div>
  </div>
</template>

<script setup>
defineProps({ selected: { type: String, default: null } })
defineEmits(['select'])

const ig = { i:'camera',            c:'#E1306C' }
const tt = { i:'music-note-beamed', c:'#111111' }
const tw = { i:'x-lg',             c:'#1a1a1a' }
const sc = { i:'chat-fill',         c:'#FFFC00', dark:true }
const tg = { i:'send-fill',         c:'#26A5E4' }
const yt = { i:'youtube',           c:'#FF0000' }
const of = { i:'heart-fill',        c:'#00AFF0' }
const wh = { i:'whatsapp',          c:'#25D366' }

const presets = [
  {
    id: 'vsl-cinema',
    label: 'VSL Cinema',
    name: 'Karine',
    location: 'Paris, France',
    tagline: 'OnlyFans creator 🔥',
    vibe: 'Full-screen VSL',
    style: 'vsl',
    bg: '#000',
    bottomBg: '#0c0a1a',
    photo: '/karine.JPG',
    socials: [ig, tt, tw, sc, tg, yt],
    thumbs: [
      'https://i.pravatar.cc/80?img=47',
      'https://i.pravatar.cc/80?img=5',
      'https://i.pravatar.cc/80?img=9',
    ],
    apply: { template: 'cinematic', bg_color: '#000', btn_color: '#6D4EE8' },
  },
  {
    id: 'nightfall',
    label: 'Nightfall',
    name: 'Jenna',
    location: 'London, UK',
    bio: 'Come explore my world 🌍',
    vibe: 'Dark & elegant',
    style: 'banner',
    bg: '#0c0c1a',
    photo: 'https://i.pravatar.cc/400?img=1',
    socials: [ig, tt, tw, sc, tg],
    thumbs: [
      'https://i.pravatar.cc/80?img=1',
      'https://i.pravatar.cc/80?img=10',
      'https://i.pravatar.cc/80?img=11',
      'https://i.pravatar.cc/80?img=12',
    ],
    apply: { template: 'nightfall', bg_color: '#0c0c1a', btn_color: '#818CF8' },
  },
  {
    id: 'rose-cinema',
    label: 'Rose',
    name: 'Rose',
    location: 'Milan',
    tagline: '20 🌙 | Influencer',
    vibe: 'Pink cinematic',
    style: 'vsl',
    bg: '#000',
    bottomBg: '#12000a',
    photo: 'https://i.pravatar.cc/400?img=32',
    socials: [ig, tt, tw, sc, tg, of],
    thumbs: [
      'https://i.pravatar.cc/80?img=32',
      'https://i.pravatar.cc/80?img=20',
      'https://i.pravatar.cc/80?img=21',
    ],
    apply: { template: 'cinematic', bg_color: '#0d0005', btn_color: '#EC4899' },
  },
  {
    id: 'ambre',
    label: 'Ambre',
    name: 'Ambre',
    location: 'Paris, France',
    bio: 'Fashion Lifestyle 🔥',
    vibe: 'Warm & fashion',
    style: 'carousel',
    bg: '#FFF0EB',
    btnColor: '#F97316',
    btnStyle: 'pill',
    textColor: '#111827',
    photo: 'https://i.pravatar.cc/400?img=41',
    socials: [ig, tt, tw, of, tg],
    apply: { template: 'minimal', bg_color: '#FFF0EB', btn_color: '#F97316' },
  },
  {
    id: 'minimal',
    label: 'Minimal',
    name: 'Sofia',
    location: 'Singapour',
    bio: 'Never dreamed about success, just work for it',
    vibe: 'Clean & minimal',
    style: 'classic',
    bg: '#ffffff',
    btnColor: '#8B1A2B',
    btnStyle: 'rounded',
    textColor: '#111827',
    photo: 'https://i.pravatar.cc/400?img=48',
    socials: [ig, tt, tw, sc, wh, yt],
    links: ['Mon OnlyFans', 'Mon MYM', 'Mon Instagram', 'Mon Telegram'],
    apply: { template: 'minimal', bg_color: '#ffffff', btn_color: '#8B1A2B' },
  },
]
</script>
