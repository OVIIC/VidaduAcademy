<template>
  <div v-if="showDashboard" class="performance-dashboard">
    <div class="dashboard-header">
      <h3>🚀 Performance Dashboard</h3>
      <button @click="toggleDashboard" class="close-btn">&times;</button>
    </div>
    
    <div class="dashboard-content">
      <!-- Core Web Vitals -->
      <div class="metrics-section">
        <h4>Core Web Vitals</h4>
        <div class="metrics-grid">
          <div class="metric-card" :class="getScoreClass(vitals.lcp)">
            <div class="metric-label">LCP</div>
            <div class="metric-value">{{ vitals.lcp }}ms</div>
            <div class="metric-description">Largest Contentful Paint</div>
          </div>
          <div class="metric-card" :class="getScoreClass(vitals.fid)">
            <div class="metric-label">FID</div>
            <div class="metric-value">{{ vitals.fid }}ms</div>
            <div class="metric-description">First Input Delay</div>
          </div>
          <div class="metric-card" :class="getScoreClass(vitals.cls * 1000)">
            <div class="metric-label">CLS</div>
            <div class="metric-value">{{ vitals.cls.toFixed(3) }}</div>
            <div class="metric-description">Cumulative Layout Shift</div>
          </div>
        </div>
      </div>

      <!-- Memory Usage -->
      <div class="metrics-section" v-if="memory">
        <h4>Memory Usage</h4>
        <div class="memory-info">
          <div class="memory-bar">
            <div 
              class="memory-used" 
              :style="{ width: (memory.used / memory.total * 100) + '%' }"
            ></div>
          </div>
          <div class="memory-stats">
            <span>{{ memory.used }}MB / {{ memory.total }}MB</span>
            <span class="memory-limit">(limit: {{ memory.limit }}MB)</span>
          </div>
        </div>
      </div>

      <!-- Recent Measurements -->
      <div class="metrics-section">
        <h4>Recent Performance Metrics</h4>
        <div class="metrics-list">
          <div 
            v-for="metric in recentMetrics.slice(0, 10)" 
            :key="metric.timestamp"
            class="metric-item"
          >
            <span class="metric-name">{{ metric.name }}</span>
            <span class="metric-timing">{{ metric.value.toFixed(2) }}ms</span>
            <span class="metric-time">{{ formatTime(metric.timestamp) }}</span>
          </div>
        </div>
      </div>

      <!-- Cache Stats -->
      <div class="metrics-section" v-if="cacheHitRate">
        <h4>Cache Performance</h4>
        <div class="cache-stats">
          <div class="cache-metric">
            <span class="cache-label">Hit Rate:</span>
            <span class="cache-value">{{ cacheHitRate }}%</span>
          </div>
          <div class="cache-metric">
            <span class="cache-label">Cached Requests:</span>
            <span class="cache-value">{{ cachedRequests }}</span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="dashboard-actions">
        <button @click="clearMetrics" class="action-btn clear-btn">
          Clear Metrics
        </button>
        <button @click="downloadMetrics" class="action-btn download-btn">
          Download Data
        </button>
        <button @click="refreshStats" class="action-btn refresh-btn">
          Refresh
        </button>
      </div>
    </div>
  </div>

  <!-- Toggle Button -->
  <button 
    v-else
    @click="toggleDashboard" 
    class="performance-toggle"
    title="Show Performance Dashboard"
  >
    📊
  </button>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { usePerformance } from '@/utils/performanceMonitor'

const { getMetrics, clearMetrics: clearStoredMetrics } = usePerformance()

const showDashboard = ref(false)
const vitals = ref({
  lcp: 0,
  fid: 0,
  cls: 0
})
const memory = ref(null)
const recentMetrics = ref([])
const cacheHitRate = ref(0)
const cachedRequests = ref(0)

const toggleDashboard = () => {
  showDashboard.value = !showDashboard.value
  if (showDashboard.value) {
    refreshStats()
  }
}

const refreshStats = () => {
  // Get performance metrics
  recentMetrics.value = getMetrics()
  
  // Get memory info
  if (performance.memory) {
    memory.value = {
      used: Math.round(performance.memory.usedJSHeapSize / 1024 / 1024),
      total: Math.round(performance.memory.totalJSHeapSize / 1024 / 1024),
      limit: Math.round(performance.memory.jsHeapSizeLimit / 1024 / 1024)
    }
  }

  // Calculate cache stats from metrics
  const apiMetrics = recentMetrics.value.filter(m => m.name.startsWith('API:'))
  const cachedApiCalls = apiMetrics.filter(m => m.value < 100) // Assume < 100ms is cached
  cacheHitRate.value = apiMetrics.length > 0 ? Math.round((cachedApiCalls.length / apiMetrics.length) * 100) : 0
  cachedRequests.value = cachedApiCalls.length

  // Update vitals from stored metrics
  const lcp = recentMetrics.value.find(m => m.name === 'LCP')
  const fid = recentMetrics.value.find(m => m.name === 'FID')  
  const cls = recentMetrics.value.find(m => m.name === 'CLS')
  
  if (lcp) vitals.value.lcp = lcp.value
  if (fid) vitals.value.fid = fid.value
  if (cls) vitals.value.cls = cls.value
}

