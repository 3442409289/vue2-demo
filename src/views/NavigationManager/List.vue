<template>
  <div>
    <div class="list">
      <Item :data="tableData" />
    </div>
  </div>
</template>

<script>
import Item from "./Item";
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
  data() {
    return {
      tableData: this.data,
    };
  },
};
</script>

<style scoped>
.list {
}
</style>