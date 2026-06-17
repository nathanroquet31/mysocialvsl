<template>
  <DashboardLayout :noPadding="true">

    <template #header-left>
      <div style="display:flex;align-items:center;gap:14px">
        <RouterLink to="/dashboard/links" class="header-back-btn">
          <i class="bi bi-arrow-left" style="font-size:11px"></i> Back
        </RouterLink>
        <div style="width:1px;height:20px;background:rgba(255,255,255,0.08)"></div>
        <div style="display:flex;align-items:center;gap:8px">
          <div v-if="page" style="width:26px;height:26px;border-radius:8px;overflow:hidden;border:1px solid rgba(255,255,255,0.1);flex-shrink:0;background:#1a1a2e">
            <img v-if="form.avatar_url" :src="form.avatar_url" style="width:100%;height:100%;object-fit:cover" />
            <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center"><i class="bi bi-link-45deg" style="font-size:12px;color:rgba(255,255,255,0.3)"></i></div>
          </div>
          <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:-0.01em">{{ page ? page.model_name || 'Edit Link' : 'Edit Link' }}</span>
          <span v-if="form.is_active" style="display:inline-flex;align-items:center;gap:4px;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);border-radius:999px;padding:2px 8px;font-size:10px;font-weight:700;color:#34d399">
            <span style="width:4px;height:4px;border-radius:50%;background:#34d399;display:inline-block"></span> Live
          </span>
          <span v-else style="display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:999px;padding:2px 8px;font-size:10px;font-weight:700;color:rgba(255,255,255,0.35)">
            Draft
          </span>
        </div>
      </div>
    </template>

    <template #header-actions>
      <div style="display:flex;align-items:center;gap:8px">
        <a v-if="page" :href="`/p/${page.slug}`" target="_blank" class="header-open-btn">
          <i class="bi bi-eye" style="font-size:12px"></i> Preview
        </a>
        <button @click="save" :disabled="saving || videoUploading" class="header-save-btn"
          :style="{opacity:(saving||videoUploading)?0.6:1}">
          <i v-if="saving" class="bi bi-arrow-repeat" style="font-size:12px;animation:spin 0.8s linear infinite"></i>
          <i v-else class="bi bi-cloud-upload" style="font-size:12px"></i>
          {{ saving ? 'Saving...' : videoUploading ? 'Uploading...' : 'Save changes' }}
        </button>
      </div>
    </template>

    <!-- Main content -->
    <div style="display:flex;flex-direction:column;height:100%;overflow:hidden">

      <!-- Alerts -->
      <Transition name="alert-slide">
        <div v-if="saved" style="display:flex;align-items:center;gap:10px;background:rgba(16,185,129,0.08);border-bottom:1px solid rgba(16,185,129,0.15);color:#34d399;padding:10px 28px;font-size:12px;font-weight:500;flex-shrink:0">
          <div style="width:18px;height:18px;border-radius:50%;background:rgba(16,185,129,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-check" style="font-size:11px"></i>
          </div>
          Changes saved successfully.
          <span style="margin-left:auto;font-size:11px;color:rgba(52,211,153,0.5)">just now</span>
        </div>
      </Transition>
      <Transition name="alert-slide">
        <div v-if="error" style="display:flex;align-items:center;gap:10px;background:rgba(239,68,68,0.08);border-bottom:1px solid rgba(239,68,68,0.15);color:#f87171;padding:10px 28px;font-size:12px;font-weight:500;flex-shrink:0">
          <i class="bi bi-exclamation-triangle" style="font-size:14px;flex-shrink:0"></i>
          {{ error }}
          <button @click="error=''" style="margin-left:auto;background:none;border:none;color:#f87171;cursor:pointer;font-size:14px;padding:0;line-height:1">×</button>
        </div>
      </Transition>

      <div v-if="loading" style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:14px">
        <div style="width:36px;height:36px;border-radius:50%;border:2px solid rgba(109,78,232,0.2);border-top-color:#6D4EE8;animation:spin 0.8s linear infinite"></div>
        <span style="color:rgba(255,255,255,0.3);font-size:13px">Loading page...</span>
      </div>

      <div v-else style="flex:1;display:flex;overflow:hidden;min-height:0">

        <!-- Form area -->
        <div style="flex:1;overflow-y:auto;padding:20px 28px;min-width:0">

          <!-- Horizontal tab bar -->
          <div style="display:flex;gap:0;margin-bottom:28px;border-bottom:1px solid rgba(255,255,255,0.07);overflow-x:auto;scrollbar-width:none">
            <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" class="tab-btn"
              :style="{
                display:'flex', alignItems:'center', gap:'6px',
                padding:'10px 16px', border:'none', borderBottom: activeTab===tab.id ? '2px solid ' + (tab.id==='vsl'?'#ef4444':'#6D4EE8') : '2px solid transparent',
                marginBottom:'-1px',
                cursor:'pointer', fontSize:'12px', fontWeight:activeTab===tab.id?700:500,
                transition:'all 0.15s', fontFamily:'Inter,sans-serif', whiteSpace:'nowrap',
                background: 'transparent',
                color: activeTab===tab.id
                  ? (tab.id==='vsl' ? '#fca5a5' : '#A78BFA')
                  : 'rgba(255,255,255,0.4)',
              }">
              <i :class="`bi bi-${tab.icon}`" style="font-size:13px"></i>
              {{ tab.label }}
              <span v-if="tab.id==='vsl' && activeTab!=='vsl'" style="width:5px;height:5px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
            </button>
          </div>

          <!-- Tab section header -->
          <div v-if="activeTab !== 'vsl'" style="margin-bottom:20px">
            <div style="display:flex;align-items:center;gap:12px">
              <div :style="{
                width:'38px', height:'38px', borderRadius:'11px', flexShrink:0,
                display:'flex', alignItems:'center', justifyContent:'center',
                background: 'linear-gradient(135deg, rgba(109,78,232,0.25), rgba(109,78,232,0.08))',
                border: '1px solid rgba(109,78,232,0.2)',
                boxShadow: '0 4px 12px rgba(109,78,232,0.15)',
              }">
                <i :class="`bi bi-${currentTab.icon}`" style="font-size:16px;color:#A78BFA"></i>
              </div>
              <div>
                <h1 style="font-size:16px;font-weight:800;color:#fff;margin:0;letter-spacing:-0.02em">{{ currentTab.label }}</h1>
                <p style="font-size:12px;color:rgba(255,255,255,0.3);margin:2px 0 0">{{ currentTab.desc }}</p>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: GENERAL ==================== -->
          <div v-if="activeTab === 'general'">
            <div class="card">
              <label class="field-label">Page name</label>
              <input v-model="form.model_name" placeholder="Ex: Karine" class="field-input" />
            </div>
            <div class="card">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:10px">Page type</p>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
                <div @click="form.page_type='landing'"
                  :style="{border:'2px solid',borderColor:form.page_type!=='direct'?'#6D4EE8':'rgba(255,255,255,0.1)',borderRadius:'12px',padding:'14px',cursor:'pointer',background:form.page_type!=='direct'?'rgba(109,78,232,0.15)':'rgba(255,255,255,0.04)'}">
                  <i class="bi bi-camera-video" style="font-size:20px;display:block;margin-bottom:6px" :style="{color:form.page_type!=='direct'?'#A78BFA':'rgba(255,255,255,0.5)'}"></i>
                  <p :style="{fontSize:'13px',fontWeight:600,color:form.page_type!=='direct'?'#A78BFA':'rgba(255,255,255,0.7)',margin:0}">Landing page</p>
                  <p style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:2px">Full page with VSL video and links</p>
                </div>
                <div @click="form.page_type='direct'"
                  :style="{border:'2px solid',borderColor:form.page_type==='direct'?'#6D4EE8':'rgba(255,255,255,0.1)',borderRadius:'12px',padding:'14px',cursor:'pointer',background:form.page_type==='direct'?'rgba(109,78,232,0.15)':'rgba(255,255,255,0.04)'}">
                  <i class="bi bi-lightning-charge" style="font-size:20px;display:block;margin-bottom:6px" :style="{color:form.page_type==='direct'?'#A78BFA':'rgba(255,255,255,0.5)'}"></i>
                  <p :style="{fontSize:'13px',fontWeight:600,color:form.page_type==='direct'?'#A78BFA':'rgba(255,255,255,0.7)',margin:0}">Direct link</p>
                  <p style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:2px">Redirects immediately to a URL</p>
                </div>
              </div>
              <div v-if="form.page_type === 'direct'">
                <label class="field-label">Destination URL</label>
                <input v-model="form.direct_url" type="url" placeholder="https://onlyfans.com/..." class="field-input" />
                <p style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:6px">Visitor will be redirected instantly. Analytics still tracked.</p>
              </div>
            </div>
            <div class="card">
              <label class="field-label">Public URL</label>
              <div style="display:flex;align-items:center;gap:0;border:1px solid rgba(255,255,255,0.1);border-radius:10px;overflow:hidden;background:rgba(255,255,255,0.04)">
                <span style="padding:10px 14px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.4);font-size:13px;white-space:nowrap;border-right:1px solid rgba(255,255,255,0.1)">mysocialvsl.com/</span>
                <input v-model="form.slug" placeholder="karine" style="flex:1;padding:10px 14px;font-size:13px;border:none;outline:none;background:transparent;color:#fff" />
              </div>
              <p style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:6px">Warning: changing the slug changes your page's public URL.</p>
            </div>
            <div class="card">
              <label class="field-label" style="margin-bottom:12px;display:block">Template</label>
              <TemplatePicker v-model="form.template" />
            </div>
            <div v-if="form.template === 'vsl-popup'" class="card">
              <p style="font-weight:600;font-size:14px;color:#fff;margin:0 0 4px">Configuration du Popup</p>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:16px">Le popup apparaît au centre de l'écran après X secondes</p>
              <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                  <label class="field-label">Délai d'apparition (secondes)</label>
                  <input v-model.number="form.popup_delay_seconds" type="number" min="0" max="120" class="field-input" placeholder="5" />
                </div>
                <div>
                  <label class="field-label">Image du popup (URL)</label>
                  <input v-model="form.popup_image_url" type="url" class="field-input" placeholder="https://..." />
                  <div v-if="form.popup_image_url" style="margin-top:8px;border-radius:10px;overflow:hidden;max-height:120px">
                    <img :src="form.popup_image_url" style="width:100%;height:120px;object-fit:cover" />
                  </div>
                </div>
                <div>
                  <label class="field-label">Texte sous l'image</label>
                  <textarea v-model="form.popup_text" rows="2" class="field-input" style="resize:vertical" placeholder="Rejoins-moi en exclusivité 🔥"></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: PROFILE ==================== -->
          <div v-if="activeTab === 'profile'">
            <div class="card">
              <label class="field-label">Profile photo</label>
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.08);overflow:hidden;border:1px solid rgba(255,255,255,0.12);flex-shrink:0">
                  <img v-if="form.avatar_url" :src="form.avatar_url" style="width:100%;height:100%;object-fit:cover" />
                  <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center"><i class="bi bi-person-fill" style="font-size:24px;color:rgba(255,255,255,0.25)"></i></div>
                </div>
                <div style="flex:1">
                  <label style="display:inline-flex;align-items:center;gap:6px;background:#6D4EE8;color:#fff;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                    <input type="file" accept="image/*" style="display:none" @change="uploadImage($event, 'avatar')" />
                    <i class="bi bi-folder2-open" style="font-size:13px"></i> {{ uploadingAvatar ? 'Uploading...' : 'Choose image' }}
                  </label>
                  <p style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:6px">JPG, PNG, WEBP — max 5MB</p>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:10px;margin-top:4px">
                <toggle-switch v-model="form.show_avatar" />
                <span style="font-size:13px;color:rgba(255,255,255,0.7)">Show profile photo</span>
              </div>
            </div>
            <div class="card">
              <label class="field-label">Background image</label>
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
                <div style="width:80px;height:44px;border-radius:8px;background:rgba(255,255,255,0.08);overflow:hidden;border:1px solid rgba(255,255,255,0.12);flex-shrink:0">
                  <img v-if="form.bg_image_url" :src="form.bg_image_url" style="width:100%;height:100%;object-fit:cover" />
                  <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center"><i class="bi bi-image" style="font-size:18px;color:rgba(255,255,255,0.25)"></i></div>
                </div>
                <label style="display:inline-flex;align-items:center;gap:6px;background:#6D4EE8;color:#fff;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                  <input type="file" accept="image/*" style="display:none" @change="uploadImage($event, 'background')" />
                  <i class="bi bi-folder2-open" style="font-size:13px"></i> {{ uploadingBg ? 'Uploading...' : 'Choose image' }}
                </label>
              </div>
              <div style="display:flex;align-items:center;gap:12px;margin-top:12px">
                <div>
                  <label style="font-size:12px;color:rgba(255,255,255,0.4);display:block;margin-bottom:4px">Background color</label>
                  <div style="display:flex;align-items:center;gap:8px">
                    <input v-model="form.bg_color" type="color" style="width:36px;height:36px;border-radius:8px;border:1px solid rgba(255,255,255,0.15);cursor:pointer;padding:2px" />
                    <span style="font-size:12px;color:rgba(255,255,255,0.4)">{{ form.bg_color }}</span>
                  </div>
                </div>
                <div>
                  <label style="font-size:12px;color:rgba(255,255,255,0.4);display:block;margin-bottom:4px">Button color</label>
                  <div style="display:flex;align-items:center;gap:8px">
                    <input v-model="form.btn_color" type="color" style="width:36px;height:36px;border-radius:8px;border:1px solid rgba(255,255,255,0.15);cursor:pointer;padding:2px" />
                    <span style="font-size:12px;color:rgba(255,255,255,0.4)">{{ form.btn_color }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <label class="field-label">Display name</label>
              <input v-model="form.model_name" placeholder="Karine" class="field-input" />
              <label class="field-label" style="margin-top:16px">Handle</label>
              <input v-model="form.model_handle" placeholder="@karinefrenchwoman" class="field-input" />
              <label class="field-label" style="margin-top:16px">Bio</label>
              <textarea v-model="form.bio" placeholder="Share something about yourself..." rows="3"
                style="width:100%;border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:10px 14px;font-size:13px;outline:none;resize:none;font-family:inherit;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff"></textarea>
              <div style="display:flex;align-items:center;gap:10px;margin-top:12px">
                <toggle-switch v-model="form.verified_badge" />
                <span style="font-size:13px;color:rgba(255,255,255,0.7)">Verified badge <i class="bi bi-check" style="font-size:12px"></i></span>
              </div>
            </div>
            <div class="card">
              <label class="field-label">Age verification</label>
              <div style="display:flex;align-items:center;gap:10px">
                <toggle-switch v-model="form.age_gate" />
                <span style="font-size:13px;color:rgba(255,255,255,0.7)">Enable age verification on buttons</span>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: VSL ==================== -->
          <div v-if="activeTab === 'vsl'">
            <div style="position:relative;border-radius:20px;overflow:hidden;margin-bottom:24px;padding:28px;background:linear-gradient(135deg,#160808 0%,#0a0918 100%);border:1px solid rgba(239,68,68,0.2)">
              <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 15% 60%,rgba(239,68,68,0.12),transparent 65%);pointer-events:none"></div>
              <div style="position:absolute;top:0;left:0;right:0;height:10px;background:repeating-linear-gradient(90deg,transparent 0px,transparent 8px,rgba(255,255,255,0.07) 8px,rgba(255,255,255,0.07) 14px);pointer-events:none"></div>
              <div style="position:absolute;bottom:0;left:0;right:0;height:10px;background:repeating-linear-gradient(90deg,transparent 0px,transparent 8px,rgba(255,255,255,0.07) 8px,rgba(255,255,255,0.07) 14px);pointer-events:none"></div>
              <div style="position:relative;z-index:1">
                <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:999px;padding:5px 14px;margin-bottom:14px">
                  <div style="width:7px;height:7px;border-radius:50%;background:#ef4444;animation:pulse-rec 1.4s ease-in-out infinite;flex-shrink:0"></div>
                  <span style="font-size:10px;font-weight:800;color:#fca5a5;letter-spacing:0.12em">VIDEO SALES LETTER</span>
                </div>
                <h2 style="font-size:26px;font-weight:900;color:#fff;margin:0 0 6px;letter-spacing:-0.04em;line-height:1.05">Votre VSL</h2>
                <p style="font-size:13px;color:rgba(255,255,255,0.38);margin:0;max-width:360px;line-height:1.6">La vidéo qui convertit.</p>
              </div>
            </div>

            <div v-if="analyticsData" style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px">
              <div style="background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.15);border-radius:14px;padding:14px 16px;text-align:center">
                <p style="font-size:10px;font-weight:700;color:rgba(252,161,161,0.65);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px">Play Rate</p>
                <p :style="{fontSize:'26px',fontWeight:900,margin:0,letterSpacing:'-0.04em',color:analyticsData.play_rate>=50?'#86efac':analyticsData.play_rate>=25?'#fcd34d':'#fca5a5'}">{{ analyticsData.play_rate }}%</p>
              </div>
              <div style="background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.15);border-radius:14px;padding:14px 16px;text-align:center">
                <p style="font-size:10px;font-weight:700;color:rgba(252,161,161,0.65);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px">Vu à 50%</p>
                <p :style="{fontSize:'26px',fontWeight:900,margin:0,letterSpacing:'-0.04em',color:analyticsData.milestones[50]>=50?'#86efac':analyticsData.milestones[50]>=25?'#fcd34d':'#fca5a5'}">{{ analyticsData.milestones[50] }}%</p>
              </div>
              <div style="background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.15);border-radius:14px;padding:14px 16px;text-align:center">
                <p style="font-size:10px;font-weight:700;color:rgba(252,161,161,0.65);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px">Watch / Clic</p>
                <p style="font-size:26px;font-weight:900;margin:0;letter-spacing:-0.04em;color:#fff">{{ analyticsData.avg_watch_before_click != null ? analyticsData.avg_watch_before_click + 's' : '—' }}</p>
              </div>
            </div>
            <div v-else style="display:flex;align-items:center;gap:10px;background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.12);border-radius:12px;padding:12px 16px;margin-bottom:20px">
              <i class="bi bi-bar-chart-line" style="font-size:16px;flex-shrink:0;color:#fca5a5;opacity:0.7"></i>
              <p style="font-size:12px;color:rgba(255,255,255,0.38);margin:0;line-height:1.5">Vos stats VSL apparaîtront ici une fois la page active.</p>
              <button @click="loadAnalytics" style="margin-left:auto;font-size:11px;color:#fca5a5;background:none;border:none;cursor:pointer;white-space:nowrap;font-weight:700;padding:0">Charger ↻</button>
            </div>

            <div style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.16);border-radius:20px;padding:24px;margin-bottom:12px">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <div style="width:30px;height:30px;background:rgba(239,68,68,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fca5a5" stroke-width="2.2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
                <div>
                  <p style="font-weight:700;font-size:14px;color:#fff;margin:0;line-height:1">Vidéo VSL</p>
                  <p style="font-size:11px;color:rgba(255,255,255,0.35);margin:3px 0 0">MP4 · MOV · WEBM — 45s recommandé</p>
                </div>
                <span style="margin-left:auto;font-size:10px;font-weight:800;color:#fca5a5;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.25);border-radius:999px;padding:3px 10px;letter-spacing:0.06em;white-space:nowrap">PRINCIPAL</span>
              </div>
              <VideoUpload v-model="form.video_url" @uploading="videoUploading = $event" />
            </div>

            <div style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.16);border-radius:20px;padding:24px;margin-bottom:12px">
              <p style="font-weight:700;font-size:14px;color:#fff;margin:0 0 20px">Rendu vidéo</p>
              <p style="font-size:11px;font-weight:700;color:rgba(252,161,161,0.55);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 10px">Cadrage</p>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:24px">
                <button @click="form.video_fit='contain'"
                  :style="{padding:'12px 8px',border:'2px solid',borderRadius:'12px',cursor:'pointer',fontFamily:'inherit',fontSize:'12px',fontWeight:600,transition:'all 0.15s',textAlign:'center',lineHeight:1.5,background:form.video_fit==='contain'?'rgba(239,68,68,0.15)':'rgba(255,255,255,0.04)',borderColor:form.video_fit==='contain'?'#ef4444':'rgba(255,255,255,0.1)',color:form.video_fit==='contain'?'#fca5a5':'rgba(255,255,255,0.6)'}">
                  Contain<br/><span style="font-weight:400;font-size:10px;opacity:0.7">Vidéo entière visible</span>
                </button>
                <button @click="form.video_fit='cover'"
                  :style="{padding:'12px 8px',border:'2px solid',borderRadius:'12px',cursor:'pointer',fontFamily:'inherit',fontSize:'12px',fontWeight:600,transition:'all 0.15s',textAlign:'center',lineHeight:1.5,background:form.video_fit==='cover'?'rgba(239,68,68,0.15)':'rgba(255,255,255,0.04)',borderColor:form.video_fit==='cover'?'#ef4444':'rgba(255,255,255,0.1)',color:form.video_fit==='cover'?'#fca5a5':'rgba(255,255,255,0.6)'}">
                  Cover<br/><span style="font-weight:400;font-size:10px;opacity:0.7">Remplit, peut recadrer</span>
                </button>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                <p style="font-size:11px;font-weight:700;color:rgba(252,161,161,0.55);text-transform:uppercase;letter-spacing:0.08em;margin:0">Fond sombre</p>
                <span style="font-size:13px;font-weight:800;color:#fca5a5">{{ Math.round(form.overlay_opacity * 100) }}%</span>
              </div>
              <input type="range" min="0.1" max="0.9" step="0.05" v-model.number="form.overlay_opacity"
                style="width:100%;accent-color:#ef4444;cursor:pointer;margin-bottom:8px" />
            </div>

            <div style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.16);border-radius:20px;padding:24px;margin-bottom:12px">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <p style="font-weight:700;font-size:14px;color:#fff;margin:0">Affichage dans la page</p>
                <toggle-switch v-model="form.vsl_enabled" />
              </div>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin:0 0 16px">Active ou masque la VSL sur ta landing page sans perdre tes réglages vidéo.</p>
              <template v-if="form.vsl_enabled">
                <p style="font-size:11px;font-weight:700;color:rgba(252,161,161,0.55);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 10px">Position</p>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
                  <button v-for="pos in [{id:'top',label:'Haut',desc:'Avant le profil'},{id:'middle',label:'Milieu',desc:'Après la bio'},{id:'bottom',label:'Bas',desc:'Après les liens'}]" :key="pos.id"
                    @click="form.vsl_position=pos.id"
                    :style="{padding:'12px 8px',border:'2px solid',borderRadius:'12px',cursor:'pointer',fontFamily:'inherit',fontSize:'12px',fontWeight:600,transition:'all 0.15s',textAlign:'center',lineHeight:1.5,background:form.vsl_position===pos.id?'rgba(239,68,68,0.15)':'rgba(255,255,255,0.04)',borderColor:form.vsl_position===pos.id?'#ef4444':'rgba(255,255,255,0.1)',color:form.vsl_position===pos.id?'#fca5a5':'rgba(255,255,255,0.6)'}">
                    {{ pos.label }}<br/><span style="font-weight:400;font-size:10px;opacity:0.7">{{ pos.desc }}</span>
                  </button>
                </div>
              </template>
            </div>

            <div style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.16);border-radius:20px;padding:24px;margin-bottom:12px">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <div style="width:30px;height:30px;background:rgba(239,68,68,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fca5a5" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <p style="font-weight:700;font-size:14px;color:#fff;margin:0;line-height:1">Apparition du CTA</p>
                  <p style="font-size:11px;color:rgba(255,255,255,0.35);margin:3px 0 0">Le bouton apparaît après X secondes de vidéo. Vide = immédiat.</p>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:12px;margin-top:14px">
                <input v-model.number="form.cta_reveal_at" type="number" min="0" max="600" placeholder="ex: 30"
                  style="width:90px;border:1px solid rgba(239,68,68,0.3);border-radius:10px;padding:10px 14px;font-size:14px;font-weight:700;outline:none;box-sizing:border-box;background:rgba(239,68,68,0.08);color:#fca5a5;text-align:center;font-family:inherit" />
                <span style="font-size:13px;color:rgba(255,255,255,0.45)">secondes après le début de la vidéo</span>
                <button v-if="form.cta_reveal_at" @click="form.cta_reveal_at = null"
                  style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.3);background:none;border:none;cursor:pointer;padding:0">Retirer ✕</button>
              </div>
            </div>

            <div style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.16);border-radius:20px;padding:24px">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:30px;height:30px;background:rgba(239,68,68,0.2);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fca5a5" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  </div>
                  <p style="font-weight:700;font-size:14px;color:#fff;margin:0">Guide script VSL</p>
                </div>
                <span style="font-size:10px;font-weight:700;color:#fca5a5;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.2);border-radius:999px;padding:3px 10px">45s</span>
              </div>
              <div style="display:flex;flex-direction:column;gap:8px">
                <div v-for="step in vslScriptGuide" :key="step.time"
                  style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px">
                  <span style="font-size:11px;font-weight:800;color:#fca5a5;background:rgba(239,68,68,0.15);border-radius:6px;padding:3px 7px;flex-shrink:0;margin-top:1px">{{ step.time }}</span>
                  <div>
                    <p style="font-size:12px;font-weight:700;color:#fff;margin:0 0 2px">{{ step.title }}</p>
                    <p style="font-size:11px;color:rgba(255,255,255,0.4);margin:0;line-height:1.5">{{ step.desc }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: CONTENT ==================== -->
          <div v-if="activeTab === 'content'">
            <div class="card">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <div>
                  <p style="font-weight:600;font-size:14px;color:#fff;margin:0">Links</p>
                  <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:2px">Drag to reorder</p>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:20px">
                <button v-for="lt in linkTypes" :key="lt.type" @click="addLink(lt.type)"
                  style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 8px;border:1px solid rgba(255,255,255,0.1);border-radius:12px;background:rgba(255,255,255,0.04);cursor:pointer;font-size:11px;font-weight:600;color:rgba(255,255,255,0.6);transition:all 0.15s;font-family:Inter,sans-serif"
                  onmouseover="this.style.borderColor='#6D4EE8';this.style.background='rgba(109,78,232,0.15)';this.style.color='#A78BFA'"
                  onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.04)';this.style.color='rgba(255,255,255,0.6)'">
                  <div :style="{width:'32px',height:'32px',borderRadius:'10px',background:lt.color,display:'flex',alignItems:'center',justifyContent:'center'}" v-html="lt.svg"></div>
                  {{ lt.label }}
                </button>
              </div>
              <div style="display:flex;flex-direction:column;gap:10px">
                <div v-for="(link, i) in form.links" :key="i"
                  style="border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:14px 16px;background:rgba(255,255,255,0.04)">
                  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <div style="display:flex;align-items:center;gap:8px">
                      <div style="display:flex;flex-direction:column;gap:2px;cursor:grab;color:rgba(255,255,255,0.25);padding:2px 4px 2px 0"
                        draggable="true" @dragstart="dragLinkIndex=i" @dragover.prevent @drop="moveLinkTo(i)">
                        <i class="bi bi-grip-vertical" style="font-size:13px"></i>
                      </div>
                      <span v-html="getLinkIcon(link.type)" style="font-size:16px;line-height:1;color:rgba(255,255,255,0.6)"></span>
                      <span style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.75);text-transform:capitalize">{{ getLinkTypeName(link.type) }}</span>
                      <span v-if="link.is_visible === false" style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.35);background:rgba(255,255,255,0.07);border-radius:999px;padding:1px 8px">Hidden</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px">
                      <button @click="link.is_visible = link.is_visible === false ? true : false"
                        style="color:rgba(255,255,255,0.4);background:none;border:none;cursor:pointer;line-height:1;padding:4px">
                        <i :class="link.is_visible === false ? 'bi bi-eye-slash' : 'bi bi-eye'" style="font-size:14px"></i>
                      </button>
                      <button @click="moveLink(i,-1)" :disabled="i===0" style="color:rgba(255,255,255,0.3);background:none;border:none;cursor:pointer;line-height:1;padding:4px" :style="{opacity:i===0?0.25:1}"><i class="bi bi-arrow-up" style="font-size:13px"></i></button>
                      <button @click="moveLink(i,1)" :disabled="i===form.links.length-1" style="color:rgba(255,255,255,0.3);background:none;border:none;cursor:pointer;line-height:1;padding:4px" :style="{opacity:i===form.links.length-1?0.25:1}"><i class="bi bi-arrow-down" style="font-size:13px"></i></button>
                      <button @click="removeLink(i)" style="color:rgba(255,255,255,0.3);background:none;border:none;cursor:pointer;line-height:1;padding:4px"><i class="bi bi-x-lg" style="font-size:13px"></i></button>
                    </div>
                  </div>
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div>
                      <label style="font-size:11px;color:rgba(255,255,255,0.35);display:block;margin-bottom:4px">Label</label>
                      <input v-model="link.label" placeholder="My OnlyFans — Private access"
                        style="width:100%;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                    </div>
                    <div>
                      <label style="font-size:11px;color:rgba(255,255,255,0.35);display:block;margin-bottom:4px">URL</label>
                      <input v-model="link.url" type="url" placeholder="https://..."
                        style="width:100%;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                    </div>
                  </div>
                  <div v-if="['image_button','banner','creator_card'].includes(link.type)" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px">
                    <div>
                      <label style="font-size:11px;color:rgba(255,255,255,0.35);display:block;margin-bottom:4px">Title</label>
                      <input v-model="link.title" placeholder="Exclusive content"
                        style="width:100%;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                    </div>
                    <div>
                      <label style="font-size:11px;color:rgba(255,255,255,0.35);display:block;margin-bottom:4px">Subtitle</label>
                      <input v-model="link.subtitle" placeholder="New drop every week"
                        style="width:100%;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                    </div>
                  </div>
                  <div v-if="['image_button','banner','creator_card'].includes(link.type)" style="margin-top:8px">
                    <label style="font-size:11px;color:rgba(255,255,255,0.35);display:block;margin-bottom:4px">Image URL</label>
                    <input v-model="link.image_url" type="url" placeholder="https://..."
                      style="width:100%;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                  </div>
                  <div v-if="link.type === 'product'" style="margin-top:8px">
                    <label style="font-size:11px;color:rgba(255,255,255,0.35);display:block;margin-bottom:4px">Price</label>
                    <input :value="link.meta?.price" @input="setLinkMeta(link, 'price', $event.target.value)" placeholder="9,99 €"
                      style="width:100%;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                  </div>
                  <div v-if="['image_grid','carousel'].includes(link.type)" style="margin-top:8px">
                    <label style="font-size:11px;color:rgba(255,255,255,0.35);display:block;margin-bottom:4px">Images — one URL per line</label>
                    <textarea :value="(link.meta?.images || []).join('\n')" @input="setLinkImages(link, $event.target.value)" rows="3"
                      placeholder="https://...&#10;https://..."
                      style="width:100%;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff;resize:vertical;font-family:inherit"></textarea>
                  </div>
                </div>
                <p v-if="form.links.length === 0" style="text-align:center;padding:32px;color:rgba(255,255,255,0.3);font-size:13px">
                  No links yet. Add one above.
                </p>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: SOCIAL ==================== -->
          <div v-if="activeTab === 'social'">
            <div class="card">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:6px">Quick add</p>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:20px">Click to add a platform. You can edit the URL afterwards in Content.</p>
              <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px">
                <button v-for="social in socialPlatforms" :key="social.type" @click="quickAddSocial(social)"
                  style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:12px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);cursor:pointer;transition:all 0.15s;text-align:left"
                  onmouseover="this.style.borderColor='#6D4EE8';this.style.background='rgba(109,78,232,0.15)'"
                  onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.04)'">
                  <div :style="{width:'32px',height:'32px',borderRadius:'8px',background:social.color,display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0,padding:'5px'}" v-html="social.svg"></div>
                  <div>
                    <p style="font-size:13px;font-weight:600;color:#fff;margin-bottom:2px">{{ social.name }}</p>
                    <p style="font-size:11px;color:rgba(255,255,255,0.35)">{{ social.desc }}</p>
                  </div>
                </button>
              </div>
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:16px">Social links</p>
              <div v-if="socialLinks.length === 0" style="text-align:center;padding:24px;color:rgba(255,255,255,0.3);font-size:13px">No social links yet.</div>
              <div v-else style="display:flex;flex-direction:column;gap:8px">
                <div v-for="(link, i) in socialLinks" :key="i"
                  style="display:flex;align-items:center;gap:12px;padding:10px 14px;border:1px solid rgba(255,255,255,0.1);border-radius:10px;background:rgba(255,255,255,0.04)">
                  <span style="font-size:20px" v-html="getLinkIcon(link.type)"></span>
                  <div style="flex:1">
                    <input v-model="link.url" type="url" :placeholder="'https://...'" style="width:100%;border:none;outline:none;font-size:13px;color:#fff;background:transparent" />
                  </div>
                  <button @click="removeLink(getLinkIndex(link))" style="color:rgba(255,255,255,0.3);background:none;border:none;cursor:pointer;padding:4px"><i class="bi bi-x-lg" style="font-size:13px"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: ADVANCED ==================== -->
          <div v-if="activeTab === 'advanced'">
            <div class="card">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:16px">Status & Info</p>
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <toggle-switch v-model="form.online_status" />
                <div>
                  <p style="font-size:13px;color:rgba(255,255,255,0.75);font-weight:500;margin:0">Show as "Online"</p>
                  <p style="font-size:11px;color:rgba(255,255,255,0.35);margin:0">A green badge appears on your page</p>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                  <label class="field-label">Location</label>
                  <input v-model="form.location" placeholder="Paris, France" class="field-input" />
                </div>
                <div>
                  <label class="field-label">Response time</label>
                  <input v-model="form.response_time" placeholder="Responds in < 1h" class="field-input" />
                </div>
              </div>
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:6px">Promo text</p>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:12px">Text displayed below bio</p>
              <input v-model="form.promo_text" placeholder="-50% ce weekend seulement !" class="field-input" />
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:6px">Countdown</p>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:12px">Create urgency with a countdown timer</p>
              <input v-model="form.countdown_end" type="datetime-local" class="field-input" />
            </div>
            <div class="card" style="margin-top:16px">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <p style="font-weight:600;font-size:14px;color:#fff;margin:0">FOMO popup</p>
                <toggle-switch v-model="form.fomo_enabled" />
              </div>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:12px">A pop-up appears after a delay to create urgency</p>
              <div v-if="form.fomo_enabled" style="display:flex;flex-direction:column;gap:10px">
                <div><label class="field-label">Title</label><input v-model="form.fomo_title" placeholder="Don't miss out 🔥" class="field-input" /></div>
                <div><label class="field-label">Message</label><textarea v-model="form.fomo_message" rows="2" placeholder="Special offer ends tonight." class="field-input" style="resize:vertical"></textarea></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                  <div><label class="field-label">Button label</label><input v-model="form.fomo_cta_label" placeholder="See exclusive content" class="field-input" /></div>
                  <div><label class="field-label">Delay (seconds)</label><input v-model.number="form.fomo_delay_seconds" type="number" min="0" max="300" class="field-input" /></div>
                </div>
              </div>
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:6px">SEO & Link preview</p>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:12px">Customize title, description and image shown when the link is shared</p>
              <div style="display:flex;flex-direction:column;gap:10px">
                <div><label class="field-label">Meta title</label><input v-model="form.meta_title" maxlength="120" placeholder="Defaults to your display name" class="field-input" /></div>
                <div><label class="field-label">Meta description</label><textarea v-model="form.meta_description" maxlength="300" rows="2" placeholder="Defaults to your bio" class="field-input" style="resize:vertical"></textarea></div>
                <div><label class="field-label">Open Graph image URL</label><input v-model="form.og_image_url" type="url" placeholder="Defaults to your avatar" class="field-input" /></div>
                <div style="display:flex;align-items:flex-start;gap:12px;margin-top:4px">
                  <toggle-switch v-model="form.utm_passthrough" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);margin:0">UTM passthrough</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:2px">Forward utm_source / utm_campaign… to destination links</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: SETTINGS ==================== -->
          <div v-if="activeTab === 'settings'">
            <div class="card">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:16px">Deep Linking</p>
              <div style="display:flex;flex-direction:column;gap:14px">
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.deep_link_enabled" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);margin:0">Deep link enabled</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:2px">Bypass Instagram/TikTok WebView and open the native app directly</p>
                  </div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.strict_deep_link" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);margin:0">Strict deep link</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:2px">Forces app opening, blocks browser fallback</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:16px">Protection & Privacy</p>
              <div style="display:flex;flex-direction:column;gap:14px">
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.bot_protection" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);margin:0">Bot protection</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:2px">Blocks crawlers and analytics bots</p>
                  </div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.link_preview_enabled" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);margin:0">Link preview (Open Graph)</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:2px">Disable if you don't want a preview when the link is shared</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:6px">Geo-routing</p>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:14px">Automatically redirect to a different URL based on the visitor's country.</p>
              <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px">
                <div v-for="(rule, i) in geoRules" :key="i" style="display:flex;align-items:center;gap:8px">
                  <input v-model="rule.country_code" placeholder="FR" maxlength="2"
                    style="width:52px;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 10px;font-size:13px;font-weight:700;text-transform:uppercase;outline:none;text-align:center;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                  <span style="color:rgba(255,255,255,0.3);font-size:13px">→</span>
                  <input v-model="rule.redirect_url" type="url" placeholder="https://onlyfans.com/..."
                    style="flex:1;border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box;background:rgba(255,255,255,0.06);color:#fff" />
                  <button @click="geoRules.splice(i,1)" style="color:rgba(255,255,255,0.3);background:none;border:none;cursor:pointer;padding:4px"><i class="bi bi-x-lg" style="font-size:13px"></i></button>
                </div>
              </div>
              <div style="display:flex;gap:8px">
                <button @click="geoRules.push({country_code:'',redirect_url:''})"
                  style="padding:7px 14px;border:1px dashed rgba(255,255,255,0.2);border-radius:8px;font-size:12px;color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.04);cursor:pointer">+ Add rule</button>
                <button @click="saveGeoRules" :disabled="savingGeo"
                  style="padding:7px 14px;background:#6D4EE8;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer"
                  :style="{opacity:savingGeo?0.5:1}">{{ savingGeo ? 'Saving...' : 'Save' }}</button>
              </div>
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:6px">Custom domain</p>
              <p style="font-size:12px;color:rgba(255,255,255,0.35);margin-bottom:12px">Pro plan required. Point your CNAME to <code style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:4px;font-size:11px;color:rgba(255,255,255,0.7)">pages.mysocialvsl.com</code></p>
              <input v-model="form.custom_domain" placeholder="links.tonsite.com" class="field-input" />
            </div>
            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:14px;color:#fff;margin-bottom:16px">Page status</p>
              <div style="display:flex;align-items:center;gap:12px">
                <toggle-switch v-model="form.is_active" />
                <div>
                  <p style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);margin:0">Page active</p>
                  <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:2px">Disable to take the page offline without deleting it</p>
                </div>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: ANALYTICS ==================== -->
          <div v-if="activeTab === 'analytics'">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px">
              <span style="font-size:12px;color:rgba(255,255,255,0.35);font-weight:600">Period:</span>
              <button v-for="d in [7, 30, 90]" :key="d" @click="analyticsPeriod = d"
                :style="{padding:'5px 14px',borderRadius:'999px',border:'none',cursor:'pointer',fontSize:'12px',fontWeight:600,transition:'all 0.15s',background:analyticsPeriod===d?'#6D4EE8':'rgba(255,255,255,0.08)',color:analyticsPeriod===d?'#fff':'rgba(255,255,255,0.5)'}">{{ d }}d</button>
              <button @click="loadAnalytics" :disabled="analyticsLoading"
                style="margin-left:auto;padding:5px 14px;border-radius:999px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.06);cursor:pointer;font-size:12px;color:rgba(255,255,255,0.5)">
                {{ analyticsLoading ? '...' : '↻ Refresh' }}
              </button>
            </div>
            <div v-if="analyticsLoading" style="text-align:center;padding:60px 0;color:rgba(255,255,255,0.35)">Loading analytics...</div>
            <div v-else-if="!analyticsData" style="text-align:center;padding:60px 0;color:rgba(255,255,255,0.35)">
              <i class="bi bi-bar-chart" style="font-size:36px;opacity:0.25;display:block;margin-bottom:12px"></i>
              <p>No data yet. Share your page to start collecting stats.</p>
            </div>
            <div v-else>
              <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
                <div class="card" style="text-align:center;padding:16px"><p style="font-size:10px;color:rgba(255,255,255,0.35);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em">Page Views</p><p style="font-size:28px;font-weight:800;color:#fff;letter-spacing:-0.03em;margin:0">{{ analyticsData.page_views }}</p></div>
                <div class="card" style="text-align:center;padding:16px"><p style="font-size:10px;color:rgba(255,255,255,0.35);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em">Play Rate</p><p :style="{fontSize:'28px',fontWeight:800,letterSpacing:'-0.03em',margin:0,color:analyticsData.play_rate>=50?'#059669':analyticsData.play_rate>=25?'#d97706':'#ef4444'}">{{ analyticsData.play_rate }}%</p></div>
                <div class="card" style="text-align:center;padding:16px"><p style="font-size:10px;color:rgba(255,255,255,0.35);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em">CTR</p><p :style="{fontSize:'28px',fontWeight:800,letterSpacing:'-0.03em',margin:0,color:analyticsData.ctr>=10?'#059669':analyticsData.ctr>=5?'#d97706':'#ef4444'}">{{ analyticsData.ctr }}%</p></div>
              </div>
              <div class="card" style="margin-bottom:16px">
                <p style="font-weight:700;font-size:14px;color:#fff;margin-bottom:16px">VSL Retention Funnel</p>
                <div style="display:flex;flex-direction:column;gap:8px">
                  <div v-for="(item, k) in [{label:'Page Views',value:analyticsData.page_views,pct:100,color:'#6D4EE8'},{label:'Video Started',value:analyticsData.video_plays,pct:analyticsData.play_rate,color:'#818cf8'},{label:'Link Clicks',value:analyticsData.link_clicks,pct:analyticsData.ctr,color:'#34d399'}]" :key="k">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                      <span style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.75)">{{ item.label }}</span>
                      <span style="font-size:12px;font-weight:700;color:#fff">{{ item.value }}</span>
                    </div>
                    <div style="height:6px;background:rgba(255,255,255,0.08);border-radius:999px">
                      <div :style="{height:'100%',background:item.color,borderRadius:'999px',width:item.pct+'%',transition:'width 0.5s'}"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="card">
                  <p style="font-weight:700;font-size:13px;color:#fff;margin-bottom:12px">Top Countries</p>
                  <div v-if="Object.keys(analyticsData.by_country).length === 0" style="font-size:12px;color:rgba(255,255,255,0.3)">No data</div>
                  <div v-for="(count, code) in Object.fromEntries(Object.entries(analyticsData.by_country).slice(0,5))" :key="code" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                    <span style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8)">{{ code }}</span>
                    <span style="font-size:12px;font-weight:700;color:#6D4EE8">{{ count }}</span>
                  </div>
                </div>
                <div class="card">
                  <p style="font-weight:700;font-size:13px;color:#fff;margin-bottom:12px">Devices</p>
                  <div v-for="(count, device) in analyticsData.by_device" :key="device" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                    <span style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);text-transform:capitalize">
                      <i :class="device === 'mobile' ? 'bi bi-phone' : 'bi bi-display'" style="margin-right:6px"></i>{{ device }}
                    </span>
                    <span style="font-size:12px;font-weight:700;color:#6D4EE8">{{ count }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- ─── Live Preview ─── -->
        <div style="width:300px;flex-shrink:0;border-left:1px solid rgba(255,255,255,0.06);background:#100e24;display:flex;flex-direction:column;align-items:center;padding:20px 16px;overflow-y:auto">

          <div style="width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <p style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:0.1em;margin:0">Live Preview</p>
            <div style="display:flex;gap:2px;background:rgba(255,255,255,0.07);border-radius:6px;padding:2px">
              <button v-for="m in ['mobile','desktop']" :key="m" @click="previewMode=m"
                :style="{padding:'4px 8px',borderRadius:'5px',border:'none',cursor:'pointer',fontSize:'10px',fontWeight:600,transition:'all 0.12s',background:previewMode===m?'rgba(255,255,255,0.12)':'transparent',color:previewMode===m?'#fff':'rgba(255,255,255,0.35)',fontFamily:'Inter,sans-serif'}">
                {{ m === 'mobile' ? '📱' : '🖥' }}
              </button>
            </div>
          </div>

          <!-- Phone frame -->
          <div style="width:220px;background:#18181b;border-radius:38px;padding:8px;box-shadow:0 20px 56px rgba(0,0,0,0.4);border:2px solid #27272a;position:relative">
            <div style="background:#000;border-radius:30px 30px 0 0;overflow:hidden">
              <div style="height:26px;display:flex;align-items:center;justify-content:space-between;padding:0 14px">
                <span style="font-size:9px;color:#fff;font-weight:600">9:41</span>
                <div style="width:46px;height:8px;background:#1a1a1a;border-radius:999px"></div>
                <span style="font-size:9px;color:#fff">●●●</span>
              </div>

              <!-- Screen content -->
              <div :style="{background:previewBg,minHeight:form.template==='cinematic'?'0':'460px',height:form.template==='cinematic'?'360px':'auto',display:'flex',flexDirection:'column',alignItems:'center',padding:form.template==='card'?'0':form.template==='cinematic'?'0':'12px 10px 16px',overflowY:form.template==='cinematic'?'hidden':'auto',position:'relative'}">

                <template v-if="form.template === 'cinematic'">
                  <div style="width:204px;height:360px;overflow:hidden;position:relative;background:#000">
                    <iframe v-if="page" :key="iframeKey" :src="`/p/${page.slug}`"
                      style="width:390px;height:693px;border:none;transform:scale(0.5231);transform-origin:top left;pointer-events:none" />
                    <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                      <i class="bi bi-play-btn" style="font-size:32px;color:rgba(255,255,255,0.15)"></i>
                    </div>
                  </div>
                </template>

                <template v-else-if="form.template === 'card'">
                  <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 30%,rgba(109,78,232,0.18),transparent 70%);z-index:0"></div>
                  <div style="position:relative;z-index:1;width:100%;flex:1;display:flex;align-items:center;justify-content:center;padding:24px 16px 6px">
                    <div style="width:65px;background:#0a0a12;border-radius:10px;overflow:hidden;aspect-ratio:9/16;max-height:120px;border:1px solid rgba(255,255,255,0.1);box-shadow:0 8px 24px rgba(0,0,0,0.7)">
                      <img v-if="form.avatar_url" :src="form.avatar_url" style="width:100%;height:100%;object-fit:cover;opacity:0.85" />
                      <div v-else style="width:100%;height:100%;background:linear-gradient(135deg,#1a0a2e,#000);display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-play-btn" style="font-size:18px;opacity:0.35;color:#fff"></i>
                      </div>
                    </div>
                  </div>
                  <div style="position:relative;z-index:1;width:100%;background:rgba(8,6,18,0.8);backdrop-filter:blur(12px);border-top:1px solid rgba(255,255,255,0.1);padding:8px 8px 6px">
                    <div style="display:flex;align-items:center;gap:5px;margin-bottom:6px">
                      <div :style="{width:'20px',height:'20px',borderRadius:'50%',overflow:'hidden',background:'#333',border:'1px solid rgba(255,255,255,0.15)',flexShrink:0}">
                        <img v-if="form.avatar_url" :src="form.avatar_url" style="width:100%;height:100%;object-fit:cover" />
                        <div v-else style="display:flex;align-items:center;justify-content:center;height:100%"><i class="bi bi-person-fill" style="font-size:9px;color:rgba(255,255,255,0.4)"></i></div>
                      </div>
                      <p :style="{fontSize:'8px',fontWeight:800,color:'#fff',margin:0}">{{ form.model_name||'Your Name' }}</p>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:3px">
                      <div v-for="(link, i) in visiblePreviewLinks.slice(0,3)" :key="i"
                        :style="{'border-radius':'5px',padding:'4px 6px',fontSize:'6px',fontWeight:700,color:'#fff',background:link.btn_color||form.btn_color||'#6D4EE8',textAlign:'center'}">
                        {{ link.label }}
                      </div>
                      <div v-if="form.links.length===0" style="border:1px dashed rgba(255,255,255,0.12);border-radius:5px;padding:4px;text-align:center">
                        <p style="font-size:6px;color:rgba(255,255,255,0.2);margin:0">Your links</p>
                      </div>
                    </div>
                  </div>
                </template>

                <template v-else>
                  <div v-if="form.online_status" style="display:inline-flex;align-items:center;gap:4px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:999px;padding:3px 8px;margin-bottom:7px;z-index:1">
                    <div style="width:5px;height:5px;border-radius:50%;background:#10b981"></div>
                    <span style="font-size:7px;color:#10b981;font-weight:700">Online</span>
                  </div>
                  <div v-if="form.show_avatar" style="margin-bottom:7px;position:relative;z-index:1">
                    <div :style="{width:'52px',height:'52px',borderRadius:'50%',overflow:'hidden',border:`2px solid ${form.template==='neon'?(form.btn_color||'#A78BFA'):'rgba(255,255,255,0.15)'}`,background:'#333'}">
                      <img v-if="form.avatar_url" :src="form.avatar_url" style="width:100%;height:100%;object-fit:cover" />
                      <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center"><i class="bi bi-person-fill" style="font-size:20px;color:rgba(255,255,255,0.3)"></i></div>
                    </div>
                  </div>
                  <div style="display:flex;align-items:center;gap:4px;margin-bottom:2px;z-index:1">
                    <p :style="{fontSize:'12px',fontWeight:800,color:previewTextColor,margin:0}">{{ form.model_name || 'Your Name' }}</p>
                    <i v-if="form.verified_badge" class="bi bi-patch-check-fill" style="font-size:9px;color:#60a5fa"></i>
                  </div>
                  <p :style="{fontSize:'9px',color:previewSubColor,marginBottom:'5px',zIndex:1}">{{ form.model_handle || '@handle' }}</p>
                  <p v-if="form.bio" :style="{fontSize:'8px',color:previewSubColor,textAlign:'center',lineHeight:1.5,marginBottom:'7px',maxWidth:'170px',zIndex:1}">{{ form.bio }}</p>
                  <div v-if="form.promo_text" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:6px;padding:3px 8px;margin-bottom:7px;z-index:1">
                    <p style="font-size:7px;color:#f87171;text-align:center;font-weight:700;margin:0">{{ form.promo_text }}</p>
                  </div>
                  <div v-if="form.video_url && form.vsl_enabled !== false" style="width:100%;border-radius:10px;overflow:hidden;margin-bottom:8px;aspect-ratio:9/16;max-height:150px;background:#000;display:flex;align-items:center;justify-content:center;position:relative;z-index:1">
                    <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1e3a5f,#000)"></div>
                    <div style="position:absolute;top:5px;left:5px;background:rgba(0,0,0,0.6);border-radius:999px;padding:2px 6px;display:flex;align-items:center;gap:3px">
                      <div style="width:4px;height:4px;border-radius:50%;background:#ef4444"></div>
                      <span style="font-size:6px;font-weight:800;color:#fff">VSL</span>
                    </div>
                    <div style="width:24px;height:24px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;z-index:1">
                      <span style="font-size:10px;color:#fff;margin-left:2px">▶</span>
                    </div>
                  </div>
                  <div style="width:100%;display:flex;flex-direction:column;gap:6px;z-index:1">
                    <div v-for="link in visiblePreviewLinks" :key="link.label || link.type" :style="previewLinkStyle(link)">
                      <span :style="{fontSize:'9px',fontWeight:700,color:previewTextColor}">{{ link.label || getLinkTypeName(link.type) }}</span>
                      <span :style="{marginLeft:'auto',fontSize:'9px',opacity:0.5,color:previewTextColor}">›</span>
                    </div>
                    <div v-if="form.links.length === 0" style="border:1px dashed rgba(255,255,255,0.15);border-radius:8px;padding:8px;text-align:center">
                      <p style="font-size:8px;color:rgba(255,255,255,0.25);margin:0">Your links will appear here</p>
                    </div>
                  </div>
                </template>
              </div>

              <div style="height:20px;background:#000;display:flex;align-items:center;justify-content:center">
                <div style="width:60px;height:3px;background:#333;border-radius:999px"></div>
              </div>
            </div>
          </div>

          <div style="margin-top:10px;display:flex;align-items:center;gap:6px">
            <span style="font-size:10px;color:rgba(255,255,255,0.25)">Template:</span>
            <span style="font-size:10px;font-weight:700;color:#6D4EE8;text-transform:capitalize">{{ form.template }}</span>
          </div>
        </div>

      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/lib/axios'
import VideoUpload from '@/components/VideoUpload.vue'
import TemplatePicker from '@/components/TemplatePicker.vue'
import ToggleSwitch from '@/components/ToggleSwitch.vue'
import DashboardLayout from '@/components/DashboardLayout.vue'

const route = useRoute()
const page = ref(null)
const loading = ref(true)
const saving = ref(false)
const videoUploading = ref(false)
const iframeKey = ref(0)
const saved = ref(false)
const error = ref('')
const activeTab = ref('general')
const previewMode = ref('mobile')
const uploadingAvatar = ref(false)
const uploadingBg = ref(false)
const geoRules = ref([])
const savingGeo = ref(false)

async function uploadImage(event, type) {
  const file = event.target.files[0]
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
  } catch {
    error.value = 'Upload failed.'
  } finally {
    if (type === 'avatar') uploadingAvatar.value = false
    else uploadingBg.value = false
  }
}

const form = ref({
  model_name: '', model_handle: '', bio: '',
  video_url: '', avatar_url: '', bg_image_url: '',
  bg_color: '#0d0d0d', btn_color: '#0ea5e9',
  template: 'classic',
  page_type: 'landing', direct_url: '',
  verified_badge: false, show_avatar: true,
  age_gate: true,
  online_status: false, location: '', response_time: '',
  countdown_end: '', promo_text: '',
  group_name: '',
  bot_protection: false, deep_link_enabled: true,
  strict_deep_link: false, link_preview_enabled: true,
  custom_domain: '', is_active: true,
  slug: '',
  video_fit: 'contain', overlay_opacity: 0.6,
  cta_reveal_at: null,
  popup_delay_seconds: 5, popup_image_url: '', popup_text: '',
  vsl_enabled: true, vsl_position: 'top',
  fomo_enabled: false, fomo_title: '', fomo_message: '', fomo_cta_label: '', fomo_delay_seconds: 5,
  meta_title: '', meta_description: '', og_image_url: '', utm_passthrough: true,
  links: [],
})

const vslScriptGuide = [
  { time: '0–5s',   title: 'Hook',            desc: "Attrape l'attention immédiatement. Pose une question ou révèle quelque chose d'exclusif." },
  { time: '5–30s',  title: 'Valeur + tension', desc: "Montre ce qu'ils vont obtenir. Crée une tension avec ce qu'ils ratent." },
  { time: '30–45s', title: 'CTA direct',       desc: 'Dis "clique en dessous" ou "le bouton juste là". Simple, direct, sans hésitation.' },
]

const tabs = [
  { id: 'general',   icon: 'gear',           label: 'General',   desc: 'Name, URL and template of your page' },
  { id: 'vsl',       icon: 'play-btn',       label: 'VSL',       desc: 'Votre vidéo de vente' },
  { id: 'profile',   icon: 'person',         label: 'Profile',   desc: 'Photo, bio and colors' },
  { id: 'content',   icon: 'link-45deg',     label: 'Content',   desc: 'Links, buttons and cards' },
  { id: 'social',    icon: 'phone',          label: 'Social',    desc: 'Add your social networks' },
  { id: 'advanced',  icon: 'stars',          label: 'Advanced',  desc: 'Countdown, promo, SEO' },
  { id: 'settings',  icon: 'shield-check',   label: 'Settings',  desc: 'Deep link, bot protection, domain' },
  { id: 'analytics', icon: 'bar-chart-line', label: 'Analytics', desc: 'VSL performance stats' },
]

const analyticsData = ref(null)
const analyticsLoading = ref(false)
const analyticsPeriod = ref(30)

async function loadAnalytics() {
  if (!page.value) return
  analyticsLoading.value = true
  try {
    const { data } = await api.get(`/pages/${page.value.id}/analytics?period=${analyticsPeriod.value}`)
    analyticsData.value = data
  } catch {} finally {
    analyticsLoading.value = false
  }
}

watch(activeTab, (tab) => { if (tab === 'analytics' || tab === 'vsl') loadAnalytics() })
watch(analyticsPeriod, () => { if (activeTab.value === 'analytics' || activeTab.value === 'vsl') loadAnalytics() })

const currentTab = computed(() => tabs.find(t => t.id === activeTab.value))

const previewBg = computed(() => {
  if (form.value.template === 'minimal') return '#F9FAFB'
  if (form.value.template === 'neon') return '#0a0118'
  if (form.value.template === 'cinematic') return '#000'
  if (form.value.template === 'card') return '#050508'
  return '#0d0d0d'
})

const previewTextColor = computed(() => form.value.template === 'minimal' ? '#111827' : '#ffffff')
const previewSubColor = computed(() => {
  if (form.value.template === 'minimal') return '#6b7280'
  if (form.value.template === 'neon') return '#A78BFA'
  return 'rgba(255,255,255,0.6)'
})

function previewLinkStyle(link) {
  const base = { display:'flex', alignItems:'center', justifyContent:'space-between', padding:'8px 10px', borderRadius:'9px', cursor:'pointer', width:'100%', boxSizing:'border-box' }
  const color = link.btn_color || form.value.btn_color || '#6D4EE8'
  if (form.value.template === 'neon') return { ...base, background:'transparent', border:`1px solid ${color}`, boxShadow:`0 0 10px ${color}55` }
  if (form.value.template === 'minimal') return { ...base, background:'#ffffff', border:'1px solid #e5e7eb', boxShadow:'0 1px 4px rgba(0,0,0,0.07)' }
  return { ...base, background:color, boxShadow:`0 4px 12px ${color}55` }
}

const linkTypes = [
  { type:'classic',      label:'Classic',   color:'#6D4EE8', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>` },
  { type:'tips',         label:'Tip me',    color:'#F59E0B', svg:`<svg viewBox="0 0 24 24" fill="white" width="16" height="16"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/></svg>` },
  { type:'product',      label:'Product',   color:'#10B981', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>` },
  { type:'image_button', label:'Image btn', color:'#3B82F6', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>` },
  { type:'creator_card', label:'Card',      color:'#8B5CF6', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>` },
  { type:'image_grid',   label:'Gallery',   color:'#EC4899', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>` },
  { type:'countdown',    label:'Countdown', color:'#EF4444', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>` },
  { type:'carousel',     label:'Carousel',  color:'#06B6D4', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><polyline points="15 18 21 12 15 6"/><path d="M3 12h18"/></svg>` },
]

const socialPlatforms = [
  { type:'onlyfans',  color:'#00AFF0', name:'OnlyFans',   desc:'Your OnlyFans profile',  label:'My OnlyFans',   svg:`<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 5.838a6.162 6.162 0 1 1 0 12.324A6.162 6.162 0 0 1 12 5.838zm0 2.456a3.706 3.706 0 1 0 0 7.412 3.706 3.706 0 0 0 0-7.412zm6.31-3.853a1.228 1.228 0 1 1 0 2.456 1.228 1.228 0 0 1 0-2.456z"/></svg>` },
  { type:'mym',       color:'#FFD700', name:'MYM',         desc:'Your MYM profile',        label:'My MYM',        svg:`<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><text x="2" y="18" font-size="16" font-weight="bold">M</text></svg>` },
  { type:'instagram', color:'#E1306C', name:'Instagram',   desc:'Your Instagram profile',  label:'My Instagram',  svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="22" height="22"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="white" stroke="none"/></svg>` },
  { type:'tiktok',    color:'#010101', name:'TikTok',      desc:'Your TikTok profile',     label:'My TikTok',     svg:`<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>` },
  { type:'twitter',   color:'#000000', name:'X / Twitter', desc:'Your X account',          label:'My X',          svg:`<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>` },
  { type:'telegram',  color:'#26A5E4', name:'Telegram',    desc:'Your Telegram channel',   label:'My Telegram',   svg:`<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>` },
  { type:'youtube',   color:'#FF0000', name:'YouTube',     desc:'Your YouTube channel',    label:'My YouTube',    svg:`<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M23 7s-.3-2-1.2-2.7c-1.1-1.2-2.4-1.2-3-1.3C16.6 3 12 3 12 3s-4.6 0-6.8.1c-.6.1-1.9.1-3 1.3C1.3 5 1 7 1 7S.7 9.2.7 11.5v2.1c0 2.2.3 4.4.3 4.4s.3 2 1.2 2.7c1.1 1.2 2.6 1.1 3.3 1.2C7.5 22 12 22 12 22s4.6 0 6.8-.2c.6-.1 1.9-.1 3-1.3.9-.7 1.2-2.7 1.2-2.7s.3-2.2.3-4.4v-2.1C23.3 9.2 23 7 23 7zM9.7 15.5V8.4l8.1 3.6-8.1 3.5z"/></svg>` },
  { type:'twitch',    color:'#9146FF', name:'Twitch',      desc:'Your Twitch stream',      label:'My Twitch',     svg:`<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>` },
]

const socialLinks = computed(() =>
  form.value.links.filter(l => ['onlyfans','mym','instagram','tiktok','twitter','telegram','youtube','twitch','snapchat'].includes(l.type))
)
const visiblePreviewLinks = computed(() => form.value.links.filter(l => l.is_visible !== false))

const linkIcons = { onlyfans:'circle-fill', mym:'star-fill', instagram:'camera', tiktok:'music-note-beamed', twitter:'twitter-x', telegram:'telegram', youtube:'youtube', twitch:'twitch', snapchat:'snapchat', classic:'link-45deg', image_button:'image', product:'bag', creator_card:'person-badge', image_grid:'grid', tips:'cup-hot', countdown:'hourglass-split', carousel:'collection' }
const linkTypeNames = { onlyfans:'OnlyFans', mym:'MYM', instagram:'Instagram', tiktok:'TikTok', twitter:'X / Twitter', telegram:'Telegram', youtube:'YouTube', twitch:'Twitch', snapchat:'Snapchat', classic:'Link', image_button:'Image button', product:'Product', creator_card:'Creator card', image_grid:'Image grid', tips:'Tips', countdown:'Countdown', carousel:'Carousel' }

function getLinkIcon(type) { return `<i class="bi bi-${linkIcons[type] || 'link-45deg'}"></i>` }
function getLinkTypeName(type) { return linkTypeNames[type] || type }
function getLinkIndex(link) { return form.value.links.indexOf(link) }

function addLink(type) {
  const social = socialPlatforms.find(s => s.type === type)
  form.value.links.push({ type, label:social?social.label:'', title:'', subtitle:'', url:'', icon:null, image_url:null, meta:null, btn_color:null, is_visible:true })
  if (['classic','image_button','product','creator_card','image_grid','tips','countdown','carousel'].includes(type)) activeTab.value = 'content'
}

function removeLink(i) { form.value.links.splice(i, 1) }

function quickAddSocial(social) {
  if (form.value.links.find(l => l.type === social.type)) return
  form.value.links.push({ type:social.type, label:social.label, title:'', subtitle:'', url:'', icon:null, image_url:null, meta:null, btn_color:null, is_visible:true })
}

function setLinkMeta(link, key, value) { link.meta = { ...(link.meta || {}), [key]: value } }
function setLinkImages(link, raw) { setLinkMeta(link, 'images', raw.split('\n').map(s => s.trim()).filter(Boolean)) }

const dragLinkIndex = ref(null)
function moveLink(i, dir) {
  const j = i + dir
  if (j < 0 || j >= form.value.links.length) return
  ;[form.value.links[i], form.value.links[j]] = [form.value.links[j], form.value.links[i]]
}
function moveLinkTo(target) {
  const from = dragLinkIndex.value
  dragLinkIndex.value = null
  if (from === null || from === target) return
  const [moved] = form.value.links.splice(from, 1)
  form.value.links.splice(target, 0, moved)
}

async function saveGeoRules() {
  savingGeo.value = true
  try {
    const rules = geoRules.value.filter(r => r.country_code && r.redirect_url)
    await api.put(`/pages/${route.params.id}/geo-rules`, { rules })
  } catch {
    error.value = 'Failed to save geo rules.'
  } finally {
    savingGeo.value = false
  }
}

onMounted(async () => {
  try {
    const [{ data }, { data: geo }] = await Promise.all([
      api.get(`/pages/${route.params.id}`),
      api.get(`/pages/${route.params.id}/geo-rules`),
    ])
    geoRules.value = geo
    page.value = data
    form.value = {
      model_name:   data.model_name   || '',
      model_handle: data.model_handle || '',
      bio:          data.bio          || '',
      video_url:    data.video_url    || '',
      avatar_url:   data.avatar_url   || '',
      bg_image_url: data.bg_image_url || '',
      bg_color:     data.bg_color     || '#0d0d0d',
      btn_color:    data.btn_color    || '#0ea5e9',
      template:     data.template     || 'classic',
      verified_badge:       !!data.verified_badge,
      show_avatar:          data.show_avatar !== false,
      age_gate:             !!data.age_gate,
      online_status:        !!data.online_status,
      location:             data.location      || '',
      response_time:        data.response_time || '',
      countdown_end:        data.countdown_end || '',
      promo_text:           data.promo_text    || '',
      group_name:           data.group_name || '',
      bot_protection:       !!data.bot_protection,
      deep_link_enabled:    data.deep_link_enabled !== false,
      strict_deep_link:     !!data.strict_deep_link,
      link_preview_enabled: data.link_preview_enabled !== false,
      custom_domain:        data.custom_domain || '',
      is_active:            data.is_active !== false,
      slug:                 data.slug || '',
      page_type:            data.page_type || 'landing',
      direct_url:           data.direct_url || '',
      video_fit:            data.video_fit || 'contain',
      overlay_opacity:      data.overlay_opacity ?? 0.6,
      vsl_enabled:          data.vsl_enabled !== false,
      vsl_position:         data.vsl_position || 'top',
      cta_reveal_at:        data.cta_reveal_at ?? null,
      popup_delay_seconds:  data.popup_delay_seconds ?? 5,
      popup_image_url:      data.popup_image_url || '',
      popup_text:           data.popup_text || '',
      fomo_enabled:         !!data.fomo_enabled,
      fomo_title:           data.fomo_title || '',
      fomo_message:         data.fomo_message || '',
      fomo_cta_label:       data.fomo_cta_label || '',
      fomo_delay_seconds:   data.fomo_delay_seconds ?? 5,
      meta_title:           data.meta_title || '',
      meta_description:     data.meta_description || '',
      og_image_url:         data.og_image_url || '',
      utm_passthrough:      data.utm_passthrough !== false,
      links: (data.links || []).map(l => ({
        type:l.type, label:l.label, url:l.url,
        title:l.title||'', subtitle:l.subtitle||'',
        icon:l.icon||null, image_url:l.image_url||null, btn_color:l.btn_color||null,
        meta:l.meta||null, is_visible:l.is_visible !== false,
      })),
    }
  } finally {
    loading.value = false
  }
})

async function save() {
  saving.value = true
  error.value = ''
  saved.value = false
  try {
    await api.put(`/pages/${route.params.id}`, form.value)
    saved.value = true
    iframeKey.value++
    setTimeout(() => saved.value = false, 3000)
  } catch {
    error.value = 'Failed to save.'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
@keyframes pulse-rec {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.5; transform: scale(0.85); }
}
@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Alert transitions */
.alert-slide-enter-active,
.alert-slide-leave-active { transition: all 0.2s ease; overflow: hidden; }
.alert-slide-enter-from,
.alert-slide-leave-to { opacity: 0; max-height: 0; padding-top: 0; padding-bottom: 0; }
.alert-slide-enter-to,
.alert-slide-leave-from { max-height: 60px; }

