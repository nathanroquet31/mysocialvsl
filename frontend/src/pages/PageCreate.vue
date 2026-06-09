<template>
  <DashboardLayout title="New Page" noPadding>

    <!-- Step indicator in header -->
    <template #header-actions>
      <div style="display:flex;align-items:center;gap:0">
        <template v-for="(step, i) in steps" :key="step.id">
          <div v-if="i > 0" :style="{width:'20px',height:'2px',background:i <= currentStepIndex ? '#6D4EE8' : 'rgba(255,255,255,0.12)',flexShrink:0}"></div>
          <div @click="goToStep(i)"
            :style="{
              width:'30px',height:'30px',borderRadius:'50%',display:'flex',alignItems:'center',justifyContent:'center',
              fontSize:'12px',fontWeight:700,flexShrink:0,cursor: 'pointer',
              background: i < currentStepIndex ? '#6D4EE8' : i === currentStepIndex ? 'transparent' : 'rgba(255,255,255,0.06)',
              border: i === currentStepIndex ? '2px solid #6D4EE8' : 'none',
              color: i < currentStepIndex ? '#fff' : i === currentStepIndex ? '#6D4EE8' : 'rgba(255,255,255,0.3)',
              transition:'all 0.15s',
            }"
            :title="step.label">
            <span v-if="i < currentStepIndex">✓</span>
            <span v-else>{{ step.id }}</span>
          </div>
        </template>
      </div>
      <span style="font-size:11px;color:rgba(255,255,255,0.35);margin-left:10px">{{ steps[currentStepIndex]?.label }} · {{ currentStepNumber }}/{{ steps.length }}</span>
    </template>

    <!-- ===== BODY ===== -->
    <div :style="{display:'flex',flex:1,overflow:'hidden',height:'calc(100vh - 60px - 72px)',fontFamily:'Inter,-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif',color:'#fff',background:'#0d0b1e'}">

      <!-- ===== LEFT FORM AREA ===== -->
      <div :style="{flex:1,minHeight:0,overflowY:'auto',padding:'32px 40px 100px'}">

        <!-- Error banner -->
        <div v-if="error" :style="{background:'rgba(239,68,68,0.1)',border:'1px solid rgba(239,68,68,0.3)',borderRadius:'10px',padding:'12px 16px',marginBottom:'20px',color:'#F87171',fontSize:'13px'}">{{ error }}</div>

        <!-- ===== STEP 1 — TYPE ===== -->
        <div v-show="activeTab === 'basics'">
          <p :style="{fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.12em',marginBottom:'20px'}">What do you want to create?</p>

          <!-- Type cards -->
          <div :style="{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'16px',marginBottom:'24px'}">
            <!-- Landing Page -->
            <div @click="form.page_type='landing'"
              :style="{border:'1.5px solid',borderRadius:'16px',padding:'24px',cursor:'pointer',transition:'all 0.2s',position:'relative',
                borderColor:form.page_type==='landing'?'#6D4EE8':'rgba(255,255,255,0.08)',
                background:form.page_type==='landing'?'rgba(109,78,232,0.08)':'rgba(255,255,255,0.04)'}">
              <div v-if="form.page_type==='landing'" :style="{position:'absolute',top:'12px',right:'12px',width:'20px',height:'20px',borderRadius:'50%',background:'#6D4EE8',display:'flex',alignItems:'center',justifyContent:'center'}">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <!-- Mini mockup -->
              <div :style="{width:'100%',height:'120px',borderRadius:'12px',background:'#0d0d1a',marginBottom:'16px',display:'flex',flexDirection:'column',alignItems:'center',padding:'10px 8px',gap:'5px',overflow:'hidden'}">
                <div :style="{width:'26px',height:'26px',borderRadius:'50%',background:'rgba(255,255,255,0.2)',border:'1.5px solid rgba(255,255,255,0.3)',flexShrink:0}"></div>
                <div :style="{width:'40px',height:'3px',borderRadius:'3px',background:'rgba(255,255,255,0.5)'}"></div>
                <div :style="{width:'30px',height:'2px',borderRadius:'2px',background:'rgba(255,255,255,0.25)'}"></div>
                <div :style="{width:'100%',height:'16px',borderRadius:'5px',background:'#818CF8',marginTop:'4px',flexShrink:0}"></div>
                <div :style="{width:'100%',height:'16px',borderRadius:'5px',background:'rgba(129,140,248,0.4)',flexShrink:0}"></div>
              </div>
              <p :style="{fontSize:'16px',fontWeight:700,color:'#fff',marginBottom:'6px'}">Landing Page</p>
              <p :style="{fontSize:'13px',color:'rgba(255,255,255,0.4)',lineHeight:1.5,marginBottom:'12px'}">Customizable page with video, bio, links and social profiles.</p>
              <div :style="{display:'flex',flexDirection:'column',gap:'5px'}">
                <div v-for="feat in ['VSL video auto-play','Profile + bio','Multiple links & socials','5 pages included']" :key="feat" :style="{display:'flex',alignItems:'center',gap:'7px'}">
                  <div :style="{width:'14px',height:'14px',borderRadius:'50%',background:'rgba(109,78,232,0.2)',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                    <svg width="7" height="7" viewBox="0 0 8 8" fill="none"><path d="M1.5 4l2 2 3-3.5" stroke="#A78BFA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </div>
                  <span :style="{fontSize:'12px',color:'rgba(255,255,255,0.55)'}">{{ feat }}</span>
                </div>
              </div>
            </div>

            <!-- Direct Link -->
            <div @click="form.page_type='direct'"
              :style="{border:'1.5px solid',borderRadius:'16px',padding:'24px',cursor:'pointer',transition:'all 0.2s',position:'relative',
                borderColor:form.page_type==='direct'?'#6D4EE8':'rgba(255,255,255,0.08)',
                background:form.page_type==='direct'?'rgba(109,78,232,0.08)':'rgba(255,255,255,0.04)'}">
              <div v-if="form.page_type==='direct'" :style="{position:'absolute',top:'12px',right:'12px',width:'20px',height:'20px',borderRadius:'50%',background:'#6D4EE8',display:'flex',alignItems:'center',justifyContent:'center'}">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div :style="{width:'100%',height:'120px',borderRadius:'12px',background:'#0f172a',marginBottom:'16px',display:'flex',alignItems:'center',justifyContent:'center',overflow:'hidden'}">
                <div :style="{display:'flex',flexDirection:'column',alignItems:'center',gap:'8px'}">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.35)" stroke-width="1.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                  <div :style="{width:'56px',height:'2px',borderRadius:'2px',background:'rgba(255,255,255,0.15)'}"></div>
                </div>
              </div>
              <p :style="{fontSize:'16px',fontWeight:700,color:'#fff',marginBottom:'6px'}">Direct Link</p>
              <p :style="{fontSize:'13px',color:'rgba(255,255,255,0.4)',lineHeight:1.5,marginBottom:'12px'}">Instant redirect to a destination URL with optional protection.</p>
              <div :style="{display:'flex',flexDirection:'column',gap:'5px'}">
                <div v-for="feat in ['Instant redirect','Deeplink bypass','Bot protection','2 pages included']" :key="feat" :style="{display:'flex',alignItems:'center',gap:'7px'}">
                  <div :style="{width:'14px',height:'14px',borderRadius:'50%',background:'rgba(109,78,232,0.2)',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                    <svg width="7" height="7" viewBox="0 0 8 8" fill="none"><path d="M1.5 4l2 2 3-3.5" stroke="#A78BFA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </div>
                  <span :style="{fontSize:'12px',color:'rgba(255,255,255,0.55)'}">{{ feat }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Direct URL input -->
          <div v-if="form.page_type==='direct'" :style="{marginBottom:'20px'}">
            <label :style="{display:'block',fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'8px'}">Destination URL</label>
            <input v-model="form.direct_url" type="url" placeholder="https://example.com"
              :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'12px',padding:'12px 16px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
              @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8';($event.target as HTMLInputElement).style.boxShadow='0 0 0 3px rgba(109,78,232,0.18)'"
              @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)';($event.target as HTMLInputElement).style.boxShadow='none'" />
          </div>

          <!-- Page name -->
          <div :style="{marginBottom:'20px'}">
            <label :style="{display:'block',fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'8px'}">Page Name</label>
            <input v-model="form.dashboard_name" placeholder="e.g. My Instagram Bio"
              :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'12px',padding:'12px 16px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
              @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8';($event.target as HTMLInputElement).style.boxShadow='0 0 0 3px rgba(109,78,232,0.18)'"
              @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)';($event.target as HTMLInputElement).style.boxShadow='none'" />
          </div>

          <!-- Advanced Settings accordion -->
          <div :style="{marginBottom:'20px'}">
            <button @click="showAdvancedSettings=!showAdvancedSettings"
              :style="{width:'100%',display:'flex',alignItems:'center',justifyContent:'space-between',padding:'12px 16px',background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:showAdvancedSettings?'12px 12px 0 0':'12px',cursor:'pointer',fontFamily:'inherit',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)'}">
              Advanced Settings
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :style="{transform:showAdvancedSettings?'rotate(180deg)':'rotate(0)',transition:'transform 0.2s'}"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div v-if="showAdvancedSettings" :style="{border:'1px solid rgba(255,255,255,0.08)',borderTop:'none',borderRadius:'0 0 12px 12px',padding:'12px 16px',background:'rgba(255,255,255,0.02)',display:'flex',flexDirection:'column',gap:'14px'}">
              <div v-for="tog in [{key:'bot_protection',label:'Bot Protection',desc:'Blocks automated bots. Disables social link previews.'},{key:'deep_link_enabled',label:'Deeplink',desc:'Opens in native browser instead of in-app.'},{key:'strict_deep_link',label:'Strict Deeplink',desc:'Forces deeplink even when standard deeplink fails.'}]" :key="tog.key"
                :style="{display:'flex',alignItems:'flex-start',justifyContent:'space-between',gap:'12px'}">
                <div>
                  <p :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.8)',marginBottom:'2px'}">{{ tog.label }}</p>
                  <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.35)',lineHeight:1.5}">{{ tog.desc }}</p>
                </div>
                <div @click="(form as any)[tog.key]=!(form as any)[tog.key]"
                  :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:(form as any)[tog.key]?'#6D4EE8':'rgba(255,255,255,0.15)',position:'relative',transition:'background 0.2s',flexShrink:0,marginTop:'2px'}">
                  <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:(form as any)[tog.key]?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Configuration card -->
          <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'16px',padding:'20px',display:'flex',flexDirection:'column',gap:'16px'}">
            <div>
              <label :style="{display:'block',fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'8px'}">Short Link</label>
              <div :style="{display:'flex',alignItems:'center',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'10px',overflow:'hidden',background:'rgba(255,255,255,0.04)'}">
                <span :style="{padding:'10px 12px',background:'rgba(255,255,255,0.06)',color:'rgba(255,255,255,0.3)',fontSize:'13px',borderRight:'1px solid rgba(255,255,255,0.08)',whiteSpace:'nowrap',flexShrink:0}">mysocialvsl.com/</span>
                <input v-model="form.slug" placeholder="unique-alias"
                  :style="{flex:1,padding:'10px 12px',fontSize:'13px',color:'#fff',border:'none',outline:'none',fontFamily:'inherit',minWidth:0,background:'transparent'}" />
              </div>
            </div>
            <div>
              <label :style="{display:'block',fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'8px'}">Dashboard Name</label>
              <input v-model="form.dashboard_name" placeholder="e.g. Instagram Bio Link"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'10px',padding:'10px 14px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
            </div>
            <div style="position:relative">
              <label :style="{display:'block',fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'8px'}">Group <span style="font-size:10px;font-weight:400;text-transform:none;letter-spacing:0;color:rgba(255,255,255,0.25)">(optional)</span></label>
              <input v-model="form.group_name" list="groups-list" placeholder="e.g. Instagram, OnlyFans..."
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'10px',padding:'10px 14px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
              <datalist id="groups-list">
                <option value="Instagram" />
                <option value="OnlyFans" />
                <option value="TikTok" />
                <option value="MYM" />
                <option value="Telegram" />
              </datalist>
            </div>
          </div>
        </div>

        <!-- ===== STEP 2 — THEME ===== -->
        <div v-show="activeTab === 'theme'">
          <h2 :style="{fontSize:'22px',fontWeight:700,color:'#fff',marginBottom:'6px'}">Select a Theme</h2>
          <p :style="{fontSize:'14px',color:'rgba(255,255,255,0.4)',marginBottom:'24px'}">Start with a beautiful foundation.</p>

          <!-- Preset gallery -->
          <PresetPicker :selected="selectedPreset" @select="applyPreset" />

          <!-- Advanced Customization accordion (open by default) -->
          <div>
            <button @click="showThemeAdvanced=!showThemeAdvanced"
              :style="{width:'100%',display:'flex',alignItems:'center',gap:'10px',padding:'16px 20px',background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:showThemeAdvanced?'16px 16px 0 0':'16px',cursor:'pointer',fontFamily:'inherit'}">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
              <div :style="{flex:1,textAlign:'left'}">
                <div :style="{fontSize:'14px',fontWeight:700,color:'#fff'}">Advanced Customization</div>
                <div :style="{fontSize:'12px',color:'rgba(255,255,255,0.4)'}">Fine-tune colors, fonts, and button styles</div>
              </div>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2" :style="{transform:showThemeAdvanced?'rotate(180deg)':'rotate(0)',transition:'transform 0.2s'}"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div v-if="showThemeAdvanced" :style="{border:'1px solid rgba(255,255,255,0.08)',borderTop:'none',borderRadius:'0 0 16px 16px',padding:'24px',background:'rgba(255,255,255,0.03)'}">
              <div :style="{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'24px'}">
                <!-- LEFT COLUMN -->
                <div>
                  <!-- Background -->
                  <div :style="{marginBottom:'24px'}">
                    <div :style="{display:'flex',alignItems:'center',gap:'8px',marginBottom:'12px'}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                      <span :style="{fontSize:'13px',fontWeight:700,color:'#fff'}">Background</span>
                    </div>
                    <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.35)',marginBottom:'10px'}">Customize your backdrop.</p>
                    <div :style="{display:'flex',gap:'8px',flexWrap:'wrap',alignItems:'center'}">
                      <div v-for="s in bgSwatches" :key="s.color" @click="form.bg_color=s.color"
                        :style="{width:'36px',height:'36px',borderRadius:'50%',background:s.color,cursor:'pointer',border:form.bg_color===s.color?'3px solid #6D4EE8':'2px solid rgba(255,255,255,0.15)',boxSizing:'border-box',transition:'border 0.1s',position:'relative'}">
                        <div v-if="form.bg_color===s.color" :style="{position:'absolute',inset:0,display:'flex',alignItems:'center',justifyContent:'center'}">
                          <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                      </div>
                      <label :style="{width:'36px',height:'36px',borderRadius:'50%',border:'2px dashed rgba(255,255,255,0.2)',display:'flex',alignItems:'center',justifyContent:'center',cursor:'pointer',overflow:'hidden',position:'relative'}">
                        <input type="color" v-model="form.bg_color" :style="{opacity:0,position:'absolute',width:'1px',height:'1px'}" />
                        <span :style="{fontSize:'16px',color:'rgba(255,255,255,0.4)'}">+</span>
                      </label>
                    </div>
                  </div>

                  <!-- Text Color -->
                  <div>
                    <div :style="{display:'flex',alignItems:'center',gap:'8px',marginBottom:'12px'}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                      <span :style="{fontSize:'13px',fontWeight:700,color:'#fff'}">Text Color</span>
                    </div>
                    <div :style="{display:'flex',gap:'6px'}">
                      <button v-for="tc in [{id:'light',label:'Light'},{id:'dark',label:'Dark'},{id:'custom',label:'Custom Text'}]" :key="tc.id"
                        @click="form.text_color=tc.id"
                        :style="{padding:'7px 14px',border:'1.5px solid',borderRadius:'8px',fontSize:'12px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',
                          borderColor:form.text_color===tc.id?'#6D4EE8':'rgba(255,255,255,0.12)',
                          background:form.text_color===tc.id?'rgba(109,78,232,0.15)':'rgba(255,255,255,0.04)',
                          color:form.text_color===tc.id?'#A78BFA':'rgba(255,255,255,0.5)'}">
                        {{ tc.label }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div>
                  <!-- Buttons -->
                  <div :style="{marginBottom:'24px'}">
                    <div :style="{display:'flex',alignItems:'center',gap:'8px',marginBottom:'12px'}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2"><rect x="3" y="8" width="18" height="8" rx="2"/></svg>
                      <span :style="{fontSize:'13px',fontWeight:700,color:'#fff'}">Buttons</span>
                    </div>
                    <div :style="{display:'flex',gap:'8px'}">
                      <div v-for="bs in [{id:'rounded',label:'Rounded',r:'10px'},{id:'square',label:'Square',r:'3px'},{id:'pill',label:'Pill',r:'999px'}]" :key="bs.id"
                        @click="form.btn_style=bs.id"
                        :style="{flex:1,border:'1.5px solid',borderRadius:'10px',padding:'10px 8px',cursor:'pointer',textAlign:'center',
                          borderColor:form.btn_style===bs.id?'#6D4EE8':'rgba(255,255,255,0.1)',
                          background:form.btn_style===bs.id?'rgba(109,78,232,0.15)':'rgba(255,255,255,0.04)'}">
                        <div :style="{width:'100%',height:'18px',borderRadius:bs.r,background:form.btn_style===bs.id?'#6D4EE8':'rgba(255,255,255,0.2)',marginBottom:'6px'}"></div>
                        <span :style="{fontSize:'11px',color:form.btn_style===bs.id?'#A78BFA':'rgba(255,255,255,0.4)',fontWeight:600}">{{ bs.label }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Typography -->
                  <div>
                    <div :style="{display:'flex',alignItems:'center',gap:'8px',marginBottom:'12px'}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                      <span :style="{fontSize:'13px',fontWeight:700,color:'#fff'}">Typography</span>
                    </div>
                    <div :style="{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'6px'}">
                      <button v-for="f in fontOptions" :key="f.id" @click="form.font=f.id"
                        :style="{padding:'7px 10px',border:'1.5px solid',borderRadius:'8px',fontSize:'12px',fontWeight:600,cursor:'pointer',fontFamily:f.family,
                          borderColor:form.font===f.id?'#6D4EE8':'rgba(255,255,255,0.1)',
                          background:form.font===f.id?'rgba(109,78,232,0.15)':'rgba(255,255,255,0.04)',
                          color:form.font===f.id?'#A78BFA':'rgba(255,255,255,0.45)'}">
                        {{ f.label }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== STEP 3 — PROFILE ===== -->
        <div v-show="activeTab === 'profile'">
          <h2 :style="{fontSize:'22px',fontWeight:700,color:'#fff',marginBottom:'24px'}">Profile</h2>

          <!-- Background upload -->
          <div :style="{marginBottom:'20px'}">
            <label :style="{display:'block',fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'8px'}">Background Image</label>
            <div
              @click="($refs.bgInput as HTMLInputElement).click()"
              @dragover.prevent="dragOverBg=true"
              @dragleave="dragOverBg=false"
              @drop.prevent="onDrop($event,'background')"
              :style="{border:'2px dashed',borderColor:dragOverBg?'#6D4EE8':'rgba(255,255,255,0.15)',borderRadius:'12px',height:'130px',display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',cursor:'pointer',transition:'all 0.2s',backgroundImage:form.bg_image_url?`url(${form.bg_image_url})`:'none',backgroundSize:'cover',backgroundPosition:'center',position:'relative',overflow:'hidden',background:dragOverBg?'rgba(109,78,232,0.06)':'rgba(255,255,255,0.03)'}">
              <div v-if="!form.bg_image_url" :style="{textAlign:'center'}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5" :style="{margin:'0 auto 6px',display:'block'}"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.4)',fontWeight:500}">Drag & drop or click to upload</p>
                <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.2)'}">PNG, JPG, GIF up to 10MB</p>
              </div>
              <div v-if="form.bg_image_url" :style="{position:'absolute',inset:0,background:'rgba(0,0,0,0.4)',display:'flex',alignItems:'center',justifyContent:'center',opacity:0,transition:'opacity 0.2s'}"
                @mouseover="($event.currentTarget as HTMLElement).style.opacity='1'"
                @mouseout="($event.currentTarget as HTMLElement).style.opacity='0'">
                <span :style="{color:'#fff',fontSize:'12px',fontWeight:600}">Change Image</span>
              </div>
            </div>
            <input ref="bgInput" type="file" accept="image/*" :style="{display:'none'}" @change="uploadFile($event,'background')" />
            <p v-if="uploadingBg" :style="{fontSize:'11px',color:'#A78BFA',marginTop:'4px'}">Uploading...</p>
          </div>

          <!-- Avatar -->
          <div :style="{display:'flex',alignItems:'flex-start',gap:'20px',marginBottom:'20px'}">
            <div :style="{position:'relative',flexShrink:0}">
              <div @click="($refs.avatarInput as HTMLInputElement).click()"
                :style="{width:'80px',height:'80px',borderRadius:'50%',background:'#6D4EE8',border:'3px solid rgba(255,255,255,0.1)',cursor:'pointer',overflow:'hidden',display:'flex',alignItems:'center',justifyContent:'center'}">
                <img v-if="form.avatar_url" :src="form.avatar_url" :style="{width:'100%',height:'100%',objectFit:'cover'}" />
                <svg v-else width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <div :style="{position:'absolute',bottom:0,right:'-4px',cursor:'pointer'}"
                @click.stop="($refs.avatarInput as HTMLInputElement).click()">
                <div :style="{width:'24px',height:'24px',background:'#6D4EE8',borderRadius:'50%',display:'flex',alignItems:'center',justifyContent:'center',border:'2px solid #0d0b1e'}">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                </div>
              </div>
              <input ref="avatarInput" type="file" accept="image/*" :style="{display:'none'}" @change="uploadFile($event,'avatar')" />
            </div>
            <div :style="{flex:1,paddingTop:'4px'}">
              <p :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'12px'}">Profile Picture</p>
              <div :style="{display:'flex',flexDirection:'column',gap:'10px'}">
                <div :style="{display:'flex',alignItems:'center',gap:'10px'}">
                  <div @click="form.show_avatar=!form.show_avatar"
                    :style="{width:'38px',height:'21px',borderRadius:'999px',cursor:'pointer',background:form.show_avatar?'#6D4EE8':'rgba(255,255,255,0.15)',position:'relative',transition:'background 0.2s',flexShrink:0}">
                    <div :style="{width:'15px',height:'15px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.show_avatar?'20px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                  </div>
                  <span :style="{fontSize:'13px',color:'rgba(255,255,255,0.6)'}">Show Profile Picture</span>
                </div>
                <div :style="{display:'flex',alignItems:'center',gap:'10px'}">
                  <div @click="form.verified_badge=!form.verified_badge"
                    :style="{width:'38px',height:'21px',borderRadius:'999px',cursor:'pointer',background:form.verified_badge?'#6D4EE8':'rgba(255,255,255,0.15)',position:'relative',transition:'background 0.2s',flexShrink:0}">
                    <div :style="{width:'15px',height:'15px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.verified_badge?'20px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                  </div>
                  <span :style="{fontSize:'13px',color:'rgba(255,255,255,0.6)'}">Verified Badge</span>
                </div>
              </div>
              <p v-if="uploadingAvatar" :style="{fontSize:'11px',color:'#A78BFA',marginTop:'6px'}">Uploading...</p>
            </div>
          </div>

          <!-- Display Name -->
          <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'14px',padding:'16px',marginBottom:'12px'}">
            <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',marginBottom:'8px'}">
              <label :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)'}">Display Name</label>
              <span :style="{fontSize:'11px',color:'rgba(255,255,255,0.3)'}">{{ form.model_name.length }}/100</span>
            </div>
            <input v-model="form.model_name" maxlength="100" placeholder="e.g. Alexandra Lancaster"
              :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'10px',padding:'10px 14px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
              @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8';($event.target as HTMLInputElement).style.boxShadow='0 0 0 3px rgba(109,78,232,0.18)'"
              @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)';($event.target as HTMLInputElement).style.boxShadow='none'" />
          </div>

          <!-- Bio -->
          <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'14px',padding:'16px',marginBottom:'16px'}">
            <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',marginBottom:'8px'}">
              <label :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)'}">Bio</label>
              <span :style="{fontSize:'11px',color:'rgba(255,255,255,0.3)'}">{{ form.bio.length }}/250</span>
            </div>
            <textarea v-model="form.bio" maxlength="250" rows="4" placeholder="Tell your visitors a little about yourself..."
              :style="{background:'rgba(255,255,255,0.06)',border:`1px solid ${triggerWordsInBio.length ? 'rgba(251,191,36,0.5)' : 'rgba(255,255,255,0.1)'}`,borderRadius:'10px',padding:'10px 14px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',resize:'none',fontFamily:'inherit',boxSizing:'border-box'}"
              @focus="($event.target as HTMLTextAreaElement).style.borderColor='#6D4EE8';($event.target as HTMLTextAreaElement).style.boxShadow='0 0 0 3px rgba(109,78,232,0.18)'"
              @blur="($event.target as HTMLTextAreaElement).style.borderColor=triggerWordsInBio.length?'rgba(251,191,36,0.5)':'rgba(255,255,255,0.1)';($event.target as HTMLTextAreaElement).style.boxShadow='none'"></textarea>
            <!-- Trigger word warning -->
            <div v-if="triggerWordsInBio.length" :style="{
              display:'flex',alignItems:'flex-start',gap:'8px',
              background:'rgba(251,191,36,0.08)',border:'1px solid rgba(251,191,36,0.25)',
              borderRadius:'8px',padding:'10px 12px',marginTop:'8px',
            }">
              <i class="bi bi-exclamation-triangle" style="color:#fbbf24;font-size:13px;flex-shrink:0;margin-top:1px"></i>
              <div>
                <p style="font-size:12px;font-weight:600;color:#fbbf24;margin:0 0 2px">Trigger words detected</p>
                <p style="font-size:11px;color:rgba(251,191,36,0.7);margin:0;line-height:1.5">
                  Words like <strong>{{ triggerWordsInBio.join(', ') }}</strong> may cause Instagram to flag your landing page. Consider rephrasing.
                </p>
              </div>
            </div>
            <div :style="{background:'rgba(109,78,232,0.08)',border:'1px solid rgba(109,78,232,0.2)',borderRadius:'8px',padding:'10px 12px',marginTop:'10px'}">
              <p :style="{fontSize:'12px',color:'#A78BFA',fontWeight:500,marginBottom:'6px'}">Dynamic tags:</p>
              <div :style="{display:'flex',gap:'6px'}">
                <span :style="{padding:'3px 10px',background:'rgba(109,78,232,0.2)',borderRadius:'999px',fontSize:'12px',fontWeight:600,color:'#A78BFA',cursor:'pointer'}" @click="form.bio+='{city}'">{city}</span>
                <span :style="{padding:'3px 10px',background:'rgba(109,78,232,0.2)',borderRadius:'999px',fontSize:'12px',fontWeight:600,color:'#A78BFA',cursor:'pointer'}" @click="form.bio+='{country}'">{country}</span>
              </div>
            </div>
          </div>

          <!-- VSL card -->
          <div :style="{background:'rgba(255,255,255,0.04)',border:'2px solid #6D4EE8',borderRadius:'14px',padding:'16px',position:'relative',overflow:'hidden'}">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#6D4EE8,#A78BFA,#F59E0B,#A78BFA,#6D4EE8);background-size:200% 100%;animation:shimmer 2.5s linear infinite"></div>
            <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',marginBottom:'10px',marginTop:'4px'}">
              <div :style="{display:'flex',alignItems:'center',gap:'8px'}">
                <span :style="{fontSize:'18px',lineHeight:1}">🎬</span>
                <p :style="{fontSize:'14px',fontWeight:700,color:'#fff'}">Video Sales Letter (VSL)</p>
              </div>
              <div :style="{display:'flex',alignItems:'center',gap:'4px',padding:'3px 10px',borderRadius:'999px',background:'linear-gradient(135deg,#F59E0B,#FBBF24)',boxShadow:'0 2px 8px rgba(245,158,11,0.35)'}">
                <span :style="{fontSize:'10px'}">⭐</span>
                <span :style="{fontSize:'10px',fontWeight:700,color:'#fff'}">Recommended</span>
              </div>
            </div>
            <div :style="{background:'rgba(109,78,232,0.1)',border:'1px solid rgba(109,78,232,0.25)',borderRadius:'10px',padding:'12px 14px',marginBottom:'14px'}">
              <p :style="{fontSize:'12px',fontWeight:700,color:'#A78BFA',marginBottom:'4px'}">What is a VSL?</p>
              <p :style="{fontSize:'12px',color:'rgba(167,139,250,0.8)',lineHeight:1.6}">A <strong>Video Sales Letter</strong> is the #1 conversion tool. Auto-plays, builds connection, drives up to <strong>3× more</strong> clicks than static pages.</p>
            </div>
            <VideoUpload v-model="form.video_url" />
          </div>
        </div>

        <!-- ===== STEP 4 — LINKS ===== -->
        <div v-show="activeTab === 'content'">
          <h2 :style="{fontSize:'22px',fontWeight:700,color:'#fff',marginBottom:'20px'}">Links</h2>

          <button @click="showLinkPicker=!showLinkPicker"
            :style="{width:'100%',padding:'12px',background:'transparent',color:'#A78BFA',border:'1px solid #6D4EE8',borderRadius:'10px',fontSize:'13px',fontWeight:700,cursor:'pointer',fontFamily:'inherit',marginBottom:'16px',display:'flex',alignItems:'center',justifyContent:'center',gap:'8px',transition:'all 0.15s'}"
            @mouseover="($event.currentTarget as HTMLElement).style.background='#6D4EE8';($event.currentTarget as HTMLElement).style.color='#fff'"
            @mouseout="($event.currentTarget as HTMLElement).style.background='transparent';($event.currentTarget as HTMLElement).style.color='#A78BFA'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Add New Link
          </button>

          <div v-if="showLinkPicker" :style="{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'8px',marginBottom:'20px'}">
            <div v-for="lt in linkTypes" :key="lt.id" @click="addLink(lt.id);showLinkPicker=false"
              :style="{border:'1px solid rgba(255,255,255,0.08)',borderRadius:'10px',padding:'12px',cursor:'pointer',transition:'all 0.15s',background:'rgba(255,255,255,0.04)'}"
              @mouseover="($event.currentTarget as HTMLElement).style.borderColor='#6D4EE8';($event.currentTarget as HTMLElement).style.background='rgba(109,78,232,0.08)'"
              @mouseout="($event.currentTarget as HTMLElement).style.borderColor='rgba(255,255,255,0.08)';($event.currentTarget as HTMLElement).style.background='rgba(255,255,255,0.04)'">
              <p :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.8)',marginBottom:'3px'}">{{ lt.name }}</p>
              <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.35)',lineHeight:1.4}">{{ lt.desc }}</p>
            </div>
          </div>

          <div v-if="contentLinks.length > 0" :style="{display:'flex',flexDirection:'column',gap:'8px'}">
            <div v-for="(link, i) in form.links.filter(l=>!l.is_social)" :key="i"
              :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',overflow:'hidden'}">
              <div :style="{display:'flex',alignItems:'center',gap:'10px',padding:'12px 14px'}">
                <span :style="{color:'rgba(255,255,255,0.2)',cursor:'grab',fontSize:'16px',lineHeight:1}">⠿</span>
                <span :style="{padding:'2px 8px',background:'rgba(109,78,232,0.15)',borderRadius:'4px',fontSize:'10px',fontWeight:600,color:'#A78BFA',textTransform:'uppercase',flexShrink:0}">{{ link.type }}</span>
                <div :style="{display:'flex',alignItems:'center',gap:'6px',flex:1,overflow:'hidden'}">
                  <span :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.8)',overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap'}">{{ link.label || 'Untitled' }}</span>
                </div>
                <button @click="link._open=!link._open"
                  :style="{padding:'4px 10px',border:'1px solid rgba(255,255,255,0.12)',borderRadius:'6px',background:'transparent',cursor:'pointer',fontSize:'11px',color:'rgba(255,255,255,0.4)',flexShrink:0,fontFamily:'inherit'}">
                  Settings
                </button>
                <button @click="removeLink(link)"
                  :style="{width:'26px',height:'26px',border:'none',background:'none',cursor:'pointer',color:'rgba(255,255,255,0.25)',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
              </div>
              <div v-if="link._open" :style="{borderTop:'1px solid rgba(255,255,255,0.06)',padding:'14px',display:'flex',flexDirection:'column',gap:'10px'}">
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:500,color:'rgba(255,255,255,0.5)',marginBottom:'5px'}">Label</label>
                  <input v-model="link.label" placeholder="Button label"
                    :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'8px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                    @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                    @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
                </div>
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:500,color:'rgba(255,255,255,0.5)',marginBottom:'5px'}">Button Type</label>
                  <select v-model="link.type"
                    :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'8px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}">
                    <option v-for="lt in linkTypes" :key="lt.id" :value="lt.id">{{ lt.name }}</option>
                  </select>
                </div>
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:500,color:'rgba(255,255,255,0.5)',marginBottom:'5px'}">URL</label>
                  <input v-model="link.url" type="url" placeholder="https://"
                    :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'8px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                    @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                    @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
                </div>
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:500,color:'rgba(255,255,255,0.5)',marginBottom:'5px'}">Description</label>
                  <textarea v-model="link.description" rows="2" placeholder="Optional description..."
                    :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'8px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',resize:'none',fontFamily:'inherit',boxSizing:'border-box'}"
                    @focus="($event.target as HTMLTextAreaElement).style.borderColor='#6D4EE8'"
                    @blur="($event.target as HTMLTextAreaElement).style.borderColor='rgba(255,255,255,0.1)'">
                  </textarea>
                </div>
                <div>
                  <label :style="{display:'block',fontSize:'12px',fontWeight:500,color:'rgba(255,255,255,0.5)',marginBottom:'5px'}">Button Effect</label>
                  <select v-model="link.animation"
                    :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'8px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}">
                    <option value="">None</option>
                    <option value="bounce">Bouncing</option>
                    <option value="buzz">Buzzing</option>
                    <option value="pulse">Pulsing</option>
                    <option value="shake">Shaking</option>
                    <option value="swing">Swinging</option>
                  </select>
                </div>
                <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',background:'rgba(239,68,68,0.06)',border:'1px solid rgba(239,68,68,0.15)',borderRadius:'8px',padding:'10px 12px'}">
                  <div>
                    <p :style="{fontSize:'12px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'1px'}">🔞 18+ Sensitive Content</p>
                    <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.3)'}">Show an adult content warning before the link opens.</p>
                  </div>
                  <div @click="link.age_restricted=!link.age_restricted"
                    :style="{width:'36px',height:'20px',borderRadius:'999px',cursor:'pointer',background:link.age_restricted?'#EF4444':'rgba(255,255,255,0.15)',position:'relative',transition:'background 0.2s',flexShrink:0}">
                    <div :style="{width:'14px',height:'14px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:link.age_restricted?'19px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else-if="!showLinkPicker" :style="{textAlign:'center',padding:'40px 24px',color:'rgba(255,255,255,0.25)',fontSize:'13px',background:'rgba(255,255,255,0.03)',border:'1px dashed rgba(255,255,255,0.1)',borderRadius:'12px'}">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" :style="{margin:'0 auto 10px',display:'block'}"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
            <p>No links yet — click "Add New Link" to get started.</p>
          </div>
        </div>

        <!-- ===== STEP 5 — SOCIALS ===== -->
        <div v-show="activeTab === 'socials'">
          <h2 :style="{fontSize:'22px',fontWeight:700,color:'#fff',marginBottom:'6px'}">Socials</h2>
          <p :style="{fontSize:'13px',color:'rgba(255,255,255,0.4)',marginBottom:'20px'}">Quick add platforms</p>

          <!-- Platform preview banner -->
          <div v-if="hoveredPlatform" :style="{
            display:'flex',alignItems:'center',gap:'20px',
            background:'rgba(255,255,255,0.03)',border:'1px solid rgba(255,255,255,0.08)',
            borderRadius:'16px',padding:'16px',marginBottom:'20px',
            animation:'fadeIn 0.2s ease',
          }">
            <SocialPlatformPreview
              :platformId="hoveredPlatform"
              :handle="form.model_handle || 'karine.vip'"
              :displayName="form.model_name || 'Karine'"
              :slug="form.slug || 'karine-UFN4V'"
              :avatarUrl="form.avatar_url || 'https://i.pravatar.cc/300?img=47'"
              :landingBg="form.bg_color || '#0d0d0d'"
              :landingBtn="form.btn_color || '#00aff0'"
              :videoUrl="form.video_url || 'https://streamable.com/e/0ed1q5'"
              style="flex-shrink:0"
            />
            <div>
              <p :style="{fontSize:'14px',fontWeight:700,color:'#fff',marginBottom:'6px',textTransform:'capitalize'}">
                {{ hoveredPlatform }} → VSL
              </p>
              <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.4)',lineHeight:1.6,margin:'0 0 12px'}">
                When someone visits your {{ hoveredPlatform }} profile and taps your bio link,
                our <strong style="color:#A78BFA">deeplink</strong> bypasses the in-app browser
                and opens your VSL page in Safari/Chrome — higher conversion, better experience.
              </p>
              <div style="display:flex;gap:'8px';flex-wrap:wrap;gap:8px">
                <span style="background:rgba(109,78,232,0.15);border:1px solid rgba(109,78,232,0.3);color:#A78BFA;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600">
                  ⚡ Deeplink bypass
                </span>
                <span style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:#10b981;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600">
                  🛡 Bot protection
                </span>
              </div>
            </div>
          </div>

          <div :style="{display:'flex',flexWrap:'wrap',gap:'8px',marginBottom:'24px'}">
            <button v-for="s in socialOptions" :key="s.type"
              @click="toggleSocial(s)"
              @mouseenter="hoveredPlatform = s.type"
              @mouseleave="hoveredPlatform = hoveredPlatform === s.type ? hoveredPlatform : hoveredPlatform"
              :style="{display:'flex',alignItems:'center',gap:'8px',padding:'7px 14px',borderRadius:'8px',border:'1.5px solid',cursor:'pointer',transition:'all 0.15s',fontFamily:'inherit',
                borderColor:isSocialSelected(s.type)?'#6D4EE8':hoveredPlatform===s.type?'rgba(255,255,255,0.25)':'rgba(255,255,255,0.1)',
                background:isSocialSelected(s.type)?'rgba(109,78,232,0.12)':hoveredPlatform===s.type?'rgba(255,255,255,0.06)':'rgba(255,255,255,0.04)'}">
              <div :style="{width:'28px',height:'28px',borderRadius:'6px',background:s.color,display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                <span v-html="s.svg"></span>
              </div>
              <span :style="{fontSize:'13px',fontWeight:600,color:isSocialSelected(s.type)?'#A78BFA':'rgba(255,255,255,0.6)'}">{{ s.name }}</span>
            </button>
          </div>

          <div :style="{marginBottom:'20px'}">
            <label :style="{display:'block',fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.4)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'8px'}">Paste a URL to auto-detect platform</label>
            <div :style="{display:'flex',gap:'8px'}">
              <input v-model="manualUrl" type="url" placeholder="https://instagram.com/yourprofile"
                :style="{flex:1,background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',outline:'none',fontFamily:'inherit',minWidth:0}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'"
                @keydown.enter="addManualUrl" />
              <button @click="addManualUrl"
                :style="{padding:'9px 16px',background:'#6D4EE8',color:'#fff',border:'none',borderRadius:'8px',fontSize:'13px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',whiteSpace:'nowrap'}">
                →
              </button>
            </div>
          </div>

          <div v-if="socialLinks.length > 0" :style="{display:'flex',flexDirection:'column',gap:'8px'}">
            <div v-for="link in socialLinks" :key="link.type"
              :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'12px 14px',display:'flex',alignItems:'center',gap:'10px'}">
              <div :style="{width:'32px',height:'32px',borderRadius:'8px',background:getSocialColor(link.type),display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                <span v-html="getSocialSvg(link.type)"></span>
              </div>
              <span :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',flexShrink:0,width:'80px'}">{{ link.label }}</span>
              <input v-model="link.url" type="url" :placeholder="`https://...`"
                :style="{flex:1,background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'7px',padding:'7px 10px',fontSize:'12px',color:'#fff',outline:'none',fontFamily:'inherit',minWidth:0}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
              <button @click="removeSocial(link.type)"
                :style="{width:'26px',height:'26px',border:'none',background:'none',cursor:'pointer',color:'rgba(255,255,255,0.25)',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- ===== STEP 6 — DEEPLINK ===== -->
        <div v-show="activeTab === 'deeplink'">
          <h2 :style="{fontSize:'22px',fontWeight:700,color:'#fff',marginBottom:'6px'}">Deeplink</h2>
          <p :style="{fontSize:'13px',color:'rgba(255,255,255,0.4)',marginBottom:'20px'}">Deeplink options &amp; platform preview</p>

          <!-- Info banner -->
          <div :style="{background:'rgba(109,78,232,0.08)',border:'1px solid rgba(109,78,232,0.25)',borderRadius:'12px',padding:'14px 16px',marginBottom:'20px',display:'flex',gap:'10px',alignItems:'flex-start'}">
            <span style="font-size:18px;flex-shrink:0">⚡</span>
            <div>
              <p :style="{fontSize:'13px',fontWeight:600,color:'#A78BFA',marginBottom:'3px'}">Deeplink protection is fully automatic</p>
              <p :style="{fontSize:'12px',color:'rgba(167,139,250,0.7)',lineHeight:1.6}">All options are enabled to ensure your visitors are redirected out of the in-app browser. Traffic is approximately <strong style="color:#A78BFA">50% Android / 50% iOS</strong>.</p>
            </div>
          </div>

          <!-- Platform selector -->
          <p :style="{fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.35)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'10px'}">Select a platform to preview</p>
          <div :style="{display:'flex',flexWrap:'wrap',gap:'6px',marginBottom:'20px'}">
            <button v-for="p in deeplinkPlatforms" :key="p.id"
              @click="selectedDeeplinkPlatform=p.id"
              :style="{display:'flex',alignItems:'center',gap:'6px',padding:'6px 12px',borderRadius:'8px',border:'1.5px solid',cursor:'pointer',fontFamily:'inherit',fontSize:'12px',fontWeight:600,transition:'all 0.15s',
                borderColor:selectedDeeplinkPlatform===p.id?p.color:'rgba(255,255,255,0.1)',
                background:selectedDeeplinkPlatform===p.id?`${p.color}18`:'rgba(255,255,255,0.04)',
                color:selectedDeeplinkPlatform===p.id?'#fff':'rgba(255,255,255,0.5)'}">
              <span :style="{width:'8px',height:'8px',borderRadius:'50%',background:p.color,display:'inline-block',flexShrink:0}"></span>
              {{ p.label }}
            </button>
          </div>

          <!-- Platform behavior + phone mockup -->
          <div :style="{display:'grid',gridTemplateColumns:'1fr 200px',gap:'20px',marginBottom:'24px',alignItems:'start'}">
            <div>
              <div v-for="os in [{key:'ios',label:'iOS'},{key:'android',label:'Android'}]" :key="os.key"
                :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'10px',padding:'14px',marginBottom:'8px'}">
                <div :style="{display:'flex',alignItems:'center',gap:'8px',marginBottom:'6px'}">
                  <span style="font-size:16px">{{ os.key==='ios'?'🍎':'🤖' }}</span>
                  <p :style="{fontSize:'13px',fontWeight:700,color:'rgba(255,255,255,0.8)'}">{{ os.label }}</p>
                  <span :style="{background:'rgba(16,185,129,0.15)',border:'1px solid rgba(16,185,129,0.3)',color:'#10b981',borderRadius:'999px',padding:'1px 8px',fontSize:'10px',fontWeight:600}">Auto</span>
                </div>
                <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.4)',lineHeight:1.5}">
                  {{ (deeplinkPlatforms.find(p=>p.id===selectedDeeplinkPlatform) as any)?.[os.key] }}
                </p>
              </div>
            </div>

            <!-- Phone mockup -->
            <div>
              <div :style="{display:'flex',gap:'4px',background:'rgba(255,255,255,0.06)',borderRadius:'8px',padding:'2px',marginBottom:'10px'}">
                <button v-for="os in ['android','ios']" :key="os" @click="deeplinkPreviewOs=(os as 'ios'|'android')"
                  :style="{flex:1,padding:'4px',border:'none',borderRadius:'6px',cursor:'pointer',fontFamily:'inherit',fontSize:'11px',fontWeight:600,
                    background:deeplinkPreviewOs===os?'rgba(255,255,255,0.15)':'transparent',
                    color:deeplinkPreviewOs===os?'#fff':'rgba(255,255,255,0.35)'}">
                  {{ os==='ios'?'iOS':'Android' }}
                </button>
              </div>
              <div :style="{background:'#1a1a1a',borderRadius:'24px',padding:'5px',boxShadow:'0 0 0 1px #2a2a2a,0 16px 40px rgba(0,0,0,0.5)'}">
                <div :style="{borderRadius:'20px',overflow:'hidden',background:'#000',minHeight:'200px',position:'relative'}">
                  <!-- Status bar -->
                  <div :style="{height:'18px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
                    <div :style="{width:'40px',height:'5px',background:'#1a1a1a',borderRadius:'999px'}"></div>
                  </div>
                  <!-- Android dialog -->
                  <div v-if="deeplinkPreviewOs==='android'" :style="{padding:'12px 10px'}">
                    <div :style="{background:'#2d2d2d',borderRadius:'10px',padding:'14px',border:'1px solid rgba(255,255,255,0.1)'}">
                      <p :style="{fontSize:'9px',fontWeight:700,color:'rgba(255,255,255,0.9)',marginBottom:'5px'}">You're leaving our app</p>
                      <p :style="{fontSize:'8px',color:'rgba(255,255,255,0.5)',lineHeight:1.4,marginBottom:'12px'}">The website you're viewing is attempting to open an external app. Would you like to continue?</p>
                      <div :style="{display:'flex',gap:'6px',justifyContent:'flex-end'}">
                        <div :style="{padding:'4px 10px',border:'1px solid rgba(255,255,255,0.15)',borderRadius:'4px',fontSize:'8px',color:'rgba(255,255,255,0.5)',fontWeight:600}">GO BACK</div>
                        <div :style="{padding:'4px 10px',background:'#6D4EE8',borderRadius:'4px',fontSize:'8px',color:'#fff',fontWeight:700}">CONTINUE</div>
                      </div>
                    </div>
                  </div>
                  <!-- iOS redirect -->
                  <div v-else :style="{padding:'12px 10px'}">
                    <div :style="{background:'#1c1c1e',borderRadius:'8px',padding:'10px',marginBottom:'8px',border:'1px solid rgba(255,255,255,0.08)'}">
                      <div :style="{display:'flex',alignItems:'center',gap:'6px',marginBottom:'6px'}">
                        <div :style="{width:'16px',height:'16px',borderRadius:'4px',background:'#007AFF',flexShrink:0}"></div>
                        <p :style="{fontSize:'8px',color:'rgba(255,255,255,0.8)',fontWeight:600}">Opening in Safari...</p>
                      </div>
                      <div :style="{height:'4px',background:'rgba(255,255,255,0.1)',borderRadius:'2px',overflow:'hidden'}">
                        <div :style="{width:'70%',height:'100%',background:'#007AFF',borderRadius:'2px'}"></div>
                      </div>
                    </div>
                    <p :style="{fontSize:'7px',color:'rgba(255,255,255,0.3)',textAlign:'center'}">Redirecting to native browser</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Deeplink customization -->
          <p :style="{fontSize:'11px',fontWeight:700,color:'rgba(255,255,255,0.35)',textTransform:'uppercase',letterSpacing:'0.1em',marginBottom:'10px'}">Customize the Deeplink</p>

          <!-- 3-Dots Hint -->
          <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px',marginBottom:'8px'}">
            <div :style="{display:'flex',alignItems:'flex-start',justifyContent:'space-between',gap:'12px'}">
              <div>
                <p :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.8)',marginBottom:'3px'}">3-Dots Hint</p>
                <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.35)',lineHeight:1.5}">Shows a helper in the top-right corner telling users to open in their browser via the 3-dot menu.</p>
              </div>
              <div @click="form.deeplink_dots_hint=!form.deeplink_dots_hint"
                :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:form.deeplink_dots_hint?'#10b981':'rgba(255,255,255,0.15)',position:'relative',transition:'background 0.2s',flexShrink:0,marginTop:'2px'}">
                <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.deeplink_dots_hint?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
              </div>
            </div>
          </div>

          <!-- Long Press Button -->
          <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
            <div :style="{display:'flex',alignItems:'flex-start',justifyContent:'space-between',gap:'12px'}">
              <div>
                <p :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.8)',marginBottom:'3px'}">Long Press Button</p>
                <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.35)',lineHeight:1.5}">User must press 2 seconds on any 18+ link to open it outside the in-app browser.</p>
              </div>
              <div @click="form.deeplink_long_press=!form.deeplink_long_press"
                :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:form.deeplink_long_press?'#10b981':'rgba(255,255,255,0.15)',position:'relative',transition:'background 0.2s',flexShrink:0,marginTop:'2px'}">
                <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:form.deeplink_long_press?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== STEP 7 — PUBLISH (Advanced) ===== -->
        <div v-show="activeTab === 'advanced'">
          <h2 :style="{fontSize:'22px',fontWeight:700,color:'#fff',marginBottom:'16px'}">Advanced</h2>

          <!-- Sub-tabs -->
          <div :style="{display:'flex',gap:'4px',background:'rgba(255,255,255,0.06)',borderRadius:'10px',padding:'3px',marginBottom:'24px',width:'fit-content'}">
            <button v-for="st in [{id:'features',label:'Page Features'},{id:'sharing',label:'Sharing & Links'},{id:'analytics',label:'Analytics'},{id:'geo',label:'Geo Filter 🌐'}]" :key="st.id"
              @click="advancedSubTab=st.id"
              :style="{padding:'7px 18px',border:'none',borderRadius:'7px',fontSize:'12px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',
                background:advancedSubTab===st.id?'rgba(255,255,255,0.1)':'transparent',
                color:advancedSubTab===st.id?'#fff':'rgba(255,255,255,0.35)',
                boxShadow:advancedSubTab===st.id?'0 1px 4px rgba(0,0,0,0.3)':'none',
                transition:'all 0.15s'}">
              {{ st.label }}
            </button>
          </div>

          <div v-if="advancedSubTab==='features'" :style="{display:'flex',flexDirection:'column',gap:'10px'}">
            <div v-for="feat in advancedFeatures" :key="feat.key"
              :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px',display:'flex',alignItems:'flex-start',justifyContent:'space-between',gap:'12px'}">
              <div>
                <p :style="{fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.8)',marginBottom:'3px'}">{{ feat.label }}</p>
                <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.35)',lineHeight:1.5}">{{ feat.desc }}</p>
              </div>
              <div @click="(form as any)[feat.key]=!(form as any)[feat.key]"
                :style="{width:'40px',height:'22px',borderRadius:'999px',cursor:'pointer',background:(form as any)[feat.key]?'#6D4EE8':'rgba(255,255,255,0.15)',position:'relative',transition:'background 0.2s',flexShrink:0,marginTop:'2px'}">
                <div :style="{width:'16px',height:'16px',borderRadius:'50%',background:'#fff',position:'absolute',top:'3px',transition:'left 0.2s',left:(form as any)[feat.key]?'21px':'3px',boxShadow:'0 1px 3px rgba(0,0,0,0.3)'}"></div>
              </div>
            </div>
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'3px'}">🕒 Response Time</label>
              <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.3)',marginBottom:'8px'}">Displays "I reply in less than X minutes" on your first button.</p>
              <select v-model="form.response_time"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}">
                <option value="">Disabled</option>
                <option value="2">2 minutes</option>
                <option value="5">5 minutes</option>
                <option value="10">10 minutes</option>
                <option value="30">30 minutes</option>
                <option value="45">45 minutes</option>
              </select>
            </div>
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'6px'}">Promo Banner</label>
              <input v-model="form.promo_text" placeholder="e.g. 🔥 Limited offer — 50% off today!"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
            </div>
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'6px'}">Countdown End Date</label>
              <input v-model="form.countdown_end" type="datetime-local"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit',colorScheme:'dark'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
            </div>
          </div>

          <div v-if="advancedSubTab==='sharing'" :style="{display:'flex',flexDirection:'column',gap:'10px'}">
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'6px'}">SEO Title</label>
              <input v-model="form.seo_title" placeholder="Page title for search engines & social share"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
            </div>
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'6px'}">SEO Description</label>
              <textarea v-model="form.seo_description" rows="3" placeholder="Description for search engines and social share previews"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',resize:'none',fontFamily:'inherit',boxSizing:'border-box'}"
                @focus="($event.target as HTMLTextAreaElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLTextAreaElement).style.borderColor='rgba(255,255,255,0.1)'">
              </textarea>
            </div>
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'6px'}">SEO Image URL</label>
              <input v-model="form.seo_image" type="url" placeholder="https://..."
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
            </div>
          </div>

          <div v-if="advancedSubTab==='analytics'" :style="{display:'flex',flexDirection:'column',gap:'10px'}">
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'3px'}">Google Analytics (GA4)</label>
              <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.3)',marginBottom:'8px'}">Track page visits via your own GA4 property.</p>
              <input v-model="form.ga4_code" placeholder="G-XXXXXXXXXX"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
            </div>
            <div :style="{background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'12px',padding:'16px'}">
              <label :style="{display:'block',fontSize:'13px',fontWeight:600,color:'rgba(255,255,255,0.7)',marginBottom:'3px'}">Facebook Pixel ID</label>
              <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.3)',marginBottom:'8px'}">Track conversions and build retargeting audiences on Meta Ads.</p>
              <input v-model="form.fb_pixel_id" placeholder="123456789012345"
                :style="{background:'rgba(255,255,255,0.06)',border:'1px solid rgba(255,255,255,0.1)',borderRadius:'8px',padding:'9px 12px',fontSize:'13px',color:'#fff',width:'100%',outline:'none',boxSizing:'border-box',fontFamily:'inherit'}"
                @focus="($event.target as HTMLInputElement).style.borderColor='#6D4EE8'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(255,255,255,0.1)'" />
            </div>
            <div :style="{background:'rgba(109,78,232,0.06)',border:'1px solid rgba(109,78,232,0.2)',borderRadius:'12px',padding:'16px',textAlign:'center'}">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6D4EE8" stroke-width="1.5" :style="{margin:'0 auto 8px',display:'block'}"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
              <p :style="{fontSize:'12px',fontWeight:600,color:'rgba(255,255,255,0.6)',marginBottom:'3px'}">MySocialVSL analytics available after publishing</p>
              <p :style="{fontSize:'11px',color:'rgba(255,255,255,0.3)'}">Track clicks, views, and conversion rates once your page is live.</p>
            </div>
          </div>

          <div v-if="advancedSubTab==='geo'" :style="{display:'flex',flexDirection:'column',gap:'12px'}">
            <div :style="{background:'rgba(255,255,255,0.03)',border:'1px dashed rgba(255,255,255,0.12)',borderRadius:'14px',padding:'36px 24px',textAlign:'center'}">
              <div :style="{fontSize:'36px',marginBottom:'12px'}">🌐</div>
              <p :style="{fontSize:'15px',fontWeight:700,color:'rgba(255,255,255,0.7)',marginBottom:'6px'}">Geo Filter</p>
              <p :style="{fontSize:'12px',color:'rgba(255,255,255,0.35)',lineHeight:1.6,marginBottom:'16px'}">
                Redirect visitors from specific countries to different URLs.<br/>
                Example: 🇺🇸 USA → OnlyFans · 🇫🇷 France → MYM · 🌍 Rest of world → default
              </p>
              <div :style="{display:'inline-flex',alignItems:'center',gap:'8px',background:'rgba(239,68,68,0.1)',border:'1px solid rgba(239,68,68,0.2)',borderRadius:'999px',padding:'6px 14px'}">
                <span :style="{fontSize:'12px'}">🔒</span>
                <p :style="{fontSize:'12px',color:'#F87171',fontWeight:600}">Save your page first to unlock Geo Filter options</p>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- ===== RIGHT PREVIEW PANEL ===== -->
      <div :style="{width:'420px',background:'rgba(0,0,0,0.3)',borderLeft:'1px solid rgba(255,255,255,0.06)',display:'flex',flexDirection:'column',alignItems:'center',padding:'24px 20px',position:'sticky',top:0,height:'calc(100vh - 60px - 72px)',overflowY:'auto',flexShrink:0}">

        <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',width:'100%',marginBottom:'20px'}">
          <p :style="{fontSize:'12px',fontWeight:600,color:'rgba(255,255,255,0.6)',letterSpacing:'0.08em'}">Live Preview</p>
          <div :style="{display:'flex',gap:'2px',background:'rgba(255,255,255,0.08)',borderRadius:'8px',padding:'2px'}">
            <button @click="previewMode='mobile'"
              :style="{padding:'4px 14px',border:'none',borderRadius:'6px',fontSize:'11px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',
                background:previewMode==='mobile'?'rgba(255,255,255,0.15)':'transparent',
                color:previewMode==='mobile'?'#fff':'rgba(255,255,255,0.35)'}">
              Mobile
            </button>
            <button @click="previewMode='desktop'"
              :style="{padding:'4px 14px',border:'none',borderRadius:'6px',fontSize:'11px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',
                background:previewMode==='desktop'?'rgba(255,255,255,0.15)':'transparent',
                color:previewMode==='desktop'?'#fff':'rgba(255,255,255,0.35)'}">
              Desktop
            </button>
          </div>
        </div>

        <!-- Phone mockup -->
        <div v-if="previewMode==='mobile'"
          :style="{width:'260px',background:'#1a1a1a',borderRadius:'44px',padding:'9px',boxShadow:'0 0 0 1px #2a2a2a,0 32px 80px rgba(0,0,0,0.6),0 0 60px rgba(109,78,232,0.2)',flexShrink:0}">
          <div :style="{borderRadius:'36px',overflow:'hidden'}">
            <div :style="{height:'26px',display:'flex',alignItems:'center',justifyContent:'center',background:'#000'}">
              <div :style="{width:'56px',height:'7px',background:'#1a1a1a',borderRadius:'999px'}"></div>
            </div>
            <div :style="{minHeight:'480px',display:'flex',flexDirection:'column',alignItems:'center',padding:'14px 10px 16px',overflowY:'auto',position:'relative',background:currentTheme?.bg||form.bg_color||'#0d0d1a'}">
              <div v-if="currentTheme?.bgGradient" :style="{position:'absolute',inset:0,backgroundImage:currentTheme.bgGradient}"></div>
              <div v-if="form.bg_image_url" :style="{position:'absolute',inset:0,backgroundImage:`url(${form.bg_image_url})`,backgroundSize:'cover',backgroundPosition:'center',opacity:0.3}"></div>
              <div v-if="form.online_status" :style="{display:'flex',alignItems:'center',gap:'3px',background:'rgba(16,185,129,0.15)',border:'1px solid rgba(16,185,129,0.3)',borderRadius:'999px',padding:'3px 8px',marginBottom:'10px',position:'relative',zIndex:1,flexShrink:0}">
                <div :style="{width:'5px',height:'5px',borderRadius:'50%',background:'#10B981'}"></div>
                <span :style="{fontSize:'9px',color:'#10B981',fontWeight:600}">Online</span>
              </div>
              <div v-if="form.show_avatar" :style="{marginBottom:'8px',position:'relative',zIndex:1,flexShrink:0}">
                <div :style="{width:'60px',height:'60px',borderRadius:'50%',background:'#333',overflow:'hidden',border:'2px solid rgba(255,255,255,0.15)'}">
                  <img v-if="form.avatar_url" :src="form.avatar_url" :style="{width:'100%',height:'100%',objectFit:'cover'}" />
                  <div v-else :style="{width:'100%',height:'100%',display:'flex',alignItems:'center',justifyContent:'center'}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  </div>
                </div>
                <div v-if="form.verified_badge" :style="{position:'absolute',bottom:0,right:0,width:'16px',height:'16px',background:'#3B82F6',borderRadius:'50%',border:'2px solid #000',display:'flex',alignItems:'center',justifyContent:'center'}">
                  <svg width="7" height="7" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5 3.5-4" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
              </div>
              <p :style="{fontSize:'13px',fontWeight:700,color:'#fff',marginBottom:'3px',position:'relative',zIndex:1}">{{ form.model_name || 'Your Name' }}</p>
              <p v-if="form.bio" :style="{fontSize:'10px',color:'rgba(255,255,255,0.5)',textAlign:'center',lineHeight:1.4,marginBottom:'8px',maxWidth:'180px',position:'relative',zIndex:1}">{{ form.bio.slice(0,80) }}{{ form.bio.length>80?'...':'' }}</p>
              <div v-if="form.promo_text" :style="{background:'rgba(239,68,68,0.15)',border:'1px solid rgba(239,68,68,0.3)',borderRadius:'6px',padding:'4px 8px',marginBottom:'8px',position:'relative',zIndex:1}">
                <p :style="{fontSize:'9px',color:'#F87171',textAlign:'center',fontWeight:600}">{{ form.promo_text }}</p>
              </div>
              <div v-if="form.video_url" :style="{width:'100%',borderRadius:'8px',overflow:'hidden',background:'linear-gradient(135deg,#1e3a5f,#000)',marginBottom:'8px',height:'70px',display:'flex',alignItems:'center',justifyContent:'center',position:'relative',zIndex:1,flexShrink:0}">
                <div :style="{width:'22px',height:'22px',background:'rgba(255,255,255,0.9)',borderRadius:'50%',display:'flex',alignItems:'center',justifyContent:'center'}">
                  <svg width="8" height="8" viewBox="0 0 24 24" fill="#111"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
              </div>
              <div :style="{width:'100%',display:'flex',flexDirection:'column',gap:'6px',position:'relative',zIndex:1}">
                <template v-if="previewLinks.length > 0">
                  <div v-if="previewLinks[0]"
                    :style="{
                      width:'100%',padding:'8px 10px',
                      borderRadius:form.btn_style==='pill'?'999px':form.btn_style==='square'?'4px':'10px',
                      textAlign:'center',fontSize:'11px',fontWeight:700,color:'#fff',
                      background:`linear-gradient(90deg,${form.btn_color||currentTheme?.btnColor||'#6D4EE8'},#A78BFA,${form.btn_color||currentTheme?.btnColor||'#6D4EE8'})`,
                      backgroundSize:'200% auto',
                      animation:'shimmer-btn 2.5s linear infinite',
                      boxSizing:'border-box',
                    }">{{ previewLinks[0].label }}</div>
                  <div v-if="previewLinks[1]"
                    :style="{
                      width:'100%',padding:'8px 10px',
                      borderRadius:form.btn_style==='pill'?'999px':form.btn_style==='square'?'4px':'10px',
                      textAlign:'center',fontSize:'11px',fontWeight:700,color:'#fff',
                      background:form.btn_color||currentTheme?.btnColor||'#6D4EE8',
                      animation:'pulse-btn 2s ease-in-out infinite',
                      boxSizing:'border-box',
                    }">{{ previewLinks[1].label }}</div>
                  <div v-for="link in previewLinks.slice(2)" :key="link.type+link.label"
                    :style="{
                      width:'100%',padding:'8px 10px',
                      borderRadius:form.btn_style==='pill'?'999px':form.btn_style==='square'?'4px':'10px',
                      textAlign:'center',fontSize:'11px',fontWeight:700,color:'#fff',
                      background:form.btn_color||currentTheme?.btnColor||'#6D4EE8',
                      animation:'glow-btn 2.5s ease-in-out infinite',
                      boxSizing:'border-box',
                    }">{{ link.label }}</div>
                </template>
                <template v-else>
                  <div :style="{width:'100%',height:'32px',borderRadius:'10px',background:'rgba(255,255,255,0.07)',border:'1px dashed rgba(255,255,255,0.12)'}"></div>
                  <div :style="{width:'100%',height:'32px',borderRadius:'10px',background:'rgba(255,255,255,0.07)',border:'1px dashed rgba(255,255,255,0.12)'}"></div>
                </template>
              </div>
              <p v-if="form.response_time" :style="{fontSize:'9px',color:'rgba(255,255,255,0.25)',marginTop:'8px',position:'relative',zIndex:1,textAlign:'center'}">💬 {{ form.response_time }}</p>
            </div>
            <div :style="{height:'22px',background:'#000',display:'flex',alignItems:'center',justifyContent:'center'}">
              <div :style="{width:'72px',height:'4px',background:'#1a1a1a',borderRadius:'999px'}"></div>
            </div>
          </div>
        </div>

        <!-- Desktop mockup -->
        <div v-if="previewMode==='desktop'" :style="{width:'100%',border:'1px solid rgba(255,255,255,0.08)',borderRadius:'10px',overflow:'hidden',boxShadow:'0 20px 60px rgba(0,0,0,0.4)'}">
          <div :style="{background:'#1a1a2e',padding:'7px 10px',display:'flex',alignItems:'center',gap:'5px',borderBottom:'1px solid rgba(255,255,255,0.06)'}">
            <div v-for="c in ['#EF4444','#F59E0B','#22C55E']" :key="c" :style="{width:'7px',height:'7px',borderRadius:'50%',background:c}"></div>
            <div :style="{flex:1,background:'rgba(255,255,255,0.07)',borderRadius:'3px',padding:'2px 8px',fontSize:'9px',color:'rgba(255,255,255,0.25)',marginLeft:'4px',overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap'}">
              mysocialvsl.com/{{ form.slug || form.dashboard_name?.toLowerCase().replace(/\s+/g,'-') || '...' }}
            </div>
          </div>
          <div :style="{background:currentTheme?.bg||form.bg_color||'#0d0d1a',padding:'20px 16px',display:'flex',flexDirection:'column',alignItems:'center',minHeight:'240px',position:'relative'}">
            <div v-if="currentTheme?.bgGradient" :style="{position:'absolute',inset:0,backgroundImage:currentTheme.bgGradient}"></div>
            <div v-if="form.show_avatar" :style="{width:'44px',height:'44px',borderRadius:'50%',background:'#333',marginBottom:'6px',overflow:'hidden',border:'2px solid rgba(255,255,255,0.15)',position:'relative',zIndex:1,flexShrink:0}">
              <img v-if="form.avatar_url" :src="form.avatar_url" :style="{width:'100%',height:'100%',objectFit:'cover'}" />
            </div>
            <p :style="{fontSize:'12px',fontWeight:700,color:'#fff',marginBottom:'2px',position:'relative',zIndex:1}">{{ form.model_name || 'Your Name' }}</p>
            <p v-if="form.bio" :style="{fontSize:'9px',color:'rgba(255,255,255,0.45)',marginBottom:'10px',textAlign:'center',maxWidth:'160px',position:'relative',zIndex:1}">{{ form.bio.slice(0,60) }}</p>
            <div :style="{width:'100%',display:'flex',flexDirection:'column',gap:'5px',position:'relative',zIndex:1}">
              <div v-for="link in previewLinks" :key="link.type+link.label"
                :style="{width:'100%',padding:'7px',borderRadius:form.btn_style==='pill'?'999px':form.btn_style==='square'?'3px':'6px',textAlign:'center',fontSize:'10px',fontWeight:600,color:'#fff',background:form.btn_color||currentTheme?.btnColor||'#6D4EE8',boxSizing:'border-box'}">
                {{ link.label }}
              </div>
            </div>
          </div>
        </div>

        <!-- Template badge -->
        <div :style="{marginTop:'16px',padding:'5px 14px',background:'rgba(255,255,255,0.06)',borderRadius:'999px',fontSize:'12px',color:'rgba(255,255,255,0.45)'}">
          {{ currentTheme?.name || 'No theme' }}
        </div>

      </div>
    </div>

    <!-- ===== BOTTOM BAR ===== -->
    <div :style="{height:'72px',background:'#0d0b1e',borderTop:'1px solid rgba(255,255,255,0.06)',display:'flex',alignItems:'center',justifyContent:'space-between',padding:'0 40px',flexShrink:0,fontFamily:'Inter,sans-serif'}">
      <!-- Back button -->
      <button v-if="currentStepIndex > 0" @click="prevStep"
        :style="{padding:'10px 22px',border:'1px solid rgba(255,255,255,0.15)',borderRadius:'10px',background:'transparent',color:'#fff',fontSize:'14px',fontWeight:600,cursor:'pointer',fontFamily:'inherit',display:'flex',alignItems:'center',gap:'6px'}"
        @mouseover="($event.currentTarget as HTMLElement).style.background='rgba(255,255,255,0.06)'"
        @mouseout="($event.currentTarget as HTMLElement).style.background='transparent'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
        Back
      </button>
      <div v-else></div>

      <!-- Dots indicator -->
      <div :style="{display:'flex',gap:'6px',alignItems:'center'}">
        <div v-for="(step, i) in steps" :key="step.id"
          :style="{width:i===currentStepIndex?'20px':'6px',height:'6px',borderRadius:'999px',background:i===currentStepIndex?'#6D4EE8':i<currentStepIndex?'rgba(109,78,232,0.5)':'rgba(255,255,255,0.15)',transition:'all 0.2s'}">
        </div>
      </div>

      <!-- Continue / Publish -->
      <button @click="nextStep" :disabled="saving"
        :style="{padding:'10px 28px',background:'#6D4EE8',color:'#fff',border:'none',borderRadius:'10px',fontSize:'14px',fontWeight:700,cursor:saving?'not-allowed':'pointer',fontFamily:'inherit',opacity:saving?0.7:1,display:'flex',alignItems:'center',gap:'6px'}">
        <span v-if="currentStepIndex < steps.length - 1">Continue →</span>
        <span v-else>{{ saving ? 'Publishing...' : '🚀 Publish' }}</span>
      </button>
    </div>

  </DashboardLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/lib/axios'
