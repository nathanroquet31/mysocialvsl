<template>
  <DashboardLayout>

    <!-- Success overlay -->
    <div v-if="successMsg"
      :style="{position:'fixed',inset:0,background:'rgba(0,0,0,0.85)',zIndex:9999,display:'flex',alignItems:'center',justifyContent:'center',fontFamily:'Inter,sans-serif'}">
      <div :style="{textAlign:'center'}">
        <i :class="isEditMode ? 'bi bi-check-circle-fill' : 'bi bi-rocket-takeoff-fill'" :style="{fontSize:'46px',marginBottom:'12px',display:'block',color: isEditMode ? '#22C55E' : '#A78BFA'}"></i>
        <p :style="{fontSize:'22px',fontWeight:700,color:'#fff',marginBottom:'6px'}">{{ isEditMode ? 'Page updated!' : 'Page published!' }}</p>
        <p :style="{fontSize:'14px',color:'rgba(255,255,255,0.5)'}">Redirecting…</p>
      </div>
    </div>

    <div :style="{display:'flex',flexDirection:'column',height:'100dvh',background:C.bg,fontFamily:'Inter,sans-serif',overflow:'hidden'}">

      <!-- TOP BAR -->
      <div class="bld-topbar" :style="{height:'60px',display:'flex',alignItems:'center',justifyContent:'space-between',padding:'0 32px',borderBottom:`1px solid ${C.borderLight}`,flexShrink:0}">
        <div :style="{display:'flex',alignItems:'center',gap:'10px'}">
          <div :style="{width:'28px',height:'28px',background:'linear-gradient(135deg,#6D4EE8,#A78BFA)',borderRadius:'8px',flexShrink:0}"></div>
          <span :style="{fontSize:'15px',fontWeight:700,color:C.text}">MySocialVSL</span>
        </div>

        <!-- Step indicator with connecting line -->
        <div class="bld-steps" :style="{position:'relative',display:'flex',alignItems:'center',gap:0}">
          <!-- Background rail -->
          <div :style="{position:'absolute',top:'50%',left:'14px',right:'14px',height:'2px',background:C.stepRail,transform:'translateY(-50%)',zIndex:0}"></div>
          <!-- Progress fill -->
          <div :style="{position:'absolute',top:'50%',left:'14px',height:'2px',background:'#6D4EE8',transform:'translateY(-50%)',zIndex:1,transition:'width 0.3s ease',width: steps.length > 1 ? (currentStepIndex / (steps.length - 1) * (100 - 28/steps.length)) + '%' : '0%'}"></div>

          <div v-for="(step, i) in steps" :key="step.id"
            :style="{position:'relative',zIndex:2,display:'flex',flexDirection:'column',alignItems:'center',flex:i < steps.length - 1 ? 1 : 'none',cursor:'pointer'}"
            @click="goToStep(i)">
            <!-- Dot -->
            <div :style="{
              width:'38px',height:'38px',borderRadius:'50%',
              display:'flex',alignItems:'center',justifyContent:'center',
              fontSize:'13px',fontWeight:700,transition:'all 0.25s',
              background: i < currentStepIndex ? '#6D4EE8' : i === currentStepIndex ? '#6D4EE8' : C.stepInactiveBg,
              color: i <= currentStepIndex ? '#fff' : C.stepInactiveText,
              boxShadow: i === currentStepIndex ? '0 0 0 6px rgba(109,78,232,0.25)' : i < currentStepIndex ? '0 0 0 2px rgba(109,78,232,0.3)' : 'none',
              border: i < currentStepIndex ? '2px solid #6D4EE8' : 'none',
            }">
              <svg v-if="i < currentStepIndex" width="13" height="13" viewBox="0 0 10 10" fill="none">
                <path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
              <span v-else>{{ i + 1 }}</span>
            </div>
            <!-- Label under dot -->
            <span :style="{fontSize:'10px',fontWeight:700,marginTop:'5px',whiteSpace:'nowrap',color:i===currentStepIndex?C.stepLabelActive:i<currentStepIndex?C.stepLabelDone:C.stepLabelInactive,letterSpacing:'0.01em'}">
              {{ step.label.split(' — ')[1] || step.label }}
            </span>
          </div>
        </div>

        <button @click="$router.push('/dashboard/links')"
          :style="{padding:'6px 16px',border:`1px solid ${C.cancelBorder}`,borderRadius:'8px',background:'transparent',color:C.cancelColor,fontSize:'13px',cursor:'pointer',fontFamily:'inherit'}">
          {{ isEditMode ? '← Back' : 'Cancel' }}
        </button>
      </div>

      <!-- Error banner -->
      <div v-if="error"
        :style="{background:theme.dark?'rgba(239,68,68,0.12)':'#FEF2F2',border:'1px solid rgba(239,68,68,0.3)',borderRadius:'0',padding:'12px 32px',fontSize:'13px',color:'#EF4444',fontWeight:500}">
        {{ error }}
      </div>

      <!-- MAIN -->
      <div :style="{flex:1,display:'flex',overflow:'hidden'}">

        <!-- LEFT: Form steps -->
        <div class="bld-form" :style="{flex:1,overflowY:'auto',padding:'48px 56px'}">

          <Transition :name="stepDir === 'forward' ? 'step-fwd' : 'step-back'" mode="out-in">

          <!-- ===== STEP 1 — SETUP ===== -->
          <div v-if="activeTab === 'setup'" key="setup">

            <!-- Sub-step indicator -->
            <div :style="{display:'flex',alignItems:'center',gap:'6px',marginBottom:'32px'}">
              <div :style="{padding:'4px 14px',borderRadius:'999px',fontSize:'11px',fontWeight:700,transition:'all 0.2s',
                background:setupSubStep===0?'#6D4EE8':C.pillBg,
                color:setupSubStep===0?'#fff':C.pillText}">Type</div>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" :stroke="C.textVeryFaint" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
              <div :style="{padding:'4px 14px',borderRadius:'999px',fontSize:'11px',fontWeight:700,transition:'all 0.2s',
                background:setupSubStep===1?'#6D4EE8':C.pillBg,
                color:setupSubStep===1?'#fff':C.pillText}">Info</div>
            </div>

            <!-- Sub-step 0 : Page type -->
            <div v-show="setupSubStep === 0">
              <h2 :style="{fontSize:'24px',fontWeight:700,color:C.text,marginBottom:'6px'}">Create your conversion page</h2>
              <p :style="{fontSize:'14px',color:C.textMuted,marginBottom:'28px'}">Two formats available depending on your goal.</p>

              <div class="bld-grid" :style="{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'16px',marginBottom:'20px'}">

                <!-- Card Page VSL -->
                <div @click="form.page_type='vsl'"
                  :style="{borderRadius:'18px',padding:'20px 16px',cursor:'pointer',transition:'all 0.2s',border:'2px solid',position:'relative',
                    borderColor:form.page_type==='vsl'?'#6D4EE8':C.border,
                    background:form.page_type==='vsl'?'rgba(109,78,232,0.1)':C.surface}">

                  <!-- Recommended badge -->
                  <div :style="{position:'absolute',top:'12px',right:'12px',background:'linear-gradient(135deg,#6D4EE8,#A78BFA)',borderRadius:'999px',padding:'2px 8px',fontSize:'9px',fontWeight:700,color:'#fff'}">Recommended</div>

                  <!-- Phone mockup VSL -->
                  <div :style="{width:'88px',margin:'0 auto 14px',background:'#111',borderRadius:'20px',padding:'4px',boxShadow:'0 12px 32px rgba(0,0,0,0.6)'}">
                    <div :style="{borderRadius:'17px',overflow:'hidden',background:'#000'}">
                      <div :style="{height:'9px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                        <div :style="{width:'18px',height:'3px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                      </div>
                      <div :style="{aspectRatio:'9/16',position:'relative',background:'linear-gradient(180deg,#0a0814 0%,#1a0d30 100%)'}">
                        <!-- Centered play icon -->
                        <div :style="{position:'absolute',top:'50%',left:'50%',transform:'translate(-50%,-60%)'}">
                          <div :style="{width:'26px',height:'26px',borderRadius:'50%',background:'rgba(255,255,255,0.1)',border:'1.5px solid rgba(255,255,255,0.25)',display:'flex',alignItems:'center',justifyContent:'center'}">
                            <svg width="9" height="9" viewBox="0 0 9 9" fill="rgba(255,255,255,0.85)"><polygon points="3,2 8,4.5 3,7"/></svg>
                          </div>
                        </div>
                        <!-- VSL badge -->
                        <div :style="{position:'absolute',top:'5px',left:'5px',background:'rgba(109,78,232,0.92)',borderRadius:'4px',padding:'2px 4px',fontSize:'6px',fontWeight:800,color:'#fff',letterSpacing:'0.06em'}">VSL</div>
                        <!-- Online badge -->
                        <div :style="{position:'absolute',top:'5px',right:'5px',background:'rgba(16,185,129,0.15)',border:'1px solid rgba(16,185,129,0.35)',borderRadius:'4px',padding:'2px 4px',display:'flex',alignItems:'center',gap:'2px'}">
                          <div :style="{width:'3px',height:'3px',borderRadius:'50%',background:'#10B981'}"></div>
                          <span :style="{fontSize:'5px',fontWeight:600,color:'#10B981'}">Online</span>
                        </div>
                        <!-- Bottom overlay + CTA -->
                        <div :style="{position:'absolute',bottom:0,left:0,right:0,padding:'28px 6px 8px',background:'linear-gradient(to top,rgba(0,0,0,0.92) 0%,transparent 100%)'}">
                          <div :style="{height:'4px',background:'rgba(255,255,255,0.4)',borderRadius:'2px',marginBottom:'2px',width:'55%'}"></div>
                          <div :style="{height:'3px',background:'rgba(255,255,255,0.18)',borderRadius:'2px',marginBottom:'7px',width:'38%'}"></div>
                          <div :style="{height:'12px',background:'#00AFF0',borderRadius:'5px',display:'flex',alignItems:'center',justifyContent:'center',boxShadow:'0 3px 10px rgba(0,175,240,0.5)'}">
                            <div :style="{height:'2px',background:'rgba(255,255,255,0.9)',borderRadius:'2px',width:'55%'}"></div>
                          </div>
                        </div>
                      </div>
                      <div :style="{height:'9px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                        <div :style="{width:'28px',height:'2px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                      </div>
                    </div>
                  </div>

                  <p :style="{fontSize:'14px',fontWeight:700,color:C.text,textAlign:'center',marginBottom:'4px'}">VSL Page</p>
                  <p :style="{fontSize:'11px',color:C.textMuted,textAlign:'center',lineHeight:1.5,marginBottom:'12px'}">A full-screen video that convinces<br/>before they even click.</p>
                  <div :style="{display:'flex',flexDirection:'column',gap:'4px'}">
                    <div v-for="f in [{i:'bi-camera-video-fill',t:'Vertical 9:16 video'},{i:'bi-unlock-fill',t:'Configurable CTA button'},{i:'bi-bullseye',t:'Built to convert clicks'}]" :key="f.t"
                      :style="{fontSize:'10px',color:C.textMuted,lineHeight:1.4,display:'flex',alignItems:'center',gap:'6px'}"><i :class="'bi '+f.i" :style="{color:'#A78BFA'}"></i>{{ f.t }}</div>
                  </div>
                  <div v-if="form.page_type==='vsl'" :style="{marginTop:'12px',display:'flex',justifyContent:'center'}">
                    <div :style="{background:'#6D4EE8',borderRadius:'999px',padding:'3px 12px',fontSize:'10px',fontWeight:700,color:'#fff',display:'inline-flex',alignItems:'center',gap:'4px'}">
                      <svg width="8" height="8" viewBox="0 0 10 10"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                      Selected
                    </div>
                  </div>
                </div>

                <!-- Card Direct link -->
                <div @click="form.page_type='direct'"
                  :style="{borderRadius:'18px',padding:'20px 16px',cursor:'pointer',transition:'all 0.2s',border:'2px solid',
                    borderColor:form.page_type==='direct'?'#6D4EE8':C.border,
                    background:form.page_type==='direct'?'rgba(109,78,232,0.1)':C.surface}">

                  <!-- Redirect flow illustration -->
                  <div :style="{height:'147px',display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',background:C.surface,borderRadius:'14px',marginBottom:'14px',position:'relative',overflow:'hidden'}">
                    <div :style="{position:'absolute',inset:0,background:'radial-gradient(ellipse at 50% 60%,rgba(109,78,232,0.1) 0%,transparent 70%)'}"></div>
                    <div :style="{display:'flex',alignItems:'center',gap:'7px',zIndex:1}">
                      <!-- Instagram -->
                      <div :style="{width:'40px',height:'40px',borderRadius:'12px',background:'linear-gradient(135deg,#f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%)',display:'flex',alignItems:'center',justifyContent:'center',boxShadow:'0 4px 12px rgba(220,39,67,0.35)',flexShrink:0}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" width="20" height="20"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="white" stroke="none"/></svg>
                      </div>
                      <!-- Arrow -->
                      <div :style="{display:'flex',flexDirection:'column',alignItems:'center',gap:'2px'}">
                        <svg width="18" height="12" viewBox="0 0 18 12" fill="none"><path d="M0 6h14" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round"/><path d="M8 1.5l8 4.5-8 4.5" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span :style="{fontSize:'6px',color:'rgba(255,255,255,0.2)',fontWeight:700,letterSpacing:'0.06em'}">BYPASS</span>
                      </div>
                      <!-- Safari -->
                      <div :style="{width:'40px',height:'40px',borderRadius:'12px',background:'linear-gradient(135deg,#1a6fdd,#4fa8ff)',display:'flex',alignItems:'center',justifyContent:'center',boxShadow:'0 4px 12px rgba(26,111,221,0.35)',flexShrink:0}">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none"><circle cx="12" cy="12" r="10" stroke="white" stroke-width="1.5"/><path d="M15.5 8.5l-3.5 3.5-3 1 1-3 3.5-3.5z" fill="white"/></svg>
                      </div>
                      <!-- Arrow -->
                      <svg width="14" height="12" viewBox="0 0 14 12" fill="none"><path d="M0 6h10" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round"/><path d="M5 1.5l7 4.5-7 4.5" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      <!-- OnlyFans -->
                      <div :style="{width:'40px',height:'40px',borderRadius:'12px',background:'#00AFF0',display:'flex',alignItems:'center',justifyContent:'center',boxShadow:'0 4px 12px rgba(0,175,240,0.35)',flexShrink:0}">
                        <span :style="{fontSize:'10px',fontWeight:900,color:'white',letterSpacing:'-0.5px'}">OF</span>
                      </div>
                    </div>
                    <p :style="{fontSize:'8px',color:'rgba(255,255,255,0.22)',marginTop:'10px',fontWeight:700,letterSpacing:'0.08em',zIndex:1}">INSTAGRAM → SAFARI → ONLYFANS</p>
                  </div>

                  <p :style="{fontSize:'14px',fontWeight:700,color:C.text,textAlign:'center',marginBottom:'4px'}">Direct link</p>
                  <p :style="{fontSize:'11px',color:C.textMuted,textAlign:'center',lineHeight:1.5,marginBottom:'12px'}">Instant redirect with<br/>deeplink bypass included.</p>
                  <div :style="{display:'flex',flexDirection:'column',gap:'4px'}">
                    <div v-for="f in [{i:'bi-lightning-charge-fill',t:'Instant redirect'},{i:'bi-link-45deg',t:'Automatic deeplink bypass'},{i:'bi-phone-fill',t:'Works with Instagram, TikTok…'}]" :key="f.t"
                      :style="{fontSize:'10px',color:C.textMuted,lineHeight:1.4,display:'flex',alignItems:'center',gap:'6px'}"><i :class="'bi '+f.i" :style="{color:'#A78BFA'}"></i>{{ f.t }}</div>
                  </div>
                  <div v-if="form.page_type==='direct'" :style="{marginTop:'12px',display:'flex',justifyContent:'center'}">
                    <div :style="{background:'#6D4EE8',borderRadius:'999px',padding:'3px 12px',fontSize:'10px',fontWeight:700,color:'#fff',display:'inline-flex',alignItems:'center',gap:'4px'}">
                      <svg width="8" height="8" viewBox="0 0 10 10"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                      Selected
                    </div>
                  </div>
                </div>

              </div>

              <!-- Advanced Options — tabbed -->
              <div :style="{border:`1px solid ${C.border}`,borderRadius:'14px',overflow:'hidden'}">

                <!-- Header -->
                <div @click="showAdvanced = !showAdvanced"
                  class="adv-toggle" :class="{'adv-toggle--attn': !showAdvanced}"
                  :style="{padding:'14px 18px',display:'flex',alignItems:'center',justifyContent:'space-between',cursor:'pointer',userSelect:'none',
                    background: showAdvanced ? C.surface : 'linear-gradient(135deg, rgba(109,78,232,0.16), rgba(167,139,250,0.04))'}">
                  <div :style="{display:'flex',alignItems:'center',gap:'12px'}">
                    <div class="adv-icon" :style="{width:'32px',height:'32px',borderRadius:'9px',background:'rgba(109,78,232,0.2)',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="1.9"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    </div>
                    <div>
                      <div :style="{display:'flex',alignItems:'center',gap:'8px'}">
                        <span :style="{fontSize:'13px',fontWeight:700,color:C.text}">Advanced options</span>
                        <span class="adv-badge">Deeplink · Bot</span>
                      </div>
                      <span :style="{fontSize:'11px',color:C.textDim}">In-app browser bypass & anti-bot protection</span>
                    </div>
                  </div>
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.2"
                    :style="{transform:showAdvanced?'rotate(180deg)':'rotate(0deg)',transition:'transform 0.2s',flexShrink:0}">
                    <path d="M6 9l6 6 6-6"/>
                  </svg>
                </div>

                <div v-show="showAdvanced" :style="{borderTop:`1px solid ${C.borderLight}`}">

                  <!-- Tab bar -->
                  <div :style="{display:'flex',borderBottom:`1px solid ${C.borderLight}`,padding:'0 4px'}">
                    <div @click="advancedTab = 0"
                      :style="{padding:'10px 16px',cursor:'pointer',fontSize:'12px',fontWeight:600,transition:'all 0.15s',display:'flex',alignItems:'center',gap:'6px',
                        color:advancedTab===0?C.text:C.textDim,
                        borderBottom:`2px solid ${advancedTab===0?'#6D4EE8':'transparent'}`}">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" :stroke="advancedTab===0?'#10b981':C.textDim" stroke-width="2" stroke-linecap="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                      Deeplink
                    </div>
                    <div @click="advancedTab = 1"
                      :style="{padding:'10px 16px',cursor:'pointer',fontSize:'12px',fontWeight:600,transition:'all 0.15s',display:'flex',alignItems:'center',gap:'6px',
                        color:advancedTab===1?C.text:C.textDim,
                        borderBottom:`2px solid ${advancedTab===1?'#6D4EE8':'transparent'}`}">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" :stroke="advancedTab===1?'#A78BFA':C.textDim" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                      Strict Deeplink
                    </div>
                    <div @click="advancedTab = 2"
                      :style="{padding:'10px 16px',cursor:'pointer',fontSize:'12px',fontWeight:600,transition:'all 0.15s',display:'flex',alignItems:'center',gap:'6px',
                        color:advancedTab===2?C.text:C.textDim,
                        borderBottom:`2px solid ${advancedTab===2?'#6D4EE8':'transparent'}`}">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" :stroke="advancedTab===2?'#F59E0B':C.textDim" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                      Bot Protection
                    </div>
                  </div>

                  <!-- ── Tab 0 : Deeplink ── -->
                  <div v-show="advancedTab === 0" :style="{padding:'20px',display:'grid',gridTemplateColumns:'1fr auto',gap:'28px',alignItems:'center'}">

                    <div :style="{display:'flex',flexDirection:'column',gap:'16px'}">
                      <div>
                        <p :style="{fontSize:'14px',fontWeight:700,color:C.text,marginBottom:'6px'}">Deeplink — Built-in browser bypass</p>
                        <p :style="{fontSize:'12px',color:C.textMuted,lineHeight:1.7}">
                          Instagram, TikTok and Snapchat open links in their <strong :style="{color:C.text2}">in-app browser</strong>. Visitors can't log in to OnlyFans from there. The deeplink detects this situation and forces the link to open in Safari or Chrome.
                        </p>
                      </div>

                      <!-- Toggle -->
                      <div :style="{background:'rgba(16,185,129,0.07)',border:'1px solid rgba(16,185,129,0.18)',borderRadius:'12px',padding:'14px 16px',display:'flex',alignItems:'center',justifyContent:'space-between',gap:'16px'}">
                        <div>
                          <p :style="{fontSize:'13px',fontWeight:600,color:C.text,marginBottom:'2px'}">Enable deeplink</p>
                          <p :style="{fontSize:'11px',color:C.textDim}">{{ isFreePlan ? 'Available on paid plans' : 'Recommended — enabled by default' }}</p>
                        </div>
                        <div v-if="isFreePlan" @click="router.push('/billing')"
                          :style="{display:'flex',alignItems:'center',gap:'5px',padding:'5px 12px',borderRadius:'999px',cursor:'pointer',background:'rgba(245,158,11,0.12)',border:'1px solid rgba(245,158,11,0.3)',flexShrink:0}">
                          <i class="bi bi-lock-fill" style="font-size:10px;color:#F59E0B"></i>
                          <span :style="{fontSize:'11px',fontWeight:700,color:'#F59E0B'}">Paid</span>
                        </div>
                        <div v-else @click="form.deep_link_enabled = !form.deep_link_enabled"
                          :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:form.deep_link_enabled?'#10b981':C.toggleInactive,position:'relative',transition:'background 0.2s',flexShrink:0}">
                          <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.deep_link_enabled?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                        </div>
                      </div>

                      <!-- Platform pills -->
                      <div>
                        <p :style="{fontSize:'10px',fontWeight:700,color:C.textFaint,textTransform:'uppercase',letterSpacing:'0.08em',marginBottom:'8px'}">Supported platforms</p>
                        <div :style="{display:'flex',gap:'5px',flexWrap:'wrap'}">
                          <div v-for="p in [{l:'Instagram',c:'#E1306C'},{l:'TikTok',c:'#69C9D0'},{l:'Twitter/X',c:'#1d9bf0'},{l:'Facebook',c:'#1877F2'},{l:'Snapchat',c:'#FFFC00'},{l:'Reddit',c:'#FF4500'},{l:'Telegram',c:'#26A5E4'}]" :key="p.l"
                            :style="{padding:'3px 10px',borderRadius:'999px',fontSize:'10px',fontWeight:600,background:p.c+'18',color:p.c,border:`1px solid ${p.c}30`}">
                            {{ p.l }}
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Live demo -->
                    <div :style="{display:'flex',flexDirection:'column',alignItems:'center',gap:'8px'}">
                      <p :style="{fontSize:'9px',fontWeight:700,color:C.textVeryFaint,textTransform:'uppercase',letterSpacing:'0.08em'}">Live demo</p>
                      <DeeplinkDemo :mode="form.page_type === 'direct' ? 'direct' : 'vsl'" />
                    </div>

                  </div>

                  <!-- ── Tab 1 : Strict Deeplink ── -->
                  <div v-show="advancedTab === 1" :style="{padding:'20px',display:'flex',flexDirection:'column',gap:'16px'}">

                    <div>
                      <p :style="{fontSize:'14px',fontWeight:700,color:C.text,marginBottom:'6px'}">Strict Deeplink — Total interception</p>
                      <p :style="{fontSize:'12px',color:C.textMuted,lineHeight:1.7}">
                        When enabled, a <strong :style="{color:C.text2}">full-screen page</strong> appears before any redirect. Visitors have to tap a button to confirm opening in Safari. Nothing else is clickable beforehand.
                      </p>
                    </div>

                    <div class="bld-grid" :style="{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'12px'}">
                      <!-- Normal deeplink -->
                      <div :style="{background:C.surface,border:`1px solid ${C.border}`,borderRadius:'12px',padding:'14px'}">
                        <p :style="{fontSize:'11px',fontWeight:700,color:C.text2,marginBottom:'6px',display:'flex',alignItems:'center',gap:'5px'}">
                          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                          Normal deeplink
                        </p>
                        <p :style="{fontSize:'11px',color:C.textDim,lineHeight:1.6}">Smooth redirect. Visitors are sent straight to Safari. A frictionless experience.</p>
                        <div :style="{marginTop:'8px',fontSize:'10px',color:'#10b981',fontWeight:600,display:'flex',alignItems:'center',gap:'5px'}"><i class="bi bi-check-circle-fill"></i>Best conversion rate</div>
                      </div>
                      <!-- Strict -->
                      <div :style="{background:'rgba(109,78,232,0.07)',border:'1px solid rgba(109,78,232,0.18)',borderRadius:'12px',padding:'14px'}">
                        <p :style="{fontSize:'11px',fontWeight:700,color:C.text2,marginBottom:'6px',display:'flex',alignItems:'center',gap:'5px'}">
                          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                          Strict Deeplink
                        </p>
                        <p :style="{fontSize:'11px',color:C.textDim,lineHeight:1.6}">Mandatory interstitial screen. Guarantees the link opens in the browser even if the deeplink fails.</p>
                        <div :style="{marginTop:'8px',fontSize:'10px',color:'#A78BFA',fontWeight:600,display:'flex',alignItems:'center',gap:'5px'}"><i class="bi bi-check-circle-fill"></i>Guaranteed open</div>
                      </div>
                    </div>

                    <!-- Toggle -->
                    <div :style="{background:C.surface2,border:`1px solid ${C.border}`,borderRadius:'12px',padding:'14px 16px',display:'flex',alignItems:'center',justifyContent:'space-between',gap:'16px'}">
                      <div>
                        <p :style="{fontSize:'13px',fontWeight:600,color:C.text,marginBottom:'2px'}">Enable Strict Deeplink</p>
                        <p :style="{fontSize:'11px',color:C.textDim}">{{ isFreePlan ? 'Available on paid plans' : "Use this if the normal deeplink doesn't work with your target audience" }}</p>
                      </div>
                      <div v-if="isFreePlan" @click="router.push('/billing')"
                        :style="{display:'flex',alignItems:'center',gap:'5px',padding:'5px 12px',borderRadius:'999px',cursor:'pointer',background:'rgba(245,158,11,0.12)',border:'1px solid rgba(245,158,11,0.3)',flexShrink:0}">
                        <i class="bi bi-lock-fill" style="font-size:10px;color:#F59E0B"></i>
                        <span :style="{fontSize:'11px',fontWeight:700,color:'#F59E0B'}">Paid</span>
                      </div>
                      <div v-else @click="form.strict_deep_link = !form.strict_deep_link"
                        :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:form.strict_deep_link?'#6D4EE8':C.toggleInactive,position:'relative',transition:'background 0.2s',flexShrink:0}">
                        <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.strict_deep_link?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                      </div>
                    </div>

                  </div>

                  <!-- ── Tab 2 : Bot Protection ── -->
                  <div v-show="advancedTab === 2" :style="{padding:'20px',display:'flex',flexDirection:'column',gap:'16px'}">

                    <div>
                      <p :style="{fontSize:'14px',fontWeight:700,color:C.text,marginBottom:'6px'}">Bot Protection — Humans only</p>
                      <p :style="{fontSize:'12px',color:C.textMuted,lineHeight:1.7}">
                        Blocks the bots, crawlers and automated scrapers that visit your page. No robot can access your VSL content or your links.
                      </p>
                    </div>

                    <div class="bld-grid" :style="{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'12px'}">
                      <div :style="{background:'rgba(16,185,129,0.06)',border:'1px solid rgba(16,185,129,0.14)',borderRadius:'12px',padding:'14px'}">
                        <p :style="{fontSize:'11px',fontWeight:700,color:'#10b981',marginBottom:'8px',display:'flex',alignItems:'center',gap:'5px'}"><i class="bi bi-shield-check"></i>What it blocks</p>
                        <div :style="{display:'flex',flexDirection:'column',gap:'4px'}">
                          <p v-for="item in ['Crawlers & scrapers','Spam bots','Social media link previews','Competitor analytics tools']" :key="item"
                            :style="{fontSize:'11px',color:C.textMuted,display:'flex',alignItems:'center',gap:'5px',lineHeight:1.4}">
                            <span :style="{color:'#10b981',fontSize:'10px'}">—</span>{{ item }}
                          </p>
                        </div>
                      </div>
                      <div :style="{background:'rgba(245,158,11,0.06)',border:'1px solid rgba(245,158,11,0.16)',borderRadius:'12px',padding:'14px'}">
                        <p :style="{fontSize:'11px',fontWeight:700,color:'#F59E0B',marginBottom:'8px',display:'flex',alignItems:'center',gap:'5px'}"><i class="bi bi-exclamation-triangle-fill"></i>Side effects</p>
                        <div :style="{display:'flex',flexDirection:'column',gap:'4px'}">
                          <p v-for="item in ['No more preview when you share the link','Instagram won\'t generate a preview','Organic reach impacted if you post the link']" :key="item"
                            :style="{fontSize:'11px',color:C.textMuted,display:'flex',alignItems:'center',gap:'5px',lineHeight:1.4}">
                            <span :style="{color:'#F59E0B',fontSize:'10px'}">—</span>{{ item }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Toggle -->
                    <div :style="{background:C.surface2,border:`1px solid ${C.border}`,borderRadius:'12px',padding:'14px 16px',display:'flex',alignItems:'center',justifyContent:'space-between',gap:'16px'}">
                      <div>
                        <p :style="{fontSize:'13px',fontWeight:600,color:C.text,marginBottom:'2px'}">Enable Bot Protection</p>
                        <p :style="{fontSize:'11px',color:C.textDim}">{{ isFreePlan ? 'Available on paid plans' : 'Disabled by default — only recommended if you have a specific issue' }}</p>
                      </div>
                      <div v-if="isFreePlan" @click="router.push('/billing')"
                        :style="{display:'flex',alignItems:'center',gap:'5px',padding:'5px 12px',borderRadius:'999px',cursor:'pointer',background:'rgba(245,158,11,0.12)',border:'1px solid rgba(245,158,11,0.3)',flexShrink:0}">
                        <i class="bi bi-lock-fill" style="font-size:10px;color:#F59E0B"></i>
                        <span :style="{fontSize:'11px',fontWeight:700,color:'#F59E0B'}">Paid</span>
                      </div>
                      <div v-else @click="form.bot_protection = !form.bot_protection"
                        :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:form.bot_protection?'#6D4EE8':C.toggleInactive,position:'relative',transition:'background 0.2s',flexShrink:0}">
                        <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.bot_protection?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                      </div>
                    </div>

                  </div>

                </div>
              </div>
            </div>

            <!-- Sub-step 1 : Info -->
            <div v-show="setupSubStep === 1">

              <!-- VSL info -->
              <template v-if="form.page_type === 'vsl'">
                <h2 :style="{fontSize:'24px',fontWeight:700,color:C.text,marginBottom:'6px'}">Your page info</h2>
                <p :style="{fontSize:'14px',color:C.textMuted,marginBottom:'32px'}">This information shows up on your public page.</p>
                <div :style="{display:'flex',flexDirection:'column',gap:'20px'}">
                  <div>
                    <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">
                      Your name / username <span :style="{fontSize:'11px',fontWeight:400,color:C.textFaint,marginLeft:'6px'}">Shown on your page</span>
                    </label>
                    <input v-model="form.model_name" @input="autoSlug" placeholder="e.g. Sofia, Lea Paris, Chloe…"
                      :style="inputStyle"
                      @focus="(e:any)=>e.target.style.borderColor='#6D4EE8'"
                      @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
                  </div>
                  <div>
                    <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">
                      Your @handle <span :style="{fontSize:'11px',fontWeight:400,color:C.textFaint,marginLeft:'6px'}">Shown under your name</span>
                    </label>
                    <div :style="{display:'flex',alignItems:'center',background:C.inputBg,border:`1px solid ${C.borderInput}`,borderRadius:'10px',overflow:'hidden'}">
                      <span :style="{padding:'10px 12px 10px 14px',fontSize:'14px',color:C.textDim,fontWeight:600,flexShrink:0}">@</span>
                      <input v-model="form.model_handle" placeholder="sofiafrench"
                        :style="{flex:1,background:'transparent',border:'none',outline:'none',padding:'10px 14px 10px 0',fontSize:'14px',color:C.text,fontFamily:'inherit'}" />
                    </div>
                  </div>
                  <div>
                    <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">Your page URL</label>
                    <div :style="{display:'flex',alignItems:'center',background:C.inputBg,border:`1px solid ${C.borderInput}`,borderRadius:'10px',overflow:'hidden'}">
                      <span :style="{padding:'10px 12px 10px 14px',fontSize:'12px',color:C.textFaint,fontWeight:500,flexShrink:0,whiteSpace:'nowrap'}">mysocialvsl.com/</span>
                      <input v-model="form.slug" placeholder="sofia"
                        :style="{flex:1,background:'transparent',border:'none',outline:'none',padding:'10px 14px 10px 0',fontSize:'14px',color:'#A78BFA',fontFamily:'inherit',fontWeight:600}" />
                    </div>
                  </div>
                </div>
              </template>

              <!-- Direct link info -->
              <template v-else>
                <h2 :style="{fontSize:'24px',fontWeight:700,color:C.text,marginBottom:'6px'}">Your direct link</h2>
                <p :style="{fontSize:'14px',color:C.textMuted,marginBottom:'32px'}">Visitors will be redirected immediately.</p>
                <div :style="{display:'flex',flexDirection:'column',gap:'20px'}">
                  <div>
                    <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">Destination URL</label>
                    <input v-model="form.direct_url" type="url" placeholder="https://onlyfans.com/..."
                      :style="inputStyle"
                      @focus="(e:any)=>e.target.style.borderColor='#6D4EE8'"
                      @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
                  </div>
                  <div>
                    <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">Internal name <span :style="{fontSize:'11px',fontWeight:400,color:C.textFaint,marginLeft:'6px'}">For your dashboard</span></label>
                    <input v-model="form.model_name" @input="autoSlug" placeholder="e.g. My OnlyFans"
                      :style="inputStyle"
                      @focus="(e:any)=>e.target.style.borderColor='#6D4EE8'"
                      @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
                  </div>
                  <div>
                    <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">Your page URL</label>
                    <div :style="{display:'flex',alignItems:'center',background:C.inputBg,border:`1px solid ${C.borderInput}`,borderRadius:'10px',overflow:'hidden'}">
                      <span :style="{padding:'10px 12px 10px 14px',fontSize:'12px',color:C.textFaint,fontWeight:500,flexShrink:0,whiteSpace:'nowrap'}">mysocialvsl.com/</span>
                      <input v-model="form.slug" placeholder="sofia"
                        :style="{flex:1,background:'transparent',border:'none',outline:'none',padding:'10px 14px 10px 0',fontSize:'14px',color:'#A78BFA',fontFamily:'inherit',fontWeight:600}" />
                    </div>
                  </div>
                </div>
              </template>

            </div>
          </div>

          <!-- ===== STEP 2 — TEMPLATE ===== -->
          <div v-else-if="activeTab === 'template'" key="template">
            <h2 :style="{fontSize:'24px',fontWeight:700,color:C.text,marginBottom:'6px'}">Choose your template</h2>
            <p :style="{fontSize:'14px',color:C.textMuted,marginBottom:'36px'}">They all keep the video front and center — it just comes down to how many links you need.</p>

            <div class="bld-grid" :style="{display:'grid',gridTemplateColumns:'repeat(3,1fr)',gap:'14px'}">

              <!-- ── Card 1: VSL Pure ── -->
              <div @click="form.template='vsl-classic'"
                :style="{borderRadius:'18px',padding:'20px 16px 16px',cursor:'pointer',transition:'all 0.25s',border:'2px solid',position:'relative',overflow:'hidden',
                  borderColor: form.template==='vsl-classic' ? '#6D4EE8' : C.border,
                  background: form.template==='vsl-classic' ? 'rgba(109,78,232,0.12)' : C.surface}">
                <div v-if="form.template==='vsl-classic'" :style="{position:'absolute',top:'10px',right:'10px',background:'#6D4EE8',borderRadius:'999px',padding:'2px 9px',fontSize:'9px',fontWeight:700,color:'#fff',display:'flex',alignItems:'center',gap:'4px'}">
                  <svg width="7" height="7" viewBox="0 0 10 10"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                  Selected
                </div>
                <!-- Phone mockup -->
                <div :style="{width:'100px',margin:'0 auto 16px',background:'#111',borderRadius:'20px',padding:'4px',boxShadow:'0 16px 40px rgba(0,0,0,0.7)',position:'relative'}">
                  <div :style="{borderRadius:'17px',overflow:'hidden',background:'#000'}">
                    <div :style="{height:'8px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                      <div :style="{width:'18px',height:'2px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                    </div>
                    <div :style="{aspectRatio:'9/16',position:'relative',background:'linear-gradient(160deg,#0d0822 0%,#1a0d35 50%,#0d0822 100%)'}">
                      <!-- Fake video content -->
                      <div :style="{position:'absolute',inset:0,display:'flex',alignItems:'center',justifyContent:'center'}">
                        <div :style="{width:'24px',height:'24px',borderRadius:'50%',border:'1.5px solid rgba(255,255,255,0.2)',display:'flex',alignItems:'center',justifyContent:'center',background:'rgba(255,255,255,0.05)'}">
                          <svg width="8" height="8" viewBox="0 0 9 9" fill="rgba(255,255,255,0.7)"><polygon points="3,2 8,4.5 3,7"/></svg>
                        </div>
                      </div>
                      <!-- Labels top -->
                      <div :style="{position:'absolute',top:'7px',left:'7px',background:'rgba(109,78,232,0.9)',borderRadius:'5px',padding:'2px 5px',fontSize:'6px',fontWeight:800,color:'#fff'}">VSL</div>
                      <!-- Bottom zone -->
                      <div :style="{position:'absolute',bottom:0,left:0,right:0,padding:'28px 8px 8px',background:'linear-gradient(to top,rgba(0,0,0,0.95) 0%,transparent 100%)'}">
                        <div :style="{height:'4px',background:'rgba(255,255,255,0.5)',borderRadius:'2px',marginBottom:'3px',width:'60%'}"></div>
                        <div :style="{height:'3px',background:'rgba(255,255,255,0.2)',borderRadius:'2px',marginBottom:'8px',width:'40%'}"></div>
                        <!-- Single CTA -->
                        <div :style="{height:'14px',background:form.btn_color,borderRadius:'5px',boxShadow:`0 4px 14px ${form.btn_color}60`,display:'flex',alignItems:'center',justifyContent:'center'}">
                          <div :style="{height:'2px',width:'50%',background:'rgba(255,255,255,0.7)',borderRadius:'2px'}"></div>
                        </div>
                      </div>
                    </div>
                    <div :style="{height:'8px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                      <div :style="{width:'28px',height:'2px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                    </div>
                  </div>
                </div>
                <p :style="{fontSize:'14px',fontWeight:700,color:C.text,textAlign:'center',marginBottom:'3px'}">Pure VSL</p>
                <p :style="{fontSize:'10px',color:C.textMuted,textAlign:'center',lineHeight:1.5}">Full-screen video + 1 CTA button. Maximum conversion.</p>
                <div :style="{marginTop:'10px',display:'flex',flexWrap:'wrap',gap:'4px',justifyContent:'center'}">
                  <span v-for="tag in ['1 button','No distraction','Max CTR']" :key="tag" :style="{fontSize:'9px',padding:'2px 7px',borderRadius:'999px',background:'rgba(109,78,232,0.15)',color:'#A78BFA',border:'1px solid rgba(109,78,232,0.25)'}">{{ tag }}</span>
                </div>
              </div>

              <!-- ── Card 2: VSL + Popup ── -->
              <div @click="selectTemplate('vsl-popup')"
                :style="{borderRadius:'18px',padding:'20px 16px 16px',cursor:'pointer',transition:'all 0.25s',border:'2px solid',position:'relative',overflow:'hidden',
                  opacity: isTemplateLocked('vsl-popup') ? 0.55 : 1,
                  borderColor: form.template==='vsl-popup' ? '#6D4EE8' : C.border,
                  background: form.template==='vsl-popup' ? 'rgba(109,78,232,0.12)' : C.surface}">
                <div v-if="form.template==='vsl-popup'" :style="{position:'absolute',top:'10px',right:'10px',background:'#6D4EE8',borderRadius:'999px',padding:'2px 9px',fontSize:'9px',fontWeight:700,color:'#fff',display:'flex',alignItems:'center',gap:'4px'}">
                  <svg width="7" height="7" viewBox="0 0 10 10"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                  Selected
                </div>
                <div v-else-if="isTemplateLocked('vsl-popup')" :style="{position:'absolute',top:'10px',right:'10px',display:'flex',alignItems:'center',gap:'4px',padding:'2px 9px',borderRadius:'999px',background:'rgba(245,158,11,0.12)',border:'1px solid rgba(245,158,11,0.3)'}">
                  <i class="bi bi-lock-fill" style="font-size:9px;color:#F59E0B"></i>
                  <span style="font-size:9px;font-weight:700;color:#F59E0B">Paid</span>
                </div>
                <div :style="{width:'100px',margin:'0 auto 16px',background:'#111',borderRadius:'20px',padding:'4px',boxShadow:'0 16px 40px rgba(0,0,0,0.7)'}">
                  <div :style="{borderRadius:'17px',overflow:'hidden',background:'#000'}">
                    <div :style="{height:'8px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                      <div :style="{width:'18px',height:'2px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                    </div>
                    <div :style="{aspectRatio:'9/16',position:'relative',background:'linear-gradient(160deg,#0d0822 0%,#1a0d35 50%,#0d0822 100%)',display:'flex',alignItems:'center',justifyContent:'center'}">
                      <!-- Blurred bg hint -->
                      <div :style="{position:'absolute',inset:0,background:'rgba(0,0,0,0.55)',backdropFilter:'blur(2px)'}"></div>
                      <!-- Popup card -->
                      <div :style="{position:'relative',zIndex:2,background:'#1a1433',borderRadius:'10px',padding:'8px',width:'68px',border:'1px solid rgba(109,78,232,0.4)',boxShadow:'0 8px 24px rgba(0,0,0,0.6)',textAlign:'center'}">
                        <!-- Close x -->
                        <div :style="{position:'absolute',top:'-5px',right:'-5px',width:'12px',height:'12px',borderRadius:'50%',background:'rgba(255,255,255,0.15)',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'7px',color:'rgba(255,255,255,0.6)'}">×</div>
                        <!-- Emoji/icon -->
                        <div :style="{fontSize:'14px',marginBottom:'4px'}">🔥</div>
                        <!-- Title bar -->
                        <div :style="{height:'4px',background:'rgba(255,255,255,0.5)',borderRadius:'2px',marginBottom:'2px',width:'80%',margin:'0 auto 2px'}"></div>
                        <div :style="{height:'3px',background:'rgba(255,255,255,0.2)',borderRadius:'2px',marginBottom:'6px',width:'60%',margin:'0 auto 6px'}"></div>
                        <!-- CTA -->
                        <div :style="{height:'11px',background:form.btn_color,borderRadius:'4px',boxShadow:`0 3px 10px ${form.btn_color}50`}"></div>
                      </div>
                    </div>
                    <div :style="{height:'8px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                      <div :style="{width:'28px',height:'2px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                    </div>
                  </div>
                </div>
                <p :style="{fontSize:'14px',fontWeight:700,color:C.text,textAlign:'center',marginBottom:'3px'}">VSL + Popup</p>
                <p :style="{fontSize:'10px',color:C.textMuted,textAlign:'center',lineHeight:1.5}">Centered popup after X sec. with title, text and CTA.</p>
                <div :style="{marginTop:'10px',display:'flex',flexWrap:'wrap',gap:'4px',justifyContent:'center'}">
                  <span v-for="tag in ['Timed popup','Custom text','Urgency']" :key="tag" :style="{fontSize:'9px',padding:'2px 7px',borderRadius:'999px',background:'rgba(245,158,11,0.12)',color:'#F59E0B',border:'1px solid rgba(245,158,11,0.2)'}">{{ tag }}</span>
                </div>
              </div>

              <!-- ── Card 3: VSL + Bandeau ── -->
              <div @click="selectTemplate('vsl-bandeau')"
                :style="{borderRadius:'18px',padding:'20px 16px 16px',cursor:'pointer',transition:'all 0.25s',border:'2px solid',position:'relative',overflow:'hidden',
                  opacity: isTemplateLocked('vsl-bandeau') ? 0.55 : 1,
                  borderColor: form.template==='vsl-bandeau' ? '#6D4EE8' : C.border,
                  background: form.template==='vsl-bandeau' ? 'rgba(109,78,232,0.12)' : C.surface}">
                <div v-if="form.template==='vsl-bandeau'" :style="{position:'absolute',top:'10px',right:'10px',background:'#6D4EE8',borderRadius:'999px',padding:'2px 9px',fontSize:'9px',fontWeight:700,color:'#fff',display:'flex',alignItems:'center',gap:'4px'}">
                  <svg width="7" height="7" viewBox="0 0 10 10"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                  Selected
                </div>
                <div v-else-if="isTemplateLocked('vsl-bandeau')" :style="{position:'absolute',top:'10px',right:'10px',display:'flex',alignItems:'center',gap:'4px',padding:'2px 9px',borderRadius:'999px',background:'rgba(245,158,11,0.12)',border:'1px solid rgba(245,158,11,0.3)'}">
                  <i class="bi bi-lock-fill" style="font-size:9px;color:#F59E0B"></i>
                  <span style="font-size:9px;font-weight:700;color:#F59E0B">Paid</span>
                </div>
                <div :style="{width:'100px',margin:'0 auto 16px',background:'#111',borderRadius:'20px',padding:'4px',boxShadow:'0 16px 40px rgba(0,0,0,0.7)'}">
                  <div :style="{borderRadius:'17px',overflow:'hidden',background:'#000'}">
                    <div :style="{height:'8px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                      <div :style="{width:'18px',height:'2px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                    </div>
                    <div :style="{aspectRatio:'9/16',position:'relative',background:'linear-gradient(160deg,#0d0822 0%,#1a0d35 50%,#0d0822 100%)'}">
                      <div :style="{position:'absolute',inset:0,display:'flex',alignItems:'center',justifyContent:'center'}">
                        <div :style="{width:'24px',height:'24px',borderRadius:'50%',border:'1.5px solid rgba(255,255,255,0.2)',display:'flex',alignItems:'center',justifyContent:'center',background:'rgba(255,255,255,0.05)'}">
                          <svg width="8" height="8" viewBox="0 0 9 9" fill="rgba(255,255,255,0.7)"><polygon points="3,2 8,4.5 3,7"/></svg>
                        </div>
                      </div>
                      <!-- Bottom overlay + CTA + drawer handle -->
                      <div :style="{position:'absolute',bottom:0,left:0,right:0,padding:'28px 8px 8px',background:'linear-gradient(to top,rgba(0,0,0,0.95) 0%,transparent 100%)'}">
                        <div :style="{height:'4px',background:'rgba(255,255,255,0.5)',borderRadius:'2px',marginBottom:'3px',width:'60%'}"></div>
                        <div :style="{height:'3px',background:'rgba(255,255,255,0.2)',borderRadius:'2px',marginBottom:'5px',width:'40%'}"></div>
                        <!-- Drawer handle -->
                        <div :style="{display:'flex',justifyContent:'center',marginBottom:'4px'}">
                          <div :style="{width:'16px',height:'2px',background:'rgba(255,255,255,0.3)',borderRadius:'999px'}"></div>
                        </div>
                        <div :style="{height:'14px',background:form.btn_color,borderRadius:'5px',boxShadow:`0 4px 14px ${form.btn_color}60`,display:'flex',alignItems:'center',justifyContent:'center'}">
                          <div :style="{height:'2px',width:'50%',background:'rgba(255,255,255,0.7)',borderRadius:'2px'}"></div>
                        </div>
                      </div>
                    </div>
                    <!-- Drawer peeking from bottom -->
                    <div :style="{height:'18px',background:'#1a1733',display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',gap:'3px',borderTop:'1px solid rgba(255,255,255,0.08)'}">
                      <div :style="{width:'14px',height:'2px',background:'rgba(255,255,255,0.25)',borderRadius:'999px'}"></div>
                      <div :style="{display:'flex',gap:'3px'}">
                        <div :style="{width:'18px',height:'2px',background:'rgba(255,255,255,0.08)',borderRadius:'999px'}"></div>
                        <div :style="{width:'14px',height:'2px',background:'rgba(255,255,255,0.06)',borderRadius:'999px'}"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <p :style="{fontSize:'14px',fontWeight:700,color:C.text,textAlign:'center',marginBottom:'3px'}">VSL + Banner</p>
                <p :style="{fontSize:'10px',color:C.textMuted,textAlign:'center',lineHeight:1.5}">1 CTA + a link drawer at the bottom. Perfect for multi-platform.</p>
                <div :style="{marginTop:'10px',display:'flex',flexWrap:'wrap',gap:'4px',justifyContent:'center'}">
                  <span v-for="tag in ['Multi-link','Drawer','Swipe up']" :key="tag" :style="{fontSize:'9px',padding:'2px 7px',borderRadius:'999px',background:'rgba(16,185,129,0.1)',color:'#10B981',border:'1px solid rgba(16,185,129,0.2)'}">{{ tag }}</span>
                </div>
              </div>

            </div>
          </div>

          <!-- ===== STEP 3 — VSL ===== -->
          <div v-else-if="activeTab === 'vsl'" key="vsl">
            <h2 :style="{fontSize:'24px',fontWeight:700,color:C.text,marginBottom:'6px'}">Your VSL video</h2>
            <p :style="{fontSize:'14px',color:C.textMuted,marginBottom:'36px'}">The video that sells. Upload it vertical (9:16) for a perfect look.</p>

            <VideoUpload v-model="form.video_url" @uploading="uploadingVideo = $event" />

            <!-- ── "How to film a VSL that converts" walkthrough (manager-facing helper) ── -->
            <div :style="{marginTop:'24px',background:C.surface2,border:`1px solid ${C.border}`,borderRadius:'14px',overflow:'hidden'}">
              <!-- Header (click to fold) -->
              <div @click="guideOpen = !guideOpen"
                :style="{display:'flex',alignItems:'center',gap:'10px',padding:'14px 18px',cursor:'pointer',userSelect:'none'}">
                <div :style="{width:'28px',height:'28px',borderRadius:'8px',background:'rgba(109,78,232,0.14)',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                  <i class="bi bi-camera-reels" style="color:#A78BFA;font-size:14px"></i>
                </div>
                <div :style="{flex:1,minWidth:0}">
                  <p :style="{fontSize:'13px',fontWeight:700,color:C.text,margin:0}">Not sure what to film?</p>
                  <p :style="{fontSize:'11px',color:C.textDim,margin:'2px 0 0'}">A 30-second guide to a VSL that converts.</p>
                </div>
                <i class="bi bi-chevron-down" :style="{color:C.textMuted,fontSize:'13px',transition:'transform 0.2s',transform:guideOpen?'rotate(180deg)':'none'}"></i>
              </div>

              <!-- Body -->
              <div v-if="guideOpen" :style="{padding:'0 18px 18px'}">
                <!-- Tabs -->
                <div :style="{display:'flex',gap:'6px',marginBottom:'16px'}">
                  <div v-for="(t,i) in ['① The right format','② Starter script','③ Ask your model']" :key="i"
                    @click="guideStep = i"
                    :style="{flex:1,textAlign:'center',padding:'8px 6px',borderRadius:'9px',fontSize:'11px',fontWeight:600,cursor:'pointer',transition:'all 0.15s',
                      background: guideStep===i ? '#6D4EE8' : C.pillBg,
                      color: guideStep===i ? '#fff' : C.pillText}">{{ t }}</div>
                </div>

                <!-- ① The right format -->
                <div v-if="guideStep===0" :style="{display:'flex',flexDirection:'column',gap:'9px'}">
                  <div v-for="tip in ['Shoot vertical (9:16) — full-screen, portrait.','Hook in the first 3 seconds: her face + a teasing line.','Keep it short — 15 to 40 seconds is the sweet spot.','Tease, never show everything — curiosity is what makes them click.','End by saying it out loud: “tap the button below”.']" :key="tip"
                    :style="{display:'flex',gap:'8px',fontSize:'12.5px',color:C.text2,lineHeight:1.5}">
                    <i class="bi bi-check-circle-fill" style="color:#A78BFA;font-size:12px;margin-top:3px;flex-shrink:0"></i>
                    <span>{{ tip }}</span>
                  </div>
                </div>

                <!-- ② Starter script -->
                <div v-else-if="guideStep===1">
                  <p :style="{fontSize:'12px',color:C.textDim,marginBottom:'10px',lineHeight:1.5}">A starter script to hand to your model — adapt it to her voice.</p>
                  <div :style="{background:C.bg,border:`1px solid ${C.border}`,borderRadius:'10px',padding:'14px',fontSize:'12.5px',color:C.text2,lineHeight:1.6,whiteSpace:'pre-line'}">{{ guideScript }}</div>
                  <button @click="copyGuideScript"
                    :style="{marginTop:'10px',display:'inline-flex',alignItems:'center',gap:'6px',padding:'8px 16px',borderRadius:'9px',border:'none',background:'#6D4EE8',color:'#fff',fontSize:'12px',fontWeight:600,cursor:'pointer',fontFamily:'inherit'}">
                    <i :class="copiedScript ? 'bi bi-check2' : 'bi bi-clipboard'" style="font-size:13px"></i>
                    {{ copiedScript ? 'Copied!' : 'Copy script' }}
                  </button>
                </div>

                <!-- ③ Ask your model -->
                <div v-else :style="{display:'flex',flexDirection:'column',gap:'9px'}">
                  <div v-for="ask in ['A 10–20s selfie-style clip, filmed vertical.','Good lighting + eye contact straight into the camera.','She says the hook AND the call-to-action out loud.','A few seconds of b-roll you can cut to (optional).','Re-record until the first 3 seconds truly grab attention.']" :key="ask"
                    :style="{display:'flex',gap:'8px',fontSize:'12.5px',color:C.text2,lineHeight:1.5}">
                    <i class="bi bi-arrow-right-short" style="color:#A78BFA;font-size:16px;flex-shrink:0"></i>
                    <span>{{ ask }}</span>
                  </div>
                </div>

                <!-- Nav -->
                <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',marginTop:'16px'}">
                  <button v-if="guideStep>0" @click="guideStep--"
                    :style="{padding:'7px 14px',borderRadius:'8px',border:`1px solid ${C.border}`,background:'transparent',color:C.text2,fontSize:'12px',fontWeight:600,cursor:'pointer',fontFamily:'inherit'}">← Back</button>
                  <span v-else></span>
                  <button v-if="guideStep<2" @click="guideStep++"
                    :style="{padding:'7px 14px',borderRadius:'8px',border:'none',background:C.pillBg,color:C.text,fontSize:'12px',fontWeight:600,cursor:'pointer',fontFamily:'inherit'}">Next →</button>
                  <RouterLink v-else to="/dashboard/guide" :style="{fontSize:'12px',color:'#A78BFA',fontWeight:600,textDecoration:'none'}">See the full guide →</RouterLink>
                </div>
              </div>
            </div>
          </div>

          <!-- ===== STEP 4 — CTA ===== -->
          <div v-else-if="activeTab === 'cta'" key="cta">

            <div>
            <h2 :style="{fontSize:'24px',fontWeight:700,color:C.text,marginBottom:'6px'}">Your CTA button</h2>
            <p :style="{fontSize:'14px',color:C.textMuted,marginBottom:'28px'}">One button, one action.</p>

            <div :style="{display:'flex',flexDirection:'column',gap:'20px'}">

              <!-- CTA URL -->
              <div>
                <label :style="{display:'flex',alignItems:'center',gap:'8px',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">
                  Destination link
                  <span :style="{fontSize:'10px',fontWeight:600,color:C.textMuted,background:C.surface,border:`1px solid ${C.border}`,borderRadius:'999px',padding:'1px 8px'}">optional</span>
                </label>
                <input v-model="form.cta_url" type="url" placeholder="https://onlyfans.com/your-username/..."
                  :style="inputStyle"
                  @focus="(e:any)=>e.target.style.borderColor='#6D4EE8'"
                  @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
              </div>

              <!-- CTA Label (text type only) -->
              <div v-if="form.cta_type==='text'">
                <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'8px'}">
                  Button text
                </label>
                <input v-model="form.btn_label" placeholder="e.g. 🔓 My OnlyFans — Private access"
                  :style="inputStyle"
                  @focus="(e:any)=>e.target.style.borderColor='#6D4EE8'"
                  @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
              </div>

              <!-- CTA Color (text type only) -->
              <div v-if="form.cta_type==='text'">
                <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'10px'}">
                  Button color
                </label>
                <div :style="{display:'flex',gap:'8px',flexWrap:'wrap',alignItems:'center'}">
                  <div v-for="preset in colorPresets" :key="preset.value"
                    @click="form.btn_color = preset.value"
                    :style="{
                      display:'flex',alignItems:'center',gap:'6px',padding:'7px 14px',borderRadius:'999px',cursor:'pointer',
                      border:'2px solid',transition:'all 0.15s',fontSize:'12px',fontWeight:600,
                      borderColor: form.btn_color === preset.value ? preset.value : C.borderInput,
                      background: form.btn_color === preset.value ? preset.value + '22' : C.surface2,
                      color: form.btn_color === preset.value ? '#fff' : C.text2,
                    }">
                    <div :style="{width:'10px',height:'10px',borderRadius:'50%',background:preset.value,flexShrink:0}"></div>
                    {{ preset.label }}
                  </div>
                  <div :style="{display:'flex',alignItems:'center',gap:'6px',padding:'7px 14px',borderRadius:'999px',border:`1px solid ${C.borderInput}`,background:C.surface2,cursor:'pointer'}">
                    <input v-model="form.btn_color" type="color"
                      :style="{width:'16px',height:'16px',border:'none',background:'none',padding:0,cursor:'pointer',borderRadius:'50%'}" />
                    <span :style="{fontSize:'12px',color:C.text2,fontWeight:600}">Other</span>
                  </div>
                </div>
              </div> <!-- end color/v-if-text div -->

              <!-- ── Popup config (vsl-popup only) ── -->
              <div v-if="form.template === 'vsl-popup'"
                :style="{background:'rgba(245,158,11,0.06)',border:'1px solid rgba(245,158,11,0.18)',borderRadius:'14px',padding:'20px',display:'flex',flexDirection:'column',gap:'16px'}">
                <div style="display:flex;align-items:center;gap:8px">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 12h6M12 9v6"/></svg>
                  <p :style="{fontSize:'13px',fontWeight:700,color:'#fff',margin:0}">Popup settings</p>
                  <span :style="{fontSize:'10px',color:'#F59E0B',fontWeight:600,background:'rgba(245,158,11,0.15)',padding:'2px 8px',borderRadius:'999px',marginLeft:'auto'}">appears after {{ form.popup_delay }}s</span>
                </div>
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:600,color:C.text2,marginBottom:'6px',textTransform:'uppercase',letterSpacing:'0.07em'}">Appearance delay</label>
                  <div :style="{display:'flex',alignItems:'center',gap:'10px'}">
                    <input v-model.number="form.popup_delay" type="range" min="1" max="45" step="1"
                      :style="{flex:1,accentColor:'#F59E0B'}" />
                    <span :style="{fontSize:'14px',fontWeight:700,color:C.text,width:'50px',textAlign:'right'}">{{ form.popup_delay }}s</span>
                  </div>
                  <p :style="{fontSize:'11px',color:C.textFaint,marginTop:'4px'}">The first few seconds convert best — 3s is the sweet spot.</p>
                </div>
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:600,color:C.text2,marginBottom:'6px',textTransform:'uppercase',letterSpacing:'0.07em'}">Popup title</label>
                  <input v-model="form.popup_title" placeholder="e.g. Join me in private 🔥"
                    :style="inputStyle"
                    @focus="(e:any)=>e.target.style.borderColor='#F59E0B'"
                    @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
                </div>
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:600,color:C.text2,marginBottom:'6px',textTransform:'uppercase',letterSpacing:'0.07em'}">Subtitle / description</label>
                  <input v-model="form.popup_subtitle" placeholder="e.g. Exclusive content available now"
                    :style="inputStyle"
                    @focus="(e:any)=>e.target.style.borderColor='#F59E0B'"
                    @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
                </div>
                <div :style="{background:C.surface2,borderRadius:'10px',padding:'12px',fontSize:'12px',color:C.textMuted,lineHeight:1.6}">
                  <i class="bi bi-lightbulb"></i> The popup button uses the same text and color as your main CTA below.
                </div>
              </div>

              <!-- Extra links (bandeau uniquement) -->
              <div v-if="form.template === 'vsl-bandeau'">
                <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:C.text2,marginBottom:'4px'}">
                  Banner links
                </label>
                <p :style="{fontSize:'12px',color:C.textDim,marginBottom:'12px'}">These links show up in the drawer that slides up from the bottom.</p>

                <!-- Quick-add platforms — iOS-style square icons -->
                <div :style="{display:'flex',gap:'10px',flexWrap:'wrap',marginBottom:'16px'}">
                  <div v-for="p in bandeauPlatforms" :key="p.id"
                    @click="addBandeauLink(p)"
                    :title="p.name"
                    :style="{
                      display:'flex',flexDirection:'column',alignItems:'center',gap:'5px',
                      cursor:'pointer',transition:'all 0.18s',
                    }">
                    <div :style="{
                      width:'54px',height:'54px',borderRadius:'16px',
                      background: form.extra_links.some(l=>l.type===p.id) ? p.color : (theme.dark ? 'rgba(255,255,255,0.09)' : '#fff'),
                      border: form.extra_links.some(l=>l.type===p.id) ? '2px solid ' + p.color : `2px solid ${C.border}`,
                      display:'flex',alignItems:'center',justifyContent:'center',
                      boxShadow: form.extra_links.some(l=>l.type===p.id) ? `0 4px 16px ${p.color}44` : '0 2px 8px rgba(0,0,0,0.15)',
                      transition:'all 0.18s',
                      flexShrink:0,
                    }">
                      <span v-html="p.svgLg" style="display:flex;align-items:center"></span>
                    </div>
                    <span :style="{fontSize:'10px',fontWeight:600,color:C.textDim,letterSpacing:'0.01em'}">{{ p.name }}</span>
                  </div>
                </div>

                <!-- Extra link rows -->
                <div :style="{display:'flex',flexDirection:'column',gap:'8px'}">
                  <div v-for="(link, i) in form.extra_links" :key="i"
                    :style="{display:'flex',gap:'8px',alignItems:'center'}">
                    <div :style="{width:'8px',height:'8px',borderRadius:'50%',background:link.color||'#6D4EE8',flexShrink:0}"></div>
                    <input v-model="link.label" placeholder="Label"
                      :style="{...inputStyle,width:'130px',flex:'none',padding:'8px 10px',fontSize:'12px'}"
                      @focus="(e:any)=>e.target.style.borderColor='#6D4EE8'"
                      @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
                    <input v-model="link.url" type="url" placeholder="https://..."
                      :style="{...inputStyle,flex:1,padding:'8px 10px',fontSize:'12px'}"
                      @focus="(e:any)=>e.target.style.borderColor='#6D4EE8'"
                      @blur="(e:any)=>e.target.style.borderColor=C.borderInput" />
                    <button @click="form.extra_links.splice(i,1)"
                      :style="{background:'none',border:'none',cursor:'pointer',color:C.textFaint,padding:'4px',display:'flex',alignItems:'center',flexShrink:0}"
                      @mouseover="(e:any)=>e.currentTarget.style.color='#F87171'"
                      @mouseout="(e:any)=>e.currentTarget.style.color=C.textFaint">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                  </div>
                </div>

                <!-- Add custom link -->
                <button @click="form.extra_links.push({label:'My link',url:'',color:'#6D4EE8'})"
                  :style="{marginTop:'10px',display:'flex',alignItems:'center',gap:'6px',padding:'7px 14px',border:`1px dashed ${C.border}`,borderRadius:'8px',background:'transparent',color:C.textMuted,fontSize:'12px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',width:'100%',justifyContent:'center'}">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                  Add a link
                </button>
              </div>

              <!-- Toggles -->
              <div :style="{display:'flex',flexDirection:'column',gap:'10px'}">

                <!-- Online status -->
                <div :style="{background:C.surface2,border:`1px solid ${C.border}`,borderRadius:'12px',padding:'16px',display:'flex',alignItems:'center',justifyContent:'space-between',gap:'16px'}">
                  <div>
                    <p :style="{fontSize:'13px',fontWeight:600,color:C.text,marginBottom:'3px'}">
                      <span :style="{display:'inline-block',width:'8px',height:'8px',borderRadius:'50%',background:'#10B981',marginRight:'7px'}"></span>
                      Online now
                    </p>
                    <p :style="{fontSize:'12px',color:C.textDim,lineHeight:1.5}">Shows a green badge on your page — boosts clicks.</p>
                  </div>
                  <div @click="form.online_status = !form.online_status"
                    :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:form.online_status?'#10b981':C.toggleInactive,position:'relative',transition:'background 0.2s',flexShrink:0}">
                    <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.online_status?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                  </div>
                </div>

                <!-- Age gate -->
                <div :style="{background:C.surface2,border:`1px solid ${C.border}`,borderRadius:'12px',padding:'16px',display:'flex',alignItems:'center',justifyContent:'space-between',gap:'16px'}">
                  <div>
                    <p :style="{fontSize:'13px',fontWeight:600,color:C.text,marginBottom:'3px'}">
                      <i class="bi bi-shield-x" style="color:#F87171;margin-right:7px"></i>
                      18+ age verification
                    </p>
                    <p :style="{fontSize:'12px',color:C.textDim,lineHeight:1.5}">A confirmation modal appears before accessing the link.</p>
                  </div>
                  <div @click="form.age_gate = !form.age_gate"
                    :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:form.age_gate?'#6D4EE8':C.toggleInactive,position:'relative',transition:'background 0.2s',flexShrink:0}">
                    <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.age_gate?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                  </div>
                </div>

              </div>
            </div>
            </div>

          </div>

          </Transition>

        </div>

        <!-- RIGHT: VSL Preview -->
        <div class="bld-preview" :style="{width:'380px',background:C.panel,borderLeft:`1px solid ${C.borderLight}`,display:'flex',flexDirection:'column',alignItems:'center',padding:'24px 20px',flexShrink:0,overflowY:'auto'}">

          <p :style="{fontSize:'11px',fontWeight:600,color:C.textMuted,letterSpacing:'0.1em',textTransform:'uppercase',marginBottom:'20px',alignSelf:'flex-start'}">Live preview</p>

          <!-- Phone outer shell -->
          <div :style="{width:'220px',background:'#1a1a1a',borderRadius:'40px',padding:'8px',boxShadow:'0 0 0 1px #2a2a2a,0 32px 80px rgba(0,0,0,0.7),0 0 60px rgba(109,78,232,0.15)',flexShrink:0}">
            <div :style="{borderRadius:'33px',overflow:'hidden',background:'#000'}">

              <!-- Status bar -->
              <div :style="{height:'24px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                <div :style="{width:'48px',height:'6px',background:'#1a1a1a',borderRadius:'999px'}"></div>
              </div>

              <!-- VSL container: dark bg, 9:16 inside phone -->
              <div :style="{background:'#0d0d0d',position:'relative'}">

                <!-- Video area placeholder -->
                <div :style="{aspectRatio:'9/16',width:'100%',position:'relative',overflow:'hidden'}">

                  <!-- Video: real preview when uploaded, placeholder otherwise -->
                  <video v-if="form.video_url"
                    :src="form.video_url"
                    :style="{position:'absolute',inset:0,width:'100%',height:'100%',objectFit:'cover'}"
                    autoplay muted loop playsinline>
                  </video>
                  <div v-else :style="{position:'absolute',inset:0,display:'flex',alignItems:'center',justifyContent:'center',background:'#0d0d0d'}">
                    <div :style="{textAlign:'center',opacity:0.2}">
                      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                  </div>

                  <!-- VSL badge top-left -->
                  <div :style="{position:'absolute',top:'8px',left:'8px',background:'rgba(109,78,232,0.9)',borderRadius:'999px',padding:'2px 7px',display:'flex',alignItems:'center',gap:'3px',zIndex:5}">
                    <div :style="{width:'4px',height:'4px',borderRadius:'50%',background:'#fff',animation:'pulse-dot 1.4s ease infinite'}"></div>
                    <span :style="{fontSize:'7px',fontWeight:800,color:'#fff',letterSpacing:'0.05em'}">VSL</span>
                  </div>

                  <!-- Online badge top-right -->
                  <div v-if="form.online_status"
                    :style="{position:'absolute',top:'8px',right:'8px',background:'rgba(16,185,129,0.15)',border:'1px solid rgba(16,185,129,0.4)',borderRadius:'999px',padding:'2px 7px',display:'flex',alignItems:'center',gap:'3px',zIndex:5}">
                    <div :style="{width:'4px',height:'4px',borderRadius:'50%',background:'#10B981'}"></div>
                    <span :style="{fontSize:'7px',fontWeight:600,color:'#10B981'}">Online</span>
                  </div>

                  <!-- Popup overlay (vsl-popup mode) -->
                  <div v-if="form.template === 'vsl-popup'"
                    :style="{position:'absolute',inset:0,background:'rgba(0,0,0,0.6)',zIndex:10,display:'flex',alignItems:'center',justifyContent:'center',padding:'12px'}">
                    <div :style="{background:'#12102a',borderRadius:'14px',padding:'14px 12px',width:'100%',maxWidth:'150px',border:'1px solid rgba(109,78,232,0.35)',boxShadow:'0 16px 40px rgba(0,0,0,0.7)',position:'relative',textAlign:'center'}">
                      <!-- X close -->
                      <div :style="{position:'absolute',top:'6px',right:'7px',width:'14px',height:'14px',borderRadius:'50%',background:'rgba(255,255,255,0.1)',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'9px',color:'rgba(255,255,255,0.5)',cursor:'pointer',fontWeight:700}">×</div>
                      <!-- Emoji glow -->
                      <div :style="{fontSize:'18px',marginBottom:'6px'}">🔥</div>
                      <!-- Title -->
                      <p :style="{fontSize:'8px',fontWeight:800,color:'#fff',margin:'0 0 3px',letterSpacing:'-0.2px',lineHeight:1.3}">{{ form.popup_title || 'Join me in private 🔥' }}</p>
                      <!-- Subtitle -->
                      <p :style="{fontSize:'6.5px',color:'rgba(255,255,255,0.45)',margin:'0 0 10px',lineHeight:1.5}">{{ form.popup_subtitle || 'Exclusive content available now' }}</p>
                      <!-- CTA -->
                      <div :style="{padding:'7px 8px',borderRadius:'7px',background:form.btn_color,fontSize:'7px',fontWeight:800,color:'#fff',boxShadow:`0 4px 14px ${form.btn_color}55`,letterSpacing:'0.02em'}">
                        {{ form.btn_label || '🔓 My OnlyFans — Private access' }}
                      </div>
                      <!-- Delay badge -->
                      <div :style="{marginTop:'7px',fontSize:'6px',color:'rgba(255,255,255,0.3)'}">Appears after {{ form.popup_delay }}s</div>
                    </div>
                  </div>

                  <!-- Bottom overlay (hidden behind popup in popup mode) -->
                  <div :style="{position:'absolute',bottom:0,left:0,right:0,padding:'48px 10px 12px',background:'linear-gradient(to top, rgba(0,0,0,0.82) 0%, rgba(0,0,0,0.3) 55%, transparent 100%)',zIndex:5,opacity:form.template==='vsl-popup'?0.3:1}">
                    <!-- Creator info -->
                    <div :style="{marginBottom:'8px'}">
                      <p :style="{fontSize:'11px',fontWeight:700,color:'#fff',letterSpacing:'-0.2px',textShadow:'0 1px 6px rgba(0,0,0,0.6)'}">{{ form.model_name || 'Your name' }}</p>
                      <p :style="{fontSize:'8px',color:'rgba(255,255,255,0.62)',textShadow:'0 1px 4px rgba(0,0,0,0.6)'}">@{{ form.model_handle || 'handle' }}</p>
                    </div>

                    <!-- Bandeau handle (visible uniquement en mode bandeau) -->
                    <div v-if="form.template === 'vsl-bandeau'"
                      :style="{display:'flex',flexDirection:'column',alignItems:'center',gap:'3px',paddingBottom:'4px',cursor:'pointer'}">
                      <div :style="{width:'20px',height:'2px',background:'rgba(255,255,255,0.3)',borderRadius:'999px'}"></div>
                      <span :style="{fontSize:'6px',color:'rgba(255,255,255,0.4)',fontWeight:600}">{{ form.extra_links.length > 0 ? `+${form.extra_links.length} links` : 'Banner' }}</span>
                    </div>

                    <!-- CTA button with bounce animation (hidden in popup mode) -->
                    <div v-if="form.template !== 'vsl-popup'" :style="{
                      width:'100%',padding:'9px 10px',borderRadius:'10px',
                      textAlign:'center',fontSize:'9px',fontWeight:700,color:'#fff',
                      background:form.btn_color,cursor:'pointer',
                      animation:'preview-bounce 2.6s cubic-bezier(0.45,0,0.55,1) infinite',
                      boxShadow:`0 4px 18px ${form.btn_color}60`,
                    }">{{ form.btn_label || '🔓 My OnlyFans — Private access' }}</div>
                  </div>
                </div>
              </div>

              <!-- Home bar -->
              <div :style="{height:'20px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                <div :style="{width:'60px',height:'3px',background:'#1a1a1a',borderRadius:'999px'}"></div>
              </div>
            </div>
          </div>

          <!-- Page URL preview -->
          <div :style="{marginTop:'16px',display:'flex',alignItems:'center',gap:'6px',background:C.surface2,borderRadius:'999px',padding:'6px 14px'}">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" :stroke="C.textDim" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
            <span :style="{fontSize:'11px',color:C.textMuted}">mysocialvsl.com/<span :style="{color:'#A78BFA',fontWeight:600}">{{ form.slug || '…' }}</span></span>
          </div>

        </div>
      </div>

      <!-- BOTTOM BAR -->
      <div class="bld-bottombar" :style="{height:'72px',background:C.bg,borderTop:`1px solid ${C.borderLight}`,display:'flex',alignItems:'center',justifyContent:'space-between',padding:'0 40px',flexShrink:0}">
        <button v-if="canGoBack" @click="prevStep"
          :style="{padding:'10px 22px',border:`1px solid ${C.cancelBorder}`,borderRadius:'10px',background:'transparent',color:C.text,fontSize:'14px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',display:'flex',alignItems:'center',gap:'6px'}"
          @mouseover="(e:any)=>e.currentTarget.style.background=C.surface2"
          @mouseout="(e:any)=>e.currentTarget.style.background='transparent'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
          Retour
        </button>
        <div v-else></div>

        <div :style="{display:'flex',gap:'8px',alignItems:'center'}">
          <div v-for="(step, i) in steps" :key="step.id" @click="goToStep(i)"
            :style="{
              width: i===currentStepIndex ? '28px' : '12px',
              height: '12px',
              borderRadius:'999px',
              transition:'all 0.22s cubic-bezier(0.4,0,0.2,1)',
              cursor:'pointer',
              background: i===currentStepIndex ? '#6D4EE8' : i<currentStepIndex ? 'rgba(109,78,232,0.55)' : C.stepDotInactive,
              transform: i===currentStepIndex ? 'scale(1)' : 'scale(0.9)',
            }">
          </div>
        </div>

        <button @click="nextStep" :disabled="saving || uploadingVideo || !isStepValid"
          :style="{padding:'10px 28px',background:'#6D4EE8',color:'#fff',border:'none',borderRadius:'10px',fontSize:'14px',fontWeight:700,cursor:(saving||uploadingVideo||!isStepValid)?'not-allowed':'pointer',fontFamily:'inherit',opacity:(saving||uploadingVideo||!isStepValid)?0.45:1,transition:'opacity 0.2s',display:'flex',alignItems:'center',gap:'6px'}"
          :title="uploadingVideo ? 'Wait for the video upload to finish' : !isStepValid ? 'Fill in the required fields' : ''">
          <i v-if="uploadingVideo" class="bi bi-hourglass-split" style="animation:spin 1s linear infinite"></i>
          <template v-else-if="!isLastAction">
            Continue
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
          </template>
          <template v-else>
            <span>{{ saving ? (isEditMode ? 'Updating…' : 'Publishing…') : (isEditMode ? 'Update' : 'Publish') }}</span>
            <i v-if="!saving" :class="isEditMode ? 'bi bi-cloud-check' : 'bi bi-rocket-takeoff'"></i>
          </template>
        </button>
      </div>

    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/lib/axios'
