import Vue from "vue";
import Toast from "@/components/Toast";

let toastInstance = null;

const createToast = () => {
  if (toastInstance) {
    toastInstance.hide(); // 主动销毁旧实例
    toastInstance = null;
  }

  const Constructor = Vue.extend(Toast);
  toastInstance = new Constructor().$mount();
  document.body.appendChild(toastInstance.$el);

  return toastInstance;
};

export default {
  install(Vue) {
    Vue.prototype.$toast = (options) => {
      const instance = createToast();
      instance.show(options);
      return {
        hide: () => instance.hide(),
      };
    };
  },
};
