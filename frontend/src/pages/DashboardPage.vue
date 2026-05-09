<template>
  <q-page class="dashboard-page q-pa-lg">

    <!-- Header -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <p class="text-overline text-primary q-mb-xs" style="letter-spacing:2px">OVERVIEW</p>
        <h1 class="text-h4 text-weight-bold text-grey-9 q-ma-none">Incident Dashboard</h1>
        <p class="text-grey-6 q-mt-xs q-mb-none">Real-time system status and metrics</p>
      </div>
      <div class="col-auto">
        <q-btn round flat icon="refresh" color="grey-7" :loading="loading" @click="fetchDashboard">
          <q-tooltip>Refresh</q-tooltip>
        </q-btn>
      </div>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="row q-col-gutter-lg q-mb-xl">
      <div v-for="i in 4" :key="i" class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card"><q-skeleton type="rect" height="120px" /></div>
      </div>
    </div>

    <!-- Stat Cards -->
    <div v-else class="row q-col-gutter-lg q-mb-xl">

      <!-- Total -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card cursor-pointer" @click="$router.push('/incidents')">
          <div class="stat-icon-wrap" style="background:#ede9fe">
            <q-icon name="layers" size="26px" style="color:#7c3aed" />
          </div>
          <div class="stat-value" style="color:#7c3aed">{{ stats.totalIncidents }}</div>
          <div class="stat-label">Total Incidents</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:100%;background:linear-gradient(90deg,#7c3aed,#a78bfa)"></div></div>
        </div>
      </div>

      <!-- Pending -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card cursor-pointer" @click="$router.push('/incidents?search=pending')">
          <div class="stat-icon-wrap" style="background:#fef3c7">
            <q-icon name="pending_actions" size="26px" style="color:#b45309" />
          </div>
          <div class="stat-value" style="color:#b45309">{{ stats.totalPendingIncidents }}</div>
          <div class="stat-label">Pending</div>
          <div class="stat-bar">
            <div class="stat-bar-fill" :style="{ width: pct(stats.totalPendingIncidents), background:'linear-gradient(90deg,#b45309,#fbbf24)' }"></div>
          </div>
        </div>
      </div>

      <!-- In Progress -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card cursor-pointer" @click="$router.push('/incidents?search=in_progress')">
          <div class="stat-icon-wrap" style="background:#dbeafe">
            <q-icon name="autorenew" size="26px" style="color:#1d4ed8" />
          </div>
          <div class="stat-value" style="color:#1d4ed8">{{ stats.totalInProgressIncidents }}</div>
          <div class="stat-label">In Progress</div>
          <div class="stat-bar">
            <div class="stat-bar-fill" :style="{ width: pct(stats.totalInProgressIncidents), background:'linear-gradient(90deg,#1d4ed8,#60a5fa)' }"></div>
          </div>
        </div>
      </div>

      <!-- Resolved -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card cursor-pointer" @click="$router.push('/incidents?search=resolved')">
          <div class="stat-icon-wrap" style="background:#dcfce7">
            <q-icon name="check_circle" size="26px" style="color:#15803d" />
          </div>
          <div class="stat-value" style="color:#15803d">{{ stats.totalResolvedIncidents }}</div>
          <div class="stat-label">Resolved</div>
          <div class="stat-bar">
            <div class="stat-bar-fill" :style="{ width: pct(stats.totalResolvedIncidents), background:'linear-gradient(90deg,#15803d,#4ade80)' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Breakdown + Donut -->
    <div v-if="!loading && stats.totalIncidents > 0" class="row q-col-gutter-lg q-mb-xl">
      <div class="col-12 col-md-7">
        <div class="info-card q-pa-lg">
          <p class="section-label">STATUS BREAKDOWN</p>
          <div class="breakdown-list">
            <div class="breakdown-row" v-for="row in breakdownRows" :key="row.label">
              <div class="breakdown-left">
                <q-icon :name="row.icon" size="18px" :style="`color:${row.color}`" />
                <span class="breakdown-label">{{ row.label }}</span>
              </div>
              <div class="breakdown-bar-wrap">
                <div class="breakdown-bar" :style="`width:${rowPct(row.value)}%; background:${row.gradient}`"></div>
              </div>
              <div class="breakdown-pct">{{ rowPct(row.value) }}%</div>
              <div class="breakdown-count">{{ row.value }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-5">
        <div class="info-card q-pa-lg column justify-between" style="height:100%">
          <p class="section-label">RESOLUTION RATE</p>
          <div class="flex flex-center column" style="flex:1">
            <div class="donut-wrap">
              <svg viewBox="0 0 120 120" class="donut-svg">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#f0fdf4" stroke-width="14" />
                <circle
                  cx="60" cy="60" r="48"
                  fill="none"
                  stroke="url(#greenGrad)"
                  stroke-width="14"
                  stroke-linecap="round"
                  stroke-dasharray="301.6"
                  :stroke-dashoffset="dashOffset"
                  transform="rotate(-90 60 60)"
                  style="transition:stroke-dashoffset 1s ease"
                />
                <defs>
                  <linearGradient id="greenGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#15803d" />
                    <stop offset="100%" stop-color="#4ade80" />
                  </linearGradient>
                </defs>
              </svg>
              <div class="donut-label">
                <div class="donut-pct">{{ resolutionRate }}%</div>
                <div class="donut-sub">Resolved</div>
              </div>
            </div>
          </div>
          <div class="text-center text-grey-6 text-caption q-mt-md">
            {{ stats.totalResolvedIncidents }} of {{ stats.totalIncidents }} incidents resolved
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <p class="section-label q-mb-md">QUICK ACTIONS</p>
    <div class="row q-col-gutter-lg">
      <div class="col-12 col-sm-6 col-md-3" v-for="action in quickActions" :key="action.label">
        <div class="quick-card" @click="$router.push(action.route)">
          <q-icon :name="action.icon" size="2em" :style="`color:${action.color}`" />
          <div class="quick-label">{{ action.label }}</div>
          <q-icon name="arrow_forward" size="1em" class="quick-arrow" />
        </div>
      </div>
    </div>

  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { IncidentService } from '../api/services'

const loading = ref(true)
const stats = ref({
  totalIncidents: 0,
  totalPendingIncidents: 0,
  totalInProgressIncidents: 0,
  totalResolvedIncidents: 0,
})

// % of total
const pct = (val) =>
  stats.value.totalIncidents > 0
    ? `${Math.round((val / stats.value.totalIncidents) * 100)}`
    : '0'

const rowPct = (val) =>
  stats.value.totalIncidents > 0
    ? Math.round((val / stats.value.totalIncidents) * 100)
    : 0

const resolutionRate = computed(() =>
  stats.value.totalIncidents > 0
    ? Math.round((stats.value.totalResolvedIncidents / stats.value.totalIncidents) * 100)
    : 0
)
const dashOffset = computed(() => 301.6 - (301.6 * resolutionRate.value) / 100)

const breakdownRows = computed(() => [
  {
    label: 'Pending',
    icon: 'pending_actions',
    color: '#b45309',
    gradient: 'linear-gradient(90deg,#b45309,#fbbf24)',
    value: stats.value.totalPendingIncidents,
  },
  {
    label: 'In Progress',
    icon: 'autorenew',
    color: '#1d4ed8',
    gradient: 'linear-gradient(90deg,#1d4ed8,#60a5fa)',
    value: stats.value.totalInProgressIncidents,
  },
  {
    label: 'Resolved',
    icon: 'check_circle',
    color: '#15803d',
    gradient: 'linear-gradient(90deg,#15803d,#4ade80)',
    value: stats.value.totalResolvedIncidents,
  },
])

const quickActions = [
  { label: 'All Incidents',      icon: 'assignment',       color: '#7c3aed', route: '/incidents' },
  { label: 'Pending',            icon: 'pending_actions',  color: '#b45309', route: '/incidents?search=pending' },
  { label: 'In Progress',        icon: 'autorenew',        color: '#1d4ed8', route: '/incidents?search=in_progress' },
  { label: 'Resolved',           icon: 'check_circle',     color: '#15803d', route: '/incidents?search=resolved' },
]

const fetchDashboard = async () => {
  loading.value = true
  try {
    const res = await IncidentService.getDashboard()
    stats.value = res.data
  } catch (e) {
    console.error('Dashboard fetch error:', e)
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboard)
</script>

<style scoped>
.dashboard-page { min-height: 100vh; background: #f8fafc; }

.section-label {
  font-size: 0.7rem;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #94a3b8;
  margin: 0 0 16px;
}

/* Stat cards */
.stat-card {
  border-radius: 18px;
  padding: 22px 20px 16px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  transition: transform 0.22s ease, box-shadow 0.22s ease;
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.1);
}
.stat-icon-wrap {
  width: 46px; height: 46px;
  border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 14px;
}
.stat-value { font-size: 2.4rem; font-weight: 800; line-height: 1; margin-bottom: 4px; }
.stat-label {
  font-size: 0.78rem; letter-spacing: 1px; text-transform: uppercase;
  color: #94a3b8; margin-bottom: 16px;
}
.stat-bar { height: 5px; border-radius: 5px; background: #f1f5f9; overflow: hidden; }
.stat-bar-fill { height: 100%; border-radius: 5px; transition: width 1s ease; }

/* Info card */
.info-card {
  border-radius: 18px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

/* Breakdown */
.breakdown-list { display: flex; flex-direction: column; gap: 20px; }
.breakdown-row  { display: flex; align-items: center; gap: 12px; }
.breakdown-left { display: flex; align-items: center; gap: 8px; min-width: 110px; }
.breakdown-label { font-size: 0.85rem; color: #475569; font-weight: 500; }
.breakdown-bar-wrap { flex: 1; height: 8px; background: #f1f5f9; border-radius: 8px; overflow: hidden; }
.breakdown-bar { height: 100%; border-radius: 8px; transition: width 1s ease; }
.breakdown-pct   { font-size: 0.78rem; color: #94a3b8; min-width: 38px; text-align: right; }
.breakdown-count { font-size: 0.85rem; font-weight: 700; color: #1e293b; min-width: 28px; text-align: right; }

/* Donut */
.donut-wrap { position: relative; width: 160px; height: 160px; }
.donut-svg { width: 100%; height: 100%; }
.donut-label {
  position: absolute; inset: 0;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.donut-pct { font-size: 2rem; font-weight: 800; color: #1e293b; line-height: 1; }
.donut-sub { font-size: 0.65rem; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8; }

/* Quick actions */
.quick-card {
  display: flex; align-items: center; gap: 14px;
  padding: 16px 18px;
  border-radius: 14px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  cursor: pointer;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.quick-card:hover { transform: translateX(4px); box-shadow: 0 6px 20px rgba(0,0,0,0.09); }
.quick-label { flex: 1; color: #334155; font-weight: 600; font-size: 0.9rem; }
.quick-arrow { color: #cbd5e1; }
</style>
