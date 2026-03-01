<template>
  <div class="min-h-screen bg-gray-50">
    <TheHeader />
    <main>
      <RouterView />
    </main>
    <TheFooter />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import TheHeader from '@/components/ui/TheHeader.vue'
import TheFooter from '@/components/ui/TheFooter.vue'

const auth = useAuthStore()

onMounted(async () => {
  await auth.fetchUser()
})
</script>
