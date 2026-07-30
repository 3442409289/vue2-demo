<template>
  <div class="xr-temp" v-html="data"></div>
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

<style>
.xr-temp table {
  margin: 10px 0 15px 0;
  border-collapse: collapse;
  border-spacing: 0;
  display: block;
  width: 100%;
  overflow: auto;
  word-break: normal;
  word-break: keep-all;
}
.xr-temp table th,
.xr-temp table td {
  border: 1px solid var(--color3B);
  padding: 0;
  margin: 0;
  width: 1%;
  text-align: center;
  background-color: var(--bg-color4);
  white-space: nowrap;
}
.xr-temp hr {
  height: 1px;
  padding: 0;
  margin: 0;
  border: 0 none;
  background-color: var(--color3B);
}

.xr-temp iframe {
  width: 100%;
  height: 500px;
}

.xr-temp .xr-video {
  width: 100%;
}

.xr-temp video {
  width: 100%;
}

.xr-temp img {
  width: 100%;
}
</style>