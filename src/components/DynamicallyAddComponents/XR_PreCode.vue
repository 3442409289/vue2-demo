<template>
  <div class="code">
    <pre
      class="line-numbers"
    ><code class="language-cpp language-javascript">{{data}}</code></pre>
    <div class="button">
      <Item @active="active">复制</Item>
    </div>
  </div>
</template>

<script>
import Item from "@/components/Item";
import Prism from "prismjs";

import "prismjs/themes/prism-tomorrow.css"; // 主题
import "prismjs/plugins/line-numbers/prism-line-numbers.css"; // 行号样式
import "prismjs/plugins/line-numbers/prism-line-numbers"; // 行号插件
import "prismjs/components/prism-c"; // 语言支持
import "prismjs/components/prism-cpp";

export default {
  components: {
    Item,
  },
  props: {
    data: {
      type: String,
      default: "文字",
    },
  },
  mounted() {
    Prism.highlightAll();
    this.InitStyle();
  },
  methods: {
    InitStyle() {
      if (!window.__myCustomStyleInjected__) {
        const style = document.createElement("style");
        style.innerHTML = `
        .line-numbers-rows > span:before { color: var(--text-color); }
      `;
        document.head.appendChild(style);

        // 设置全局标记，表示已经运行过
        window.__myCustomStyleInjected__ = true;
      }
    },
    async active() {
      await this.$copyText(`${this.data}`).then(
        () => this.$toast({ message: "代码复制成功" }),
        () => this.$toast({ message: "代码复制失败" })
      );
    },
  },
};
</script>

<style scoped>
.code {
  position: relative;
}

.code .line-numbers {
  background: var(--bg-color2);
  border-radius: 3px;
  padding: 0.8em;
  line-height: 0.5; /* 与代码行高一致 */
  padding-left: 3.8em; /* 为行号预留空间 */
  cursor: auto;
}

.code .line-numbers code {
  font-family: var(--Current-Font);
  font-size: 0.85em;
  color: var(--text-color);
  text-shadow: none;
  user-select: text;
}
.code .button {
  position: absolute;
  top: 0;
  right: 0;
}
/* prism-tomorrow.css 中的行号相关样式 */
.line-numbers-rows > span:before {
  color: var(--text-color); /* 修改此处颜色值 */
}
</style>