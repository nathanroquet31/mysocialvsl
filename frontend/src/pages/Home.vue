<template>
  <div class="w-full text-white font-sans antialiased page-root">

    <!-- NAV -->
    <header style="position:fixed;top:0;left:0;right:0;z-index:50;border-bottom:1px solid rgba(255,255,255,0.07);background:rgba(0,0,0,0.85);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)">
      <div style="max-width:1200px;margin:0 auto;padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between">

        <!-- Logo -->
        <RouterLink to="/" style="display:flex;align-items:center;gap:10px;text-decoration:none">
          <LogoMark :size="30" />
          <span style="font-weight:700;color:#fff;font-size:15px;letter-spacing:-0.02em">MySocialVSL</span>
        </RouterLink>

        <!-- Nav desktop -->
        <nav class="nav-desktop" style="display:flex;align-items:center;gap:32px">
          <a href="#features" class="nav-link">Features</a>
          <a href="#pricing" class="nav-link">Pricing</a>
          <a href="#faq" class="nav-link">FAQ</a>
        </nav>

        <!-- CTA desktop -->
        <div class="nav-desktop" style="display:flex;align-items:center;gap:12px">
          <RouterLink to="/login" class="nav-link">Login</RouterLink>
          <RouterLink to="/register" class="nav-cta">
            Get Started Free <i class="bi bi-arrow-right"></i>
          </RouterLink>
        </div>

        <!-- Hamburger mobile -->
        <button class="nav-mobile nav-burger" @click="mobileMenuOpen = true" aria-label="Menu">
          <i class="bi bi-list" style="font-size:22px;color:#fff"></i>
        </button>
      </div>

      <!-- Drawer mobile -->
      <Teleport to="body">
        <Transition name="nav-drawer">
          <div v-if="mobileMenuOpen" style="position:fixed;inset:0;z-index:100;display:flex;justify-content:flex-end" @click.self="mobileMenuOpen = false">
            <div style="width:280px;height:100%;background:#0a0a0a;border-left:1px solid rgba(255,255,255,0.08);padding:24px;display:flex;flex-direction:column;gap:8px;animation:drawer-in 0.25s cubic-bezier(0.16,1,0.3,1)">

              <!-- Header drawer -->
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
                <div style="display:flex;align-items:center;gap:8px">
                  <LogoMark :size="26" />
                  <span style="font-weight:700;color:#fff;font-size:14px">MySocialVSL</span>
                </div>
                <button @click="mobileMenuOpen = false" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.4);padding:4px">
                  <i class="bi bi-x-lg" style="font-size:18px"></i>
                </button>
              </div>

              <!-- Links -->
              <a href="#features" @click="mobileMenuOpen = false" class="drawer-link">
                <i class="bi bi-lightning-charge" style="font-size:15px;opacity:0.5"></i> Features
              </a>
              <a href="#pricing" @click="mobileMenuOpen = false" class="drawer-link">
                <i class="bi bi-tag" style="font-size:15px;opacity:0.5"></i> Pricing
              </a>
              <a href="#faq" @click="mobileMenuOpen = false" class="drawer-link">
                <i class="bi bi-question-circle" style="font-size:15px;opacity:0.5"></i> FAQ
              </a>

              <!-- Divider -->
              <div style="height:1px;background:rgba(255,255,255,0.07);margin:12px 0"></div>

              <!-- Auth -->
              <RouterLink to="/login" @click="mobileMenuOpen = false" class="drawer-link">
                <i class="bi bi-box-arrow-in-right" style="font-size:15px;opacity:0.5"></i> Login
              </RouterLink>
              <RouterLink to="/register" @click="mobileMenuOpen = false"
                style="margin-top:8px;display:block;text-align:center;background:linear-gradient(135deg,#6D4EE8,#8B6FF0);color:#fff;padding:13px;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;box-shadow:0 0 20px rgba(109,78,232,0.35)">
                Get Started Free <i class="bi bi-arrow-right"></i>
              </RouterLink>
            </div>
          </div>
        </Transition>
      </Teleport>
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
              Create my page for free <i class="bi bi-arrow-right"></i>
            </RouterLink>
            <a href="#features" style="color:rgba(255,255,255,0.35);font-size:14px;text-decoration:none;display:flex;align-items:center;gap:5px">
              See how it works <i class="bi bi-arrow-down"></i>
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
              name="Karine"
              handle="@karinefrenchwoman"
              video-id="0ed1q5"
              cta="Private access — Join"
            />
          </div>
        </div>

      </div>
    </section>

    <!-- HOW IT WORKS — Orbital Timeline -->
    <div style="position:relative">
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 80% 50%,rgba(109,78,232,0.06),transparent 70%);pointer-events:none"></div>
      <OrbitalTimeline />
    </div>

    <!-- FEATURE 1 — Video -->
    <section id="features" style="padding:96px 24px;border-top:1px solid rgba(255,255,255,0.06);position:relative;overflow:hidden">
      <div style="position:absolute;top:0;right:0;width:50%;height:100%;background:radial-gradient(ellipse 80% 60% at 90% 40%,rgba(109,78,232,0.06),transparent 70%);pointer-events:none"></div>
      <div style="max-width:1152px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center">

        <!-- Left — texte -->
        <div data-aos="fade-right">
          <p style="color:#A78BFA;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">Video Sales Letter</p>
          <h2 style="font-weight:700;color:#fff;letter-spacing:-0.03em;line-height:1.1;margin-bottom:20px;font-size:clamp(1.8rem,3.5vw,2.8rem)">
            Your face before<br/>the first click
          </h2>
          <p style="color:rgba(255,255,255,0.45);font-size:1.05rem;line-height:1.7;margin-bottom:36px">
            The video plays automatically, muted and looped. Your audience sees you and builds trust before they even tap a button — the #1 conversion tool for creators.
          </p>
          <div style="display:flex;flex-direction:column;gap:14px">
            <div v-for="item in videoFeatures" :key="item.label" style="display:flex;align-items:flex-start;gap:14px">
              <div style="width:22px;height:22px;border-radius:8px;background:rgba(109,78,232,0.15);border:1px solid rgba(109,78,232,0.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
                <i :class="'bi ' + item.icon" style="color:#A78BFA;font-size:10px"></i>
              </div>
              <div>
                <p style="color:rgba(255,255,255,0.8);font-weight:500;font-size:14px;margin:0 0 2px">{{ item.label }}</p>
                <p v-if="item.sub" style="color:rgba(255,255,255,0.3);font-size:12px;margin:0">{{ item.sub }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right — Realistic mockup of the actual product -->
        <div data-aos="fade-left" data-aos-delay="100"
          style="position:relative;display:flex;align-items:center;justify-content:center;padding:40px 0">

          <!-- Ambient glow behind the phone -->
          <div style="position:absolute;width:260px;height:260px;background:radial-gradient(circle,rgba(109,78,232,0.15),transparent 70%);filter:blur(40px);pointer-events:none;top:50%;left:50%;transform:translate(-50%,-50%)"></div>

          <!-- Phone -->
          <div style="position:relative;width:210px;flex-shrink:0">

            <!-- Cadre phone -->
            <div style="background:#0a0a0a;border-radius:40px;padding:3px;box-shadow:0 0 0 1px rgba(255,255,255,0.12),0 40px 80px rgba(0,0,0,0.85),0 16px 40px rgba(0,0,0,0.6);overflow:hidden">

              <!-- Screen -->
              <div style="border-radius:37px;overflow:hidden;background:#000;position:relative;aspect-ratio:9/16">

                <!-- Dynamic Island -->
                <div style="position:absolute;top:10px;left:50%;transform:translateX(-50%);width:90px;height:26px;background:#000;border-radius:16px;z-index:20;box-shadow:0 0 0 1px rgba(255,255,255,0.07)"></div>

                <!-- Status bar -->
                <div style="position:absolute;top:12px;left:0;right:0;display:flex;justify-content:space-between;align-items:center;padding:0 20px;z-index:15;pointer-events:none">
                  <span style="font-size:9px;font-weight:700;color:#fff;letter-spacing:-0.3px">9:41</span>
                  <div style="display:flex;align-items:center;gap:3px">
                    <div style="display:flex;align-items:flex-end;gap:1px">
                      <div style="width:2px;height:3px;background:rgba(255,255,255,0.45);border-radius:0.5px"></div>
                      <div style="width:2px;height:5px;background:rgba(255,255,255,0.65);border-radius:0.5px"></div>
                      <div style="width:2px;height:7px;background:#fff;border-radius:0.5px"></div>
                    </div>
                    <div style="position:relative;width:17px;height:8px;border:1.5px solid rgba(255,255,255,0.65);border-radius:2px;margin-left:3px">
                      <div style="position:absolute;left:1px;top:1px;bottom:1px;width:65%;background:#fff;border-radius:1px"></div>
                      <div style="position:absolute;right:-3px;top:50%;transform:translateY(-50%);width:2px;height:5px;background:rgba(255,255,255,0.4);border-radius:0 1px 1px 0"></div>
                    </div>
                  </div>
                </div>

                <!-- Real looping video -->
                <iframe
                  src="https://streamable.com/e/0ed1q5?autoplay=1&muted=1&loop=1&nocontrols=1"
                  allow="autoplay; fullscreen"
                  allowfullscreen
                  style="position:absolute;inset:0;width:100%;height:100%;border:none;pointer-events:none"
                ></iframe>

                <!-- Badge VSL -->
                <div style="position:absolute;top:48px;left:12px;background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);border-radius:999px;padding:3px 9px;display:flex;align-items:center;gap:5px;border:1px solid rgba(255,255,255,0.12);z-index:10">
                  <span style="width:5px;height:5px;border-radius:50%;background:#ff3b3b;animation:pulse-dot 1.5s ease-in-out infinite;display:inline-block;flex-shrink:0"></span>
                  <span style="font-size:7.5px;font-weight:800;color:#fff;letter-spacing:0.1em">VSL</span>
                </div>

                <!-- Muted -->
                <div style="position:absolute;top:48px;right:12px;background:rgba(0,0,0,0.55);backdrop-filter:blur(6px);border-radius:6px;padding:3px 7px;border:1px solid rgba(255,255,255,0.08);z-index:10">
                  <i class="bi bi-volume-mute-fill" style="color:rgba(255,255,255,0.6);font-size:9px"></i>
                </div>

                <!-- Bottom overlay — exact copy of the real UI -->
                <div style="position:absolute;bottom:0;left:0;right:0;padding:56px 14px 0;background:linear-gradient(to top,rgba(0,0,0,0.95) 0%,rgba(0,0,0,0.6) 55%,transparent 100%)">

                  <!-- Creator profile -->
                  <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                    <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#f09433,#e6683c,#dc2743);display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,0.4)">🌸</div>
                    <div>
                      <p style="color:#fff;font-size:10px;font-weight:700;margin:0;line-height:1.1">Karine</p>
                      <p style="color:rgba(255,255,255,0.45);font-size:7.5px;margin:0">@karinefrenchwoman</p>
                    </div>
                  </div>

                  <!-- CTA button — identical to the real product -->
                  <div style="background:#00aff0;border-radius:12px;padding:10px 12px;text-align:center;box-shadow:0 4px 20px rgba(0,175,240,0.5);margin-bottom:10px;animation:cta-pulse 3s ease-in-out infinite">
                    <span style="color:#fff;font-size:9.5px;font-weight:800;letter-spacing:0.02em">🔐 My OnlyFans — Private access</span>
                  </div>

                  <!-- Home indicator -->
                  <div style="width:60px;height:3px;background:rgba(255,255,255,0.2);border-radius:999px;margin:0 auto 10px"></div>
                </div>

              </div>
            </div>

            <!-- Side buttons -->
            <div style="position:absolute;right:-4px;top:130px;width:3px;height:72px;background:linear-gradient(180deg,#333,#222,#333);border-radius:0 3px 3px 0;box-shadow:2px 0 4px rgba(0,0,0,0.5)"></div>
            <div style="position:absolute;left:-4px;top:100px;width:3px;height:26px;background:linear-gradient(180deg,#333,#222,#333);border-radius:3px 0 0 3px;box-shadow:-2px 0 4px rgba(0,0,0,0.4)"></div>
            <div style="position:absolute;left:-4px;top:142px;width:3px;height:46px;background:linear-gradient(180deg,#333,#222,#333);border-radius:3px 0 0 3px;box-shadow:-2px 0 4px rgba(0,0,0,0.4)"></div>
            <div style="position:absolute;left:-4px;top:200px;width:3px;height:46px;background:linear-gradient(180deg,#333,#222,#333);border-radius:3px 0 0 3px;box-shadow:-2px 0 4px rgba(0,0,0,0.4)"></div>
          </div>

          <!-- Carte flottante — conversion -->
          <div style="position:absolute;bottom:10px;right:0;background:#0d0d0d;border:1px solid rgba(52,211,153,0.25);border-radius:14px;padding:12px 16px;box-shadow:0 8px 32px rgba(0,0,0,0.7)">
            <p style="color:rgba(255,255,255,0.28);font-size:9px;margin:0 0 4px;text-transform:uppercase;letter-spacing:0.1em">Taux de conversion</p>
            <p style="color:#34d399;font-size:22px;font-weight:700;letter-spacing:-0.03em;margin:0 0 2px">41.7%</p>
            <p style="color:rgba(52,211,153,0.55);font-size:10px;margin:0">↑ +80% vs Linktree</p>
          </div>

        </div>
      </div>
    </section>

    <!-- FEATURE 2 — Deep Linking -->
    <section
      style="padding:96px 24px;border-top:1px solid rgba(255,255,255,0.06);position:relative;overflow:hidden;min-height:680px;display:flex;align-items:center"
      @mousemove="onDeepLinkMouseMove"
    >
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 70% at 50% 50%,rgba(109,78,232,0.07),transparent);pointer-events:none"></div>

      <!-- Floating icons — outer div gets repulsion transform via RAF, inner div floats via CSS -->
      <div
        v-for="(icon, i) in deepLinkFloatingIcons"
        :key="icon.name"
        :ref="el => { if (el) deepLinkIconRefs[i] = el }"
        :style="[{ position: 'absolute', willChange: 'transform', zIndex: 1 }, icon.pos]"
      >
        <div
          :style="{
            width: '78px', height: '78px', borderRadius: '22px',
            background: icon.color,
            border: '1px solid rgba(255,255,255,0.15)',
            backdropFilter: 'blur(12px)', WebkitBackdropFilter: 'blur(12px)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            padding: '16px', boxSizing: 'border-box',
            boxShadow: '0 8px 40px ' + icon.glow,
            animationName: 'dl-float',
            animationDuration: icon.duration + 's',
            animationDelay: icon.delay + 's',
            animationTimingFunction: 'ease-in-out',
            animationIterationCount: 'infinite',
            animationDirection: 'alternate',
          }"
        ><i :class="'bi ' + icon.biClass" :style="{ fontSize: '2.75rem', color: icon.iconColor, lineHeight: 1 }"></i></div>
      </div>

      <!-- Center content -->
      <div style="max-width:640px;margin:0 auto;width:100%;text-align:center;position:relative;z-index:2">
        <p data-aos="fade-up" style="color:#A78BFA;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">Deep Linking</p>
        <h2 data-aos="fade-up" data-aos-delay="60" style="font-weight:700;color:#fff;letter-spacing:-0.03em;line-height:1.1;margin-bottom:20px;font-size:clamp(1.8rem,3.5vw,2.8rem)">
          Bypass Instagram.<br/>Open the native app.
        </h2>
        <p data-aos="fade-up" data-aos-delay="120" style="color:rgba(255,255,255,0.45);font-size:1.05rem;line-height:1.7;margin-bottom:36px">
          Fans tap from Instagram or TikTok — MySocialVSL bypasses the in-app WebView and opens the destination app directly. Zero friction, maximum conversions.
        </p>
        <div data-aos="fade-up" data-aos-delay="180" style="display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap">
          <span v-for="platform in deepLinkFloatingIcons.map(i => i.name)" :key="platform"
            style="display:inline-flex;align-items:center;gap:7px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);font-size:13px;color:rgba(255,255,255,0.5)">
            <span style="width:6px;height:6px;border-radius:50%;background:#34d399;flex-shrink:0;display:inline-block"></span>
            {{ platform }}
          </span>
        </div>
      </div>
    </section>

    <!-- FEATURE 3 — Shield Protection™ -->
    <section style="padding:96px 24px;border-top:1px solid rgba(255,255,255,0.06);position:relative;overflow:hidden">
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 50% at 50% 0%,rgba(16,185,129,0.04),transparent 70%);pointer-events:none"></div>
      <div style="max-width:1152px;margin:0 auto">
        <div data-aos="fade-up" style="text-align:center;margin-bottom:56px">
          <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:999px;padding:6px 16px;margin-bottom:20px">
            <span style="width:7px;height:7px;border-radius:50%;background:#10b981;display:inline-block;animation:pulse-dot 2s ease-in-out infinite"></span>
            <span style="font-size:12px;color:#34d399;font-weight:700;letter-spacing:0.08em;text-transform:uppercase">Shield Protection™</span>
          </div>
          <h2 style="font-weight:700;color:#fff;letter-spacing:-0.03em;line-height:1.1;margin-bottom:16px;font-size:clamp(1.8rem,3.5vw,2.8rem)">
            Invisible to bots.<br/>Only real fans get through.
          </h2>
          <p style="color:rgba(255,255,255,0.4);font-size:1.05rem;max-width:560px;margin:0 auto;line-height:1.7">
            Our dual-layer protection detects 30+ crawlers and Instagram's scanner — and shows them a harmless decoy page instead of your real link.
          </p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
          <!-- Card 1: Bot Detection -->
          <div data-glow data-aos="fade-up" data-aos-delay="0"
            style="--base:155;--spread:120;--radius:14;--border:1;--size:260;border:1px solid rgba(255,255,255,0.07);border-radius:20px;padding:32px;position:relative">
            <div data-glow></div>
            <div style="position:absolute;top:0;right:0;width:120px;height:120px;background:radial-gradient(circle,rgba(16,185,129,0.08),transparent 70%);pointer-events:none"></div>
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);display:flex;align-items:center;justify-content:center;margin-bottom:20px">
              <i class="bi bi-robot" style="font-size:20px"></i>
            </div>
            <h3 style="font-weight:700;color:#fff;font-size:16px;margin-bottom:10px">Bot & Crawler Detection</h3>
            <p style="color:rgba(255,255,255,0.35);font-size:13px;line-height:1.65;margin-bottom:20px">
              Detects and blocks 30+ known crawlers — Googlebot, AhrefsBot, SemrushBot, Meta, and more — before they can index your destination URLs.
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:6px">
              <span v-for="bot in ['Googlebot','Meta','AhrefsBot','TikTokBot','Twitterbot','Slackbot']" :key="bot"
                style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:6px;padding:3px 9px;font-size:11px;color:rgba(255,255,255,0.35)">
                {{ bot }}
              </span>
              <span style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:6px;padding:3px 9px;font-size:11px;color:rgba(255,255,255,0.35)">+24 more</span>
            </div>
          </div>

          <!-- Card 2: Decoy Page -->
          <div data-glow data-aos="fade-up" data-aos-delay="80"
            style="--base:155;--spread:120;--radius:14;--border:1;--size:290;border:1px solid rgba(16,185,129,0.2);border-radius:20px;padding:32px;position:relative;box-shadow:0 0 40px rgba(16,185,129,0.06)">
            <div data-glow></div>
            <div style="position:absolute;top:0;right:0;width:140px;height:140px;background:radial-gradient(circle,rgba(16,185,129,0.1),transparent 70%);pointer-events:none"></div>
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);display:flex;align-items:center;justify-content:center;margin-bottom:20px">
              <i class="bi bi-mask" style="font-size:20px"></i>
            </div>
            <div style="position:absolute;top:16px;right:16px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:999px;padding:3px 10px">
              <span style="font-size:10px;color:#34d399;font-weight:700">KEY FEATURE</span>
            </div>
            <h3 style="font-weight:700;color:#fff;font-size:16px;margin-bottom:10px">Instagram Decoy Page</h3>
            <p style="color:rgba(255,255,255,0.35);font-size:13px;line-height:1.65;margin-bottom:20px">
              When Instagram scans your link, it sees a harmless "My Links" page — no adult keywords, no real URL. Your actual page is only shown to real visitors.
            </p>
            <!-- Mini decoy preview — concrete, side by side -->
            <div style="background:#111;border-radius:12px;border:1px solid rgba(255,255,255,0.06);padding:14px;font-size:11px">
              <p style="color:rgba(255,255,255,0.35);margin-bottom:7px"><i class="bi bi-robot" style="margin-right:5px"></i>Instagram's bot sees:</p>
              <div style="background:#1a1a1a;border-radius:8px;padding:16px 12px;text-align:center">
                <p style="color:rgba(255,255,255,0.55);font-weight:700;font-size:12px;margin:0 0 3px">My Links</p>
                <p style="color:rgba(255,255,255,0.25);font-size:10px;margin:0">Check out my latest content</p>
              </div>
              <p style="color:rgba(255,255,255,0.35);margin-top:11px;margin-bottom:7px"><i class="bi bi-heart-fill" style="margin-right:5px;color:#E1306C"></i>A real fan sees:</p>
              <div style="background:linear-gradient(135deg,#1e1040,#0d0d0d);border-radius:8px;border:1px solid rgba(109,78,232,0.25);overflow:hidden">
                <div style="height:56px;background:linear-gradient(160deg,#2a1a4d,#140a26);display:flex;align-items:center;justify-content:center;position:relative">
                  <span style="position:absolute;top:6px;left:7px;background:rgba(109,78,232,0.9);border-radius:4px;padding:1px 5px;font-size:7px;font-weight:800;color:#fff;letter-spacing:0.05em">VSL</span>
                  <div style="width:24px;height:24px;border-radius:50%;background:rgba(255,255,255,0.14);display:flex;align-items:center;justify-content:center"><i class="bi bi-play-fill" style="color:#fff;font-size:13px"></i></div>
                </div>
                <div style="padding:8px">
                  <div style="height:20px;background:#00AFF0;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff"><i class="bi bi-lock-fill" style="margin-right:4px;font-size:8px"></i>OnlyFans — Private access</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Deep Link Bypass -->
          <div data-glow data-aos="fade-up" data-aos-delay="160"
            style="--base:265;--spread:120;--radius:14;--border:1;--size:260;border:1px solid rgba(255,255,255,0.07);border-radius:20px;padding:32px;position:relative">
            <div data-glow></div>
            <div style="position:absolute;top:0;right:0;width:120px;height:120px;background:radial-gradient(circle,rgba(109,78,232,0.08),transparent 70%);pointer-events:none"></div>
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(109,78,232,0.1);border:1px solid rgba(109,78,232,0.2);display:flex;align-items:center;justify-content:center;margin-bottom:20px">
              <i class="bi bi-link-45deg" style="font-size:20px"></i>
            </div>
            <h3 style="font-weight:700;color:#fff;font-size:16px;margin-bottom:10px">WebView Bypass</h3>
            <p style="color:rgba(255,255,255,0.35);font-size:13px;line-height:1.65;margin-bottom:20px">
              When a fan taps your link inside Instagram or TikTok, we bypass the in-app browser and open the destination app natively — zero friction, no login wall.
            </p>
            <div style="display:flex;flex-direction:column;gap:8px">
              <div v-for="item in [
                { icon:'phone', label:'Instagram WebView', status:'Bypassed', ok:true },
                { icon:'music-note-beamed', label:'TikTok WebView', status:'Bypassed', ok:true },
                { icon:'box-arrow-up-right', label:'App opens natively', status:'Always', ok:true },
              ]" :key="item.label"
                style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:rgba(255,255,255,0.025);border-radius:8px;border:1px solid rgba(255,255,255,0.05)">
                <span style="font-size:12px;color:rgba(255,255,255,0.55)"><i :class="`bi bi-${item.icon}`" style="margin-right:4px"></i>{{ item.label }}</span>
                <span style="font-size:11px;font-weight:700;color:#34d399">{{ item.status }} <i class="bi bi-check" style="font-size:10px"></i></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURE 4 — Analytics -->
    <section style="padding:96px 24px 0;border-top:1px solid rgba(255,255,255,0.06);position:relative;overflow:hidden">
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 20% 50%,rgba(109,78,232,0.06),transparent 70%);pointer-events:none"></div>
      <!-- Fondu vers le Pricing -->
      <div style="position:absolute;bottom:0;left:0;right:0;height:220px;background:linear-gradient(to bottom,transparent,#080810);pointer-events:none;z-index:2"></div>
      <div style="max-width:1152px;margin:0 auto;padding-bottom:120px;position:relative;z-index:1">

        <!-- Header -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;margin-bottom:48px">
          <div data-aos="fade-right">
            <p style="color:#A78BFA;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:16px">VSL Analytics</p>
            <h2 style="font-weight:700;color:#fff;letter-spacing:-0.03em;line-height:1.1;margin-bottom:20px;font-size:clamp(1.8rem,3.5vw,2.8rem)">
              Know exactly when<br/>they clicked.
            </h2>
            <p style="color:rgba(255,255,255,0.45);font-size:1.05rem;line-height:1.7">
              Page views, VSL play rate, seconds watched before clicking, milestone funnel (25/50/75/100%), link clicks, countries, devices. Real time.
            </p>
          </div>
          <!-- Stat cards -->
          <div data-aos="fade-left" data-aos-delay="80" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div v-for="stat in analyticsStats" :key="stat.label"
              style="background:#0d0d0d;border-radius:16px;padding:22px;border:1px solid rgba(255,255,255,0.06);position:relative;overflow:hidden">
              <div style="position:absolute;top:0;right:0;width:70px;height:70px;background:radial-gradient(circle,rgba(109,78,232,0.07),transparent 70%);pointer-events:none"></div>
              <p style="color:rgba(255,255,255,0.28);font-size:10px;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.08em">{{ stat.label }}</p>
              <p style="font-size:26px;font-weight:700;color:#fff;letter-spacing:-0.03em;margin-bottom:6px">{{ stat.value }}</p>
              <div style="display:flex;align-items:center;gap:5px">
                <span :style="{display:'inline-block',width:'5px',height:'5px',borderRadius:'50%',background:stat.up?'#34d399':'#60a5fa',flexShrink:0}"></span>
                <p :style="{fontSize:'11px',fontWeight:500,color:stat.up?'#34d399':'#60a5fa',margin:0}">{{ stat.trend }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- VSL Watch Funnel -->
        <div data-aos="fade-up" data-aos-delay="120"
          style="background:#0d0d0d;border-radius:20px;border:1px solid rgba(255,255,255,0.06);padding:36px">

          <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px">
            <div>
              <p style="color:#fff;font-size:15px;font-weight:600;margin-bottom:5px">VSL Watch Funnel</p>
              <p style="color:rgba(255,255,255,0.3);font-size:12px;line-height:1.5">How far your audience watches before clicking — last 30 days</p>
            </div>
            <div style="display:flex;align-items:center;gap:6px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:999px;padding:5px 14px;flex-shrink:0">
              <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;animation:pulse-dot 2s ease-in-out infinite"></span>
              <span style="font-size:11px;color:#34d399;font-weight:700;letter-spacing:0.05em">LIVE</span>
            </div>
          </div>

          <div style="display:flex;flex-direction:column;gap:10px">
            <div v-for="(step, i) in vslFunnel" :key="step.label" style="display:flex;align-items:center;gap:16px">
              <!-- Step label -->
              <div style="width:130px;flex-shrink:0">
                <p style="font-size:12px;color:rgba(255,255,255,0.4);font-weight:500;margin:0">{{ step.label }}</p>
              </div>
              <!-- Progress bar -->
              <div style="flex:1;background:rgba(255,255,255,0.04);border-radius:999px;height:32px;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.04)">
                <div :style="{
                  position:'absolute',left:0,top:0,bottom:0,
                  width: step.pct + '%',
                  background: i===0 ? 'linear-gradient(90deg,#5B2FD4,#8B6FF0)' :
                              i===vslFunnel.length-1 ? 'linear-gradient(90deg,#059669,#34d399)' :
                              'linear-gradient(90deg,rgba(109,78,232,0.55),rgba(139,111,240,0.75))',
                  borderRadius:'999px',
                  transition:'width 1s cubic-bezier(0.4,0,0.2,1)'
                }">
                  <div style="position:absolute;inset:0;background:linear-gradient(to right,transparent,rgba(255,255,255,0.06));border-radius:999px"></div>
                </div>
                <!-- Count label inside bar if wide enough -->
                <div style="position:absolute;inset:0;display:flex;align-items:center;padding:0 12px">
                  <span v-if="step.pct > 20" style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.9)">{{ step.value }}</span>
                </div>
              </div>
              <!-- Percentage badge -->
              <div style="width:52px;flex-shrink:0;text-align:right">
                <span :style="{
                  fontSize:'12px',fontWeight:700,
                  color: i===vslFunnel.length-1 ? '#34d399' : i===0 ? '#A78BFA' : 'rgba(255,255,255,0.5)'
                }">{{ step.pct }}%</span>
              </div>
            </div>
          </div>

          <!-- Footer note -->
          <div style="margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
            <p style="font-size:12px;color:rgba(255,255,255,0.25);margin:0">
              <i class="bi bi-info-circle" style="margin-right:5px"></i>
              The video keeps them watching — 38% end up tapping through to OnlyFans (avg. 18s before the click)
            </p>
            <div style="display:flex;align-items:center;gap:16px">
              <div v-for="leg in [{color:'#8B6FF0',label:'Watching'},{color:'#34d399',label:'Clicked OnlyFans'}]" :key="leg.label"
                style="display:flex;align-items:center;gap:5px">
                <span :style="{width:'8px',height:'8px',borderRadius:'2px',background:leg.color,display:'inline-block'}"></span>
                <span style="font-size:11px;color:rgba(255,255,255,0.35)">{{ leg.label }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- PRICING -->
    <PricingSection />

    <!-- FAQ -->
    <section id="faq" style="padding:96px 24px;border-top:none;position:relative;overflow:hidden;background:linear-gradient(to bottom,#0a0814 0%,#050508 30%,#000 60%)">
      <!-- Fondu depuis le haut — absorbe la fin du Pricing -->
      <div style="position:absolute;top:0;left:0;right:0;height:160px;background:linear-gradient(to bottom,#0a0814,transparent);pointer-events:none;z-index:0"></div>
      <!-- Orb ambiant -->
      <div style="position:absolute;top:20%;left:50%;transform:translateX(-50%);width:70vw;height:400px;background:radial-gradient(ellipse at center,rgba(109,78,232,0.04),transparent 70%);pointer-events:none;z-index:0"></div>
      <div style="max-width:760px;margin:0 auto;position:relative;z-index:1">

        <!-- Header -->
        <div data-aos="fade-up" style="text-align:center;margin-bottom:64px">
          <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(109,78,232,0.08);border:1px solid rgba(109,78,232,0.2);border-radius:999px;padding:6px 16px;margin-bottom:20px">
            <i class="bi bi-question-circle" style="color:#A78BFA;font-size:13px"></i>
            <span style="font-size:12px;color:#A78BFA;font-weight:700;letter-spacing:0.08em;text-transform:uppercase">FAQ</span>
          </div>
          <h2 style="font-weight:700;color:#fff;letter-spacing:-0.03em;font-size:clamp(1.8rem,3.5vw,2.6rem);margin-bottom:12px">
            Frequently Asked Questions
          </h2>
          <p style="color:rgba(255,255,255,0.35);font-size:1rem;line-height:1.6">Everything you need to know about MySocialVSL.</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:6px">
          <div v-for="(faq, i) in faqs" :key="i" data-aos="fade-up" :data-aos-delay="i * 50"
            :style="{
              border: openFaq === i ? '1px solid rgba(109,78,232,0.3)' : '1px solid rgba(255,255,255,0.07)',
              borderRadius: '16px',
              overflow: 'hidden',
              background: openFaq === i ? 'rgba(109,78,232,0.04)' : 'rgba(255,255,255,0.015)',
              transition: 'border-color 0.2s ease, background 0.2s ease'
            }">
            <button
              style="width:100%;display:flex;align-items:center;gap:14px;padding:18px 20px;text-align:left;background:none;cursor:pointer;border:none"
              @click="openFaq = openFaq === i ? null : i">
              <!-- Icon bubble -->
              <div :style="{
                width:'38px',height:'38px',borderRadius:'11px',flexShrink:0,display:'flex',alignItems:'center',justifyContent:'center',
                background: openFaq === i ? 'rgba(109,78,232,0.2)' : 'rgba(255,255,255,0.04)',
                border: openFaq === i ? '1px solid rgba(109,78,232,0.35)' : '1px solid rgba(255,255,255,0.08)',
                transition:'all 0.2s ease'
              }">
                <i :class="'bi ' + faq.icon" :style="{color: openFaq===i?'#A78BFA':'rgba(255,255,255,0.35)',fontSize:'15px',transition:'color 0.2s ease'}"></i>
              </div>
              <!-- Question text -->
              <span style="flex:1;font-weight:500;color:rgba(255,255,255,0.85);font-size:14px;line-height:1.4">{{ faq.q }}</span>
              <!-- Rotating chevron -->
              <div :style="{
                width:'28px',height:'28px',borderRadius:'999px',flexShrink:0,display:'flex',alignItems:'center',justifyContent:'center',
                background:'rgba(255,255,255,0.04)',border:'1px solid rgba(255,255,255,0.08)',
                transform: openFaq===i ? 'rotate(180deg)':'rotate(0deg)',
                transition:'transform 0.28s cubic-bezier(0.4,0,0.2,1)'
              }">
                <i class="bi bi-chevron-down" style="color:rgba(255,255,255,0.4);font-size:11px"></i>
              </div>
            </button>
            <Transition name="faq-slide">
              <div v-if="openFaq === i" style="padding:0 20px 20px 72px">
                <div style="border-top:1px solid rgba(255,255,255,0.06);padding-top:14px">
                  <p style="color:rgba(255,255,255,0.45);font-size:14px;line-height:1.75;margin:0">{{ faq.a }}</p>
                </div>
              </div>
            </Transition>
          </div>
        </div>

        <!-- Bottom CTA -->
        <div data-aos="fade-up" style="text-align:center;margin-top:48px">
          <p style="color:rgba(255,255,255,0.3);font-size:14px;margin-bottom:14px">Still have questions?</p>
          <a href="https://t.me/mysocialvsl" target="_blank"
            style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:999px;background:rgba(109,78,232,0.1);border:1px solid rgba(109,78,232,0.25);color:#A78BFA;font-size:14px;font-weight:600;text-decoration:none;transition:all 0.2s">
            <i class="bi bi-telegram"></i> Chat with us on Telegram
          </a>
        </div>

      </div>
    </section>

    <!-- CTA FINAL -->
    <section style="padding:80px 24px 100px;border-top:1px solid rgba(255,255,255,0.06);position:relative;overflow:hidden;background:linear-gradient(to bottom,#000,#050508)">
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 80%,rgba(109,78,232,0.08),transparent 70%);pointer-events:none"></div>
      <div style="max-width:1100px;margin:0 auto">
        <div data-aos="fade-up" class="cta-box">

          <!-- Decorative orbs -->
          <div style="position:absolute;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(109,78,232,0.18),transparent 70%);top:-80px;right:-60px;pointer-events:none;filter:blur(40px)"></div>
          <div style="position:absolute;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,0.12),transparent 70%);bottom:-60px;left:-40px;pointer-events:none;filter:blur(30px)"></div>

          <!-- Badge -->
          <div style="display:inline-flex;align-items:center;gap:6px;border:1px solid rgba(109,78,232,0.35);border-radius:999px;padding:5px 14px;font-size:11px;font-weight:700;color:#A78BFA;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:24px">
            <span style="width:5px;height:5px;border-radius:50%;background:#A78BFA;display:inline-block;animation:pulse-dot 2s ease-in-out infinite"></span>
            Get started
          </div>

          <!-- Titre + sous-titre -->
          <h2 style="font-weight:800;color:#fff;letter-spacing:-0.04em;line-height:1.1;font-size:clamp(2rem,4vw,3.2rem);margin-bottom:16px;max-width:560px">
            Try MySocialVSL today
          </h2>
          <p style="color:rgba(255,255,255,0.4);font-size:1rem;line-height:1.7;max-width:480px;margin-bottom:36px">
            Live in 30 seconds. Free to start. The only link-in-bio built for creators who want real conversions — not just clicks.
          </p>

          <!-- Boutons -->
          <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center">
            <a href="https://t.me/mysocialvsl" target="_blank" class="cta-btn-outline">
              <i class="bi bi-telegram"></i> Contact us
            </a>
            <RouterLink to="/register" class="cta-btn-primary">
              Sign up free <i class="bi bi-arrow-right"></i>
            </RouterLink>
          </div>

        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer style="position:relative;border-top:1px solid rgba(255,255,255,0.06);background:linear-gradient(to bottom,#080810,#0a0814);font-family:Inter,sans-serif">
      <div style="max-width:1152px;margin:0 auto;padding:64px 24px 0">

        <!-- Decorative orb -->
        <div style="position:absolute;top:0;right:5%;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(109,78,232,0.08),transparent 70%);pointer-events:none"></div>

        <div style="display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:48px;margin-bottom:56px">

          <!-- Col 1 — Brand + newsletter -->
          <div style="position:relative">
            <!-- Logo -->
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
              <LogoMark :size="28" />
              <span style="font-weight:700;color:#fff;font-size:15px;letter-spacing:-0.02em">MySocialVSL</span>
            </div>
            <p style="color:rgba(255,255,255,0.35);font-size:13px;line-height:1.65;margin:0 0 20px;max-width:240px">
              The VSL link-in-bio that turns your fans into paying subscribers.
            </p>
            <!-- Newsletter -->
            <div style="position:relative;max-width:280px">
              <input type="email" placeholder="Your email address"
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

          <!-- Col 2 — Product -->
          <div>
            <h3 style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.9);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 18px">Product</h3>
            <nav style="display:flex;flex-direction:column;gap:10px">
              <a href="#features" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Features</a>
              <a href="#pricing" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Pricing</a>
              <a href="#faq" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">FAQ</a>
              <RouterLink to="/register" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Create an account</RouterLink>
              <RouterLink to="/login" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Log in</RouterLink>
            </nav>
          </div>

          <!-- Col 3 — Resources -->
          <div>
            <h3 style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.9);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 18px">Resources</h3>
            <nav style="display:flex;flex-direction:column;gap:10px">
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Blog</a>
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">API Documentation</a>
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Affiliates</a>
              <a href="#" style="color:rgba(255,255,255,0.4);font-size:13px;text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='#A78BFA'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">Status</a>
            </nav>
          </div>

          <!-- Col 4 — Social -->
          <div>
            <h3 style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.9);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 18px">Follow us</h3>
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
          <p style="font-size:12px;color:rgba(255,255,255,0.2);margin:0">© 2026 MySocialVSL. All rights reserved.</p>
          <nav style="display:flex;gap:24px">
            <RouterLink to="/privacy" style="color:rgba(255,255,255,0.2);font-size:12px;text-decoration:none;transition:color 0.15s" @mouseover="e => e.target.style.color='rgba(255,255,255,0.5)'" @mouseout="e => e.target.style.color='rgba(255,255,255,0.2)'">Privacy Policy</RouterLink>
            <RouterLink to="/terms" style="color:rgba(255,255,255,0.2);font-size:12px;text-decoration:none;transition:color 0.15s" @mouseover="e => e.target.style.color='rgba(255,255,255,0.5)'" @mouseout="e => e.target.style.color='rgba(255,255,255,0.2)'">Terms</RouterLink>
            <RouterLink to="/cookies" style="color:rgba(255,255,255,0.2);font-size:12px;text-decoration:none;transition:color 0.15s" @mouseover="e => e.target.style.color='rgba(255,255,255,0.5)'" @mouseout="e => e.target.style.color='rgba(255,255,255,0.2)'">Cookies</RouterLink>
          </nav>
        </div>

      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const mobileMenuOpen = ref(false)
