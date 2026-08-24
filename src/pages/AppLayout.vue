<template>
  <MainLayout>
    <template v-slot:head>
      <XRMenuEx>
        <Music v-if="musics !== undefined" :data="musics" />
      </XRMenuEx>
      <Marquee v-if="xr_data !== undefined" :text="marqueeText" />
    </template>
    <template v-slot:mid>
      <BackgroundVideo
        ref="background"
        v-if="xr_data !== undefined"
        :src="xr_data.BackgroundVideo"
        :animation="animation"
        @XR_scroll="handleScroll"
      >
        <router-view
          ref="router_view"
          @hover_start="hover_start"
          @hover_end="hover_end"
          :animation="animation"
          @items1="items1Function"
          @items2="items2Function"
          @scrollToTop="scrollToTop"
          @update_My_black_and_white="update_My_black_and_white"
          @handleAnchorReached="handleAnchorReached"
          @top="top = $event"
        />
      </BackgroundVideo>
      <Loading v-else />
      <Canvas :is_animation="is_animation" :is_love="is_love" />
      <RectifyAndReform
        v-if="xr_data !== undefined"
        :version="xr_data.vue_version"
        >{{ xr_data.vue_text
        }}<a href="/#/AppLayout/LogComponent/log.json" button
          >更新日志</a
        ></RectifyAndReform
      >
      <SideMenu
        v-if="items1 !== undefined"
        :is_show="showSideMenu"
        :data="items1"
        @active="executeFunction"
        :top="top"
      />
      <SideMenu
        v-if="items2 !== undefined"
        ref="sidemenu"
        :is_show="showSideMenu"
        :data="items2"
        @active="executeFunction"
        :position="true"
        :order="true"
      />
    </template>
    <template v-slot:bottom>
      <CopyRight
        @hover_start="hover_start"
        @hover_end="hover_end"
        @active="active"
      />
    </template>
  </MainLayout>
</template>

<script>
import Background from "@/components/Background";
import BackgroundVideo from "@/components/BackgroundVideo";
import MainLayout from "@/components/MainLayout";
import XRMenu from "@/components/XRMenu";
import XRMenuEx from "@/components/XRMenuEx";
import Music from "@/components/Music";
import MusicEx from "@/components/MusicEx";
import CopyRight from "@/components/CopyRight";
import Canvas from "@/components/Canvas";
import SideMenu from "@/components/SideMenu";
import Marquee from "@/components/Marquee";
import RectifyAndReform from "@/components/RectifyAndReform";
import Loading from "../components/Loading";
import channelServ from "@/services/channel";
import api from "@/utils/api";
export default {
  components: {
    Background,
    BackgroundVideo,
    MainLayout,
    XRMenu,
    XRMenuEx,
    Music,
    MusicEx,
    CopyRight,
    Canvas,
    SideMenu,
    Marquee,
    RectifyAndReform,
    Loading,
  },
  data() {
    return {
      text: "是否开启特效，如果开启则无法关闭刷新页面可重置为初始化状态\n并且开启特效会占用大量CPU资源 ≈ 30%\n该数据来自 Intel(R) Core(TM) I5-4690 CPU @ 3.50GHz",
      isExpand: true,
      musics: undefined,
      xr_data: undefined,
      is_love: false,
      is_animation: false,
      animation: true,
      showSideMenu: false,
      items1: undefined,
      items2: undefined,
      top: "auto",
      marqueeText:
        "富强 民主 文明 和谐 自由 平等 公正 法治 爱国 敬业 诚信 友善",
    };
  },
  async created() {
    await this.InitData();
  },
  watch: {
    // 监听路由变化（页面切换时触发）
    $route(to, from) {
      if (to.name === "AppMain") {
        this.animation = true;
      } else {
        this.animation = false;
      }
    },
  },
  methods: {
    async InitData() {
      this.Loading = true;

      const baseUrl = window.location.protocol + "//" + window.location.host;

      const cacheKey = "music_data";
      const cacheKey2 = "RectifyAndReform";
      const cachedData = await this.getLargeSessionStorage(cacheKey);

      if (cachedData) {
        const { musics, xr_data } = cachedData;
        this.musics = musics;
        this.xr_data = xr_data;
        this.marqueeText = this.xr_data.marqueeText;
      } else {
        this.musics = await channelServ.getChannels("/music/index.php?mode=0");

        const response = await api.post("/mysql/index.php", {
          mode: "JsonArraysTable",
          type: 0,
          id: 1,
        });
        this.xr_data = response.data[0];
        this.marqueeText = this.xr_data.marqueeText;

        sessionStorage.setItem(
          cacheKey,
          JSON.stringify({
            musics: this.musics,
            xr_data: this.xr_data,
            baseUrl: baseUrl,
          })
        );
        this.$XR_version(this.xr_data.vue_version);
      }

      sessionStorage.setItem(cacheKey2, this.xr_data.is_show);
      document.title = this.xr_data.title;
      this.Loading = false;
    },
    active() {
      this.$emit("active");
      if (!this.is_animation) {
        this.$confirm(this.text.replace(/\n/g, "<br>"), "警告 特效测试", {
          confirmButtonText: "是",
          cancelButtonText: "否",
          type: "warning",
          dangerouslyUseHTMLString: true, // 关键：允许解析HTML标签
        })
          .then(() => {
            // 用户点击是
            this.is_animation = true;
          })
          .catch(() => {
            // 用户点击否
          });
      }
    },
    hover_start() {
      this.$emit("hover_start");
      this.animation = true;
      this.is_love = true;
      this.$globalSwitch.BgOpacity.toggle();
    },
    hover_end() {
      this.$emit("hover_end");
      if (this.$route.name === "AppMain") {
        this.animation = true;
      } else {
        this.animation = false;
      }
      this.is_love = false;
      this.$globalSwitch.BgOpacity.toggle();
    },
    handleScroll(e) {
      this.showSideMenu = e > 200; // 滚动超过200px时显示按钮
      if (this.$refs.router_view.$refs.paperview) {
        this.$refs.router_view.$refs.paperview.handleScroll(e);
      } else if (this.$refs.router_view.$refs.AppPaperView) {
        if (this.$refs.router_view.$refs.AppPaperView.$refs.paperview) {
          this.$refs.router_view.$refs.AppPaperView.$refs.paperview.handleScroll(
            e
          );
        }
      }
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
      const matchedItems = this.items.filter(
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
    executeFunction({ id, array }) {
      let scrollTimer;
      const item = array.find((item) => item.id === id);
      if (item && item.action) {
        this.AnchorPoint = false;
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
          this.AnchorPoint = true;
        }, 1000);
        item.action(id);
      } // 执行对应 ID 的函数
    },
    items1Function(e) {
      this.items1 = e;
    },
    items2Function(e) {
      this.items2 = e;
    },
    scrollToTop() {
      this.$refs.background.scrollToTop();
    },
    handleAnchorReached(e) {
      this.items2.forEach((item, index) => {
        if (index === e) {
          item.icon = "AnchorPointHover";
          if (this.AnchorPoint) {
            this.$refs.sidemenu.$children[e].$el.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
          }
        } else {
          item.icon = "AnchorPoint";
        }
      });
    },
    update_My_black_and_white() {
      this.$refs.background.Set_My_black_and_white(1);
    },
  },
};
</script>

<style>
</style>