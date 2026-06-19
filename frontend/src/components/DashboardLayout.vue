<template>
  <div :style="{minHeight:'100vh',background:theme.dark?'#0d0b1e':'#F9FAFB',display:'flex',fontFamily:'Inter,sans-serif',transition:'background 0.2s'}">

    <!-- Sidebar -->
    <aside :style="{
      width: collapsed ? '64px' : '256px',
      minHeight:'100vh', background: theme.dark ? '#100e22' : '#fff',
      borderRight: theme.dark ? '1px solid rgba(255,255,255,0.06)' : '1px solid #E5E7EB',
      display:'flex', flexDirection:'column',
      position:'fixed', top:0, left:0, zIndex:20,
      transition:'width 0.2s ease',
      overflowX:'hidden'
    }">

      <!-- Logo + collapse -->
      <div :style="{padding:'0 12px',display:'flex',alignItems:'center',justifyContent:'space-between',borderBottom:`1px solid ${border}`,height:'60px',flexShrink:0}">
        <div style="display:flex;align-items:center;gap:9px;min-width:0;overflow:hidden">
          <LogoMark :size="28" />
          <span v-if="!collapsed" :style="{fontWeight:700,color:theme.dark?'#fff':'#0F0A3C',fontSize:'14px',letterSpacing:'-0.01em',whiteSpace:'nowrap'}">MySocialVSL</span>
        </div>
        <button @click="collapsed=!collapsed"
          :style="{background:'none',border:'none',cursor:'pointer',color:textMuted,padding:'4px',borderRadius:'6px',display:'flex',alignItems:'center',flexShrink:0,fontSize:'16px'}"
          :title="collapsed ? 'Expand' : 'Collapse'">
          <i v-if="!collapsed" class="bi bi-chevron-left"></i>
          <i v-else class="bi bi-chevron-right"></i>
        </button>
      </div>

      <!-- User + Plan -->
      <div :style="{padding:'10px 8px', borderBottom:`1px solid ${border}`, flexShrink:0}">
        <div style="display:flex;align-items:center;gap:8px;padding:8px;border-radius:10px;">
          <div style="width:30px;height:30px;background:#EEE9FF;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#6D4EE8;flex-shrink:0">
            {{ auth.user?.name?.[0]?.toUpperCase() || 'P' }}
          </div>
          <div v-if="!collapsed" style="flex:1;min-width:0">
            <p :style="{fontSize:'12px',fontWeight:600,color:textPrimary,whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',margin:0}">{{ auth.user?.name || 'Creator' }}</p>
            <span v-if="auth.user?.is_admin" style="font-size:10px;font-weight:700;background:linear-gradient(135deg,#6D4EE8,#A78BFA);color:#fff;border-radius:999px;padding:1px 8px;display:inline-block;margin-top:2px"><i class="bi bi-lightning-charge-fill" style="margin-right:3px"></i>Admin</span>
            <RouterLink v-else to="/billing" style="text-decoration:none">
              <span :style="{fontSize:'10px',fontWeight:600,background:auth.user?.plan==='pro'?'#FEF3C7':auth.user?.plan==='agency'?'#ECFDF5':'#EEE9FF',color:auth.user?.plan==='pro'?'#D97706':auth.user?.plan==='agency'?'#059669':'#6D4EE8',borderRadius:'999px',padding:'1px 8px',display:'inline-block',marginTop:'2px'}">{{ auth.user?.plan === 'pro' ? 'Pro' : auth.user?.plan === 'agency' ? 'Agency' : 'Free plan' }}</span>
            </RouterLink>
          </div>
        </div>
      </div>

      <!-- Nav -->
      <nav style="padding:8px;flex:1;overflow-y:auto;overflow-x:hidden">

        <!-- Dashboard (analytics overview) -->
        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard" :active="route.path==='/dashboard'" label="Dashboard" icon="dashboard" />

        <!-- Links (pages list) -->
        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/links" :active="route.path==='/dashboard/links'" label="Links" icon="links" />

        <!-- Analytics (expandable) -->
        <div>
          <div @click="analyticsOpen=!analyticsOpen"
            :style="{
              display:'flex', alignItems:'center', gap:'10px',
              padding: collapsed ? '10px' : '8px 10px',
              borderRadius:'8px', fontSize:'13px',
              fontWeight: route.path.startsWith('/dashboard/analytics') ? '600' : '500',
              color: route.path.startsWith('/dashboard/analytics') ? '#6D4EE8' : textSecondary,
              background: route.path.startsWith('/dashboard/analytics') ? '#EEE9FF' : 'transparent',
              marginBottom:'2px', cursor:'pointer',
              justifyContent: collapsed ? 'center' : 'flex-start',
              transition:'all 0.12s', position:'relative', userSelect:'none'
            }"
            @mouseenter="e => { if(!route.path.startsWith('/dashboard/analytics')) e.currentTarget.style.background=hoverBg }"
            @mouseleave="e => { if(!route.path.startsWith('/dashboard/analytics')) e.currentTarget.style.background='transparent' }">
            <span :style="{flexShrink:0,display:'flex',alignItems:'center',color:'inherit',fontSize:'16px',lineHeight:1}">
              <i class="bi bi-graph-up"></i>
            </span>
            <span v-if="!collapsed" style="flex:1">Analytics</span>
            <i v-if="!collapsed" class="bi bi-chevron-down"
              :style="{transform: analyticsOpen ? 'rotate(180deg)' : 'rotate(0deg)', transition:'transform 0.2s', fontSize:'13px'}"></i>
          </div>
          <!-- Sub-items -->
          <div v-if="analyticsOpen && !collapsed" style="padding-left:16px;margin-bottom:2px">
            <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',padding:'6px 10px',borderRadius:'8px',fontSize:'12px',color:textMuted,cursor:'default'}">
              <span><i class="bi bi-instagram" style="font-size:11px;margin-right:6px"></i>Instagram</span>
              <span :style="{fontSize:'10px',fontWeight:600,background:hoverBg,color:textMuted,borderRadius:'999px',padding:'1px 7px',border:`1px solid ${border}`}">Soon</span>
            </div>
            <div :style="{display:'flex',alignItems:'center',justifyContent:'space-between',padding:'6px 10px',borderRadius:'8px',fontSize:'12px',color:textMuted,cursor:'default'}">
              <span><i class="bi bi-tiktok" style="font-size:11px;margin-right:6px"></i>TikTok</span>
              <span :style="{fontSize:'10px',fontWeight:600,background:hoverBg,color:textMuted,borderRadius:'999px',padding:'1px 7px',border:`1px solid ${border}`}">Soon</span>
            </div>
          </div>
        </div>

        <!-- Account section -->
        <div :style="{height:'1px',background:border,margin:'8px 2px'}"></div>
        <p v-if="!collapsed" :style="{fontSize:'10px',fontWeight:600,color:textMuted,textTransform:'uppercase',letterSpacing:'0.1em',padding:'0 8px',margin:'8px 0 4px',whiteSpace:'nowrap'}">Account</p>

        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/profile" :active="route.path==='/dashboard/profile'" label="Profile" icon="profile" />
        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/billing" :active="route.path==='/billing'" label="Billing" icon="billing" />

        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/affiliates" :active="route.path==='/dashboard/affiliates'" label="Affiliates" icon="affiliates" />

        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/domains" :active="route.path==='/dashboard/domains'" label="Domains" icon="domains" />

        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/api" :active="route.path==='/dashboard/api'" label="API" icon="api" />

        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/settings" :active="route.path==='/dashboard/settings'" label="Settings" icon="settings" />

        <!-- Admin section — founder only (is_admin) -->
        <template v-if="auth.user?.is_admin">
          <div :style="{height:'1px',background:border,margin:'8px 2px'}"></div>
          <p v-if="!collapsed" :style="{fontSize:'10px',fontWeight:600,color:textMuted,textTransform:'uppercase',letterSpacing:'0.1em',padding:'0 8px',margin:'8px 0 4px',whiteSpace:'nowrap'}">Admin</p>
          <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/admin" :active="route.path==='/dashboard/admin'" label="Admin" icon="admin" />
        </template>

        <!-- Help section -->
        <div :style="{height:'1px',background:border,margin:'8px 2px'}"></div>
        <p v-if="!collapsed" :style="{fontSize:'10px',fontWeight:600,color:textMuted,textTransform:'uppercase',letterSpacing:'0.1em',padding:'0 8px',margin:'8px 0 4px',whiteSpace:'nowrap'}">Help</p>

        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/help" :active="route.path==='/dashboard/help'" label="Help Center" icon="help" />
        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/guide" :active="route.path==='/dashboard/guide'" label="Creator Guide" icon="guide" />
        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/support" :active="route.path==='/dashboard/support'" label="Support" icon="support" />
        <NavItem :collapsed="collapsed" :dark="theme.dark" href="/dashboard/legacy" :active="route.path==='/dashboard/legacy'" label="Legacy" icon="legacy" />
      </nav>

      <!-- Usage quotas -->
      <div v-if="!collapsed" :style="{padding:'12px 14px',borderTop:`1px solid ${border}`,flexShrink:0}">
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span :style="{fontSize:'11px',color:textMuted,fontWeight:500}">VSL Pages</span>
            <span :style="{fontSize:'11px',color:textMuted}">{{ usagePagesCount }} / {{ fmtLimit(usagePagesLimit) }}</span>
          </div>
          <div :style="{height:'4px',background:hoverBg,borderRadius:'999px',overflow:'hidden'}">
            <div :style="{height:'100%',borderRadius:'999px',background:'#6D4EE8',width:quotaPct(usagePagesCount,usagePagesLimit)+'%',transition:'width 0.3s'}"></div>
          </div>
        </div>
        <div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span :style="{fontSize:'11px',color:textMuted,fontWeight:500}">Direct links</span>
            <span :style="{fontSize:'11px',color:textMuted}">{{ usageDirectCount }} / {{ fmtLimit(usageDirectLimit) }}</span>
          </div>
          <div :style="{height:'4px',background:hoverBg,borderRadius:'999px',overflow:'hidden'}">
            <div :style="{height:'100%',borderRadius:'999px',background:'#6D4EE8',width:quotaPct(usageDirectCount,usageDirectLimit)+'%',transition:'width 0.3s'}"></div>
          </div>
        </div>
      </div>

      <!-- User menu -->
      <div :style="{padding:'8px',borderTop:`1px solid ${border}`,flexShrink:0,position:'relative'}">
        <div
          :style="{
            display:'flex', alignItems:'center', gap:'8px',
            padding:'8px', borderRadius:'10px', cursor:'pointer',
            background: userMenuOpen ? hoverBg : 'transparent',
            transition:'background 0.15s'
          }"
          @click="userMenuOpen=!userMenuOpen"
          @mouseenter="e => e.currentTarget.style.background=hoverBg"
          @mouseleave="e => { if(!userMenuOpen) e.currentTarget.style.background='transparent' }">
          <div style="width:30px;height:30px;background:#EEE9FF;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#6D4EE8;flex-shrink:0">
            {{ auth.user?.name?.[0]?.toUpperCase() || '?' }}
          </div>
          <div v-if="!collapsed" style="flex:1;min-width:0">
            <p :style="{fontSize:'12px',fontWeight:600,color:textPrimary,whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',margin:0}">{{ auth.user?.name }}</p>
            <p :style="{fontSize:'10px',color:textMuted,whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',margin:0}">{{ auth.user?.email }}</p>
          </div>
          <i v-if="!collapsed" class="bi bi-three-dots" :style="{color:textMuted,fontSize:'15px'}"></i>
        </div>

        <!-- Dropdown -->
        <div v-if="userMenuOpen" :style="{position:'absolute',bottom:'56px',left:'8px',right:'8px',background:dropdownBg,border:`1px solid ${borderMain}`,borderRadius:'12px',boxShadow:'0 8px 24px rgba(0,0,0,0.15)',overflow:'hidden',zIndex:30}">
          <RouterLink to="/billing"
            :style="{display:'flex',alignItems:'center',gap:'10px',padding:'10px 14px',fontSize:'13px',color:textSecondary,textDecoration:'none'}"
            @mouseenter="e => e.currentTarget.style.background=hoverBg"
            @mouseleave="e => e.currentTarget.style.background='transparent'">
            <i class="bi bi-credit-card" style="font-size:14px"></i>
            Billing
          </RouterLink>
          <div v-if="false" :style="{height:'1px',background:border}"></div>
          <!-- Dark mode toggle row — hidden for V1 (dark-only). Remove v-if when light mode is finished. -->
          <div
            v-if="false"
            :style="{display:'flex',alignItems:'center',justifyContent:'space-between',padding:'10px 14px',cursor:'pointer'}"
            @click="theme.toggle()"
            @mouseenter="e => e.currentTarget.style.background=hoverBg"
            @mouseleave="e => e.currentTarget.style.background='transparent'">
            <div :style="{display:'flex',alignItems:'center',gap:'10px'}">
              <i :class="theme.dark ? 'bi bi-moon-fill' : 'bi bi-sun-fill'" style="font-size:14px;line-height:1"></i>
              <span :style="{fontSize:'13px',color:textSecondary}">{{ theme.dark ? 'Dark mode' : 'Light mode' }}</span>
            </div>
            <ThemeToggle />
          </div>
          <div :style="{height:'1px',background:border}"></div>
          <button @click="handleLogout"
            :style="{width:'100%',display:'flex',alignItems:'center',gap:'10px',padding:'10px 14px',fontSize:'13px',color:'#EF4444',background:'none',border:'none',cursor:'pointer',textAlign:'left',fontFamily:'Inter,sans-serif'}"
            @mouseenter="e => e.currentTarget.style.background=theme.dark?'rgba(239,68,68,0.08)':'#FEF2F2'"
            @mouseleave="e => e.currentTarget.style.background='transparent'">
            <i class="bi bi-box-arrow-right" style="font-size:14px"></i>
            Sign out
          </button>
        </div>
      </div>
    </aside>

    <!-- Main wrapper -->
    <div :style="{marginLeft: collapsed ? '64px' : '256px', flex:1, minWidth:0, minHeight:0, height:'100vh', overflow:'hidden', transition:'margin-left 0.2s ease', display:'flex', flexDirection:'column'}">

      <!-- Top header -->
      <header :style="{height:'60px',background:theme.dark?'#100e22':'#fff',borderBottom:theme.dark?'1px solid rgba(255,255,255,0.06)':'1px solid #E5E7EB',display:'flex',alignItems:'center',justifyContent:'space-between',padding:'0 28px',position:'sticky',top:0,zIndex:10,flexShrink:0}">
        <slot name="header-left">
          <h1 :style="{fontSize:'15px',fontWeight:600,color:theme.dark?'#fff':'#111827',letterSpacing:'-0.01em',margin:0}">{{ title }}</h1>
        </slot>
        <slot name="header-actions" />
      </header>

      <!-- Page content -->
      <div :style="{padding:props.noPadding?'0':'28px',flex:1,minHeight:0,overflow:props.noPadding?'hidden':'auto',color:theme.dark?'#fff':'',display:'flex',flexDirection:'column'}">
        <slot />
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import NavItem from '@/components/NavItem.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import LogoMark from '@/components/LogoMark.vue'