import AOS from 'aos'
import PhoneDemo from '@/components/PhoneDemo.vue'
import PricingSection from '@/components/PricingSection.vue'
import LogoMark from '@/components/LogoMark.vue'
import OrbitalTimeline from '@/components/OrbitalTimeline.vue'

const syncGlowPointer = (e) => {
  const xp = (e.clientX / window.innerWidth).toFixed(2)
  const yp = (e.clientY / window.innerHeight).toFixed(2)
  // Coordinates relative to each card so the effects stay independent
  document.querySelectorAll('[data-glow]:not([data-glow] [data-glow])').forEach(el => {
    const rect = el.getBoundingClientRect()
    el.style.setProperty('--x',  (e.clientX - rect.left).toFixed(2))
    el.style.setProperty('--y',  (e.clientY - rect.top).toFixed(2))
    el.style.setProperty('--xp', xp)
    el.style.setProperty('--yp', yp)
  })
}

onMounted(() => {
  AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 80 })
  deepLinkRaf = requestAnimationFrame(tickDeepLink)
  document.addEventListener('pointermove', syncGlowPointer)
})
onUnmounted(() => {
  cancelAnimationFrame(deepLinkRaf)
  document.removeEventListener('pointermove', syncGlowPointer)
})

const openFaq = ref(null)

