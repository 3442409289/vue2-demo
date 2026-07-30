<template>
  <a class="link" :href="data.src" :target="`${data.blank ? '_blank' : ''}`">
    {{ data.title }}
  </a>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
    },
  },
};
</script>

<style>
.link {
  text-decoration: none;
  margin-top: 6px;
  min-height: 50px;
  user-select: auto;
  display: flex;
  border-radius: 8px;
  font-size: 20px;
  color: var(--text-color);
  border: var(--border_width1A) solid var(--bg-color6B);
  background-color: var(--bg-color1);
  transform-origin: center;
  transition: 0.2s;
  cursor: pointer;
  justify-content: center;
  flex-direction: column;
  box-sizing: border-box;
  position: relative;
  letter-spacing: 0.1em;
}

.link::before {
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

.link::after {
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

.link:hover {
  color: var(--bg-color6B);
  border: var(--border_width1A) solid var(--bg-color6B);
  letter-spacing: 0em;
}

.link:hover::before {
  left: 20%;
}

.link:hover::after {
  left: 75%;
}

.link:active {
  background-color: var(--color3B);
  transform: scale(0.95) translateZ(0);
}
</style>