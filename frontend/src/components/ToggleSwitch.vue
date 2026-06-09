<template>
  <button type="button" @click="$emit('update:modelValue', !modelValue)"
    :style="{
      width:'36px', height:'20px', borderRadius:'999px', border:'none', cursor:'pointer',
      background: modelValue ? '#6D4EE8' : '#d1d5db', position:'relative', transition:'background 0.2s', flexShrink:0
    }">
    <div :style="{
      width:'14px', height:'14px', borderRadius:'50%', background:'#fff',
      position:'absolute', top:'3px', transition:'left 0.2s',
      left: modelValue ? '19px' : '3px', boxShadow:'0 1px 3px rgba(0,0,0,0.2)'
    }"></div>
  </button>
</template>

<script setup>
defineProps(['modelValue'])
defineEmits(['update:modelValue'])
</script>
