<template>
  <div>
    <!-- Grid -->
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px">
      <div v-for="t in templates" :key="t.id"
        @click="$emit('update:modelValue', t.id)"
        :style="{
          position:'relative',
          borderRadius:'16px',
          overflow:'hidden',
          cursor:'pointer',
          border: modelValue===t.id ? '2px solid #6D4EE8' : '2px solid rgba(255,255,255,0.07)',
          boxShadow: modelValue===t.id ? '0 0 0 3px rgba(109,78,232,0.2)' : 'none',
          transition:'all 0.15s',
          background:'#0d0b1e',
        }">

        <!-- Checkmark -->
        <div v-if="modelValue===t.id" style="position:absolute;top:10px;right:10px;z-index:20;width:22px;height:22px;border-radius:50%;background:#6D4EE8;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(109,78,232,0.5)">
          <i class="bi bi-check2" style="color:#fff;font-size:13px;font-weight:800"></i>
        </div>

        <!-- Phone mockup -->
        <div style="padding:14px 14px 0;display:flex;justify-content:center">
          <div :style="{
            width:'120px',
            minHeight:'200px',
            borderRadius:'18px',
            overflow:'hidden',
            background:t.bg,
            position:'relative',
            boxShadow:'0 8px 32px rgba(0,0,0,0.5)',
            border:'1px solid rgba(255,255,255,0.08)',
          }">

            <!-- CLASSIC template preview -->
            <template v-if="t.id==='classic'">
              <div style="position:relative;height:100%">
                <!-- Gradient bg -->
                <div :style="{position:'absolute',inset:0,background:`linear-gradient(to bottom,${t.bg} 0%,${t.bg} 100%)`}"></div>
                <!-- Avatar -->
                <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;padding:18px 10px 10px">
                  <div :style="{width:'38px',height:'38px',borderRadius:'50%',background:t.avatarGradient,overflow:'hidden',border:'2px solid rgba(255,255,255,0.15)',marginBottom:'7px',flexShrink:0}">
                    <img :src="t.avatar" style="width:100%;height:100%;object-fit:cover" />
                  </div>
                  <div style="display:flex;align-items:center;gap:3px;margin-bottom:2px">
                    <span :style="{fontSize:'9px',fontWeight:800,color:t.textColor,letterSpacing:'-0.02em'}">{{ t.name }}</span>
                    <i class="bi bi-patch-check-fill" style="color:#60a5fa;font-size:8px"></i>
                  </div>
                  <span :style="{fontSize:'7px',color:'rgba(255,255,255,0.4)',marginBottom:'4px'}">{{ t.handle }}</span>
                  <div style="display:flex;gap:3px;margin-bottom:8px">
                    <i v-for="ic in t.icons" :key="ic" :class="`bi bi-${ic}`" style="font-size:8px;color:rgba(255,255,255,0.6)"></i>
                  </div>
                  <!-- Links -->
                  <div style="width:100%;display:flex;flex-direction:column;gap:4px">
                    <div v-for="(l,i) in t.links" :key="i"
                      :style="{borderRadius:'6px',padding:'5px 8px',fontSize:'7px',fontWeight:700,color:'#fff',background:t.btnColor,textAlign:'center',boxShadow:`0 2px 8px ${t.btnColor}44`}">
                      {{ l }}
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <!-- CINEMATIC template preview -->
            <template v-else-if="t.id==='cinematic'">
              <div style="position:relative;height:200px;overflow:hidden">
                <img :src="t.avatar" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" />
                <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.15) 0%,transparent 30%,rgba(0,0,0,0.85) 100%)"></div>
                <!-- VSL badge -->
                <div style="position:absolute;top:7px;left:7px;background:rgba(0,0,0,0.6);border-radius:999px;padding:2px 6px;display:flex;align-items:center;gap:3px">
                  <i class="bi bi-play-fill" style="font-size:5px;color:#fff"></i><span style="font-size:6px;font-weight:800;color:#fff">VSL</span>
                </div>
                <!-- Online badge -->
                <div style="position:absolute;top:7px;right:7px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:999px;padding:2px 5px;display:flex;align-items:center;gap:2px">
                  <span style="width:4px;height:4px;border-radius:50%;background:#10b981;display:inline-block"></span>
                  <span style="font-size:5px;color:#10b981;font-weight:700">Online</span>
                </div>
                <!-- Bottom content -->
                <div style="position:absolute;bottom:0;left:0;right:0;padding:6px 8px">
                  <div style="display:flex;align-items:center;gap:5px;margin-bottom:5px">
                    <img :src="t.avatar" style="width:16px;height:16px;border-radius:50%;border:1px solid rgba(255,255,255,0.3);object-fit:cover;flex-shrink:0" />
                    <div>
                      <span style="font-size:8px;font-weight:800;color:#fff;display:block;line-height:1">{{ t.name }}</span>
                      <span style="font-size:6px;color:rgba(255,255,255,0.5)">{{ t.handle }}</span>
                    </div>
                  </div>
                  <div style="display:flex;flex-direction:column;gap:3px">
                    <div v-for="(l,i) in t.links" :key="i"
                      :style="{borderRadius:'5px',padding:'4px 6px',fontSize:'6px',fontWeight:700,color:'#fff',background:t.btnColor,textAlign:'center'}">
                      {{ l }}
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <!-- NEON template preview -->
            <template v-else-if="t.id==='neon'">
              <div style="position:relative;display:flex;flex-direction:column;align-items:center;padding:16px 10px 10px">
                <div :style="{width:'38px',height:'38px',borderRadius:'50%',overflow:'hidden',border:`2px solid ${t.btnColor}`,boxShadow:`0 0 12px ${t.btnColor}66`,marginBottom:'7px',flexShrink:0}">
                  <img :src="t.avatar" style="width:100%;height:100%;object-fit:cover" />
                </div>
                <span :style="{fontSize:'9px',fontWeight:800,color:t.btnColor,letterSpacing:'-0.02em',marginBottom:'2px'}">{{ t.name }}</span>
                <span :style="{fontSize:'6px',color:'rgba(255,255,255,0.35)',marginBottom:'8px'}">{{ t.handle }}</span>
                <div style="width:100%;display:flex;flex-direction:column;gap:4px">
                  <div v-for="(l,i) in t.links" :key="i"
                    :style="{borderRadius:'6px',padding:'5px 8px',fontSize:'7px',fontWeight:700,color:t.btnColor,background:'transparent',textAlign:'center',border:`1.5px solid ${t.btnColor}`,boxShadow:`0 0 8px ${t.btnColor}33`}">
                    {{ l }}
                  </div>
                </div>
              </div>
            </template>

            <!-- CARD template preview -->
            <template v-else-if="t.id==='card'">
              <div style="position:relative;height:200px;overflow:hidden;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:0 12px 16px">
                <!-- Navy gradient bg + purple glow -->
                <div style="position:absolute;inset:0;background:linear-gradient(160deg,#060c18 0%,#080f1d 60%,#060810 100%)">
                  <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 42% 38%,rgba(109,78,232,0.14),transparent 62%)"></div>
                </div>
                <!-- VSL badge solid purple -->
                <div style="position:absolute;top:8px;left:8px;background:#6D4EE8;border-radius:999px;padding:3px 8px;display:flex;align-items:center;gap:3px;box-shadow:0 2px 8px rgba(109,78,232,0.5)">
                  <i class="bi bi-play-fill" style="font-size:5px;color:#fff"></i><span style="font-size:5.5px;font-weight:800;color:#fff">VSL</span>
                </div>
                <!-- Video player centered -->
                <div style="position:relative;z-index:1;width:88px;border-radius:11px;overflow:hidden;box-shadow:0 8px 28px rgba(0,0,0,0.7),0 0 0 1px rgba(255,255,255,0.07);margin-bottom:11px;margin-top:10px">
                  <img :src="t.avatar" style="width:100%;aspect-ratio:16/9;object-fit:cover;display:block" />
                  <!-- Play button overlay -->
                  <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.25)">
                    <div style="width:20px;height:20px;border-radius:50%;background:rgba(255,255,255,0.18);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center">
                      <div style="width:0;height:0;border-top:5px solid transparent;border-bottom:5px solid transparent;border-left:8px solid rgba(255,255,255,0.9);margin-left:2px"></div>
                    </div>
                  </div>
                </div>
                <!-- Name + handle -->
                <div style="position:relative;z-index:1;width:100%;margin-bottom:9px">
                  <p :style="{fontSize:'8px',fontWeight:800,color:'#fff',margin:0,lineHeight:1.2,letterSpacing:'-0.02em'}">{{ t.name }}</p>
                  <p :style="{fontSize:'6px',color:'rgba(255,255,255,0.4)',margin:'2px 0 0'}">{{ t.handle }}</p>
                </div>
                <!-- CTA button -->
                <div style="position:relative;z-index:1;width:100%;display:flex;flex-direction:column;gap:4px">
                  <div v-for="(l,i) in t.links" :key="i"
                    :style="{borderRadius:'7px',padding:'5px 8px',fontSize:'6.5px',fontWeight:700,color:'#fff',background:t.btnColor,textAlign:'center',boxShadow:`0 2px 10px ${t.btnColor}55`}">
                    <i class="bi bi-arrow-right"></i> {{ l }}
                  </div>
                </div>
              </div>
            </template>

            <!-- VSL-BANDEAU template preview -->
            <template v-else-if="t.id==='vsl-bandeau'">
              <div style="position:relative;height:100%;background:#0d0d0d;display:flex;flex-direction:column;justify-content:flex-end;padding:8px">
                <div style="font-size:8px;font-weight:700;color:#fff;margin-bottom:4px">{{ t.name }}</div>
                <div style="width:100%;height:22px;background:rgba(255,255,255,0.06);border-radius:6px;display:flex;align-items:center;justify-content:center">
                  <span style="font-size:6px;color:rgba(255,255,255,0.5)">▲ Voir plus</span>
                </div>
              </div>
            </template>

            <!-- VSL-POPUP template preview -->
            <template v-else-if="t.id==='vsl-popup'">
              <div style="position:relative;height:100%;background:#0d0d0d;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:6px">
                <div style="background:#13101f;border-radius:10px;padding:8px;width:100%;border:1px solid rgba(255,255,255,0.1);text-align:center">
                  <div style="width:100%;height:28px;background:rgba(255,255,255,0.06);border-radius:5px;margin-bottom:5px"></div>
                  <div style="font-size:6px;color:rgba(255,255,255,0.4);margin-bottom:5px">Rejoins-moi !</div>
                  <div :style="{background:t.btnColor,borderRadius:'5px',padding:'4px',fontSize:'6px',fontWeight:700,color:'#fff'}">S'abonner</div>
                </div>
              </div>
            </template>

            <!-- MINIMAL template preview -->
            <template v-else-if="t.id==='minimal'">
              <div style="position:relative;display:flex;flex-direction:column;align-items:center;padding:16px 10px 10px">
                <div :style="{width:'38px',height:'38px',borderRadius:'50%',overflow:'hidden',border:'2px solid #E5E7EB',marginBottom:'7px',flexShrink:0}">
                  <img :src="t.avatar" style="width:100%;height:100%;object-fit:cover" />
                </div>
                <span style="font-size:9px;font-weight:800;color:#111827;letter-spacing:-0.02em;margin-bottom:2px">{{ t.name }}</span>
                <span style="font-size:6px;color:#9CA3AF;margin-bottom:8px">{{ t.handle }}</span>
                <div style="width:100%;display:flex;flex-direction:column;gap:4px">
                  <div v-for="(l,i) in t.links" :key="i"
                    style="border-radius:6px;padding:5px 8px;font-size:7px;font-weight:700;color:#111827;background:#fff;text-align:center;border:1px solid #E5E7EB;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
                    {{ l }}
                  </div>
                </div>
              </div>
            </template>

          </div>
        </div>

        <!-- Label -->
        <div style="padding:10px 12px 12px;text-align:center">
          <p :style="{fontSize:'12px',fontWeight:700,color:modelValue===t.id?'#A78BFA':'rgba(255,255,255,0.7)',margin:'0 0 2px'}">{{ t.label }}</p>
          <p style="font-size:10px;color:rgba(255,255,255,0.3);margin:0">{{ t.desc }}</p>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ modelValue: String })
