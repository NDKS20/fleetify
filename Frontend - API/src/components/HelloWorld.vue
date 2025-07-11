<script setup lang="ts">
defineProps<{
  msg: string
}>()
</script>

<template>
  <div class="greetings">
    <h1
      class="text-4xl font-medium text-green-600 dark:text-green-400 relative -top-2.5 text-center lg:text-left"
    >
      {{ msg }}
    </h1>
    <h3 class="text-xl text-gray-600 dark:text-gray-300 text-center lg:text-left">
      You've successfully created a project with
      <a
        href="https://vite.dev/"
        target="_blank"
        rel="noopener"
        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
        >Vite</a
      >
      +
      <a
        href="https://vuejs.org/"
        target="_blank"
        rel="noopener"
        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
        >Vue 3</a
      >. What's next?
    </h3>
  </div>
</template>
