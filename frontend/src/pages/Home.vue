<template>
  <div class="w-full bg-black text-white font-sans antialiased">

    <!-- NAV -->
    <header style="position:fixed;top:0;left:0;right:0;z-index:50;border-bottom:1px solid rgba(255,255,255,0.07);background:rgba(0,0,0,0.85);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)">
      <div style="max-width:1200px;margin:0 auto;padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between">
        <!-- Logo -->
        <div style="display:flex;align-items:center;gap:10px">
          <div style="width:30px;height:30px;background:linear-gradient(135deg,#6D4EE8,#8B6FF0);border-radius:8px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 16px rgba(109,78,232,0.4)">
            <span style="color:#fff;font-weight:900;font-size:9px;letter-spacing:-0.05em">VSL</span>
          </div>
          <span style="font-weight:700;color:#fff;font-size:15px;letter-spacing:-0.02em">MySocialVSL</span>
        </div>
        <!-- Nav -->
        <nav style="display:flex;align-items:center;gap:32px">
          <a href="#features" style="color:rgba(255,255,255,0.45);font-size:14px;text-decoration:none;transition:color 0.15s;font-weight:500"
            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">Features</a>
          <a href="#pricing" style="color:rgba(255,255,255,0.45);font-size:14px;text-decoration:none;transition:color 0.15s;font-weight:500"
            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">Pricing</a>
          <a href="#faq" style="color:rgba(255,255,255,0.45);font-size:14px;text-decoration:none;transition:color 0.15s;font-weight:500"
            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">FAQ</a>
        </nav>
        <!-- CTA -->
        <div style="display:flex;align-items:center;gap:16px">
          <RouterLink to="/login" style="color:rgba(255,255,255,0.45);font-size:14px;text-decoration:none;font-weight:500;transition:color 0.15s"
            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">Login</RouterLink>
          <RouterLink to="/register"
            style="background:linear-gradient(135deg,#6D4EE8,#8B6FF0);color:#fff;padding:10px 22px;border-radius:999px;font-size:13px;font-weight:700;text-decoration:none;box-shadow:0 0 20px rgba(109,78,232,0.35);transition:all 0.2s"
            onmouseover="this.style.boxShadow='0 0 30px rgba(109,78,232,0.55)'" onmouseout="this.style.boxShadow='0 0 20px rgba(109,78,232,0.35)'">
            Get Started Free →
          </RouterLink>
        </div>
      </div>
    </header>

    <!-- HERO -->
    <section style="min-height:100vh;display:flex;align-items:center;padding:96px 24px 80px;position:relative;overflow:hidden;">
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 60% at 60% 40%,rgba(109,78,232,0.14),transparent);pointer-events:none"></div>
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse 50% 50% at 20% 80%,rgba(109,78,232,0.07),transparent);pointer-events:none"></div>

      <div style="max-width:1200px;margin:0 auto;width:100%;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;position:relative;z-index:10">

        <!-- Left — texte -->
        <div>
          <div data-aos="fade-right" style="display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(109,78,232,0.3);border-radius:999px;padding:6px 16px;font-size:12px;color:rgba(255,255,255,0.5);margin-bottom:32px;background:rgba(109,78,232,0.08)">
            <span style="width:6px;height:6px;border-radius:50%;background:#A78BFA;display:inline-block;animation:pulse-dot 2s ease-in-out infinite"></span>
            The #1 VSL link-in-bio for content creators
          </div>

          <h1 data-aos="fade-right" data-aos-delay="80"
            style="font-weight:800;color:#fff;letter-spacing:-0.04em;line-height:1.05;margin-bottom:24px;font-size:clamp(2.4rem,5vw,4rem)">
            The link-in-bio that
            <span style="background:linear-gradient(135deg,#A78BFA,#6D4EE8,#8B6FF0);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"> actually converts</span>
          </h1>

          <p data-aos="fade-right" data-aos-delay="140" style="color:rgba(255,255,255,0.45);font-size:1.1rem;line-height:1.7;margin-bottom:40px;max-width:440px">
            Replace Linktree. Your audience sees your video, clicks directly into the app. Result: <strong style="color:rgba(255,255,255,0.8)">up to 3× more conversions.</strong>
          </p>

          <div data-aos="fade-right" data-aos-delay="180" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:40px">
            <RouterLink to="/register"
              style="background:linear-gradient(135deg,#6D4EE8,#8B6FF0);color:#fff;padding:14px 28px;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;display:inline-block;box-shadow:0 0 32px rgba(109,78,232,0.5)">
              Create my page for free →
            </RouterLink>
            <a href="#features" style="color:rgba(255,255,255,0.35);font-size:14px;text-decoration:none;display:flex;align-items:center;gap:5px">
              See how it works ↓
            </a>
          </div>

          <!-- Social proof -->
          <div data-aos="fade-right" data-aos-delay="220" style="display:flex;flex-direction:column;gap:10px">
            <div v-for="stat in heroStats" :key="stat.label" style="display:flex;align-items:center;gap:10px">
              <div style="width:6px;height:6px;border-radius:50%;background:#34d399;flex-shrink:0"></div>
              <span style="font-size:13px;color:rgba(255,255,255,0.5)">
                <strong style="color:rgba(255,255,255,0.9)">{{ stat.value }}</strong> {{ stat.label }}
              </span>
            </div>
          </div>
        </div>

        <!-- Right — Phone 3D -->
        <div data-aos="fade-left" data-aos-delay="100" style="display:flex;justify-content:center;align-items:center">
          <div style="transform:scale(0.65);transform-origin:center center">
            <PhoneDemo
              name="🌸 Karine"
              handle="@karinefrenchwoman"
              bio="Je partage ici ce qui me ressemble,<br>sans en faire trop 😌"
              avatar="/karine.JPG"
              video-id="0ed1q5"
              cta="🔓 Accès privé — Rejoindre"
            />
          </div>
        </div>

      </div>
    </section>

    <!-- SCROLLING PLATFORM BANNER -->
    <div style="border-top:1px solid rgba(255,255,255,0.06);border-bottom:1px solid rgba(255,255,255,0.06);padding:36px 0;overflow:hidden;position:relative;background:rgba(255,255,255,0.01)">
      <!-- fade edges -->
      <div style="position:absolute;left:0;top:0;bottom:0;width:140px;background:linear-gradient(to right,#000,transparent);z-index:2;pointer-events:none"></div>
      <div style="position:absolute;right:0;top:0;bottom:0;width:140px;background:linear-gradient(to left,#000,transparent);z-index:2;pointer-events:none"></div>

      <p style="text-align:center;font-size:11px;color:rgba(255,255,255,0.2);text-transform:uppercase;letter-spacing:0.18em;margin-bottom:28px;font-weight:600">Works on every social platform</p>

      <div class="scroll-track">
        <div class="scroll-inner">
          <!-- logos × 2 for seamless loop -->
          <template v-for="pass in 2" :key="pass">
            <div v-for="p in platformLogos" :key="p.name + pass"
              style="display:inline-flex;flex-direction:column;align-items:center;gap:10px;padding:0 48px;flex-shrink:0;vertical-align:top">
              <div :style="{width:'52px',height:'52px',borderRadius:'14px',background:'rgba(255,255,255,0.07)',border:'1px solid rgba(255,255,255,0.1)',display:'flex',alignItems:'center',justifyContent:'center',transition:'all 0.2s',opacity:'0.7'}" v-html="p.svg"></div>
              <span style="font-size:11px;color:rgba(255,255,255,0.4);font-weight:500;white-space:nowrap;letter-spacing:0.01em">{{ p.name }}</span>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- FEATURE 1 — Vidéo -->
    <section id="features" class="section">
      <div style="max-width:1152px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center">
        <div data-aos="fade-right">
          <p style="color:#A78BFA;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">Video Sales Letter</p>
          <h2 style="font-weight:700;color:#fff;letter-spacing:-0.03em;line-height:1.1;margin-bottom:20px;font-size:clamp(1.8rem,3.5vw,2.8rem)">
            Your face before<br/>the first click
          </h2>
          <p style="color:rgba(255,255,255,0.45);font-size:1.05rem;line-height:1.7;margin-bottom:32px">
            The video plays automatically, muted and looped. Your audience sees you and builds trust before they even tap a button — the #1 conversion tool for creators.
          </p>
          <div style="display:flex;flex-direction:column;gap:12px">
            <div v-for="item in videoFeatures" :key="item" style="display:flex;align-items:center;gap:12px;color:rgba(255,255,255,0.55);font-size:14px">
              <div style="width:20px;height:20px;border-radius:50%;background:rgba(109,78,232,0.15);border:1px solid rgba(109,78,232,0.3);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="color:#A78BFA;font-size:10px">✓</span>
              </div>
              {{ item }}
            </div>
          </div>
        </div>
        <div data-aos="fade-left" data-aos-delay="100" style="background:#0d0d0d;border-radius:20px;border:1px solid rgba(255,255,255,0.06);padding:40px;display:flex;align-items:center;justify-content:center;min-height:400px">
          <div style="width:150px;background:#000;border-radius:24px;overflow:hidden;box-shadow:0 0 60px rgba(0,0,0,0.8);border:1px solid rgba(255,255,255,0.1)">
            <div style="position:relative;background:linear-gradient(to bottom,#1e3a5f,#000);aspect-ratio:9/16">
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center">
                  <span style="color:#fff;font-size:18px;margin-left:3px">▶</span>
                </div>
              </div>
              <div style="position:absolute;top:10px;left:10px;background:#6D4EE8;border-radius:4px;padding:2px 8px;color:#fff;font-size:9px;font-weight:700">▶ VSL</div>
              <div style="position:absolute;bottom:0;left:0;right:0;padding:12px;background:linear-gradient(to top,#000,transparent)">
                <p style="color:#fff;font-size:11px;font-weight:600;margin-bottom:2px">🌸 Karine</p>
                <p style="color:rgba(255,255,255,0.3);font-size:9px;margin-bottom:8px">@karinefrenchwoman</p>
                <div style="background:#6D4EE8;border-radius:10px;padding:6px;text-align:center;color:#fff;font-size:9px;font-weight:700">→ Subscribe Now</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURE 2 — Deep Linking -->
    <section style="padding:96px 24px;border-top:1px solid rgba(255,255,255,0.06)">
      <div style="max-width:1152px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center">
        <div data-aos="fade-right" style="display:flex;flex-direction:column;gap:12px">
          <div v-for="step in deepLinkSteps" :key="step.app"
            style="display:flex;align-items:center;gap:16px;padding:16px 20px;border-radius:14px;background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06)">
            <div :style="{width:'40px',height:'40px',borderRadius:'12px',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0,background:platformLogos.find(p=>p.name===step.app)?.color||'rgba(255,255,255,0.08)',overflow:'hidden',padding:'6px'}" v-html="platformLogos.find(p=>p.name===step.app)?.svg||''"></div>
            <div style="flex:1">
              <p style="color:#fff;font-size:14px;font-weight:500;margin-bottom:2px">{{ step.app }}</p>
              <p style="color:rgba(255,255,255,0.3);font-size:12px">{{ step.desc }}</p>
            </div>
            <span style="font-size:12px;color:#34d399;font-weight:600">Opened ✓</span>
          </div>
        </div>
        <div data-aos="fade-left" data-aos-delay="100">
          <p style="color:#A78BFA;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">Deep Linking</p>
          <h2 style="font-weight:700;color:#fff;letter-spacing:-0.03em;line-height:1.1;margin-bottom:20px;font-size:clamp(1.8rem,3.5vw,2.8rem)">
            Bypass Instagram.<br/>Open the native app.
          </h2>
          <p style="color:rgba(255,255,255,0.45);font-size:1.05rem;line-height:1.7">
            Fans tap from Instagram or TikTok — MySocialVSL bypasses the in-app WebView and opens the destination app directly. Zero friction, maximum conversions.
          </p>
        </div>
      </div>
    </section>

    <!-- FEATURE 3 — Analytics -->
    <section style="padding:96px 24px;border-top:1px solid rgba(255,255,255,0.06)">
      <div style="max-width:1152px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center">
        <div data-aos="fade-right">
          <p style="color:#A78BFA;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">Analytics</p>
          <h2 style="font-weight:700;color:#fff;letter-spacing:-0.03em;line-height:1.1;margin-bottom:20px;font-size:clamp(1.8rem,3.5vw,2.8rem)">
            Every click.<br/>Real time.
          </h2>
          <p style="color:rgba(255,255,255,0.45);font-size:1.05rem;line-height:1.7">
            Page views, clicks per link, conversion rate, country, device type. Know exactly what's working and what isn't — in real time.
          </p>
        </div>
        <div data-aos="fade-left" data-aos-delay="100" style="background:#0d0d0d;border-radius:20px;border:1px solid rgba(255,255,255,0.06);padding:32px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div v-for="stat in analyticsStats" :key="stat.label" style="background:rgba(255,255,255,0.03);border-radius:14px;padding:20px;border:1px solid rgba(255,255,255,0.05)">
            <p style="color:rgba(255,255,255,0.3);font-size:11px;margin-bottom:8px">{{ stat.label }}</p>
            <p style="font-size:24px;font-weight:700;color:#fff;letter-spacing:-0.03em;margin-bottom:4px">{{ stat.value }}</p>
            <p :style="{fontSize:'11px',fontWeight:500,color:stat.up?'#34d399':'#60a5fa'}">{{ stat.trend }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PRICING -->
    <PricingSection />

    <!-- FAQ -->
    <section id="faq" style="padding:96px 24px;border-top:1px solid rgba(255,255,255,0.06)">
      <div style="max-width:680px;margin:0 auto">
        <h2 data-aos="fade-up" style="font-weight:700;color:#fff;text-align:center;margin-bottom:56px;letter-spacing:-0.03em;font-size:clamp(1.8rem,3.5vw,2.6rem)">
          Frequently Asked Questions
        </h2>
        <div style="display:flex;flex-direction:column;gap:8px">
          <div v-for="(faq, i) in faqs" :key="i" data-aos="fade-up" :data-aos-delay="i * 40"
            style="border:1px solid rgba(255,255,255,0.08);border-radius:14px;overflow:hidden">
            <button style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:20px 24px;text-align:left;background:none;cursor:pointer;border:none" @click="openFaq = openFaq === i ? null : i">
              <span style="font-weight:500;color:rgba(255,255,255,0.8);font-size:14px">{{ faq.q }}</span>
              <span style="color:rgba(255,255,255,0.3);font-size:18px;margin-left:16px;flex-shrink:0">{{ openFaq === i ? '−' : '+' }}</span>
            </button>
            <div v-if="openFaq === i" style="padding:0 24px 20px;color:rgba(255,255,255,0.4);font-size:14px;line-height:1.7;border-top:1px solid rgba(255,255,255,0.06);padding-top:16px">
              {{ faq.a }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA FINAL -->
    <section style="padding:96px 24px;border-top:1px solid rgba(255,255,255,0.06)">
      <div style="max-width:600px;margin:0 auto;text-align:center">
        <h2 data-aos="fade-up" style="font-weight:800;color:#fff;margin-bottom:20px;letter-spacing:-0.04em;font-size:clamp(2.2rem,5vw,3.5rem)">
          Ready to convert?
        </h2>
        <p data-aos="fade-up" data-aos-delay="100" style="color:rgba(255,255,255,0.35);font-size:1.1rem;margin-bottom:40px">
          Live in 30 seconds. Free to start.
        </p>
        <RouterLink data-aos="fade-up" data-aos-delay="200" to="/register"
          style="display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#6D4EE8,#8B6FF0);color:#fff;padding:16px 40px;border-radius:999px;font-weight:700;font-size:15px;text-decoration:none;box-shadow:0 0 40px rgba(109,78,232,0.4);transition:all 0.15s">
          Create my page for free →
        </RouterLink>
      </div>
    </section>

    <!-- FOOTER -->
    <footer style="position:relative;border-top:1px solid rgba(255,255,255,0.06);background:linear-gradient(to bottom,#080810,#0a0814);font-family:Inter,sans-serif">
      <div style="max-width:1152px;margin:0 auto;padding:64px 24px 0">

        <!-- Orb déco -->
        <div style="position:absolute;top:0;right:5%;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(109,78,232,0.08),transparent 70%);pointer-events:none"></div>

        <div style="display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:48px;margin-bottom:56px">

          <!-- Col 1 — Brand + newsletter -->
          <div style="position:relative">
            <!-- Logo -->
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
              <div style="width:28px;height:28px;background:linear-gradient(135deg,#6D4EE8,#8B5CF6);border-radius:8px;display:flex;align-items:center;justify-content:center">
                <span style="color:#fff;font-weight:900;font-size:13px;letter-spacing:-0.05em">V</span>
              </div>
              <span style="font-weight:700;color:#fff;font-size:15px;letter-spacing:-0.02em">MySocialVSL</span>
            </div>
            <p style="color:rgba(255,255,255,0.35);font-size:13px;line-height:1.65;margin:0 0 20px;max-width:240px">
              Le link-in-bio VSL qui convertit tes fans en abonnés payants.
            </p>
            <!-- Newsletter -->
            <div style="position:relative;max-width:280px">
              <input type="email" placeholder="Ton adresse email"
                style="width:100%;padding:10px 44px 10px 14px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:#fff;font-size:13px;font-family:Inter,sans-serif;outline:none;box-sizing:border-box;backdrop-filter:blur(8px)"
                @focus="e => e.target.style.borderColor='rgba(109,78,232,0.5)'"
                @blur="e => e.target.style.borderColor='rgba(255,255,255,0.1)'" />
              <button style="position:absolute;right:6px;top:50%;transform:translateY(-50%);width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,#5940D1,#6D4EE8);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:opacity 0.15s"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <i class="bi bi-send-fill" style="color:#fff;font-size:11px"></i>
              </button>
            </div>
            <div style="position:absolute;right:-20px;top:-10px;width:120px;height:120px;border-radius:50%;background:rgba(109,78,232,0.06);filter:blur(30px);pointer-events:none"></div>
          </div>

          <!-- Col 2 — Produit -->
          <div>
            <h3 style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.9);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 18px">Produit</h3>
            <nav style="display:flex;flex-direction:column;gap:10px">
              <a href="#features" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Fonctionnalités</a>
              <a href="#pricing" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Pricing</a>
              <a href="#faq" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">FAQ</a>
              <RouterLink to="/register" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Créer un compte</RouterLink>
              <RouterLink to="/login" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Se connecter</RouterLink>
            </nav>
          </div>

          <!-- Col 3 — Ressources -->
          <div>
            <h3 style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.9);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 18px">Ressources</h3>
            <nav style="display:flex;flex-direction:column;gap:10px">
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Blog</a>
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Documentation API</a>
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Affiliés</a>
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Status</a>
            </nav>
          </div>

          <!-- Col 4 — Réseaux sociaux -->
          <div>
            <h3 style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.9);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 18px">Nous suivre</h3>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
              <a v-for="s in socials" :key="s.label" :href="s.href" :title="s.label"
                style="width:36px;height:36px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all 0.15s;color:rgba(255,255,255,0.5);font-size:15px"
                onmouseover="this.style.borderColor='rgba(109,78,232,0.5)';this.style.background='rgba(109,78,232,0.1)';this.style.color='#A78BFA'"
                onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.04)';this.style.color='rgba(255,255,255,0.5)'">
                <i :class="s.icon"></i>
              </a>
            </div>
          </div>

        </div>

        <!-- Bottom bar -->
        <div style="border-top:1px solid rgba(255,255,255,0.06);padding:24px 0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
          <p style="font-size:12px;color:rgba(255,255,255,0.2);margin:0">© 2026 MySocialVSL. Tous droits réservés.</p>
          <nav style="display:flex;gap:24px">
            <a href="#" style="color:rgba(255,255,255,0.2);font-size:12px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='rgba(255,255,255,0.5)'" onmouseout="this.style.color='rgba(255,255,255,0.2)'">Politique de confidentialité</a>
            <a href="#" style="color:rgba(255,255,255,0.2);font-size:12px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='rgba(255,255,255,0.5)'" onmouseout="this.style.color='rgba(255,255,255,0.2)'">CGU</a>
            <a href="#" style="color:rgba(255,255,255,0.2);font-size:12px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='rgba(255,255,255,0.5)'" onmouseout="this.style.color='rgba(255,255,255,0.2)'">Cookies</a>
          </nav>
        </div>

      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AOS from 'aos'
