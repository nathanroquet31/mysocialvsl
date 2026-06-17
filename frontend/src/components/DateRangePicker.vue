<template>
  <div ref="triggerEl" style="position:relative;display:inline-block">
    <!-- Trigger button -->
    <button @click="toggle" :class="['drp-trigger', dark ? 'drp-dark' : 'drp-light']">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      <span>{{ triggerLabel }}</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
        :style="{transform: isOpen ? 'rotate(180deg)' : 'rotate(0deg)', transition:'transform 0.2s'}">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>

    <!-- Panel — rendered in fixed position via JS coords -->
    <div v-if="isOpen"
      ref="panelEl"
      :class="['drp-panel', dark ? 'drp-dark' : 'drp-light']"
      :style="panelPos"
      @click.stop>

      <div style="display:flex">
        <!-- ─ Presets column ─ -->
        <div class="drp-presets">
          <p class="drp-col-title">Preset Ranges</p>
          <button
            v-for="p in PRESETS" :key="p.value"
            class="drp-preset-row"
            :class="{active: activePreset === p.value}"
            @click="choosePreset(p)">
            {{ p.label }}
          </button>
        </div>

        <!-- ─ Divider ─ -->
        <div class="drp-vdiv"></div>

        <!-- ─ Calendar column ─ -->
        <div class="drp-cal">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <p class="drp-col-title" style="margin:0">Custom Range</p>
            <button class="drp-close-x" @click="isOpen=false">×</button>
          </div>

          <!-- Month nav -->
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <button @click="prevMonth" class="drp-nav-btn">‹</button>
            <span class="drp-month-lbl">{{ monthLabel }}</span>
            <button @click="nextMonth" class="drp-nav-btn">›</button>
          </div>

          <!-- Day grid -->
          <div class="drp-grid">
            <div v-for="d in WEEKDAYS" :key="d" class="drp-wday">{{ d }}</div>
            <div
              v-for="(cell, i) in cells" :key="i"
              class="drp-day"
              :class="{
                empty: !cell.day,
                today: cell.isToday && !cell.isStart && !cell.isEnd,
                start: cell.isStart,
                end:   cell.isEnd,
                range: cell.inRange && !cell.isStart && !cell.isEnd,
                clickable: !!cell.day,
              }"
              @click="cell.day ? clickDay(cell) : null">
              <span v-if="cell.day">{{ cell.day }}</span>
            </div>
          </div>

          <!-- Hint -->
          <p class="drp-hint">{{ rangeHint }}</p>

          <!-- Selected range display -->
          <div class="drp-selected-block">
            <p class="drp-field-lbl">SELECTED RANGE</p>
            <p class="drp-range-val">{{ rangeDisplay }}</p>
          </div>

          <!-- Timezone -->
          <div class="drp-tz-block">
            <p class="drp-field-lbl">Timezone</p>
            <select v-model="tz" class="drp-tz-sel">
              <option v-for="t in TIMEZONES" :key="t.value" :value="t.value">{{ t.label }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="drp-footer">
        <button @click="clearAll" class="drp-btn-clear">Clear</button>
        <div style="display:flex;gap:8px">
          <button @click="isOpen=false" class="drp-btn-cancel">Cancel</button>
          <button @click="applyRange" class="drp-btn-apply">Apply</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({ preset: '30d', start: null, end: null }) },
  dark:       { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue', 'change'])

// ── Constants ────────────────────────────────────────────────────────────
const PRESETS = [
  { label: 'Today',          value: 'today' },
  { label: 'Yesterday',      value: 'yesterday' },
  { label: 'Last 24 Hours',  value: '24h' },
  { label: 'Last 7 Days',    value: '7d' },
  { label: 'Last 30 Days',   value: '30d' },
  { label: 'Last 90 Days',   value: '90d' },
  { label: 'Last 6 Months',  value: '6m' },
  { label: 'Last 12 Months', value: '12m' },
  { label: 'All Time',       value: 'all' },
]
const WEEKDAYS = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']
const TIMEZONES = [
  { label: 'UTC (GMT+0)',              value: 'UTC' },
  { label: 'Paris (UTC+2) — Current', value: 'Europe/Paris' },
  { label: 'London (UTC+1)',           value: 'Europe/London' },
  { label: 'New York (UTC-4)',         value: 'America/New_York' },
  { label: 'Los Angeles (UTC-7)',      value: 'America/Los_Angeles' },
  { label: 'Tokyo (UTC+9)',            value: 'Asia/Tokyo' },
  { label: 'Dubai (UTC+4)',            value: 'Asia/Dubai' },
  { label: 'Sydney (UTC+10)',          value: 'Australia/Sydney' },
]

// ── State ─────────────────────────────────────────────────────────────────
const isOpen       = ref(false)
const triggerEl    = ref(null)
const panelEl      = ref(null)
const panelPos     = ref({ position: 'fixed', top: '0px', right: '0px', zIndex: 9999 })
const activePreset = ref(props.modelValue?.preset || '30d')
const viewYear     = ref(new Date().getFullYear())
const viewMonth    = ref(new Date().getMonth())
const startDate    = ref(null)
const endDate      = ref(null)
const picking      = ref('start') // 'start' | 'end'
const tz           = ref('Europe/Paris')

// ── Open / position ───────────────────────────────────────────────────────
function toggle() {
  isOpen.value = !isOpen.value
  if (isOpen.value) nextTick(positionPanel)
}

function positionPanel() {
  if (!triggerEl.value) return
  const r = triggerEl.value.getBoundingClientRect()
  const panelW = 600
  let right = window.innerWidth - r.right
  if (right < 8) right = 8
  // if panel would go off the left side
  if (window.innerWidth - right - panelW < 8) right = Math.max(8, window.innerWidth - panelW - 8)
  panelPos.value = {
    position: 'fixed',
    top:   (r.bottom + 8) + 'px',
    right: right + 'px',
    zIndex: 9999,
  }
}

// ── Calendar logic ─────────────────────────────────────────────────────────
const monthLabel = computed(() =>
  new Date(viewYear.value, viewMonth.value, 1)
    .toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
)

const cells = computed(() => {
  const y = viewYear.value, m = viewMonth.value
  const firstDow = new Date(y, m, 1).getDay()       // 0=Sun
  const offset   = (firstDow + 6) % 7               // shift to Mon=0
  const daysInM  = new Date(y, m + 1, 0).getDate()
  const today    = new Date()
  const result   = []

  for (let i = 0; i < offset; i++) result.push({ day: null })

  for (let d = 1; d <= daysInM; d++) {
    const date   = new Date(y, m, d)
    const isStart = startDate.value && sameDay(date, startDate.value)
    const isEnd   = endDate.value   && sameDay(date, endDate.value)
    const inRange = startDate.value && endDate.value
      && date > startDate.value && date < endDate.value
    result.push({
      day: d, date,
      isStart, isEnd, inRange,
      isToday: sameDay(date, today),
    })
  }
  return result
})

function sameDay(a, b) {
  return a.getFullYear() === b.getFullYear()
    && a.getMonth() === b.getMonth()
    && a.getDate()  === b.getDate()
}

function clickDay(cell) {
  if (picking.value === 'start' || !startDate.value) {
    startDate.value  = new Date(cell.date)
    endDate.value    = null
    picking.value    = 'end'
    activePreset.value = 'custom'
  } else {
    if (cell.date < startDate.value) {
      endDate.value   = new Date(startDate.value)
      startDate.value = new Date(cell.date)
    } else {
      endDate.value   = new Date(cell.date)
    }
    picking.value = 'start'
    activePreset.value = 'custom'
  }
}

function prevMonth() {
  if (viewMonth.value === 0) { viewMonth.value = 11; viewYear.value-- }
  else viewMonth.value--
}
function nextMonth() {
  if (viewMonth.value === 11) { viewMonth.value = 0; viewYear.value++ }
  else viewMonth.value++
}

// ── Computed labels ────────────────────────────────────────────────────────
const rangeHint = computed(() =>
  picking.value === 'end' && startDate.value ? 'Select end date' : 'Select start date'
)

const rangeDisplay = computed(() => {
  const fmt = d => d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  if (!startDate.value)              return 'Select start and end dates'
  if (!endDate.value)                return fmt(startDate.value) + ' → ...'
  return fmt(startDate.value) + ' → ' + fmt(endDate.value)
})

const triggerLabel = computed(() => {
  if (activePreset.value && activePreset.value !== 'custom')
    return PRESETS.find(p => p.value === activePreset.value)?.label || 'All Time'
  if (startDate.value && endDate.value) {
    const fmt = d => d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    return fmt(startDate.value) + ' → ' + fmt(endDate.value)
  }
  return 'All Time'
})

// ── Actions ────────────────────────────────────────────────────────────────
function choosePreset(p) {
  activePreset.value = p.value
  startDate.value    = null
  endDate.value      = null
  picking.value      = 'start'
  const val = { preset: p.value, start: null, end: null }
  emit('update:modelValue', val)
  emit('change', val)
  isOpen.value = false
}

function applyRange() {
  if (activePreset.value !== 'custom') {
    choosePreset(PRESETS.find(p => p.value === activePreset.value) || PRESETS[4])
    return
  }
  if (!startDate.value || !endDate.value) return
  const val = {
    preset: 'custom',
    start:  fmt(startDate.value),
    end:    fmt(endDate.value),
  }
  emit('update:modelValue', val)
  emit('change', val)
  isOpen.value = false
}

function fmt(d) {
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
}

function clearAll() {
  activePreset.value = 'all'
  startDate.value    = null
  endDate.value      = null
  picking.value      = 'start'
}

// ── Outside click ─────────────────────────────────────────────────────────
function onDocClick(e) {
  if (!isOpen.value) return
  const inTrigger = triggerEl.value?.contains(e.target)
  const inPanel   = panelEl.value?.contains(e.target)
  if (!inTrigger && !inPanel) isOpen.value = false
}

onMounted(() => document.addEventListener('click', onDocClick, { capture: true }))
onUnmounted(() => document.removeEventListener('click', onDocClick, { capture: true }))
</script>

<style>
/* ─ Trigger button ─────────────────────────────────────────────────────── */
.drp-trigger {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 7px 14px; border-radius: 8px; border: 1px solid;
  font-size: 13px; font-weight: 500; cursor: pointer; font-family: Inter, sans-serif;
  white-space: nowrap; transition: all 0.12s;
}
.drp-trigger.drp-light {
  background: #fff; border-color: #E5E7EB; color: #374151;
}
.drp-trigger.drp-light:hover { border-color: #6D4EE8; color: #6D4EE8; }
.drp-trigger.drp-dark {
  background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.75);
}
.drp-trigger.drp-dark:hover { border-color: #6D4EE8; color: #A78BFA; }

/* ─ Panel ──────────────────────────────────────────────────────────────── */
.drp-panel {
  width: 600px; border-radius: 14px; overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: 1px solid;
}
.drp-panel.drp-light { background: #fff; border-color: #E5E7EB; }
.drp-panel.drp-dark  { background: #1a1733; border-color: rgba(255,255,255,0.1); }

/* ─ Presets column ─────────────────────────────────────────────────────── */
.drp-presets {
  width: 180px; flex-shrink: 0; padding: 20px 0;
}
.drp-presets.drp-dark { background: rgba(255,255,255,0.02); }

.drp-col-title {
  font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;
  padding: 0 18px; margin: 0 0 10px;
}
.drp-panel.drp-light .drp-col-title { color: #9CA3AF; }
.drp-panel.drp-dark  .drp-col-title { color: rgba(255,255,255,0.3); }

.drp-preset-row {
  width: 100%; display: flex; align-items: center;
  padding: 8px 18px; background: none; border: none;
  font-size: 13px; font-weight: 500; cursor: pointer; text-align: left;
  font-family: Inter, sans-serif; transition: background 0.1s;
}
.drp-panel.drp-light .drp-preset-row { color: #4B5563; }
.drp-panel.drp-dark  .drp-preset-row { color: rgba(255,255,255,0.6); }
.drp-panel.drp-light .drp-preset-row:hover { background: #F3F4F6; }
.drp-panel.drp-dark  .drp-preset-row:hover { background: rgba(255,255,255,0.06); }
.drp-preset-row.active { color: #6D4EE8 !important; font-weight: 600; }
.drp-panel.drp-light .drp-preset-row.active { background: #EEE9FF; }
.drp-panel.drp-dark  .drp-preset-row.active { background: rgba(109,78,232,0.15); }

/* ─ Vertical divider ───────────────────────────────────────────────────── */
.drp-vdiv { width: 1px; flex-shrink: 0; }
.drp-panel.drp-light .drp-vdiv { background: #E5E7EB; }
.drp-panel.drp-dark  .drp-vdiv { background: rgba(255,255,255,0.08); }

/* ─ Calendar column ────────────────────────────────────────────────────── */
.drp-cal { flex: 1; padding: 20px; }

.drp-close-x {
  background: none; border: none; font-size: 20px; cursor: pointer;
  line-height: 1; padding: 0; opacity: 0.4; transition: opacity 0.1s;
}
.drp-panel.drp-light .drp-close-x { color: #111827; }
.drp-panel.drp-dark  .drp-close-x { color: #fff; }
.drp-close-x:hover { opacity: 0.8; }

.drp-nav-btn {
  background: none; border: none; font-size: 20px; cursor: pointer;
  padding: 2px 8px; border-radius: 6px; line-height: 1;
  transition: background 0.1s;
}
.drp-panel.drp-light .drp-nav-btn { color: #374151; }
.drp-panel.drp-dark  .drp-nav-btn { color: rgba(255,255,255,0.6); }
.drp-panel.drp-light .drp-nav-btn:hover { background: #F3F4F6; }
.drp-panel.drp-dark  .drp-nav-btn:hover { background: rgba(255,255,255,0.08); }

.drp-month-lbl { font-size: 14px; font-weight: 700; }
.drp-panel.drp-light .drp-month-lbl { color: #111827; }
.drp-panel.drp-dark  .drp-month-lbl { color: #fff; }

/* ─ Grid ───────────────────────────────────────────────────────────────── */
.drp-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; margin-bottom: 8px; }

.drp-wday {
  text-align: center; font-size: 11px; font-weight: 600; padding: 4px 0;
  text-transform: uppercase; letter-spacing: 0.05em;
}
.drp-panel.drp-light .drp-wday { color: #9CA3AF; }
.drp-panel.drp-dark  .drp-wday { color: rgba(255,255,255,0.3); }

.drp-day {
  aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
  border-radius: 6px; font-size: 12px; font-weight: 500;
  transition: all 0.1s;
}
.drp-day.empty { visibility: hidden; }
.drp-day.clickable { cursor: pointer; }

.drp-panel.drp-light .drp-day.clickable:hover { background: #F3F4F6; }
.drp-panel.drp-dark  .drp-day.clickable:hover { background: rgba(255,255,255,0.08); }

.drp-panel.drp-light .drp-day { color: #374151; }
.drp-panel.drp-dark  .drp-day { color: rgba(255,255,255,0.7); }

.drp-day.today {
  font-weight: 700;
}
.drp-panel.drp-light .drp-day.today { color: #6D4EE8; }
.drp-panel.drp-dark  .drp-day.today { color: #A78BFA; }

.drp-day.start, .drp-day.end {
  background: #6D4EE8 !important; color: #fff !important; font-weight: 700;
  border-radius: 8px;
}
.drp-day.range {
  border-radius: 0;
}
.drp-panel.drp-light .drp-day.range { background: #EEE9FF; color: #6D4EE8; }
.drp-panel.drp-dark  .drp-day.range { background: rgba(109,78,232,0.2); color: #A78BFA; }

/* ─ Info blocks ─────────────────────────────────────────────────────────── */
.drp-hint {
  font-size: 11px; margin: 6px 0 12px; opacity: 0.5;
}
.drp-panel.drp-light .drp-hint { color: #6B7280; }
.drp-panel.drp-dark  .drp-hint { color: rgba(255,255,255,0.4); }

.drp-selected-block { margin-bottom: 12px; }
.drp-tz-block { margin-bottom: 4px; }

.drp-field-lbl {
  font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;
  margin: 0 0 5px;
}
.drp-panel.drp-light .drp-field-lbl { color: #9CA3AF; }
.drp-panel.drp-dark  .drp-field-lbl { color: rgba(255,255,255,0.3); }

.drp-range-val {
  font-size: 13px; font-weight: 500; margin: 0;
  padding: 8px 12px; border-radius: 7px; border: 1px solid;
}
.drp-panel.drp-light .drp-range-val { color: #374151; background: #F9FAFB; border-color: #E5E7EB; }
.drp-panel.drp-dark  .drp-range-val { color: rgba(255,255,255,0.7); background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.08); }

.drp-tz-sel {
  width: 100%; padding: 7px 10px; border-radius: 7px; border: 1px solid;
  font-size: 12px; font-family: Inter, sans-serif; outline: none; cursor: pointer;
}
.drp-panel.drp-light .drp-tz-sel { background: #F9FAFB; border-color: #E5E7EB; color: #374151; }
.drp-panel.drp-dark  .drp-tz-sel { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); }

/* ─ Footer ──────────────────────────────────────────────────────────────── */
.drp-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 20px; border-top: 1px solid;
}
.drp-panel.drp-light .drp-footer { border-color: #E5E7EB; }
.drp-panel.drp-dark  .drp-footer { border-color: rgba(255,255,255,0.08); }

.drp-btn-clear, .drp-btn-cancel, .drp-btn-apply {
  padding: 7px 16px; border-radius: 7px; font-size: 13px; font-weight: 600;
  cursor: pointer; font-family: Inter, sans-serif; transition: all 0.12s; border: 1px solid;
}
.drp-btn-clear {
  background: transparent;
}
.drp-panel.drp-light .drp-btn-clear { color: #6B7280; border-color: #E5E7EB; }
.drp-panel.drp-dark  .drp-btn-clear { color: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.1); }
.drp-btn-clear:hover { border-color: #EF4444 !important; color: #EF4444 !important; }

.drp-btn-cancel {
  background: transparent;
}
.drp-panel.drp-light .drp-btn-cancel { color: #6B7280; border-color: #E5E7EB; }
.drp-panel.drp-dark  .drp-btn-cancel { color: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.1); }

.drp-btn-apply {
  background: #6D4EE8; border-color: #6D4EE8; color: #fff;
}
.drp-btn-apply:hover { background: #5b3fd6; border-color: #5b3fd6; box-shadow: 0 4px 14px rgba(109,78,232,0.4); }
</style>
