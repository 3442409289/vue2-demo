<template>
  <div class="button">{{ name }}</div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      default: "文字",
    },
  },
};
</script>

<style scoped>
.button {
  position: relative;
  padding: 10px 30px;
  color: #21ebff;
  text-decoration: none;
  font-size: 20px;
  text-align: center;
  overflow: hidden;
  transition: 0.5s;
  -webkit-box-reflect: below 1px linear-gradient(transparent, #0003);
}
.button::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 10px;
  height: 10px;
  border-left: 2px solid #21ebff;
  border-top: 2px solid #21ebff;
  transition: 0.5s;
}
.button::after {
  content: "";
  position: absolute;
  bottom: 0;
  right: 0;
  width: 10px;
  height: 10px;
  border-right: 2px solid #21ebff;
  border-bottom: 2px solid #21ebff;
  transition: 0.5s;
}
.button:hover {
  cursor: pointer;
  background-color: #21ebff;
  color: #111;
  box-shadow: 0 0 50px #21ebff;
  transition-delay: 0.5s;
}
.button:hover::before,
.button:hover::after {
  width: 100%;
  height: 100%;
}
</style>