# laravelbase
功能描述：自动生成controller、service、model

**composer引入zph/laravelbase**
```
composer require zph/laravelbase
```
**创建表demos**
```
CREATE TABLE `demos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
**新建路由访问ZphController**
```
Route::get('/zph/createBase', 'ZphController@createBase');
Route::get('/zph/createDemo', 'ZphController@createDemo');
```

**新建ZphController,来调用生成方法createBase()、createDemo()**
```
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Zph\Zph;

class ZphController extends Controller
{
    /*
     *生成app/Controllers/BaseController.php
     *生成app/Services/BaseService.php
    */
    public function createBase()
    {
        return Zph::createBase(app_path());
    }
    /*
     *生成app/Controllers/DemoController.php
     *生成app/Services/DemoService.php
     *生成app/Models/Demo.php
    */
    public function createDemo()
    {
        //如果表名为demos，那么$name的值为Demo
        $name = 'Demo';
        return Zph::createDemo(app_path(), $name);
    }
}
```
**新建路由访问新建DemoController**
```
//查询
Route::get('/demo', 'DemoController@index');
//添加
Route::post('/demo', 'DemoController@store');
```
**通过浏览器或者postman来访问DemoController就可以了**
