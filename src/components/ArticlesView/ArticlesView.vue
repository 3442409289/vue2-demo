<template>
  <div class="articles-paper">
    <div class="articles-list">
      <div v-for="item in list" :key="item.id">
        <ArticleItem :data="item" />
      </div>
    </div>
    <el-pagination background layout="prev, pager, next" :total="total_pages">
    </el-pagination>
  </div>
</template>

<script>
import ArticleItem from "./ArticleItem";
import api from "@/utils/api";
export default {
  components: {
    ArticleItem,
  },
  props: {
    url: { type: Number, default: 1 },
  },
  data() {
    return {
      total_pages: 0,
      list: [],
    };
  },
  async created() {
    this.$emit("items");
    const response = await api.post("/mysql/index.php", {
      mode: "ArticlesView",
      type: 0,
      page: this.url,
    });
    this.total_pages = response.data.total_pages;
    this.list = response.data.data;
  },
};
</script>

<style scoped>
.articles-paper {
  position: relative;
  left: 50%;
  transform: translateX(-50%);
}

@media screen and (min-width: 769px) {
  .articles-paper {
    padding: 4em var(--Layout-LeftOfRightPadding);
  }
}

@media screen and (max-width: 768px) {
  .articles-paper {
    padding: 4em 1em;
  }
}
</style>