const heroStats = [
  { value: '+80%',   label: 'avg conversion rate vs Linktree' },
  { value: '94%',    label: 'of clicks open the native app (deep link)' },
  { value: '< 2min', label: 'to create and publish your page' },
]

const stats = [
  { label: 'Views this month', value: '1,860', sub: '↑ +52% vs last month',  up: true },
  { label: 'Conversion',       value: '41.7%', sub: '↑ +80% vs Linktree',     up: true },
  { label: 'Link clicks',      value: '775',   sub: '↑ +44% this month',      up: true },
  { label: 'Deep link bypass', value: '94%',   sub: 'Native app open',        up: false },
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
  { label: 'Autoplay muted on page load',          icon: 'bi-play-circle',       sub: 'Works on Chrome & Safari mobile' },
  { label: 'Multiple videos per page',              icon: 'bi-collection-play',   sub: null },
  { label: '10+ fully customizable themes',         icon: 'bi-palette',           sub: null },
  { label: 'Deep linking — opens native app',       icon: 'bi-link-45deg',        sub: 'Bypasses Instagram WebView' },
]

const deepLinkSteps = [
  { app: 'Instagram', desc: "Bypasses in-app browser → Safari/Chrome" },
  { app: 'TikTok',   desc: "Bypasses in-app browser → Safari/Chrome" },
  { app: 'Twitch',   desc: "Opens native app directly" },
  { app: 'Telegram', desc: "Opens native app directly" },
]

