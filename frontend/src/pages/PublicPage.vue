<template>
  <!-- Loading -->
  <div v-if="loading" style="min-height:100dvh;display:flex;align-items:center;justify-content:center;background:#0d0d0d">
    <div style="width:32px;height:32px;border:2px solid rgba(255,255,255,0.15);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite"></div>
  </div>

  <!-- 404 -->
  <div v-else-if="!page" style="min-height:100dvh;display:flex;align-items:center;justify-content:center;background:#0d0d0d;color:#fff;text-align:center;padding:24px">
    <div>
      <p style="font-size:64px;font-weight:900;margin-bottom:8px">404</p>
      <p style="color:#6b7280;font-size:15px">This page doesn't exist or has been disabled.</p>
    </div>
  </div>

  <!-- ─── VSL PAGE ─────────────────────────────────────────────────────────────── -->
  <div v-else style="background:#0d0d0d;min-height:100dvh;display:flex;flex-direction:column;align-items:center;justify-content:safe center;padding:16px;font-family:-apple-system,BlinkMacSystemFont,'Inter','Segoe UI',sans-serif">

    <!-- 9:16 video container -->
    <div style="position:relative;width:100%;max-width:380px;border-radius:20px;overflow:hidden;background:#000;aspect-ratio:9/16">

      <!-- Local video (direct upload) -->
      <video v-if="vslActive && isLocalVideo" ref="videoEl" :src="page.video_url"
        autoplay muted loop playsinline preload="auto"
        @play="onVideoPlay" @timeupdate="onVideoTimeUpdate"
        style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border:none" />

      <!-- Iframe (Streamable, Vimeo…) -->
      <div v-else-if="vslActive" style="position:absolute;inset:0">
        <iframe :src="videoEmbedUrl" allow="autoplay; fullscreen"
          style="width:100%;height:100%;border:none;pointer-events:none" />
      </div>

      <!-- No video: dark placeholder -->
      <div v-else style="position:absolute;inset:0;background:linear-gradient(135deg,#1a0a2e,#000);display:flex;align-items:center;justify-content:center">
        <i class="bi bi-play-btn" style="font-size:48px;color:rgba(255,255,255,0.15)"></i>
      </div>

      <!-- VSL badge + bouton mute — haut-gauche -->
      <div v-if="vslActive" style="position:absolute;top:12px;left:12px;z-index:10;display:flex;align-items:center;gap:6px">
        <button v-if="isLocalVideo" @click="toggleMute"
          style="background:rgba(0,0,0,0.55);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.18);border-radius:999px;padding:4px 12px;cursor:pointer;display:flex;align-items:center;gap:5px;outline:none"
          :style="{ animation: videoMuted ? 'pulse-unmute 2s ease-in-out infinite' : 'none' }">
          <i :class="videoMuted ? 'bi bi-volume-mute-fill' : 'bi bi-volume-up-fill'" style="font-size:13px;color:#fff"></i>
          <span style="font-size:10px;font-weight:700;color:#fff">{{ videoMuted ? 'Tap for sound' : 'Mute' }}</span>
        </button>
      </div>

      <!-- Overlay bas : gradient + nom + CTA -->
      <div style="position:absolute;bottom:0;left:0;right:0;padding:90px 16px 20px;background:linear-gradient(to top,rgba(0,0,0,0.82) 0%,rgba(0,0,0,0.3) 55%,transparent 100%);z-index:5;display:flex;flex-direction:column;gap:14px">

        <!-- Nom + handle -->
        <div>
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px">
            <p style="font-size:19px;font-weight:700;color:#fff;letter-spacing:-0.3px;text-shadow:0 1px 8px rgba(0,0,0,0.6);margin:0">{{ page.model_name }}</p>
            <span v-if="page.online_status" class="online-dot" style="flex-shrink:0"></span>
            <svg v-if="page.verified_badge" viewBox="0 0 24 24" width="17" height="17" style="flex-shrink:0">
              <circle cx="12" cy="12" r="11" fill="#3b82f6"/>
              <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
          </div>
          <p v-if="page.model_handle" style="font-size:13px;color:rgba(255,255,255,0.62);text-shadow:0 1px 6px rgba(0,0,0,0.6);margin:0">{{ page.model_handle }}</p>
        </div>

        <!-- CTA button (bounce) — hidden for vsl-popup (the popup replaces the CTA) -->
        <button v-if="ctaVisible && primaryLink && !isPopup" @click="handleLinkClick(primaryLink)"
          class="btn-bounce cta-appear"
          :style="{
            '--btn-shadow-sm': `0 4px 22px ${btnColor}60`,
            '--btn-shadow-lg': `0 10px 40px ${btnColor}bb`,
            width:'100%', padding:'17px 16px', borderRadius:'15px',
            display:'flex', alignItems:'center', justifyContent:'center', gap:'9px',
            fontSize:'16px', fontWeight:700, letterSpacing:'0.2px',
            border:'none', cursor:'pointer',
            background:btnColor, color:'#fff', fontFamily:'inherit',
          }">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
          {{ primaryLink.label || 'Rejoindre' }}
        </button>

        <!-- vsl-popup: once the popup has been closed, the bottom CTA behaves like
             the classic one — it goes straight to the link (age gate → destination)
             instead of re-opening the popup, so visitors aren't looped back. -->
        <button v-else-if="isPopup && popupShownOnce && !popupVisible && primaryLink" @click="handleLinkClick(primaryLink)"
          class="btn-bounce cta-appear"
          :style="{
            '--btn-shadow-sm': `0 4px 22px ${btnColor}60`,
            '--btn-shadow-lg': `0 10px 40px ${btnColor}bb`,
            width:'100%', padding:'17px 16px', borderRadius:'15px', textAlign:'center',
            display:'flex', alignItems:'center', justifyContent:'center', gap:'8px',
            fontSize:'16px', fontWeight:700, letterSpacing:'0.2px',
            border:'none', cursor:'pointer',
            background:btnColor, color:'#fff', fontFamily:'inherit',
          }">
          {{ primaryLink.label || 'Rejoindre' }}
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>

        <!-- CTA not revealed yet: progress bar -->
        <div v-else-if="!ctaVisible && page.cta_reveal_at && primaryLink && !isPopup" style="padding:4px 0">
          <div style="width:100%;height:3px;background:rgba(255,255,255,0.1);border-radius:999px;overflow:hidden;margin-bottom:8px">
            <div :style="{height:'100%',background:btnColor,borderRadius:'999px',width:ctaProgress+'%',transition:'width 1s linear'}"></div>
          </div>
          <p style="font-size:11px;color:rgba(255,255,255,0.3);margin:0;text-align:center;letter-spacing:0.02em">Continue watching...</p>
        </div>

        <!-- Handle banner — only visible if vsl-bandeau template and extra links -->
        <div v-if="isBandeau && additionalLinks.length > 0"
          @click="drawerOpen = true"
          class="drawer-handle"
          style="display:flex;flex-direction:column;align-items:center;gap:7px;cursor:pointer;padding:10px 0 4px">
          <div class="drawer-handle-arrow" :style="{color: btnColor, lineHeight:0}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
          </div>
          <span style="font-size:13px;font-weight:700;letter-spacing:0.01em;color:#fff;display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:999px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);backdrop-filter:blur(6px)">
            All my links
            <span :style="{fontSize:'11px',fontWeight:800,background:btnColor,color:'#fff',borderRadius:'999px',padding:'1px 8px',minWidth:'18px',textAlign:'center'}">{{ additionalLinks.length }}</span>
          </span>
        </div>

      </div>
    </div>

    <!-- Drawer bandeau (slide-up) -->
    <Teleport to="body">
      <div v-if="isBandeau && drawerOpen"
        style="position:fixed;inset:0;z-index:60;display:flex;align-items:flex-end;justify-content:center;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px)"
        @click.self="drawerOpen = false">
        <div style="background:#111;border-radius:24px 24px 0 0;padding:16px 20px 44px;max-width:480px;width:100%;max-height:88dvh;overflow-y:auto;border:1px solid rgba(255,255,255,0.08);border-bottom:none;animation:bandeau-up 0.32s cubic-bezier(0.16,1,0.3,1)">
          <!-- Handle -->
          <div style="display:flex;justify-content:center;margin-bottom:20px">
            <div style="width:36px;height:4px;background:rgba(255,255,255,0.2);border-radius:999px"></div>
          </div>

          <!-- ── Money links (OnlyFans / MYM) — highlighted ── -->
          <div v-if="drawerMoneyLinks.length > 0" style="display:flex;flex-direction:column;gap:11px;margin-bottom:22px">
            <button v-for="link in drawerMoneyLinks" :key="link.id ?? link.type"
              @click="handleLinkClick(link); drawerOpen = false"
              class="drawer-money-btn"
              :style="{
                width:'100%', border:'none', cursor:'pointer', fontFamily:'inherit',
                display:'flex', alignItems:'center', gap:'13px', padding:'15px 16px', borderRadius:'16px',
                background: PLATFORM_COLORS[linkPlatform(link)] || '#00AFF0',
                boxShadow: `0 8px 28px ${(PLATFORM_COLORS[linkPlatform(link)] || '#00AFF0')}55`,
              }">
              <span v-html="PLATFORM_SVG[linkPlatform(link)] || ''" style="display:flex;align-items:center;justify-content:center;width:26px;height:26px;flex-shrink:0"></span>
              <span style="flex:1;text-align:left;display:flex;flex-direction:column;gap:1px">
                <span style="font-size:15px;font-weight:800;color:#fff;line-height:1.15">{{ link.label || PLATFORM_SHORT[linkPlatform(link)] }}</span>
                <span style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.82)">Exclusive access →</span>
              </span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;opacity:0.9"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>

          <!-- ── Titre ── -->
          <p v-if="drawerSocialLinks.length > 0" style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 14px">Retrouve-moi sur</p>

          <!-- ── Social media squares (iOS style) ── -->
          <div v-if="drawerSocialLinks.length > 0" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
            <button v-for="link in drawerSocialLinks" :key="link.id ?? link.type"
              @click="handleLinkClick(link); drawerOpen = false"
              :style="{
                aspectRatio:'1', border:'none', cursor:'pointer',
                background:'#fff',
                borderRadius:'18px',
                display:'flex', flexDirection:'column', alignItems:'center', justifyContent:'center',
                gap:'6px', padding:'10px 6px',
                boxShadow:'0 2px 8px rgba(0,0,0,0.25)',
                transition:'transform 0.12s, box-shadow 0.12s',
              }"
              onmouseover="this.style.transform='scale(0.95)'"
              onmouseout="this.style.transform='scale(1)'">
              <span v-html="PLATFORM_SVG_DARK[link.type] || PLATFORM_SVG[link.type] || ''"
                style="display:flex;align-items:center;justify-content:center;line-height:1;width:28px;height:28px"></span>
              <span style="font-size:9px;font-weight:700;color:#1a1a2e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;padding:0 2px;letter-spacing:0.01em">
                {{ PLATFORM_SHORT[link.type] || link.label }}
              </span>
            </button>
          </div>

          <!-- ── Bouton image ── -->
          <div v-if="drawerImageLinks.length > 0" style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px">
            <button v-for="link in drawerImageLinks" :key="link.id ?? link.type"
              @click="handleLinkClick(link); drawerOpen = false"
              style="width:100%;position:relative;border-radius:16px;border:none;cursor:pointer;overflow:hidden;padding:0;transition:transform 0.15s"
              onmouseover="this.style.transform='scale(0.98)'"
              onmouseout="this.style.transform='scale(1)'">
              <img v-if="link.image_url" :src="link.image_url" style="width:100%;height:100px;object-fit:cover;display:block" />
              <div v-else style="width:100%;height:100px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;gap:8px">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <span style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.3)">{{ link.label }}</span>
              </div>
              <div style="position:absolute;top:8px;right:8px;width:26px;height:26px;border-radius:50%;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
              </div>
            </button>
          </div>

          <!-- ── Standard links (auto-detected icon + chevron) ── -->
          <div v-if="drawerClassicLinks.length > 0" style="display:flex;flex-direction:column;gap:10px">
            <button v-for="link in drawerClassicLinks" :key="link.id ?? link.type"
              @click="handleLinkClick(link); drawerOpen = false"
              class="drawer-classic-btn"
              :style="{
                width:'100%', padding:'12px 14px', borderRadius:'15px', border:'1px solid rgba(255,255,255,0.09)',
                cursor:'pointer', background:'rgba(255,255,255,0.05)', fontFamily:'inherit',
                display:'flex', alignItems:'center', gap:'13px',
              }">
              <span :style="{
                width:'40px', height:'40px', borderRadius:'12px', flexShrink:0,
                display:'flex', alignItems:'center', justifyContent:'center',
                background: linkPlatform(link) ? (PLATFORM_COLORS[linkPlatform(link)] || '#6D4EE8') : 'rgba(255,255,255,0.09)',
              }">
                <span v-if="linkPlatform(link)" v-html="PLATFORM_SVG[linkPlatform(link)]" style="display:flex;align-items:center;justify-content:center;width:22px;height:22px"></span>
                <svg v-else width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
              </span>
              <span style="flex:1;text-align:left;font-size:15px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ link.label }}</span>
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.38)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Branding — hidden on paid plans (white-label) -->
    <a v-if="page.show_branding" href="/" style="font-size:10px;color:rgba(255,255,255,0.12);margin-top:14px;text-decoration:none;letter-spacing:0.05em">
      Powered by <strong style="color:rgba(255,255,255,0.2)">MySocialVSL</strong>
    </a>

  </div>

  <!-- ─── Popup central (template vsl-popup) ───────────────────────────────── -->
  <Teleport to="body">
    <div v-if="isPopup && popupVisible"
      style="position:fixed;inset:0;z-index:80;display:flex;align-items:safe center;justify-content:center;padding:24px;background:rgba(0,0,0,0.72);backdrop-filter:blur(4px);overflow-y:auto"
      @click.self="popupVisible = false">
      <div style="position:relative;background:#13101f;border-radius:24px;padding:0;max-width:330px;width:100%;border:1px solid rgba(255,255,255,0.09);box-shadow:0 32px 80px rgba(0,0,0,0.7);overflow:hidden;animation:popup-bounce-in 0.5s cubic-bezier(0.34,1.56,0.64,1) both">

        <!-- Discreet close (X) in the corner -->
        <button @click="popupVisible = false" aria-label="Fermer"
          style="position:absolute;top:10px;right:10px;z-index:2;width:30px;height:30px;border-radius:50%;border:none;cursor:pointer;background:rgba(0,0,0,0.45);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;padding:0">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.85)" stroke-width="2.4" stroke-linecap="round"><line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/></svg>
        </button>

        <!-- Image hero + nom en overlay -->
        <div v-if="page.popup_image_url" style="position:relative">
          <img :src="page.popup_image_url" style="width:100%;height:200px;object-fit:cover;display:block" />
          <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(19,16,31,0.96) 3%,rgba(19,16,31,0.35) 45%,transparent 75%)"></div>
          <div style="position:absolute;bottom:12px;left:16px;right:16px;display:flex;align-items:center;gap:6px">
            <p style="font-size:18px;font-weight:800;color:#fff;margin:0;text-shadow:0 1px 8px rgba(0,0,0,0.6)">{{ page.model_name }}</p>
            <span v-if="page.online_status" class="online-dot" style="flex-shrink:0"></span>
            <svg v-if="page.verified_badge" viewBox="0 0 24 24" width="15" height="15" style="flex-shrink:0"><circle cx="12" cy="12" r="11" fill="#3b82f6"/><path d="M7 12.5l3.5 3.5 6.5-7" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
          </div>
        </div>

        <div style="padding:18px 20px 22px">
          <!-- Title: the manager's custom popup title, falling back to the model name -->
          <p v-if="!page.popup_image_url && (page.popup_title || page.model_name)" style="font-size:18px;font-weight:800;color:#fff;margin:0 0 10px">{{ page.popup_title || page.model_name }}</p>

          <!-- "Incoming DM" framing: online + animated typing indicator -->
          <div v-if="page.online_status" style="display:flex;align-items:center;gap:9px;margin-bottom:14px;flex-wrap:wrap">
            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(16,185,129,0.13);border:1px solid rgba(16,185,129,0.25);border-radius:999px;padding:4px 11px">
              <span class="online-dot"></span>
              <span style="font-size:11px;font-weight:700;color:#10b981;letter-spacing:0.02em">Online</span>
            </div>
            <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-style:italic;color:rgba(255,255,255,0.5)">
              {{ page.model_name || 'She' }} is typing
              <span class="pp-typing"><i></i><i></i><i></i></span>
            </span>
          </div>

          <!-- Subtitle/text: custom popup subtitle, falling back to legacy popup_text -->
          <p v-if="page.popup_subtitle || page.popup_text" style="font-size:14px;color:rgba(255,255,255,0.7);line-height:1.55;margin:0 0 16px">{{ page.popup_subtitle || page.popup_text }}</p>

          <!-- CTA bounce + arrow -->
          <button v-if="primaryLink" @click="handleLinkClick(primaryLink); popupVisible = false"
            class="btn-bounce pp-cta-shine"
            :style="{
              '--btn-shadow-sm': `0 6px 24px ${btnColor}66`,
              '--btn-shadow-lg': `0 12px 44px ${btnColor}cc`,
              width:'100%', padding:'17px 16px', borderRadius:'15px',
              display:'flex', alignItems:'center', justifyContent:'center', gap:'8px',
              fontSize:'16px', fontWeight:800, letterSpacing:'0.2px', border:'none', cursor:'pointer',
              background:`linear-gradient(135deg, ${btnColor}, ${btnColor}cc)`,
              boxShadow:`0 6px 24px ${btnColor}66`,
              color:'#fff', fontFamily:'inherit', position:'relative', overflow:'hidden',
            }">
            {{ primaryLink.label || 'Rejoindre' }}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>

          <!-- Refus discret -->
          <button @click="popupVisible = false"
            style="display:block;margin:11px auto 0;background:none;border:none;color:rgba(255,255,255,0.26);font-size:11px;cursor:pointer;font-family:inherit;padding:3px 8px">
            Plus tard
          </button>
        </div>
      </div>
    </div>
  </Teleport>

  <!-- ─── FOMO modal ─────────────────────────────────────────────────────────── -->
  <Teleport to="body">
    <div v-if="fomoVisible" style="position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:90;display:flex;align-items:flex-end;justify-content:center;padding:0;backdrop-filter:blur(3px)" @click.self="fomoVisible=false">
      <div style="background:#13101f;border-radius:24px 24px 0 0;padding:28px 24px 32px;max-width:480px;width:100%;text-align:center;border:1px solid rgba(255,255,255,0.08);border-bottom:none;animation:fomo-slide-up 0.35s cubic-bezier(0.16,1,0.3,1)">
        <button @click="fomoVisible=false" style="position:absolute;margin-left:auto;background:none;border:none;color:rgba(255,255,255,0.35);font-size:20px;cursor:pointer;float:right;line-height:1;padding:4px">
          <i class="bi bi-x-lg"></i>
        </button>
        <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);border-radius:999px;padding:4px 14px;margin-bottom:14px">
          <span style="width:7px;height:7px;border-radius:50%;background:#ef4444;animation:pulse-rec 1.4s ease-in-out infinite"></span>
          <span style="font-size:11px;font-weight:800;color:#f87171;letter-spacing:0.08em;text-transform:uppercase">Limited offer</span>
        </div>
        <h2 style="font-size:20px;font-weight:800;color:#fff;margin:0 0 8px">{{ page?.fomo_title || "Don't miss out" }}</h2>
        <p v-if="page?.fomo_message" style="color:rgba(255,255,255,0.55);font-size:14px;line-height:1.6;margin:0 0 20px">{{ page.fomo_message }}</p>
        <button @click="fomoCta"
          :style="{width:'100%',padding:'15px',borderRadius:'14px',border:'none',cursor:'pointer',background:page?.btn_color||'#6D4EE8',color:'#fff',fontSize:'15px',fontWeight:700,fontFamily:'inherit'}">
          {{ page?.fomo_cta_label || 'See exclusive content' }}
        </button>
      </div>
    </div>
  </Teleport>

  <!-- ─── Age gate modal ─────────────────────────────────────────────────────── -->
  <Teleport to="body">
    <div v-if="ageModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.88);z-index:100;display:flex;align-items:safe center;justify-content:center;padding:24px;backdrop-filter:blur(4px);overflow-y:auto">
      <div style="background:#111;border-radius:24px;padding:40px 28px;max-width:360px;width:100%;text-align:center;border:1px solid rgba(255,255,255,0.08);box-shadow:0 24px 60px rgba(0,0,0,0.6)">
        <i class="bi bi-shield-x" style="font-size:56px;margin-bottom:12px;display:block;color:#F87171"></i>
        <h2 style="font-size:22px;font-weight:800;color:#fff;margin-bottom:8px">18+ Content</h2>
        <p style="color:#6b7280;font-size:14px;line-height:1.65;margin-bottom:28px">
          This content is for adults only.<br/>Please confirm you are 18 years or older.
        </p>
        <button @click="confirmAge"
          :style="{width:'100%',padding:'15px',borderRadius:'14px',border:'none',cursor:'pointer',background:pendingLink?.btn_color||page?.btn_color||'#6D4EE8',color:'#fff',fontSize:'15px',fontWeight:700,marginBottom:'12px',fontFamily:'inherit',transition:'opacity 0.15s'}"
          onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
          I confirm I am 18+ <i class="bi bi-check"></i>
        </button>
        <button @click="ageModal=false" style="background:none;border:none;color:#6b7280;font-size:13px;cursor:pointer;font-family:inherit;padding:6px">
          Go back
        </button>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/lib/axios'

