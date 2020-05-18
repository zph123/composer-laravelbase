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
创建命令
```
php artisan make:command ZphCreate
```
打开app/Console/Commands/ZphCreate.php,替换成下面的代码
```
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Zph\Zph;

class ZphCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zph:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->ask('输入类名:(例如Base或者User)');
        $name = ucfirst($name);
        if ($name == "Base"){
            Zph::createBase(app_path(),$this);
        }else{
            Zph::createDemo(app_path(), $name,$this);
        }
    }
}
```
执行创建命令
```
root@e0be1c8900b6:/var/www/zph/laravelDemo# php artisan zph:create

 输入类名:(例如Base或者User):
 > Base

/var/www/zph/laravelDemo/app/Http/Controllers/BaseController.php创建成功
/var/www/zph/laravelDemo/app/Services/BaseService.php创建成功


root@e0be1c8900b6:/var/www/zph/laravelDemo# php artisan zph:create

 输入类名:(例如Base或者User):
 > demo

/var/www/zph/laravelDemo/app/Models/Demo.php已经存在
/var/www/zph/laravelDemo/app/Http/Controllers/DemoController.php已经存在
/var/www/zph/laravelDemo/app/Http/Controllers/DemoController.php创建成功
```