/* Header buttons */
.header-back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,0.45);
  text-decoration: none;
  transition: all 0.15s;
  background: rgba(255,255,255,0.04);
  letter-spacing: 0.01em;
}
.header-back-btn:hover {
  border-color: rgba(109,78,232,0.35);
  color: #A78BFA;
  background: rgba(109,78,232,0.08);
}
.header-open-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,0.5);
  text-decoration: none;
  transition: all 0.15s;
  background: rgba(255,255,255,0.04);
}
.header-open-btn:hover {
  border-color: rgba(109,78,232,0.3);
  color: #A78BFA;
  background: rgba(109,78,232,0.07);
}
.header-save-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 7px 18px;
  background: linear-gradient(135deg, #7c5ce8, #5b3bc5);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
  font-family: Inter, sans-serif;
  box-shadow: 0 2px 8px rgba(109,78,232,0.4), 0 0 0 1px rgba(255,255,255,0.06) inset;
  letter-spacing: 0.01em;
}
.header-save-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #8b6ef0, #6a47d4);
  box-shadow: 0 4px 16px rgba(109,78,232,0.5), 0 0 0 1px rgba(255,255,255,0.1) inset;
  transform: translateY(-1px);
}
.header-save-btn:disabled { cursor: not-allowed; }

/* Tab bar */
.tab-btn { background: transparent; }
.tab-btn:hover { color: rgba(255,255,255,0.75) !important; background: rgba(255,255,255,0.04) !important; }

/* Cards */
.card {
  background: rgba(255,255,255,0.03);
  border-radius: 16px;
  padding: 20px 22px;
  border: 1px solid rgba(255,255,255,0.07);
  margin-bottom: 12px;
  transition: border-color 0.2s;
}
.card:hover { border-color: rgba(255,255,255,0.10); }

/* Field labels */
.field-label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: rgba(255,255,255,0.35);
  margin-bottom: 7px;
  text-transform: uppercase;
  letter-spacing: 0.07em;
}

/* Field inputs */
.field-input {
  width: 100%;
  border: 1px solid rgba(255,255,255,0.09);
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 13px;
  outline: none;
  font-family: inherit;
  box-sizing: border-box;
  transition: all 0.15s;
  background: rgba(255,255,255,0.05);
  color: #fff;
}
.field-input:hover { border-color: rgba(255,255,255,0.15); }
.field-input:focus {
  border-color: rgba(109,78,232,0.6);
  background: rgba(109,78,232,0.06);
  box-shadow: 0 0 0 3px rgba(109,78,232,0.12);
}
.field-input::placeholder { color: rgba(255,255,255,0.2); }
</style>
