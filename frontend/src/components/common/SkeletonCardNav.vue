<template>
  <ul class="flex-1 flex justify-center space-x-4 md:space-x-8 text-sm md:leading-[3] animate-pulse">
    <li v-for="i in 5" :key="i">
      <div class="h-4 w-16 bg-gray-300 rounded"></div>
    </li>
  </ul>
</template>