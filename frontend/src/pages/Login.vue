<template>
  <div style="min-height:100vh;background:#0d0d0d;display:flex;font-family:'Inter',sans-serif;position:relative;overflow:hidden">

    <!-- Background glow blobs -->
    <div style="position:absolute;top:-120px;left:-120px;width:400px;height:400px;background:radial-gradient(circle,rgba(109,78,232,0.25) 0%,transparent 70%);pointer-events:none"></div>
    <div style="position:absolute;bottom:-80px;right:-80px;width:320px;height:320px;background:radial-gradient(circle,rgba(167,139,250,0.15) 0%,transparent 70%);pointer-events:none"></div>

    <!-- Left panel — branding -->
    <div style="flex:1;display:none;flex-direction:column;justify-content:center;padding:64px;position:relative" class="left-panel">
      <div style="margin-bottom:48px">
        <div style="display:inline-flex;align-items:center;gap:10px;margin-bottom:40px">
          <div style="width:36px;height:36px;background:#6D4EE8;border-radius:10px;display:flex;align-items:center;justify-content:center">
            <span style="color:#fff;font-weight:900;font-size:13px;letter-spacing:-0.05em">VSL</span>
          </div>
          <span style="font-weight:700;color:#fff;font-size:18px">MySocialVSL</span>
        </div>
        <h2 style="font-size:36px;font-weight:800;color:#fff;line-height:1.2;margin-bottom:16px">Your link-in-bio<br><span style="color:#A78BFA">that converts.</span></h2>
        <p style="font-size:15px;color:rgba(255,255,255,0.5);line-height:1.6">VSL pages, deep linking, analytics — everything a creator needs to turn followers into subscribers.</p>
      </div>

      <!-- Testimonial -->
      <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:20px 24px">
        <p style="font-size:13px;color:rgba(255,255,255,0.7);line-height:1.6;margin-bottom:12px">"MySocialVSL doubled my OnlyFans conversion rate in 2 weeks. The VSL feature is insane."</p>
        <div style="display:flex;align-items:center;gap:10px">
          <div style="width:32px;height:32px;border-radius:50%;background:#6D4EE8;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff">K</div>
          <div>
            <p style="font-size:12px;font-weight:600;color:#fff;margin:0">Karine</p>
            <p style="font-size:11px;color:rgba(255,255,255,0.4);margin:0">OnlyFans creator · 12k fans</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right panel — form -->
    <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:24px">
      <div style="width:100%;max-width:400px">

        <!-- Logo -->
        <RouterLink to="/" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:36px">
          <div style="width:36px;height:36px;background:#6D4EE8;border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 20px rgba(109,78,232,0.4)">
            <span style="color:#fff;font-weight:900;font-size:11px;letter-spacing:-0.05em">VSL</span>
          </div>
          <span style="font-weight:700;color:#fff;font-size:17px">MySocialVSL</span>
        </RouterLink>

        <h1 style="font-size:26px;font-weight:800;color:#fff;margin:0 0 6px">Welcome back</h1>
        <p style="font-size:14px;color:rgba(255,255,255,0.4);margin:0 0 32px">
          No account yet?
          <RouterLink to="/register" style="color:#A78BFA;font-weight:600;text-decoration:none">Sign up free</RouterLink>
        </p>

        <!-- Form card -->
        <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:28px">

          <!-- Error -->
          <div v-if="error" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#f87171;font-size:13px;border-radius:10px;padding:10px 14px;margin-bottom:20px">
            {{ error }}
          </div>

          <form @submit.prevent="submit">
            <!-- Email -->
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:12px;font-weight:600;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:7px">Email</label>
              <input v-model="form.email" type="email" required placeholder="you@example.com"
                style="width:100%;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:11px 14px;font-size:14px;color:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color 0.15s"
                @focus="e => e.target.style.borderColor='#6D4EE8'"
                @blur="e => e.target.style.borderColor='rgba(255,255,255,0.1)'" />
            </div>

            <!-- Password -->
            <div style="margin-bottom:24px">
              <label style="display:block;font-size:12px;font-weight:600;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:7px">Password</label>
              <input v-model="form.password" type="password" required placeholder="••••••••"
                style="width:100%;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:11px 14px;font-size:14px;color:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color 0.15s"
                @focus="e => e.target.style.borderColor='#6D4EE8'"
                @blur="e => e.target.style.borderColor='rgba(255,255,255,0.1)'" />
            </div>

            <!-- Submit -->
            <button type="submit" :disabled="loading"
              :style="{
                width:'100%', padding:'12px', borderRadius:'12px', border:'none', cursor:loading?'not-allowed':'pointer',
                fontSize:'14px', fontWeight:700, color:'#fff', fontFamily:'inherit',
                background: loading ? 'rgba(109,78,232,0.5)' : 'linear-gradient(135deg,#6D4EE8,#8B6FF0)',
                boxShadow: loading ? 'none' : '0 4px 20px rgba(109,78,232,0.4)',
                transition:'all 0.2s', opacity: loading ? 0.7 : 1
              }">
              {{ loading ? 'Signing in...' : 'Sign in' }}<i v-if="!loading" class="bi bi-arrow-right" style="margin-left:6px"></i>
            </button>
          </form>
        </div>

        <!-- Footer -->
        <p style="text-align:center;font-size:12px;color:rgba(255,255,255,0.2);margin-top:24px">
          By signing in you agree to our
          <a href="#" style="color:rgba(255,255,255,0.4);text-decoration:none">Terms</a> &
          <a href="#" style="color:rgba(255,255,255,0.4);text-decoration:none">Privacy Policy</a>
        </p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const form = ref({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(form.value.email, form.value.password)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Incorrect email or password.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@media (min-width: 900px) {
  .left-panel { display: flex !important; }
}
input::placeholder { color: rgba(255,255,255,0.2); }
</style>