const route = useRoute()
// Slug comes from the URL (/p/:slug) or, on a custom domain served at root by
// Laravel, from the injected window.__VSL_SLUG__.
const slug = route.params.slug || window.__VSL_SLUG__
const page = ref(null)
const loading = ref(true)
const ageModal = ref(false)
const pendingLink = ref(null)
const pageStartTime = ref(0)

// ─── VSL ───────────────────────────────────────────────────────────────────────
const vslActive = computed(() => !!page.value?.video_url && page.value?.vsl_enabled !== false)

const primaryLink = computed(() =>
  (page.value?.links || []).find(l => l.is_visible !== false && l.url)
)

const isBandeau = computed(() => page.value?.template === 'vsl-bandeau')
const isPopup = computed(() => page.value?.template === 'vsl-popup')
const drawerOpen = ref(false)
const popupVisible = ref(false)
const popupShownOnce = ref(false) // true once the popup has appeared → reveals the reopen CTA
let popupTimer = null

function openPopup() {
  popupVisible.value = true
  popupShownOnce.value = true
}

const additionalLinks = computed(() =>
  (page.value?.links || []).filter(l => l.is_visible !== false && l.url).slice(1)
)

const SOCIAL_TYPES = new Set(['onlyfans','mym','instagram','tiktok','twitter','telegram','youtube','twitch','snapchat'])