const analyticsStats = [
  { label: 'Page Views',    value: '12.4k', trend: '↑ +28% this month', up: true },
  { label: 'VSL Play Rate', value: '74%',   trend: '74% of fans hit play', up: true },
  { label: 'Avg CTR',       value: '38%',   trend: '↑ +12 pts vs Linktree', up: true },
  { label: 'Watch → Click', value: '18s',   trend: 'avg watch before click', up: false },
]

const vslFunnel = [
  { label: 'Visitors',           value: '12 400', pct: 100 },
  { label: 'Started the video',  value: '9 176',  pct: 74  },
  { label: 'Watched halfway',    value: '5 084',  pct: 41  },
  { label: 'Clicked → OnlyFans', value: '4 712',  pct: 38  },
]


const faqs = [
  { icon: 'bi-play-circle',
    q: "How does autoplay video work?",
    a: "The video starts automatically, muted, as soon as a visitor lands on your page. They can tap to unmute. It works on all modern mobile browsers — Chrome and Safari — without any extra configuration." },
  { icon: 'bi-link-45deg',
    q: "What is deep linking?",
    a: "MySocialVSL detects when a fan is visiting from inside Instagram, TikTok, or any in-app browser, and forces the link to open in their native browser (Safari/Chrome). This bypasses restrictions that break payments and signups on OnlyFans or MYM." },
  { icon: 'bi-arrow-left-right',
    q: "What's the difference with Linktree?",
    a: "Linktree is a generic static page with no video. MySocialVSL adds autoplay VSL, deep linking, Shield Protection™ (bot detection), and granular analytics. Average conversion rate is +80% higher than Linktree." },
  { icon: 'bi-camera-video',
    q: "Can I use my own video?",
    a: "Yes. Paste any video URL (Streamable, YouTube, Cloudflare Stream, Vimeo) or upload an MP4 directly from your dashboard. The video is automatically encoded for fast mobile playback." },
  { icon: 'bi-phone',
    q: "Does it work on mobile?",
    a: "70%+ of traffic comes from mobile. Pages are 100% responsive and muted autoplay works on both Chrome and Safari mobile. The phone-first design is optimised for the Instagram and TikTok audience." },
  { icon: 'bi-shield-check',
    q: "What is Shield Protection™?",
    a: "Shield Protection™ detects 30+ known crawlers and Instagram's internal scanner. When a bot visits, it sees a harmless decoy page instead of your real links — so your page stays safe and never gets flagged." },
]

