<template>
  <div ref="el" class="gv-wrap">
    <div v-if="!ready" class="gv-skeleton">
      <div class="gv-spinner"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  byCountry:  { type: Object, default: () => ({}) },
  liveEvents: { type: Array,  default: () => [] },
})

const el    = ref(null)
const ready = ref(false)
let globe   = null
let rings   = []
let seenKeys = new Set()

const CC = {
  FR:[46.2,2.2],US:[37.1,-95.7],GB:[55.4,-3.4],DE:[51.2,10.4],ES:[40.5,-3.7],
  IT:[42.8,12.8],CA:[56.1,-106.3],AU:[-25.3,133.8],BR:[-14.2,-51.9],MX:[23.6,-102.6],
  JP:[36.2,138.3],KR:[35.9,127.8],NL:[52.1,5.3],BE:[50.5,4.5],CH:[46.8,8.2],
  PT:[39.4,-8.2],PL:[51.9,19.1],RU:[61.5,105.3],UA:[48.4,31.2],NG:[9.1,8.7],
  MA:[31.8,-7.1],TN:[34.0,9.0],DZ:[28.0,2.6],SN:[14.5,-14.5],CI:[7.5,-5.5],
  CM:[3.9,11.5],GH:[7.9,-1.0],ZA:[-29.0,25.1],EG:[26.8,30.8],KE:[0.0,37.9],
  IN:[20.6,78.9],PK:[30.4,69.3],BD:[23.7,90.4],TR:[38.9,35.2],SA:[23.9,45.1],
  AE:[23.4,53.8],AR:[-38.4,-63.6],CO:[4.6,-74.3],CL:[-35.7,-71.5],PE:[-9.2,-75.0],
  VE:[6.4,-66.6],PH:[12.9,121.8],ID:[-0.8,113.9],MY:[4.2,108.0],TH:[15.9,100.9],
  VN:[14.1,108.3],CN:[35.9,104.2],TW:[23.7,121.0],SG:[1.4,103.8],
  SE:[60.1,18.6],NO:[60.5,8.5],DK:[56.3,9.5],FI:[64.0,26.0],AT:[47.5,14.6],
  RO:[45.9,24.9],HU:[47.2,19.5],CZ:[49.8,15.5],GR:[39.1,21.8],HR:[45.1,15.2],
  IL:[31.0,34.9],IR:[32.4,53.7],IQ:[33.2,43.7],QA:[25.4,51.2],KW:[29.3,47.5],
  NZ:[-40.9,174.9],LY:[26.3,17.2],SD:[12.9,30.2],ET:[9.1,40.5],AO:[-11.2,17.9],
  SK:[48.7,19.7],BG:[42.7,25.5],RS:[44.0,21.0],BY:[53.7,27.9],LT:[55.2,23.9],
  LV:[56.9,24.6],EE:[58.6,25.0],SI:[46.2,14.8],MK:[41.6,21.7],BA:[43.9,17.7],
  ME:[42.7,19.4],AL:[41.2,20.2],MD:[47.0,28.5],AM:[40.1,44.5],GE:[42.3,43.4],
  AZ:[40.1,47.6],KZ:[48.0,67.0],UZ:[41.4,64.6],TM:[40.0,59.6],KG:[41.0,74.8],
  TJ:[38.9,71.3],MN:[46.9,103.8],AF:[33.9,67.7],MM:[17.1,96.7],KH:[12.6,104.9],
  LA:[18.2,103.8],NP:[28.4,84.1],LK:[7.9,80.8],
  ZM:[-13.1,27.8],ZW:[-19.0,29.8],TZ:[-6.4,35.0],UG:[-1.3,32.3],RW:[-2.0,29.9],
  CD:[-4.0,21.8],CG:[-0.2,15.8],ML:[17.6,-2.0],BF:[12.4,-1.6],NE:[16.1,7.9],
  TD:[15.4,18.7],MR:[20.3,-10.9],GN:[9.9,-11.4],SL:[8.5,-11.8],
  OM:[21.5,55.9],YE:[15.6,48.5],JO:[30.6,36.2],LB:[33.9,35.5],SY:[34.8,38.9],
  CU:[21.5,-79.4],DO:[18.7,-70.2],HT:[19.0,-72.3],JM:[18.1,-77.3],
  GT:[15.5,-90.2],HN:[15.2,-86.2],SV:[13.8,-88.9],NI:[12.9,-85.2],
  CR:[9.7,-83.8],PA:[8.5,-80.8],EC:[-1.8,-78.2],BO:[-16.3,-63.6],
  PY:[-23.4,-58.4],UY:[-32.5,-55.8],
}

