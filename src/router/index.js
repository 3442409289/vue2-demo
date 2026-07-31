import Vue from "vue";
import VueRouter from "vue-router";
import Store from "@/store"; // 确保路径正确

Vue.use(VueRouter); // 注册插件

const routes = [
  {
    path: "/",
    component: () => import("@/App.vue"),
    children: [
      {
        path: "", // 空路径，匹配根路由
        redirect: "/AppLayout", // 重定向到 AppLayout
      },
      {
        path: "AppLayout",
        component: () => import("@/pages/AppLayout"),
        children: [
          {
            path: "", // 默认子路由（如首页）
            name: "AppMain",
            component: () => import("@/pages/AppMain"),
            meta: { requiresAuth: false },
          },
          {
            path: "live",
            name: "Live",
            component: () => import("@/pages/Live"),
            meta: { requiresAuth: false },
          },
          {
            path: "paperedit",
            name: "PaperEdit",
            component: () => import("@/pages/AppPaperEdit"),
            meta: { requiresAuth: false },
          },
          {
            path: "paperview/:url",
            name: "PaperView",
            component: () => import("@/pages/AppPaperView"),
            props: (route) => ({
              url: route.params.url,
            }),
            meta: { requiresAuth: false },
          },
          {
            path: "PaperViewId/:id",
            name: "PaperViewId",
            component: () => import("@/pages/AppPaperViewId"),
            props: (route) => ({
              id: Number(route.params.id),
            }),
            meta: { requiresAuth: false },
          },
          {
            path: "AppPaperViewFile",
            name: "AppPaperViewFile",
            component: () => import("@/pages/AppPaperViewFile"),
            meta: { requiresAuth: false },
          },
          {
            path: "LogComponent/:data",
            name: "LogComponent",
            component: () => import("@/components/LogComponent"),
            props: (route) => ({
              data: route.params.data, // 必传参数
            }),
            meta: { requiresAuth: false },
          },
          {
            path: "ArticlesView/:url",
            name: "ArticlesView",
            component: () => import("@/pages/AppArticlesView"),
            props: (route) => ({
              url: Number(route.params.url),
            }),
            meta: { requiresAuth: false },
          },
          {
            path: "login",
            name: "Login",
            component: () => import("@/views/Login"),
            meta: { requiresAuth: false },
          },
        ],
      },
      {
        path: "/XRArtPlayer/:url/:isLive?/:autoplay?",
        name: "XRArtPlayer",
        component: () => import("@/components/XRArtPlayer"),
        props: (route) => ({
          url: route.params.url, // 必传参数
          isLive: route.params.isLive === "1" || false, // 必传参数
          autoplay: route.params.autoplay === "1" || false, // 默认值为 false
        }),
        meta: { requiresAuth: false },
      },
      {
        path: "/XRArtPlayerList/:url/:isLive?/:autoplay?",
        name: "XRArtPlayerList",
        component: () => import("@/components/XRArtPlayerList"),
        props: (route) => ({
          url: route.params.url,
          isLive: route.params.isLive === "1" || false, // 必传参数
          autoplay: route.params.autoplay === "1" || false, // 默认值为 false
        }),
        meta: { requiresAuth: false },
      },
      {
        path: "/root",
        name: "Root",
        component: () => import("@/views/Root"),
        children: [
          {
            path: "NavigationManager",
            name: "NavigationManager",
            component: () => import("@/views/NavigationManager"),
            meta: {
              requiresAuth: true, // 需要登录
              requiredRoles: ["*"], // 指定允许访问的角色[1,5](@ref)
            },
          },
          {
            path: "Users",
            name: "Users",
            component: () => import("@/views/Users"),
            meta: {
              requiresAuth: true, // 需要登录
              requiredRoles: ["*"], // 指定允许访问的角色[1,5](@ref)
            },
          },
          {
            path: "Log",
            name: "Log",
            component: () => import("@/views/Log"),
            meta: {
              requiresAuth: true, // 需要登录
              requiredRoles: ["*"], // 指定允许访问的角色[1,5](@ref)
            },
          },
        ],
        meta: {
          requiresAuth: true, // 需要登录
          requiredRoles: ["*"], // 指定允许访问的角色[1,5](@ref)
        },
      },
      {
        path: "/Profile",
        name: "Profile",
        component: () => import("@/views/Profile"),
        children: [
          {
            path: "User",
            name: "User",
            component: () => import("@/views/Profile/User"),
            meta: {
              requiresAuth: true, // 需要登录
              requiredRoles: ["user"], // 指定允许访问的角色[1,5](@ref)
            },
          },
          {
            path: "Articles",
            name: "Articles",
            component: () => import("@/views/Profile/Articles"),
            meta: {
              requiresAuth: true, // 需要登录
              requiredRoles: ["user"], // 指定允许访问的角色[1,5](@ref)
            },
          },
        ],
        meta: {
          requiresAuth: true, // 需要登录
          requiredRoles: ["user"], // 指定允许访问的角色[1,5](@ref)
        },
      },
    ],
  },
];

const router = new VueRouter({
  routes,
  mode: "hash", // 默认 hash 模式，可选 history 模式（需后端支持）
});

// 路由守卫
router.beforeEach(async (to, from, next) => {
  // 使用 async
  try {
    // 等待初始化认证状态完成
    await Store.dispatch("auth/initializeAuth"); // 使用 await

    const isAuthenticated = Store.getters["auth/isAuthenticated"];

    // ... 其余检查逻辑保持不变，确保每个分支调用一次 next() 并用 return
    if (to.meta.requiresAuth && !isAuthenticated) {
      next({ name: "Login" });
      return;
    } else if (to.path === "/login" && isAuthenticated) {
      next({ path: "/dashboard" });
      return;
    }
    if (to.meta.requiredRoles) {
      const hasPermission = Store.getters["auth/hasPermission"](
        to.meta.requiredRoles
      );
      if (!hasPermission) {
        next({ name: "AppMain" }); // 请确保跳转到 "AppMain" 不会再次触发权限检查导致循环
        return;
      }
    }
    next();
  } catch (error) {
    console.error("路由守卫中出现错误:", error);
    next({ name: "AppMain" }); // 可以跳转到一个错误页面
  }
});

export default router; // 导出路由实例