import VideoUpload from '@/components/VideoUpload.vue'
import DashboardLayout from '@/components/DashboardLayout.vue'
import DeeplinkDemo from '@/components/DeeplinkDemo.vue'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route  = useRoute()
const theme  = useThemeStore()
const auth   = useAuthStore()
// Deeplink bypass is a paid feature — gate the toggle for free-plan owners.
const isFreePlan = computed(() => { const p = (auth.user as any)?.plan; return !p || p === 'free' })
// Popup/Drawer templates are paid-only — free users see the cards but can't select them.
const isPaidUser = computed(() => {
  const p = (auth.user as any)?.plan
  return p === 'pro' || p === 'agency' || !!(auth.user as any)?.is_admin
})
const PAID_TEMPLATES = ['vsl-popup', 'vsl-bandeau']
const isTemplateLocked = (tpl: string) => PAID_TEMPLATES.includes(tpl) && !isPaidUser.value
const selectTemplate = (tpl: string) => {
  if (isTemplateLocked(tpl)) { router.push('/billing'); return }
  form.value.template = tpl
}

const editPageId = computed(() => route.params.id as string | undefined)
const isEditMode = computed(() => !!editPageId.value)

const C = computed(() => theme.dark ? {
  bg: '#0a0812',
  panel: 'rgba(0,0,0,0.3)',
  border: 'rgba(255,255,255,0.08)',
  borderLight: 'rgba(255,255,255,0.06)',
  borderInput: 'rgba(255,255,255,0.1)',
  text: '#fff',
  text2: 'rgba(255,255,255,0.6)',
  textMuted: 'rgba(255,255,255,0.4)',
  textDim: 'rgba(255,255,255,0.3)',
  textFaint: 'rgba(255,255,255,0.25)',
  textVeryFaint: 'rgba(255,255,255,0.2)',
  surface: 'rgba(255,255,255,0.03)',
  surface2: 'rgba(255,255,255,0.04)',
  inputBg: 'rgba(255,255,255,0.05)',
  pillBg: 'rgba(255,255,255,0.06)',
  pillText: 'rgba(255,255,255,0.3)',
  cancelBorder: 'rgba(255,255,255,0.12)',
  cancelColor: 'rgba(255,255,255,0.5)',
  stepRail: 'rgba(255,255,255,0.1)',
  stepInactiveBg: 'rgba(255,255,255,0.08)',
  stepInactiveText: 'rgba(255,255,255,0.3)',
  stepLabelActive: '#A78BFA',
  stepLabelDone: 'rgba(255,255,255,0.5)',
  stepLabelInactive: 'rgba(255,255,255,0.2)',
  stepDotInactive: 'rgba(255,255,255,0.15)',
  toggleInactive: 'rgba(255,255,255,0.15)',
} : {
  bg: '#F9FAFB',
  panel: '#F3F4F6',
  border: '#E5E7EB',
  borderLight: '#F3F4F6',
  borderInput: '#D1D5DB',
  text: '#111827',
  text2: '#4B5563',
  textMuted: '#6B7280',
  textDim: '#9CA3AF',
  textFaint: '#D1D5DB',
  textVeryFaint: '#E5E7EB',
  surface: '#fff',
  surface2: '#F9FAFB',
  inputBg: '#fff',
  pillBg: '#F3F4F6',
  pillText: '#6B7280',
  cancelBorder: '#E5E7EB',
  cancelColor: '#6B7280',
  stepRail: '#E5E7EB',
  stepInactiveBg: '#E5E7EB',
  stepInactiveText: '#9CA3AF',
  stepLabelActive: '#7C3AED',
  stepLabelDone: '#6B7280',
  stepLabelInactive: '#D1D5DB',
  stepDotInactive: '#E5E7EB',
  toggleInactive: '#D1D5DB',
})

