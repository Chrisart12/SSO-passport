<script setup>
defineProps({
  text: {
    type: String,
    default: "Chargement..."
  }
})
</script>

<template>
  <div class="loader-container">
    <div class="spinner"></div>
    <p>{{ text }}</p>
  </div>
</template>

<style scoped>

.loader-container{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  padding:40px;
}

.spinner{
  width:40px;
  height:40px;
  border:4px solid #eee;
  border-top:4px solid #42b883;
  border-radius:50%;
  animation: spin 1s linear infinite;
}

@keyframes spin{
  0%{
    transform:rotate(0deg);
  }
  100%{
    transform:rotate(360deg);
  }
}

p{
  margin-top:10px;
  font-size:14px;
}

</style>