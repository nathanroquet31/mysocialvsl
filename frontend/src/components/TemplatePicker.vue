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
                    <span style="color:#60a5fa;font-size:8px">✓</span>
                  </div>
                  <span :style="{fontSize:'7px',color:'rgba(255,255,255,0.4)',marginBottom:'4px'}">{{ t.handle }}</span>
                  <div style="display:flex;gap:3px;margin-bottom:8px">
                    <span v-for="ic in t.icons" :key="ic" style="font-size:8px">{{ ic }}</span>
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
                  <span style="font-size:6px;font-weight:800;color:#fff">▶ VSL</span>
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
    icons: ['📸','🎵','✕','✈️'],
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
]
</script>
