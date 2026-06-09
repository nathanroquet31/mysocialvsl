<template>
  <div style="position:relative;display:inline-flex;align-items:center" @mouseenter="show=true" @mouseleave="show=false">
    <button type="button"
      :style="{width:'16px',height:'16px',borderRadius:'50%',background:show?'#6D4EE8':'#E5E7EB',border:'none',cursor:'pointer',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'10px',fontWeight:'700',color:show?'#fff':'#6B7280',flexShrink:0,transition:'background 0.15s',padding:'0'}">
      ?
    </button>
    <div v-if="show"
      style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);z-index:200;max-width:240px;min-width:180px;background:#1F2937;color:#F9FAFB;font-size:12px;line-height:1.5;padding:8px 12px;border-radius:8px;pointer-events:none;box-shadow:0 4px 12px rgba(0,0,0,0.25);white-space:normal;text-align:left">
      {{ text }}
      <div style="position:absolute;top:100%;left:50%;transform:translateX(-50%);border:4px solid transparent;border-top-color:#1F2937"></div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
defineProps({ text: { type: String, required: true } })
const show = ref(false)
</script>
