<template>
  <div>
    <!-- Zone drag & drop -->
    <div v-if="!modelValue && !uploading"
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
      <p style="font-size:16px;font-weight:700;color:#fff;margin-bottom:6px">Glisse ta vidéo ici</p>
      <p style="font-size:13px;color:rgba(255,255,255,0.35);margin-bottom:20px">MP4, MOV, WEBM — max 100MB · 45s recommandé</p>
      <div style="display:inline-flex;align-items:center;gap:8px;background:#6D4EE8;color:#fff;padding:11px 24px;border-radius:10px;font-size:13px;font-weight:700">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Choisir un fichier
      </div>
      <input ref="input" type="file" accept="video/mp4,video/quicktime,video/webm,video/avi" style="display:none" @change="onFileSelect" />
    </div>

    <!-- Progress upload -->
    <div v-if="uploading" style="border:1px solid rgba(109,78,232,0.3);border-radius:16px;padding:32px 24px;background:rgba(109,78,232,0.06);text-align:center">
      <div style="width:48px;height:48px;background:rgba(109,78,232,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <p style="font-size:14px;font-weight:600;color:#fff;margin-bottom:16px">Upload en cours... {{ progress }}%</p>
      <div style="height:6px;background:rgba(255,255,255,0.08);border-radius:999px;overflow:hidden">
        <div :style="{height:'100%',background:'linear-gradient(90deg,#6D4EE8,#A78BFA)',borderRadius:'999px',width:progress+'%',transition:'width 0.3s'}"></div>
      </div>
      <p style="font-size:12px;color:rgba(255,255,255,0.3);margin-top:10px">{{ fileName }}</p>
      <p v-if="isHeavyFile" style="font-size:12px;color:rgba(255,255,255,0.4);margin-top:12px;line-height:1.5">
        Ta vidéo est volumineuse — l'upload peut prendre quelques minutes selon ta connexion. Ne ferme pas cette page.
      </p>
    </div>

    <!-- Vidéo uploadée -->
    <div v-if="modelValue && !uploading" style="border:1px solid rgba(109,78,232,0.4);border-radius:16px;overflow:hidden;background:#000;position:relative">
      <video :src="modelValue" controls playsinline
        style="width:100%;max-height:420px;display:block;object-fit:contain">
      </video>
      <div style="position:absolute;top:10px;right:10px;display:flex;gap:6px">
        <div style="background:rgba(109,78,232,0.85);backdrop-filter:blur(8px);border-radius:8px;padding:4px 10px;display:flex;align-items:center;gap:5px">
          <div style="width:6px;height:6px;border-radius:50%;background:#A78BFA"></div>
          <span style="font-size:10px;font-weight:700;color:#fff;letter-spacing:0.05em">VSL</span>
        </div>
        <button @click="removeVideo"
          style="background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:4px 10px;color:rgba(255,255,255,0.7);font-size:12px;cursor:pointer;font-weight:600">
          <i class="bi bi-x-lg" style="font-size:11px"></i> Changer
        </button>
      </div>
    </div>

    <!-- Erreur -->
    <p v-if="error" style="font-size:12px;color:#F87171;margin-top:8px;text-align:center">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/lib/axios'

const props = defineProps({ modelValue: String })
const emit = defineEmits(['update:modelValue', 'uploading'])

const dragging = ref(false)
const uploading = ref(false)
const progress = ref(0)
const error = ref('')
const fileName = ref('')
const isHeavyFile = ref(false)

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
  emit('update:modelValue', '')
}

async function upload(file) {
  const maxSize = 100 * 1024 * 1024 // 100MB
  if (file.size > maxSize) {
    error.value = 'Fichier trop lourd (max 100MB). Compresse ta vidéo ou réduis la résolution à 720p.'
    return
  }

  error.value = ''
  uploading.value = true
  progress.value = 0
  fileName.value = file.name
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
  } catch (e) {
    error.value = 'Erreur lors de l\'upload. Réessaie.'
  } finally {
    uploading.value = false
    emit('uploading', false)
  }
}
</script>
