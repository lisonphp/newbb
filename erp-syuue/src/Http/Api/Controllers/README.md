## About Controller
Controller 控制器

1. 控制器层调用[服务层]
2. [服务层]调用建立的[仓储层]，和[表单验证层]



资源型控制器#
Laravel 的资源路由通过单行代码即可将典型的「CURD (增删改查)」路由分配给控制器。例如，你希望创建一个控制器来处理保存 “照片” 应用的所有 HTTP 请求。使用 Artisan 命令 make:controller 可以快速创建这样一个控制器：

php artisan make:controller PhotoController --resource

————————————————
转自链接：https://learnku.com/docs/laravel/7.x/controllers/7461#defining-controllers