const PLATFORM_COLORS: Record<string,string> = {
  onlyfans: '#00AFF0', mym: '#222', instagram: '#E1306C',
  tiktok: '#010101', twitter: '#111', telegram: '#26A5E4',
  youtube: '#FF0000', twitch: '#9146FF', snapchat: '#FFFC00',
}

const PLATFORM_SVG: Record<string,string> = {
  onlyfans: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 5.838a6.162 6.162 0 1 1 0 12.324A6.162 6.162 0 0 1 12 5.838zm0 2.456a3.706 3.706 0 1 0 0 7.412 3.706 3.706 0 0 0 0-7.412z"/></svg>`,
  mym: `<svg viewBox="0 0 24 24" fill="none" width="22" height="22"><text x="3" y="18" font-size="15" font-weight="900" fill="white" font-family="Arial">M</text></svg>`,
  instagram: `<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" width="22" height="22"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="white" stroke="none"/></svg>`,
  tiktok: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>`,
  twitter: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`,
  telegram: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>`,
  youtube: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M23 7s-.3-2-1.2-2.7c-1.1-1.2-2.4-1.2-3-1.3C16.6 3 12 3 12 3s-4.6 0-6.8.1c-.6.1-1.9.1-3 1.3C1.3 5 1 7 1 7S.7 9.2.7 11.5v2.1c0 2.2.3 4.4.3 4.4s.3 2 1.2 2.7c1.1 1.2 2.6 1.1 3.3 1.2C7.5 22 12 22 12 22s4.6 0 6.8-.2c.6-.1 1.9-.1 3-1.3.9-.7 1.2-2.7 1.2-2.7s.3-2.2.3-4.4v-2.1C23.3 9.2 23 7 23 7zM9.7 15.5V8.4l8.1 3.6-8.1 3.5z"/></svg>`,
  twitch: `<svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>`,
  snapchat: `<svg viewBox="0 0 24 24" fill="#000" width="22" height="22"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.146 0 .27.029.383.074.42.194.789.3 1.104.3.234 0 .384-.06.465-.105l-.046-.569c-.098-1.626-.225-3.651.307-4.837C7.392 1.077 10.739.807 11.727.807l.419-.015h.06z"/></svg>`,
}

