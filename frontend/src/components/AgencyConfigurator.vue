<template>
  <div :style="{
    border: `1.5px solid ${props.dark ? 'rgba(109,78,232,0.35)' : '#C7BBFF'}`,
    borderRadius: '16px',
    overflow: 'hidden',
    background: props.dark ? 'rgba(109,78,232,0.06)' : '#FDFCFF',
    fontFamily: 'Inter, sans-serif',
  }">

    <!-- Header -->
    <div :style="{
      padding: '20px 24px',
      borderBottom: `1px solid ${props.dark ? 'rgba(255,255,255,0.08)' : '#EDE9FE'}`,
      display: 'flex', alignItems: 'center', gap: '12px',
    }">
      <div :style="{
        width: '36px', height: '36px', borderRadius: '10px',
        background: 'linear-gradient(135deg,#5940D1,#6D4EE8)',
        display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
      }">
        <i class="bi bi-buildings" style="color:#fff;font-size:16px"></i>
      </div>
      <div>
        <p :style="{fontSize:'15px',fontWeight:700,color:props.dark?'#fff':'#111827',margin:0}">Agency</p>
        <p :style="{fontSize:'12px',color:props.dark?'rgba(255,255,255,0.45)':'#9CA3AF',margin:0}">Customize your plan to fit your needs</p>
      </div>
    </div>

    <div style="display:flex;gap:0">

      <!-- LEFT: presets + sliders -->
      <div :style="{flex:1,padding:'24px',borderRight:`1px solid ${props.dark?'rgba(255,255,255,0.07)':'#EDE9FE'}`}">

        <!-- Presets -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <p :style="{fontSize:'13px',fontWeight:600,color:props.dark?'rgba(255,255,255,0.7)':'#374151',margin:0}">Start from a preset</p>
          <p :style="{fontSize:'11px',color:props.dark?'rgba(255,255,255,0.3)':'#9CA3AF',margin:0}">Tap one to apply</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:24px">
          <div v-for="p in presets" :key="p.id"
            @click="applyPreset(p)"
            :style="{
              border: selectedPreset===p.id
                ? '1.5px solid #6D4EE8'
                : `1px solid ${props.dark?'rgba(255,255,255,0.1)':'#E5E7EB'}`,
              borderRadius: '10px',
              padding: '12px',
              cursor: 'pointer',
              position: 'relative',
              background: selectedPreset===p.id
                ? (props.dark?'rgba(109,78,232,0.15)':'#F3F0FF')
                : (props.dark?'rgba(255,255,255,0.03)':'#fff'),
              transition: 'all 0.15s',
            }">
            <!-- Most popular badge -->
            <div v-if="p.popular" :style="{
              position:'absolute',top:'-9px',left:'50%',transform:'translateX(-50%)',
              background:'linear-gradient(90deg,#5940D1,#6D4EE8)',
              color:'#fff',fontSize:'9px',fontWeight:700,padding:'2px 8px',
              borderRadius:'999px',letterSpacing:'0.08em',whiteSpace:'nowrap',
            }">MOST POPULAR</div>
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px">
              <i :class="`bi bi-${p.icon}`" :style="{color:'#6D4EE8',fontSize:'13px'}"></i>
              <span :style="{fontSize:'12px',fontWeight:700,color:props.dark?'#fff':'#111827'}">{{ p.label }}</span>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <span v-for="stat in p.stats" :key="stat.label"
                :style="{fontSize:'10px',color:props.dark?'rgba(255,255,255,0.5)':'#6B7280',display:'flex',alignItems:'center',gap:'2px'}">
                <i :class="`bi bi-${stat.icon}`"></i> {{ stat.val }}
              </span>
            </div>
          </div>
        </div>

        <!-- Sliders -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
          <p :style="{fontSize:'13px',fontWeight:600,color:props.dark?'rgba(255,255,255,0.7)':'#374151',margin:0}">Or fine-tune your limits</p>
          <p :style="{fontSize:'11px',color:props.dark?'rgba(255,255,255,0.3)':'#9CA3AF',margin:0}">Drag each slider</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
          <div v-for="sl in sliders" :key="sl.key"
            :style="{
              border:`1px solid ${props.dark?'rgba(255,255,255,0.07)':'#E5E7EB'}`,
              borderRadius:'12px',
              padding:'16px 18px',
              background:props.dark?'rgba(255,255,255,0.02)':'#fff',
            }">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
              <div style="display:flex;align-items:center;gap:10px">
                <div :style="{
                  width:'30px',height:'30px',borderRadius:'8px',
                  background:props.dark?'rgba(109,78,232,0.15)':'#F3F0FF',
                  display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0,
                }">
                  <i :class="`bi bi-${sl.icon}`" style="color:#6D4EE8;font-size:13px"></i>
                </div>
                <div>
                  <p :style="{fontSize:'13px',fontWeight:600,color:props.dark?'#fff':'#111827',margin:0}">{{ sl.label }}</p>
                  <p :style="{fontSize:'11px',color:props.dark?'rgba(255,255,255,0.35)':'#9CA3AF',margin:0}">{{ sl.desc }}</p>
                </div>
              </div>
              <span :style="{fontSize:'22px',fontWeight:800,color:'#6D4EE8'}">
                {{ values[sl.key] === Infinity ? '∞' : values[sl.key] }}
              </span>
            </div>

            <!-- Slider track -->
            <div style="position:relative;margin-bottom:8px">
              <input type="range"
                :min="0" :max="sl.steps.length - 1" :step="1"
                :value="sl.steps.indexOf(values[sl.key])"
                @input="e => setSlider(sl.key, sl.steps[+e.target.value])"
                :style="{
                  width:'100%',
                  accentColor:'#6D4EE8',
                  cursor:'pointer',
                  height:'4px',
                }" />
            </div>

            <!-- Tick labels -->
            <div style="display:flex;justify-content:space-between">
              <span v-for="(step, i) in sl.steps" :key="i"
                :style="{
                  fontSize:'10px',
                  fontWeight: values[sl.key]===step ? '700' : '400',
                  color: values[sl.key]===step ? '#6D4EE8' : (props.dark?'rgba(255,255,255,0.25)':'#D1D5DB'),
                }">
                {{ step === Infinity ? '∞' : step }}
              </span>
            </div>
          </div>
        </div>

        <!-- Contact custom -->
        <p :style="{fontSize:'12px',color:props.dark?'rgba(255,255,255,0.35)':'#9CA3AF',margin:'16px 0 0',display:'flex',alignItems:'center',gap:'4px'}">
          <i class="bi bi-lightning-charge" style="color:#F59E0B"></i>
          Need more?
          <a href="mailto:hello@mysocialvsl.com" style="color:#6D4EE8;font-weight:600;text-decoration:none">Contact us</a>
          for a custom plan.
        </p>

      </div>

      <!-- RIGHT: summary -->
      <div :style="{width:'260px',padding:'24px',display:'flex',flexDirection:'column',flexShrink:0}">

        <!-- Price -->
        <div style="display:flex;align-items:center;gap:'8px';margin-bottom:16px">
          <div :style="{
            width:'22px',height:'22px',borderRadius:'6px',
            background:'#6D4EE8',display:'flex',alignItems:'center',justifyContent:'center',marginRight:'8px',flexShrink:0,
          }">
            <i class="bi bi-check2" style="color:#fff;font-size:12px"></i>
          </div>
          <p :style="{fontSize:'13px',fontWeight:700,color:props.dark?'#fff':'#111827',margin:0}">Your plan summary</p>
        </div>

        <div style="margin-bottom:4px">
          <span :style="{fontSize:'40px',fontWeight:800,color:props.dark?'#fff':'#111827',lineHeight:1}">${{ price }}</span>
          <span :style="{fontSize:'13px',color:props.dark?'rgba(255,255,255,0.4)':'#9CA3AF'}"> / month</span>
        </div>
        <p :style="{fontSize:'11px',color:props.dark?'rgba(255,255,255,0.3)':'#9CA3AF',margin:'0 0 20px'}">
          {{ billing === 'annually' ? `Billed $${price*10}/yr — save 2 months` : 'Billed monthly' }}
        </p>

        <!-- Limits -->
        <div :style="{
          border:`1px solid ${props.dark?'rgba(255,255,255,0.08)':'#E5E7EB'}`,
          borderRadius:'10px',
          padding:'14px',
          marginBottom:'16px',
          background:props.dark?'rgba(255,255,255,0.02)':'#F9FAFB',
        }">
          <p :style="{fontSize:'10px',fontWeight:700,color:props.dark?'rgba(255,255,255,0.4)':'#9CA3AF',textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 10px'}">Your limits</p>
          <div style="display:flex;flex-direction:column;gap:8px">
            <div v-for="sl in sliders" :key="sl.key" style="display:flex;align-items:center;gap:8px">
              <i :class="`bi bi-${sl.icon}`" style="color:#6D4EE8;font-size:13px;flex-shrink:0"></i>
              <span :style="{fontSize:'13px',color:props.dark?'rgba(255,255,255,0.7)':'#374151'}">
                <strong>{{ values[sl.key] === Infinity ? '∞' : values[sl.key] }}</strong> {{ sl.label }}
              </span>
            </div>
          </div>
        </div>

        <!-- Domain bonus -->
        <div :style="{
          background: props.dark ? 'rgba(16,185,129,0.1)' : '#F0FDF4',
          border: `1px solid ${props.dark?'rgba(16,185,129,0.25)':'#BBF7D0'}`,
          borderRadius:'10px',
          padding:'12px 14px',
          marginBottom:'16px',
          display:'flex',alignItems:'center',gap:'10px',
        }">
          <i class="bi bi-gift" style="color:#10B981;font-size:16px;flex-shrink:0"></i>
          <div>
            <p style="font-size:12px;font-weight:700;color:#10B981;margin:0">2 free domains included</p>
            <p :style="{fontSize:'11px',color:props.dark?'rgba(255,255,255,0.4)':'#6B7280',margin:0}">Switch to annual for +1 extra</p>
          </div>
        </div>

        <!-- Features -->
        <div :style="{marginBottom:'20px'}">
          <p :style="{fontSize:'10px',fontWeight:700,color:props.dark?'rgba(255,255,255,0.4)':'#9CA3AF',textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 10px'}">Everything in Agency</p>
          <div style="display:flex;flex-direction:column;gap:7px">
            <div v-for="f in agencyFeatures" :key="f" style="display:flex;align-items:center;gap:8px">
              <i class="bi bi-check-circle-fill" style="color:#6D4EE8;font-size:12px;flex-shrink:0"></i>
              <span :style="{fontSize:'12px',color:props.dark?'rgba(255,255,255,0.6)':'#4B5563'}">{{ f }}</span>
            </div>
          </div>
        </div>

        <!-- CTA -->
        <button @click="$emit('checkout', { pages: values.pages, links: values.links, price, billing })"
          :style="{
            width:'100%',padding:'12px',borderRadius:'10px',
            background:'linear-gradient(135deg,#5940D1,#6D4EE8)',
            color:'#fff',border:'none',fontSize:'14px',fontWeight:700,
            cursor:'pointer',fontFamily:'Inter,sans-serif',
            boxShadow:'0 4px 14px rgba(109,78,232,0.4)',
          }">
          Subscribe Now
        </button>
        <p :style="{fontSize:'11px',color:props.dark?'rgba(255,255,255,0.3)':'#9CA3AF',textAlign:'center',margin:'8px 0 0',display:'flex',alignItems:'center',justifyContent:'center',gap:'4px'}">
          <i class="bi bi-shield-check"></i>
          Secure payment · Cancel anytime
        </p>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  dark: { type: Boolean, default: false },
  billing: { type: String, default: 'monthly' },
})
defineEmits(['checkout'])