import VideoUpload from '@/components/VideoUpload.vue'
import PresetPicker from '@/components/PresetPicker.vue'
import DashboardLayout from '@/components/DashboardLayout.vue'
import SocialPlatformPreview from '@/components/SocialPlatformPreview.vue'

const router = useRouter()

// ===== STATE =====
const saving = ref(false)
const error = ref('')
const activeTab = ref('basics')
const advancedSubTab = ref('features')
const previewMode = ref<'mobile'|'desktop'>('mobile')
const dragOverBg = ref(false)
const uploadingAvatar = ref(false)
const uploadingBg = ref(false)
const manualUrl = ref('')
const showAdvancedSettings = ref(false)
const showThemeAdvanced = ref(true)
const showLinkPicker = ref(false)
const hoveredPlatform = ref<string|null>('instagram')

// ===== STATIC DATA =====
const themes = [
  { id:'ethereal',    name:'Ethereal',    bg:'#f0e6ff', btnColor:'#7C3AED', bgGradient:null },
  { id:'dreamy',      name:'Dreamy',      bg:'#ffe0f0', btnColor:'#DB2777', bgGradient:null },
  { id:'nightfall',   name:'Nightfall',   bg:'#0d0d1a', btnColor:'#818CF8', bgGradient:'linear-gradient(160deg,#0d0d2b 0%,#0d0d1a 100%)' },
  { id:'bold',        name:'Bold',        bg:'#0a0a0a', btnColor:'#EF4444', bgGradient:null },
  { id:'hero',        name:'Hero',        bg:'#1a1a2e', btnColor:'#6366F1', bgGradient:'linear-gradient(135deg,#1a1a2e,#16213e)' },
  { id:'bento',       name:'Bento',       bg:'#fff',    btnColor:'#111827', bgGradient:null },
  { id:'terrain',     name:'Terrain',     bg:'#1c2b1a', btnColor:'#4ADE80', bgGradient:'linear-gradient(135deg,#1c2b1a,#0f1f0e)' },
  { id:'luminescent', name:'Luminescent', bg:'#001a33', btnColor:'#22D3EE', bgGradient:'linear-gradient(135deg,#001a33,#002952)' },
  { id:'luggage-tag', name:'Luggage Tag', bg:'#f5f0e8', btnColor:'#92400E', bgGradient:null },
  { id:'clay',        name:'Clay',        bg:'#f0ede8', btnColor:'#78716C', bgGradient:null },
]

