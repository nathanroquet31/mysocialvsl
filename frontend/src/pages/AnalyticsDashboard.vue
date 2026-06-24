<template>
  <DashboardLayout title="Dashboard" :pagesCount="pagesCount" :pagesLimit="pagesLimit">

    <!-- ── Top bar ────────────────────────────────────────────────────────── -->
    <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',flexWrap:'wrap',gap:'12px',marginBottom:'20px'}">
      <!-- Greeting -->
      <div>
        <h2 :style="{fontSize:'22px',fontWeight:700,color:textPrimary,margin:'0 0 2px',letterSpacing:'-0.02em'}">
          {{ greeting }}, {{ firstName }}
        </h2>
        <p :style="{fontSize:'13px',color:textMuted,margin:0}">Here's what's happening with your pages.</p>
      </div>

      <!-- Actions -->
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <!-- Links + Countries combined pill -->
        <div style="position:relative">
          <button @click="linksDropOpen=!linksDropOpen" class="adash-filter-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            {{ selectedLinkName }} · {{ filterCountry || 'All Countries' }}
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div v-if="linksDropOpen" class="adash-dropdown" style="width:260px;right:0;left:auto" @click.stop>
            <div style="padding:8px 8px 0">
              <input v-model="linkSearch" placeholder="Search pages…" class="adash-search-input" @click.stop />
            </div>
            <p :style="{fontSize:'10px',fontWeight:600,textTransform:'uppercase',letterSpacing:'0.08em',color:textMuted,padding:'10px 12px 4px',margin:0}">Links</p>
            <div style="max-height:160px;overflow-y:auto">
              <button class="adash-drop-item" :class="{active:!filterLinkId}" @click="filterLinkId=null;linksDropOpen=false">All links</button>
              <button v-for="p in filteredPages" :key="p.id" class="adash-drop-item"
                :class="{active:filterLinkId===p.id}" @click="filterLinkId=p.id;linksDropOpen=false">
                {{ p.model_name || p.name }}
              </button>
            </div>
            <div :style="{height:'1px',background:border,margin:'4px 0'}"></div>
            <p :style="{fontSize:'10px',fontWeight:600,textTransform:'uppercase',letterSpacing:'0.08em',color:textMuted,padding:'6px 12px 4px',margin:0}">Countries</p>
            <div style="max-height:140px;overflow-y:auto">
              <button class="adash-drop-item" :class="{active:!filterCountry}" @click="filterCountry='';linksDropOpen=false">All countries</button>
              <button v-for="(count, code) in data.by_country" :key="code" class="adash-drop-item"
                :class="{active:filterCountry===code}" @click="filterCountry=code;linksDropOpen=false">
                {{ countryLabel(code) }}
                <span :style="{marginLeft:'auto',fontSize:'11px',opacity:0.5}">{{ count }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Date range picker -->
        <DateRangePicker v-model="dateRange" :dark="theme.dark" @change="onDateChange" />

        <!-- Refresh -->
        <button @click="loadData" :style="{padding:'7px',borderRadius:'7px',border:`1px solid ${border}`,background:'transparent',cursor:'pointer',color:textMuted,display:'flex',alignItems:'center'}">
          <svg :style="{animation:loading?'adash-spin 1s linear infinite':''}" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.49"/></svg>
        </button>
      </div>
    </div>

    <!-- ── Overview (hero + metric grid, CRM-style) ───────────────────────── -->
    <div class="adash-overview">
      <div class="adash-ov-head">
        <h3 class="adash-card-title" style="margin:0">Analytics overview</h3>
        <span :style="{fontSize:'12px',color:textMuted}">{{ dateRangeLabel }}</span>
      </div>

      <div class="adash-ov-body">
        <!-- Hero: total views -->
        <div class="adash-ov-hero">
          <div class="adash-ov-hero-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </div>
          <p class="adash-ov-hero-label">Total views</p>
          <p class="adash-ov-hero-value">{{ formatNum(data.page_views) }}</p>
          <p class="adash-ov-hero-sub" :title="'Real people — the same visitor returning is counted once'">
            {{ formatNum(data.unique_visitors) }} unique visitor{{ data.unique_visitors === 1 ? '' : 's' }}
          </p>
        </div>

        <!-- Column 1 -->
        <div class="adash-ov-col">
          <div class="adash-ov-cell">
            <div><p class="adash-ov-val">{{ formatNum(data.visits_with_clicks) }}</p><p class="adash-ov-lbl">Clicks</p></div>
            <div class="adash-ov-icon" style="background:rgba(234,88,12,0.14)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fb923c" stroke-width="2" stroke-linecap="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
          </div>
          <div class="adash-ov-cell">
            <div>
              <p class="adash-ov-val">{{ data.ctr }}%</p>
              <p class="adash-ov-lbl">Click-through rate</p>
              <!-- The fair VSL read: of people who actually watched the video, how
                   many clicked. Raw CTR above is diluted by drive-by visitors who
                   never engaged — not comparable to a link-list page. -->
              <p v-if="data.vsl?.watchers" :title="`${data.vsl.engaged_clicks} of ${data.vsl.watchers} video watchers clicked`"
                :style="{margin:'4px 0 0',fontSize:'11px',fontWeight:600,color:theme.dark?'#A78BFA':'#6D4EE8'}">
                {{ data.vsl.engaged_ctr }}% of watchers clicked
              </p>
            </div>
            <div class="adash-ov-icon" style="background:rgba(22,163,74,0.14)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
          </div>
        </div>

        <!-- Column 2 -->
        <div class="adash-ov-col">
          <div class="adash-ov-cell">
            <div><p class="adash-ov-val">{{ pagesCount }}</p><p class="adash-ov-lbl">Active pages</p></div>
            <div class="adash-ov-icon" style="background:rgba(109,78,232,0.15)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></div>
          </div>
          <div class="adash-ov-cell">
            <div><p class="adash-ov-val">{{ countriesCount }}</p><p class="adash-ov-lbl">Countries</p></div>
            <div class="adash-ov-icon" style="background:rgba(8,145,178,0.15)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
          </div>
        </div>

        <!-- Column 3 -->
        <div class="adash-ov-col">
          <div class="adash-ov-cell">
            <div><p class="adash-ov-val">{{ data.live?.visitors_now ?? 0 }}</p><p class="adash-ov-lbl">Visitors now</p></div>
            <div class="adash-ov-icon" style="background:rgba(16,185,129,0.14)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"/></svg></div>
          </div>
          <div class="adash-ov-cell">
            <div><p class="adash-ov-val">{{ topCountries.length ? countryLabel(topCountries[0].code) : '—' }}</p><p class="adash-ov-lbl">Top country</p></div>
            <div class="adash-ov-icon" style="background:rgba(236,72,153,0.14)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f472b6" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Performance chart ──────────────────────────────────────────────── -->
    <div class="adash-card" :style="{marginBottom:'20px'}">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px">
        <div>
          <h3 class="adash-card-title">Performance</h3>
          <p class="adash-card-sub">Track your performance over time</p>
        </div>
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
          <div class="adash-toggle-group">
            <button v-for="m in chartMetrics" :key="m.value" @click="chartMetric=m.value" class="adash-toggle-btn" :class="{active:chartMetric===m.value}">{{ m.label }}</button>
          </div>
          <div class="adash-toggle-group">
            <button v-for="b in chartBreakdowns" :key="b.value" @click="chartBreakdown=b.value" class="adash-toggle-btn" :class="{active:chartBreakdown===b.value}">{{ b.label }}</button>
          </div>
          <div class="adash-toggle-group">
            <button @click="chartType='line'" class="adash-toggle-btn" :class="{active:chartType==='line'}" title="Line">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </button>
            <button @click="chartType='area'" class="adash-toggle-btn" :class="{active:chartType==='area'}" title="Area">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
            </button>
            <button @click="chartType='bar'" class="adash-toggle-btn" :class="{active:chartType==='bar'}" title="Bar">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="18" y="3" width="4" height="18"/><rect x="10" y="8" width="4" height="13"/><rect x="2" y="13" width="4" height="8"/></svg>
            </button>
          </div>
        </div>
      </div>
      <div :style="{height:'220px',position:'relative'}">
        <canvas ref="chartCanvas" style="width:100%;height:100%"></canvas>
        <div v-if="!data.series?.labels?.length" class="adash-empty-chart">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"><rect x="18" y="3" width="4" height="18"/><rect x="10" y="8" width="4" height="13"/><rect x="2" y="13" width="4" height="8"/></svg>
          <p style="margin:8px 0 0;font-size:13px;color:#9CA3AF">No data available</p>
        </div>
      </div>
    </div>

    <!-- ── Page-specific banner (when filtered to a single link) ─────────── -->
    <div v-if="selectedPageInfo" :style="{background:theme.dark?'rgba(109,78,232,0.1)':'#F3F0FF',border:`1px solid ${theme.dark?'rgba(109,78,232,0.35)':'#DDD6FE'}`,borderRadius:'14px',padding:'14px 20px',marginBottom:'20px',display:'flex',alignItems:'center',gap:'16px',flexWrap:'wrap'}">
      <div :style="{width:'36px',height:'36px',borderRadius:'10px',background:'linear-gradient(135deg,#6D4EE8,#A78BFA)',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
        <span :style="{color:'#fff',fontWeight:800,fontSize:'13px'}">{{ (selectedPageInfo.model_name||'P')[0].toUpperCase() }}</span>
      </div>
      <div style="flex:1;min-width:0">
        <p :style="{fontSize:'13px',fontWeight:700,color:theme.dark?'#c4b5fd':'#5B21B6',margin:'0 0 2px'}">{{ selectedPageInfo.model_name }}</p>
        <p :style="{fontSize:'12px',color:theme.dark?'rgba(196,181,253,0.7)':'#7C3AED',margin:0,fontFamily:'monospace'}">mysocialvsl.com/{{ selectedPageInfo.slug }}</p>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
        <span :style="{fontSize:'11px',color:theme.dark?'rgba(196,181,253,0.6)':'#7C3AED',fontStyle:'italic'}">Share this link to get data →</span>
        <button @click="copyPageUrl(selectedPageInfo.slug)"
          :style="{padding:'6px 14px',borderRadius:'8px',border:'none',cursor:'pointer',fontSize:'12px',fontWeight:600,fontFamily:'Inter,sans-serif',background:copiedPageUrl?'#16a34a':'#6D4EE8',color:'#fff',transition:'background 0.2s',flexShrink:0}">
          {{ copiedPageUrl ? '✓ Copied!' : 'Copy Link' }}
        </button>
        <button @click="filterLinkId=null"
          :style="{padding:'6px 10px',borderRadius:'8px',border:`1px solid ${theme.dark?'rgba(109,78,232,0.4)':'#DDD6FE'}`,cursor:'pointer',fontSize:'12px',color:theme.dark?'rgba(196,181,253,0.6)':'#7C3AED',background:'transparent',fontFamily:'Inter,sans-serif',flexShrink:0}">
          All pages
        </button>
      </div>
    </div>

    <!-- ── Audience Retention Curve (Pro/Agency) ──────────────────────────── -->
    <div class="adash-card" :style="{marginBottom:'20px',position:'relative'}">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px">
        <div>
          <h3 class="adash-card-title" style="margin:0">
            Audience retention
            <span :style="{fontSize:'10px',fontWeight:700,color:'#A78BFA',background:theme.dark?'rgba(109,78,232,0.2)':'#EEE9FF',borderRadius:'999px',padding:'2px 8px',marginLeft:'6px',verticalAlign:'middle'}">PRO</span>
          </h3>
          <p class="adash-card-sub">% of viewers still watching, second by second — one line per video. The first seconds tell you if the hook lands.</p>
        </div>
        <div v-if="isPaid && retentionTotalPlays" style="text-align:right">
          <p :style="{margin:0,fontSize:'18px',fontWeight:700,color:theme.dark?'#fff':'#111827'}">{{ retentionTotalPlays }}</p>
          <p :style="{margin:0,fontSize:'11px',color:textMuted}">video plays analysed</p>
        </div>
      </div>

      <!-- Locked state for Free -->
      <div v-if="!isPaid" :style="{textAlign:'center',padding:'40px 16px'}">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="1.6" stroke-linecap="round" style="margin-bottom:10px"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <p :style="{fontSize:'14px',fontWeight:700,color:textPrimary,margin:'0 0 4px'}">Retention curve is a Pro feature</p>
        <p :style="{fontSize:'12px',color:textMuted,margin:'0 0 14px'}">See second by second when your audience drops off and converts.</p>
        <router-link to="/billing" :style="{display:'inline-block',padding:'8px 18px',borderRadius:'9px',background:'#6D4EE8',color:'#fff',fontSize:'13px',fontWeight:600,textDecoration:'none'}">Upgrade to Pro</router-link>
      </div>

      <!-- Empty state (paid, no data yet) -->
      <div v-else-if="!retentionPages.length" :style="{textAlign:'center',padding:'40px 16px'}">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round" style="margin-bottom:8px"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        <p :style="{fontSize:'13px',color:textMuted,margin:'0 0 4px',fontWeight:600}">No watch data yet</p>
        <p :style="{fontSize:'12px',color:textMuted,margin:0,opacity:0.7}">Once people watch your VSL, the retention curve appears here.</p>
      </div>

      <div v-else>
        <div :style="{height:'260px',position:'relative'}">
          <canvas ref="retentionCanvas" style="width:100%;height:100%"></canvas>
        </div>
        <!-- Per-video legend -->
        <div :style="{display:'flex',gap:'14px',flexWrap:'wrap',marginTop:'12px'}">
          <span v-for="p in retentionPages" :key="p.id" @click="toggleRetentionLine(p.id)" :title="selectedRetentionId === p.id ? 'Show all videos' : 'Show only this video'" :style="{display:'flex',alignItems:'center',gap:'6px',fontSize:'11px',color:textSecondary,cursor:'pointer',opacity: selectedRetentionId && selectedRetentionId !== p.id ? 0.4 : 1,transition:'opacity 0.15s'}">
            <span :style="{display:'inline-block',width:'12px',height:'3px',borderRadius:'2px',background:retentionColor(p.id)}"></span>
            {{ p.name }}
            <span :style="{color:textMuted}">· {{ p.plays }} plays</span>
            <span v-if="retentionPeakClick(p) != null" :style="{color:'#16A34A',fontWeight:600}">· clic ~{{ retentionPeakClick(p) }}s</span>
          </span>
          <span v-if="retentionPages.length === 1" :style="{display:'flex',alignItems:'center',gap:'6px',fontSize:'11px',color:textMuted}">
            <span :style="{display:'inline-block',width:'10px',height:'10px',borderRadius:'2px',background:'#16A34A'}"></span> clicks per second
          </span>
        </div>
        <p v-if="retentionPages.length > 1" :style="{margin:'8px 0 0',fontSize:'10px',color:textMuted}">
          {{ selectedRetentionId ? 'Showing one video — click it again in the legend to show all.' : 'Tip: click a video in the legend to isolate its line.' }}
        </p>
        <p v-if="retentionInsight" :style="{margin:'10px 0 0',fontSize:'11px',color:textMuted}"><i class="bi bi-lightbulb"></i> {{ retentionInsight }}</p>
      </div>
    </div>

    <!-- ── Top Links + Goal ─────────────────────────────────────────────── -->
    <div :style="{marginBottom:'20px'}">
      <div class="adash-card">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
          <i class="bi bi-trophy-fill" :style="{fontSize:'16px',color:'#F5A623'}"></i>
          <h3 class="adash-card-title" style="margin:0">Top links</h3>
        </div>
        <div v-if="!data.top_links?.length" :style="{textAlign:'center',padding:'32px 16px'}">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round" style="margin-bottom:8px"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><path d="M6 15H4.5a2.5 2.5 0 0 0 0 5H6"/><path d="M18 15h1.5a2.5 2.5 0 0 1 0 5H18"/></svg>
          <p :style="{fontSize:'13px',color:textMuted,margin:'0 0 4px',fontWeight:600}">No top links yet</p>
          <p :style="{fontSize:'12px',color:textMuted,margin:0,opacity:0.7}">Your most clicked pages will appear here once they start getting traffic.</p>
        </div>
        <div v-else style="display:flex;flex-direction:column;gap:6px">
          <div v-for="(link,i) in data.top_links" :key="link.id"
            class="adash-toplink" :class="{'adash-toplink--first': i===0}">
            <span class="adash-rank" :style="rankStyle(i)">{{ i+1 }}</span>
            <div class="adash-tl-avatar">{{ (link.name||'?')[0].toUpperCase() }}</div>
            <div style="flex:1;min-width:0">
              <p :style="{fontSize:'13px',fontWeight:700,color:textPrimary,margin:'0 0 1px',overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap'}">{{ link.name }}</p>
              <p :style="{fontSize:'11px',color:textMuted,margin:0,fontFamily:'monospace',overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap'}">mysocialvsl.com/{{ link.slug }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:14px;flex-shrink:0">
              <div style="text-align:right">
                <p :style="{fontSize:'14px',fontWeight:800,color:textPrimary,margin:0,letterSpacing:'-0.02em'}">{{ formatNum(link.views) }}</p>
                <p :style="{fontSize:'10px',color:textMuted,margin:0,textTransform:'uppercase',letterSpacing:'0.05em'}">views</p>
              </div>
              <span :style="{minWidth:'48px',textAlign:'center',padding:'5px 9px',borderRadius:'999px',fontSize:'12px',fontWeight:700,background: link.ctr>30 ? (theme.dark?'rgba(109,78,232,0.22)':'#EEE9FF') : (theme.dark?'rgba(255,255,255,0.06)':'#F3F4F6'), color: link.ctr>30 ? (theme.dark?'#A78BFA':'#6D4EE8') : textMuted}">{{ link.ctr }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Live Globe ────────────────────────────────────────────────────── -->
    <div class="adash-card" style="display:flex;gap:0;padding:0;overflow:hidden;margin-bottom:20px">

      <!-- Left: rotating globe -->
      <div :style="{
        width:'360px', flexShrink:0, position:'relative',
        background:'#050410',
        borderRight:`1px solid ${border}`,
        display:'flex', alignItems:'center', justifyContent:'center',
        padding:'12px',
      }">
        <GlobeViz :byCountry="data.by_country || {}" :liveEvents="data.live?.events || []" />

        <!-- LIVE badge -->
        <div style="position:absolute;top:12px;left:12px;display:flex;align-items:center;gap:5px;background:rgba(0,0,0,0.65);border:1px solid rgba(255,255,255,0.12);border-radius:999px;padding:4px 10px;backdrop-filter:blur(8px);z-index:10">
          <span class="adash-live-dot" style="margin:0"></span>
          <span style="font-size:10px;font-weight:700;color:#fff;letter-spacing:0.06em">LIVE</span>
        </div>

        <!-- Visitors now pill — bottom center -->
        <div style="position:absolute;bottom:14px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.7);border:1px solid rgba(109,78,232,0.4);border-radius:999px;padding:6px 20px;backdrop-filter:blur(10px);white-space:nowrap;text-align:center;z-index:10">
          <p style="font-size:20px;font-weight:800;color:#fff;margin:0;letter-spacing:-0.04em;line-height:1">{{ data.live?.visitors_now ?? 0 }}</p>
          <p style="font-size:9px;color:rgba(255,255,255,0.45);margin:2px 0 0;letter-spacing:0.08em;text-transform:uppercase">visitors now</p>
        </div>
      </div>

      <!-- Right: stats + countries + feed -->
      <div style="flex:1;padding:22px 24px;display:flex;flex-direction:column;gap:18px;min-width:0">

        <!-- Header -->
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div>
            <h3 class="adash-card-title" style="margin:0 0 3px">Live Analytics</h3>
            <p :style="{fontSize:'12px',color:textMuted,margin:0}">
              {{ topCountries.length }} countries · updated every 8s
            </p>
          </div>
          <span style="font-size:11px;color:#16A34A;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:999px;padding:3px 10px;font-weight:600">
            ● Active
          </span>
        </div>

        <!-- 3 KPI cells -->
        <div :style="{display:'grid',gridTemplateColumns:'1fr 1fr 1fr',gap:'1px',background:border,borderRadius:'10px',overflow:'hidden'}">
          <div class="adash-live-cell">
            <p :style="{fontSize:'9px',fontWeight:600,color:textMuted,margin:'0 0 4px',textTransform:'uppercase',letterSpacing:'0.06em'}">Views / 30m</p>
            <p class="adash-live-num" style="font-size:22px">{{ data.live?.views_30m ?? 0 }}</p>
          </div>
          <div class="adash-live-cell">
            <p :style="{fontSize:'9px',fontWeight:600,color:textMuted,margin:'0 0 4px',textTransform:'uppercase',letterSpacing:'0.06em'}">Clicks / 30m</p>
            <p class="adash-live-num" style="font-size:22px">{{ data.live?.clicks_30m ?? 0 }}</p>
          </div>
          <div class="adash-live-cell">
            <p :style="{fontSize:'9px',fontWeight:600,color:textMuted,margin:'0 0 4px',textTransform:'uppercase',letterSpacing:'0.06em'}">Click Rate</p>
            <p class="adash-live-num" style="font-size:22px">{{ liveCtr }}%</p>
          </div>
        </div>

        <!-- Watching right now (live presence) — Pro/Agency -->
        <div>
          <p :style="{fontSize:'10px',fontWeight:700,color:textMuted,textTransform:'uppercase',letterSpacing:'0.08em',margin:'0 0 8px'}">
            On the page right now
            <span v-if="isPaid && data.live?.visitors?.length" :style="{color:'#16A34A',marginLeft:'6px'}">{{ data.live.visitors_now }} live</span>
            <span v-else-if="!isPaid" :style="{fontSize:'9px',color:'#A78BFA',background:theme.dark?'rgba(109,78,232,0.2)':'#EEE9FF',borderRadius:'999px',padding:'1px 6px',marginLeft:'6px'}">PRO</span>
          </p>
          <div v-if="!isPaid" :style="{fontSize:'12px',color:textMuted}">
            <router-link to="/billing" :style="{color:'#A78BFA',textDecoration:'none',fontWeight:600}">Upgrade</router-link> to see who's on your pages live.
          </div>
          <div v-else-if="!data.live?.visitors?.length" :style="{fontSize:'12px',color:textMuted}">Nobody on your pages right now</div>
          <div v-else style="display:flex;flex-wrap:wrap;gap:6px">
            <div v-for="(v,i) in data.live.visitors.slice(0,12)" :key="i"
              :style="{display:'flex',alignItems:'center',gap:'6px',padding:'4px 9px',borderRadius:'999px',background:hoverBg,fontSize:'11px',color:textSecondary}">
              <span class="adash-live-dot" style="margin:0;width:6px;height:6px"></span>
              <span :style="{color:textMuted}">{{ countryLabel(v.country) }}</span>
              <i :class="v.device === 'mobile' ? 'bi bi-phone' : 'bi bi-laptop'" :style="{color:textMuted}"></i>
              <span :style="{color:textMuted,fontFamily:'monospace'}">{{ formatDuration(v.seconds) }}</span>
            </div>
          </div>
        </div>

        <!-- Countries + Event feed -->
        <div class="adash-charts" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;flex:1;min-height:0">

          <!-- Top countries -->
          <div>
            <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',gap:'8px',margin:'0 0 10px'}">
              <p :style="{fontSize:'10px',fontWeight:700,color:textMuted,textTransform:'uppercase',letterSpacing:'0.08em',margin:0}">
                Top Countries
                <span v-if="topCountries.length" :style="{color:'#16A34A',marginLeft:'6px'}">
                  {{ formatNum(Object.values(data.by_country||{}).reduce((a,b)=>a+b,0)) }} visits
                </span>
              </p>
              <div :style="{display:'inline-flex',gap:'2px',padding:'2px',borderRadius:'7px',background:hoverBg,border:`1px solid ${dividerBg}`,flexShrink:0}">
                <button @click="countryMode='code'" :style="ctyTab('code')">Code</button>
                <button @click="countryMode='name'" :style="ctyTab('name')">Name</button>
              </div>
            </div>
            <div v-if="!topCountries.length" :style="{fontSize:'12px',color:textMuted}">No data yet</div>
            <div v-for="item in topCountries.slice(0,7)" :key="item.code" :style="{display:'flex',alignItems:'center',gap:'7px',marginBottom:'7px'}">
              <span :style="{fontSize:'12px',fontWeight:600,color:textSecondary,minWidth:'28px',maxWidth:'130px',whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',flexShrink:0}">{{ countryLabel(item.code) }}</span>
              <div :style="{flex:1,height:'4px',background:dividerBg,borderRadius:'999px',overflow:'hidden'}">
                <div :style="{height:'100%',width:item.pct+'%',background:'linear-gradient(90deg,#6D4EE8,#A78BFA)',borderRadius:'999px',transition:'width 0.6s ease'}"></div>
              </div>
              <span :style="{fontSize:'10px',color:textMuted,width:'28px',textAlign:'right',flexShrink:0}">{{ item.pct }}%</span>
            </div>
          </div>

          <!-- Event feed -->
          <div style="overflow:hidden;display:flex;flex-direction:column">
            <p :style="{fontSize:'10px',fontWeight:700,color:textMuted,textTransform:'uppercase',letterSpacing:'0.08em',margin:'0 0 10px'}">Event Feed</p>
            <div v-if="!data.live?.events?.length" :style="{fontSize:'12px',color:textMuted}">No active events</div>
            <div v-else style="display:flex;flex-direction:column;gap:3px;overflow-y:auto;flex:1">
              <div v-for="(ev,i) in [...(data.live.events)].reverse().slice(0,14)" :key="i"
                :style="{display:'flex',alignItems:'center',gap:'6px',padding:'4px 7px',borderRadius:'6px',background:hoverBg,fontSize:'11px',flexShrink:0}">
                <i :class="eventIcon(ev.type)" :style="{color:eventColor(ev.type),flexShrink:0}"></i>
                <span :style="{flex:1,color:textSecondary,overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap'}">{{ eventLabel(ev.type) }}</span>
                <span v-if="ev.country" :style="{flexShrink:0,color:textMuted,fontSize:'10px'}">{{ countryLabel(ev.country) }}</span>
                <i :class="ev.device === 'mobile' ? 'bi bi-phone' : 'bi bi-laptop'" :style="{color:textMuted,flexShrink:0}"></i>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import { useRoute } from 'vue-router'
import api from '@/lib/axios'
import DashboardLayout from '@/components/DashboardLayout.vue'
import GlobeViz from '@/components/GlobeViz.vue'
import DateRangePicker from '@/components/DateRangePicker.vue'
import {
  Chart, LineController, BarController,
  LineElement, BarElement, PointElement,
  LinearScale, CategoryScale, Filler, Tooltip,
} from 'chart.js'

Chart.register(LineController, BarController, LineElement, BarElement, PointElement, LinearScale, CategoryScale, Filler, Tooltip)

const auth  = useAuthStore()
const theme = useThemeStore()
const route = useRoute()

// ── Theme tokens ───────────────────────────────────────────────────────────
const textPrimary   = computed(() => theme.dark ? '#fff' : '#111827')
const textSecondary = computed(() => theme.dark ? 'rgba(255,255,255,0.6)' : '#4B5563')
const textMuted     = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
const border        = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const hoverBg       = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6')
const dividerBg     = computed(() => theme.dark ? 'rgba(255,255,255,0.07)' : '#F3F4F6')

// ── State ─────────────────────────────────────────────────────────────────
const loading    = ref(true)
const pagesCount = ref(0)
const pagesLimit = computed(() => auth.user?.plan === 'agency' ? 999 : auth.user?.plan === 'pro' ? 5 : 1)
const data       = ref({
  page_views: 0, visits_with_clicks: 0, ctr: 0,
  unique_visitors: 0, unique_clicks: 0, unique_ctr: 0,
  series: { labels: [], views: [], clicks: [] },
  top_links: [], by_country: {}, by_device: {}, by_referrer: {},
  hourly: Array(24).fill(0),
  live: { visitors_now: 0, views_30m: 0, clicks_30m: 0, top_country: null, events: [], visitors: [] },
  per_link: [], pages: [],
  is_paid: false,
  retention: null,
  vsl: { plays: 0, play_rate: 0, milestones: {}, avg_watch_before_click: null, avg_time_on_page: null, watchers: 0, engaged_clicks: 0, engaged_ctr: 0 },
})

const isPaid = computed(() => data.value.is_paid || auth.user?.plan === 'pro' || auth.user?.plan === 'agency' || auth.user?.is_admin)

// ── Filters ───────────────────────────────────────────────────────────────
const dateRange      = ref({ preset: '30d', start: null, end: null })
const filterLinkId   = ref(null)
const filterCountry  = ref('')
const linksDropOpen  = ref(false)
const linkSearch     = ref('')
const geoView        = ref('globe')

// ── Chart ─────────────────────────────────────────────────────────────────
const chartCanvas    = ref(null)
const chartMetric    = ref('views')
const chartType      = ref('area')
const chartBreakdown = ref('total')
const chartMetrics   = [{ label: 'Page views', value: 'views' }, { label: 'Clicks', value: 'clicks' }]
const chartBreakdowns = [{ label: 'Total', value: 'total' }, { label: 'By link', value: 'links' }, { label: 'By country', value: 'country' }]
let chartInstance    = null

// ── Retention curve (one line per video, per second) ─────────────────────────
const retentionCanvas = ref(null)
let retentionChart    = null
const retentionPages  = computed(() => data.value.retention?.pages || [])
const retentionTotalPlays = computed(() => retentionPages.value.reduce((s, p) => s + (p.plays || 0), 0))
// Click a legend item to isolate its line (null = show every video).
const selectedRetentionId = ref(null)
function toggleRetentionLine(id) { selectedRetentionId.value = selectedRetentionId.value === id ? null : id }

// Distinct, stable colour per video line (light blue / red / green first, à la Lucas).
const RETENTION_COLORS = ['#38BDF8', '#EF4444', '#22C55E', '#A855F7', '#F59E0B', '#EC4899']
function retentionColor(pageId) {
  const idx = retentionPages.value.findIndex(p => p.id === pageId)
  return RETENTION_COLORS[(idx < 0 ? 0 : idx) % RETENTION_COLORS.length]
}

// The second where this video gets the most clicks — "when the fan clicked".
function retentionPeakClick(page) {
  let bestSec = null, best = 0
  for (const pt of page.points || []) {
    if ((pt.clicks || 0) > best) { best = pt.clicks; bestSec = pt.sec }
  }
  return bestSec
}

// Plain-language takeaway, only when a single video is shown (otherwise the lines
// speak for themselves): hook strength at 3s, biggest drop, best converting second.
const retentionInsight = computed(() => {
  if (retentionPages.value.length !== 1) return ''
  const pts = retentionPages.value[0].points || []
  if (pts.length < 2) return ''
  const at = (s) => pts.find(p => p.sec === s)
  const parts = []
  const hook = at(3)
  if (hook) parts.push(`${hook.pct}% still watching at 3s`)
  let maxDrop = 0, dropAt = 0
  for (let i = 1; i < pts.length; i++) {
    const d = pts[i - 1].pct - pts[i].pct
    if (d > maxDrop) { maxDrop = d; dropAt = pts[i].sec }
  }
  if (maxDrop >= 5) parts.push(`biggest drop at ${dropAt}s`)
  const bestClick = [...pts].sort((a, b) => (b.clicks || 0) - (a.clicks || 0))[0]
  if (bestClick && bestClick.clicks > 0) parts.push(`most clicks around ${bestClick.sec}s`)
  return parts.join(' · ')
})

// ── Greeting ──────────────────────────────────────────────────────────────
const firstName = computed(() => auth.user?.name?.split(' ')[0] || 'Creator')
const greeting  = computed(() => {
  const h = new Date().getHours()
  return h < 12 ? 'Good morning' : h < 18 ? 'Good afternoon' : 'Good evening'
})

// ── Date label ─────────────────────────────────────────────────────────────
const DATE_LABELS = { today:'Today', yesterday:'Yesterday', '24h':'Last 24 Hours', '7d':'Last 7 Days', '30d':'Last 30 Days', '90d':'Last 90 Days', '6m':'Last 6 Months', '12m':'Last 12 Months', all:'All Time', custom:'Custom Range' }
const dateRangeLabel = computed(() => DATE_LABELS[dateRange.value.preset] || 'Selected period')

// ── Derived ───────────────────────────────────────────────────────────────
const filteredPages = computed(() => {
  const q = linkSearch.value.toLowerCase()
  const pages = data.value.pages || []
  return q ? pages.filter(p => (p.model_name || p.name || '').toLowerCase().includes(q)) : pages
})

const selectedLinkName = computed(() => {
  if (!filterLinkId.value) return 'All Links'
  const p = (data.value.pages || []).find(p => p.id === filterLinkId.value)
  return p ? (p.model_name || p.name) : 'All Links'
})

const selectedPageInfo = computed(() => {
  if (!filterLinkId.value) return null
  return (data.value.pages || []).find(p => p.id === filterLinkId.value) || null
})

const copiedPageUrl = ref(false)
function copyPageUrl(slug) {
  navigator.clipboard.writeText(`https://mysocialvsl.com/${slug}`)
  copiedPageUrl.value = true
  setTimeout(() => { copiedPageUrl.value = false }, 2000)
}

// Rank medals for the Top links list (gold / silver / bronze, then muted)
const RANK_COLORS = [
  { bg: 'linear-gradient(135deg,#FFD66B,#F5A623)', color: '#5c3d00' },
  { bg: 'linear-gradient(135deg,#DDE3EE,#AEB6C2)', color: '#2c333d' },
  { bg: 'linear-gradient(135deg,#E8A87C,#C26B3F)', color: '#3d1e0a' },
]
function rankStyle(i) {
  const r = RANK_COLORS[i]
  if (r) return { background: r.bg, color: r.color }
  return { background: theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6', color: textMuted.value }
}

const countriesCount = computed(() => Object.keys(data.value.by_country || {}).length)

const topCountries = computed(() => {
  const bc = data.value.by_country || {}
  const total = Object.values(bc).reduce((s, v) => s + v, 0) || 1
  return Object.entries(bc).sort(([,a],[,b]) => b-a).slice(0,5)
    .map(([code, count]) => ({ code, count, pct: Math.round(count / total * 100) }))
})

const liveCtr = computed(() => {
  const v = data.value.live?.views_30m || 0
  const c = data.value.live?.clicks_30m || 0
  return v > 0 ? Math.round(c/v*100) : 0
})

// (country coords + globe markers now handled inside GlobeViz.vue)

// ── Load data ─────────────────────────────────────────────────────────────
async function loadData() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    params.set('preset', dateRange.value.preset)
    if (dateRange.value.preset === 'custom' && dateRange.value.start) params.set('custom_start', dateRange.value.start)
    if (dateRange.value.preset === 'custom' && dateRange.value.end)   params.set('custom_end',   dateRange.value.end)
    if (filterLinkId.value)  params.set('link_ids[]', filterLinkId.value)
    if (filterCountry.value) params.set('country', filterCountry.value)

    const { data: d } = await api.get('/analytics/dashboard?' + params)
    // Preserve pages list loaded separately in onMounted if analytics response has none
    const prevPages = data.value.pages
    data.value = d
    if (!d.pages?.length && prevPages?.length) data.value.pages = prevPages
    pagesCount.value = data.value.pages?.length ?? 0
  } catch (e) { console.error(e) }
  finally {
    loading.value = false
    await nextTick()
    renderChart()
    renderRetention()
  }
}

function onDateChange(val) {
  dateRange.value = val
  loadData()
}

// ── Chart ─────────────────────────────────────────────────────────────────
function renderChart() {
  if (!chartCanvas.value) return
  if (chartInstance) { chartInstance.destroy(); chartInstance = null }
  const series = data.value.series || {}
  const labels = series.labels || []
  const values = chartMetric.value === 'views' ? (series.views || []) : (series.clicks || [])
  if (!labels.length) return
  const isDark = theme.dark
  const ctx    = chartCanvas.value.getContext('2d')
  const grad   = ctx.createLinearGradient(0, 0, 0, 220)
  grad.addColorStop(0, 'rgba(109,78,232,0.35)')
  grad.addColorStop(1, 'rgba(109,78,232,0)')

  const isBar = chartType.value === 'bar'
  chartInstance = new Chart(ctx, {
    type: isBar ? 'bar' : 'line',
    data: {
      labels,
      datasets: [{
        label: chartMetric.value === 'views' ? 'Page views' : 'Clicks',
        data: values,
        borderColor: '#6D4EE8',
        backgroundColor: isBar ? 'rgba(109,78,232,0.7)' : grad,
        borderWidth: isBar ? 0 : 2.5,
        pointRadius: labels.length > 30 ? 0 : 3,
        pointBackgroundColor: '#6D4EE8',
        tension: 0.4,
        fill: !isBar,
        borderRadius: isBar ? 4 : 0,
        borderSkipped: false,
      }],
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: isDark ? '#1a1733' : '#fff',
          titleColor: isDark ? '#fff' : '#111827',
          bodyColor: isDark ? 'rgba(255,255,255,0.6)' : '#6B7280',
          borderColor: isDark ? 'rgba(255,255,255,0.1)' : '#E5E7EB',
          borderWidth: 1, padding: 10, cornerRadius: 8,
        },
      },
      scales: {
        x: { grid:{ color: isDark?'rgba(255,255,255,0.04)':'rgba(0,0,0,0.04)' }, ticks:{ color: isDark?'rgba(255,255,255,0.3)':'#9CA3AF', font:{ size:10, family:'Inter,sans-serif' }, maxTicksLimit:10 }, border:{ display:false } },
        y: { grid:{ color: isDark?'rgba(255,255,255,0.04)':'rgba(0,0,0,0.04)' }, ticks:{ color: isDark?'rgba(255,255,255,0.3)':'#9CA3AF', font:{ size:10, family:'Inter,sans-serif' } }, border:{ display:false }, beginAtZero:true },
      },
    },
  })
}