const PAGE_STEPS  = [25, 50, 100, 200, 400, 600, 800, Infinity]
const LINK_STEPS  = [25, 50, 100, 200, 400, 600, 800, Infinity]

const sliders = [
  { key: 'pages', label: 'Landing pages',  desc: 'Number of landing pages you can create', icon: 'file-earmark-richtext', steps: PAGE_STEPS },
  { key: 'links', label: 'Direct links',   desc: 'Number of direct links you can create',  icon: 'link-45deg',            steps: LINK_STEPS },
]

const presets = [
  {
    id: 'small', label: 'Small agency', icon: 'rocket-takeoff', popular: false,
    pages: 50, links: 50,
    stats: [{ icon: 'file-earmark-richtext', val: 50, label: 'pages' }, { icon: 'link-45deg', val: 50, label: 'links' }],
  },
  {
    id: 'growing', label: 'Growing agency', icon: 'graph-up-arrow', popular: true,
    pages: 200, links: 100,
    stats: [{ icon: 'file-earmark-richtext', val: 200, label: 'pages' }, { icon: 'link-45deg', val: 100, label: 'links' }],
  },
  {
    id: 'scaling', label: 'Scaling agency', icon: 'building-up', popular: false,
    pages: 400, links: 200,
    stats: [{ icon: 'file-earmark-richtext', val: 400, label: 'pages' }, { icon: 'link-45deg', val: 200, label: 'links' }],
  },
]

const agencyFeatures = [
  'Multi-client team access',
  'Advanced analytics & reports',
  'White label & custom domain',
  'Deeplink bypass (all accounts)',
  'Bot protection (all accounts)',
  'API Access',
  'Priority support',
]

const selectedPreset = ref('growing')
const values = ref({ pages: 200, links: 100 })

function applyPreset(p) {
  selectedPreset.value = p.id
  values.value.pages = p.pages
  values.value.links = p.links
}

function setSlider(key, val) {
  values.value[key] = val
  selectedPreset.value = null
}

// Pricing formula: base $49 + extra pages at $0.15 + extra links at $0.10
const price = computed(() => {
  const p = values.value.pages === Infinity ? 1000 : values.value.pages
  const l = values.value.links  === Infinity ? 500  : values.value.links
  const base = 49
  const extraPages = Math.max(0, p - 50) * 0.15
  const extraLinks = Math.max(0, l - 50) * 0.10
  const raw = base + extraPages + extraLinks
  // Round to nearest $5
  return Math.round(raw / 5) * 5
})
</script>
