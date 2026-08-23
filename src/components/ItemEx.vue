<template>
  <div class="item" :style="{ fontSize: `${fontSize}px` }" @click="handleClick">
    <!-- 插槽 -->
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    fontSize: {
      type: Number,
    },
  },
  methods: {
    handleClick() {
      this.$emit("click");
    },
  },
};
</script>

<style scoped>
@media screen and (min-width: 769px) {
  .item {
    font-size: 16px;
  }
}
@media screen and (max-width: 768px) {
  .item {
    font-size: 12px;
  }
}
.item {
  width: 100%;
  height: 100%;
  transition: 0.5s;
  text-align: center;
  cursor: pointer;
  position: relative;
  color: var(--text-color);
  border: var(--border_width1A) solid var(--bg-color6B);
  box-sizing: border-box;
  padding: 6px;
  letter-spacing: 0.1em;
  border-radius: 10px;
  user-select: none;
}
.item:hover {
  transition: 0.5s;
  color: var(--bg-color6B);
  border: var(--border_width1A) solid var(--bg-color6B);
  letter-spacing: 0em;
  box-shadow: 0 0 40px var(--bg-color6B);
}
.item:hover::before {
  left: 20%;
}
.item:hover::after {
  left: 75%;
}
.item::before {
  transition: 0.3s;
  position: absolute;
  content: "";
  top: calc(var(--border_width1A) * -1);
  left: 75%;
  width: 15%;
  background-color: var(--bg-color5B);
  height: var(--border_width1A);
}
.item::after {
  transition: 0.3s;
  position: absolute;
  content: "";
  bottom: calc(var(--border_width1A) * -1);
  left: 20%;
  width: 15%;
  background-color: var(--bg-color5B);
  height: var(--border_width1A);
}
</style>