// ── Retention chart — one line per video, per-second X axis ───────────────────
function renderRetention() {
  if (retentionChart) { retentionChart.destroy(); retentionChart = null }
  if (!retentionCanvas.value) return
  const all = retentionPages.value
  // Isolate one video if selected (and the id still exists), else show all.
  const sel = selectedRetentionId.value && all.some(p => p.id === selectedRetentionId.value) ? selectedRetentionId.value : null
  const pages = sel ? all.filter(p => p.id === sel) : all
  if (!pages.length) return

  const isDark = theme.dark
  const single = pages.length === 1

  // Shared per-second X axis spanning the longest video; each line stops at its
  // own end (null beyond), so different video lengths coexist on one axis.
  const maxSec = Math.max(...pages.map(p => p.points.length ? p.points[p.points.length - 1].sec : 0))
  const labels = Array.from({ length: maxSec + 1 }, (_, s) => s + 's')

  const datasets = pages.map((p) => {
    const color = retentionColor(p.id)
    const arr = new Array(maxSec + 1).fill(null)
    for (const pt of p.points) arr[pt.sec] = pt.pct
    return {
      type: 'line', label: p.name, data: arr, yAxisID: 'y',
      borderColor: color, backgroundColor: color + '22', borderWidth: 2.5,
      pointRadius: 0, pointHoverRadius: 4, pointBackgroundColor: color,
      tension: 0.3, fill: single, spanGaps: false, order: 2,
    }
  })

  // Click bars only in single-video view (overlapping bars across videos = noise).
  if (single) {
    const clicks = new Array(maxSec + 1).fill(0)
    for (const pt of pages[0].points) clicks[pt.sec] = pt.clicks || 0
    datasets.push({
      type: 'bar', label: 'Clicks', data: clicks, yAxisID: 'yClicks',
      backgroundColor: 'rgba(34,197,94,0.7)', borderRadius: 3, borderSkipped: false,
      barThickness: 'flex', maxBarThickness: 12, order: 1,
    })
  }

  const ctx = retentionCanvas.value.getContext('2d')
  retentionChart = new Chart(ctx, {
    type: 'bar',
    data: { labels, datasets },
    options: {
      responsive: true, maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: isDark ? '#1a1733' : '#fff',
          titleColor: isDark ? '#fff' : '#111827',
          bodyColor: isDark ? 'rgba(255,255,255,0.7)' : '#6B7280',
          borderColor: isDark ? 'rgba(255,255,255,0.1)' : '#E5E7EB',
          borderWidth: 1, padding: 10, cornerRadius: 8,
          callbacks: {
            label: (c) => c.dataset.label === 'Clicks'
              ? ` ${c.parsed.y} click${c.parsed.y === 1 ? '' : 's'}`
              : ` ${c.dataset.label}: ${c.parsed.y}% still watching`,
          },
        },
      },
      scales: {
        x: { grid: { color: isDark ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.04)' }, ticks: { color: isDark ? 'rgba(255,255,255,0.3)' : '#9CA3AF', font: { size: 10, family: 'Inter,sans-serif' }, maxTicksLimit: 14, autoSkip: true }, border: { display: false } },
        y: { min: 0, max: 100, grid: { color: isDark ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.04)' }, ticks: { color: isDark ? 'rgba(255,255,255,0.3)' : '#9CA3AF', font: { size: 10, family: 'Inter,sans-serif' }, callback: (v) => v + '%' }, border: { display: false } },
        yClicks: { display: single, position: 'right', min: 0, grid: { display: false }, ticks: { color: isDark ? 'rgba(255,255,255,0.25)' : '#C7CCD4', font: { size: 10, family: 'Inter,sans-serif' }, precision: 0 }, border: { display: false } },
      },
    },
  })
}

