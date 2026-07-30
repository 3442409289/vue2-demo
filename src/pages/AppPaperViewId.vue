<template>
  <div class="appPaperViewId">
    <div v-if="author_UserInfo !== undefined">
      <div class="user-info">
        <XR_H5 :data="'作者 ' + author_UserInfo.username" />
        <XR_H5 :data="'时间 ' + author_UserInfo.created_at" />
      </div>
      <AppPaperView
        :key="componentKey"
        ref="AppPaperView"
        :mode="true"
        :data="data"
        :Like="Like"
        :LikeNumber="LikeNumber"
        @items1="$emit('items1', $event)"
        @items2="$emit('items2', $event)"
        @top="$emit('top', $event)"
        @scrollToTop="$emit('scrollToTop')"
        @handleAnchorReached="$emit('handleAnchorReached', $event)"
        @LikeAction="LikeAction"
      />
    </div>
    <Loading v-else />
  </div>
</template>

<script>
import AppPaperView from "./AppPaperView";
import Loading from "../components/Loading";
import XR_H5 from "@/components/DynamicallyAddComponents/XR_H5";
import api from "@/utils/api";
import { mapGetters } from "vuex";
import Store from "@/store"; // 确保路径正确
export default {
  components: {
    AppPaperView,
    Loading,
    XR_H5,
  },
  props: {
    id: { type: Number, required: true },
  },
  data() {
    return {
      author_id: 0,
      author_UserInfo: undefined,
      data: undefined,
      Like: false,
      LikeNumber: 0,
      componentKey: true,
    };
  },
  async created() {
    this.Init();
    this.GetUserLike();
  },
  computed: {
    ...mapGetters("auth", ["isAuthenticated"]),
    UserInfo() {
      const isAuthenticated = Store.getters["auth/isAuthenticated"];
      if (isAuthenticated) {
        return Store.getters["auth/currentUser"];
      }
      return undefined;
    },
  },
  watch: {
    // 也可以显式监听
    isAuthenticated(val) {
      if (!val) {
        this.GetUserLike();
      }
    },
  },
  methods: {
    async Init() {
      const response = await api.post("/mysql/index.php", {
        mode: "Articles",
        type: 0,
        id: this.id,
      });
      this.author_id = Number(response.data.author_id);
      this.data = JSON.parse(response.data.content);
      const user = await api.post("/mysql/index.php", {
        mode: "User",
        type: 0,
        id: this.author_id,
      });
      this.author_UserInfo = {
        id: Number(user.data.id),
        username: user.data.username,
        created_at: response.data.created_at,
      };
    },
    async LikeAction() {
      if (this.UserInfo === undefined) {
        this.$confirm("未登录，是否前往登录", "警告", {
          confirmButtonText: "是",
          cancelButtonText: "否",
          type: "warning",
          dangerouslyUseHTMLString: true, // 关键：允许解析HTML标签
        })
          .then(() => {
            // 用户点击是
            this.$router.push({
              name: "Login",
              query: { redirect: this.$route.fullPath },
            });
          })
          .catch(() => {
            // 用户点击否
          });
        return;
      }
      let response = undefined;
      if (this.Like) {
        response = await api.post("/mysql/index.php", {
          mode: "ArticlesLike",
          type: 1,
          article_id: this.id,
          user_id: this.UserInfo.id,
          action: "unlike",
        });
      } else {
        response = await api.post("/mysql/index.php", {
          mode: "ArticlesLike",
          type: 1,
          article_id: this.id,
          user_id: this.UserInfo.id,
          action: "like",
        });
      }
      if (response.data.success) {
        this.GetUserLike();
      }
    },
    async GetUserLike() {
      let response = undefined;
      if (this.UserInfo !== undefined) {
        response = await api.post("/mysql/index.php", {
          mode: "ArticlesLike",
          type: 0,
          article_id: this.id,
          user_id: this.UserInfo.id,
        });
        if (response.data.success) {
          this.Like = response.data.is_liked;
          this.LikeNumber = response.data.total_likes;
          if (this.$refs.AppPaperView !== undefined) {
            this.$refs.AppPaperView.UpdateLike(this.Like, this.LikeNumber);
          }
        }
      } else {
        this.Like = false;
        this.LikeNumber = 0;
        this.refreshComponent();
      }
    },
    refreshComponent() {
      this.componentKey = !this.componentKey; // true -> false -> true 交替
    },
  },
};
</script>

<style scoped>
.user-info {
}

@media screen and (min-width: 769px) {
  .user-info {
    padding: 0 10em;
    padding-top: 4em;
  }
}

@media screen and (max-width: 768px) {
  .user-info {
    padding: 0 1em;
    padding-top: 4em;
  }
}
</style>