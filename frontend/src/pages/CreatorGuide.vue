<template>
  <DashboardLayout title="Creator Guide">

    <!-- Intro -->
    <div :style="{
      background: theme.dark ? 'linear-gradient(135deg,rgba(109,78,232,0.15),rgba(109,78,232,0.05))' : 'linear-gradient(135deg,#F3F0FF,#EEE9FF)',
      border: `1px solid ${theme.dark ? 'rgba(109,78,232,0.3)' : '#C7BBFF'}`,
      borderRadius: '16px', padding: '24px', marginBottom: '24px',
      display: 'flex', alignItems: 'flex-start', gap: '16px',
    }">
      <div style="width:44px;height:44px;background:linear-gradient(135deg,#5940D1,#6D4EE8);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="bi bi-shield-fill-check" style="color:#fff;font-size:20px"></i>
      </div>
      <div>
        <h2 :style="{fontSize:'16px',fontWeight:700,color:text,margin:'0 0 4px'}">Stay safe on Instagram & TikTok</h2>
        <p :style="{fontSize:'13px',color:sub,margin:'0',lineHeight:1.6}">
          These are the 6 main reasons creators get banned or limited. Follow this guide to maximize your account lifespan.
        </p>
      </div>
    </div>

    <!-- 6 Reasons -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'16px',marginBottom:'24px',overflow:'hidden'}">
      <div :style="{padding:'20px 24px',borderBottom:`1px solid ${divider}`}">
        <h3 :style="{fontSize:'14px',fontWeight:700,color:text,margin:'0 0 2px'}">⚠️ 6 ban & limit reasons</h3>
        <p :style="{fontSize:'12px',color:sub,margin:0}">Linked to your link activity on Instagram</p>
      </div>
      <div>
        <div v-for="(reason, i) in reasons" :key="i"
          :style="{
            display:'flex',alignItems:'flex-start',gap:'14px',
            padding:'16px 24px',
            borderBottom: i < reasons.length-1 ? `1px solid ${divider}` : 'none',
          }">
          <div :style="{
            width:'28px',height:'28px',borderRadius:'8px',flexShrink:0,
            background:reason.fixed ? 'rgba(16,185,129,0.1)' : 'rgba(251,191,36,0.1)',
            border:`1px solid ${reason.fixed ? 'rgba(16,185,129,0.25)' : 'rgba(251,191,36,0.25)'}`,
            display:'flex',alignItems:'center',justifyContent:'center',
          }">
            <i :class="`bi bi-${reason.fixed ? 'check-lg' : 'exclamation-triangle'}`"
              :style="{fontSize:'12px',color:reason.fixed ? '#10b981' : '#fbbf24'}"></i>
          </div>
          <div style="flex:1">
            <div style="display:flex;align-items:center;gap:'8px';flex-wrap:wrap;gap:8px;margin-bottom:4px">
              <p :style="{fontSize:'13px',fontWeight:600,color:text,margin:0}">{{ reason.title }}</p>
              <span v-if="reason.fixed" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:#10b981;border-radius:999px;padding:1px 8px;font-size:10px;font-weight:600">
                ✓ Protected by MySocialVSL
              </span>
            </div>
            <p :style="{fontSize:'12px',color:sub,margin:0,lineHeight:1.6}">{{ reason.desc }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Best Practices -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">

      <!-- Custom Domains -->
      <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'16px',padding:'20px'}">
        <div style="display:flex;align-items:center;gap:'10px';gap:10px;margin-bottom:12px">
          <div style="width:34px;height:34px;background:rgba(109,78,232,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-globe2" style="color:#6D4EE8;font-size:16px"></i>
          </div>
          <h3 :style="{fontSize:'14px',fontWeight:700,color:text,margin:0}">Use custom domains</h3>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
          <div v-for="pt in domainPoints" :key="pt" style="display:flex;align-items:flex-start;gap:8px">
            <i class="bi bi-check-circle-fill" style="color:#6D4EE8;font-size:12px;flex-shrink:0;margin-top:2px"></i>
            <p :style="{fontSize:'12px',color:sub,margin:0,lineHeight:1.5}">{{ pt }}</p>
          </div>
        </div>
        <a href="/dashboard/domains" style="display:inline-flex;align-items:center;gap:6px;padding:'8px 16px';padding:8px 16px;background:#6D4EE8;color:#fff;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none">
          <i class="bi bi-globe2"></i> Setup custom domain →
        </a>
      </div>

      <!-- Shield Protection -->
      <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'16px',padding:'20px'}">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
          <div style="width:34px;height:34px;background:rgba(109,78,232,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-shield-fill-check" style="color:#6D4EE8;font-size:16px"></i>
          </div>
          <div>
            <h3 :style="{fontSize:'14px',fontWeight:700,color:text,margin:0}">Shield Protection™</h3>
            <span style="font-size:10px;background:linear-gradient(90deg,#5940D1,#6D4EE8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:700">Advanced security layer</span>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
          <div v-for="pt in shieldPoints" :key="pt" style="display:flex;align-items:flex-start;gap:8px">
            <i class="bi bi-check-circle-fill" style="color:#6D4EE8;font-size:12px;flex-shrink:0;margin-top:2px"></i>
            <p :style="{fontSize:'12px',color:sub,margin:0,lineHeight:1.5}">{{ pt }}</p>
          </div>
        </div>
        <p :style="{fontSize:'11px',color:sub,margin:0,padding:'10px 12px',background:theme.dark?'rgba(255,255,255,0.04)':'#F9FAFB',borderRadius:'8px',lineHeight:1.5}">
          <i class="bi bi-info-circle" style="margin-right:4px"></i>
          Activate <strong>Shield Protection</strong> in your page settings → Advanced → Features.
        </p>
      </div>
    </div>

    <!-- Deeplink guide -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'16px',padding:'20px',marginBottom:'24px'}">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
        <div style="width:34px;height:34px;background:rgba(109,78,232,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="bi bi-arrow-up-right-square" style="color:#6D4EE8;font-size:16px"></i>
        </div>
        <h3 :style="{fontSize:'14px',fontWeight:700,color:text,margin:0}">Activate Deeplink Bypass</h3>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        <div v-for="step in deeplinkSteps" :key="step.n"
          :style="{
            background:theme.dark?'rgba(255,255,255,0.03)':'#F9FAFB',
            border:`1px solid ${border}`,borderRadius:'12px',padding:'14px',
          }">
          <div :style="{
            width:'24px',height:'24px',borderRadius:'6px',background:'#6D4EE8',
            display:'flex',alignItems:'center',justifyContent:'center',marginBottom:'8px',
          }">
            <span style="color:#fff;font-size:11px;font-weight:800">{{ step.n }}</span>
          </div>
          <p :style="{fontSize:'12px',fontWeight:600,color:text,margin:'0 0 4px'}">{{ step.title }}</p>
          <p :style="{fontSize:'11px',color:sub,margin:0,lineHeight:1.5}">{{ step.desc }}</p>
        </div>
      </div>
    </div>

    <!-- Timing tip -->
    <div :style="{
      display:'flex',alignItems:'flex-start',gap:'12px',
      background:'rgba(251,191,36,0.06)',border:'1px solid rgba(251,191,36,0.2)',
      borderRadius:'12px',padding:'16px 20px',
    }">
      <i class="bi bi-clock-history" style="color:#fbbf24;font-size:18px;flex-shrink:0;margin-top:2px"></i>
      <div>
        <p style="font-size:13px;font-weight:600;color:#fbbf24;margin:0 0 3px">Wait 1 week before adding a bio link on a new account</p>
        <p :style="{fontSize:'12px',color:sub,margin:0,lineHeight:1.5}">
          Meta is suspicious of new accounts that add links too quickly. Give the algorithm time to see normal activity first. This applies to every fresh account, even if you reuse the same creator.
        </p>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import DashboardLayout from '@/components/DashboardLayout.vue'

