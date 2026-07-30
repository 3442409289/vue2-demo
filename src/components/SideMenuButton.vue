<template>
  <div class="button" @click="$emit('active')">
    <div class="icon">
      <Icon :type="icon" extraClass="icon" />
    </div>
    <span class="text">{{ data }}</span>
  </div>
</template>

<script>
import Item from "./Item";
import Icon from "./Icon";
export default {
  components: {
    Item,
    Icon,
  },
  props: {
    data: {
      type: String,
      default: "文字",
    },
    icon: {
      type: String,
      default: "arrowUp",
    },
  },
};
</script>

<style scoped>
.button {
  margin-top: 6px;
  min-height: 50px;
  width: 50px;
  user-select: auto;
  display: flex;
  border-radius: 8px;
  font-size: 12px;
  color: var(--text-color);
  border: var(--border_width1A) solid var(--bg-color6B);
  background-color: var(--bg-color1);
  transform-origin: center;
  transition: 0.2s;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  box-sizing: border-box;
  position: relative;
  letter-spacing: 0.1em;
}

.button::before {
  transition: 0.3s;
  position: absolute;
  content: "";
  top: calc(var(--border_width1A) * -1);
  left: 75%;
  width: 15%;
  background-color: var(--bg-color5B);
  height: var(--border_width1A);
  transform: skewX(-50deg);
}

.button::after {
  transition: 0.3s;
  position: absolute;
  content: "";
  bottom: calc(var(--border_width1A) * -1);
  left: 20%;
  width: 15%;
  background-color: var(--bg-color5B);
  height: var(--border_width1A);
  transform: skewX(-50deg);
}

.button:hover {
  color: var(--bg-color6B);
  border: var(--border_width1A) solid var(--bg-color6B);
  letter-spacing: 0em;
}

.button:hover::before {
  left: 20%;
}

.button:hover::after {
  left: 75%;
}

.button:active {
  background-color: var(--color3B);
  transform: scale(0.95) translateZ(0);
}

.button .icon {
  width: 16px;
  height: 16px;
  font-size: 16px;
}

.button .text {
  margin-top: 2px;
  text-align: center;
  font-size: 12px;
  line-height: 14px;
  word-break: break-all;
  white-space: normal;
}
</style>