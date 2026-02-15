<template>
  <section class="relative w-full min-h-[90vh] flex items-center z-10 pt-20">
    <canvas ref="canvasRef" class="absolute inset-0 w-full h-full pointer-events-none z-0 blur-[3px] opacity-70"></canvas>
    
    <!-- Content -->
    <div class="relative container-responsive w-full px-4 sm:px-6 lg:px-8 z-10 pointer-events-none">
        <div class="max-w-4xl mx-auto text-center pointer-events-auto">
        <div class="melting-text-container mb-8">
          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight tracking-tight drop-shadow-2xl mb-4">
            <span class="block text-white mb-2">Dominuj na YouTube</span>
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-primary-500 to-secondary-500 animate-gradient-x">
              so svojim kanálom
            </span>
          </h1>
        </div>
        
        <p class="text-xl sm:text-2xl text-dark-200 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200 font-extralight">
          Tvor obsah ako skutočný kreatívec.
          <br class="hidden sm:block" />
          VidaduAcademy ťa naučí presné postupy, ktoré používa top <span class="text-primary-400 font-black">1 %</span> online tvorcov.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-5 justify-center items-center animate-fade-in-up delay-300">
          <router-link to="/courses" class="group relative px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-primary-500/25 transform hover:-translate-y-1 overflow-hidden">
            <span class="relative z-10 flex items-center gap-2">
              Začni študovať
              <ArrowRightIcon class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-primary-500 via-white/20 to-primary-500 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-in-out"></div>
          </router-link>
          
          <a href="#how-it-works" class="px-8 py-4 bg-dark-800/50 hover:bg-dark-800 text-white font-medium rounded-2xl border border-dark-700 hover:border-dark-600 transition-all duration-300 backdrop-blur-sm flex items-center gap-2">
            <PlayCircleIcon class="w-6 h-6 text-dark-300" />
            Ako to funguje
          </a>
        </div>

        <!-- Stats Quick View -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 pt-8 border-t border-dark-800/50 animate-fade-in-up delay-500">
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">1000+</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Študentov</div>
          </div>
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">50+</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Hodín obsahu</div>
          </div>
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">15+</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Expertov</div>
          </div>
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">4.9/5</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Hodnotenie</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { ArrowRightIcon, PlayCircleIcon } from '@heroicons/vue/24/outline'

const canvasRef = ref(null);
let animationFrameId = null;
let gl = null;
let program = null;
let resizeCanvas = null;

const vertexShaderSource = `
  attribute vec2 position;
  void main() {
      gl_Position = vec4(position, 0.0, 1.0);
  }
`;