// ── Helpers ───────────────────────────────────────────────────────────────
function formatDuration(s) {
  s = Math.max(0, Math.round(s || 0))
  const m = Math.floor(s / 60)
  return m + ':' + String(s % 60).padStart(2, '0')
}
function formatNum(n) {
  if (n == null) return '—'
  if (n >= 1e6) return (n/1e6).toFixed(1)+'M'
  if (n >= 1e3) return (n/1e3).toFixed(1)+'K'
  return String(n)
}
// Countries are shown as a 2-letter code or full name (user toggle), no flags.
const countryMode = ref('code') // 'code' | 'name'
const REGION_NAMES = (typeof Intl !== 'undefined' && Intl.DisplayNames) ? new Intl.DisplayNames(['en'], { type: 'region' }) : null
function countryName(c) { try { return (c && REGION_NAMES?.of(c)) || c } catch { return c } }
function countryLabel(c) { return countryMode.value === 'name' ? countryName(c) : c }
function ctyTab(mode) {
  const on = countryMode.value === mode
  return { border: 'none', cursor: 'pointer', fontFamily: 'Inter,sans-serif', fontSize: '10px', fontWeight: 600, padding: '3px 9px', borderRadius: '5px', color: on ? '#fff' : (theme.dark ? 'rgba(255,255,255,0.45)' : '#6B7280'), background: on ? '#6D4EE8' : 'transparent', transition: 'all 0.12s' }
}
function eventIcon(t)  { return 'bi ' + ({page_view:'bi-eye-fill',link_click:'bi-lightning-charge-fill',age_confirmed:'bi-shield-check',video_play:'bi-play-circle-fill',video_position:'bi-clock-fill',video_progress:'bi-bar-chart-fill',video_unmute:'bi-volume-up-fill'}[t]||'bi-dot') }
function eventColor(t) { return {page_view:'#6D4EE8',link_click:'#16A34A',age_confirmed:'#0EA5E9',video_play:'#A78BFA',video_position:'#9CA3AF',video_progress:'#F59E0B',video_unmute:'#EC4899'}[t]||'#9CA3AF' }
function eventLabel(t) { return {page_view:'Page view',link_click:'Link click',age_confirmed:'Age confirmed',video_play:'Video play',video_position:'Still watching',video_progress:'Watch milestone',video_unmute:'Unmuted video'}[t]||t }
// ── Live polling (8s, dedicated endpoint) ─────────────────────────────────
let liveTimer = null
async function pollLive() {
  try {
    const params = new URLSearchParams()
    if (filterLinkId.value) params.set('link_ids[]', filterLinkId.value)
    const { data: d } = await api.get('/analytics/live?' + params)
    if (d) data.value.live = d
  } catch {}
}