// Symmetric 5+5 zigzag: outer(8%) and inner(22%) alternating every 140px
const deepLinkFloatingIcons = [
  // ── Gauche ──────────────────────────────────────────────────────────
  { name: 'Instagram', duration: 6,   delay: 0,   biClass: 'bi-instagram', iconColor: 'white',
    pos: { top: '20px',  left: '8%' },
    color: 'linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)', glow: 'rgba(220,39,67,0.4)' },
  { name: 'Pinterest', duration: 7.2, delay: 2.0, biClass: 'bi-pinterest', iconColor: 'white',
    pos: { top: '160px', left: '22%' },
    color: '#E60023', glow: 'rgba(230,0,35,0.38)' },
  { name: 'TikTok',    duration: 7.5, delay: 1.2, biClass: 'bi-tiktok',    iconColor: 'white',
    pos: { top: '300px', left: '8%' },
    color: '#010101', glow: 'rgba(105,201,208,0.35)' },
  { name: 'Spotify',   duration: 7.8, delay: 1.0, biClass: 'bi-spotify',   iconColor: '#1DB954',
    pos: { top: '440px', left: '22%' },
    color: '#191414', glow: 'rgba(29,185,84,0.38)' },
  { name: 'YouTube',   duration: 8,   delay: 0.6, biClass: 'bi-youtube',   iconColor: 'white',
    pos: { top: '580px', left: '8%' },
    color: '#FF0000', glow: 'rgba(255,0,0,0.38)' },
  // ── Droite ──────────────────────────────────────────────────────────
  { name: 'Twitter',   duration: 6.5, delay: 1.8, biClass: 'bi-twitter-x', iconColor: 'white',
    pos: { top: '20px',  right: '8%' },
    color: '#000000', glow: 'rgba(255,255,255,0.12)' },
  { name: 'Discord',   duration: 8.5, delay: 0.3, biClass: 'bi-discord',   iconColor: 'white',
    pos: { top: '160px', right: '22%' },
    color: '#5865F2', glow: 'rgba(88,101,242,0.45)' },
  { name: 'Threads',   duration: 9,   delay: 0.9, biClass: 'bi-threads',   iconColor: 'white',
    pos: { top: '300px', right: '8%' },
    color: '#000000', glow: 'rgba(255,255,255,0.12)' },
  { name: 'Telegram',  duration: 6.2, delay: 2.2, biClass: 'bi-telegram',  iconColor: 'white',
    pos: { top: '440px', right: '22%' },
    color: '#26A5E4', glow: 'rgba(38,165,228,0.45)' },
  { name: 'Twitch',    duration: 7,   delay: 1.5, biClass: 'bi-twitch',    iconColor: 'white',
    pos: { top: '580px', right: '8%' },
    color: '#9146FF', glow: 'rgba(145,70,255,0.45)' },
]