const saving = ref(false)
const error = ref('')
const successMsg = ref(false)
const activeTab = ref('setup')
const uploadingVideo = ref(false)
const setupSubStep = ref(0) // 0=type, 1=infos
const showAdvanced = ref(false)
const advancedTab  = ref(0) // 0=deeplink, 1=strict, 2=bot
const stepDir = ref<'forward' | 'back'>('forward')

// Step-3 "How to film a VSL that converts" walkthrough (manager-facing helper).
const guideStep = ref(0)               // 0=format, 1=script, 2=checklist
const guideOpen = ref(true)            // collapses once a video is uploaded
const copiedScript = ref(false)
const guideScript = computed(() => {
  const name = (form.value.model_name || '').trim() || 'babe'
  return `Hey, it's ${name} 😘\n`
    + `I post way more than I can show here — the good stuff is on my page.\n`
    + `If you want to see the rest (and talk to me directly), it's all waiting for you below.\n`
    + `Tap the button under this video — I'll see you on the other side 🔥`
})
function copyGuideScript() {
  navigator.clipboard.writeText(guideScript.value)
  copiedScript.value = true
  setTimeout(() => { copiedScript.value = false }, 2000)
}
// Once a video is in, the manager doesn't need the how-to anymore — fold it away
// (they can still reopen it from the header).
watch(() => form.value.video_url, (url, prev) => {
  if (url && !prev) guideOpen.value = false
})

