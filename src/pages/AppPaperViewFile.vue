<template>
  <div class="appPaperViewFile">
    <div v-if="data.length === 0" class="file">
      <input type="file" @change="handleFileChange" />
    </div>
    <AppPaperView
      v-else
      ref="AppPaperView"
      :mode="true"
      :data="data"
      @items1="$emit('items1', $event)"
      @items2="$emit('items2', $event)"
      @top="$emit('top', $event)"
      @scrollToTop="$emit('scrollToTop')"
      @handleAnchorReached="$emit('handleAnchorReached', $event)"
    />
  </div>
</template>

<script>
import AppPaperView from "./AppPaperView";
export default {
  components: {
    AppPaperView,
  },
  data() {
    return {
      data: [],
    };
  },
  methods: {
    handleFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          const data = e.target.result;
          const json = JSON.parse(data);
          this.data = json; // 文件内容（支持中文编码）
        };
        reader.readAsText(file, "UTF-8"); // 指定编码读取中文
      }
    },
  },
};
</script>

<style scoped>
.appPaperViewFile {
  width: 100%;
  height: 100%;
}

.appPaperViewFile .file {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>