const deepLinkIconRefs = []
const deepLinkMousePos = { x: -9999, y: -9999 }
const deepLinkCurrentOffsets = deepLinkFloatingIcons.map(() => ({ x: 0, y: 0 }))
let deepLinkRaf = null

const onDeepLinkMouseMove = (e) => {
  deepLinkMousePos.x = e.clientX
  deepLinkMousePos.y = e.clientY
}

const tickDeepLink = () => {
  deepLinkIconRefs.forEach((el, i) => {
    if (!el) return
    const rect = el.getBoundingClientRect()
    const cx = rect.left + rect.width / 2
    const cy = rect.top + rect.height / 2
    const dx = deepLinkMousePos.x - cx
    const dy = deepLinkMousePos.y - cy
    const dist = Math.sqrt(dx * dx + dy * dy)
    const o = deepLinkCurrentOffsets[i]
    let tx = 0, ty = 0
    if (dist < 150 && dist > 0) {
      const force = (1 - dist / 150) * 50
      tx = -(dx / dist) * force
      ty = -(dy / dist) * force
    }
    o.x += (tx - o.x) * 0.12
    o.y += (ty - o.y) * 0.12
    el.style.transform = `translate(${o.x.toFixed(2)}px, ${o.y.toFixed(2)}px)`
  })
  deepLinkRaf = requestAnimationFrame(tickDeepLink)
}