// Dark versions (for white background) of the social media icons
const PLATFORM_SVG_DARK: Record<string,string> = {
  onlyfans: `<svg viewBox="0 0 24 24" fill="#00AFF0" width="26" height="26"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 5.838a6.162 6.162 0 1 1 0 12.324A6.162 6.162 0 0 1 12 5.838zm0 2.456a3.706 3.706 0 1 0 0 7.412 3.706 3.706 0 0 0 0-7.412z"/></svg>`,
  mym: `<svg viewBox="0 0 24 24" fill="none" width="26" height="26"><text x="3" y="18" font-size="15" font-weight="900" fill="#222" font-family="Arial">M</text></svg>`,
  instagram: `<svg viewBox="0 0 24 24" fill="none" stroke="url(#igGrad)" stroke-width="1.8" stroke-linecap="round" width="26" height="26"><defs><linearGradient id="igGrad" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" stop-color="#f09433"/><stop offset="25%" stop-color="#e6683c"/><stop offset="50%" stop-color="#dc2743"/><stop offset="75%" stop-color="#cc2366"/><stop offset="100%" stop-color="#bc1888"/></linearGradient></defs><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="#cc2366" stroke="none"/></svg>`,
  tiktok: `<svg viewBox="0 0 24 24" fill="#010101" width="26" height="26"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>`,
  twitter: `<svg viewBox="0 0 24 24" fill="#111" width="26" height="26"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`,
  telegram: `<svg viewBox="0 0 24 24" fill="#26A5E4" width="26" height="26"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>`,
  youtube: `<svg viewBox="0 0 24 24" fill="#FF0000" width="26" height="26"><path d="M23 7s-.3-2-1.2-2.7c-1.1-1.2-2.4-1.2-3-1.3C16.6 3 12 3 12 3s-4.6 0-6.8.1c-.6.1-1.9.1-3 1.3C1.3 5 1 7 1 7S.7 9.2.7 11.5v2.1c0 2.2.3 4.4.3 4.4s.3 2 1.2 2.7c1.1 1.2 2.6 1.1 3.3 1.2C7.5 22 12 22 12 22s4.6 0 6.8-.2c.6-.1 1.9-.1 3-1.3.9-.7 1.2-2.7 1.2-2.7s.3-2.2.3-4.4v-2.1C23.3 9.2 23 7 23 7zM9.7 15.5V8.4l8.1 3.6-8.1 3.5z"/></svg>`,
  twitch: `<svg viewBox="0 0 24 24" fill="#9146FF" width="26" height="26"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>`,
  snapchat: `<svg viewBox="0 0 24 24" fill="#FFFC00" width="26" height="26"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.146 0 .27.029.383.074.42.194.789.3 1.104.3.234 0 .384-.06.465-.105l-.046-.569c-.098-1.626-.225-3.651.307-4.837C7.392 1.077 10.739.807 11.727.807l.419-.015h.06z"/></svg>`,
}