const themeGradients: Record<string, string> = {
  'ethereal':    'linear-gradient(135deg,#c084fc,#818cf8,#f0abfc)',
  'dreamy':      'linear-gradient(135deg,#f9a8d4,#c084fc,#f472b6)',
  'nightfall':   'linear-gradient(135deg,#1e1b4b,#312e81,#4f46e5)',
  'bold':        'linear-gradient(135deg,#450a0a,#7f1d1d,#ef4444)',
  'hero':        'linear-gradient(135deg,#0f172a,#1e3a5f,#3b82f6)',
  'bento':       'linear-gradient(135deg,#f3f4f6,#e5e7eb,#d1d5db)',
  'terrain':     'linear-gradient(135deg,#052e16,#14532d,#4ade80)',
  'luminescent': 'linear-gradient(135deg,#0c4a6e,#0e7490,#22d3ee)',
  'luggage-tag': 'linear-gradient(135deg,#78350f,#b45309,#fbbf24)',
  'clay':        'linear-gradient(135deg,#292524,#44403c,#a8a29e)',
}

const linkTypes = [
  { id:'classic',  name:'Classic Link',    desc:'Standard button with icon and text' },
  { id:'image',    name:'Button Image',    desc:'Custom image as button' },
  { id:'product',  name:'Product Button',  desc:'Product card with price' },
  { id:'creator',  name:'Creator Card',    desc:'Profile-style card' },
  { id:'carousel', name:'Image Carousel',  desc:'Sliding image gallery' },
  { id:'grid',     name:'Image Grid',      desc:'Grid of clickable images' },
  { id:'tip',      name:'Tip Me',          desc:'Accept tips from your audience' },
]

