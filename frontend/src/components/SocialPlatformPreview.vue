<template>
  <div :style="{position:'relative',width:'200px',flexShrink:0}">

    <!-- Phone shell -->
    <div :style="{
      width:'200px',
      background:'#0a0a0f',
      borderRadius:'28px',
      border:'2px solid rgba(255,255,255,0.12)',
      overflow:'hidden',
      boxShadow:'0 24px 60px rgba(0,0,0,0.6)',
      position:'relative',
    }">

      <!-- Notch -->
      <div style="height:22px;display:flex;align-items:center;justify-content:center;padding-top:6px">
        <div style="width:48px;height:6px;background:#1a1a2e;border-radius:999px"></div>
      </div>

      <!-- Platform screen -->
      <div :style="{height:'340px',position:'relative',overflow:'hidden',background:platform.bg}">

        <!-- === INSTAGRAM === -->
        <template v-if="platform.id==='instagram'">
          <!-- Step 0: profile page -->
          <div :style="{position:'absolute',inset:0,opacity:step===0?1:0,transition:'opacity 0.5s'}">
            <!-- Header -->
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px">
              <span style="font-size:11px;font-weight:800;color:#fff">{{ handle }}</span>
              <div style="display:flex;gap:8px">
                <i class="bi bi-plus-square" style="color:#fff;font-size:14px"></i>
                <i class="bi bi-list" style="color:#fff;font-size:14px"></i>
              </div>
            </div>
            <!-- Profile info -->
            <div style="padding:6px 10px">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div :style="{width:'52px',height:'52px',borderRadius:'50%',background:`linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)`,padding:'2px',flexShrink:0}">
                  <div :style="{width:'100%',height:'100%',borderRadius:'50%',overflow:'hidden',border:'2px solid #000'}">
                    <img :src="avatarUrl" style="width:100%;height:100%;object-fit:cover" />
                  </div>
                </div>
                <div style="display:flex;gap:12px;flex:1;justify-content:space-around">
                  <div style="text-align:center"><p style="font-size:11px;font-weight:800;color:#fff;margin:0">247</p><p style="font-size:8px;color:rgba(255,255,255,0.55);margin:0">posts</p></div>
                  <div style="text-align:center"><p style="font-size:11px;font-weight:800;color:#fff;margin:0">48.2K</p><p style="font-size:8px;color:rgba(255,255,255,0.55);margin:0">followers</p></div>
                  <div style="text-align:center"><p style="font-size:11px;font-weight:800;color:#fff;margin:0">312</p><p style="font-size:8px;color:rgba(255,255,255,0.55);margin:0">following</p></div>
                </div>
              </div>
              <p style="font-size:9px;font-weight:700;color:#fff;margin:0 0 1px">{{ displayName }}</p>
              <p style="font-size:8px;color:rgba(255,255,255,0.5);margin:0 0 3px">✨ Content creator · 🔞 18+</p>
              <!-- BIO LINK — highlighted -->
              <div :style="{
                display:'flex',alignItems:'center',gap:'4px',
                background: step===0 ? 'rgba(0,149,246,0.15)' : 'transparent',
                borderRadius:'4px',padding:'2px 4px',
                transition:'background 0.3s',
                cursor:'pointer',
              }" @click="() => {}">
                <i class="bi bi-link-45deg" style="color:#0095f6;font-size:10px"></i>
                <span style="font-size:9px;color:#0095f6;font-weight:600">mysocialvsl.com/{{ slug }}</span>
              </div>
              <!-- Tap indicator -->
              <div v-if="step===0 && showTap" :style="{
                position:'absolute',
                top:'112px',left:'38px',
                width:'20px',height:'20px',
                borderRadius:'50%',
                background:'rgba(255,255,255,0.35)',
                border:'2px solid rgba(255,255,255,0.8)',
                animation:'tap-pulse 0.6s ease-out',
              }"></div>
            </div>
            <!-- Grid posts -->
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;margin-top:4px">
              <div v-for="n in 9" :key="n" :style="{aspectRatio:'1',background:`hsl(${n*37},30%,${15+n*3}%)`}"></div>
            </div>
          </div>

          <!-- Step 1: deeplink redirect animation -->
          <div :style="{position:'absolute',inset:0,opacity:step===1?1:0,transition:'opacity 0.5s',background:'#000',display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',gap:'12px'}">
            <div :style="{
              width:'40px',height:'40px',borderRadius:'50%',
              border:'3px solid rgba(109,78,232,0.3)',
              borderTop:'3px solid #6D4EE8',
              animation:'spin 0.8s linear infinite',
            }"></div>
            <p style="font-size:9px;color:rgba(255,255,255,0.5);text-align:center;margin:0">Opening in Safari…<br><span style="color:#6D4EE8;font-weight:600">deeplink active</span></p>
          </div>

          <!-- Step 2: VSL landing page -->
          <div :style="{position:'absolute',inset:0,opacity:step===2?1:0,transition:'opacity 0.5s'}">
            <VSLPageMockup :avatar="avatarUrl" :name="displayName" :bg="landingBg" :btn="landingBtn" :videoUrl="videoUrl" />
          </div>
        </template>

        <!-- === TIKTOK === -->
        <template v-else-if="platform.id==='tiktok'">
          <div :style="{position:'absolute',inset:0,opacity:step===0?1:0,transition:'opacity 0.5s',background:'#000'}">
            <!-- Header -->
            <div style="display:flex;align-items:center;justify-content:center;padding:8px;position:relative">
              <span style="font-size:10px;font-weight:700;color:#fff">Profile</span>
              <i class="bi bi-three-dots" style="color:#fff;position:absolute;right:10px;font-size:13px"></i>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;padding:8px">
              <div :style="{width:'56px',height:'56px',borderRadius:'50%',overflow:'hidden',border:'2px solid rgba(255,255,255,0.2)',marginBottom:'6px'}">
                <img :src="avatarUrl" style="width:100%;height:100%;object-fit:cover" />
              </div>
              <p style="font-size:11px;font-weight:800;color:#fff;margin:0 0 2px">@{{ handle }}</p>
              <div style="display:flex;gap:14px;margin:6px 0">
                <div style="text-align:center"><p style="font-size:11px;font-weight:800;color:#fff;margin:0">1.2M</p><p style="font-size:7px;color:rgba(255,255,255,0.5);margin:0">Followers</p></div>
                <div style="text-align:center"><p style="font-size:11px;font-weight:800;color:#fff;margin:0">84K</p><p style="font-size:7px;color:rgba(255,255,255,0.5);margin:0">Likes</p></div>
              </div>
              <!-- Bio link -->
              <div :style="{background:'rgba(255,255,255,0.08)',borderRadius:'6px',padding:'5px 10px',marginTop:'4px',display:'flex',alignItems:'center',gap:'4px'}">
                <i class="bi bi-link-45deg" style="color:#fe2c55;font-size:10px"></i>
                <span style="font-size:8px;color:rgba(255,255,255,0.7)">mysocialvsl.com/{{ slug }}</span>
              </div>
              <!-- TikTok videos grid -->
              <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;width:100%;margin-top:10px">
                <div v-for="n in 6" :key="n" :style="{aspectRatio:'9/16',background:`hsl(${n*50},40%,${12+n*4}%)`,position:'relative'}">
                  <i class="bi bi-play-fill" style="position:absolute;bottom:3px;left:3px;color:rgba(255,255,255,0.6);font-size:7px"></i>
                  <span style="position:absolute;bottom:3px;right:3px;font-size:6px;color:rgba(255,255,255,0.5)">{{ [48,122,310,87,205,33][n-1] }}K</span>
                </div>
              </div>
            </div>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===1?1:0,transition:'opacity 0.5s',background:'#000',display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',gap:'12px'}">
            <div :style="{width:'40px',height:'40px',borderRadius:'50%',border:'3px solid rgba(254,44,85,0.3)',borderTop:'3px solid #fe2c55',animation:'spin 0.8s linear infinite'}"></div>
            <p style="font-size:9px;color:rgba(255,255,255,0.5);text-align:center;margin:0">Opening in browser…<br><span style="color:#fe2c55;font-weight:600">deeplink active</span></p>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===2?1:0,transition:'opacity 0.5s'}">
            <VSLPageMockup :avatar="avatarUrl" :name="displayName" :bg="landingBg" :btn="landingBtn" :videoUrl="videoUrl" />
          </div>
        </template>

        <!-- === SNAPCHAT === -->
        <template v-else-if="platform.id==='snapchat'">
          <div :style="{position:'absolute',inset:0,opacity:step===0?1:0,transition:'opacity 0.5s',background:'#FFFC00'}">
            <div style="display:flex;flex-direction:column;align-items:center;padding:20px 10px 10px">
              <div :style="{width:'60px',height:'60px',borderRadius:'50%',overflow:'hidden',border:'3px solid rgba(0,0,0,0.15)',marginBottom:'8px'}">
                <img :src="avatarUrl" style="width:100%;height:100%;object-fit:cover" />
              </div>
              <p style="font-size:12px;font-weight:800;color:#000;margin:0 0 2px">{{ displayName }}</p>
              <p style="font-size:9px;color:rgba(0,0,0,0.5);margin:0 0 10px">@{{ handle }}</p>
              <div style="background:rgba(0,0,0,0.08);border-radius:8px;padding:6px 12px;display:flex;align-items:center;gap:4px">
                <i class="bi bi-link-45deg" style="color:#000;font-size:10px"></i>
                <span style="font-size:8px;color:#000;font-weight:600">mysocialvsl.com/{{ slug }}</span>
              </div>
              <div style="display:flex;gap:8px;margin-top:12px">
                <div style="background:rgba(0,0,0,0.1);border-radius:999px;padding:5px 14px;font-size:9px;font-weight:700;color:#000">+ Add Friend</div>
                <div style="background:rgba(0,0,0,0.1);border-radius:999px;padding:5px 12px;font-size:9px;font-weight:700;color:#000">Message</div>
              </div>
            </div>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===1?1:0,transition:'opacity 0.5s',background:'#FFFC00',display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',gap:'12px'}">
            <div :style="{width:'40px',height:'40px',borderRadius:'50%',border:'3px solid rgba(0,0,0,0.15)',borderTop:'3px solid #000',animation:'spin 0.8s linear infinite'}"></div>
            <p style="font-size:9px;color:rgba(0,0,0,0.5);text-align:center;margin:0">Opening in browser…<br><span style="color:#000;font-weight:600">deeplink active</span></p>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===2?1:0,transition:'opacity 0.5s'}">
            <VSLPageMockup :avatar="avatarUrl" :name="displayName" :bg="landingBg" :btn="landingBtn" :videoUrl="videoUrl" />
          </div>
        </template>

        <!-- === TWITTER/X === -->
        <template v-else-if="platform.id==='twitter'">
          <div :style="{position:'absolute',inset:0,opacity:step===0?1:0,transition:'opacity 0.5s',background:'#000'}">
            <div :style="{height:'60px',background:'linear-gradient(135deg,#1a1a2e,#16213e)',position:'relative'}">
              <div :style="{position:'absolute',bottom:'-20px',left:'10px',width:'44px',height:'44px',borderRadius:'50%',overflow:'hidden',border:'2px solid #000'}">
                <img :src="avatarUrl" style="width:100%;height:100%;object-fit:cover" />
              </div>
            </div>
            <div style="padding:28px 10px 10px">
              <p style="font-size:11px;font-weight:800;color:#fff;margin:0 0 1px">{{ displayName }}</p>
              <p style="font-size:9px;color:rgba(255,255,255,0.4);margin:0 0 6px">@{{ handle }}</p>
              <p style="font-size:9px;color:rgba(255,255,255,0.7);margin:0 0 4px">✨ Content creator · 18+ 🔞</p>
              <div style="display:flex;align-items:center;gap:3px;margin-bottom:8px">
                <i class="bi bi-link-45deg" style="color:rgba(255,255,255,0.4);font-size:9px"></i>
                <span style="font-size:8px;color:#1d9bf0">mysocialvsl.com/{{ slug }}</span>
              </div>
              <div style="display:flex;gap:12px">
                <span style="font-size:9px;color:rgba(255,255,255,0.5)"><strong style="color:#fff">312</strong> Following</span>
                <span style="font-size:9px;color:rgba(255,255,255,0.5)"><strong style="color:#fff">48.2K</strong> Followers</span>
              </div>
            </div>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===1?1:0,transition:'opacity 0.5s',background:'#000',display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',gap:'12px'}">
            <div :style="{width:'40px',height:'40px',borderRadius:'50%',border:'3px solid rgba(29,155,240,0.3)',borderTop:'3px solid #1d9bf0',animation:'spin 0.8s linear infinite'}"></div>
            <p style="font-size:9px;color:rgba(255,255,255,0.5);text-align:center;margin:0">Opening in browser…<br><span style="color:#1d9bf0;font-weight:600">deeplink active</span></p>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===2?1:0,transition:'opacity 0.5s'}">
            <VSLPageMockup :avatar="avatarUrl" :name="displayName" :bg="landingBg" :btn="landingBtn" :videoUrl="videoUrl" />
          </div>
        </template>

        <!-- === GENERIC (facebook, threads, reddit, pinterest) === -->
        <template v-else>
          <div :style="{position:'absolute',inset:0,opacity:step===0?1:0,transition:'opacity 0.5s',background:platform.bg}">
            <div style="display:flex;flex-direction:column;align-items:center;padding:20px 10px 10px">
              <div :style="{width:'56px',height:'56px',borderRadius:'50%',overflow:'hidden',border:`2px solid ${platform.color}33`,marginBottom:'8px'}">
                <img :src="avatarUrl" style="width:100%;height:100%;object-fit:cover" />
              </div>
              <p :style="{fontSize:'11px',fontWeight:800,color:platform.textColor,margin:'0 0 2px'}">{{ displayName }}</p>
              <p :style="{fontSize:'9px',color:platform.textColor+'88',margin:'0 0 8px'}">@{{ handle }}</p>
              <div :style="{background:platform.color+'22',borderRadius:'8px',padding:'5px 10px',display:'flex',alignItems:'center',gap:'4px'}">
                <i class="bi bi-link-45deg" :style="{color:platform.color,fontSize:'10px'}"></i>
                <span :style="{fontSize:'8px',color:platform.color,fontWeight:600}">mysocialvsl.com/{{ slug }}</span>
              </div>
            </div>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===1?1:0,transition:'opacity 0.5s',background:platform.bg,display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',gap:'12px'}">
            <div :style="{width:'40px',height:'40px',borderRadius:'50%',border:`3px solid ${platform.color}33`,borderTop:`3px solid ${platform.color}`,animation:'spin 0.8s linear infinite'}"></div>
            <p :style="{fontSize:'9px',color:platform.textColor+'66',textAlign:'center',margin:0}">Opening in browser…<br><span :style="{color:platform.color,fontWeight:600}">deeplink active</span></p>
          </div>
          <div :style="{position:'absolute',inset:0,opacity:step===2?1:0,transition:'opacity 0.5s'}">
            <VSLPageMockup :avatar="avatarUrl" :name="displayName" :bg="landingBg" :btn="landingBtn" :videoUrl="videoUrl" />
          </div>
        </template>

      </div>

      <!-- Home bar -->
      <div style="height:20px;display:flex;align-items:center;justify-content:center">
        <div style="width:60px;height:3px;background:rgba(255,255,255,0.3);border-radius:999px"></div>
      </div>
    </div>

    <!-- Step dots -->
    <div style="display:flex;justify-content:center;gap:5px;margin-top:10px">
      <div v-for="i in 3" :key="i"
        :style="{
          width: step===i-1?'16px':'5px', height:'5px',
          borderRadius:'999px',
          background: step===i-1?'#6D4EE8':'rgba(255,255,255,0.2)',
          transition:'all 0.3s',
        }"></div>
    </div>

    <!-- Step label -->
    <p style="text-align:center;font-size:10px;color:rgba(255,255,255,0.4);margin:6px 0 0;font-family:Inter,sans-serif">
      {{ ['Profile page','Deeplink redirect','VSL Landing page'][step] }}
    </p>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import VSLPageMockup from '@/components/VSLPageMockup.vue'

