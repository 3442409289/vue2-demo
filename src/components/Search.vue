<template>
  <form class="search-area" @submit.prevent="handleSearch">
    <input type="text" v-model="searchValue" :placeholder="placeHolder" />
    <div class="icon" @click="handleSearch">
      <Icon type="search" />
    </div>
  </form>
</template>

<script>
import Icon from "./Icon";
export default {
  components: {
    Icon,
  },
  props: {
    placeHolder: {
      type: String,
      default: "搜索关键字",
    },
  },
  data() {
    return {
      searchValue: "",
    };
  },
  methods: {
    handleSearch() {
      if (this.searchValue) {
        this.$emit("Search", this.searchValue);
      }
    },
  },
};
</script>

<style scoped>
.search-area {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  border: 1px solid var(--color3B);
  border-radius: 2px;
  position: relative;
}
.search-area input {
  border: none;
  outline: none;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  padding-left: 8px;
  padding-right: 45px;
  padding-top: 0;
  padding-bottom: 0;
  background: var(--bg-color1);
}
.search-area input::placeholder {
  color: #999;
}
.icon {
  position: relative;
  width: 40px;
  height: 100%;
  left: calc(100% - 40px);
  bottom: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c0c0c0;
  cursor: pointer;
}
</style>