const socialOptions = [
  { type:'onlyfans',  color:'#00AFF0', name:'OnlyFans',  label:'My OnlyFans',  icon:'📸', svg:'<svg viewBox="0 0 24 24" fill="white" width="14" height="14"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 5.838a6.162 6.162 0 1 1 0 12.324A6.162 6.162 0 0 1 12 5.838zm0 2.456a3.706 3.706 0 1 0 0 7.412 3.706 3.706 0 0 0 0-7.412zm6.31-3.853a1.228 1.228 0 1 1 0 2.456 1.228 1.228 0 0 1 0-2.456z"/></svg>' },
  { type:'instagram', color:'#E1306C', name:'Instagram', label:'My Instagram', icon:'📸', svg:'<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" width="14" height="14"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="white" stroke="none"/></svg>' },
  { type:'tiktok',    color:'#010101', name:'TikTok',    label:'My TikTok',    icon:'🎵', svg:'<svg viewBox="0 0 24 24" fill="white" width="14" height="14"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>' },
  { type:'twitter',   color:'#000000', name:'Twitter/X', label:'My X',         icon:'✕',  svg:'<svg viewBox="0 0 24 24" fill="white" width="14" height="14"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' },
  { type:'mym',       color:'#E8A020', name:'MYM',       label:'My MYM',       icon:'💛', svg:'<svg viewBox="0 0 24 24" fill="white" width="14" height="14"><text x="2" y="18" font-size="16" font-weight="bold" font-family="Arial">M</text></svg>' },
  { type:'telegram',  color:'#26A5E4', name:'Telegram',  label:'My Telegram',  icon:'✈️', svg:'<svg viewBox="0 0 24 24" fill="white" width="14" height="14"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>' },
  { type:'youtube',   color:'#FF0000', name:'YouTube',   label:'My YouTube',   icon:'▶️', svg:'<svg viewBox="0 0 24 24" fill="white" width="14" height="14"><path d="M23 7s-.3-2-1.2-2.7c-1.1-1.2-2.4-1.2-3-1.3C16.6 3 12 3 12 3s-4.6 0-6.8.1c-.6.1-1.9.1-3 1.3C1.3 5 1 7 1 7S.7 9.2.7 11.5v2.1c0 2.2.3 4.4.3 4.4s.3 2 1.2 2.7c1.1 1.2 2.6 1.1 3.3 1.2C7.5 22 12 22 12 22s4.6 0 6.8-.2c.6-.1 1.9-.1 3-1.3.9-.7 1.2-2.7 1.2-2.7s.3-2.2.3-4.4v-2.1C23.3 9.2 23 7 23 7zM9.7 15.5V8.4l8.1 3.6-8.1 3.5z"/></svg>' },
  { type:'twitch',    color:'#9146FF', name:'Twitch',    label:'My Twitch',    icon:'💜', svg:'<svg viewBox="0 0 24 24" fill="white" width="14" height="14"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>' },
  { type:'snapchat',  color:'#FFFC00', name:'Snapchat',  label:'My Snapchat',  icon:'👻', svg:'<i class="bi bi-snapchat" style="color:#000;font-size:14px;line-height:1"></i>' },
]