const theme = useThemeStore()
const card    = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#fff')
const border  = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const divider = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6')
const text    = computed(() => theme.dark ? '#fff' : '#111827')
const sub     = computed(() => theme.dark ? 'rgba(255,255,255,0.5)' : '#6B7280')

const reasons = [
  {
    title: 'Opening the final destination in the in-app browser',
    desc: 'If users open OF/Fanvue/MYM inside Instagram browser, the platform detects and logs the destination URL.',
    fixed: true,
  },
  {
    title: 'Trigger words or adult emojis on your landing page',
    desc: 'Instagram reviews the landing page you link to and may flag content based on language. MySocialVSL warns you when your bio contains risky words.',
    fixed: true,
  },
  {
    title: 'Flagged domain — not changing it after a ban',
    desc: 'Once an account is flagged, the domain itself gets blacklisted. Use a fresh custom domain to start clean.',
    fixed: false,
  },
  {
    title: 'Direct link to fan platforms without protection',
    desc: 'A direct link to OF/Fanvue/MYM in bio gets scanned instantly. Shield Protection™ intercepts Meta bots before they reach your destination.',
    fixed: true,
  },
  {
    title: 'Explicit or suggestive pictures on the landing page',
    desc: 'Instagram scans images on linked pages. Keep your VSL page professional — show a face photo, not suggestive content.',
    fixed: false,
  },
  {
    title: 'Adding a bio link too fast on a new account',
    desc: 'Meta flags accounts that add links within the first few days. Wait at least 1 week on any new account.',
    fixed: false,
  },
]

const domainPoints = [
  'If flagged, switch domain in 1 click and start clean',
  'Harder for Instagram to detect — you\'re not sharing a domain with thousands of others',
  'Better conversion — looks more professional and trustworthy',
  'Custom domains available from your Domains settings',
]

const shieldPoints = [
  'Detects Meta/Instagram preview bots server-side — before any JS loads',
  'Serves a neutral decoy page to crawlers — hides your real destination URL',
  'Logs every blocked bot attempt in your analytics',
  'Works on all pages, all link types, all platforms',
]

const deeplinkSteps = [
  { n: 1, title: 'Create your VSL page', desc: 'Set up your landing page with video, bio and links in MySocialVSL.' },
  { n: 2, title: 'Enable Deeplink', desc: 'In step 1 of page creation, toggle on "Deeplink" in Advanced Settings.' },
  { n: 3, title: 'Add link to bio', desc: 'Copy your mysocialvsl.com/your-slug link and paste it in your Instagram bio.' },
  { n: 4, title: 'Test on mobile', desc: 'Tap the link from the Instagram app — it should open in Safari/Chrome, not in-app.' },
  { n: 5, title: 'Enable Shield', desc: 'Also turn on "Shield Protection" to block Instagram\'s preview bots from scanning the page.' },
  { n: 6, title: 'Use custom domain', desc: 'For agency use, set up a custom domain per creator for full isolation.' },
]
</script>
