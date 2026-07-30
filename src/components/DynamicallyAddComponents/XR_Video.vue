<template>
  <video class="xr-video" :src="data" controls></video>
</template>

<script>
export default {
  props: {
    data: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped>
.xr-video {
  width: 100%;
  height: 500px;
}
</style>