const bgSwatches = [
  {color:'#7C3AED'},{color:'#0284C7'},{color:'#0d0d1a'},{color:'#1a1a2e'},{color:'#f0e6ff'},{color:'#ffffff'},
]

const fontOptions = [
  { id:'auto',       label:'Auto',      family:'Inter,sans-serif' },
  { id:'inter',      label:'Inter',     family:'Inter,sans-serif' },
  { id:'poppins',    label:'Poppins',   family:'Poppins,sans-serif' },
  { id:'montserrat', label:'Montserrat',family:'Montserrat,sans-serif' },
  { id:'outfit',     label:'Outfit',    family:'Outfit,sans-serif' },
  { id:'dm',         label:'DM Sans',   family:'\'DM Sans\',sans-serif' },
]

const advancedFeatures = [
  { key:'online_status',      label:'🟢 Online Status',      desc:'Show a green "Online now" indicator — boosts clicks on exclusive content.' },
  { key:'city_geoip',         label:'📍 City Display',        desc:'Use GeoIP to show the visitor\'s city — makes fans feel closer to you.' },
  { key:'age_gate',           label:'🔞 Age Gate',            desc:'Show age verification before visitors can view your page.' },
  { key:'disable_link_logos', label:'🚫 Disable Link Logos',  desc:'Hide platform logos in your buttons (OnlyFans, Instagram, etc.).' },
  { key:'hide_branding',      label:'✨ Hide Branding',       desc:'Remove "Powered by MySocialVSL" from your page (Pro).' },
  { key:'traffic_recovery',   label:'♻️ Traffic Recovery',   desc:'Recover visitors who close the page without clicking.' },
]

