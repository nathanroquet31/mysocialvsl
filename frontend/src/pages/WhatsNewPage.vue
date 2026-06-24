<template>
  <DashboardLayout title="What's New">

    <div style="position:relative;max-width:1060px;margin:0 auto">
      <!-- Brand glow blob -->
      <div aria-hidden="true" style="position:absolute;top:-50px;left:50%;transform:translateX(-50%);width:460px;height:300px;background:radial-gradient(ellipse,rgba(109,78,232,0.18),transparent 70%);filter:blur(60px);pointer-events:none;z-index:0"></div>

      <div style="position:relative;z-index:1">
        <!-- Hero -->
        <div style="text-align:center;margin-bottom:34px">
          <div style="display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(109,78,232,0.3);border-radius:999px;padding:6px 15px;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#A78BFA;background:rgba(109,78,232,0.08);margin-bottom:20px">
            <span style="width:6px;height:6px;border-radius:50%;background:#A78BFA;box-shadow:0 0 8px #A78BFA"></span>
            Shipping fast
          </div>
          <h1 style="font-size:34px;font-weight:800;letter-spacing:-0.03em;margin:0 0 10px;color:var(--text)">
            What's <span style="background:linear-gradient(135deg,#A78BFA,#6D4EE8,#8B6FF0);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">New</span>
          </h1>
          <p style="font-size:14px;color:var(--text-muted);margin:0">What we're building, what just shipped, what we improved and fixed.</p>
        </div>

        <!-- Board -->
        <div style="display:flex;gap:16px;overflow-x:auto;padding-bottom:10px">
          <div v-for="col in columns" :key="col.key"
            style="flex:1;min-width:234px;display:flex;flex-direction:column;gap:12px">

            <!-- Column header -->
            <div style="display:flex;align-items:center;gap:8px;padding:0 2px 4px">
              <span :style="{width:'8px',height:'8px',borderRadius:'50%',background:col.color,boxShadow:`0 0 8px ${col.color}`}"></span>
              <span style="font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:var(--text)">{{ col.label }}</span>
              <span :style="countPill(col.color)">{{ col.items.length }}</span>
            </div>

            <!-- Cards -->
            <div v-for="(it, i) in col.items" :key="it.title"
              :style="cardStyle(col, `${col.key}-${i}`)"
              @mouseenter="hover = `${col.key}-${i}`" @mouseleave="hover = null">
              <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px">
                <p style="font-size:13.5px;font-weight:700;color:var(--text);margin:0;line-height:1.35">{{ it.title }}</p>
                <span v-if="it.date" style="font-size:10px;font-weight:600;color:var(--text-muted);white-space:nowrap;flex-shrink:0">{{ it.date }}</span>
                <span v-else :style="{fontSize:'9px',fontWeight:700,letterSpacing:'0.06em',textTransform:'uppercase',color:col.color,flexShrink:0}">soon</span>
              </div>
              <p :style="descStyle">{{ it.desc }}</p>
            </div>

            <div v-if="col.items.length === 0" :style="emptyStyle">Nothing here yet.</div>
          </div>
        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import DashboardLayout from '@/components/DashboardLayout.vue'

const hover = ref(null)

// Shipped — edit to add a changelog entry. Newest first.
const entries = [
  { date: 'Jun 23', type: 'new',      title: 'Real visitors, not raw hits', desc: 'Unique visitors and unique clicks with a persistent visitor ID — real people, not repeated page loads.' },
  { date: 'Jun 23', type: 'improved', title: 'Per-second VSL retention + live viewers', desc: 'See where viewers drop off second by second, who is watching now, and your true average watch time.' },
  { date: 'Jun 23', type: 'improved', title: 'Stronger iPhone escape', desc: 'Deeplinks break out of the in-app Instagram browser more reliably on iOS 17+.' },
  { date: 'Jun 23', type: 'improved', title: 'Clean public links', desc: 'Your pages now live at a clean address — shorter, sharper, easier to share.' },
  { date: 'Jun 22', type: 'improved', title: 'Smarter CTA popup', desc: '1–45s reveal delay, settings that persist, and a close that sends straight to your link.' },
  { date: 'Jun 22', type: 'fixed',    title: 'Duplicating a page copies its links', desc: 'The copy now carries over all of its links, ready to tweak.' },
  { date: 'Jun 22', type: 'new',      title: 'How to film a converting VSL', desc: 'A short walkthrough in the builder shows you how to shoot a VSL that converts.' },
  { date: 'Jun 21', type: 'new',      title: 'Light mode', desc: 'Switch the app and landing between light and dark — your choice is remembered.' },
  { date: 'Jun 20', type: 'new',      title: 'In-app notifications', desc: 'A bell keeps you on top of billing, security and account events as they happen.' },
  { date: 'Jun 20', type: 'new',      title: 'VSL Watch Funnel', desc: 'See how far visitors get through your video — view, watch, and click in one glance.' },
  { date: 'Jun 19', type: 'improved', title: 'White-label on Agency', desc: 'Agency plans fully remove our branding from public pages.' },
  { date: 'Jun 19', type: 'fixed',    title: 'Reliable video uploads', desc: 'Web-playable video only (MP4/WebM), with the real reason surfaced when something is off.' },
]

// Coming soon — the roadmap teaser. No date; rendered as "soon".
const soon = [
  { title: 'VSL templates', desc: 'Start from a proven layout instead of a blank page.' },
  { title: 'Instagram & TikTok analytics', desc: 'Your real social stats, right next to your VSL numbers.' },
  { title: 'Team seats', desc: 'Invite your team to manage pages together.' },
  { title: 'Fresh CTA styles', desc: 'New urgency and reveal animations for your button.' },
]

const columns = computed(() => [
  { key: 'soon',     label: 'Coming soon', color: '#A78BFA', items: soon },
  { key: 'new',      label: 'New',         color: '#10B981', items: entries.filter(e => e.type === 'new') },
  { key: 'improved', label: 'Improved',    color: '#3B82F6', items: entries.filter(e => e.type === 'improved') },
  { key: 'fixed',    label: 'Fixed',       color: '#F59E0B', items: entries.filter(e => e.type === 'fixed') },
])

const descStyle = {
  fontSize: '12px', color: 'var(--text-muted)', margin: 0, lineHeight: 1.55,
  display: '-webkit-box', WebkitLineClamp: 3, WebkitBoxOrient: 'vertical', overflow: 'hidden',
}

const emptyStyle = {
  fontSize: '12px', color: 'var(--text-muted)', textAlign: 'center', padding: '18px',
  border: '1px dashed var(--border)', borderRadius: '12px',
}

function countPill(color) {
  return {
    marginLeft: 'auto', fontSize: '10px', fontWeight: 700, color,
    background: `${color}1f`, border: `1px solid ${color}3d`, borderRadius: '999px', padding: '1px 8px',
  }
}

function cardStyle(col, id) {
  const on = hover.value === id
  const isSoon = col.key === 'soon'
  return {
    background: 'var(--card-bg)',
    border: on ? `1px solid ${col.color}88` : (isSoon ? '1px dashed var(--border)' : '1px solid var(--border)'),
    borderRadius: '14px', padding: '14px',
    boxShadow: on ? `0 0 24px ${col.color}26` : 'none',
    transform: on ? 'translateY(-2px)' : 'none',
    transition: 'all 0.18s ease',
    cursor: 'default',
  }
}
</script>
