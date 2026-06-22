<template>
  <DashboardLayout
    title="My Pages"
    :pagesCount="pages.length"
    :pagesLimit="auth.user?.page_limit ?? 1"
  >
    <template #header-actions>
      <RouterLink to="/pages/new" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#6D4EE8;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:background 0.15s" onmouseover="this.style.background='#5d3ed4'" onmouseout="this.style.background='#6D4EE8'">
        <i class="bi bi-plus-lg" style="font-size:13px"></i> New page
      </RouterLink>
    </template>

    <!-- Loading -->
    <div v-if="loading" style="display:flex;align-items:center;justify-content:center;padding:80px;color:var(--text-dim);font-size:13px">
      <div style="width:20px;height:20px;border:2px solid var(--border);border-top-color:#6D4EE8;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:10px"></div>
      Loading...
    </div>

    <template v-else>
      <!-- ─── Brand-new creator → welcoming, VSL-first empty state ─── -->
      <div v-if="pages.length === 0" style="display:flex;flex-direction:column;align-items:center;text-align:center;padding:52px 24px 64px;max-width:680px;margin:0 auto">

        <!-- Video/VSL hero icon -->
        <div style="position:relative;width:84px;height:84px;margin-bottom:26px">
          <div style="position:absolute;inset:-6px;background:radial-gradient(circle,rgba(109,78,232,0.4),transparent 70%);filter:blur(10px)"></div>
          <div style="position:relative;width:84px;height:84px;border-radius:22px;background:linear-gradient(145deg,#6D4EE8,#4c1d95);display:flex;align-items:center;justify-content:center;box-shadow:0 12px 32px rgba(109,78,232,0.4)">
            <i class="bi bi-play-fill" style="font-size:42px;color:#fff;margin-left:4px"></i>
          </div>
        </div>

        <h1 style="font-size:34px;font-weight:800;letter-spacing:-0.03em;color:var(--text);margin:0 0 14px;line-height:1.12">
          Your bio link, but it <span style="background:linear-gradient(90deg,#A78BFA,#6D4EE8);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">sells</span>.
        </h1>
        <p style="font-size:15px;color:var(--text-muted);margin:0 0 32px;line-height:1.6;max-width:490px">
          A short video that hooks your visitors and sends them straight to your OnlyFans — that's a <strong style="color:var(--text2);font-weight:600">VSL</strong>. It converts far better than a plain list of links. Build yours in under a minute.
        </p>

        <!-- CTAs -->
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:center">
          <RouterLink to="/pages/new" style="display:inline-flex;align-items:center;gap:8px;padding:13px 24px;background:#6D4EE8;color:#fff;border-radius:11px;font-size:14px;font-weight:700;text-decoration:none;transition:all 0.15s;box-shadow:0 8px 24px rgba(109,78,232,0.35)"
            onmouseover="this.style.background='#5d3ed4';this.style.transform='translateY(-1px)'"
            onmouseout="this.style.background='#6D4EE8';this.style.transform='none'">
            Create my first VSL <i class="bi bi-arrow-right"></i>
          </RouterLink>
          <button @click="watchExample" style="display:inline-flex;align-items:center;gap:8px;padding:13px 22px;background:var(--pill-bg);color:var(--text2);border:1px solid var(--border);border-radius:11px;font-size:14px;font-weight:600;cursor:pointer;font-family:Inter,sans-serif;transition:all 0.15s">
            <i class="bi bi-play-circle"></i> Watch a live example
          </button>
        </div>
        <p style="font-size:12px;color:var(--text-dim);margin:14px 0 0;display:flex;align-items:center;gap:5px">
          <i class="bi bi-lightning-charge-fill" style="color:#fb923c"></i> Takes less than 30 seconds
        </p>

        <!-- Feature cards -->
        <div style="width:100%;margin-top:52px">
          <p style="font-size:11px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.14em;margin:0 0 18px">Everything you need to convert</p>
          <div class="dash-feat-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
            <div class="feat-card">
              <div class="feat-icon" style="background:rgba(167,139,250,0.14);color:#A78BFA"><i class="bi bi-box-arrow-up-right"></i></div>
              <p class="feat-title">Deeplink Bypass</p>
              <p class="feat-desc">Escapes Instagram's in-app browser so your links open in real Safari &amp; Chrome.</p>
            </div>
            <div class="feat-card">
              <div class="feat-icon" style="background:rgba(34,211,238,0.14);color:#22d3ee"><i class="bi bi-shield-fill-check"></i></div>
              <p class="feat-title">Bot Protection</p>
              <p class="feat-desc">Cloaks your real page from Meta's crawlers — bots see a safe decoy, humans see you.</p>
            </div>
            <div class="feat-card">
              <div class="feat-icon" style="background:rgba(74,222,128,0.14);color:#4ade80"><i class="bi bi-graph-up-arrow"></i></div>
              <p class="feat-title">Real-time Analytics</p>
              <p class="feat-desc">Every view, click &amp; video play tracked live — see exactly what converts.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── Has pages → full dashboard ─── -->
      <template v-else>
      <!-- Stats summary row -->
      <div class="dash-kpi-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px">
        <div class="kpi-card">
          <div class="kpi-card-top">
            <span class="kpi-card-label">VSL Pages</span>
            <div class="kpi-card-icon" style="background:rgba(109,78,232,0.15)"><i class="bi bi-grid-1x2-fill" style="color:#A78BFA"></i></div>
          </div>
          <p class="kpi-card-value">{{ pages.length }}</p>
          <p class="kpi-card-sub">Limit {{ (auth.user?.page_limit ?? 1) > 100000 ? '∞' : (auth.user?.page_limit ?? 1) }}</p>
        </div>
        <div class="kpi-card">
          <div class="kpi-card-top">
            <span class="kpi-card-label">Page views</span>
            <div class="kpi-card-icon" style="background:rgba(8,145,178,0.15)"><i class="bi bi-eye-fill" style="color:#22d3ee"></i></div>
          </div>
          <p class="kpi-card-value">{{ totalViews.toLocaleString() }}</p>
          <p class="kpi-card-sub">Last {{ period }} days</p>
        </div>
        <div class="kpi-card">
          <div class="kpi-card-top">
            <span class="kpi-card-label">Total clicks</span>
            <div class="kpi-card-icon" style="background:rgba(234,88,12,0.15)"><i class="bi bi-lightning-charge-fill" style="color:#fb923c"></i></div>
          </div>
          <p class="kpi-card-value">{{ totalClicks.toLocaleString() }}</p>
          <p class="kpi-card-sub">Last {{ period }} days</p>
        </div>
        <div class="kpi-card">
          <div class="kpi-card-top">
            <span class="kpi-card-label">Avg. CTR</span>
            <div class="kpi-card-icon" style="background:rgba(22,163,74,0.15)"><i class="bi bi-graph-up-arrow" style="color:#4ade80"></i></div>
          </div>
          <p class="kpi-card-value">{{ avgCtr }}%</p>
          <p class="kpi-card-sub">Last {{ period }} days</p>
        </div>
      </div>

      <!-- Period selector -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <h2 style="font-size:14px;font-weight:600;color:var(--text);margin:0">Your pages</h2>
        <div style="display:flex;gap:2px;background:var(--pill-bg);border-radius:8px;padding:3px">
          <button v-for="p in [7,30,90]" :key="p" @click="setPeriod(p)"
            :style="{padding:'4px 12px',borderRadius:'6px',border:'none',fontSize:'12px',fontWeight:600,cursor:'pointer',transition:'all 0.12s',background:period===p?'var(--surface)':'transparent',color:period===p?'var(--text)':'var(--text-muted)',fontFamily:'Inter,sans-serif',boxShadow:period===p?'0 1px 3px rgba(0,0,0,0.12)':'none'}">
            {{ p }}d
          </button>
        </div>
      </div>

      <!-- Groups list -->
      <div style="display:flex;flex-direction:column;gap:10px">

        <div v-for="(group, gi) in groups" :key="group.id"
          style="background:var(--card-bg);border:1px solid var(--border);border-radius:16px;overflow:hidden">

          <!-- Group header -->
          <div @click="group.open = !group.open"
            style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;cursor:pointer;transition:background 0.12s"
            onmouseover="this.style.background='rgba(109,78,232,0.05)'"
            onmouseout="this.style.background='transparent'">
            <div style="display:flex;align-items:center;gap:10px">
              <i class="bi bi-folder2" style="font-size:14px;color:var(--text-muted)"></i>
              <span style="font-size:13px;font-weight:700;color:var(--text)">{{ group.name }}</span>
              <span style="font-size:11px;color:var(--text-dim);background:var(--pill-bg);border-radius:999px;padding:1px 8px">{{ pagesInGroup(group.id).length }} page{{ pagesInGroup(group.id).length !== 1 ? 's' : '' }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <button v-if="gi > 0" @click.stop="removeGroup(gi)"
                style="background:none;border:none;cursor:pointer;padding:4px 6px;color:var(--text-faint);border-radius:6px;display:flex;align-items:center;transition:all 0.15s;font-size:13px"
                onmouseover="this.style.color='#f87171';this.style.background='rgba(248,113,113,0.08)'"
                onmouseout="this.style.color='var(--text-faint)';this.style.background='none'">
                <i class="bi bi-trash3"></i>
              </button>
              <i :class="group.open ? 'bi bi-chevron-up' : 'bi bi-chevron-down'" style="font-size:12px;color:var(--text-dim)"></i>
            </div>
          </div>

          <div v-if="group.open">
            <!-- Empty state (only for ungrouped / first group) -->
            <div v-if="pagesInGroup(group.id).length === 0 && gi === 0 && pages.length === 0"
              style="padding:48px 32px;text-align:center;border-top:1px solid var(--border-light)">
              <div style="width:48px;height:48px;background:rgba(109,78,232,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <i class="bi bi-plus-lg" style="font-size:20px;color:#6D4EE8"></i>
              </div>
              <p style="font-size:14px;font-weight:600;color:var(--text);margin:0 0 6px">No pages yet</p>
              <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px">Create your first VSL page to get started</p>
              <RouterLink to="/pages/new" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#6D4EE8;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
                <i class="bi bi-plus-lg"></i> Create page
              </RouterLink>
            </div>
            <!-- Empty group (not ungrouped) -->
            <div v-else-if="pagesInGroup(group.id).length === 0"
              style="padding:28px 20px;text-align:center;border-top:1px solid var(--border-light)">
              <p style="font-size:13px;color:var(--text-faint);margin:0">Empty group — drag pages here</p>
            </div>

            <!-- Pages list -->
            <div v-else>
              <div v-for="page in pagesInGroup(group.id)" :key="page.id"
                @click="openModal(page)"
                class="dash-page-row"
                style="display:flex;align-items:center;gap:12px;padding:13px 20px;border-top:1px solid var(--border-light);cursor:pointer;transition:background 0.12s"
                onmouseover="this.style.background='rgba(109,78,232,0.05)'"
                onmouseout="this.style.background='transparent'">

                <!-- Avatar -->
                <div :style="{width:'38px',height:'38px',borderRadius:'10px',background:page.bg_color||'#1a1438',display:'flex',alignItems:'center',justifyContent:'center',overflow:'hidden',flexShrink:0,border:'1px solid var(--border)'}">
                  <img v-if="page.avatar_url" :src="page.avatar_url" style="width:100%;height:100%;object-fit:cover" />
                  <i v-else class="bi bi-play-btn-fill" style="font-size:14px;color:rgba(255,255,255,0.5)"></i>
                </div>

                <!-- Info -->
                <div style="flex:1;min-width:0">
                  <p style="font-size:13px;font-weight:600;color:var(--text);margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ page.model_name }}</p>
                  <p style="font-size:11px;color:var(--text-dim);margin:0">mysocialvsl.com/p/{{ page.slug }}</p>
                </div>

                <!-- Badges -->
                <span v-if="page.video_url" style="font-size:10px;font-weight:700;color:#f97316;background:rgba(249,115,22,0.12);border:1px solid rgba(249,115,22,0.3);border-radius:999px;padding:1px 8px;flex-shrink:0">VSL</span>
                <span v-if="page.deep_link_enabled" style="font-size:10px;font-weight:700;color:#8b5cf6;background:rgba(139,92,246,0.12);border:1px solid rgba(139,92,246,0.3);border-radius:999px;padding:1px 8px;flex-shrink:0">Deep link</span>

                <!-- Stats inline -->
                <div class="dash-row-stats" style="display:flex;gap:16px;flex-shrink:0">
                  <div style="text-align:center">
                    <p style="font-size:12px;font-weight:700;color:var(--text);margin:0">{{ getPageStat(page.id, 'page_views') }}</p>
                    <p style="font-size:9px;color:var(--text-dim);margin:0;text-transform:uppercase;letter-spacing:0.05em">Views</p>
                  </div>
                  <div style="text-align:center">
                    <p style="font-size:12px;font-weight:700;color:var(--text);margin:0">{{ getPageStat(page.id, 'link_clicks') }}</p>
                    <p style="font-size:9px;color:var(--text-dim);margin:0;text-transform:uppercase;letter-spacing:0.05em">Clicks</p>
                  </div>
                  <div style="text-align:center">
                    <p :style="{fontSize:'12px',fontWeight:700,margin:0,color:getPageStat(page.id,'ctr')>10?'#4ade80':'var(--text)'}">{{ getPageStat(page.id, 'ctr') }}%</p>
                    <p style="font-size:9px;color:var(--text-dim);margin:0;text-transform:uppercase;letter-spacing:0.05em">CTR</p>
                  </div>
                </div>

                <!-- Active toggle -->
                <button @click.stop="toggleActive(page)"
                  :style="{display:'inline-flex',alignItems:'center',gap:'5px',padding:'5px 11px',borderRadius:'999px',border:'none',cursor:'pointer',fontSize:'11px',fontWeight:600,transition:'all 0.15s',flexShrink:0,fontFamily:'Inter,sans-serif',background:page.is_active?'rgba(16,185,129,0.15)':'var(--pill-bg)',color:page.is_active?'#10b981':'var(--text-muted)'}">
                  <i class="bi bi-power" style="font-size:12px"></i>
                  {{ page.is_active ? 'Active' : 'Off' }}
                </button>

                <!-- 3-dot -->
                <button @click.stop="openModal(page)"
                  style="background:none;border:none;cursor:pointer;padding:6px;color:var(--text-faint);border-radius:6px;display:flex;align-items:center;flex-shrink:0;transition:all 0.15s"
                  onmouseover="this.style.background='var(--pill-bg)';this.style.color='var(--text2)'"
                  onmouseout="this.style.background='none';this.style.color='var(--text-faint)'">
                  <i class="bi bi-three-dots" style="font-size:15px"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Add New Group -->
        <div v-if="!addingGroup"
          @click="addingGroup = true"
          style="display:flex;align-items:center;gap:8px;padding:12px 20px;border:1px dashed var(--border);border-radius:12px;cursor:pointer;transition:all 0.15s"
          onmouseover="this.style.borderColor='rgba(109,78,232,0.4)';this.style.background='rgba(109,78,232,0.05)'"
          onmouseout="this.style.borderColor='var(--border)';this.style.background='transparent'">
          <i class="bi bi-folder-plus" style="font-size:14px;color:var(--text-dim)"></i>
          <span style="font-size:12px;font-weight:500;color:var(--text-dim)">Add a new group</span>
        </div>

        <!-- New group inline input -->
        <div v-else style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid rgba(109,78,232,0.5);border-radius:12px;background:rgba(109,78,232,0.06)">
          <i class="bi bi-folder2" style="font-size:14px;color:#6D4EE8;flex-shrink:0"></i>
          <input ref="newGroupInput" v-model="newGroupName"
            @keydown.enter="confirmAddGroup"
            @keydown.esc="addingGroup = false; newGroupName = ''"
            placeholder="Group name…"
            style="flex:1;background:none;border:none;outline:none;font-size:13px;font-weight:600;color:var(--text);font-family:inherit;min-width:0"
          />
          <button @click="confirmAddGroup"
            :disabled="!newGroupName.trim()"
            style="padding:5px 14px;border-radius:8px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:inherit;background:#6D4EE8;color:#fff;flex-shrink:0;transition:opacity 0.15s"
            :style="{opacity: newGroupName.trim() ? 1 : 0.4, cursor: newGroupName.trim() ? 'pointer' : 'default'}">
            Add
          </button>
          <button @click="addingGroup = false; newGroupName = ''"
            style="background:none;border:none;cursor:pointer;padding:4px;color:var(--text-dim);font-size:14px;flex-shrink:0">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

      </div>
      </template>
    </template>

    <!-- ─── Quick View Modal ─── -->
    <Teleport to="body">
      <div v-if="selectedPage"
        style="position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:200;display:flex;align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(4px)"
        @click.self="selectedPage=null">
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:20px;width:100%;max-width:820px;max-height:88vh;overflow-y:auto;box-shadow:0 32px 80px rgba(0,0,0,0.4)">

          <!-- Modal header -->
          <div style="display:flex;align-items:center;gap:14px;padding:20px 24px;border-bottom:1px solid var(--border)">
            <div :style="{width:'52px',height:'52px',borderRadius:'12px',background:selectedPage.bg_color||'#1a1438',display:'flex',alignItems:'center',justifyContent:'center',overflow:'hidden',flexShrink:0,border:'1px solid var(--border)'}">
              <img v-if="selectedPage.avatar_url" :src="selectedPage.avatar_url" style="width:100%;height:100%;object-fit:cover" />
              <i v-else class="bi bi-play-btn-fill" style="font-size:22px;color:rgba(255,255,255,0.5)"></i>
            </div>
            <div style="flex:1;min-width:0">
              <p style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 3px">{{ selectedPage.model_name }}</p>
              <p style="font-size:12px;color:var(--text-muted);margin:0 0 2px">mysocialvsl.com/p/{{ selectedPage.slug }}</p>
              <p style="font-size:11px;color:var(--text-faint);margin:0">Group: <span style="color:var(--text-muted)">Ungrouped</span></p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
              <button @click="toggleActive(selectedPage)"
                :style="{display:'inline-flex',alignItems:'center',gap:'6px',padding:'7px 14px',borderRadius:'999px',border:selectedPage.is_active?'1px solid rgba(16,185,129,0.4)':'1px solid var(--border)',cursor:'pointer',fontSize:'12px',fontWeight:700,transition:'all 0.15s',fontFamily:'Inter,sans-serif',background:selectedPage.is_active?'rgba(16,185,129,0.12)':'var(--pill-bg)',color:selectedPage.is_active?'#10b981':'var(--text-muted)'}">
                <i class="bi bi-power" style="font-size:12px"></i>
                {{ selectedPage.is_active ? 'Active' : 'Inactive' }}
              </button>
              <button @click="selectedPage=null"
                style="background:var(--pill-bg);border:1px solid var(--border);border-radius:8px;padding:7px 9px;cursor:pointer;color:var(--text-muted);display:flex;align-items:center;transition:all 0.15s">
                <i class="bi bi-x-lg" style="font-size:13px"></i>
              </button>
            </div>
          </div>

          <!-- Modal body -->
          <div style="display:grid;grid-template-columns:1fr 260px;gap:12px;padding:16px 24px 24px">

            <!-- Left column -->
            <div style="display:flex;flex-direction:column;gap:10px">

              <!-- Today stat -->
              <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:14px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between">
                <div>
                  <p style="font-size:10px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px;display:flex;align-items:center;gap:5px">
                    <i class="bi bi-clock" style="font-size:11px"></i> Today
                  </p>
                  <p style="font-size:30px;font-weight:800;color:var(--text);letter-spacing:-0.04em;margin:0;line-height:1">
                    {{ getPageStat(selectedPage.id, 'page_views') }}
                  </p>
                </div>
                <i class="bi bi-graph-up-arrow" style="font-size:24px;color:rgba(109,78,232,0.45)"></i>
              </div>

              <!-- Stats row -->
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
                <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:12px 14px">
                  <p style="font-size:9px;color:var(--text-dim);text-transform:uppercase;letter-spacing:0.07em;margin:0 0 5px;font-weight:600">Views</p>
                  <p style="font-size:22px;font-weight:800;color:var(--text);margin:0;letter-spacing:-0.03em">{{ getPageStat(selectedPage.id, 'page_views') }}</p>
                </div>
                <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:12px 14px">
                  <p style="font-size:9px;color:var(--text-dim);text-transform:uppercase;letter-spacing:0.07em;margin:0 0 5px;font-weight:600">Clicks</p>
                  <p style="font-size:22px;font-weight:800;color:var(--text);margin:0;letter-spacing:-0.03em">{{ getPageStat(selectedPage.id, 'link_clicks') }}</p>
                </div>
                <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:12px 14px">
                  <p style="font-size:9px;color:var(--text-dim);text-transform:uppercase;letter-spacing:0.07em;margin:0 0 5px;font-weight:600">CTR</p>
                  <p :style="{fontSize:'22px',fontWeight:800,margin:0,letterSpacing:'-0.03em',color:getPageStat(selectedPage.id,'ctr')>10?'#4ade80':'var(--text)'}">{{ getPageStat(selectedPage.id, 'ctr') }}%</p>
                </div>
              </div>

              <!-- Clicks trend -->
              <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:14px;padding:16px 20px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                  <p style="font-size:13px;font-weight:600;color:var(--text);margin:0">Clicks Trend</p>
                  <span style="font-size:11px;color:var(--text-dim)">Last 7 days</span>
                </div>
                <svg viewBox="0 0 300 50" style="width:100%;height:44px">
                  <defs>
                    <linearGradient id="sparkGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#6D4EE8" stop-opacity="0.3"/>
                      <stop offset="100%" stop-color="#6D4EE8" stop-opacity="0"/>
                    </linearGradient>
                  </defs>
                  <path d="M0,40 L50,40 L100,38 L150,40 L200,37 L250,40 L300,38" fill="none" stroke="#6D4EE8" stroke-width="2" stroke-linecap="round"/>
                  <path d="M0,40 L50,40 L100,38 L150,40 L200,37 L250,40 L300,38 L300,50 L0,50Z" fill="url(#sparkGrad)"/>
                </svg>
              </div>
            </div>

            <!-- Right column -->
            <div style="display:flex;flex-direction:column;gap:10px">

              <!-- Quick Actions -->
              <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:14px;overflow:hidden">
                <p style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;padding:11px 16px;border-bottom:1px solid var(--border);margin:0">Quick Actions</p>
                <RouterLink :to="`/dashboard?link_id=${selectedPage.id}`" class="modal-action-btn" @click="selectedPage=null">
                  <i class="bi bi-graph-up-arrow" style="font-size:13px"></i> View Analytics
                </RouterLink>
                <button @click="copyLink(selectedPage.slug)" class="modal-action-btn">
                  <i class="bi bi-link-45deg" style="font-size:14px"></i>
                  {{ copiedLink === selectedPage.slug ? 'Copied!' : 'Copy Link' }}
                </button>
                <button @click="openPage(selectedPage.slug)" class="modal-action-btn">
                  <i class="bi bi-box-arrow-up-right" style="font-size:13px"></i> View Page
                </button>
                <RouterLink :to="`/pages/${selectedPage.id}/edit`" class="modal-action-btn" @click="selectedPage=null">
                  <i class="bi bi-pencil" style="font-size:13px"></i> Edit Link
                </RouterLink>
              </div>

              <!-- Management -->
              <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:14px;overflow:hidden">
                <p style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;padding:11px 16px;border-bottom:1px solid var(--border);margin:0">Management</p>
                <button @click="duplicatePage(selectedPage);selectedPage=null" class="modal-action-btn">
                  <i class="bi bi-copy" style="font-size:13px"></i> Duplicate
                </button>
                <RouterLink to="/dashboard/domains" class="modal-action-btn" @click="selectedPage=null">
                  <i class="bi bi-globe" style="font-size:13px"></i> Custom Domain
                </RouterLink>
              </div>

              <!-- Delete -->
              <button @click="deletePage(selectedPage.id);selectedPage=null" class="modal-delete-btn">
                <i class="bi bi-trash" style="font-size:13px"></i> Delete Link
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const theme = useThemeStore()
const pages = ref([])
const stats = ref({})
const loading = ref(true)
const period = ref(30)
const groups = ref([{ id: 'ungrouped', name: 'Ungrouped', open: true }])
const addingGroup = ref(false)
const newGroupName = ref('')
const newGroupInput = ref(null)
const selectedPage = ref(null)
const copiedLink = ref('')

// "Watch a live example" → opens a real public VSL page that showcases the product.
// Points at the live Karine page; keep that page published (don't delete/rename
// its slug) or this button 404s.
const DEMO_VSL_URL = 'https://mysocialvsl.com/p/karine-ZSnjk'
function watchExample() {
  window.open(DEMO_VSL_URL, '_blank')
}

function openModal(page) {
  selectedPage.value = page
}

function pagesInGroup(groupId) {
  if (groupId === 'ungrouped') {
    const customIds = new Set(groups.value.slice(1).map(g => g.id))
    return pages.value.filter(p => !p.group_id || !customIds.has(p.group_id))
  }
  return pages.value.filter(p => p.group_id === groupId)
}

watch(addingGroup, async (val) => {
  if (val) { await nextTick(); newGroupInput.value?.focus() }
})

function confirmAddGroup() {
  const name = newGroupName.value.trim()
  if (!name) return
  groups.value.push({ id: `group_${Date.now()}`, name, open: true })
  newGroupName.value = ''
  addingGroup.value = false
}

function removeGroup(index) {
  const g = groups.value[index]
  pages.value.forEach(p => { if (p.group_id === g.id) p.group_id = null })
  groups.value.splice(index, 1)
}

function getPageStat(pageId, key) {
  return stats.value[pageId]?.[key] ?? 0
}

async function toggleActive(page) {
  try {
    await api.patch(`/pages/${page.id}`, { is_active: !page.is_active })
    page.is_active = !page.is_active
    if (selectedPage.value?.id === page.id) {
      selectedPage.value = { ...page }
    }
  } catch {}
}

function copyLink(slug) {
  navigator.clipboard.writeText(`https://mysocialvsl.com/p/${slug}`)
  copiedLink.value = slug
  setTimeout(() => { copiedLink.value = '' }, 2000)
}

function openPage(slug) {
  window.open(`/p/${slug}`, '_blank')
}

async function setPeriod(p) {
  period.value = p
  await Promise.all(pages.value.map(async page => {
    try {
      const { data: s } = await api.get(`/pages/${page.id}/analytics?period=${p}`)
      stats.value[page.id] = s
    } catch {
      stats.value[page.id] = { page_views: 0, link_clicks: 0, age_confirmed: 0, ctr: 0 }
    }
  }))
}

const totalViews  = computed(() => Object.values(stats.value).reduce((s, p) => s + (p.page_views  || 0), 0))
const totalClicks = computed(() => Object.values(stats.value).reduce((s, p) => s + (p.link_clicks || 0), 0))
const avgCtr = computed(() => {
  const values = Object.values(stats.value).map(p => p.ctr || 0)
  if (!values.length) return 0
  return Math.round(values.reduce((s, v) => s + v, 0) / values.length * 10) / 10
})

onMounted(async () => {
  // Returning from Stripe checkout: refresh the cached user so the new plan shows
  if (route.query.upgraded) {
    await auth.fetchMe()
    router.replace({ query: {} })
  }
  try {
    const { data } = await api.get('/pages')
    pages.value = data
    await Promise.all(data.map(async page => {
      try {
        const { data: s } = await api.get(`/pages/${page.id}/analytics?period=${period.value}`)
        stats.value[page.id] = s
      } catch {
        stats.value[page.id] = { page_views: 0, link_clicks: 0, age_confirmed: 0, ctr: 0 }
      }
    }))
  } finally {
    loading.value = false
  }
})

async function deletePage(id) {
  if (!confirm('Delete this page?')) return
  await api.delete(`/pages/${id}`)
  pages.value = pages.value.filter(p => p.id !== id)
}

async function duplicatePage(page) {
  try {
    const { data } = await api.post('/pages', {
      model_name:    page.model_name + ' (copy)',
      model_handle:  page.model_handle,
      bio:           page.bio,
      page_type:     page.page_type,
      direct_url:    page.direct_url,
      video_url:     page.video_url,
      avatar_url:    page.avatar_url,
      bg_image_url:  page.bg_image_url,
      bg_color:      page.bg_color,
      btn_color:     page.btn_color,
      template:      page.template,
      verified_badge: page.verified_badge,
      show_avatar:   page.show_avatar,
      age_gate:      page.age_gate,
      online_status: page.online_status,
      location:      page.location,
      response_time: page.response_time,
      promo_text:    page.promo_text,
      bot_protection:       page.bot_protection,
      deep_link_enabled:    page.deep_link_enabled,
      strict_deep_link:     page.strict_deep_link,
      link_preview_enabled: page.link_preview_enabled,
      video_fit:     page.video_fit,
      overlay_opacity: page.overlay_opacity,
      card_position: page.card_position,
      vsl_enabled:   page.vsl_enabled,
      vsl_position:  page.vsl_position,
      popup_title:    page.popup_title,
      popup_subtitle: page.popup_subtitle,
      popup_text:     page.popup_text,
      popup_image_url: page.popup_image_url,
      popup_delay_seconds: page.popup_delay_seconds,
      // The actual fix: the CTA + extra links live in the links relation, not on
      // the page row. The old duplicate dropped them, so the copy had nothing to
      // redirect to. Carry them over.
      links: (page.links || []).map(l => ({
        type:      l.type,
        label:     l.label,
        title:     l.title,
        subtitle:  l.subtitle,
        url:       l.url,
        icon:      l.icon,
        image_url: l.image_url,
        meta:      l.meta,
        btn_color: l.btn_color,
        is_visible: l.is_visible,
      })),
    })
    pages.value.push(data)
    stats.value[data.id] = { page_views: 0, link_clicks: 0, age_confirmed: 0, ctr: 0 }
  } catch {}
}
</script>

<style scoped>
@keyframes spin { to { transform: rotate(360deg) } }

.kpi-card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 16px 18px;
  transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s, background 0.15s;
}
.kpi-card:hover {
  transform: translateY(-2px);
  border-color: rgba(109,78,232,0.35);
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}
.kpi-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.kpi-card-label { font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); }
.kpi-card-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 13px; }
.kpi-card-value { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; color: var(--text); margin: 0; }
.kpi-card-sub { font-size: 11px; color: var(--text-muted); margin: 6px 0 0; }

