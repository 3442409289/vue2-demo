<template>
  <div class="item">
    <a v-if="!isRouter" :href="url" :target="`${blank ? '_blank' : ''}`">
      <span>{{ name }}</span>
    </a>
    <router-link class="button" v-else :to="{ name: url, params: params }"
      ><span>{{ name }}</span>
    </router-link>
  </div>
</template>

<script>
export default {
  props: {
    isRouter: {
      type: Boolean,
      default: false,
    },
    params: {
      type: Object,
    },
    url: {
      type: String,
    },
    blank: {
      type: Boolean,
      default: false,
    },
    name: {
      type: String,
      default: "文字",
    },
  },
};
</script>

<style scoped>
@media screen and (min-width: 769px) {
  .item a span,
  .item .button span {
    font-size: 16px;
  }
}
@media screen and (max-width: 768px) {
  .item a span,
  .item .button span {
    font-size: 12px;
  }
}
a {
  text-decoration: none;
  color: inherit;
  cursor: auto;
}
a:-webkit-any-link:focus-visible {
  outline: none;
  background-color: var(--color1B);
}
a:-webkit-any-link:focus-visible span {
  color: var(--color1D);
}
.item {
  width: 100%;
  height: 100%;
}
.item a,
.item .button {
  display: block;
  text-decoration: none;
  transition: 0.5s;
  text-align: center;
  cursor: pointer;
  height: 100%;
}
.item a:hover span,
.item .button:hover span {
  transition: 0.5s;
  color: var(--bg-color6B);
  border: var(--border_width1A) solid var(--bg-color6B);
  letter-spacing: 0em;
  box-shadow: 0 0 40px var(--bg-color6B);
}
.item a:hover span::before,
.item .button:hover span::before {
  left: 20%;
}
.item a:hover span::after,
.item .button:hover span::after {
  left: 75%;
}
.item a span,
.item .button span {
  position: relative;
  display: flex;
  word-break: break-all;
  transition: all 0.5s, background-color 0.3s ease, color 0s;
  color: var(--text-color);
  height: 100%;
  justify-content: center;
  align-items: center;
  border: var(--border_width1A) solid var(--bg-color6B);
  box-sizing: border-box;
  padding: 10px 10px;
  letter-spacing: 0.1em;
  border-radius: 10px;
}
.item a span::before,
.item .button span::before {
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
.item a span::after,
.item .button span::after {
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
</style>