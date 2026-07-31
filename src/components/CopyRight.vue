<template>
  <div
    class="copyright"
    @mouseover="$emit('hover_start')"
    @mouseout="$emit('hover_end')"
    @click="$emit('active')"
  >
    {{ `COPYRIGHT © ${year} - ${getyear} ${text}. ALL RIGHTS RESERVED.` }}
  </div>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default: "XINGXING",
    },
    year: {
      type: Number,
      default: 2021,
    },
  },
  computed: {
    getyear() {
      var date = new Date();
      var year = date.getFullYear();
      return year;
    },
  },
};
</script>

<style scoped>
.copyright {
  width: 100%;
  text-align: center;
  animation-timing-function: linear;
  animation-iteration-count: infinite;
  animation-name: copyright1A;
  animation-duration: 2s;
  z-index: 2;
  transition: 0.5s;
}

.copyright:hover {
  font-size: 1.5em;
  cursor: pointer;
  text-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px #fff, 0 0 20px #fff;
}

@keyframes copyright1A {
  0% {
    color: var(--color2B);
  }

  50% {
    color: var(--color3B);
  }

  100% {
    color: var(--color2B);
  }
}
</style>