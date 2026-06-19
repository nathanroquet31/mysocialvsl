<template>
  <DashboardLayout title="Billing">

    <!-- Card-free trial banner -->
    <div v-if="onTrial"
      :style="{display:'flex',alignItems:'center',justifyContent:'space-between',flexWrap:'wrap',gap:'14px',marginBottom:'24px',padding:'16px 20px',borderRadius:'12px',border:`1px solid ${trialDaysLeft <= 7 ? '#F59E0B' : '#6D4EE8'}`,background: trialDaysLeft <= 7 ? 'rgba(245,158,11,0.10)' : 'rgba(109,78,232,0.10)'}">
      <div style="display:flex;align-items:center;gap:12px">
        <i class="bi bi-stars" :style="{fontSize:'20px',color: trialDaysLeft <= 7 ? '#F59E0B' : '#6D4EE8'}"></i>
        <div>
          <p :style="{fontSize:'14px',fontWeight:700,color:text,margin:'0 0 2px'}">
            {{ trialDaysLeft <= 1 ? 'Your free trial ends today' : `${trialDaysLeft} days left in your free trial` }}
          </p>
          <p :style="{fontSize:'12px',color:sub,margin:0}">You're on Agency, free. Add a card to keep your pages live when the trial ends — you won't be charged today.</p>
        </div>
      </div>
      <button @click="checkout('agency')" :disabled="checkoutLoading==='agency'"
        :style="{background:'#6D4EE8',color:'#fff',border:'none',borderRadius:'8px',padding:'10px 18px',fontSize:'13px',fontWeight:600,cursor:'pointer',fontFamily:'Inter,sans-serif',whiteSpace:'nowrap',opacity: checkoutLoading==='agency' ? 0.7 : 1}">
        {{ checkoutLoading==='agency' ? 'Loading…' : 'Add my card' }}
      </button>
    </div>

    <!-- Current Plan card -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px',marginBottom:'24px'}">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px">
        <div>
          <p :style="{fontSize:'11px',fontWeight:600,color:muted,textTransform:'uppercase',letterSpacing:'0.1em',margin:'0 0 10px'}">Current Plan</p>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <span :style="{fontSize:'18px',fontWeight:700,color:text,textTransform:'capitalize'}">{{ currentPlan }}</span>
            <span v-if="onTrial" style="background:rgba(109,78,232,0.12);border:1px solid #6D4EE8;color:#6D4EE8;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">Free trial</span>
            <span v-else style="background:#F0FDF4;border:1px solid #BBF7D0;color:#16A34A;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">Active</span>
          </div>
          <p v-if="onTrial" :style="{fontSize:'12px',color:sub,margin:'0 0 14px'}">Trial ends in <strong :style="{color:label}">{{ trialDaysLeft }} {{ trialDaysLeft === 1 ? 'day' : 'days' }}</strong></p>
          <p v-else :style="{fontSize:'12px',color:sub,margin:'0 0 14px'}">Subscription Renews On: <strong :style="{color:label}">{{ renewalDate }}</strong></p>
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
          <button v-if="currentPlan !== 'free' && !onTrial" @click="openPortal" :disabled="portalLoading"
            style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif"
            :style="{opacity: portalLoading ? 0.7 : 1}">
            {{ portalLoading ? 'Loading…' : 'Manage Subscription' }}
          </button>
          <button v-if="!onTrial" :style="{background:'none',border:'none',fontSize:'12px',color:muted,cursor:'pointer',fontFamily:'Inter,sans-serif'}">Cancel Plan</button>
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
            <i class="bi bi-check-circle-fill" style="margin-right:4px"></i>Current Plan
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
            <i class="bi bi-check-circle-fill" style="margin-right:4px"></i>Current Plan
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
        <AgencyConfigurator :dark="theme.dark" :billing="billing" :current-plan="currentPlan" @checkout="checkoutAgency" />
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

    <!-- Extra Packs (Pro only) -->
    <div v-if="currentPlan === 'pro'"
      :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px',marginBottom:'24px'}">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:0}">Extra packs</p>
        <span style="background:#EEE9FF;border:1px solid #C7BBFF;color:#6D4EE8;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">Pro add-on</span>
      </div>
      <p :style="{fontSize:'13px',color:sub,margin:'0 0 24px'}">Add packs of 100 extra pages or 100 extra direct links, billed on top of your Pro subscription. The difference is charged pro rata right away.</p>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">

        <!-- Pages packs -->
        <div :style="{border:`1px solid ${border}`,borderRadius:'12px',padding:'18px',background:subtleBg}">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
            <div style="width:32px;height:32px;border-radius:8px;background:#EEE9FF;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <i class="bi bi-file-earmark-richtext" style="color:#6D4EE8;font-size:14px"></i>
            </div>
            <div>
              <p :style="{fontSize:'13px',fontWeight:600,color:text,margin:0}">VSL Pages</p>
              <p :style="{fontSize:'11px',color:muted,margin:0}">+100 pages / pack — $9/mo</p>
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <span :style="{fontSize:'12px',color:sub}">Active packs</span>
            <div style="display:flex;align-items:center;gap:0;border-radius:8px;overflow:hidden;border:'1px solid #E5E7EB'" :style="{border:`1px solid ${border}`}">
              <button @click="pagesPacks = Math.max(0, pagesPacks - 1)"
                :style="{width:'32px',height:'32px',border:'none',background:theme.dark?'rgba(255,255,255,0.08)':'#F3F4F6',color:text,fontWeight:700,cursor:'pointer',fontSize:'16px',fontFamily:'Inter,sans-serif',display:'flex',alignItems:'center',justifyContent:'center'}">
                −
              </button>
              <span :style="{padding:'0 14px',fontSize:'15px',fontWeight:700,color:text,minWidth:'48px',textAlign:'center',background:inputBg}">{{ pagesPacks }}</span>
              <button @click="pagesPacks++"
                :style="{width:'32px',height:'32px',border:'none',background:theme.dark?'rgba(255,255,255,0.08)':'#F3F4F6',color:text,fontWeight:700,cursor:'pointer',fontSize:'16px',fontFamily:'Inter,sans-serif',display:'flex',alignItems:'center',justifyContent:'center'}">
                +
              </button>
            </div>
          </div>
          <div :style="{background:theme.dark?'rgba(109,78,232,0.12)':'#F3F0FF',borderRadius:'8px',padding:'10px 12px',display:'flex',alignItems:'center',justifyContent:'space-between'}">
            <span :style="{fontSize:'12px',color:'#6D4EE8',fontWeight:500}">New limit</span>
            <span :style="{fontSize:'14px',fontWeight:700,color:'#6D4EE8'}">{{ pagesPacks === 0 ? '5' : newPageLimit }} pages</span>
          </div>
        </div>

        <!-- Links packs -->
        <div :style="{border:`1px solid ${border}`,borderRadius:'12px',padding:'18px',background:subtleBg}">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
            <div style="width:32px;height:32px;border-radius:8px;background:#EEE9FF;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <i class="bi bi-link-45deg" style="color:#6D4EE8;font-size:14px"></i>
            </div>
            <div>
              <p :style="{fontSize:'13px',fontWeight:600,color:text,margin:0}">Direct Links</p>
              <p :style="{fontSize:'11px',color:muted,margin:0}">+100 links / pack — $9/mo</p>
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <span :style="{fontSize:'12px',color:sub}">Active packs</span>
            <div style="display:flex;align-items:center;gap:0;border-radius:8px;overflow:hidden" :style="{border:`1px solid ${border}`}">
              <button @click="linksPacks = Math.max(0, linksPacks - 1)"
                :style="{width:'32px',height:'32px',border:'none',background:theme.dark?'rgba(255,255,255,0.08)':'#F3F4F6',color:text,fontWeight:700,cursor:'pointer',fontSize:'16px',fontFamily:'Inter,sans-serif',display:'flex',alignItems:'center',justifyContent:'center'}">
                −
              </button>
              <span :style="{padding:'0 14px',fontSize:'15px',fontWeight:700,color:text,minWidth:'48px',textAlign:'center',background:inputBg}">{{ linksPacks }}</span>
              <button @click="linksPacks++"
                :style="{width:'32px',height:'32px',border:'none',background:theme.dark?'rgba(255,255,255,0.08)':'#F3F4F6',color:text,fontWeight:700,cursor:'pointer',fontSize:'16px',fontFamily:'Inter,sans-serif',display:'flex',alignItems:'center',justifyContent:'center'}">
                +
              </button>
            </div>
          </div>
          <div :style="{background:theme.dark?'rgba(109,78,232,0.12)':'#F3F0FF',borderRadius:'8px',padding:'10px 12px',display:'flex',alignItems:'center',justifyContent:'space-between'}">
            <span :style="{fontSize:'12px',color:'#6D4EE8',fontWeight:500}">New limit</span>
            <span :style="{fontSize:'14px',fontWeight:700,color:'#6D4EE8'}">{{ linksPacks === 0 ? '2' : newLinkLimit }} links</span>
          </div>
        </div>

      </div>

      <!-- Total + confirm -->
      <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',padding:'16px 20px',background:theme.dark?'rgba(255,255,255,0.04)':'#F9FAFB',borderRadius:'12px',border:`1px solid ${border}`}">
        <div>
          <p :style="{fontSize:'12px',color:muted,margin:'0 0 2px'}">Monthly add-on</p>
          <div style="display:flex;align-items:baseline;gap:4px">
            <span :style="{fontSize:'22px',fontWeight:800,color:text}">+${{ addonTotal }}</span>
            <span :style="{fontSize:'13px',color:muted}">/mo</span>
          </div>
          <p :style="{fontSize:'11px',color:muted,margin:'2px 0 0'}">On top of your $19 Pro <i class="bi bi-arrow-right" style="font-size:10px"></i> <strong :style="{color:text}">${{ 19 + addonTotal }}/mo total</strong></p>
        </div>
        <button @click="saveAddons" :disabled="addonsLoading"
          :style="{background:addonsSaved?'#16A34A':'#6D4EE8',color:'#fff',border:'none',borderRadius:'10px',padding:'11px 24px',fontSize:'13px',fontWeight:600,cursor:addonsLoading?'wait':'pointer',fontFamily:'Inter,sans-serif',opacity:addonsLoading?0.7:1,transition:'background 0.2s',minWidth:'160px'}">
          <i v-if="addonsSaved" class="bi bi-check" style="margin-right:4px"></i>{{ addonsSaved ? 'Saved' : addonsLoading ? 'Saving…' : 'Confirm packs' }}
        </button>
      </div>
    </div>

    <!-- Manage Subscription card -->
    <div :style="{background:card,border:`1px solid ${border}`,borderRadius:'12px',padding:'24px',marginBottom:'24px'}">
      <p :style="{fontSize:'14px',fontWeight:600,color:text,margin:'0 0 8px'}">Manage Subscription</p>
      <p :style="{fontSize:'13px',color:sub,margin:'0 0 16px'}">Access your billing portal to update payment method, download invoices, and manage your subscription.</p>
      <button @click="openPortal" :disabled="portalLoading"
        style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif"
        :style="{opacity: portalLoading ? 0.7 : 1}">
        {{ portalLoading ? 'Loading…' : 'Go to Billing Portal' }} <i v-if="!portalLoading" class="bi bi-arrow-right"></i>
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

    <!-- Agency plan confirmation modal (replaces the native confirm popup) -->
    <Teleport to="body">
      <div v-if="agencyConfirm" @click.self="cancelAgency"
        style="position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;padding:20px">
        <div style="background:#13101f;border:1px solid rgba(255,255,255,0.1);border-radius:18px;max-width:400px;width:100%;padding:26px;box-shadow:0 30px 80px rgba(0,0,0,0.6);font-family:Inter,sans-serif">
          <h3 style="font-size:17px;font-weight:800;color:#fff;margin:0 0 6px">{{ agencyConfirm.isUpdate ? 'Update your Agency plan' : 'Confirm Agency plan' }}</h3>
          <p style="font-size:13px;color:rgba(255,255,255,0.55);margin:0 0 18px;line-height:1.5">Review your new plan before confirming.</p>

          <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:16px;margin-bottom:16px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
              <span style="font-size:13px;color:rgba(255,255,255,0.6)">Landing pages</span>
              <span style="font-size:14px;font-weight:700;color:#fff">{{ agencyConfirm.pages === Infinity ? '∞' : agencyConfirm.pages }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
              <span style="font-size:13px;color:rgba(255,255,255,0.6)">Direct links</span>
              <span style="font-size:14px;font-weight:700;color:#fff">{{ agencyConfirm.links === Infinity ? '∞' : agencyConfirm.links }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid rgba(255,255,255,0.08)">
              <span style="font-size:13px;color:rgba(255,255,255,0.6)">Price</span>
              <span style="font-size:18px;font-weight:800;color:#fff">${{ agencyConfirm.price }}<span style="font-size:12px;font-weight:500;color:rgba(255,255,255,0.5)">/mo</span></span>
            </div>

            <!-- Exact prorated amount charged right now (in-place updates only) -->
            <div v-if="agencyConfirm.isUpdate" style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.08)">
              <span style="font-size:13px;color:rgba(255,255,255,0.6)">Charged now <span style="font-size:11px;color:rgba(255,255,255,0.4)">(pro rata)</span></span>
              <span style="font-size:16px;font-weight:800;color:#A78BFA">
                <template v-if="agencyPreview && agencyPreview.loading">…</template>
                <template v-else-if="agencyPreview && agencyPreview.amount != null">${{ agencyPreview.amount.toFixed(2) }}</template>
                <template v-else>—</template>
              </span>
            </div>
          </div>

          <div style="display:flex;gap:8px;align-items:flex-start;background:rgba(109,78,232,0.1);border:1px solid rgba(109,78,232,0.25);border-radius:10px;padding:11px 13px;margin-bottom:18px">
            <i class="bi bi-info-circle" style="color:#A78BFA;font-size:14px;margin-top:1px"></i>
            <p style="font-size:12px;color:rgba(255,255,255,0.7);margin:0;line-height:1.5">
              {{ agencyConfirm.isUpdate
                ? 'The prorated difference for the rest of this billing period is charged to your saved card now. If the payment fails, your plan stays unchanged.'
                : 'You\'ll be redirected to Stripe to enter your card and pay.' }}
            </p>
          </div>

          <p v-if="agencyError" style="font-size:12px;color:#F87171;margin:0 0 14px;text-align:center;line-height:1.5">{{ agencyError }}</p>

          <div style="display:flex;gap:10px">
            <button @click="cancelAgency" :disabled="checkoutLoading==='agency-custom'"
              style="flex:1;padding:11px;border-radius:10px;border:1px solid rgba(255,255,255,0.14);background:transparent;color:rgba(255,255,255,0.8);font-size:13px;font-weight:600;cursor:pointer;font-family:Inter,sans-serif">
              Cancel
            </button>
            <button @click="proceedAgency" :disabled="checkoutLoading==='agency-custom'"
              :style="{flex:1,padding:'11px',borderRadius:'10px',border:'none',background:'#6D4EE8',color:'#fff',fontSize:'13px',fontWeight:700,cursor:checkoutLoading==='agency-custom'?'wait':'pointer',fontFamily:'Inter,sans-serif',opacity:checkoutLoading==='agency-custom'?0.7:1}">
              {{ checkoutLoading==='agency-custom' ? 'Processing…' : agencyConfirm.isUpdate ? 'Confirm & pay' : 'Continue' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

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
const agencyConfirm = ref(null)   // pending Agency change shown in the confirm modal
const agencyError = ref('')       // error surfaced inside the modal (e.g. prorated charge failed)
const agencyPreview = ref(null)   // { loading } | { amount, currency } | { amount: null } — prorated charge preview
const billing = ref('monthly')
const promoCode = ref('')
const invoices = ref([])
const faqOpen = ref(null)
const showAgencyConfig = ref(false)

const PACK_PRICE = 9
const pagesPacks = ref(0)
const linksPacks = ref(0)
const addonsLoading = ref(false)
const addonsSaved = ref(false)

const addonTotal = computed(() => (pagesPacks.value + linksPacks.value) * PACK_PRICE)
const newPageLimit = computed(() => 5 + pagesPacks.value * 100)
const newLinkLimit = computed(() => 2 + linksPacks.value * 100)

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
// Card-free beta trial state (from /me).
const onTrial = computed(() => !!auth.user?.on_trial)
const trialDaysLeft = computed(() => auth.user?.trial_days_left ?? 0)
// Show the real cap from the API (agency = its chosen tier, e.g. 25); ∞ only when truly unlimited.
const fmtLimit = (n, fallback) => {
  const v = n ?? fallback
  return v > 100000 ? '∞' : String(v)
}
const pageLimitLabel = computed(() => fmtLimit(auth.user?.page_limit, currentPlan.value === 'pro' ? 5 : 1))
const directLimitLabel = computed(() => fmtLimit(auth.user?.link_limit, currentPlan.value === 'pro' ? 2 : 1))
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
    if (data.url) {
      window.location.href = data.url
      return
    }
    // Updated in place (existing subscriber) — refresh instead of redirecting to undefined.
    await auth.fetchMe()
    checkoutLoading.value = null
    alert('Plan updated ✓')
  } catch {
    alert('Error creating checkout. Please try again.')
    checkoutLoading.value = null
  }
}

// Open the confirmation modal (replaces the raw window.confirm) — explicit consent
// before anything is charged.
function checkoutAgency({ pages, links, price, billing: b }) {
  agencyError.value = ''
  agencyPreview.value = null
  const isUpdate = currentPlan.value === 'agency'
  agencyConfirm.value = { pages, links, price, billing: b, isUpdate }
  // For in-place updates, ask the server the exact prorated amount to show in the modal.
  if (isUpdate) fetchAgencyPreview(pages, links)
}

async function fetchAgencyPreview(pages, links) {
  agencyPreview.value = { loading: true }
  try {
    const { data } = await api.post('/billing/preview', {
      custom_pages: pages === Infinity ? null : pages,
      custom_links: links === Infinity ? null : links,
    })
    agencyPreview.value = { amount: data.amount_now, currency: data.currency || 'USD' }
  } catch {
    agencyPreview.value = { amount: null } // fall back to the generic notice
  }
}

function cancelAgency() {
  if (checkoutLoading.value === 'agency-custom') return
  agencyConfirm.value = null
  agencyPreview.value = null
}

// Confirmed in the modal → create the Stripe checkout, or update the subscription
// in place (the server charges the prorated difference immediately and only then
// grants the new limits; a failed charge returns 402 and changes nothing).
async function proceedAgency() {
  const { pages, links, billing: b } = agencyConfirm.value
  agencyError.value = ''
  checkoutLoading.value = 'agency-custom'
  try {
    // Price is recomputed server-side from the chosen tiers — never sent by the client.
    const { data } = await api.post('/billing/checkout', {
      plan: 'agency',
      billing: b,
      custom_pages: pages === Infinity ? null : pages,
      custom_links: links === Infinity ? null : links,
    })
    if (data.url) {
      window.location.href = data.url
      return
    }
    // Already subscribed → updated in place. Refresh limits and close.
    await auth.fetchMe()
    checkoutLoading.value = null
    agencyConfirm.value = null
    agencyPreview.value = null
  } catch (e) {
    checkoutLoading.value = null
    agencyError.value = e.response?.data?.message || 'Something went wrong. Your plan was not changed.'
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

async function saveAddons() {
  addonsLoading.value = true
  addonsSaved.value = false
  try {
    const { data } = await api.post('/billing/addons', {
      pages_packs: pagesPacks.value,
      links_packs: linksPacks.value,
    })
    auth.user.extra_pages = data.extra_pages
    auth.user.extra_links = data.extra_links
    auth.user.page_limit  = data.page_limit
    auth.user.link_limit  = data.link_limit
    addonsSaved.value = true
    setTimeout(() => { addonsSaved.value = false }, 3000)
  } catch (e) {
    // 402 = the prorated charge for the added packs failed; nothing was changed.
    alert(e.response?.data?.message || 'Update failed. Please try again.')
  } finally {
    addonsLoading.value = false
  }
}

onMounted(async () => {
  await auth.fetchMe()
  pagesPacks.value = Math.round((auth.user?.extra_pages ?? 0) / 100)
  linksPacks.value = Math.round((auth.user?.extra_links ?? 0) / 100)
})
</script>
