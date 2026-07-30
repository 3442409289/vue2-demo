<template>
  <transition name="modal-fade">
    <div v-if="show" class="modal-mask">
      <div class="modal-box">
        <slot></slot>
      </div>
    </div>
  </transition>
</template>
 
<script>
export default {
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
};
</script>
 
<style scoped>
.modal-mask {
  position: fixed;
  z-index: 10;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  transition: opacity 0.3s ease;
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-box {
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: Helvetica, Arial, sans-serif;
}
</style>