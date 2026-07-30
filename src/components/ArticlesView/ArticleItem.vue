<template>
  <div class="item">
    <router-link
      class="button"
      :to="{ name: 'PaperViewId', params: { id: data.id } }"
      ><div>
        <h2>{{ data.title }}</h2>
        <span>作者:{{ data.author_name }}</span>
        <span>时间:{{ data.created_at }}</span>
      </div>
    </router-link>
  </div>
</template>

<script>
export default {
  props: {
    data: { type: Object, required: true },
  },
  created() {},
};
</script>

<style scoped>
@media screen and (min-width: 769px) {
  .item a div,
  .item .button div {
    font-size: 16px;
  }
}
@media screen and (max-width: 768px) {
  .item a div,
  .item .button div {
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
a:-webkit-any-link:focus-visible div {
  color: var(--color1D);
}
.item {
  width: 100%;
  height: 100%;
  padding: 10px 0;
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
.item a:hover div,
.item .button:hover div {
  transition: 0.5s;
  color: var(--bg-color6B);
  border: var(--border_width1A) solid var(--bg-color6B);
  letter-spacing: 0em;
  box-shadow: 0 0 40px var(--bg-color6B);
}
.item a:hover div::before,
.item .button:hover div::before {
  left: 20%;
}
.item a:hover div::after,
.item .button:hover div::after {
  left: 75%;
}
.item a div,
.item .button div {
  position: relative;
  display: flex;
  word-break: break-all;
  transition: all 0.5s, background-color 0.3s ease, color 0s;
  color: var(--text-color);
  height: 100%;
  border: var(--border_width1A) solid var(--bg-color6B);
  box-sizing: border-box;
  padding: 10px 10px;
  letter-spacing: 0.1em;
  border-radius: 10px;
  flex-direction: column;
  align-items: flex-start;
  background: var(--bg-color2A);
}
.item a div::before,
.item .button div::before {
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
.item a div::after,
.item .button div::after {
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