// ── Close dropdowns ───────────────────────────────────────────────────────
function handleOutsideClick(e) {
  if (!e.target.closest('[data-dropdown]')) linksDropOpen.value = false
}

watch([filterLinkId, filterCountry], loadData)
watch([chartMetric, chartType, () => theme.dark], () => nextTick(renderChart))
watch(() => theme.dark, () => nextTick(renderRetention))
watch(selectedRetentionId, () => nextTick(renderRetention))

onMounted(async () => {
  // Load pages first so the dropdown + banner work regardless of analytics state
  try {
    const { data: pagesData } = await api.get('/pages')
    data.value.pages = pagesData.map(p => ({ id: p.id, model_name: p.model_name, slug: p.slug }))
    pagesCount.value = pagesData.length
  } catch {}

  if (route.query.link_id) filterLinkId.value = Number(route.query.link_id)
  document.addEventListener('click', handleOutsideClick)
  await loadData()
  liveTimer = setInterval(pollLive, 8000)
})
onUnmounted(() => {
  if (chartInstance) chartInstance.destroy()
  if (retentionChart) retentionChart.destroy()
  if (liveTimer) clearInterval(liveTimer)
  document.removeEventListener('click', handleOutsideClick)
})
</script>

<style scoped>
.adash-card {
  background: v-bind("theme.dark ? 'rgba(255,255,255,0.04)' : '#fff'");
  border: 1px solid v-bind("border");
  border-radius: 14px; padding: 20px;
}
.adash-card-title { font-size: 14px; font-weight: 700; color: v-bind("textPrimary"); margin: 0 0 4px; }
.adash-card-sub   { font-size: 12px; color: v-bind("textMuted"); margin: 0; }

