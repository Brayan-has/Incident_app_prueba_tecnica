<template>
  <q-layout>
    <q-page-container>
      <q-page class="flex flex-center login-page">
        <div class="login-container">
          <q-card class="login-card glass-card">
            <q-card-section class="text-center">
              <div class="text-h4 text-weight-bold text-primary q-mb-md">Welcome Back</div>
              <div class="text-subtitle2 text-grey-7">Please sign in to continue</div>
            </q-card-section>

            <q-card-section>
              <q-form @submit="onSubmit" class="q-gutter-md">
                <q-input
                  filled
                  v-model="email"
                  label="Email"
                  type="email"
                  lazy-rules
                  :rules="[ val => val && val.length > 0 || 'Please type your email']"
                >
                  <template v-slot:prepend>
                    <q-icon name="email" />
                  </template>
                </q-input>

                <q-input
                  filled
                  v-model="password"
                  label="Password"
                  type="password"
                  lazy-rules
                  :rules="[ val => val && val.length > 0 || 'Please type your password']"
                >
                  <template v-slot:prepend>
                    <q-icon name="lock" />
                  </template>
                </q-input>

                <div class="q-mt-lg">
                  <q-btn
                    label="Sign In"
                    type="submit"
                    color="primary"
                    class="full-width login-btn"
                    size="lg"
                    unelevated
                    :loading="loading"
                  />
                </div>
              </q-form>
            </q-card-section>
          </q-card>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useQuasar } from 'quasar'

const email = ref('test@gmail.com')
const password = ref('123456')
const loading = ref(false)
const router = useRouter()
const auth = useAuthStore()
const $q = useQuasar()

async function onSubmit () {
  loading.value = true
  
  try {
    await auth.login({ 
      email: email.value, 
      password: password.value 
    })
    
    $q.notify({
      color: 'positive',
      message: 'Login successful',
      icon: 'check_circle'
    })
    router.push('/')
  } catch (error) {
    // Error is handled by axios interceptor notification
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.login-container {
  width: 100%;
  max-width: 400px;
  padding: 20px;
}

.glass-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.login-btn {
  border-radius: 12px;
  text-transform: none;
  font-weight: 600;
}
</style>
