<template>
  <div class="smokey-bg">
    <canvas ref="canvasRef" class="smokey-canvas"></canvas>
    <div class="smokey-blur" :style="{ backdropFilter: `blur(${blur}px)` }"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  color: { type: String, default: '#6D4EE8' }, // brand violet
  blur: { type: Number, default: 6 },
})

const canvasRef = ref(null)
let rafId = null
let cleanupListeners = null

const vertexSrc = `
  attribute vec4 a_position;
  void main() { gl_Position = a_position; }
`

const fragmentSrc = `
precision mediump float;
uniform vec2 iResolution;
uniform float iTime;
uniform vec2 iMouse;
uniform vec3 u_color;
void mainImage(out vec4 fragColor, in vec2 fragCoord){
  vec2 centeredUV = (2.0 * fragCoord - iResolution.xy) / min(iResolution.x, iResolution.y);
  float time = iTime * 0.5;
  vec2 mouse = iMouse / iResolution;
  vec2 rippleCenter = 2.0 * mouse - 1.0;
  vec2 distortion = centeredUV;
  for (float i = 1.0; i < 8.0; i++) {
    distortion.x += 0.5 / i * cos(i * 2.0 * distortion.y + time + rippleCenter.x * 3.1415);
    distortion.y += 0.5 / i * cos(i * 2.0 * distortion.x + time + rippleCenter.y * 3.1415);
  }
  float wave = abs(sin(distortion.x + distortion.y + time));
  float glow = smoothstep(0.9, 0.2, wave);
  fragColor = vec4(u_color * glow, 1.0);
}
void main() { mainImage(gl_FragColor, gl_FragCoord.xy); }
`

function hexToRgb(hex) {
  return [
    parseInt(hex.substring(1, 3), 16) / 255,
    parseInt(hex.substring(3, 5), 16) / 255,
    parseInt(hex.substring(5, 7), 16) / 255,
  ]
}

onMounted(() => {
  const canvas = canvasRef.value
  if (!canvas) return
  const gl = canvas.getContext('webgl')
  if (!gl) { console.error('WebGL not supported'); return }

  const compile = (type, src) => {
    const sh = gl.createShader(type)
    gl.shaderSource(sh, src)
    gl.compileShader(sh)
    if (!gl.getShaderParameter(sh, gl.COMPILE_STATUS)) {
      console.error('Shader error:', gl.getShaderInfoLog(sh))
      gl.deleteShader(sh)
      return null
    }
    return sh
  }

  const vs = compile(gl.VERTEX_SHADER, vertexSrc)
  const fs = compile(gl.FRAGMENT_SHADER, fragmentSrc)
  if (!vs || !fs) return

  const program = gl.createProgram()
  gl.attachShader(program, vs)
  gl.attachShader(program, fs)
  gl.linkProgram(program)
  if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
    console.error('Program link error:', gl.getProgramInfoLog(program))
    return
  }
  gl.useProgram(program)

  const buffer = gl.createBuffer()
  gl.bindBuffer(gl.ARRAY_BUFFER, buffer)
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1]), gl.STATIC_DRAW)
  const posLoc = gl.getAttribLocation(program, 'a_position')
  gl.enableVertexAttribArray(posLoc)
  gl.vertexAttribPointer(posLoc, 2, gl.FLOAT, false, 0, 0)

  const uRes = gl.getUniformLocation(program, 'iResolution')
  const uTime = gl.getUniformLocation(program, 'iTime')
  const uMouse = gl.getUniformLocation(program, 'iMouse')
  const uColor = gl.getUniformLocation(program, 'u_color')

  const [r, g, b] = hexToRgb(props.color)
  gl.uniform3f(uColor, r, g, b)

  const start = Date.now()
  let mouse = { x: 0, y: 0 }
  let hovering = false

  const onMove = (e) => {
    const rect = canvas.getBoundingClientRect()
    mouse = { x: e.clientX - rect.left, y: e.clientY - rect.top }
  }
  const onEnter = () => { hovering = true }
  const onLeave = () => { hovering = false }
  canvas.addEventListener('mousemove', onMove)
  canvas.addEventListener('mouseenter', onEnter)
  canvas.addEventListener('mouseleave', onLeave)
  cleanupListeners = () => {
    canvas.removeEventListener('mousemove', onMove)
    canvas.removeEventListener('mouseenter', onEnter)
    canvas.removeEventListener('mouseleave', onLeave)
  }

  const render = () => {
    const w = canvas.clientWidth
    const h = canvas.clientHeight
    canvas.width = w
    canvas.height = h
    gl.viewport(0, 0, w, h)
    gl.uniform2f(uRes, w, h)
    gl.uniform1f(uTime, (Date.now() - start) / 1000)
    gl.uniform2f(uMouse, hovering ? mouse.x : w / 2, hovering ? h - mouse.y : h / 2)
    gl.drawArrays(gl.TRIANGLES, 0, 6)
    rafId = requestAnimationFrame(render)
  }
  render()
})

onBeforeUnmount(() => {
  if (rafId) cancelAnimationFrame(rafId)
  if (cleanupListeners) cleanupListeners()
})
</script>

<style scoped>
.smokey-bg { position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; }
.smokey-canvas { width: 100%; height: 100%; display: block; }
.smokey-blur { position: absolute; inset: 0; }

/* Light theme: the shader's base is opaque black, so fade it to a faint violet
   shimmer and let the light page background show through. */
[data-theme="light"] .smokey-canvas { opacity: 0.13; }
</style>