const steps = computed(() =>
  form.value.page_type === 'direct'
    ? [
        { id: 'setup', label: '1 — Setup' },
      ]
    : [
        { id: 'setup',    label: '1 — Setup' },
        { id: 'template', label: '2 — Template' },
        { id: 'vsl',      label: '3 — VSL' },
        { id: 'cta',      label: '4 — CTA' },
      ]
)
const tabOrder = computed(() => steps.value.map(s => s.id))
const currentStepIndex = computed(() => tabOrder.value.indexOf(activeTab.value))
const canGoBack = computed(() =>
  currentStepIndex.value > 0 || setupSubStep.value > 0
)
const isLastAction = computed(() => {
  if (form.value.page_type === 'direct') return activeTab.value === 'setup' && setupSubStep.value === 1
  return currentStepIndex.value === steps.value.length - 1 && activeTab.value !== 'setup'
})

const isStepValid = computed(() => {
  if (activeTab.value === 'setup') {
    if (setupSubStep.value === 0) return true // type selection always valid
    if (!form.value.model_name.trim()) return false
    if (form.value.page_type === 'direct' && !form.value.direct_url.trim()) return false
    return true
  }
  if (activeTab.value === 'template') return true
  if (activeTab.value === 'vsl') return true
  if (activeTab.value === 'cta') return true // CTA URL optional — can be added later
  return true
})

