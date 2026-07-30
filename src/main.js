import Vue from "vue";
import App from "./App.vue";
import Store from "@/store";
import VueMeta from "vue-meta";
import ElementUI from "element-ui";
import VueClipboard from "vue-clipboard2";
import Toast from "./plugins/toast";

// 引入全局样式文件
import "./styles/root.css";
import "element-ui/lib/theme-chalk/index.css"; // 引入样式文件

// 导入路由实例
import router from "@/router"; // 会自动寻找 ./router/index.js

Vue.use(ElementUI, {
  zIndex: 1000000, // 全局设置所有弹出组件的基准 z-index
});
Vue.use(Toast);
Vue.use(VueClipboard); // 注册插件 复制内容到剪切板
Vue.use(VueMeta);

Vue.prototype.$XR_version = function (e) {
  console.log(
    `%c XIANREN %c ${e} %c http://xianren.ueuo.com`,
    "color: #fff; background: #008C8C",
    "color: #fff; background: #FF8C8C",
    ""
  );
};

Vue.prototype.$globalSwitch = Vue.observable({
  DarkMode: {
    isDarkMode: false,
    toggle() {
      this.isDarkMode = !this.isDarkMode;
      sessionStorage.setItem("darkMode", JSON.stringify(this.isDarkMode));
    },
    setMode(val) {
      this.isDarkMode = val;
    },
  },
  BgOpacity: {
    isBgOpacity: false,
    toggle() {
      this.isBgOpacity = !this.isBgOpacity;
      sessionStorage.setItem("bgOpacity", JSON.stringify(this.isBgOpacity));
    },
    setMode(val) {
      this.isBgOpacity = val;
    },
  },
});

new Vue({
  router, // 添加路由实例
  store: Store,
  render: (h) => h(App),
}).$mount("#app");