/* Empty-state feature cards */
.feat-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; padding: 18px 16px; text-align: left; transition: border-color 0.15s, background 0.15s, transform 0.15s; }
.feat-card:hover { border-color: rgba(109,78,232,0.3); transform: translateY(-2px); }
.feat-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 15px; margin-bottom: 12px; }
.feat-title { font-size: 13px; font-weight: 700; color: var(--text); margin: 0 0 5px; }
.feat-desc { font-size: 12px; color: var(--text-muted); margin: 0; line-height: 1.5; }

.modal-action-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 11px 16px;
  background: none;
  border: none;
  border-bottom: 1px solid var(--border-light);
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  color: var(--text2);
  text-align: left;
  font-family: Inter, sans-serif;
  text-decoration: none;
  transition: background 0.12s, color 0.12s;
}
.modal-action-btn:last-child { border-bottom: none; }
.modal-action-btn:hover { background: rgba(109,78,232,0.12); color: #A78BFA; }

.modal-delete-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 12px;
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.25);
  border-radius: 14px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  color: #ef4444;
  font-family: Inter, sans-serif;
  transition: all 0.15s;
}
.modal-delete-btn:hover { background: rgba(239,68,68,0.18); border-color: rgba(239,68,68,0.4); }

/* ── Mobile ── */
@media (max-width: 768px) {
  /* Empty-state feature cards + KPI cards stack instead of squishing */
  .dash-feat-grid { grid-template-columns: 1fr !important; }
  .dash-kpi-grid  { grid-template-columns: 1fr 1fr !important; }
  /* Page-row inline stats overflow the URL on phones — they're available on tap (modal) */
  .dash-row-stats { display: none !important; }
  /* Let the row wrap so badges + Active toggle drop to a second line instead of overlapping the URL */
  .dash-page-row  { flex-wrap: wrap; row-gap: 8px; }
}
</style>