const socials = [
  { label: 'Instagram', href: '#', icon: 'bi bi-instagram' },
  { label: 'TikTok',    href: '#', icon: 'bi bi-tiktok' },
  { label: 'Twitter/X', href: '#', icon: 'bi bi-twitter-x' },
  { label: 'Telegram',  href: '#', icon: 'bi bi-telegram' },
]
</script>

<style scoped>
/* ── Navbar ── */
.nav-link {
  color: rgba(255,255,255,0.45);
  font-size: 14px;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.15s;
}
.nav-link:hover { color: #fff; }

.nav-cta {
  background: linear-gradient(135deg, #6D4EE8, #8B6FF0);
  color: #fff;
  padding: 10px 22px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  box-shadow: 0 0 20px rgba(109,78,232,0.35);
  transition: box-shadow 0.2s;
}
.nav-cta:hover { box-shadow: 0 0 30px rgba(109,78,232,0.55); }

.nav-burger {
  background: none;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 8px;
  padding: 6px 10px;
  cursor: pointer;
  display: none;
}

.drawer-link {
  display: flex;
  align-items: center;
  gap: 10px;
  color: rgba(255,255,255,0.6);
  font-size: 15px;
  font-weight: 500;
  text-decoration: none;
  padding: 12px 14px;
  border-radius: 10px;
  transition: all 0.15s;
}
.drawer-link:hover {
  background: rgba(255,255,255,0.05);
  color: #fff;
}

@media (max-width: 768px) {
  .nav-desktop { display: none !important; }
  .nav-mobile  { display: flex !important; }
}

@keyframes drawer-in {
  from { transform: translateX(100%); opacity: 0; }
  to   { transform: translateX(0);    opacity: 1; }
}
.nav-drawer-enter-active { animation: drawer-in 0.25s cubic-bezier(0.16,1,0.3,1); }
.nav-drawer-leave-active { animation: drawer-in 0.2s cubic-bezier(0.16,1,0.3,1) reverse; }

/* ── CTA Final ── */
.cta-box {
  position: relative;
  overflow: hidden;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 24px;
  padding: 64px 48px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.cta-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #6D4EE8, #8B6FF0);
  color: #fff;
  padding: 14px 28px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  box-shadow: 0 0 28px rgba(109,78,232,0.4);
  transition: box-shadow 0.2s;
}
.cta-btn-primary:hover { box-shadow: 0 0 42px rgba(109,78,232,0.6); }

.cta-btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.05);
  color: rgba(255,255,255,0.7);
  padding: 14px 28px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  border: 1px solid rgba(255,255,255,0.12);
  transition: all 0.2s;
}
.cta-btn-outline:hover {
  background: rgba(255,255,255,0.09);
  color: #fff;
  border-color: rgba(255,255,255,0.22);
}