const deeplinkPlatforms = [
  { id:'instagram', label:'Instagram', color:'#E1306C', ios:'Users are automatically redirected to their default browser.', android:'Users are automatically redirected to their default browser.' },
  { id:'tiktok',    label:'TikTok',    color:'#010101', ios:'Users are redirected to Safari via a deeplink trigger.',       android:'A system dialog prompts users to open in Chrome.' },
  { id:'twitter',   label:'Twitter/X', color:'#000000', ios:'Users are redirected to their default browser.',               android:'Users are redirected to their default browser.' },
  { id:'reddit',    label:'Reddit',    color:'#FF4500', ios:'Users are redirected to Safari.',                               android:'A dialog prompts to open in external browser.' },
  { id:'facebook',  label:'Facebook',  color:'#1877F2', ios:'Users are redirected to Safari via deeplink.',                 android:'A "Leave app?" dialog appears before redirect.' },
  { id:'messenger', label:'Messenger', color:'#0084FF', ios:'Users are redirected to their default browser.',               android:'A dialog prompts users to open externally.' },
  { id:'snapchat',  label:'Snapchat',  color:'#FFFC00', ios:'Users are redirected to Safari.',                               android:'Users are redirected to their default browser.' },
  { id:'telegram',  label:'Telegram',  color:'#26A5E4', ios:'Users are redirected to Safari.',                               android:'Users are redirected to their default browser.' },
  { id:'pinterest', label:'Pinterest', color:'#E60023', ios:'Users are redirected to their default browser.',               android:'Users are redirected to their default browser.' },
]
const selectedDeeplinkPlatform = ref('instagram')
const deeplinkPreviewOs = ref<'ios'|'android'>('android')