/* Overview panel (CRM-style hero + metric grid) */
.adash-overview {
  background: v-bind("theme.dark ? 'rgba(255,255,255,0.04)' : '#fff'");
  border: 1px solid v-bind("border");
  border-radius: 16px;
  padding: 18px 22px 22px;
  margin-bottom: 20px;
}
.adash-ov-head {
  display: flex; align-items: center; justify-content: space-between;
  padding-bottom: 16px; margin-bottom: 18px;
  border-bottom: 1px solid v-bind("border");
}
.adash-ov-body {
  display: grid;
  grid-template-columns: 1.15fr 1fr 1fr 1fr;
}
.adash-ov-hero {
  display: flex; flex-direction: column;
  padding-right: 24px;
}
.adash-ov-hero-icon {
  width: 54px; height: 54px; border-radius: 50%;
  background: linear-gradient(135deg, #6D4EE8, #A78BFA);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 18px;
  box-shadow: 0 8px 20px rgba(109,78,232,0.35);
}
.adash-ov-hero-label { font-size: 13px; font-weight: 600; color: #A78BFA; margin: 0 0 8px; }
.adash-ov-hero-value { font-size: 38px; font-weight: 800; letter-spacing: -0.04em; color: #A78BFA; margin: 0; line-height: 1; }
.adash-ov-hero-sub { font-size: 12px; font-weight: 600; color: #A78BFA; opacity: 0.72; margin: 8px 0 0; cursor: help; }
.adash-ov-col {
  display: flex; flex-direction: column; justify-content: center; gap: 26px;
  padding: 0 22px;
  border-left: 1px solid v-bind("border");
}
.adash-ov-cell { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.adash-ov-val { font-size: 22px; font-weight: 800; letter-spacing: -0.02em; color: v-bind("textPrimary"); margin: 0 0 2px; }
.adash-ov-lbl { font-size: 12px; color: v-bind("textMuted"); margin: 0; }
.adash-ov-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

/* Top links rows */
.adash-toplink {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 12px; border-radius: 10px;
  background: transparent; border: 1px solid transparent;
  transition: background 0.15s, transform 0.15s, border-color 0.15s;
}
.adash-toplink:hover { background: v-bind("hoverBg"); transform: translateX(2px); }
.adash-toplink--first {
  background: v-bind("theme.dark ? 'linear-gradient(90deg, rgba(109,78,232,0.18), rgba(109,78,232,0.03))' : 'linear-gradient(90deg,#F1ECFF,#FAF8FF)'");
  border-color: v-bind("theme.dark ? 'rgba(109,78,232,0.35)' : '#E5DBFF'");
}
.adash-toplink--first:hover {
  background: v-bind("theme.dark ? 'linear-gradient(90deg, rgba(109,78,232,0.24), rgba(109,78,232,0.06))' : 'linear-gradient(90deg,#ECE5FF,#F7F3FF)'");
  transform: translateX(2px);
}
.adash-rank {
  width: 24px; height: 24px; border-radius: 7px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 800;
}
.adash-tl-avatar {
  width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 800; color: #fff;
  background: linear-gradient(135deg, #6D4EE8, #A78BFA);
}


.adash-filter-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 12px; border-radius: 8px; border: 1px solid v-bind("border");
  background: v-bind("theme.dark ? 'rgba(255,255,255,0.04)' : '#fff'");
  color: v-bind("textSecondary"); font-size: 13px; font-weight: 500;
  cursor: pointer; font-family: Inter, sans-serif; white-space: nowrap; transition: all 0.12s;
}
.adash-filter-btn:hover { border-color: #6D4EE8; color: #6D4EE8; }

.adash-dropdown {
  position: absolute; top: calc(100% + 6px); right: 0; z-index: 50;
  background: v-bind("theme.dark ? '#1a1733' : '#fff'");
  border: 1px solid v-bind("theme.dark ? 'rgba(255,255,255,0.1)' : '#E5E7EB'");
  border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); overflow: hidden;
}
.adash-search-input {
  width: 100%; padding: 6px 10px; border-radius: 7px;
  border: 1px solid v-bind("border");
  background: v-bind("theme.dark ? 'rgba(255,255,255,0.05)' : '#F9FAFB'");
  color: v-bind("textPrimary"); font-size: 12px; font-family: Inter, sans-serif; outline: none;
  box-sizing: border-box;
}
.adash-drop-item {
  width: 100%; display: flex; align-items: center; gap: 8px; padding: 8px 14px;
  background: none; border: none; font-size: 13px; color: v-bind("textSecondary");
  cursor: pointer; font-family: Inter, sans-serif; text-align: left; transition: background 0.1s;
}
.adash-drop-item:hover { background: v-bind("hoverBg"); }
.adash-drop-item.active { color: #6D4EE8; font-weight: 600; background: #EEE9FF; }

.adash-toggle-group {
  display: flex; gap: 2px;
  background: v-bind("dividerBg");
  border-radius: 8px; padding: 3px;
}
.adash-toggle-btn {
  padding: 4px 10px; border-radius: 6px; border: none; background: transparent;
  color: v-bind("textMuted"); font-size: 12px; font-weight: 500; cursor: pointer;
  font-family: Inter, sans-serif; transition: all 0.12s; display: flex; align-items: center; gap: 4px;
}
.adash-toggle-btn.active {
  background: v-bind("theme.dark ? 'rgba(255,255,255,0.12)' : '#fff'");
  color: v-bind("textPrimary"); box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

/* Live cells */
.adash-live-cell {
  padding: 12px 14px;
  background: v-bind("theme.dark ? 'rgba(255,255,255,0.02)' : '#FAFAFA'");
}
.adash-live-num { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; color: v-bind("textPrimary"); margin: 0; }
.adash-live-lbl { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: v-bind("textMuted"); margin: 0 0 4px; }

.adash-live-dot {
  width: 8px; height: 8px; border-radius: 50%; background: #16A34A; flex-shrink: 0;
  animation: adash-pulse 2s ease-out infinite;
}

/* Mini stats */
.adash-mini-stat { padding: 10px; border-radius: 8px; background: v-bind("hoverBg"); text-align: center; }
.adash-mini-val  { font-size: 16px; font-weight: 800; color: v-bind("textPrimary"); margin: 0; letter-spacing: -0.02em; }
.adash-mini-label { font-size: 10px; color: v-bind("textMuted"); margin: 3px 0 0; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500; }

/* Empty chart */
.adash-empty-chart { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }

/* Table */
.adash-th   { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: v-bind("textMuted"); padding: 0 0 10px; text-align: left; }
.adash-th-r { text-align: right; }
.adash-tr   { border-top: 1px solid v-bind("theme.dark ? 'rgba(255,255,255,0.05)' : '#F3F4F6'"); transition: background 0.1s; }
.adash-tr:hover { background: v-bind("hoverBg"); }
.adash-td   { padding: 10px 0; font-size: 13px; color: v-bind("textSecondary"); }
.adash-td-r { text-align: right; }

/* Form */
.adash-label  { display: block; font-size: 11px; font-weight: 600; color: v-bind("textMuted"); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.07em; }
.adash-select { width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid v-bind("border"); background: v-bind("theme.dark ? 'rgba(255,255,255,0.05)' : '#F9FAFB'"); color: v-bind("textPrimary"); font-size: 13px; font-family: Inter, sans-serif; outline: none; }
.adash-input  { width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid v-bind("border"); background: v-bind("theme.dark ? 'rgba(255,255,255,0.05)' : '#F9FAFB'"); color: v-bind("textPrimary"); font-size: 13px; font-family: Inter, sans-serif; outline: none; box-sizing: border-box; }

.adash-btn-secondary { width: 100%; padding: 8px; border-radius: 8px; border: 1px solid v-bind("border"); background: transparent; color: v-bind("textSecondary"); font-size: 13px; font-weight: 600; cursor: pointer; font-family: Inter, sans-serif; transition: all 0.12s; }
.adash-btn-secondary:hover { border-color: #6D4EE8; color: #6D4EE8; }

@keyframes adash-pulse { 0%,100%{ box-shadow:0 0 0 0 rgba(22,163,74,0.4) } 70%{ box-shadow:0 0 0 8px rgba(22,163,74,0) } }
@keyframes adash-spin  { to { transform: rotate(360deg) } }

/* ── Mobile ── */
@media (max-width: 768px) {
  /* KPI overview: hero full width, the stat cells in 2 columns instead of 4 */
  .adash-ov-body { grid-template-columns: 1fr 1fr !important; }
  .adash-ov-hero { grid-column: 1 / -1 !important; padding-right: 0 !important; }
  /* Countries + live event feed stack instead of two cramped halves */
  .adash-charts  { grid-template-columns: 1fr !important; }
}
</style>
