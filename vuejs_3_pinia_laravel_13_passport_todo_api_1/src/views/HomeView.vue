<script setup>

import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

// const counterStore = useCounterStore()
const authStore = useAuthStore()


</script>

<template>
  <div >
    <div class="home">
      <div v-if="authStore.isAuthenticated">
        <h2>Welcome, {{ authStore.user.name }}!</h2>
        <p>You are logged in.</p>
      </div>
      <!-- <h1 class="count">{{ counterStore.count }}</h1> -->
      <button class="btn" @click="counterStore.increment" type="button">+</button>
      <button class="btn" @click="counterStore.decrement" type="button">-</button>
    </div>
    
    <hr>
    <!-- <div> The counter value is: {{ counterStore.oddOrEven }}</div> -->
  </div>
</template>
<style>
.home {
  display: flex;
  align-items: center;
  justify-content: center;
}
.count {
  font-size: 5rem;
}
.btn {
  margin-left: 10px;
  font-size: 1rem;
  padding: 0.5rem 1rem;
}
</style>
<!-- <style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style> -->