/* ── Scroll logos ── */
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
@keyframes dl-float {
  0%   { transform: translate(0px, 0px) rotate(0deg); }
  33%  { transform: translate(4px, -9px) rotate(3deg); }
  66%  { transform: translate(-3px, -5px) rotate(-2deg); }
  100% { transform: translate(2px, -12px) rotate(1.5deg); }
}

@keyframes vsl-wave {
  0%   { transform: scaleY(0.5); opacity: 0.5; }
  100% { transform: scaleY(1.4); opacity: 1; }
}

/* FAQ accordion transition */
.faq-slide-enter-active { transition: all 0.28s cubic-bezier(0.4,0,0.2,1); overflow:hidden; }
.faq-slide-leave-active { transition: all 0.2s cubic-bezier(0.4,0,0.2,1); overflow:hidden; }
.faq-slide-enter-from   { opacity:0; max-height:0; }
.faq-slide-enter-to     { opacity:1; max-height:200px; }
.faq-slide-leave-from   { opacity:1; max-height:200px; }
.faq-slide-leave-to     { opacity:0; max-height:0; }

@keyframes cta-pulse {
  0%,100% { box-shadow: 0 4px 16px rgba(0,175,240,0.4); }
  50%     { box-shadow: 0 6px 28px rgba(0,175,240,0.75); }
}

/* ── Page global ambient ── */
.page-root {
  background:
    radial-gradient(ellipse 90% 40% at 70% 0%,   rgba(109,78,232,0.07) 0%, transparent 100%),
    radial-gradient(ellipse 70% 35% at 10% 100%, rgba(89,64,209,0.05)  0%, transparent 100%),
    #000;
}
</style>

<style>
/* ── GlowCard ── spotlight + border glow following the cursor ── */
[data-glow]:not([data-glow] [data-glow]) {
  --border-size:    calc(var(--border, 1) * 1px);
  --spotlight-size: calc(var(--size, 200) * 1px);
  --hue:            calc(var(--base, 220) + (var(--xp, 0) * var(--spread, 0)));
  background-color: #0d0d0d;
  background-image: radial-gradient(
    var(--spotlight-size) var(--spotlight-size) at
    calc(var(--x, -9999) * 1px) calc(var(--y, -9999) * 1px),
    hsl(var(--hue) 100% 65% / 0.07),
    transparent
  );
}

[data-glow]::before,
[data-glow]::after {
  pointer-events: none;
  content: "";
  position: absolute;
  inset: calc(var(--border-size, 1px) * -1);
  border: var(--border-size, 1px) solid transparent;
  border-radius: calc(var(--radius, 14) * 1px);
  background-repeat: no-repeat;
  -webkit-mask: linear-gradient(transparent, transparent), linear-gradient(white, white);
  mask: linear-gradient(transparent, transparent), linear-gradient(white, white);
  -webkit-mask-clip: padding-box, border-box;
  mask-clip: padding-box, border-box;
  -webkit-mask-composite: destination-in;
  mask-composite: intersect;
}

[data-glow]::before {
  background-image: radial-gradient(
    calc(var(--spotlight-size, 200px) * 0.75) calc(var(--spotlight-size, 200px) * 0.75) at
    calc(var(--x, -9999) * 1px) calc(var(--y, -9999) * 1px),
    hsl(var(--hue, 220) 100% 50% / 1),
    transparent 100%
  );
  filter: brightness(2);
}

[data-glow]::after {
  background-image: radial-gradient(
    calc(var(--spotlight-size, 200px) * 0.5) calc(var(--spotlight-size, 200px) * 0.5) at
    calc(var(--x, -9999) * 1px) calc(var(--y, -9999) * 1px),
    hsl(0 100% 100% / 0.8),
    transparent 100%
  );
}

[data-glow] [data-glow] {
  position: absolute;
  inset: 0;
  will-change: filter;
  border-radius: calc(var(--radius, 14) * 1px);
  filter: blur(calc(var(--border-size, 1px) * 8));
  background: none;
  pointer-events: none;
  border: none;
  opacity: 0.6;
}

[data-glow] > [data-glow]::before {
  inset: -8px;
  border-width: 8px;
}
</style>
