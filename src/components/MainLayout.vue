<template>
  <div class="main-layout">
    <header class="head">
      <slot name="head"></slot>
    </header>
    <div class="mid">
      <slot name="mid"></slot>
    </div>
    <div class="bottom">
      <slot name="bottom"></slot>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style scoped>
.main-layout {
  user-select: none;
  width: 100%;
  height: 100%;
  position: fixed;
  left: 0;
  top: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.main-layout .head {
  display: flex;
  flex-direction: column;
  width: 100%;
  z-index: 1;
  transition: 1s;
  opacity: var(--bg-opacity);
}
.main-layout .mid {
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex: 1;
}
.main-layout .bottom {
  display: flex;
}
</style>