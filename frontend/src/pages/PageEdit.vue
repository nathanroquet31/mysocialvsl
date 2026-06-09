<template>
  <div style="min-height:100vh;background:#f8f8f8;display:flex">

    <!-- Sidebar -->
    <aside style="width:220px;min-height:100vh;background:#fff;border-right:1px solid rgba(0,0,0,0.08);display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:10">
      <div style="padding:20px;border-bottom:1px solid rgba(0,0,0,0.06)">
        <div style="display:flex;align-items:center;gap:8px">
          <div style="width:28px;height:28px;background:#6D4EE8;border-radius:8px;display:flex;align-items:center;justify-content:center">
            <span style="color:#fff;font-weight:900;font-size:12px">V</span>
          </div>
          <span style="font-weight:700;color:#111;font-size:15px">MySocialVSL</span>
        </div>
      </div>
      <nav style="padding:12px;flex:1">
        <RouterLink to="/dashboard" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:13px;font-weight:500;color:#666;text-decoration:none;margin-bottom:4px">
          ← My pages
        </RouterLink>
        <div style="height:1px;background:rgba(0,0,0,0.06);margin:8px 0"></div>
        <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
          :style="{display:'flex',alignItems:'center',gap:'10px',padding:'10px 12px',borderRadius:'10px',fontSize:'13px',fontWeight:'500',cursor:'pointer',border:'none',width:'100%',textAlign:'left',marginBottom:'4px',background:activeTab===tab.id?'#EEE9FF':'transparent',color:activeTab===tab.id?'#6D4EE8':'#555',transition:'all 0.15s'}">
          <span style="font-size:16px">{{ tab.icon }}</span>
          {{ tab.label }}
        </button>
      </nav>
      <div style="padding:16px;border-top:1px solid rgba(0,0,0,0.06)">
        <button @click="save" :disabled="saving"
          style="width:100%;background:#6D4EE8;color:#fff;padding:10px;border-radius:10px;font-weight:700;font-size:13px;border:none;cursor:pointer;transition:opacity 0.15s"
          :style="{opacity:saving?0.5:1}">
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </aside>

    <!-- Main -->
    <div style="margin-left:220px;flex:1;display:flex;min-height:100vh">

      <!-- Form area -->
      <div style="flex:1;padding:32px;max-width:720px">

        <!-- Alerts -->
        <div v-if="saved" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:12px;padding:12px 16px;margin-bottom:20px;font-size:13px">
          ✅ Changes saved.
        </div>
        <div v-if="error" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:12px;padding:12px 16px;margin-bottom:20px;font-size:13px">
          {{ error }}
        </div>

        <div v-if="loading" style="text-align:center;padding:80px 0;color:#888">Loading...</div>

        <div v-else>

          <!-- Tab header -->
          <div style="margin-bottom:28px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
              <span style="font-size:22px">{{ currentTab.icon }}</span>
              <h1 style="font-size:22px;font-weight:700;color:#111">{{ currentTab.label }}</h1>
            </div>
            <p style="font-size:13px;color:#888">{{ currentTab.desc }}</p>
          </div>

          <!-- ==================== TAB: GENERAL ==================== -->
          <div v-if="activeTab === 'general'">
            <div class="card">
              <label class="field-label">Page name</label>
              <input v-model="form.model_name" placeholder="Ex: Karine" class="field-input" />
            </div>
            <div class="card">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:6px">Page type</p>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
                <div @click="form.page_type='landing'"
                  :style="{border:'2px solid',borderColor:form.page_type!=='direct'?'#6D4EE8':'#e5e7eb',borderRadius:'12px',padding:'14px',cursor:'pointer',background:form.page_type!=='direct'?'#EEE9FF':'#fff'}">
                  <p style="font-size:18px;margin-bottom:4px">🎬</p>
                  <p :style="{fontSize:'13px',fontWeight:600,color:form.page_type!=='direct'?'#6D4EE8':'#374151'}">Landing page</p>
                  <p style="font-size:11px;color:#9ca3af;margin-top:2px">Full page with VSL video and links</p>
                </div>
                <div @click="form.page_type='direct'"
                  :style="{border:'2px solid',borderColor:form.page_type==='direct'?'#6D4EE8':'#e5e7eb',borderRadius:'12px',padding:'14px',cursor:'pointer',background:form.page_type==='direct'?'#EEE9FF':'#fff'}">
                  <p style="font-size:18px;margin-bottom:4px">⚡</p>
                  <p :style="{fontSize:'13px',fontWeight:600,color:form.page_type==='direct'?'#6D4EE8':'#374151'}">Direct link</p>
                  <p style="font-size:11px;color:#9ca3af;margin-top:2px">Redirects immediately to a URL</p>
                </div>
              </div>
              <div v-if="form.page_type === 'direct'">
                <label class="field-label">Destination URL</label>
                <input v-model="form.direct_url" type="url" placeholder="https://onlyfans.com/..." class="field-input" />
                <p style="font-size:11px;color:#9ca3af;margin-top:6px">Visitor will be redirected instantly. Analytics still tracked.</p>
              </div>
            </div>
            <div class="card">
              <label class="field-label">Public URL</label>
              <div style="display:flex;align-items:center;gap:0;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fff">
                <span style="padding:10px 14px;background:#f3f4f6;color:#9ca3af;font-size:13px;white-space:nowrap;border-right:1px solid #e5e7eb">mysocialvsl.com/</span>
                <input v-model="form.slug" placeholder="karine" style="flex:1;padding:10px 14px;font-size:13px;border:none;outline:none" />
              </div>
              <p style="font-size:11px;color:#9ca3af;margin-top:6px">Warning: changing the slug changes your page's public URL.</p>
            </div>
            <div class="card">
              <label class="field-label" style="margin-bottom:12px;display:block">Template</label>
              <TemplatePicker v-model="form.template" />
            </div>
          </div>

          <!-- ==================== TAB: PROFILE ==================== -->
          <div v-if="activeTab === 'profile'">
            <div class="card">
              <label class="field-label">Profile photo</label>
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
                <div style="width:56px;height:56px;border-radius:50%;background:#f3f4f6;overflow:hidden;border:1px solid #e5e7eb;flex-shrink:0">
                  <img v-if="form.avatar_url" :src="form.avatar_url" style="width:100%;height:100%;object-fit:cover" />
                  <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:22px">👤</div>
                </div>
                <div style="flex:1">
                  <label style="display:inline-flex;align-items:center;gap:6px;background:#6D4EE8;color:#fff;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                    <input type="file" accept="image/*" style="display:none" @change="uploadImage($event, 'avatar')" />
                    {{ uploadingAvatar ? 'Uploading...' : '📁 Choose image' }}
                  </label>
                  <p style="font-size:11px;color:#9ca3af;margin-top:6px">JPG, PNG, WEBP — max 5MB</p>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:10px;margin-top:4px">
                <toggle-switch v-model="form.show_avatar" />
                <span style="font-size:13px;color:#555">Show profile photo</span>
              </div>
            </div>
            <div class="card">
              <label class="field-label">Background image</label>
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
                <div style="width:80px;height:44px;border-radius:8px;background:#f3f4f6;overflow:hidden;border:1px solid #e5e7eb;flex-shrink:0">
                  <img v-if="form.bg_image_url" :src="form.bg_image_url" style="width:100%;height:100%;object-fit:cover" />
                  <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:18px">🖼️</div>
                </div>
                <label style="display:inline-flex;align-items:center;gap:6px;background:#6D4EE8;color:#fff;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                  <input type="file" accept="image/*" style="display:none" @change="uploadImage($event, 'background')" />
                  {{ uploadingBg ? 'Uploading...' : '📁 Choose image' }}
                </label>
              </div>
              <div style="display:flex;align-items:center;gap:12px;margin-top:12px">
                <div>
                  <label style="font-size:12px;color:#888;display:block;margin-bottom:4px">Background color</label>
                  <div style="display:flex;align-items:center;gap:8px">
                    <input v-model="form.bg_color" type="color" style="width:36px;height:36px;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;padding:2px" />
                    <span style="font-size:12px;color:#888">{{ form.bg_color }}</span>
                  </div>
                </div>
                <div>
                  <label style="font-size:12px;color:#888;display:block;margin-bottom:4px">Button color</label>
                  <div style="display:flex;align-items:center;gap:8px">
                    <input v-model="form.btn_color" type="color" style="width:36px;height:36px;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;padding:2px" />
                    <span style="font-size:12px;color:#888">{{ form.btn_color }}</span>
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
                style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13px;outline:none;resize:none;font-family:inherit;box-sizing:border-box"></textarea>
              <div style="display:flex;align-items:center;gap:10px;margin-top:12px">
                <toggle-switch v-model="form.verified_badge" />
                <span style="font-size:13px;color:#555">Verified badge ✓</span>
              </div>
            </div>
            <div class="card">
              <label class="field-label">VSL Video</label>
              <VideoUpload v-model="form.video_url" style="margin-top:8px" />
            </div>
            <div class="card">
              <label class="field-label">Age verification</label>
              <div style="display:flex;align-items:center;gap:10px">
                <toggle-switch v-model="form.age_gate" />
                <span style="font-size:13px;color:#555">Enable age verification on buttons</span>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: CONTENT ==================== -->
          <div v-if="activeTab === 'content'">
            <div class="card">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <div>
                  <p style="font-weight:600;font-size:15px;color:#111">Links</p>
                  <p style="font-size:12px;color:#9ca3af;margin-top:2px">Drag to reorder</p>
                </div>
                <div style="display:flex;gap:8px">
                  <button @click="addLink('classic')"
                    style="background:#6D4EE8;color:#fff;border:none;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;cursor:pointer">
                    + Add link
                  </button>
                </div>
              </div>

              <!-- Link type selector -->
              <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:20px">
                <button v-for="lt in linkTypes" :key="lt.type" @click="addLink(lt.type)"
                  style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 8px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;cursor:pointer;font-size:11px;font-weight:600;color:#555;transition:all 0.15s;font-family:Inter,sans-serif"
                  onmouseover="this.style.borderColor='#6D4EE8';this.style.background='#EEE9FF';this.style.color='#6D4EE8'"
                  onmouseout="this.style.borderColor='#e5e7eb';this.style.background='#fff';this.style.color='#555'">
                  <div :style="{width:'32px',height:'32px',borderRadius:'10px',background:lt.color,display:'flex',alignItems:'center',justifyContent:'center'}" v-html="lt.svg"></div>
                  {{ lt.label }}
                </button>
              </div>

              <!-- Links list -->
              <div style="display:flex;flex-direction:column;gap:10px">
                <div v-for="(link, i) in form.links" :key="i"
                  style="border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;background:#fff">
                  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <div style="display:flex;align-items:center;gap:8px">
                      <span style="font-size:18px">{{ getLinkIcon(link.type) }}</span>
                      <span style="font-size:12px;font-weight:600;color:#374151;text-transform:capitalize">{{ getLinkTypeName(link.type) }}</span>
                    </div>
                    <button @click="removeLink(i)" style="color:#d1d5db;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;padding:0">✕</button>
                  </div>
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div>
                      <label style="font-size:11px;color:#9ca3af;display:block;margin-bottom:4px">Label</label>
                      <input v-model="link.label" placeholder="My OnlyFans — Private access"
                        style="width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box" />
                    </div>
                    <div>
                      <label style="font-size:11px;color:#9ca3af;display:block;margin-bottom:4px">URL</label>
                      <input v-model="link.url" type="url" placeholder="https://..."
                        style="width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box" />
                    </div>
                  </div>
                </div>
                <p v-if="form.links.length === 0" style="text-align:center;padding:32px;color:#9ca3af;font-size:13px">
                  No links yet. Add one above.
                </p>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: SOCIAL ==================== -->
          <div v-if="activeTab === 'social'">
            <div class="card">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:6px">Quick add</p>
              <p style="font-size:12px;color:#9ca3af;margin-bottom:20px">Click to add a platform. You can edit the URL afterwards in Content.</p>
              <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px">
                <button v-for="social in socialPlatforms" :key="social.type" @click="quickAddSocial(social)"
                  style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:12px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;transition:all 0.15s;text-align:left"
                  onmouseover="this.style.borderColor='#6D4EE8';this.style.background='#EEE9FF'"
                  onmouseout="this.style.borderColor='#e5e7eb';this.style.background='#fff'">
                  <div :style="{width:'32px',height:'32px',borderRadius:'8px',background:social.color,display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0,padding:'5px'}" v-html="social.svg"></div>
                  <div>
                    <p style="font-size:13px;font-weight:600;color:#111;margin-bottom:2px">{{ social.name }}</p>
                    <p style="font-size:11px;color:#9ca3af">{{ social.desc }}</p>
                  </div>
                </button>
              </div>
            </div>

            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:16px">Social links</p>
              <div v-if="socialLinks.length === 0" style="text-align:center;padding:24px;color:#9ca3af;font-size:13px">
                No social links yet. Use the buttons above.
              </div>
              <div v-else style="display:flex;flex-direction:column;gap:8px">
                <div v-for="(link, i) in socialLinks" :key="i"
                  style="display:flex;align-items:center;gap:12px;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;background:#fff">
                  <span style="font-size:20px">{{ getLinkIcon(link.type) }}</span>
                  <div style="flex:1">
                    <input v-model="link.url" type="url" :placeholder="'https://...'" style="width:100%;border:none;outline:none;font-size:13px;color:#374151" />
                  </div>
                  <button @click="removeLink(getLinkIndex(link))" style="color:#d1d5db;background:none;border:none;cursor:pointer;font-size:16px">✕</button>
                </div>
              </div>
            </div>
          </div>

          <!-- ==================== TAB: ADVANCED ==================== -->
          <div v-if="activeTab === 'advanced'">
            <div class="card">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:16px">Status & Info</p>
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <toggle-switch v-model="form.online_status" />
                <div>
                  <p style="font-size:13px;color:#555;font-weight:500">Show as "Online"</p>
                  <p style="font-size:11px;color:#9ca3af">A green badge appears on your page</p>
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
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:6px">Promo text</p>
              <p style="font-size:12px;color:#9ca3af;margin-bottom:12px">Text displayed below bio (e.g. special offer, limited promo)</p>
              <input v-model="form.promo_text" placeholder="🔥 -50% ce weekend seulement !" class="field-input" />
            </div>

            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:6px">Countdown</p>
              <p style="font-size:12px;color:#9ca3af;margin-bottom:12px">Create urgency with a countdown timer</p>
              <input v-model="form.countdown_end" type="datetime-local" class="field-input" />
            </div>
          </div>

          <!-- ==================== TAB: SETTINGS ==================== -->
          <div v-if="activeTab === 'settings'">
            <div class="card">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:16px">Deep Linking</p>
              <div style="display:flex;flex-direction:column;gap:14px">
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.deep_link_enabled" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:#374151">Deep link enabled</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Bypass Instagram/TikTok WebView and open the native app directly</p>
                  </div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.strict_deep_link" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:#374151">Strict deep link</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Forces app opening, blocks browser fallback</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:16px">Protection & Privacy</p>
              <div style="display:flex;flex-direction:column;gap:14px">
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.bot_protection" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:#374151">Bot protection</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Blocks crawlers and analytics bots</p>
                  </div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <toggle-switch v-model="form.link_preview_enabled" style="margin-top:2px" />
                  <div>
                    <p style="font-size:13px;font-weight:500;color:#374151">Link preview (Open Graph)</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Disable if you don't want a preview when the link is shared</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:6px">Geo-routing</p>
              <p style="font-size:12px;color:#9ca3af;margin-bottom:14px">Automatically redirect to a different URL based on the visitor's country.</p>
              <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px">
                <div v-for="(rule, i) in geoRules" :key="i" style="display:flex;align-items:center;gap:8px">
                  <input v-model="rule.country_code" placeholder="FR" maxlength="2"
                    style="width:52px;border:1px solid #e5e7eb;border-radius:8px;padding:8px 10px;font-size:13px;font-weight:700;text-transform:uppercase;outline:none;text-align:center;box-sizing:border-box" />
                  <span style="color:#9ca3af;font-size:13px">→</span>
                  <input v-model="rule.redirect_url" type="url" placeholder="https://onlyfans.com/..."
                    style="flex:1;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box" />
                  <button @click="geoRules.splice(i,1)" style="color:#d1d5db;background:none;border:none;cursor:pointer;font-size:18px;padding:0;line-height:1">✕</button>
                </div>
              </div>
              <div style="display:flex;gap:8px">
                <button @click="geoRules.push({country_code:'',redirect_url:''})"
                  style="padding:7px 14px;border:1px dashed #d1d5db;border-radius:8px;font-size:12px;color:#6b7280;background:#fff;cursor:pointer">
                  + Add rule
                </button>
                <button @click="saveGeoRules" :disabled="savingGeo"
                  style="padding:7px 14px;background:#6D4EE8;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer"
                  :style="{opacity:savingGeo?0.5:1}">
                  {{ savingGeo ? 'Saving...' : 'Save' }}
                </button>
              </div>
            </div>

            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:6px">Custom domain</p>
              <p style="font-size:12px;color:#9ca3af;margin-bottom:12px">Pro plan required. Point your CNAME to <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:11px">pages.mysocialvsl.com</code></p>
              <input v-model="form.custom_domain" placeholder="links.tonsite.com" class="field-input" />
            </div>

            <div class="card" style="margin-top:16px">
              <p style="font-weight:600;font-size:15px;color:#111;margin-bottom:16px">Page status</p>
              <div style="display:flex;align-items:center;gap:12px">
                <toggle-switch v-model="form.is_active" />
                <div>
                  <p style="font-size:13px;font-weight:500;color:#374151">Page active</p>
                  <p style="font-size:12px;color:#9ca3af;margin-top:2px">Disable to take the page offline without deleting it</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Live Preview -->
      <div style="width:340px;padding:24px 20px;position:sticky;top:0;height:100vh;display:flex;flex-direction:column;align-items:center;border-left:1px solid rgba(0,0,0,0.06);background:#F9FAFB;overflow-y:auto;flex-shrink:0">

        <div style="width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
          <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.1em;margin:0">Live Preview</p>
          <a v-if="page" :href="`/p/${page.slug}`" target="_blank"
            style="font-size:11px;color:#6D4EE8;text-decoration:none;display:flex;align-items:center;gap:4px;background:#EEE9FF;border:1px solid #C7BBFF;border-radius:6px;padding:4px 10px;font-weight:600">
            Open ↗
          </a>
        </div>

        <!-- Phone frame -->
        <div style="width:240px;background:#18181b;border-radius:40px;padding:10px;box-shadow:0 24px 64px rgba(0,0,0,0.35);border:2px solid #27272a;position:relative">
          <!-- Status bar -->
          <div style="background:#000;border-radius:30px 30px 0 0;overflow:hidden">
            <div style="height:28px;display:flex;align-items:center;justify-content:space-between;padding:0 16px">
              <span style="font-size:9px;color:#fff;font-weight:600">9:41</span>
              <div style="width:50px;height:8px;background:#1a1a1a;border-radius:999px"></div>
              <span style="font-size:9px;color:#fff">●●●</span>
            </div>

            <!-- Screen content -->
            <div :style="{
              background: previewBg,
              minHeight:'480px',
              display:'flex', flexDirection:'column', alignItems:'center',
              padding:'14px 12px 20px',
              overflowY:'auto',
              position:'relative'
            }">
              <!-- Gradient overlay for classic/neon -->
              <div v-if="form.template !== 'minimal'" style="position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(transparent,rgba(0,0,0,0.4));pointer-events:none;z-index:0"></div>

              <!-- Online badge -->
              <div v-if="form.online_status" style="display:inline-flex;align-items:center;gap:4px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:999px;padding:3px 10px;margin-bottom:8px;z-index:1">
                <div style="width:5px;height:5px;border-radius:50%;background:#10b981"></div>
                <span style="font-size:8px;color:#10b981;font-weight:700">Online</span>
              </div>

              <!-- Avatar -->
              <div v-if="form.show_avatar" style="margin-bottom:8px;position:relative;z-index:1">
                <div :style="{width:'56px',height:'56px',borderRadius:'50%',overflow:'hidden',border:`2px solid ${form.template==='neon'?(form.btn_color||'#A78BFA'):'rgba(255,255,255,0.15)'}`,background:'#333',boxShadow:form.template==='neon'?`0 0 16px ${form.btn_color||'#A78BFA'}55`:'none'}">
                  <img v-if="form.avatar_url" :src="form.avatar_url" style="width:100%;height:100%;object-fit:cover" />
                  <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:20px">👤</div>
                </div>
              </div>

              <!-- Name + verified -->
              <div style="display:flex;align-items:center;gap:4px;margin-bottom:2px;z-index:1">
                <p :style="{fontSize:'13px',fontWeight:800,color:previewTextColor,margin:0}">{{ form.model_name || 'Your Name' }}</p>
                <span v-if="form.verified_badge" style="font-size:10px;color:#60a5fa;font-weight:700">✓</span>
              </div>
              <p :style="{fontSize:'10px',color:previewSubColor,marginBottom:'6px',zIndex:1}">{{ form.model_handle || '@handle' }}</p>
              <p v-if="form.bio" :style="{fontSize:'9px',color:previewSubColor,textAlign:'center',lineHeight:1.5,marginBottom:'8px',maxWidth:'170px',zIndex:1}">{{ form.bio }}</p>

              <!-- Promo -->
              <div v-if="form.promo_text" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:7px;padding:4px 9px;margin-bottom:8px;z-index:1">
                <p style="font-size:8px;color:#f87171;text-align:center;font-weight:700;margin:0">{{ form.promo_text }}</p>
              </div>

              <!-- Video placeholder -->
              <div v-if="form.video_url" style="width:100%;border-radius:12px;overflow:hidden;margin-bottom:10px;aspect-ratio:9/16;max-height:160px;background:#000;display:flex;align-items:center;justify-content:center;position:relative;z-index:1">
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1e3a5f,#000)"></div>
                <div style="position:absolute;top:6px;left:6px;background:rgba(0,0,0,0.6);border-radius:999px;padding:2px 8px;display:flex;align-items:center;gap:4px">
                  <div style="width:5px;height:5px;border-radius:50%;background:#ef4444"></div>
                  <span style="font-size:7px;font-weight:800;color:#fff">VSL</span>
                </div>
                <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;z-index:1">
                  <span style="font-size:12px;color:#fff;margin-left:2px">▶</span>
                </div>
              </div>

              <!-- Links preview -->
              <div style="width:100%;display:flex;flex-direction:column;gap:7px;z-index:1">
                <div v-for="link in form.links" :key="link.label || link.type"
                  :style="previewLinkStyle(link)">
                  <span :style="{fontSize:'10px',fontWeight:700,color:previewTextColor}">{{ link.label || getLinkTypeName(link.type) }}</span>
                  <span :style="{marginLeft:'auto',fontSize:'10px',opacity:0.5,color:previewTextColor}">›</span>
                </div>
                <div v-if="form.links.length === 0" style="border:1px dashed rgba(255,255,255,0.15);border-radius:10px;padding:10px;text-align:center">
                  <p style="font-size:9px;color:rgba(255,255,255,0.25);margin:0">Your links will appear here</p>
                </div>
              </div>

            </div>
            <!-- Home bar -->
            <div style="height:22px;background:#000;display:flex;align-items:center;justify-content:center">
              <div style="width:70px;height:4px;background:#333;border-radius:999px"></div>
            </div>
          </div>
        </div>

        <!-- Template badge -->
        <div style="margin-top:12px;display:flex;align-items:center;gap:6px">
          <span style="font-size:11px;color:#9CA3AF">Template:</span>
          <span style="font-size:11px;font-weight:700;color:#6D4EE8;text-transform:capitalize">{{ form.template }}</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/lib/axios'
import VideoUpload from '@/components/VideoUpload.vue'
import TemplatePicker from '@/components/TemplatePicker.vue'
import ToggleSwitch from '@/components/ToggleSwitch.vue'


const route = useRoute()
const page = ref(null)
const loading = ref(true)
const saving = ref(false)
const saved = ref(false)
const error = ref('')
const activeTab = ref('profile')
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
  links: [],
})

const tabs = [
  { id: 'general',  icon: '⚙️', label: 'General',  desc: 'Name, URL and template of your page' },
  { id: 'profile',  icon: '👤', label: 'Profile',   desc: 'Photo, bio, video and colors' },
  { id: 'content',  icon: '🔗', label: 'Content',   desc: 'Links, buttons and cards' },
  { id: 'social',   icon: '📲', label: 'Social',    desc: 'Add your social networks' },
  { id: 'advanced', icon: '✨', label: 'Advanced',  desc: 'Countdown, promo, online status' },
  { id: 'settings', icon: '🛡️', label: 'Settings',  desc: 'Deep link, bot protection, domain' },
]

const currentTab = computed(() => tabs.find(t => t.id === activeTab.value))

// Preview computed properties
const previewBg = computed(() => {
  if (form.value.template === 'minimal') return '#F9FAFB'
  if (form.value.template === 'neon') return '#0a0118'
  if (form.value.template === 'cinematic') return '#000'
  return '#0d0d0d' // classic
})

const previewTextColor = computed(() => {
  return form.value.template === 'minimal' ? '#111827' : '#ffffff'
})

const previewSubColor = computed(() => {
  if (form.value.template === 'minimal') return '#6b7280'
  if (form.value.template === 'neon') return '#A78BFA'
  return 'rgba(255,255,255,0.6)'
})

function previewLinkStyle(link) {
  const base = {
    display: 'flex', alignItems: 'center', justifyContent: 'space-between',
    padding: '9px 12px', borderRadius: '10px', cursor: 'pointer',
    width: '100%', boxSizing: 'border-box',
  }
  const color = link.btn_color || form.value.btn_color || '#6D4EE8'
  if (form.value.template === 'neon') {
    return { ...base, background: 'transparent', border: `1px solid ${color}`, boxShadow: `0 0 10px ${color}55, inset 0 0 8px ${color}11` }
  }
  if (form.value.template === 'minimal') {
    return { ...base, background: '#ffffff', border: '1px solid #e5e7eb', boxShadow: '0 1px 4px rgba(0,0,0,0.07)' }
  }
  // classic
  return { ...base, background: color, boxShadow: `0 4px 12px ${color}55` }
}

const templates = [
  { id: 'classic',   name: 'Classic',   desc: 'Dark & premium',   bg:'#0d0d0d', avatarBg:'#333',    textColor:'#fff',     btnColor:'#6D4EE8' },
  { id: 'neon',      name: 'Neon',      desc: 'Glowing borders',  bg:'#0a0118', avatarBg:'#2d1b4e', textColor:'#A78BFA',  btnColor:'transparent' },
  { id: 'minimal',   name: 'Minimal',   desc: 'Clean & bright',   bg:'#F9FAFB', avatarBg:'#E5E7EB', textColor:'#374151',  btnColor:'#fff' },
  { id: 'cinematic', name: 'Cinematic', desc: 'Video fullscreen',  bg:'#000',    avatarBg:'#222',    textColor:'#fff',     btnColor:'#6D4EE8' },
]

const linkTypes = [
  { type:'classic',      label:'Classic',   color:'#6D4EE8', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>` },
  { type:'tips',         label:'Tip me ☕', color:'#F59E0B', svg:`<svg viewBox="0 0 24 24" fill="white" width="16" height="16"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>` },
  { type:'product',      label:'Product',   color:'#10B981', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>` },
  { type:'image_button', label:'Image btn', color:'#3B82F6', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>` },
  { type:'creator_card', label:'Card',      color:'#8B5CF6', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>` },
  { type:'image_grid',   label:'Gallery',   color:'#EC4899', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>` },
  { type:'countdown',    label:'Countdown', color:'#EF4444', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>` },
  { type:'carousel',     label:'Carousel',  color:'#06B6D4', svg:`<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="16" height="16"><polyline points="15 18 21 12 15 6"/><path d="M3 12h18"/></svg>` },
]

const socialPlatforms = [
  { type: 'onlyfans',  color: '#00AFF0', name: 'OnlyFans',   desc: 'Your OnlyFans profile',  label: 'My OnlyFans',
    svg: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 5.838a6.162 6.162 0 1 1 0 12.324A6.162 6.162 0 0 1 12 5.838zm0 2.456a3.706 3.706 0 1 0 0 7.412 3.706 3.706 0 0 0 0-7.412zm6.31-3.853a1.228 1.228 0 1 1 0 2.456 1.228 1.228 0 0 1 0-2.456z"/></svg>` },
  { type: 'mym',       color: '#FFD700', name: 'MYM',         desc: 'Your MYM profile',        label: 'My MYM',
    svg: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><text x="2" y="18" font-size="16" font-weight="bold">M</text></svg>` },
  { type: 'instagram', color: '#E1306C', name: 'Instagram',   desc: 'Your Instagram profile',  label: 'My Instagram',
    svg: `<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" width="22" height="22"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="white" stroke="none"/></svg>` },
  { type: 'tiktok',    color: '#010101', name: 'TikTok',      desc: 'Your TikTok profile',     label: 'My TikTok',
    svg: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>` },
  { type: 'twitter',   color: '#000000', name: 'X / Twitter', desc: 'Your X account',          label: 'My X',
    svg: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>` },
  { type: 'telegram',  color: '#26A5E4', name: 'Telegram',    desc: 'Your Telegram channel',   label: 'My Telegram',
    svg: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>` },
  { type: 'youtube',   color: '#FF0000', name: 'YouTube',     desc: 'Your YouTube channel',    label: 'My YouTube',
    svg: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M23 7s-.3-2-1.2-2.7c-1.1-1.2-2.4-1.2-3-1.3C16.6 3 12 3 12 3s-4.6 0-6.8.1c-.6.1-1.9.1-3 1.3C1.3 5 1 7 1 7S.7 9.2.7 11.5v2.1c0 2.2.3 4.4.3 4.4s.3 2 1.2 2.7c1.1 1.2 2.6 1.1 3.3 1.2C7.5 22 12 22 12 22s4.6 0 6.8-.2c.6-.1 1.9-.1 3-1.3.9-.7 1.2-2.7 1.2-2.7s.3-2.2.3-4.4v-2.1C23.3 9.2 23 7 23 7zM9.7 15.5V8.4l8.1 3.6-8.1 3.5z"/></svg>` },
  { type: 'twitch',    color: '#9146FF', name: 'Twitch',      desc: 'Your Twitch stream',      label: 'My Twitch',
    svg: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>` },
  { type: 'snapchat',  color: '#FFFC00', name: 'Snapchat',    desc: 'Your Snapchat profile',   label: 'My Snapchat',
    svg: `<i class="bi bi-snapchat" style="color:#000;font-size:20px;line-height:1"></i>` },
]

const socialLinks = computed(() =>
  form.value.links.filter(l =>
    ['onlyfans','mym','instagram','tiktok','twitter','telegram','youtube','twitch','snapchat'].includes(l.type)
  )
)

const linkIcons = {
  onlyfans: '🔵', mym: '💛', instagram: '📸', tiktok: '🎵',
  twitter: '🐦', telegram: '✈️', youtube: '▶️', twitch: '💜', snapchat: '👻',
  classic: '🔗', image_button: '🖼️', product: '🛍️',
  creator_card: '🎴', image_grid: '📷', tips: '💸',
  countdown: '⏱️', carousel: '🎠',
}

const linkTypeNames = {
  onlyfans: 'OnlyFans', mym: 'MYM', instagram: 'Instagram', tiktok: 'TikTok',
  twitter: 'X / Twitter', telegram: 'Telegram', youtube: 'YouTube', twitch: 'Twitch', snapchat: 'Snapchat',
  classic: 'Link', image_button: 'Image button', product: 'Product',
  creator_card: 'Creator card', image_grid: 'Image grid', tips: 'Tips',
  countdown: 'Countdown', carousel: 'Carousel',
}

function getLinkIcon(type) { return linkIcons[type] || '🔗' }
function getLinkTypeName(type) { return linkTypeNames[type] || type }
function getLinkIndex(link) { return form.value.links.indexOf(link) }

function addLink(type) {
  const social = socialPlatforms.find(s => s.type === type)
  form.value.links.push({
    type,
    label: social ? social.label : '',
    url: '',
    icon: null,
    image_url: null,
    btn_color: null,
  })
  if (['classic','image_button','product','creator_card','image_grid','tips','countdown','carousel'].includes(type)) {
    activeTab.value = 'content'
  }
}

function removeLink(i) {
  form.value.links.splice(i, 1)
}

function quickAddSocial(social) {
  const exists = form.value.links.find(l => l.type === social.type)
  if (exists) return
  form.value.links.push({ type: social.type, label: social.label, url: '', icon: null, image_url: null, btn_color: null })
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
      links: (data.links || []).map(l => ({
        type: l.type, label: l.label, url: l.url,
        icon: l.icon || null, image_url: l.image_url || null, btn_color: l.btn_color || null,
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
    setTimeout(() => saved.value = false, 3000)
  } catch (e) {
    error.value = 'Failed to save.'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.card {
  background: #fff;
  border-radius: 16px;
  padding: 20px 24px;
  border: 1px solid rgba(0,0,0,0.07);
  margin-bottom: 12px;
}
.field-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.field-input {
  width: 100%;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 13px;
  outline: none;
  font-family: inherit;
  box-sizing: border-box;
  transition: border-color 0.15s;
}
.field-input:focus {
  border-color: #6D4EE8;
  box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
}
</style>
