import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/',          component: () => import('@/pages/Home.vue'),      meta: { guest: true } },
  { path: '/login',     component: () => import('@/pages/Login.vue'),     meta: { guest: true } },
  { path: '/register',  component: () => import('@/pages/Register.vue'),  meta: { guest: true } },
  { path: '/dashboard', component: () => import('@/pages/Dashboard.vue'), meta: { auth: true } },
  { path: '/pages/new', component: () => import('@/pages/PageCreate.vue'),meta: { auth: true } },
  { path: '/pages/:id/edit', component: () => import('@/pages/PageEdit.vue'), meta: { auth: true } },
  { path: '/pages/:id',      component: () => import('@/pages/PageEdit.vue'), meta: { auth: true } },
  { path: '/billing',        component: () => import('@/pages/Billing.vue'),  meta: { auth: true } },
  { path: '/dashboard/profile',  component: () => import('@/pages/ProfilePage.vue'),  meta: { auth: true } },
  { path: '/dashboard/settings', component: () => import('@/pages/SettingsPage.vue'), meta: { auth: true } },
  { path: '/dashboard/domains',  component: () => import('@/pages/DomainsPage.vue'),  meta: { auth: true } },
  { path: '/dashboard/support',  component: () => import('@/pages/SupportPage.vue'),  meta: { auth: true } },
  { path: '/dashboard/guide',    component: () => import('@/pages/CreatorGuide.vue'),  meta: { auth: true } },
  { path: '/p/:slug',   component: () => import('@/pages/PublicPage.vue'), meta: {} },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.auth && !auth.isLoggedIn()) return '/login'
  if (to.meta.guest && auth.isLoggedIn() && to.path !== '/') return '/dashboard'
})

export default router