const props = defineProps({
  title: { type: String, default: 'Dashboard' },
  noPadding: { type: Boolean, default: false },
  pagesCount: { type: Number, default: 0 },
  pagesLimit: { type: Number, default: 5 },
  directCount: { type: Number, default: 0 },
  directLimit: { type: Number, default: 2 },
})

const auth = useAuthStore()
const theme = useThemeStore()
const route = useRoute()
const router = useRouter()
const collapsed = ref(false)
const userMenuOpen = ref(false)
const analyticsOpen = ref(false)

// Real usage quotas come from /me (auth.user); fall back to props if a page passes them.
const fmtLimit = (n) => (n == null || n > 100000 ? '∞' : n)
const usagePagesCount = computed(() => auth.user?.pages_count ?? props.pagesCount)
const usagePagesLimit = computed(() => auth.user?.page_limit ?? props.pagesLimit)
const usageDirectCount = computed(() => auth.user?.direct_count ?? props.directCount)
const usageDirectLimit = computed(() => auth.user?.link_limit ?? props.directLimit)

const analyticsSubItems = [
  { label: 'Instagram' },
  { label: 'TikTok' },
]

// Theme-aware color tokens
const border      = computed(() => theme.dark ? 'rgba(255,255,255,0.07)' : '#F3F4F6')
const borderMain  = computed(() => theme.dark ? 'rgba(255,255,255,0.08)' : '#E5E7EB')
const textPrimary = computed(() => theme.dark ? '#fff' : '#111827')
const textSecondary = computed(() => theme.dark ? 'rgba(255,255,255,0.6)' : '#4B5563')
const textMuted   = computed(() => theme.dark ? 'rgba(255,255,255,0.35)' : '#9CA3AF')
const hoverBg     = computed(() => theme.dark ? 'rgba(255,255,255,0.06)' : '#F3F4F6')
const dropdownBg  = computed(() => theme.dark ? '#1a1733' : '#fff')

function quotaPct(used, limit) {
  if (!limit) return 0
  return Math.min(100, Math.round((used / limit) * 100))
}

async function handleLogout() {
  userMenuOpen.value = false
  await auth.logout()
  router.push('/login')
}
</script>
