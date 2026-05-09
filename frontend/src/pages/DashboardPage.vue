<template>
  <q-page class="q-pa-md bg-grey-2">
    <div class="row q-col-gutter-md">
      <div class="col-12">
        <h1 class="text-h4 text-weight-bold q-my-md text-grey-9">Incident Dashboard</h1>
        <p class="text-subtitle1 text-grey-7">Select a category to manage incidents</p>
      </div>

      <!-- Navigation Cards -->
      <div class="col-12 col-sm-6 col-md-4">
        <q-card class="nav-card bg-primary text-white cursor-pointer" @click="$router.push('/incidents')">
          <q-card-section class="flex flex-center q-pa-xl">
            <div class="text-center">
              <q-icon name="assignment" size="4em" />
              <div class="text-h5 q-mt-md">All Incidents</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <q-card class="nav-card bg-orange text-white cursor-pointer" @click="$router.push('/incidents?search=pending')">
          <q-card-section class="flex flex-center q-pa-xl">
            <div class="text-center">
              <q-icon name="pending_actions" size="4em" />
              <div class="text-h5 q-mt-md">Pending</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <q-card class="nav-card bg-blue text-white cursor-pointer" @click="$router.push('/incidents?search=in_progress')">
          <q-card-section class="flex flex-center q-pa-xl">
            <div class="text-center">
              <q-icon name="autorenew" size="4em" />
              <div class="text-h5 q-mt-md">In Progress</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <q-card class="nav-card bg-red text-white cursor-pointer" @click="$router.push('/incidents?search=expired')">
          <q-card-section class="flex flex-center q-pa-xl">
            <div class="text-center">
              <q-icon name="running_with_errors" size="4em" />
              <div class="text-h5 q-mt-md">Expired</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <q-card class="nav-card bg-green text-white cursor-pointer" @click="$router.push('/incidents?search=resolved')">
          <q-card-section class="flex flex-center q-pa-xl">
            <div class="text-center">
              <q-icon name="check_circle" size="4em" />
              <div class="text-h5 q-mt-md">Resolved</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <q-card class="nav-card bg-grey-8 text-white cursor-pointer" @click="$router.push('/incidents?search=closed')">
          <q-card-section class="flex flex-center q-pa-xl">
            <div class="text-center">
              <q-icon name="inventory_2" size="4em" />
              <div class="text-h5 q-mt-md">Closed</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
// No script logic needed for now as we removed the stats fetching
</script>

<style scoped>
.nav-card {
  border-radius: 16px;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.nav-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 25px rgba(0,0,0,0.2);
}
</style>