function buildPoints(bc) {
  const vals = Object.values(bc || {})
  const max  = vals.length ? Math.max(...vals, 1) : 1
  return Object.entries(bc || {})
    .filter(([c]) => CC[c])
    .map(([code, count]) => ({
      lat:    CC[code][0],
      lng:    CC[code][1],
      radius: 0.32 + Math.sqrt(count / max) * 1.85,
      color:  `rgba(167,139,250,${0.4 + (count / max) * 0.6})`,
      label:  `${code}: ${count}`,
    }))
}

onMounted(async () => {
  const { default: Globe } = await import('globe.gl')
  if (!el.value) return

  const size = el.value.offsetWidth || 380

  globe = Globe()(el.value)
    .width(size).height(size)
    .backgroundColor('rgba(0,0,0,0)')
    .globeImageUrl('//unpkg.com/three-globe/example/img/earth-dark.jpg')
    .bumpImageUrl('//unpkg.com/three-globe/example/img/earth-topology.png')
    .atmosphereColor('rgba(109,78,232,0.85)')
    .atmosphereAltitude(0.18)
    .pointsData(buildPoints(props.byCountry))
    .pointLat('lat').pointLng('lng')
    .pointRadius('radius').pointColor('color')
    .pointAltitude(0.012).pointLabel('label')
    .ringsData([])
    .ringLat('lat').ringLng('lng')
    .ringColor(() => t => `rgba(109,78,232,${Math.max(0, 1 - t * 1.5)})`)
    .ringMaxRadius(4.5).ringPropagationSpeed(2.8)
    .ringRepeatPeriod(2200)

  globe.controls().autoRotate      = true
  globe.controls().autoRotateSpeed = 0.38
  globe.controls().enableZoom      = false
  globe.controls().enablePan       = false

  el.value.addEventListener('mouseenter', () => { globe?.controls() && (globe.controls().autoRotate = false) })
  el.value.addEventListener('mouseleave', () => { globe?.controls() && (globe.controls().autoRotate = true) })

  ready.value = true
})

function flashRing(lat, lng) {
  if (!globe) return
  const r = { lat, lng, _id: Math.random() }
  rings = [...rings, r]
  globe.ringsData(rings)
  setTimeout(() => {
    rings = rings.filter(x => x._id !== r._id)
    globe?.ringsData(rings)
  }, 2600)
}

watch(() => props.byCountry, bc => {
  if (globe) globe.pointsData(buildPoints(bc || {}))
}, { deep: true })

watch(() => props.liveEvents, evts => {
  if (!globe || !evts?.length) return
  evts.forEach(ev => {
    const k = `${ev.created_at}|${ev.country}|${ev.type}`
    if (!seenKeys.has(k) && ev.country && CC[ev.country]) {
      seenKeys.add(k)
      flashRing(CC[ev.country][0], CC[ev.country][1])
    }
  })
  if (seenKeys.size > 400) seenKeys = new Set([...seenKeys].slice(-200))
}, { deep: true })

onUnmounted(() => {
  try { globe?.renderer()?.dispose() } catch {}
  globe = null
})
</script>

<style scoped>
.gv-wrap {
  width: 100%;
  aspect-ratio: 1 / 1;
  position: relative;
}
.gv-wrap :deep(canvas) { border-radius: 50%; }
.gv-skeleton {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background: radial-gradient(circle at 38% 38%, #1e174a, #0d0b1e);
  display: flex;
  align-items: center;
  justify-content: center;
}
.gv-spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(109,78,232,0.2);
  border-top-color: #6D4EE8;
  animation: gv-spin 0.8s linear infinite;
}
@keyframes gv-spin { to { transform: rotate(360deg) } }
</style>