const PLATFORM_SHORT: Record<string,string> = {
  onlyfans: 'OnlyFans', mym: 'MYM', instagram: 'Instagram', tiktok: 'TikTok',
  twitter: 'X', telegram: 'Telegram', youtube: 'YouTube', twitch: 'Twitch', snapchat: 'Snapchat',
}

// "money" links (paid platforms) — highlighted at the top of the drawer.
// Detected by type OR by label (e.g. a custom link "Mon OF" becomes a money button).
const drawerMoneyLinks = computed(() =>
  additionalLinks.value.filter(isMoneyLink)
)
const drawerSocialLinks = computed(() =>
  additionalLinks.value.filter(l => SOCIAL_TYPES.has(l.type) && !isMoneyLink(l)).slice(0, 4)
)
const drawerImageLinks = computed(() =>
  additionalLinks.value.filter(l => !SOCIAL_TYPES.has(l.type) && (l.image_url || l.type === 'image_button')).slice(0, 2)
)
const drawerClassicLinks = computed(() =>
  additionalLinks.value.filter(l => !SOCIAL_TYPES.has(l.type) && !l.image_url && l.type !== 'image_button' && !isMoneyLink(l))
)

// A link is "money" if its detected platform is OnlyFans or MYM
function isMoneyLink(link: any): boolean {
  const p = linkPlatform(link)
  return p === 'onlyfans' || p === 'mym'
}

