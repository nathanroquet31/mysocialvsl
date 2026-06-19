<template>
  <div>
    <!-- Zone drag & drop — rien de sélectionné -->
    <div v-if="!previewSrc"
      @dragover.prevent="dragging = true"
      @dragleave.prevent="dragging = false"
      @drop.prevent="onDrop"
      @click="$refs.input.click()"
      :style="{
        border: '2px dashed', borderRadius: '16px', padding: '52px 24px',
        textAlign: 'center', cursor: 'pointer', transition: 'all 0.2s',
        borderColor: dragging ? '#6D4EE8' : 'rgba(255,255,255,0.15)',
        background: dragging ? 'rgba(109,78,232,0.08)' : 'rgba(255,255,255,0.03)',
      }">
      <div style="width:64px;height:64px;background:rgba(109,78,232,0.15);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="1.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
      </div>
      <p style="font-size:16px;font-weight:700;color:#fff;margin-bottom:6px">Drop your video here</p>
      <p style="font-size:13px;color:rgba(255,255,255,0.35);margin-bottom:20px">MP4 (H.264) or WebM — max 100MB · 45s recommended</p>
      <div style="display:inline-flex;align-items:center;gap:8px;background:#6D4EE8;color:#fff;padding:11px 24px;border-radius:10px;font-size:13px;font-weight:700">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Choose a file
      </div>
      <input ref="input" type="file" accept="video/mp4,video/webm" style="display:none" @change="onFileSelect" />
    </div>

    <!-- Preview vidéo (locale dès la sélection) + overlay de progression pendant l'upload -->
    <div v-else style="border:1px solid rgba(109,78,232,0.4);border-radius:16px;overflow:hidden;background:#000;position:relative">
      <video :src="previewSrc" controls playsinline
        style="width:100%;max-height:420px;display:block;object-fit:contain">
      </video>

      <!-- Overlay tant que ça uploade -->
      <div v-if="uploading"
        style="position:absolute;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(2px);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px">
        <p style="font-size:14px;font-weight:700;color:#fff;margin-bottom:14px">
          {{ progress < 100 ? `Uploading… ${progress}%` : 'Finalizing…' }}
        </p>
        <div style="width:80%;max-width:280px;height:6px;background:rgba(255,255,255,0.15);border-radius:999px;overflow:hidden">
          <div :style="{height:'100%',background:'linear-gradient(90deg,#6D4EE8,#A78BFA)',borderRadius:'999px',width:progress+'%',transition:'width 0.3s'}"></div>
        </div>
        <p v-if="isHeavyFile" style="font-size:12px;color:rgba(255,255,255,0.6);margin-top:14px;text-align:center;line-height:1.5;max-width:320px">
          Large video — the upload may take a few minutes. Don't close this page.
        </p>
      </div>

      <!-- Badges + Replace une fois l'upload terminé -->
      <div v-else style="position:absolute;top:10px;right:10px;display:flex;gap:6px">
        <div style="background:rgba(109,78,232,0.85);backdrop-filter:blur(8px);border-radius:8px;padding:4px 10px;display:flex;align-items:center;gap:5px">
          <div style="width:6px;height:6px;border-radius:50%;background:#A78BFA"></div>
          <span style="font-size:10px;font-weight:700;color:#fff;letter-spacing:0.05em">VSL</span>
        </div>
        <button @click="removeVideo"
          style="background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:4px 10px;color:rgba(255,255,255,0.7);font-size:12px;cursor:pointer;font-weight:600">
          <i class="bi bi-x-lg" style="font-size:11px"></i> Replace
        </button>
      </div>
    </div>

    <!-- Error -->
    <p v-if="error" style="font-size:12px;color:#F87171;margin-top:8px;text-align:center">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount } from 'vue'
import api from '@/lib/axios'

const props = defineProps({ modelValue: String })
const emit = defineEmits(['update:modelValue', 'uploading'])

const dragging = ref(false)
const uploading = ref(false)
const progress = ref(0)
const error = ref('')
const isHeavyFile = ref(false)
const localPreview = ref('')

// Affiche la vidéo locale tant qu'elle est là (preview instantané),
// sinon l'URL distante déjà enregistrée (cas édition d'une page existante).
const previewSrc = computed(() => localPreview.value || props.modelValue)

function clearLocalPreview() {
  if (localPreview.value) {
    URL.revokeObjectURL(localPreview.value)
    localPreview.value = ''
  }
}

function onDrop(e) {
  dragging.value = false
  const file = e.dataTransfer.files[0]
  if (file) upload(file)
}

function onFileSelect(e) {
  const file = e.target.files[0]
  if (file) upload(file)
}

function removeVideo() {
  clearLocalPreview()
  emit('update:modelValue', '')
}

async function upload(file) {
  // Only accept formats that play across all browsers. iPhones record HEVC in a
  // .mov container by default, which Chrome/Firefox/Android can't decode — it
  // would upload fine but show a black, unplayable video on the public page.
  const okType = /^video\/(mp4|webm)$/i.test(file.type) || /\.(mp4|webm)$/i.test(file.name)
  if (!okType) {
    error.value = "This format won't play in every browser (looks like a .mov/QuickTime, often HEVC). Upload an MP4 (H.264) or WebM. iPhone tip: Settings → Camera → Formats → “Most Compatible”."
    return
  }

  const maxSize = 100 * 1024 * 1024 // 100MB
  if (file.size > maxSize) {
    error.value = 'File too large (max 100MB). Compress your video or reduce the resolution to 720p.'
    return
  }

  // Preview instantané : on montre la vidéo locale tout de suite, avant même l'upload.
  clearLocalPreview()
  localPreview.value = URL.createObjectURL(file)

  error.value = ''
  uploading.value = true
  progress.value = 0
  isHeavyFile.value = file.size > 20 * 1024 * 1024
  emit('uploading', true)

  const fd = new FormData()
  fd.append('file', file)

  try {
    const { data } = await api.post('/upload/video', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (e) => {
        progress.value = Math.round((e.loaded / e.total) * 100)
      },
    })
    emit('update:modelValue', data.url)
    // On garde la preview locale à l'écran (même vidéo, déjà en mémoire) :
    // pas de re-téléchargement depuis R2. modelValue porte l'URL réelle pour la sauvegarde.
  } catch (e) {
    error.value = 'Upload failed. Please try again.'
    clearLocalPreview()
  } finally {
    uploading.value = false
    emit('uploading', false)
  }
}

onBeforeUnmount(clearLocalPreview)
</script>
