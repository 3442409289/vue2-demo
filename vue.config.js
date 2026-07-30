const { defineConfig } = require("@vue/cli-service");
module.exports = defineConfig({
  productionSourceMap: false, // 关闭后打包不会生成 .map 文件
  transpileDependencies: true,
  devServer: {
    proxy: {
      "/MySQL": {
        target: "http://xianren.ueuo.com/",
      },
      "/": {
        target: "http://127.0.0.1/",
      },
    },
  },
});
