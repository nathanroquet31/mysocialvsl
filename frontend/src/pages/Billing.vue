<template>
  <DashboardLayout title="Billing">

    <!-- Current Plan card -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px',marginBottom:'24px'}">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px">
        <div>
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 10px'}">Current Plan</p>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <span :style="{fontSize:'18px',fontWeight:700,color:text,textTransform:'capitalize'}">{{ currentPlan }}</span>
            <span style="background:#F0FDF4;border:1px solid #BBF7D0;color:#16A34A;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">Active</span>
          </div>
          <p :style="{fontSize:'12px',color:sub,margin:'0 0 14px'}">Subscription Renews On: <strong :style="{color:label}">{{ renewalDate }}</strong></p>
          <div style="display:flex;gap:32px">
            <div>
              <p :style="{fontSize:'11px',color:muted,margin:'0 0 4px'}">Direct links</p>
              <p :style="{fontSize:'13px',fontWeight:600,color:text,margin:0}">{{ auth.user?.direct_count ?? 0 }} / {{ directLimitLabel }}</p>
            </div>
            <div>
              <p :style="{fontSize:'11px',color:muted,margin:'0 0 4px'}">VSL Pages</p>
              <p :style="{fontSize:'13px',fontWeight:600,color:text,margin:0}">{{ auth.user?.pages_count ?? 0 }} / {{ pageLimitLabel }}</p>
            </div>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end">
          <button v-if="currentPlan !== 'free'" @click="openPortal" :disabled="portalLoading"
            style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif"
            :style="{opacity: portalLoading ? 0.7 : 1}">
            {{ portalLoading ? 'Loading…' : 'Manage Subscription' }}
          </button>
          <button :style="{background:'none',border:'none',fontSize:'12px',color:muted,cursor:'pointer',fontFamily:'Inter,sans-serif'}">Cancel Plan</button>
        </div>
      </div>
    </div>

    <!-- Available Plans -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px',marginBottom:'24px'}">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:0}">Available Plans</p>
        <!-- Monthly/Annual toggle -->
        <div style="display:flex;align-items:center;gap:10px">
          <span :style="{fontSize:'13px',fontWeight:billing==='monthly'?'600':'400',color:billing==='monthly'?text:muted}">Monthly</span>
          <div @click="billing = billing==='monthly' ? 'annually' : 'monthly'"
            :style="{width:'40px',height:'22px',borderRadius:'999px',background:billing==='annually'?'#6D4EE8':(theme.dark?'rgba(255,255,255,0.15)':'#D1D5DB'),cursor:'pointer',position:'relative',transition:'background 0.2s',flexShrink:0}">
            <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',left:billing==='annually'?'21px':'3px',transition:'left 0.2s',boxShadow:'0 1px 3px rgba(0,0,0,0.2)'}"></div>
          </div>
          <span :style="{fontSize:'13px',fontWeight:billing==='annually'?'600':'400',color:billing==='annually'?text:muted}">
            Annually <span style="font-size:11px;background:#F0FDF4;color:#16A34A;border-radius:999px;padding:1px 6px;font-weight:600;border:1px solid #BBF7D0">-20%</span>
          </span>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px">

        <!-- Free -->
        <div :style="{border:currentPlan==='free'?'2px solid #6D4EE8':`1px solid ${border}`,borderRadius:'12px',padding:'20px',position:'relative',background:card}">
          <p :style="{fontSize:'13px',fontWeight:700,color:label,margin:'0 0 4px',textTransform:'uppercase',letterSpacing:'0.06em'}">Free</p>
          <div style="display:flex;align-items:baseline;gap:3px;margin-bottom:4px">
            <span :style="{fontSize:'28px',fontWeight:800,color:text}">$0</span>
            <span :style="{fontSize:'13px',color:muted}">/mo</span>
          </div>
          <p :style="{fontSize:'12px',color:muted,margin:'0 0 16px'}">To get started</p>
          <ul style="list-style:none;padding:0;margin:0 0 20px;display:flex;flex-direction:column;gap:8px">
            <li v-for="f in freePlan.features" :key="f" :style="{display:'flex',alignItems:'center',gap:'8px',fontSize:'12px',color:label}">
              <i class="bi bi-check-lg" style="font-size:13px;color:#16A34A"></i>
              {{ f }}
            </li>
          </ul>
          <button disabled :style="{width:'100%',padding:'9px',borderRadius:'8px',border:`1px solid ${border}`,background:subtleBg,color:muted,fontSize:'13px',fontWeight:600,cursor:'default',fontFamily:'Inter,sans-serif'}">
            {{ currentPlan==='free' ? 'Current Plan' : 'Downgrade' }}
          </button>
        </div>

        <!-- Pro -->
        <div :style="{border:currentPlan==='pro'?'2px solid #6D4EE8':'1.5px solid #C7BBFF',borderRadius:'12px',padding:'20px',background:theme.dark?'rgba(109,78,232,0.08)':'#FDFCFF',position:'relative'}">
          <div style="position:absolute;top:-11px;left:50%;transform:translateX(-50%);background:#6D4EE8;color:#fff;font-size:10px;font-weight:700;padding:3px 12px;border-radius:999px;letter-spacing:0.06em;white-space:nowrap">
            MOST POPULAR
          </div>
          <p style="font-size:13px;font-weight:700;color:#6D4EE8;margin:0 0 4px;text-transform:uppercase;letter-spacing:0.06em">Pro</p>
          <div style="display:flex;align-items:baseline;gap:3px;margin-bottom:4px">
            <span :style="{fontSize:'28px',fontWeight:800,color:text}">${{ billing==='annually' ? '15' : '19' }}</span>
            <span :style="{fontSize:'13px',color:muted}">/mo</span>
          </div>
          <p :style="{fontSize:'12px',color:muted,margin:'0 0 16px'}">{{ billing==='annually' ? 'Billed $180/yr' : 'Billed monthly' }}</p>
          <ul style="list-style:none;padding:0;margin:0 0 20px;display:flex;flex-direction:column;gap:8px">
            <li v-for="f in proPlan.features" :key="f" :style="{display:'flex',alignItems:'center',gap:'8px',fontSize:'12px',color:label}">
              <i class="bi bi-check-lg" style="font-size:13px;color:#6D4EE8"></i>
              {{ f }}
            </li>
          </ul>
          <button v-if="currentPlan!=='pro'" @click="checkout('pro')" :disabled="checkoutLoading==='pro'"
            style="width:100%;padding:9px;border-radius:8px;border:none;background:#6D4EE8;color:#fff;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif"
            :style="{opacity: checkoutLoading==='pro' ? 0.7 : 1}">
            {{ checkoutLoading==='pro' ? 'Redirecting…' : currentPlan==='agency' ? 'Downgrade' : 'Upgrade to Pro' }}
          </button>
          <div v-else style="width:100%;padding:9px;border-radius:8px;background:#EEE9FF;border:1px solid #C7BBFF;color:#6D4EE8;font-size:13px;font-weight:600;text-align:center">
            Current Plan ✓
          </div>
        </div>

        <!-- Agency -->
        <div :style="{border:currentPlan==='agency'?'2px solid #6D4EE8':`1px solid ${border}`,borderRadius:'12px',padding:'20px',position:'relative',background:card}">
          <p :style="{fontSize:'13px',fontWeight:700,color:label,margin:'0 0 4px',textTransform:'uppercase',letterSpacing:'0.06em'}">Agency</p>
          <div style="display:flex;align-items:baseline;gap:3px;margin-bottom:4px">
            <span :style="{fontSize:'28px',fontWeight:800,color:text}">${{ billing==='annually' ? '39' : '49' }}</span>
            <span :style="{fontSize:'13px',color:muted}">/mo</span>
          </div>
          <p :style="{fontSize:'12px',color:muted,margin:'0 0 16px'}">{{ billing==='annually' ? 'Billed $468/yr' : 'Billed monthly' }}</p>
          <ul style="list-style:none;padding:0;margin:0 0 20px;display:flex;flex-direction:column;gap:8px">
            <li v-for="f in agencyPlan.features" :key="f" :style="{display:'flex',alignItems:'center',gap:'8px',fontSize:'12px',color:label}">
              <i class="bi bi-check-lg" style="font-size:13px;color:#16A34A"></i>
              {{ f }}
            </li>
          </ul>
          <button v-if="currentPlan!=='agency'" @click="checkout('agency')" :disabled="checkoutLoading==='agency'"
            :style="{width:'100%',padding:'9px',borderRadius:'8px',border:'none',background:theme.dark?'rgba(255,255,255,0.12)':'#111827',color:theme.dark?'#fff':'#fff',fontSize:'13px',fontWeight:600,cursor:'pointer',fontFamily:'Inter,sans-serif',opacity:checkoutLoading==='agency'?0.7:1}">
            {{ checkoutLoading==='agency' ? 'Redirecting…' : 'Upgrade to Agency' }}
          </button>
          <div v-else style="width:100%;padding:9px;border-radius:8px;background:#F0FDF4;border:1px solid #BBF7D0;color:#16A34A;font-size:13px;font-weight:600;text-align:center">
            Current Plan ✓
          </div>
        </div>
      </div>

      <!-- Agency configurator (shown when agency is selected or current plan) -->
      <div v-if="showAgencyConfig" :style="{marginBottom:'20px'}">
        <div :style="{display:'flex',alignItems:'center',gap:'8px',marginBottom:'14px'}">
          <div style="height:1px;flex:1" :style="{background:divider}"></div>
          <span :style="{fontSize:'11px',fontWeight:600,color:muted,letterSpacing:'0.1em'}">AGENCY PLAN CONFIGURATOR</span>
          <div style="height:1px;flex:1" :style="{background:divider}"></div>
        </div>
        <AgencyConfigurator :dark="theme.dark" :billing="billing" @checkout="checkoutAgency" />
      </div>
      <button v-if="!showAgencyConfig" @click="showAgencyConfig=true"
        :style="{width:'100%',padding:'10px',borderRadius:'8px',border:`1px dashed ${border}`,background:'transparent',color:sub,fontSize:'13px',fontWeight:500,cursor:'pointer',fontFamily:'Inter,sans-serif',marginBottom:'20px',display:'flex',alignItems:'center',justifyContent:'center',gap:'6px'}">
        <i class="bi bi-sliders"></i> Configure Agency Plan
      </button>

      <!-- Promo code -->
      <div :style="{display:'flex',gap:'10px',alignItems:'center',paddingTop:'16px',borderTop:`1px solid ${divider}`}">
        <input v-model="promoCode" type="text" placeholder="Enter promo code"
          :style="{border:`1px solid ${inputBorder}`,borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:text,width:'200px',outline:'none',fontFamily:'Inter,sans-serif',background:inputBg}"
          @focus="e => { e.target.style.borderColor='#6D4EE8'; e.target.style.boxShadow='0 0 0 3px rgba(109,78,232,0.12)' }"
          @blur="e => { e.target.style.borderColor=theme.dark?'rgba(255,255,255,0.12)':'#E5E7EB'; e.target.style.boxShadow='none' }" />
        <button :style="{background:'transparent',color:sub,border:`1px solid ${border}`,borderRadius:'8px',padding:'9px 16px',fontSize:'13px',fontWeight:500,cursor:'pointer',fontFamily:'Inter,sans-serif'}">Apply Code</button>
      </div>
    </div>

    <!-- Manage Subscription card -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px',marginBottom:'24px'}">
      <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:'0 0 8px'}">Manage Subscription</p>
      <p :style="{fontSize:'13px',color:sub,margin:'0 0 16px'}">Access your billing portal to update payment method, download invoices, and manage your subscription.</p>
      <button @click="openPortal" :disabled="portalLoading"
        style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif"
        :style="{opacity: portalLoading ? 0.7 : 1}">
        {{ portalLoading ? 'Loading…' : 'Go to Billing Portal →' }}
      </button>
    </div>

    <!-- Billing History -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',overflow:'hidden',marginBottom:'24px'}">
      <div :style="{padding:'20px 24px',borderBottom:`1px solid ${divider}`}">
        <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:0}">Billing History</p>
      </div>
      <div v-if="invoices.length === 0" style="padding:48px 24px;text-align:center">
        <p :style="{fontSize:'13px',color:muted,margin:0}">No billing history yet.</p>
      </div>
      <table v-else style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr :style="{background:subtleBg,borderBottom:`1px solid ${border}`}">
            <th :style="{padding:'10px 20px',textAlign:'left',fontWeight:600,color:label}">Date</th>
            <th :style="{padding:'10px 20px',textAlign:'left',fontWeight:600,color:label}">Description</th>
            <th :style="{padding:'10px 20px',textAlign:'left',fontWeight:600,color:label}">Amount</th>
            <th :style="{padding:'10px 20px',textAlign:'left',fontWeight:600,color:label}">Status</th>
            <th :style="{padding:'10px 20px',textAlign:'left',fontWeight:600,color:label}">Invoice</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="inv in invoices" :key="inv.id" :style="{borderBottom:`1px solid ${divider}`}">
            <td :style="{padding:'12px 20px',color:label}">{{ inv.date }}</td>
            <td :style="{padding:'12px 20px',color:label}">{{ inv.description }}</td>
            <td :style="{padding:'12px 20px',color:label}">${{ inv.amount }}</td>
            <td style="padding:12px 20px">
              <span style="background:#F0FDF4;border:1px solid #BBF7D0;color:#16A34A;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">{{ inv.status }}</span>
            </td>
            <td style="padding:12px 20px">
              <a :href="inv.invoice_url" target="_blank" style="color:#6D4EE8;font-size:12px;font-weight:500;text-decoration:none">Download</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- FAQ -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px'}">
      <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:'0 0 16px'}">Frequently Asked Questions</p>
      <div style="display:flex;flex-direction:column;gap:0">
        <div v-for="(q, i) in faq" :key="i"
          :style="{borderBottom:`1px solid ${divider}`,cursor:'pointer'}"
          @click="faqOpen === i ? faqOpen = null : faqOpen = i">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0">
            <p :style="{fontSize:'13px',fontWeight:600,color:text,margin:0}">{{ q.q }}</p>
            <i class="bi bi-chevron-down" :style="{transform: faqOpen===i ? 'rotate(180deg)' : 'rotate(0deg)', transition:'transform 0.2s', flexShrink:0, fontSize:'15px', color:muted}"></i>
          </div>
          <div v-if="faqOpen===i" style="padding:0 0 14px">
            <p :style="{fontSize:'13px',color:sub,margin:0,lineHeight:1.65}">{{ q.a }}</p>
          </div>
        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'
