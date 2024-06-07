# 怪兽导航

## 开始之前

1、浏览器的收藏夹内容原来越多，有时候无法很快的找到一个不常用的网址，所以萌生了一个想法：搞一个自己的个人导航页。

2、如果你跟我有一样的需求，那么可以试试怪兽导航，部署方便简单，不需要任何服务器成本，可以说是：零成本。

3、唯一需要的能力就是会简单使用Git、Github和简单的修改前端代码。

## 演示效果

>体验地址：https://codermonster.top/

1、日间模式

![](images/日间模式.png)

2、夜间模式

![](images/夜间模式.png)

## 下载部署

>前提：本地已经安装配置了Git工具。

1、克隆项目

```shell
// 克隆项目
git clone https://github.com/oucystong/oucystong.github.io.git
```

2、查看项目的远程地址

```shell
// 查看项目的远程地址
git remote -v
```

>此时远程地址为本项目的远程地址，需要修改成你自己的远程地址，否则后续修改代码后无法推送和部署。

3、注册Github账号，并且按照如下图片要求新建仓库

![](images/新建Github仓库要求.png)

4、获取新建仓库地址并且设置本地项目的远程地址

```shell
# 设置远程仓库地址
git remote set-url origin https://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@github.com/oucystong/oucystong.github.io.git
# 查看远程仓库地址
git remote -v
```

>注意：
>
>1、你的新建仓库地址可能跟上述仓库格式不太一样，必须要跟上述仓库格式相同才可以，否则无法推送，这是因为Github不再允许通过用户名和密码的方式进行验证。
>
>2、解决方案：https://blog.csdn.net/yjw123456/article/details/119696726

5、修改代码

>代码基本都可以见名知意，自定义内容替换为自己的即可。

6、提交代码

```shell
# 一套三连招
git add .
git commit -m "update"
git push -u origin main
```

7、通过Github Pages部署项目

![](images/部署成功.png)

8、通过Github Pages生成的网址测试访问

9、如果想自定义域名，可以通过阿里云或者腾讯云直接申请，如果仅仅作为Github Pages的绑定域名，则不需要备案

> * 提交CNAME文件到仓库中，CNAME文件内容是申请的域名。
> * 在阿里云或者腾讯云中配置域名解析。

## 参考项目

* [WebStack](https://github.com/WebStackPage/WebStackPage.github.io/)
* [WebStack-Hugo](https://github.com/shenweiyan/WebStack-Hugo)

## 请喝可乐

觉得有帮助的小伙伴，欢迎请作者喝杯肥宅快乐水！

<img src="images/赞赏码.JPG" width = "300" height = "300" alt="图片名称" align="center" />



## 关注怪兽

码农怪兽，一只喜欢研究技术、科技、思想、软件、资源、产品的非不正经的UP主！如果有任何问题可以关注怪兽的微信公众号及时留言反馈！

![](images/码农怪兽公众号二维码.png)