// Guess a custom link's platform from its type or label
// (e.g. "Mon Instagram" → instagram) to show the right brand icon.
function linkPlatform(link: any): string | null {
  if (link.type && PLATFORM_SHORT[link.type]) return link.type
  const s = (link.label || '').toLowerCase()
  if (/instagram|insta\b|\big\b/.test(s)) return 'instagram'
  if (/tiktok|tik\s?tok|\btt\b/.test(s)) return 'tiktok'
  if (/snap/.test(s)) return 'snapchat'
  if (/telegram|\btg\b/.test(s)) return 'telegram'
  if (/twitter|\bx\b/.test(s)) return 'twitter'
  if (/youtube|\byt\b/.test(s)) return 'youtube'
  if (/twitch/.test(s)) return 'twitch'
  if (/onlyfans|only\s?fans|\bof\b/.test(s)) return 'onlyfans'
  if (/\bmym\b/.test(s)) return 'mym'
  return null
}

const btnColor = computed(() =>
  primaryLink.value?.btn_color || page.value?.btn_color || '#6D4EE8'
)

// CTA reveal
const ctaVisible = ref(true)
const videoCurrentTime = ref(0)
const ctaProgress = computed(() => {
  const at = page.value?.cta_reveal_at
  if (!at) return 100
  return Math.min(100, (videoCurrentTime.value / at) * 100)
})

// ─── FOMO modal ────────────────────────────────────────────────────────────────
const fomoVisible = ref(false)
let fomoTimer = null

function fomoCta() {
  fomoVisible.value = false
  if (primaryLink.value) handleLinkClick(primaryLink.value)
}

// ─── SEO / Open Graph ──────────────────────────────────────────────────────────
function applySeoMeta(data) {
  document.title = data.meta_title || (data.model_name ? `${data.model_name}` : 'My Links')
  const ensure = (attr, key, content) => {
    if (!content) return
    let el = document.querySelector(`meta[${attr}="${key}"]`)
    if (!el) { el = document.createElement('meta'); el.setAttribute(attr, key); document.head.appendChild(el) }
    el.setAttribute('content', content)
  }
  ensure('name', 'description', data.meta_description || data.bio)
  ensure('property', 'og:type', 'website')
  ensure('property', 'og:title', data.meta_title || data.model_name)
  ensure('property', 'og:description', data.meta_description || data.bio)
  ensure('property', 'og:image', data.og_image_url || data.avatar_url)
}

// ─── UTM passthrough ───────────────────────────────────────────────────────────
function withUtm(url) {
  if (!url || page.value?.utm_passthrough === false) return url
  try {
    const current = new URLSearchParams(window.location.search)
    const u = new URL(url)
    for (const [k, v] of current.entries()) {
      if (k.startsWith('utm_') && !u.searchParams.has(k)) u.searchParams.set(k, v)
    }
    return u.toString()
  } catch { return url }
}

// ─── Video ─────────────────────────────────────────────────────────────────────
const videoEl = ref(null)
const videoMuted = ref(true)
const videoPlayTracked = ref(false)
const watchMilestonesTracked = ref(new Set<number>())
const videoStartTime = ref<number | null>(null)
const watchSeconds = ref(0)
const lastVideoBucket = ref(-1)

function onVideoPlay() {
  if (videoPlayTracked.value) return
  videoPlayTracked.value = true
  videoStartTime.value = Date.now()
  api.post(`/p/${slug}/event`, { type: 'video_play' }).catch(() => {})
}

