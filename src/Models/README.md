## About Models
Models 数据模型层

模型定义
首先，创建一个 Eloquent 模型。 模型通常在 app 目录中，但你可以根据 composer.json 文件将他们放置在可以被自动加载的任意位置。所有的 Eloquent 模型都继承至 Illuminate\Database\Eloquent\Model 类。

创建模型最简单的方法就是使用 make:model Artisan 命令:

php artisan make:model ErpOrder
————————————————

批量赋值
你也可以使用 create 方法来保存新模型。 此方法会返回模型实例。 不过，在使用之前，你需要在模型上指定 fillable 或 guarded 属性，因为所有的 Eloquent 模型都默认不可进行批量赋值。

当用户通过请求传入意外的 HTTP 参数，并且该参数更改了数据库中你不需要更改的字段时，就会发生批量赋值漏洞。 比如：恶意用户可能会通过 HTTP 请求传入 is_admin 参数，然后将其传给 create 方法，此操作能让用户将自己升级成管理员。

所以，在开始之前，你应该定义好模型上的哪些属性是可以被批量赋值的。你可以通过模型上的 $fillable 属性来实现。 例如：让 Flight 模型的 name 属性可以被批量赋值：


<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['name'];
}