const fragmentShaderSource = `
  precision highp float;
 
  uniform float uTime;
  uniform vec2 uResolution;
  uniform vec2 uMouse;
 
  #define PI 3.14159265359
  #define TAU 6.28318530718
  #define MAX_STEPS 80
  #define MAX_DIST 50.0
  #define SURF_DIST 0.001
 
  float hash(float n) {
      return fract(sin(n) * 43758.5453123);
  }
 
  mat2 rot(float a) {
      float s = sin(a);
      float c = cos(a);
      return mat2(c, -s, s, c);
  }
 
  float sdOctahedron(vec3 p, float s) {
      p = abs(p);
      float m = p.x + p.y + p.z - s;
      vec3 q;
      if(3.0 * p.x < m) q = p.xyz;
      else if(3.0 * p.y < m) q = p.yzx;
      else if(3.0 * p.z < m) q = p.zxy;
      else return m * 0.57735027;
     
      float k = clamp(0.5 * (q.z - q.y + s), 0.0, s);
      return length(vec3(q.x, q.y - s + k, q.z - k));
  }
  float sdTriPrism(vec3 p, vec2 h) {
      vec3 q = abs(p);
      return max(q.z - h.y, max(q.x * 0.866025 + p.y * 0.5, -p.y) - h.x * 0.5);
  }
 
  float smin(float a, float b, float k) {
      float h = clamp(0.5 + 0.5 * (b - a) / k, 0.0, 1.0);
      return mix(b, a, h) - k * h * (1.0 - h);
  }
  float smax(float a, float b, float k) {
      return -smin(-a, -b, k);
  }
 
  float map(vec3 p) {
      vec3 op = p;
     
      vec2 m = (uMouse - 0.5) * 2.5;
      p.xy += m * 0.4;
     
      p.xz *= rot(uTime * 0.12);
      // p.xy *= rot(uTime * 0.08);
      
      float d = 100.0;
      
      vec3 p1 = p;
      p1.y += sin(uTime * 1.5) * 0.2; // Added vertical pulse
      // p1.yz *= rot(uTime * 0.15);
     
      float core_distort = sin(p1.x * 3.0 + uTime) * sin(p1.y * 3.0 + uTime) * sin(p1.z * 3.0 + uTime) * 0.1;
      float core = sdOctahedron(p1, 1.6) + core_distort;
     
      vec3 p2 = p1;
      p2.xy *= rot(PI * 0.25 + uTime * 0.2);
      float prism = sdTriPrism(p2, vec2(1.4, 2.0));
      core = smax(core, -prism, 0.2);
     
      d = core;
     
      float k_blend = 0.2 + 0.15 * (0.5 + 0.5 * sin(uTime * 1.5));
     
      for(int i = 0; i < 4; i++) {
          float fi = float(i);
          float angle = fi * TAU / 4.0 + sin(uTime * 0.3) * 2.0;
         
          float radius = 3.0 + 0.3 * sin(uTime * 0.4 + fi);
         
          vec3 pos = vec3(
              cos(angle) * radius,
              abs(sin(angle * 0.7)) * 0.6 + 0.8,
              sin(angle) * radius
          );
         
          vec3 po = p - pos;
          po.xy *= rot(uTime * 0.5 + fi);
         
          float sat_distort = sin(po.x * 5.0 + fi) * sin(po.y * 5.0 + fi) * sin(po.z * 5.0 + fi) * 0.05;
          float satellite = sdOctahedron(po, 0.4) + sat_distort;
         
          d = smin(d, satellite, k_blend);
      }
     
      return d;
  }
 
  vec3 getNormal(vec3 p) {
      vec2 e = vec2(0.001, 0.0);
      return normalize(vec3(
          map(p + e.xyy) - map(p - e.xyy),
          map(p + e.yxy) - map(p - e.yxy),
          map(p + e.yyx) - map(p - e.yyx)
      ));
  }
 
  float raymarch(vec3 ro, vec3 rd) {
      float t = 0.0;
      for(int i = 0; i < MAX_STEPS; i++) {
          vec3 p = ro + rd * t;
          float d = map(p);
          if(abs(d) < SURF_DIST || t > MAX_DIST) break;
          t += d * 0.7;
      }
      return t;
  }
 
  void main() {
      vec2 uv = (gl_FragCoord.xy - 0.5 * uResolution) / min(uResolution.x, uResolution.y);
     
      vec2 m = (uMouse - 0.5) * 0.5;
      vec3 ro = vec3(m.x * 2.0, m.y * 2.0, 5.5);
      vec3 rd = normalize(vec3(uv, -1.0));
     
      rd.xy *= rot(m.x * 0.2);
      rd.yz *= rot(m.y * 0.2);
     
      float t = raymarch(ro, rd);
     
      vec4 finalColor = vec4(0.0); // Default to transparent
     
      if(t < MAX_DIST) {
          vec3 p = ro + rd * t;
          vec3 normal = getNormal(p);
         
          vec3 viewDir = normalize(ro - p);
          float fresnel = pow(1.0 - max(dot(viewDir, normal), 0.0), 3.0);
          
          // Simplified lighting for transparent integration
          vec3 lightDir = normalize(vec3(1.0, 1.0, -1.0));
          vec3 halfDir = normalize(lightDir + viewDir);
          float spec = pow(max(dot(normal, halfDir), 0.0), 100.0);
          
          vec3 baseColor = vec3(0.1, 0.05, 0.2); // Dark base
          vec3 fresnelColor = vec3(
              0.5 + 0.5 * sin(fresnel * TAU + uTime),
              0.5 + 0.5 * sin(fresnel * TAU + uTime + TAU / 3.0),
              0.5 + 0.5 * sin(fresnel * TAU + uTime + TAU * 2.0 / 3.0)
          );
          
          vec3 color = baseColor + fresnel * fresnelColor * 1.5 + spec * vec3(1.0);
          
          // Distance fog to blend smoothly
          float fog = 1.0 - exp(-t * 0.05);
          color = mix(color, vec3(0.0), fog);
          
          float alpha = 0.3 + 0.6 * fresnel; // Liquid glass alpha
          finalColor = vec4(color, alpha);
      }
     
      gl_FragColor = finalColor;
  }
`;

