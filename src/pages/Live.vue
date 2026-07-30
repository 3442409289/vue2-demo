<template>
  <div class="live">
    <div>
      <Search @Search="handleSearch" placeHolder="媒体链接" />
    </div>
  </div>
</template>

<script>
import Search from "@/components/Search";
export default {
  components: {
    Search,
  },
  created() {
    document.title = "观看媒体";
  },
  methods: {
    handleSearch(e) {
      this.$router.push({ name: "XRArtPlayer", params: { url: e } });
    },
  },
};
</script>

<style scoped>
.live {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  flex-direction: column;
}
</style>