function onVideoTimeUpdate() {
  const el = videoEl.value as HTMLVideoElement | null
  if (!el || !el.duration) return

  const current = el.currentTime
  videoCurrentTime.value = current

  // Tracking drop-off toutes les 10s
  const bucket = Math.floor(current / 10)
  if (bucket > 0 && bucket !== lastVideoBucket.value) {
    lastVideoBucket.value = bucket
    api.post(`/p/${slug}/event`, { type: 'video_position', value: bucket * 10 }).catch(() => {})
  }

  // CTA reveal
  if (!ctaVisible.value && page.value?.cta_reveal_at && current >= page.value.cta_reveal_at) {
    ctaVisible.value = true
  }

  // Milestones (analytics compatibility)
  const pct = Math.floor((current / el.duration) * 100)
  for (const milestone of [25, 50, 75, 100]) {
    if (pct >= milestone && !watchMilestonesTracked.value.has(milestone)) {
      watchMilestonesTracked.value.add(milestone)
      api.post(`/p/${slug}/event`, { type: 'video_progress', value: milestone }).catch(() => {})
    }
  }

  if (videoStartTime.value) {
    watchSeconds.value = Math.floor((Date.now() - videoStartTime.value) / 1000)
  }
}

const isLocalVideo = computed(() => {
  const url = page.value?.video_url || ''
  if (!url) return false
  const iframeServices = ['youtube.com', 'youtu.be', 'vimeo.com', 'streamable.com', 'dailymotion.com', 'twitch.tv']
  return !iframeServices.some(s => url.includes(s))
})

function toggleMute() {
  videoMuted.value = !videoMuted.value
  if (videoEl.value) (videoEl.value as HTMLVideoElement).muted = videoMuted.value
  if (!videoMuted.value) {
    api.post(`/p/${slug}/event`, { type: 'video_unmute' }).catch(() => {})
  }
}

const videoEmbedUrl = computed(() => {
  const url = page.value?.video_url || ''
  if (url.includes('streamable.com')) {
    const id = url.split('/').pop().split('?')[0]
    return `https://streamable.com/e/${id}?autoplay=1&muted=1&loop=1`
  }
  if (url.includes('cloudflarestream.com')) return url + (url.includes('?') ? '&' : '?') + 'autoplay=true&muted=true&loop=true'
  return url
})

// ─── Age gate + navigation ─────────────────────────────────────────────────────
function handleLinkClick(link) {
  if (page.value?.age_gate) { pendingLink.value = link; ageModal.value = true }
  else goToLink(link)
}

function confirmAge() {
  api.post(`/p/${slug}/event`, { type: 'age_confirmed' }).catch(() => {})
  ageModal.value = false
  goToLink(pendingLink.value)
  pendingLink.value = null
}

// Reliable event delivery: sendBeacon survives the immediate navigation to
// OnlyFans, where a normal XHR gets cancelled mid-flight and the click is lost
// — silently undercounting CTR, the metric that matters most here. The JSON
// Blob type is required so Laravel parses the body; a plain string arrives as
// text/plain and is dropped by validation.
function sendEvent(type, extra = {}) {
  const payload = JSON.stringify({ type, ...extra })
  if (navigator.sendBeacon) {
    navigator.sendBeacon(`/api/p/${slug}/event`, new Blob([payload], { type: 'application/json' }))
  } else {
    api.post(`/p/${slug}/event`, { type, ...extra }).catch(() => {})
  }
}

function goToLink(link) {
  sendEvent('link_click', {
    page_link_id: link.id,
    value: watchSeconds.value > 0 ? watchSeconds.value : null,
  })
  const url = withUtm(link.url)
  if (!url) return
  if (page.value?.deep_link_enabled) {
    const ua = navigator.userAgent || ''
    const isAndroid = /Android/.test(ua)
    const isInApp = /Instagram|FBAN|FBAV|TikTok/.test(ua)
    if (isInApp) {
      const enc = encodeURIComponent(url)
      if (isAndroid) {
        try { const u = new URL(url); window.location.href = `intent://${u.host}${u.pathname}${u.search}#Intent;scheme=https;S.browser_fallback_url=${enc};end` }
        catch { window.location.href = url }
      } else {
        window.location.href = `instagram://extbrowser/?url=${enc}`
        setTimeout(() => { window.location.href = url }, 1500)
      }
      return
    }
  }
  window.location.href = url
}

function applyDeepLinkBypass() {
  const ua = navigator.userAgent || ''
  if (!/Instagram|FBAN|FBAV/.test(ua)) return
  const url = window.location.href
  const enc = encodeURIComponent(url)
  if (/Android/.test(ua)) {
    try { const u = new URL(url); window.location.href = `intent://${u.host}${u.pathname}${u.search}#Intent;scheme=https;S.browser_fallback_url=${enc};end` }
    catch {}
  } else {
    window.location.href = `instagram://extbrowser/?url=${enc}`
    setTimeout(() => { window.location.href = url }, 1500)
  }
}

// ─── Shield Protection™ ────────────────────────────────────────────────────────
// NB: "Instagram" is deliberately NOT here — the in-app Instagram browser is a real human,
// not a crawler. Treating it as a bot would serve the decoy page to actual visitors.
const META_BOT_PAT = 'facebookexternalhit|Facebot|facebookcatalog|FacebookBot|meta-externalagent'
const CRAWL_BOT_PAT = 'bot|crawl|spider|slurp|LinkedInBot|Twitterbot|WhatsApp|Slackbot|TelegramBot|Discordbot|pinterest|Applebot|bingbot|Googlebot|YandexBot|DuckDuckBot|ia_archiver|AhrefsBot|SemrushBot|MJ12bot|DotBot|BLEXBot|DataForSeoBot|PetalBot|BaiduSpider'

function isMetaBot(): boolean {
  return new RegExp(META_BOT_PAT, 'i').test(navigator.userAgent)
}
function isBot(): boolean {
  return isMetaBot() || new RegExp(CRAWL_BOT_PAT, 'i').test(navigator.userAgent)
}