function createShader(gl, type, source) {
  const shader = gl.createShader(type);
  gl.shaderSource(shader, source);
  gl.compileShader(shader);
 
  if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
      console.error('Shader compile error:', gl.getShaderInfoLog(shader));
      gl.deleteShader(shader);
      return null;
  }
  return shader;
}

function createProgram(gl, vertexShader, fragmentShader) {
  const program = gl.createProgram();
  gl.attachShader(program, vertexShader);
  gl.attachShader(program, fragmentShader);
  gl.linkProgram(program);
 
  if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
      console.error('Program link error:', gl.getProgramInfoLog(program));
      gl.deleteProgram(program);
      return null;
  }
  return program;
}

onMounted(() => {
  const canvas = canvasRef.value;
  gl = canvas.getContext('webgl', { alpha: true }) || canvas.getContext('experimental-webgl', { alpha: true });
  if (!gl) {
      console.error('WebGL not supported');
      return;
  }

  gl.enable(gl.BLEND);
  gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);

  resizeCanvas = () => {
    if (!canvas) return;
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    gl.viewport(0, 0, canvas.width, canvas.height);
  };
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  const vertexShader = createShader(gl, gl.VERTEX_SHADER, vertexShaderSource);
  const fragmentShader = createShader(gl, gl.FRAGMENT_SHADER, fragmentShaderSource);
  program = createProgram(gl, vertexShader, fragmentShader);

  if (!program) return;

  const uTime = gl.getUniformLocation(program, 'uTime');
  const uResolution = gl.getUniformLocation(program, 'uResolution');
  const uMouse = gl.getUniformLocation(program, 'uMouse');

  const positions = new Float32Array([
      -1, -1,
       1, -1,
      -1, 1,
       1, 1,
  ]);
  const positionBuffer = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
  gl.bufferData(gl.ARRAY_BUFFER, positions, gl.STATIC_DRAW);

  const mouse = { x: 0.5, y: 0.5, targetX: 0.5, targetY: 0.5 };
 
  window.addEventListener('mousemove', (e) => {
      mouse.targetX = e.clientX / window.innerWidth;
      mouse.targetY = 1.0 - e.clientY / window.innerHeight;
  });

  const startTime = Date.now();
  const render = () => {
      const currentTime = (Date.now() - startTime) * 0.001;
     
      mouse.x += (mouse.targetX - mouse.x) * 0.05;
      mouse.y += (mouse.targetY - mouse.y) * 0.05;
     
      // Clear with transparency
      gl.clearColor(0.0, 0.0, 0.0, 0.0);
      gl.clear(gl.COLOR_BUFFER_BIT);
      gl.useProgram(program);
     
      gl.uniform1f(uTime, currentTime);
      gl.uniform2f(uResolution, canvas.width, canvas.height);
      gl.uniform2f(uMouse, mouse.x, mouse.y);
     
      const positionLocation = gl.getAttribLocation(program, 'position');
      gl.enableVertexAttribArray(positionLocation);
      gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
      gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);
     
      gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
     
      animationFrameId = requestAnimationFrame(render);
  };
  render();
});

onBeforeUnmount(() => {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
  }
  if (resizeCanvas) {
    window.removeEventListener('resize', resizeCanvas);
  }
});
</script>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.8s ease-out forwards;
  opacity: 0;
  transform: translateY(20px);
}

.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-500 { animation-delay: 0.5s; }

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradientX 4s ease infinite;
}

@keyframes gradientX {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>
 
