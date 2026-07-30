<template>
  <div>
    <Loading v-if="Loading" :animation="animation" />
    <PaperView
      v-else
      ref="paperview"
      :data="componentList"
      :animation="animation"
      @items="items"
      @AnchorReached="handleAnchorReached"
      :RefIndex="RefIndex"
    />
  </div>
</template>

<script>
import MainLayout from "@/components/MainLayout";
import XRMenu from "@/components/XRMenu";
import Music from "@/components/Music";
import CopyRight from "@/components/CopyRight";
import PaperView from "../components/DynamicallyAddComponents/PaperView";
import SideMenu from "../components/SideMenu";
import Loading from "../components/Loading";
import channelServ from "../services/channel";
export default {
  components: {
    MainLayout,
    XRMenu,
    Music,
    CopyRight,
    PaperView,
    SideMenu,
    Loading,
  },
  props: {
    mode: { type: Boolean, default: false },
    url: { type: String, default: "" },
    data: {
      type: Array,
      default: () => [],
    },
    Like: {
      type: Boolean,
      default: false,
    },
    LikeNumber: {
      type: Number,
      default: 0.0,
    },
  },
  data() {
    return {
      componentList: [],
      animation: false,
      isExpand: true,
      musics: undefined,
      xr_data: undefined,
      showSideMenu: false,
      Loading: true,
      RefIndex: 1,
      AnchorPoint: true,
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
        {
          id: 2,
          name: "分享",
          icon: "share",
          is_show: true,
          action: async (e) => {
            await this.$copyText(
              `<iframe src="${this.$el.baseURI}" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe>`
            ).then(
              () => this.$toast({ message: "iframe嵌入代码复制成功" }),
              () => this.$toast({ message: "iframe嵌入代码复制失败" })
            );
          },
        },
        {
          id: 3,
          name: "点赞",
          icon: "LikeNone",
          is_show: true,
          action: (e) => {
            this.$emit("LikeAction");
          },
        },
        {
          id: 4,
          name: "更多",
          icon: "arrowUp2",
          is_show: true,
          childmenu: [
            {
              id: 0,
              name: "H1",
              icon: "AnchorPoint",
              is_show: true,
              action: (e) => {
                this.AnchorPointHover(e);
                this.RefIndex = 0;
                this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                this.$nextTick(() => {
                  this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                });
              },
            },
            {
              id: 1,
              name: "H2",
              icon: "AnchorPointHover",
              is_show: true,
              action: (e) => {
                this.AnchorPointHover(e);
                this.RefIndex = 1;
                this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                this.$nextTick(() => {
                  this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                });
              },
            },
            {
              id: 2,
              name: "H3",
              icon: "AnchorPoint",
              is_show: true,
              action: (e) => {
                this.AnchorPointHover(e);
                this.RefIndex = 2;
                this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                this.$nextTick(() => {
                  this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                });
              },
            },
            {
              id: 3,
              name: "H4",
              icon: "AnchorPoint",
              is_show: true,
              action: (e) => {
                this.AnchorPointHover(e);
                this.RefIndex = 3;
                this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                this.$nextTick(() => {
                  this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                });
              },
            },
            {
              id: 4,
              name: "H5",
              icon: "AnchorPoint",
              is_show: true,
              action: (e) => {
                this.AnchorPointHover(e);
                this.RefIndex = 4;
                this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                this.$nextTick(() => {
                  this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                });
              },
            },
            {
              id: 5,
              name: "H6",
              icon: "AnchorPoint",
              is_show: true,
              action: (e) => {
                this.AnchorPointHover(e);
                this.RefIndex = 5;
                this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                this.$nextTick(() => {
                  this.$refs.paperview.$refs.DynamicallyAddComponents.InitData();
                });
              },
            },
          ],
        },
      ],
      items2: [],
    };
  },
  async created() {
    document.title = "浏览文章";
    if (this.$globalSwitch.DarkMode.isDarkMode) {
      this.items1[1].name = "开灯";
      this.items1[1].icon = "TurnOffTheLights2";
    } else {
      this.items1[1].name = "关灯";
      this.items1[1].icon = "TurnOnTheLight2";
    }
    await this.InitData();
    this.UpdateLike(this.Like, this.LikeNumber);
  },
  methods: {
    async InitData() {
      this.Loading = true;

      await this.$nextTick(); // 等待 DOM 更新完成

      const cacheKey = "music_data";
      const cachedData = await this.getLargeSessionStorage(cacheKey);

      if (cachedData) {
        const { musics, xr_data } = cachedData;
        this.musics = musics;
        this.xr_data = xr_data;
      } else {
        this.musics = await channelServ.getChannels("/music/index.php?mode=0");
        this.xr_data = await channelServ.getChannels("/1.json");
        sessionStorage.setItem(
          cacheKey,
          JSON.stringify({
            musics: this.musics,
            xr_data: this.xr_data,
          })
        );
      }

      if (this.mode) {
        this.componentList = this.data;
      } else {
        const url = this.url;
        const cachedData2 = await this.getLargeSessionStorage(url);

        if (cachedData2) {
          const { componentList } = cachedData2;
          this.componentList = componentList;
        } else {
          this.componentList = await channelServ.getChannels(url);
          sessionStorage.setItem(
            url,
            JSON.stringify({
              componentList: this.componentList,
            })
          );
        }
      }

      await this.$nextTick(); // 等待 DOM 更新完成

      this.Loading = false;
    },
    active() {
      this.$emit("active");
    },
    hover_start() {
      this.$emit("hover_start");
      this.animation = true;
    },
    hover_end() {
      this.$emit("hover_end");
      this.animation = false;
    },
    handleAnchorReached(e) {
      this.$emit("handleAnchorReached", e);
    },
    test(e) {
      console.log(e);
    },
    async getLargeSessionStorage(key) {
      return new Promise((resolve) => {
        setTimeout(() => {
          const data = sessionStorage.getItem(key);
          resolve(data ? JSON.parse(data) : null);
        }, 0);
      });
    },
    AnchorPointHover(e) {
      const childmenu = this.items1.filter(
        (item) => item.childmenu !== undefined
      );
      const matchedItems = childmenu[0].childmenu.filter(
        (item) =>
          item.icon === "AnchorPointHover" || item.icon === "AnchorPoint"
      );
      matchedItems.forEach((item) => {
        item.icon = "AnchorPoint";
        if (item.id === e) {
          item.icon = "AnchorPointHover";
        }
      });
    },
    items(e) {
      this.items2 = e;
      this.$emit("items1", this.items1);
      this.$emit("items2", this.items2);
      this.$emit("top", "100px");
    },
    LikeNumberToText(e) {
      if (e < 10000) {
        return e.toString();
      } else {
        const numInTenThousands = e / 10000;
        const formattedNum = parseFloat(numInTenThousands.toFixed(1));
        return formattedNum + "万";
      }
    },
    UpdateLike(like, number) {
      this.items1[3].icon = like ? "Like" : "LikeNone";
      this.items1[3].name =
        number <= 0.0 ? "点赞" : this.LikeNumberToText(number);
      this.$emit("items1", this.items1);
    },
  },
  beforeDestroy() {
    // Vue 2
    this.$emit("items1", undefined);
    this.$emit("items2", undefined);
    this.$emit("top", "auto");
    this.$emit("update_My_black_and_white");
  },
};
</script>

<style>
</style>