const getScoreClass = (value) => {
  if (value === 0) return 'score-unknown'
  if (value < 100) return 'score-good'
  if (value < 300) return 'score-needs-improvement'
  return 'score-poor'
}

const formatTime = (timestamp) => {
  const now = Date.now()
  const diff = now - timestamp
  if (diff < 60000) return `${Math.round(diff / 1000)}s ago`
  if (diff < 3600000) return `${Math.round(diff / 60000)}m ago`
  return `${Math.round(diff / 3600000)}h ago`
}

const clearMetrics = () => {
  clearStoredMetrics()
  recentMetrics.value = []
  vitals.value = { lcp: 0, fid: 0, cls: 0 }
}

const downloadMetrics = () => {
  const data = {
    timestamp: new Date().toISOString(),
    vitals: vitals.value,
    memory: memory.value,
    metrics: recentMetrics.value
  }
  
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `performance-data-${Date.now()}.json`
  a.click()
  URL.revokeObjectURL(url)
}

// Auto-refresh every 30 seconds when dashboard is open
let refreshInterval = null

onMounted(() => {
  refreshStats()
})

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})

// Watch for dashboard visibility to start/stop auto-refresh
const startAutoRefresh = () => {
  if (!refreshInterval) {
    refreshInterval = setInterval(refreshStats, 30000)
  }
}

const stopAutoRefresh = () => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
    refreshInterval = null
  }
}

// Start/stop auto-refresh based on dashboard visibility
// Start/stop auto-refresh based on dashboard visibility
watch(showDashboard, (newValue) => {
  if (newValue) {
    startAutoRefresh()
  } else {
    stopAutoRefresh()
  }
})
</script>

<style scoped>
.performance-dashboard {
  position: fixed;
  top: 20px;
  right: 20px;
  width: 400px;
  max-height: 80vh;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  overflow: hidden;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.dashboard-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #374151;
}

.dashboard-content {
  padding: 16px;
  max-height: calc(80vh - 60px);
  overflow-y: auto;
}

.metrics-section {
  margin-bottom: 24px;
}

.metrics-section h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.metric-card {
  text-align: center;
  padding: 12px 8px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.score-good {
  background: #f0f9f0;
  border-color: #22c55e;
}

.score-needs-improvement {
  background: #fffbeb;
  border-color: #f59e0b;
}

.score-poor {
  background: #fef2f2;
  border-color: #ef4444;
}

.score-unknown {
  background: #f8fafc;
  border-color: #e2e8f0;
}

.metric-label {
  font-weight: 600;
  font-size: 12px;
  color: #374151;
}

.metric-value {
  font-size: 18px;
  font-weight: 700;
  margin: 4px 0;
  color: #111827;
}

.metric-description {
  font-size: 10px;
  color: #6b7280;
}

.memory-info {
  background: #f9fafb;
  padding: 12px;
  border-radius: 6px;
}

.memory-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.memory-used {
  height: 100%;
  background: linear-gradient(90deg, #22c55e, #f59e0b, #ef4444);
  transition: width 0.3s ease;
}

.memory-stats {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #374151;
}

.memory-limit {
  color: #6b7280;
}

.metrics-list {
  max-height: 200px;
  overflow-y: auto;
}

.metric-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
  font-size: 12px;
}

.metric-item:last-child {
  border-bottom: none;
}

.metric-name {
  flex: 1;
  color: #374151;
  font-weight: 500;
}

.metric-timing {
  color: #111827;
  font-weight: 600;
  margin: 0 12px;
}

.metric-time {
  color: #6b7280;
  font-size: 11px;
}

.cache-stats {
  display: flex;
  gap: 16px;
}

.cache-metric {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 8px;
  background: #f9fafb;
  border-radius: 6px;
  flex: 1;
}

.cache-label {
  font-size: 11px;
  color: #6b7280;
  margin-bottom: 4px;
}

.cache-value {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.dashboard-actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.action-btn {
  flex: 1;
  padding: 8px 12px;
  font-size: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.clear-btn {
  background: #fef2f2;
  color: #dc2626;
  border-color: #fecaca;
}

.clear-btn:hover {
  background: #fee2e2;
}

.download-btn {
  background: #eff6ff;
  color: #2563eb;
  border-color: #dbeafe;
}

.download-btn:hover {
  background: #dbeafe;
}

.refresh-btn {
  background: #f0f9f0;
  color: #16a34a;
  border-color: #bbf7d0;
}

.refresh-btn:hover {
  background: #dcfce7;
}

.performance-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #3b82f6;
  color: white;
  border: none;
  font-size: 20px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  z-index: 1000;
  transition: all 0.2s;
}

.performance-toggle:hover {
  background: #2563eb;
  transform: scale(1.05);
}

/* Development only - hide in production */
@media (max-width: 768px) {
  .performance-dashboard {
    width: calc(100vw - 40px);
    right: 20px;
    left: 20px;
  }
}
</style>
