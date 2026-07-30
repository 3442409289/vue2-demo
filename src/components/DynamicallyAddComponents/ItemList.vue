<template>
  <div class="menu">
    <Item
      v-for="(item, i) in data"
      :key="i"
      @active="$emit('active', item.id)"
      >{{ item.name }}</Item
    >
  </div>
</template>

<script>
import Item from "../Item";
export default {
  components: {
    Item,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style scoped>
.menu {
  background: var(--color7B);
}
</style>