function serveDecoyPage() {
  document.querySelectorAll('meta').forEach(m => m.remove())
  const head = document.head
  const metas = [
    { name: 'robots', content: 'noindex, nofollow' },
    { property: 'og:type', content: 'website' },
    { property: 'og:title', content: 'My Links' },
    { property: 'og:description', content: 'Check out my latest content' },
  ]
  metas.forEach(m => {
    const el = document.createElement('meta')
    Object.entries(m).forEach(([k, v]) => el.setAttribute(k, v))
    head.appendChild(el)
  })
  document.title = 'My Links'
  document.body.innerHTML = `
    <div style="font-family:sans-serif;max-width:480px;margin:80px auto;padding:24px;text-align:center">
      <div style="width:48px;height:48px;border-radius:12px;background:#6D4EE8;margin:0 auto 16px;display:flex;align-items:center;justify-content:center">
        <span style="color:#fff;font-weight:900;font-size:14px">VSL</span>
      </div>
      <h1 style="font-size:18px;font-weight:700;margin:0 0 8px;color:#111">My Links</h1>
      <p style="color:#6b7280;font-size:14px;margin:0">Follow me on social media for my latest content.</p>
    </div>
  `
}

// ─── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(async () => {
  applyDeepLinkBypass()
  try {
    const { data } = await api.get(`/p/${slug}`)
    page.value = data
    ctaVisible.value = !data.cta_reveal_at

    if (data.bot_protection) {
      if (isMetaBot()) {
        api.post(`/p/${slug}/event`, { type: 'bot_blocked', ua: navigator.userAgent }).catch(() => {})
        serveDecoyPage()
        return
      }
      if (isBot()) {
        serveDecoyPage()
        return
      }
    }

    pageStartTime.value = Date.now()
    const referrer = document.referrer ? document.referrer.slice(0, 500) : null
    api.post(`/p/${slug}/event`, { type: 'page_view', referrer }).catch(() => {})

    if (data.link_preview_enabled !== false) applySeoMeta(data)

    if (data.template === 'vsl-popup') {
      popupTimer = setTimeout(openPopup, (data.popup_delay_seconds ?? 5) * 1000)
    }

    if (data.fomo_enabled && (data.fomo_title || data.fomo_message)) {
      fomoTimer = setTimeout(() => { fomoVisible.value = true }, (data.fomo_delay_seconds ?? 5) * 1000)
    }
  } catch { page.value = null }
  finally { loading.value = false }
})

onUnmounted(() => {
  if (popupTimer) clearTimeout(popupTimer)
  if (fomoTimer) clearTimeout(fomoTimer)
  if (pageStartTime.value && page.value) {
    const secs = Math.round((Date.now() - pageStartTime.value) / 1000)
    if (secs >= 2) {
      sendEvent('time_on_page', { value: secs })
    }
  }
})
</script>

<style>
@keyframes spin { to { transform: rotate(360deg) } }
@keyframes pulse-rec { 0%,100% { opacity:1 } 50% { opacity:0.3 } }
@keyframes pulse-online { 0% { box-shadow:0 0 0 0 rgba(16,185,129,0.5) } 70% { box-shadow:0 0 0 8px rgba(16,185,129,0) } 100% { box-shadow:0 0 0 0 rgba(16,185,129,0) } }
@keyframes pulse-unmute { 0%,100% { box-shadow:0 0 0 0 rgba(255,255,255,0.3) } 50% { box-shadow:0 0 0 6px rgba(255,255,255,0) } }
@keyframes fomo-slide-up { from { transform:translateY(100%); opacity:0 } to { transform:translateY(0); opacity:1 } }
@keyframes bandeau-up { from { transform:translateY(60px); opacity:0 } to { transform:translateY(0); opacity:1 } }
@keyframes popup-bounce-in { 0% { transform:scale(0.7); opacity:0 } 60% { transform:scale(1.04) } 100% { transform:scale(1); opacity:1 } }
@keyframes cta-appear { from { opacity:0; transform:translateY(10px) } to { opacity:1; transform:translateY(0) } }
@keyframes btn-bounce {
  0%,100% { transform:scale(1);     box-shadow:var(--btn-shadow-sm) }
  48%     { transform:scale(1.035); box-shadow:var(--btn-shadow-lg) }
}

.cta-appear { animation: cta-appear 0.45s cubic-bezier(0.16,1,0.3,1) both }
.btn-bounce { animation: btn-bounce 2.6s cubic-bezier(0.45,0,0.55,1) infinite }
.btn-bounce:active { transform: scale(0.96) !important; animation: none !important }

.online-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #10b981;
  animation: pulse-online 2s ease-out infinite;
  flex-shrink: 0;
}

/* Tiroir (vsl-bandeau) */
@keyframes handle-bounce { 0%,100% { transform:translateY(0) } 50% { transform:translateY(-4px) } }
.drawer-handle { transition: transform 0.12s }
.drawer-handle:active { transform: scale(0.97) }
.drawer-handle-arrow { animation: handle-bounce 1.8s ease-in-out infinite }
.drawer-money-btn { transition: transform 0.14s, filter 0.14s }
.drawer-money-btn:hover { filter: brightness(1.07) }
.drawer-money-btn:active { transform: scale(0.97) }
.drawer-classic-btn { transition: transform 0.14s, background 0.15s, border-color 0.15s }
.drawer-classic-btn:hover { background: rgba(255,255,255,0.09) !important; border-color: rgba(255,255,255,0.16) !important }
.drawer-classic-btn:active { transform: scale(0.98) }
</style>