import AgencyConfigurator from '@/components/AgencyConfigurator.vue'

const auth = useAuthStore()
const theme = useThemeStore()
const checkoutLoading = ref(null)
const portalLoading = ref(false)
const billing = ref('monthly')
const promoCode = ref('')
const invoices = ref([])
const faqOpen = ref(null)
const showAgencyConfig = ref(false)

// Theme tokens
const card      = computed(() => theme.dark ? 'rgba(255,255,255,0.04)' : '#fff')
const border    = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const divider   = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6')
const text      = computed(() => theme.dark ? '#fff' : '#111827')
const sub       = computed(() => theme.dark ? 'rgba(255,255,255,0.55)' : '#6B7280')
const muted     = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
const label     = computed(() => theme.dark ? 'rgba(255,255,255,0.7)' : '#374151')
const subtleBg  = computed(() => theme.dark ? 'rgba(255,255,255,0.03)' : '#F9FAFB')
const inputBg   = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#fff')
const inputBorder = computed(() => theme.dark ? 'rgba(255,255,255,0.12)' : '#E5E7EB')

const currentPlan = computed(() => auth.user?.plan || 'free')
const pageLimitLabel = computed(() => currentPlan.value === 'agency' ? '∞' : currentPlan.value === 'pro' ? '5' : '1')
const directLimitLabel = computed(() => currentPlan.value === 'agency' ? '∞' : currentPlan.value === 'pro' ? '2' : '1')
const renewalDate = computed(() => {
  if (!auth.user?.subscription_renews_at) return '—'
  return new Date(auth.user.subscription_renews_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
})

const freePlan = { features: ['1 Direct Link', '1 VSL Page', 'Basic Analytics', 'Bot Protection', 'Mobile Optimized'] }
const proPlan = { features: ['2 Direct Links', '5 VSL Pages', 'Real-Time Analytics', 'Bot Protection', 'Themes', 'Social Analytics', 'Custom Domain', 'Geo Targeting', 'Deep Linking', 'Payments'] }
const agencyPlan = { features: ['Everything in Pro', 'Free Domain', 'Advanced Analytics', 'Paywall', 'API Access', 'Priority Support', 'White Label', 'Team Management'] }

const faq = [
  { q: 'Can I cancel at any time?', a: 'Yes, no commitment. You continue to benefit from your plan until the end of the current period.' },
  { q: 'Are payments secure?', a: 'Yes. Payments are processed by Stripe, PCI DSS level 1 certified. No card data is stored on our servers.' },
  { q: 'Can I change my plan?', a: 'Yes, you can upgrade or downgrade at any time from the Stripe billing portal.' },
  { q: 'What happens to my pages if I downgrade?', a: 'Your pages remain accessible but you may not be able to create new ones beyond your plan limit.' },
]

async function checkout(plan) {
  checkoutLoading.value = plan
  try {
    const { data } = await api.post('/billing/checkout', { plan, billing: billing.value })
    window.location.href = data.url
  } catch {
    alert('Error creating checkout. Please try again.')
    checkoutLoading.value = null
  }
}

async function checkoutAgency({ pages, links, price, billing: b }) {
  checkoutLoading.value = 'agency-custom'
  try {
    const { data } = await api.post('/billing/checkout', {
      plan: 'agency',
      billing: b,
      custom_pages: pages === Infinity ? null : pages,
      custom_links: links === Infinity ? null : links,
      custom_price: price,
    })
    window.location.href = data.url
  } catch {
    alert('Error creating checkout. Please try again.')
    checkoutLoading.value = null
  }
}

async function openPortal() {
  portalLoading.value = true
  try {
    const { data } = await api.post('/billing/portal')
    window.location.href = data.url
  } catch {
    alert('Error opening billing portal.')
    portalLoading.value = false
  }
}

onMounted(async () => {
  await auth.fetchMe()
})
</script>