const colorPresets = [
  { label: 'OnlyFans', value: '#00AFF0' },
  { label: 'Rose',     value: '#EC4899' },
  { label: 'Violet',   value: '#6D4EE8' },
  { label: 'Rouge',    value: '#EF4444' },
  { label: 'Vert',     value: '#10B981' },
]

const inputStyle = computed(() => ({
  width: '100%',
  background: C.value.inputBg,
  border: `1px solid ${C.value.borderInput}`,
  borderRadius: '10px',
  padding: '11px 14px',
  fontSize: '14px',
  color: C.value.text,
  outline: 'none',
  boxSizing: 'border-box' as const,
  fontFamily: 'inherit',
  transition: 'border-color 0.15s',
}))

const form = ref({
  page_type:        'vsl' as 'vsl' | 'direct',
  model_name:       '',
  model_handle:     '',
  slug:             '',
  direct_url:       '',
  template:         'vsl-classic' as 'vsl-classic' | 'vsl-bandeau' | 'vsl-popup',
  video_url:        '',
  cta_url:          '',
  cta_type:         'text' as 'text' | 'image',
  cta_image_url:    '',
  btn_label:        '',
  btn_color:        '#00AFF0',
  online_status:    false,
  age_gate:         true,
  deep_link_enabled:  true,
  strict_deep_link:   false,
  bot_protection:     true,
  cta_reveal_at:    null as number | null,
  extra_links:      [] as Array<{ type?: string; label: string; url: string; color: string }>,
  popup_title:      'Join me in private 🔥',
  popup_subtitle:   'Exclusive content available now',
  popup_delay:      3,
})

