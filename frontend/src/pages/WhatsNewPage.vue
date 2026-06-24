<template>
  <DashboardLayout title="What's New">

    <div style="position:relative;max-width:740px;margin:0 auto">
      <!-- Brand glow blob -->
      <div aria-hidden="true" style="position:absolute;top:-50px;left:50%;transform:translateX(-50%);width:420px;height:300px;background:radial-gradient(ellipse,rgba(109,78,232,0.20),transparent 70%);filter:blur(55px);pointer-events:none;z-index:0"></div>

      <div style="position:relative;z-index:1">
        <!-- Hero -->
        <div style="text-align:center;margin-bottom:30px">
          <div style="display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(109,78,232,0.3);border-radius:999px;padding:6px 15px;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#A78BFA;background:rgba(109,78,232,0.08);margin-bottom:22px">
            <span style="width:6px;height:6px;border-radius:50%;background:#A78BFA;box-shadow:0 0 8px #A78BFA"></span>
            Shipping fast
          </div>
          <h1 style="font-size:34px;font-weight:800;letter-spacing:-0.03em;margin:0 0 10px;color:var(--text)">
            What's <span style="background:linear-gradient(135deg,#A78BFA,#6D4EE8,#8B6FF0);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">New</span>
          </h1>
          <p style="font-size:14px;color:var(--text-muted);margin:0">Every feature, improvement and fix we ship — newest first.</p>
        </div>

        <!-- Filters -->
        <div style="display:flex;justify-content:center;margin-bottom:36px">
          <div style="display:inline-flex;gap:4px;padding:5px;border-radius:999px;background:var(--pill-bg);border:1px solid var(--border)">
            <button v-for="f in filters" :key="f.key" @click="active = f.key" :style="tab(f.key)">
              {{ f.label }} <span style="opacity:0.55;margin-left:1px">{{ counts[f.key] }}</span>
            </button>
          </div>
        </div>

        <!-- Timeline -->
        <div v-for="(e, i) in visible" :key="e.title"
          style="display:grid;grid-template-columns:60px 1fr;gap:14px;align-items:start">
          <div style="text-align:right;padding-top:20px">
            <p style="font-size:12px;font-weight:700;color:var(--text);margin:0;white-space:nowrap">{{ e.date }}</p>
          </div>
          <div style="position:relative;padding-left:24px;padding-bottom:14px;border-left:1px solid var(--border)">
            <span :style="dot(e.type)"></span>
            <div :style="card(i)" @mouseenter="hover = i" @mouseleave="hover = null">
              <span :style="badge(e.type)">{{ e.type }}</span>
              <p style="font-size:15px;font-weight:700;color:var(--text);margin:11px 0 6px">{{ e.title }}</p>
              <p style="font-size:13px;color:var(--text-muted);margin:0;line-height:1.6">{{ e.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import DashboardLayout from '@/components/DashboardLayout.vue'

const active = ref('all')
const hover = ref(null)

const filters = [
  { key: 'all',      label: 'All' },
  { key: 'new',      label: 'New' },
  { key: 'improved', label: 'Improved' },
  { key: 'fixed',    label: 'Fixed' },
]

// Edit this list to ship a changelog entry. Newest first.
const entries = [
  { date: 'Jun 23', type: 'new',      title: 'Real visitors, not raw hits', desc: 'Analytics now count unique visitors and unique clicks with a persistent visitor ID — so your numbers reflect real people, not repeated page loads.' },
  { date: 'Jun 23', type: 'improved', title: 'Per-second VSL retention + live viewers', desc: 'See exactly where viewers drop off second by second, who is watching right now, and the true average watch time of your VSL.' },
  { date: 'Jun 23', type: 'improved', title: 'Stronger iPhone escape from the Instagram browser', desc: 'Deeplinks now break out of the in-app Instagram webview more reliably on iOS 17+, so more of your traffic lands in a real browser.' },
  { date: 'Jun 23', type: 'improved', title: 'Clean public links', desc: 'Your pages now live at a clean address (yourname) — shorter, sharper, and easier to share.' },
  { date: 'Jun 22', type: 'improved', title: 'Smarter CTA popup', desc: 'Set a 1–45s reveal delay, your popup settings now persist, and after closing it the bottom button goes straight to your link.' },
  { date: 'Jun 22', type: 'fixed',    title: 'Duplicating a page now copies its links', desc: 'Duplicate a page and the copy actually carries over all of its links, ready to tweak.' },
  { date: 'Jun 22', type: 'new',      title: 'How to film a converting VSL', desc: 'A short walkthrough built into the page builder shows you how to shoot a VSL that actually converts.' },
  { date: 'Jun 21', type: 'new',      title: 'Light mode', desc: 'Switch the whole app and landing between light and dark — your choice is remembered.' },
  { date: 'Jun 20', type: 'new',      title: 'In-app notifications', desc: 'A notification bell keeps you on top of billing, security and account events as they happen.' },
  { date: 'Jun 20', type: 'new',      title: 'VSL Watch Funnel', desc: 'A new dashboard funnel shows how far visitors get through your video — view, watch, and click in one glance.' },
  { date: 'Jun 19', type: 'improved', title: 'White-label on Agency', desc: 'Agency plans now fully remove our branding from public pages.' },
  { date: 'Jun 19', type: 'improved', title: 'Reliable video uploads', desc: 'Uploads now accept web-playable video (MP4/WebM) and tell you the real reason when something is off — no more silent failures.' },
]

const counts = computed(() => ({
  all:      entries.length,
  new:      entries.filter(e => e.type === 'new').length,
  improved: entries.filter(e => e.type === 'improved').length,
  fixed:    entries.filter(e => e.type === 'fixed').length,
}))

const visible = computed(() => active.value === 'all' ? entries : entries.filter(e => e.type === active.value))

function typeColor(type) {
  return type === 'new' ? '#10B981' : type === 'fixed' ? '#F59E0B' : '#A78BFA'
}

function dot(type) {
  const c = typeColor(type)
  return {
    position: 'absolute', left: '-5px', top: '21px', width: '9px', height: '9px',
    borderRadius: '50%', background: c,
    boxShadow: `0 0 0 4px var(--bg), 0 0 10px ${c}`,
  }
}

function badge(type) {
  const c = typeColor(type)
  return {
    display: 'inline-block', fontSize: '10px', fontWeight: 700, letterSpacing: '0.08em',
    textTransform: 'uppercase', color: c, background: `${c}1f`, border: `1px solid ${c}3d`,
    borderRadius: '999px', padding: '3px 10px',
  }
}

function card(i) {
  const on = hover.value === i
  return {
    background: 'var(--card-bg)',
    border: `1px solid ${on ? 'rgba(167,139,250,0.45)' : 'var(--border)'}`,
    borderRadius: '16px', padding: '18px',
    boxShadow: on ? '0 0 28px rgba(109,78,232,0.15)' : 'none',
    transform: on ? 'translateX(2px)' : 'none',
    transition: 'all 0.18s ease',
  }
}

function tab(key) {
  const on = active.value === key
  return {
    border: 'none', cursor: 'pointer', fontFamily: 'Inter,sans-serif',
    fontSize: '12px', fontWeight: 600, padding: '7px 15px', borderRadius: '999px',
    color: on ? '#fff' : 'var(--text-muted)',
    background: on ? 'linear-gradient(135deg,#6D4EE8,#8B6FF0)' : 'transparent',
    boxShadow: on ? '0 0 18px rgba(109,78,232,0.45)' : 'none',
    transition: 'all 0.15s',
  }
}
</script>
