<template>
  <h5 class="xr-h5" :style="{ color }">
    {{ data }}
  </h5>
</template>

<script>
export default {
  props: {
    data: {
      type: String,
      default: "文字",
    },
    color: {
      type: String,
      default: "#ffcb21",
    },
  },
};
</script>

<style scoped>
.xr-h5 {
  text-shadow: 0 0 5px var(--text-luminescence),
    0 0 10px var(--text-luminescence), 0 0 15px var(--text-luminescence),
    0 0 20px var(--text-luminescence);
}
</style>