<template>
  <div>
    <!-- Zone drag & drop -->
    <div v-if="!modelValue && !uploading"
      @dragover.prevent="dragging = true"
      @dragleave.prevent="dragging = false"
      @drop.prevent="onDrop"
      @click="$refs.input.click()"
      :style="{
        border: '2px dashed', borderRadius: '16px', padding: '48px 24px',
        textAlign: 'center', cursor: 'pointer', transition: 'all 0.2s',
        borderColor: dragging ? '#2563eb' : '#d1d5db',
        background: dragging ? '#eff6ff' : '#fafafa',
      }">
      <div style="font-size:48px;margin-bottom:12px">🎬</div>
      <p style="font-size:15px;font-weight:700;color:#111;margin-bottom:6px">Glisse ta vidéo ici</p>
      <p style="font-size:13px;color:#9ca3af;margin-bottom:16px">MP4, MOV, WEBM — max 300MB</p>
      <div style="display:inline-flex;align-items:center;gap:8px;background:#2563eb;color:#fff;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600">
        <span>📁</span> Choisir un fichier
      </div>
      <input ref="input" type="file" accept="video/mp4,video/quicktime,video/webm,video/avi" style="display:none" @change="onFileSelect" />
    </div>

    <!-- Progress upload -->
    <div v-if="uploading" style="border:1px solid #e5e7eb;border-radius:16px;padding:28px 24px;background:#fff;text-align:center">
      <div style="font-size:32px;margin-bottom:12px">⏫</div>
      <p style="font-size:14px;font-weight:600;color:#111;margin-bottom:16px">Upload en cours... {{ progress }}%</p>
      <div style="height:6px;background:#f3f4f6;border-radius:999px;overflow:hidden">
        <div :style="{height:'100%',background:'#2563eb',borderRadius:'999px',width:progress+'%',transition:'width 0.3s'}"></div>
      </div>
      <p style="font-size:12px;color:#9ca3af;margin-top:10px">{{ fileName }}</p>
    </div>

    <!-- Vidéo uploadée -->
    <div v-if="modelValue && !uploading" style="border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;background:#000;position:relative">
      <video :src="modelValue" controls playsinline
        style="width:100%;max-height:420px;display:block;object-fit:contain">
      </video>
      <!-- Overlay actions -->
      <div style="position:absolute;top:10px;right:10px;display:flex;gap:6px">
        <div style="background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);border-radius:8px;padding:4px 10px;display:flex;align-items:center;gap:5px">
          <div style="width:6px;height:6px;border-radius:50%;background:#ef4444"></div>
          <span style="font-size:10px;font-weight:700;color:#fff;letter-spacing:0.05em">VSL</span>
        </div>
        <button @click="removeVideo"
          style="background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);border:none;border-radius:8px;padding:4px 10px;color:#fff;font-size:12px;cursor:pointer;font-weight:600">
          ✕ Changer
        </button>
      </div>
    </div>

    <!-- Erreur -->
    <p v-if="error" style="font-size:12px;color:#ef4444;margin-top:8px;text-align:center">{{ error }}</p>

    <!-- Info tooltip -->
    <div style="display:flex;align-items:flex-start;gap:10px;background:#eff6ff;border-radius:12px;padding:12px 14px;margin-top:12px">
      <span style="font-size:16px;flex-shrink:0">💡</span>
      <p style="font-size:12px;color:#1d4ed8;line-height:1.5">
        <strong>Pourquoi ça convertit :</strong> La vidéo se lance automatiquement quand le fan arrive sur ta page. Il voit ton contenu avant même de cliquer — le CTR moyen est <strong>3x plus élevé</strong> qu'une page sans vidéo.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/lib/axios'

const props = defineProps({ modelValue: String })
const emit = defineEmits(['update:modelValue'])

const dragging = ref(false)
const uploading = ref(false)
const progress = ref(0)
const error = ref('')
const fileName = ref('')

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
  const maxSize = 300 * 1024 * 1024 // 300MB
  if (file.size > maxSize) {
    error.value = 'Fichier trop lourd (max 300MB)'
    return
  }

  error.value = ''
  uploading.value = true
  progress.value = 0
  fileName.value = file.name

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
  }
}
</script>