// ===== FORM =====
const form = ref({
  page_type: 'landing' as 'landing'|'direct',
  direct_url: '',
  slug: '',
  dashboard_name: '',
  group_name: '',
  bot_protection: false,
  deep_link_enabled: true,
  strict_deep_link: false,
  deeplink_dots_hint: true,
  deeplink_long_press: true,
  city_geoip: false,
  disable_link_logos: false,
  ga4_code: '',
  fb_pixel_id: '',
  template: 'nightfall',
  bg_color: '#0d0d1a',
  btn_color: '#818CF8',
  text_color: 'light',
  btn_style: 'rounded',
  font: 'auto',
  avatar_url: '',
  bg_image_url: '',
  show_avatar: true,
  verified_badge: false,
  model_name: '',
  bio: '',
  video_url: '',
  online_status: false,
  location_display: '0',
  response_time: '',
  promo_text: '',
  countdown_end: '',
  age_gate: false,
  hide_branding: false,
  traffic_recovery: false,
  seo_title: '',
  seo_description: '',
  seo_image: '',
  links: [] as Array<{
    type: string
    label: string
    url: string
    description?: string
    age_restricted: boolean
    is_social?: boolean
    container_style?: string
    animation?: string
    grid_size?: string
    hide_logo?: boolean
    title_color?: string
    desc_color?: string
    _open?: boolean
  }>,
})

