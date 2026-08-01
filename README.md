# xingxing-app

## Project setup

```
npm install
```

### Compiles and hot-reloads for development

```
npm run serve
```

### Compiles and minifies for production

```
npm run build
```

### Customize configuration

See [Configuration Reference](https://cli.vuejs.org/config/).

## 打包说明

使用 npm run build 打包后的 dist 目录里的文件需要复制到 php 服务器根目录后下载 Release.zip 后解压里面的文件把 css 和 js 文件夹去掉换成你打包的，然后还要配置 php 找到 public 目录进去创建一个 config.ini 配置文件，注意 config.ini 是服务器账号密码意味着有泄露风险必须设置该文件的访问权限不可直接访问只能由服务器访问

```
[database]
host = "本地数据库IP"
username = "本地数据库账号"
password = "本地数据库密码"
dbname = "本地数据库"

[database_prod]
host = "远端数据库IP"
username = "远端数据库账号"
password = "远端数据库密码"
dbname = "远端数据库"
```

### 配置数据库

找到 sql 文件夹就是 public 里，把里面的 sql 全部上传之后还需要注册一个管理员账号直接在数据库里编辑好信息密码得需要加密一下，用浏览器访问"服务器地址/api/login/test.php?=要加密的密码"permissions 是权限参数["\*"]代表管理员如果是["user"]就是普通用户目前只有这两个权限它是一个数组的显示可以是["\*","user"]这样写既是管理员又是普通用户但是不符合逻辑所以这是以后要添加新权限用的比如 vip 或 svip 以及作者权限
