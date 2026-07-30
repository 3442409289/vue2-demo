<template>
  <div class="appArticlesView">
    <ArticlesView :page="page" @items="items" />
  </div>
</template>

<script>
import ArticlesView from "@/components/ArticlesView/ArticlesView";
export default {
  components: {
    ArticlesView,
  },
  props: {
    page: { type: Number, default: 1 },
  },
  data() {
    return {
      items1: [
        {
          id: 0,
          name: "顶部",
          icon: "arrowUp2",
          is_show: false,
          action: (e) => {
            this.$emit("scrollToTop");
          },
        },
        {
          id: 1,
          name: "关灯",
          icon: "TurnOnTheLight2",
          is_show: true,
          action: (e) => {
            this.$globalSwitch.DarkMode.toggle();
            this.$nextTick(() => {
              if (this.$globalSwitch.DarkMode.isDarkMode) {
                this.items1[e].name = "开灯";
                this.items1[e].icon = "TurnOffTheLights2";
              } else {
                this.items1[e].name = "关灯";
                this.items1[e].icon = "TurnOnTheLight2";
              }
            });
          },
        },
      ],
      items2: undefined,
    };
  },
  created() {
    document.title = "文章列表";
    if (this.$globalSwitch.DarkMode.isDarkMode) {
      this.items1[1].name = "开灯";
      this.items1[1].icon = "TurnOffTheLights2";
    } else {
      this.items1[1].name = "关灯";
      this.items1[1].icon = "TurnOnTheLight2";
    }
  },
  methods: {
    items() {
      this.$nextTick(() => {
        this.$emit("items1", this.items1);
        this.$emit("items2", this.items2);
      });
    },
  },
};
</script>

<style scoped>
.appArticlesView {
  width: 100%;
  height: 100%;
}
</style>