<script setup>
defineProps({
  type: {
    type: String,
    default: "submit",
  },
});
</script>

<template>
  <button
    :type="type"
    class="text-white bg-gray-800 hover:bg-gray-900 dark: focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-base px-5 py-4 mr-2 mb-2 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-800 border border-gray-300 dark:border-gray-700 transition-colors duration-300"
  >
    <slot />
  </button>
</template>