// ===== COMPUTED =====
const steps = computed(() =>
  form.value.page_type === 'direct'
    ? [{id:1,label:'Type'},{id:2,label:'Deeplink'},{id:3,label:'Publish'}]
    : [{id:1,label:'Type'},{id:2,label:'Theme'},{id:3,label:'Profile'},{id:4,label:'Links'},{id:5,label:'Socials'},{id:6,label:'Deeplink'},{id:7,label:'Publish'}]
)
const tabOrder = computed(() =>
  form.value.page_type === 'direct'
    ? ['basics','deeplink','advanced']
    : ['basics','theme','profile','content','socials','deeplink','advanced']
)
const currentStepIndex = computed(() => tabOrder.value.indexOf(activeTab.value))
const currentStepNumber = computed(() => currentStepIndex.value + 1)

const TRIGGER_WORDS = ['onlyfans','only fans','fanvue','mym','fansly','uncove','18+','xxx','nude','naked','nsfw','adult','explicit','sex','porn','escort','leaked']
const triggerWordsInBio = computed(() => {
  const bio = (form.value.bio || '').toLowerCase()
  return TRIGGER_WORDS.filter(w => bio.includes(w))
})

const currentTheme = computed(() => themes.find(t => t.id === form.value.template))
const socialLinks = computed(() => form.value.links.filter(l => l.is_social))
const contentLinks = computed(() => form.value.links.filter(l => !l.is_social))
const previewLinks = computed(() => form.value.links.slice(0, 3))

// ===== NAVIGATION =====
function nextStep() {
  const next = tabOrder.value[currentStepIndex.value + 1]
  if (next) activeTab.value = next
  else save()
}
function prevStep() {
  const prev = tabOrder.value[currentStepIndex.value - 1]
  if (prev) activeTab.value = prev
}
function goToStep(i: number) {
  const tab = tabOrder.value[i]
  if (tab) activeTab.value = tab
}

// ===== THEME =====
function applyTheme(t: typeof themes[0]) {
  form.value.template = t.id
  form.value.bg_color  = t.bg
  form.value.btn_color = t.btnColor
}

const selectedPreset = ref<string|null>(null)
function applyPreset(p: any) {
  selectedPreset.value = p.id
  form.value.template  = p.apply.template
  form.value.bg_color  = p.apply.bg_color
  form.value.btn_color = p.apply.btn_color
  if (p.btnStyle) form.value.btn_style = p.btnStyle
}

// ===== LINKS =====
function addLink(typeId: string) {
  const lt = linkTypes.find(l => l.id === typeId)
  form.value.links.push({
    type: typeId,
    label: lt?.name || 'New Link',
    url: '',
    description: '',
    age_restricted: false,
    is_social: false,
    _open: true,
  })
}

function removeLink(link: typeof form.value.links[0]) {
  const idx = form.value.links.indexOf(link)
  if (idx >= 0) form.value.links.splice(idx, 1)
}

// ===== SOCIALS =====
function isSocialSelected(type: string): boolean {
  return form.value.links.some(l => l.type === type && l.is_social)
}

function toggleSocial(s: typeof socialOptions[0]) {
  const idx = form.value.links.findIndex(l => l.type === s.type && l.is_social)
  if (idx >= 0) {
    form.value.links.splice(idx, 1)
  } else {
    form.value.links.push({ type: s.type, label: s.label, url: '', age_restricted: false, is_social: true })
  }
}

function removeSocial(type: string) {
  const idx = form.value.links.findIndex(l => l.type === type && l.is_social)
  if (idx >= 0) form.value.links.splice(idx, 1)
}

function getSocialColor(type: string): string {
  return socialOptions.find(s => s.type === type)?.color || '#6D4EE8'
}

function getSocialSvg(type: string): string {
  return socialOptions.find(s => s.type === type)?.svg || ''
}

function addManualUrl() {
  const url = manualUrl.value.trim()
  if (!url) return
  const platforms = [
    { key:'onlyfans',  matches:['onlyfans.com'] },
    { key:'mym',       matches:['mym.fans'] },
    { key:'instagram', matches:['instagram.com'] },
    { key:'tiktok',    matches:['tiktok.com'] },
    { key:'twitter',   matches:['twitter.com','x.com'] },
    { key:'telegram',  matches:['t.me','telegram.me'] },
    { key:'youtube',   matches:['youtube.com','youtu.be'] },
    { key:'twitch',    matches:['twitch.tv'] },
    { key:'snapchat',  matches:['snapchat.com','snap.com'] },
  ]
  let type = 'classic'
  let label = 'My Link'
  for (const p of platforms) {
    if (p.matches.some(m => url.includes(m))) {
      type  = p.key
      label = socialOptions.find(s => s.type === p.key)?.name || p.key
      break
    }
  }
  const existing = form.value.links.find(l => l.type === type && l.is_social)
  if (existing) {
    existing.url = url
  } else {
    form.value.links.push({ type, label, url, age_restricted: false, is_social: true })
  }
  manualUrl.value = ''
}

// ===== UPLOAD =====
async function uploadFile(event: Event | { target: { files: File[] } }, type: 'avatar' | 'background') {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return
  const fd = new FormData()
  fd.append('file', file)
  fd.append('type', type)
  if (type === 'avatar') uploadingAvatar.value = true
  else uploadingBg.value = true
  try {
    const { data } = await api.post('/upload/image', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    if (type === 'avatar') form.value.avatar_url = data.url
    else form.value.bg_image_url = data.url
  } catch (e: any) {
    error.value = 'Upload error: ' + (e.response?.data?.message || e.message || 'Upload failed')
  } finally {
    if (type === 'avatar') uploadingAvatar.value = false
    else uploadingBg.value = false
  }
}

function onDrop(event: DragEvent, type: 'avatar' | 'background') {
  dragOverBg.value = false
  const file = event.dataTransfer?.files[0]
  if (file) uploadFile({ target: { files: [file] } } as any, type)
}

// ===== SAVE =====
async function save() {
  saving.value = true
  error.value = ''
  try {
    const payload: any = {
      model_name: form.value.model_name || form.value.dashboard_name,
      slug: form.value.slug || undefined,
      group_name: form.value.group_name || undefined,
      page_type: form.value.page_type,
      direct_url: form.value.direct_url || undefined,
      template: form.value.template,
      bg_color: form.value.bg_color,
      btn_color: form.value.btn_color,
      avatar_url: form.value.avatar_url || undefined,
      bg_image_url: form.value.bg_image_url || undefined,
      show_avatar: form.value.show_avatar,
      verified_badge: form.value.verified_badge,
      bio: form.value.bio || undefined,
      video_url: form.value.video_url || undefined,
      deep_link_enabled: form.value.deep_link_enabled,
      age_gate: form.value.age_gate,
      online_status: form.value.online_status,
      response_time: form.value.response_time || undefined,
      promo_text: form.value.promo_text || undefined,
      countdown_end: form.value.countdown_end || null,
      is_active: true,
    }
    payload.links = form.value.links
      .filter((l: any) => l.url)
      .map((l: any, i: number) => ({
        type: l.is_social ? 'social' : (l.type || 'classic'),
        label: l.label || l.type || 'Link',
        url: l.url,
        order: i,
      }))
    const { data } = await api.post('/pages', payload)
    router.push(`/pages/${data.id}/edit`)
  } catch (e: any) {
    if (e.response?.data?.error === 'plan_limit') { router.push('/billing?limit=1'); return }
    const errors = e.response?.data?.errors
    error.value = errors ? Object.values(errors).flat().join(' ') : (e.response?.data?.message || 'Error creating page.')
    activeTab.value = 'basics'
    window.scrollTo(0, 0)
  } finally {
    saving.value = false
  }
}
</script>

<style>
@keyframes shimmer {
  0%   { background-position: 200% 0 }
  100% { background-position: -200% 0 }
}
@keyframes shimmer-btn {
  0%   { background-position: -200% center }
  100% { background-position: 200% center }
}
@keyframes pulse-btn {
  0%, 100% { transform: scale(1); }
  50%       { transform: scale(1.02); }
}
@keyframes glow-btn {
  0%, 100% { box-shadow: 0 0 6px rgba(109,78,232,0.3) }
  50%       { box-shadow: 0 0 20px rgba(109,78,232,0.7) }
}
</style>
