# OTA-Server
OTA(Over-the-Air Technology) API Service And Web Management.
  
## 安装
  
### 所需环境

* PHP 5.6+
* Mysql 5+
* Nginx or Apache or other HTTP Server.

### 安装开发环境

1. 安装 npm
  npm is distributed with Node.js- which means that when you download Node.js, you automatically get npm installed on your computer. 
  You can find install instructions on this site:
<pre><code>https://nodejs.org/en/docs/</code></pre>

1. 安装 composer
<pre><code>curl -sS https://getcomposer.org/installer | php</code></pre>

1. 安装 bower
<pre><code>npm install -g bower</code></pre>

1. 安装 gulp
<pre><code>// Install gulp globally:
npm install --global gulp
// Initialize your project directory:
npm install --save-dev gulp</code></pre>

1. 安装 PHP packages
<pre><code>composer update</code></pre>

1. 下载web前端资源
<pre><code>bower update</code></pre>

1. 编译前端代码
<pre><code>gulp</code></pre>

1. 获取项目源码
<pre><code>git clone https://github.com/tytymnty/OTA-Server.git</code></pre>

1. 初始化数据库
<pre><code>./mysql -u [数据库用户名] -p [OTA服务器名字] < ota-server.sql</code></pre>

1. 初始化配置文件
<pre><code>cp .env.example .env // 修改环境变量
</code></pre>

## 浏览器支持情况

IE 9+
Firefox (latest)
Chrome (latest)
Safari (latest)
Opera (latest)