const props = defineProps({
  platformId:  { type: String, default: 'instagram' },
  handle:      { type: String, default: 'karine.vip' },
  displayName: { type: String, default: 'Karine' },
  slug:        { type: String, default: 'karine-UFN4V' },
  avatarUrl:   { type: String, default: 'https://i.pravatar.cc/300?img=47' },
  landingBg:   { type: String, default: '#0d0d0d' },
  landingBtn:  { type: String, default: '#00aff0' },
  videoUrl:    { type: String, default: 'https://streamable.com/e/0ed1q5' },
})

const PLATFORMS = {
  instagram: { id:'instagram', bg:'#000',        color:'#0095f6', textColor:'#fff' },
  tiktok:    { id:'tiktok',    bg:'#000',        color:'#fe2c55', textColor:'#fff' },
  snapchat:  { id:'snapchat',  bg:'#FFFC00',     color:'#000',    textColor:'#000' },
  twitter:   { id:'twitter',   bg:'#000',        color:'#1d9bf0', textColor:'#fff' },
  facebook:  { id:'facebook',  bg:'#18191a',     color:'#1877F2', textColor:'#fff' },
  threads:   { id:'threads',   bg:'#101010',     color:'#fff',    textColor:'#fff' },
  reddit:    { id:'reddit',    bg:'#dae0e6',     color:'#ff4500', textColor:'#1c1c1c' },
  pinterest: { id:'pinterest', bg:'#fff',        color:'#e60023', textColor:'#111' },
}

const platform = computed(() => PLATFORMS[props.platformId] || PLATFORMS.instagram)

const step = ref(0)
const showTap = ref(false)
let timer = null

function runAnimation() {
  step.value = 0
  showTap.value = false
  // Show tap indicator at step 0
  setTimeout(() => { showTap.value = true }, 1200)
  // Go to deeplink step
  setTimeout(() => { step.value = 1; showTap.value = false }, 2000)
  // Go to VSL landing page
  setTimeout(() => { step.value = 2 }, 3200)
  // Loop
  timer = setTimeout(() => { runAnimation() }, 5500)
}

onMounted(() => { runAnimation() })
onUnmounted(() => { clearTimeout(timer) })
</script>

<style>
@keyframes tap-pulse {
  0%   { transform: scale(0.5); opacity: 1 }
  100% { transform: scale(1.5); opacity: 0 }
}
@keyframes spin { to { transform: rotate(360deg) } }
@keyframes fadeIn { from { opacity:0; transform:translateY(4px) } to { opacity:1; transform:translateY(0) } }
</style>
