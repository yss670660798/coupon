<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/11/26
 * Time: 18:19
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    /**
     * 上传文件
     * @param Request $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @author yss
     * @date  2018/11/27 11:33
     */
    public function index(Request $request)
    {
        $file     = $request->file('file');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            return response(ReturnCode::error(ReturnCode::FAILED,'文件上传出错'));
        }
        $fileSize = ceil($file->getClientSize() / 1024);
        $fileExt  = $file->getClientOriginalExtension();
        $fileName = $file->getClientOriginalName();

        if ($fileExt) {
            $fileExt = '.' . strtolower($fileExt);
        }

        //限制大小
        if ($fileSize > 4096) {
            return ['success' => false, 'msg' => '文件超过限制大小4M'];
        }

        $tempName = date('ymdhis') . str_random(16) . $fileExt; //重命名
        $path     = storage_path('app/public/upload/'.date('Ymd').'/');   //临时存储文件夹
        // 创建ship_id目录
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $file->move($path, $tempName);  //转存图片
        $fullPath = $path . $tempName;    //图片绝对路径

        $fileKey = md5(file_get_contents($fullPath)) . $fileExt; //获取文件内容的md5字串作为文件名key

        if ($attachment =Attachment::where('file_key', $fileKey)->first()) {

            return response([
                'code' => ReturnCode::SUCCESS,
                'data'    => ['id' => $attachment->id]
            ]);
        }

        $attachment =Attachment::create([
            'owner_id'     => 1,
            'file_key'     => $fileKey,
            'file_name'    => $fileName,
            'file_path'    => 'app/public/upload/'.date('Ymd').'/'.$tempName,
            'file_url'     => config('app.url').'api/',
            'filesize'     => $fileSize,
            'ip_upload'    => $request->ip()
        ]);

        $attachment->file_url=config('app.url').'api/';

        return response(['code' => ReturnCode::SUCCESS, 'data' => ['id' => $attachment->id]]);
    }

    /**
     * 展示图片
     * @param Request $request
     * @param $id
     * @return mixed
     * @author yss
     * @date  2018/11/27 11:39
     */
    public function showImg(Request $request,$id)
    {
        $file=Attachment::find($id);
        if(!$file){
            Log::info($id);
            return response()->file(storage_path('app/public/img/default.jpg'));
        }

        if(!file_exists(storage_path($file->file_path))){
            Log::info($id);
            return response()->file(storage_path('app/public/img/default.jpg'));
        }

        return response()->file(storage_path($file->file_path));
    }
}