import PhoneDemo from '@/components/PhoneDemo.vue'
import PricingSection from '@/components/PricingSection.vue'

onMounted(() => AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 80 }))

const openFaq = ref(null)

const heroStats = [
  { value: '+80%',   label: 'avg conversion rate vs Linktree' },
  { value: '94%',    label: 'of clicks open the native app (deep link)' },
  { value: '< 2min', label: 'to create and publish your page' },
]

const stats = [
  { label: 'Vues ce mois',     value: '1 860', sub: '↑ +52% vs mois dernier', up: true },
  { label: 'Conversion',       value: '41.7%', sub: '↑ +80% vs Linktree',     up: true },
  { label: 'Clics liens',      value: '775',   sub: '↑ +44% ce mois',         up: true },
  { label: 'Deep link bypass', value: '94%',   sub: 'Ouverture app native',   up: false },
]

const platformLogos = [
  { name: 'Instagram', color: '#E1306C', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="white" stroke="none"/></svg>` },
  { name: 'TikTok',    color: '#010101', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.31 6.31 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.74a4.85 4.85 0 01-1.01-.05z"/></svg>` },
  { name: 'YouTube',   color: '#FF0000', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M23 7s-.3-2-1.2-2.7c-1.1-1.2-2.4-1.2-3-1.3C16.6 3 12 3 12 3s-4.6 0-6.8.1c-.6.1-1.9.1-3 1.3C1.3 5 1 7 1 7S.7 9.2.7 11.5v2.1c0 2.2.3 4.4.3 4.4s.3 2 1.2 2.7c1.1 1.2 2.6 1.1 3.3 1.2C7.5 22 12 22 12 22s4.6 0 6.8-.2c.6-.1 1.9-.1 3-1.3.9-.7 1.2-2.7 1.2-2.7s.3-2.2.3-4.4v-2.1C23.3 9.2 23 7 23 7zM9.7 15.5V8.4l8.1 3.6-8.1 3.5z"/></svg>` },
  { name: 'Snapchat',  color: '#FFFC00', svg: `<svg viewBox="0 0 24 24" width="28" height="28" fill="#000"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.44 4.625.03 1.023.308 1.499 1.447 1.754.35.078.572.41.572.79 0 .27-.117.52-.343.693-.39.295-1.053.385-1.8.305-.105-.01-.21-.02-.326-.021-.03.004-.074.01-.121.012.048.096.1.2.16.314.29.546.738 1.17 1.384 1.63.23.163.474.294.738.419.19.09.248.293.166.491-.077.187-.314.363-.733.516-.193.07-.383.126-.573.182a2.75 2.75 0 0 0-.563.217c-.123.075-.17.18-.16.31.005.074.024.158.05.25.1.338.335.814.64 1.276.08.121.143.256.143.392 0 .297-.18.566-.529.714-.42.177-1.076.247-2.022.049a6.018 6.018 0 0 1-1.336-.502c-.427-.233-.766-.397-1.08-.397-.313 0-.653.164-1.08.397a6.015 6.015 0 0 1-1.336.502c-.946.198-1.602.128-2.023-.049-.349-.148-.528-.417-.528-.714 0-.136.063-.27.143-.392.305-.462.539-.938.64-1.275.025-.093.044-.177.05-.251.01-.13-.038-.235-.161-.31a2.76 2.76 0 0 0-.563-.217 12.5 12.5 0 0 1-.573-.182c-.42-.153-.656-.33-.733-.516-.083-.198-.025-.4.166-.491.264-.125.508-.256.738-.419.646-.46 1.094-1.084 1.384-1.63.06-.114.112-.218.16-.314a1.96 1.96 0 0 1-.12-.012c-.116.001-.22.011-.327.021-.747.08-1.41-.01-1.8-.305a.932.932 0 0 1-.342-.693c0-.38.222-.712.572-.79 1.14-.255 1.416-.731 1.447-1.754.037-1.406-.089-3.432.44-4.625C7.858 1.07 11.215.793 12.205.793z"/></svg>` },
  { name: 'X / Twitter', color: '#000000', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>` },
  { name: 'Telegram',  color: '#26A5E4', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>` },
  { name: 'Twitch',    color: '#9146FF', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>` },
  { name: 'Discord',   color: '#5865F2', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>` },
  { name: 'Threads',   color: '#000000', svg: `<svg viewBox="0 0 192 192" width="28" height="28" fill="white"><path d="M141.537 88.988a66.667 66.667 0 0 0-2.518-1.143c-1.482-27.307-16.403-42.94-41.457-43.1h-.34c-14.986 0-27.449 6.396-35.12 18.036l13.779 9.452c5.73-8.695 14.724-10.548 21.348-10.548h.229c8.249.053 14.474 2.452 18.503 7.129 2.932 3.405 4.893 8.111 5.864 14.05-7.314-1.243-15.224-1.626-23.68-1.14-23.82 1.371-39.134 15.264-38.105 34.568.522 9.792 5.4 18.216 13.735 23.719 7.047 4.652 16.124 6.927 25.557 6.412 12.458-.683 22.231-5.436 29.049-14.127 5.178-6.6 8.453-15.153 9.899-25.93 5.937 3.583 10.337 8.298 12.767 13.966 4.132 9.635 4.373 25.468-8.546 38.376-11.319 11.308-24.925 16.2-45.488 16.351-22.809-.169-40.06-7.484-51.275-21.742C35.236 139.966 29.808 120.682 29.605 96c.203-24.682 5.63-43.966 16.133-57.317C56.954 24.425 74.204 17.11 97.013 16.94c22.975.17 40.526 7.52 52.171 21.847 5.71 7.026 10.015 15.86 12.853 26.162l16.147-4.308c-3.44-12.68-8.853-23.606-16.219-32.668C147.036 9.607 125.202.195 97.24 0h-.483C68.786.195 47.219 9.643 32.8 28.08 19.894 44.487 13.2 67.315 13 96c.2 28.685 6.894 51.512 19.8 67.92 14.419 18.438 35.986 27.886 64.257 28.08h.483c25.023-.173 42.61-6.713 57.114-21.205 18.508-18.478 17.926-41.63 11.836-55.86-4.308-10.041-12.542-18.157-24.953-23.947ZM98.44 129.507c-10.44.588-21.286-4.098-21.82-14.135-.397-7.442 5.296-15.746 22.461-16.735 1.966-.113 3.895-.169 5.79-.169 6.235 0 12.068.606 17.371 1.765-1.978 24.702-13.58 28.713-23.802 29.274Z"/></svg>` },
  { name: 'Facebook',  color: '#1877F2', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>` },
  { name: 'Spotify',   color: '#1DB954', svg: `<svg viewBox="0 0 24 24" width="40" height="40" fill="white"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>` },
]

const videoFeatures = [
  'Autoplay muted on page load',
  'Multiple videos per page',
  '10+ fully customizable themes',
  'Deep linking — opens native app directly',
]

const deepLinkSteps = [
  { app: 'Instagram', desc: "Bypasses in-app browser → Safari/Chrome" },
  { app: 'TikTok',   desc: "Bypasses in-app browser → Safari/Chrome" },
  { app: 'Twitch',   desc: "Opens native app directly" },
  { app: 'Telegram', desc: "Opens native app directly" },
]

const analyticsStats = [
  { label: 'Total views',    value: '12.4k', trend: '↑ +28% this month', up: true },
  { label: 'Avg CTR',        value: '38.2%', trend: '↑ +12 pts',         up: true },
  { label: 'Link clicks',    value: '4 730', trend: '↑ +41%',            up: true },
  { label: 'Deep link rate', value: '94%',   trend: 'Native app',        up: false },
]


const faqs = [
  { q: "How does autoplay video work?",
    a: "The video starts automatically, muted, as soon as a visitor lands on your page. They can tap to unmute. It works on all modern mobile browsers." },
  { q: "What is deep linking?",
    a: "MySocialVSL detects when a fan is visiting from inside Instagram, TikTok, or any in-app browser, and forces the link to open in their native browser (Safari/Chrome). This bypasses restrictions that break payments and signups." },
  { q: "What's the difference with Linktree?",
    a: "Linktree is a generic static page. MySocialVSL adds autoplay video (VSL), deep linking, and granular analytics. Average conversion rate is +80% higher." },
  { q: "Can I use my own video?",
    a: "Yes. Paste any video URL (Streamable, YouTube, Cloudflare Stream) or upload an MP4 directly from your dashboard." },
  { q: "Does it work on mobile?",
    a: "70%+ of traffic comes from mobile. Pages are 100% responsive and muted autoplay works on both Chrome and Safari mobile." },
]

const socials = [
  { label: 'Instagram', href: '#', icon: 'bi bi-instagram' },
  { label: 'TikTok',    href: '#', icon: 'bi bi-tiktok' },
  { label: 'Twitter/X', href: '#', icon: 'bi bi-twitter-x' },
  { label: 'Telegram',  href: '#', icon: 'bi bi-telegram' },
]
</script>

<style scoped>
.scroll-track {
  overflow: hidden;
  white-space: nowrap;
}
.scroll-inner {
  display: inline-flex;
  align-items: flex-start;
  animation: scroll-logos 40s linear infinite;
}
.scroll-inner:hover {
  animation-play-state: paused;
}
@keyframes scroll-logos {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.5; transform: scale(1.4); }
}
</style>