defineEmits(['update:modelValue'])

const templates = [
  {
    id: 'classic',
    label: 'Classic',
    desc: 'Dark & premium',
    bg: '#0d0d0d',
    btnColor: '#6D4EE8',
    textColor: '#fff',
    avatarGradient: '#333',
    avatar: 'https://i.pravatar.cc/150?img=47',
    name: 'Sofia',
    handle: '@sofiamodel',
    icons: ['camera','music-note-beamed','x-lg','send'],
    links: ['Subscribe on OnlyFans', 'My Instagram'],
  },
  {
    id: 'cinematic',
    label: 'Cinematic',
    desc: 'Vidéo plein écran',
    bg: '#000',
    btnColor: '#6D4EE8',
    textColor: '#fff',
    avatar: 'https://i.pravatar.cc/150?img=25',
    name: 'Erika',
    handle: '@erikavip',
    links: ['Subscribe on OnlyFans'],
  },
  {
    id: 'neon',
    label: 'Neon',
    desc: 'Glowing borders',
    bg: '#0a0118',
    btnColor: '#A78BFA',
    textColor: '#A78BFA',
    avatar: 'https://i.pravatar.cc/150?img=32',
    name: 'Bianca',
    handle: '@biancacreator',
    links: ['Subscribe on OnlyFans', 'My Telegram'],
  },
  {
    id: 'minimal',
    label: 'Minimal',
    desc: 'Clean & bright',
    bg: '#F9FAFB',
    btnColor: '#fff',
    textColor: '#111827',
    avatar: 'https://i.pravatar.cc/150?img=41',
    name: 'Ambre',
    handle: '@ambrefashion',
    links: ['Subscribe on MYM', 'My Instagram'],
  },
  {
    id: 'vsl-bandeau',
    label: 'Tiroir',
    desc: 'Liens dans un drawer',
    bg: '#0d0d0d',
    btnColor: '#6D4EE8',
    textColor: '#fff',
    name: 'Sofia',
    handle: '@sofiamodel',
    links: ['My OnlyFans'],
  },
  {
    id: 'vsl-popup',
    label: 'Popup',
    desc: 'Popup central avec image',
    bg: '#0d0d0d',
    btnColor: '#6D4EE8',
    textColor: '#fff',
    name: 'Sofia',
    handle: '@sofiamodel',
    links: ['Rejoindre'],
  },
  {
    id: 'card',
    label: 'Card',
    desc: 'VSL + glass card',
    bg: '#050508',
    btnColor: '#6D4EE8',
    textColor: '#fff',
    avatar: 'https://i.pravatar.cc/150?img=36',
    name: 'Léa',
    handle: '@leavip',
    links: ['Subscribe on OnlyFans'],
  },
]
</script>
