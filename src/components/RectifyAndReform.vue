<template>
  <div
    class="rectify_and_reform"
    @click="active"
    :style="{ display: `${is_show ? 'flex' : 'none'}` }"
  >
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    version: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      is_show: true,
    };
  },
  created() {
    const cacheKey = "RectifyAndReform";
    const RectifyAndReform = JSON.parse(sessionStorage.getItem(cacheKey));
    if (RectifyAndReform !== null) {
      this.is_show = RectifyAndReform;
    }
  },
  methods: {
    active() {
      if (this.is_show) {
        this.$confirm("测试版本" + this.version + "\n是否测试", "警告", {
          confirmButtonText: "是",
          cancelButtonText: "否",
          type: "warning",
        })
          .then(() => {
            // 用户点击是
            this.is_show = false;
            const cacheKey = "RectifyAndReform";
            sessionStorage.setItem(cacheKey, JSON.stringify(this.is_show));
          })
          .catch(() => {
            // 用户点击否
          });
      }
    },
  },
};
</script>

<style scoped>
.rectify_and_reform {
  display: flex;
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1;
  background-color: var(--bg-color2A);
  text-align: center;
  justify-content: center;
  align-items: center;
  font-size: 2em;
  color: var(--text-color2);
}
</style>