<?php
namespace Zph;
use Illuminate\Support\Facades\Schema;
class Zph
{
    public static function createBase($appDir,$command)
    {
        $toBaseController = $appDir."/Http/Controllers/BaseController.php";
        if(!file_exists($toBaseController)){
            copy(dirname(__FILE__)."/base/BaseController.php",$toBaseController);
            $command->info($toBaseController.'创建成功');
        }else{
            $command->info($toBaseController.'已经存在');
        }

        $toBaseService = $appDir."/Services/BaseService.php";
        if(!file_exists($toBaseService)){
            if(!is_dir($appDir."/Services")){
                mkdir($appDir."/Services");
            }
            copy(dirname(__FILE__)."/base/BaseService.php",$toBaseService);
            $command->info($toBaseService.'创建成功');
        }else{
            $command->info($toBaseService.'已经存在');
        }
    }
    public static function createDemo($appDir,$name,$command)
    {
        $columns = Schema::getColumnListing($name.'s');

        if(!is_dir($appDir."/Models")){
            mkdir($appDir."/Models");
        }
        if(!is_dir($appDir."/Services")){
            mkdir($appDir."/Services");
        }
        $toDemoModel = $appDir."/Models/".$name.".php";
        if (!file_exists($toDemoModel)){
            $codeModel = "<?php\n";
            $codeModel .= "namespace App\Models;\n";
            $codeModel .= "use Illuminate\Database\Eloquent\Model;\n";
            $codeModel .= 'class '.$name.' extends Model'."\n";
            $codeModel .= "{\n";
            $codeModel .= "\t".'protected $fillable = ['."\n";
            foreach ($columns as $v){
                $codeModel .= "\t\t'".$v."',\n";
            }
            $codeModel .= "\t".'];'."\n";
            $codeModel .= "}\n";
            file_put_contents($toDemoModel, $codeModel, LOCK_EX);
            $command->info($toDemoModel.'创建成功');
        }else{
            $command->error($toDemoModel.'已经存在');
        }

        $toDemoController = $appDir."/Http/Controllers/".$name."Controller.php";
        if (!file_exists($toDemoController)){
            $codeController = "<?php\n";
            $codeController .= "namespace App\Http\Controllers;\n";
            $codeController .= "use App\Http\Controllers\Controller;\n";
            $codeController .= "use App\Services\\".$name."Service;\n";
            $codeController .= 'class '.$name.'Controller extends BaseController'."\n";
            $codeController .= "{\n";
            $codeController .= "\t".'protected $service;'."\n";
            $codeController .= "\t".'public function __construct('.$name.'Service $service)'."\n";
            $codeController .= "\t".'{'."\n";
            $codeController .= "\t"."\t".'$this->service = $service;'."\n";
            $codeController .= "\t".'}'."\n";
            $codeController .= "}\n";
            file_put_contents($toDemoController, $codeController, LOCK_EX);
            $command->info($toDemoController.'创建成功');
        }else{
            $command->error($toDemoController.'已经存在');
        }

        $toDemoService = $appDir."/Services/".$name."Service.php";
        if (!file_exists($toDemoService)){
            $codeService = "<?php\n";
            $codeService .= "namespace App\Services;\n";
            $codeService .= "use App\Models\\".$name.";\n";
            $codeService .= "use App\Services\BaseService;\n";
            $codeService .= 'class '.$name.'Service extends BaseService'."\n";
            $codeService .= "{\n";
            $codeService .= "\t".'protected $model;'."\n";
            $codeService .= "\t".'public function __construct('.$name.' $app)'."\n";
            $codeService .= "\t".'{'."\n";
            $codeService .= "\t"."\t".' $this->model = $app;'."\n";
            $codeService .= "\t".'}'."\n";
            $codeService .= "}\n";
            file_put_contents($toDemoService, $codeService, LOCK_EX);
            $command->info($toDemoController.'创建成功');
        }else{
            $command->error($toDemoService.'已经存在');
        }


    }
}

