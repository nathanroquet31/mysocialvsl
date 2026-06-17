<template>
  <section id="pricing" :style="{position:'relative',overflow:'hidden',padding:'96px 24px 80px',background:'linear-gradient(to bottom, #080810 0%, #0d0b1e 25%, #0f0c22 100%)'}">

    <!-- Top fade — raccord avec la section du dessus -->
    <div style="position:absolute;top:0;left:0;right:0;height:180px;background:linear-gradient(to bottom,#080810 0%,transparent 100%);z-index:2;pointer-events:none"></div>

    <!-- Background grid + glow orbs -->
    <div style="position:absolute;inset:0;pointer-events:none;z-index:0">
      <!-- Grid -->
      <div style="position:absolute;inset:0;background-image:linear-gradient(to right,rgba(109,78,232,0.06) 1px,transparent 1px),linear-gradient(to bottom,rgba(109,78,232,0.04) 1px,transparent 1px);background-size:70px 80px;mask-image:radial-gradient(60% 60% at 50% 0%,white,transparent)"></div>
      <!-- Purple orb top-center -->
      <div style="position:absolute;left:-20%;right:-20%;top:-100px;height:520px;border-radius:50%;border:160px solid #6D4EE8;filter:blur(100px);opacity:0.25"></div>
      <!-- Violet radial glow center -->
      <div style="position:absolute;left:10%;right:10%;top:0;bottom:0;background:radial-gradient(circle at 50% 25%, #5940D1 0%, transparent 60%);opacity:0.18;mix-blend-mode:screen"></div>
      <!-- Soft ambient bottom -->
      <div style="position:absolute;left:20%;right:20%;bottom:0;height:300px;background:radial-gradient(circle at 50% 100%, rgba(109,78,232,0.12) 0%, transparent 70%)"></div>
      <!-- Bottom fade — bridge to FAQ -->
      <div style="position:absolute;bottom:0;left:0;right:0;height:220px;background:linear-gradient(to bottom,transparent,#0a0814);pointer-events:none;z-index:3"></div>
      <!-- Sparkles canvas -->
      <canvas ref="sparklesCanvas" style="position:absolute;inset:0;width:100%;height:100%;mask-image:radial-gradient(50% 50% at 50% 0%, white, transparent 85%);opacity:0.5"></canvas>
    </div>

    <!-- Header -->
    <div :class="['pricing-header', {visible: sectionVisible}]" style="position:relative;z-index:10;text-align:center;margin-bottom:48px;max-width:760px;margin-left:auto;margin-right:auto">
      <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(109,78,232,0.12);border:1px solid rgba(109,78,232,0.25);border-radius:999px;padding:4px 14px;margin-bottom:20px">
        <span style="width:6px;height:6px;border-radius:50%;background:#6D4EE8;display:inline-block"></span>
        <span style="font-size:11px;font-weight:600;color:rgba(109,78,232,0.9);letter-spacing:0.1em;text-transform:uppercase">Pricing</span>
      </div>
      <h2 style="font-size:clamp(2rem,4vw,3rem);font-weight:700;color:#fff;letter-spacing:-0.04em;margin:0 0 12px">
        Plans that work best for you
      </h2>
      <p style="color:rgba(255,255,255,0.4);font-size:1.05rem;margin:0 0 28px">
        Start free. Scale when you're ready. Cancel anytime.
      </p>

      <!-- Toggle -->
      <div style="display:flex;justify-content:center">
        <div style="position:relative;display:flex;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:999px;padding:4px">
          <button @click="isYearly=false"
            :style="{position:'relative',zIndex:2,padding:'8px 24px',borderRadius:'999px',border:'none',fontFamily:'Inter,sans-serif',fontSize:'13px',fontWeight:600,cursor:'pointer',transition:'color 0.2s',color:!isYearly?'#fff':'rgba(255,255,255,0.45)',background:'none'}">
            <span v-if="!isYearly" style="position:absolute;inset:0;border-radius:999px;background:linear-gradient(135deg,#5940D1,#6D4EE8);box-shadow:0 0 18px rgba(109,78,232,0.5);z-index:-1"></span>
            Monthly
          </button>
          <button @click="isYearly=true"
            :style="{position:'relative',zIndex:2,padding:'8px 24px',borderRadius:'999px',border:'none',fontFamily:'Inter,sans-serif',fontSize:'13px',fontWeight:600,cursor:'pointer',transition:'color 0.2s',color:isYearly?'#fff':'rgba(255,255,255,0.45)',background:'none'}">
            <span v-if="isYearly" style="position:absolute;inset:0;border-radius:999px;background:linear-gradient(135deg,#5940D1,#6D4EE8);box-shadow:0 0 18px rgba(109,78,232,0.5);z-index:-1"></span>
            <span style="display:flex;align-items:center;gap:6px">
              Yearly
              <span style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);border-radius:999px;padding:1px 7px;font-size:10px;font-weight:700">-20%</span>
            </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Cards grid -->
    <div style="position:relative;z-index:10;display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:980px;margin:0 auto">
      <div v-for="(plan, index) in plans" :key="plan.name"
        :class="['pricing-card', {visible: sectionVisible}]"
        :style="{
          borderRadius:'20px',
          background: plan.popular
            ? 'linear-gradient(135deg, rgba(109,78,232,0.18) 0%, rgba(89,64,209,0.1) 50%, rgba(109,78,232,0.08) 100%)'
            : 'rgba(255,255,255,0.025)',
          border: plan.popular ? '1.5px solid rgba(109,78,232,0.5)' : '1px solid rgba(255,255,255,0.07)',
          padding:'32px',
          position:'relative',
          transform: plan.popular ? 'translateY(-10px)' : 'none',
          boxShadow: plan.popular ? '0 0 80px rgba(109,78,232,0.2), 0 20px 60px rgba(0,0,0,0.4)' : 'none',
          transition:'transform 0.25s, box-shadow 0.25s',
          animationDelay: (index * 0.12) + 's'
        }"
        @mouseenter="e => {
          if(!plan.popular) e.currentTarget.style.transform='translateY(-4px)';
          e.currentTarget.style.boxShadow = plan.popular
            ? '0 0 120px rgba(109,78,232,0.35), 0 30px 80px rgba(0,0,0,0.5)'
            : '0 8px 32px rgba(109,78,232,0.12)'
        }"
        @mouseleave="e => {
          e.currentTarget.style.transform = plan.popular ? 'translateY(-10px)' : 'none';
          e.currentTarget.style.boxShadow = plan.popular ? '0 0 80px rgba(109,78,232,0.2), 0 20px 60px rgba(0,0,0,0.4)' : 'none'
        }">

        <!-- Popular badge -->
        <div v-if="plan.popular" style="position:absolute;top:-13px;left:50%;transform:translateX(-50%);background:linear-gradient(90deg,#5940D1,#8B5CF6);color:#fff;font-size:10px;font-weight:700;padding:3px 14px;border-radius:999px;letter-spacing:0.08em;white-space:nowrap;box-shadow:0 2px 12px rgba(109,78,232,0.4)">
          MOST POPULAR
        </div>

        <!-- Plan name -->
        <h3 :style="{fontSize:'26px',fontWeight:700,color:'#fff',margin:'0 0 8px',letterSpacing:'-0.02em'}">{{ plan.name }}</h3>
        <p style="font-size:13px;color:rgba(255,255,255,0.4);margin:0 0 20px;line-height:1.5">{{ plan.description }}</p>

        <!-- Price -->
        <div style="display:flex;align-items:baseline;gap:2px;margin-bottom:4px">
          <span style="font-size:36px;font-weight:800;color:#fff;letter-spacing:-0.04em">
            $<NumberFlow :value="isYearly ? plan.yearlyPrice : plan.price" :format="{style:'decimal'}" style="display:inline" />
          </span>
          <span style="font-size:13px;color:rgba(255,255,255,0.3);margin-left:4px">/{{ isYearly ? 'an' : 'mois' }}</span>
        </div>
        <p v-if="isYearly && plan.price > 0" style="font-size:11px;color:rgba(167,139,250,0.8);margin:0 0 24px;font-weight:600">
          Économisez ${{ plan.price * 12 - plan.yearlyPrice }}/an
        </p>
        <div v-else style="margin-bottom:24px"></div>

        <!-- CTA button -->
        <RouterLink :to="'/register'"
          :style="{
            display:'block', textAlign:'center', padding:'13px', borderRadius:'12px',
            fontWeight:700, fontSize:'14px', textDecoration:'none',
            marginBottom:'24px', transition:'all 0.15s',
            background: plan.popular
              ? 'linear-gradient(135deg, #5940D1, #6D4EE8)'
              : 'rgba(255,255,255,0.06)',
            color:'#fff',
            border: plan.popular ? '1px solid rgba(109,78,232,0.6)' : '1px solid rgba(255,255,255,0.1)',
            boxShadow: plan.popular ? '0 4px 20px rgba(109,78,232,0.4)' : 'none'
          }">
          {{ plan.buttonText }}
        </RouterLink>

        <!-- Features -->
        <div :style="{borderTop:'1px solid rgba(255,255,255,0.07)',paddingTop:'20px'}">
          <p style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 12px">{{ plan.includes[0] }}</p>
          <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:9px">
            <li v-for="(f, fi) in plan.includes.slice(1)" :key="fi" style="display:flex;align-items:center;gap:10px">
              <span style="width:6px;height:6px;border-radius:50%;background:linear-gradient(135deg,#6D4EE8,#A78BFA);flex-shrink:0;box-shadow:0 0 6px rgba(109,78,232,0.4)"></span>
              <span style="font-size:13px;color:rgba(255,255,255,0.5)">{{ f }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import NumberFlow from '@number-flow/vue'

const isYearly = ref(false)
const sparklesCanvas = ref(null)
const sectionVisible = ref(false)
let animFrame = null
let observer = null

const plans = [
  {
    name: 'Free',
    description: 'Pour démarrer et tester la plateforme sans engagement.',
    price: 0,
    yearlyPrice: 0,
    buttonText: 'Démarrer gratuitement',
    includes: [
      'Inclus :',
      '1 VSL Page',
      '1 Direct Link',
      'Analytics basiques',
      'Bot Protection',
      'Mobile optimisé',
    ],
  },
  {
    name: 'Pro',
    description: 'La meilleure valeur pour les créatrices qui veulent scaler.',
    price: 19,
    yearlyPrice: 182,
    buttonText: 'Commencer',
    popular: true,
    includes: [
      'Tout ce qui est en Free, +',
      '5 VSL Pages',
      '2 Direct Links',
      'Analytics temps réel',
      'Thèmes premium',
      'Domaine personnalisé',
      'Geo Targeting',
      'Deep Linking',
    ],
  },
  {
    name: 'Agency',
    description: 'Pour les agences et équipes avec des besoins avancés.',
    price: 49,
    yearlyPrice: 470,
    buttonText: 'Commencer',
    includes: [
      'Tout ce qui est en Pro, +',
      '25 VSL Pages + 25 liens (extensible)',
      'Analytics avancées',
      'Paywall intégré',
      'Accès API',
      'Support prioritaire',
      'White Label',
    ],
  },
]

function initSparkles() {
  const canvas = sparklesCanvas.value
  if (!canvas) return
  const ctx = canvas.getContext('2d')
  const parent = canvas.parentElement
  const resize = () => {
    canvas.width = parent.offsetWidth
    canvas.height = parent.offsetHeight
  }
  resize()
  window.addEventListener('resize', resize)

  const particles = Array.from({ length: 120 }, () => ({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height * 0.6,
    r: Math.random() * 1.2 + 0.3,
    alpha: Math.random(),
    speed: Math.random() * 0.3 + 0.1,
    drift: (Math.random() - 0.5) * 0.3,
    // slight purple tint on some
    hue: Math.random() > 0.7 ? '220,180,255' : '255,255,255',
  }))

  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    for (const p of particles) {
      p.y += p.speed
      p.x += p.drift
      p.alpha += (Math.random() - 0.5) * 0.04
      p.alpha = Math.max(0.05, Math.min(0.9, p.alpha))
      if (p.y > canvas.height * 0.6) { p.y = 0; p.x = Math.random() * canvas.width }
      ctx.beginPath()
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2)
      ctx.fillStyle = `rgba(${p.hue},${p.alpha})`
      ctx.fill()
    }
    animFrame = requestAnimationFrame(draw)
  }
  draw()
}

onMounted(() => {
  initSparkles()

  // IntersectionObserver pour déclencher l'animation au scroll
  const section = document.getElementById('pricing')
  if (section) {
    observer = new IntersectionObserver(
      ([entry]) => { if (entry.isIntersecting) { sectionVisible.value = true; observer.disconnect() } },
      { threshold: 0.1 }
    )
    observer.observe(section)
  }
})

onUnmounted(() => {
  if (animFrame) cancelAnimationFrame(animFrame)
  if (observer) observer.disconnect()
})
</script>

<style scoped>
.pricing-header {
  opacity: 0;
  transform: translateY(24px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}
.pricing-header.visible {
  opacity: 1;
  transform: translateY(0);
}

.pricing-card {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity 0.6s ease, transform 0.6s ease;
}
.pricing-card.visible {
  opacity: 1;
  transform: translateY(0);
}
.pricing-card.visible[style*="translateY(-10px)"] {
  transform: translateY(-10px) !important;
}

.pricing-card:nth-child(1) { transition-delay: 0.05s; }
.pricing-card:nth-child(2) { transition-delay: 0.17s; }
.pricing-card:nth-child(3) { transition-delay: 0.29s; }
</style>