const bandeauPlatforms = [
  { id: 'instagram', name: 'Instagram', color: '#E1306C', defaultLabel: 'Mon Instagram',
    svgLg: '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" width="24" height="24"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="white" stroke="none"/></svg>',
    svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" width="14" height="14"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none"/></svg>' },
  { id: 'tiktok', name: 'TikTok', color: '#010101', defaultLabel: 'Mon TikTok',
    svgLg: '<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>',
    svg: '<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>' },
  { id: 'twitter', name: 'X', color: '#000000', defaultLabel: 'Mon X',
    svgLg: '<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    svg: '<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' },
  { id: 'telegram', name: 'Telegram', color: '#26A5E4', defaultLabel: 'Mon Telegram',
    svgLg: '<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
    svg: '<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>' },
  { id: 'snapchat', name: 'Snapchat', color: '#FFFC00', defaultLabel: 'Mon Snapchat',
    svgLg: '<svg viewBox="0 0 24 24" fill="black" width="22" height="22"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.239.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.146 0 .27.029.383.074.42.194.789.3 1.104.3.234 0 .384-.06.465-.105l-.046-.569c-.098-1.626-.225-3.651.307-4.837C7.392 1.077 10.739.807 11.727.807l.419-.015h.06z"/></svg>',
    svg: '<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.239.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.146 0 .27.029.383.074.42.194.789.3 1.104.3.234 0 .384-.06.465-.105l-.046-.569c-.098-1.626-.225-3.651.307-4.837C7.392 1.077 10.739.807 11.727.807l.419-.015h.06z"/></svg>' },
]

function addBandeauLink(p: typeof bandeauPlatforms[0]) {
  const idx = form.value.extra_links.findIndex(l => l.type === p.id)
  if (idx !== -1) { form.value.extra_links.splice(idx, 1); return }
  form.value.extra_links.push({ type: p.id, label: p.defaultLabel, url: '', color: p.color })
}

async function uploadCtaImage(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return
  const fd = new FormData()
  fd.append('file', file)
  fd.append('type', 'cta')
  try {
    const { data } = await api.post('/upload/image', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    form.value.cta_image_url = data.url
  } catch { error.value = 'Image upload failed.' }
}

function autoSlug() {
  if (isEditMode.value) return // don't override slug when editing
  const base = (form.value.model_handle || form.value.model_name)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
  if (base) form.value.slug = base
}

async function loadPageForEdit() {
  try {
    const { data } = await api.get(`/pages/${editPageId.value}`)
    const ctaLink     = (data.links || []).find((l: any) => l.type === 'classic')
    const extraLinks  = (data.links || [])
      .filter((l: any) => l !== ctaLink && l.url)
      .map((l: any) => ({ label: l.label || l.type, url: l.url, color: l.btn_color || '#6D4EE8' }))

    form.value.page_type        = data.page_type === 'direct' ? 'direct' : 'vsl'
    form.value.model_name       = data.model_name   || ''
    form.value.model_handle     = data.model_handle || ''
    form.value.slug             = data.slug         || ''
    form.value.direct_url       = data.direct_url   || ''
    form.value.template         = (data.template as any) || 'vsl-classic'
    form.value.video_url        = data.video_url    || ''
    form.value.cta_url          = ctaLink?.url      || ''
    form.value.btn_label        = ctaLink?.label    || ''
    form.value.btn_color        = data.btn_color    || '#00AFF0'
    form.value.online_status    = !!data.online_status
    form.value.age_gate         = !!data.age_gate
    form.value.deep_link_enabled  = data.deep_link_enabled  !== false
    form.value.strict_deep_link   = !!data.strict_deep_link
    form.value.bot_protection     = !!data.bot_protection
    form.value.cta_reveal_at    = data.cta_reveal_at ?? null
    form.value.extra_links      = extraLinks
    form.value.popup_title      = data.popup_title    || 'Join me in private 🔥'
    form.value.popup_subtitle   = data.popup_subtitle || 'Exclusive content available now'
    form.value.popup_delay      = data.popup_delay_seconds ?? 3

    // skip type-selection sub-step, go straight to infos
    setupSubStep.value = 1
  } catch {
    error.value = 'Could not load the page.'
  }
}

onMounted(() => {
  if (isEditMode.value) loadPageForEdit()
})

function goToStep(i: number) {
  const tab = tabOrder.value[i]
  if (!tab) return
  stepDir.value = i > currentStepIndex.value ? 'forward' : 'back'
  activeTab.value = tab
  if (tab === 'setup') setupSubStep.value = 1
}

function nextStep() {
  error.value = ''
  stepDir.value = 'forward'

  if (activeTab.value === 'setup') {
    if (setupSubStep.value === 0) { setupSubStep.value = 1; return }
    if (!form.value.model_name.trim()) { error.value = 'Enter your name or handle.'; return }
    if (form.value.page_type === 'direct' && !form.value.direct_url.trim()) {
      error.value = 'Enter the destination URL.'; return
    }
    if (form.value.page_type === 'direct') { save(); return }
  }

  const next = tabOrder.value[currentStepIndex.value + 1]
  if (next) { activeTab.value = next }
  else { save() }
}

function prevStep() {
  stepDir.value = 'back'
  if (activeTab.value === 'setup' && setupSubStep.value > 0) {
    setupSubStep.value--; return
  }
  const prev = tabOrder.value[currentStepIndex.value - 1]
  if (prev) {
    activeTab.value = prev
    if (prev === 'setup') setupSubStep.value = 1
  }
}

async function save() {
  saving.value = true
  error.value = ''
  try {
    const payload: any = {
      model_name:    form.value.model_name,
      model_handle:  form.value.model_handle || undefined,
      slug:          form.value.slug || undefined,
      group_name:    form.value.model_name,
      page_type:     form.value.page_type === 'direct' ? 'direct' : 'landing',
      direct_url:    form.value.direct_url || undefined,
      template:      form.value.page_type === 'direct' ? 'direct' : form.value.template,
      bg_color:      '#0d0d0d',
      btn_color:     form.value.btn_color,
      video_url:     form.value.video_url || undefined,
      vsl_enabled:   true,
      online_status: form.value.online_status,
      age_gate:      form.value.age_gate,
      cta_reveal_at: form.value.cta_reveal_at || undefined,
      deep_link_enabled:  form.value.deep_link_enabled,
      strict_deep_link:   form.value.strict_deep_link,
      bot_protection:     form.value.bot_protection,
      popup_title:         form.value.popup_title || undefined,
      popup_subtitle:      form.value.popup_subtitle || undefined,
      popup_delay_seconds: form.value.popup_delay,
      is_active:     true,
      links: [
        ...(form.value.cta_url ? [{
          type:      form.value.cta_type === 'image' ? 'image_button' : 'classic',
          label:     form.value.cta_type === 'image' ? '' : (form.value.btn_label || '🔓 My OnlyFans — Private access'),
          url:       form.value.cta_url,
          image_url: form.value.cta_image_url || undefined,
          order:     0,
        }] : []),
        ...form.value.extra_links
          .filter(l => l.url)
          .map((l, i) => ({ type: l.type || 'classic', label: l.label, url: l.url, order: i + 1 })),
      ],
    }

    if (isEditMode.value) {
      await api.put(`/pages/${editPageId.value}`, payload)
      successMsg.value = true
      setTimeout(() => router.push('/dashboard/links'), 1200)
    } else {
      const { data } = await api.post('/pages', payload)
      successMsg.value = true
      setTimeout(() => router.push('/dashboard/links'), 1200)
    }
  } catch (e: any) {
    if (e.response?.data?.error === 'plan_limit') {
      router.push('/billing?limit=1')
      return
    }
    const errors = e.response?.data?.errors
    error.value = errors
      ? Object.values(errors).flat().join(' ')
      : (e.response?.data?.message || (isEditMode.value ? 'Update failed.' : 'Creation failed.'))
    activeTab.value = 'setup'
    window.scrollTo(0, 0)
  } finally {
    saving.value = false
  }
}
</script>

<style>
@keyframes spin {
  from { transform: rotate(0deg) }
  to   { transform: rotate(360deg) }
}

/* ── Advanced options toggle — highlighted ── */
.adv-toggle { transition: background 0.18s, filter 0.15s; }
.adv-toggle:hover { filter: brightness(1.18); }
.adv-icon { transition: transform 0.25s ease; }
.adv-toggle:hover .adv-icon { transform: rotate(35deg); }
.adv-toggle--attn { animation: adv-pulse 2.4s ease-in-out infinite; }
@keyframes adv-pulse {
  0%, 100% { box-shadow: inset 0 0 0 0 rgba(109,78,232,0); }
  50%      { box-shadow: inset 0 0 0 1.5px rgba(109,78,232,0.5); }
}
.adv-badge {
  font-size: 9px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase;
  color: #A78BFA; background: rgba(109,78,232,0.18);
  border: 1px solid rgba(109,78,232,0.32); border-radius: 999px; padding: 2px 7px;
}

/* ── Step slide transitions ── */
.step-fwd-enter-active,
.step-back-enter-active { transition: all 0.22s cubic-bezier(0.4,0,0.2,1); }
.step-fwd-leave-active,
.step-back-leave-active { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }

.step-fwd-enter-from { opacity:0; transform:translateX(32px); }
.step-fwd-leave-to   { opacity:0; transform:translateX(-32px); }
.step-back-enter-from { opacity:0; transform:translateX(-32px); }
.step-back-leave-to   { opacity:0; transform:translateX(32px); }
@keyframes preview-bounce {
  0%   { transform: scale(1);     box-shadow: 0 4px 16px color-mix(in srgb, var(--btn, #00AFF0) 38%, transparent); }
  48%  { transform: scale(1.035); box-shadow: 0 8px 32px color-mix(in srgb, var(--btn, #00AFF0) 72%, transparent); }
  100% { transform: scale(1);     box-shadow: 0 4px 16px color-mix(in srgb, var(--btn, #00AFF0) 38%, transparent); }
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1 }
  50%       { opacity: 0.4 }
}

/* ── Mobile: the builder is desktop-first. On phones the 380px live preview
   can't sit next to the form, so we hide it (preview via the published page),
   give the form the full width, drop the redundant top step-rail (the bottom
   bar already navigates), and collapse the option grids to a single column. ── */
@media (max-width: 768px) {
  .bld-steps   { display: none !important; }
  .bld-preview { display: none !important; }
  .bld-form    { padding: 24px 16px !important; }
  .bld-topbar,
  .bld-bottombar { padding: 0 16px !important; }
  .bld-grid    { grid-template-columns: 1fr !important; }
}
</style>
