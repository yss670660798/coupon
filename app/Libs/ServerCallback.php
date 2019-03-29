<?php
/**
 * Created by PhpStorm.
 * Author: hongpo
 * Date: 2018/8/8
 * Time: 13:28
 */

namespace App\Libs;


use App\Models\Attachment;
use App\Models\DatasetDetail;
use App\Models\DatasetLabel;
use App\Models\ModelQueue;
use App\Models\Models;
use App\Models\ModelVersion;
use App\Models\Sku;
use App\Models\VersionDataset;
use Illuminate\Support\Facades\Log;

class ServerCallback
{
    /**
     * @no.1
     * @des 获得所需参数
     * @author kevin
     * @param $model_version_id
     * @return array
     */
    public static function getData($model_version_id)
    {
        try{
            $bool=true;
            $msg=[];
            //查询模型版本
            $modelVersion=ModelVersion::find($model_version_id);
            if(!$modelVersion){
                $bool=false;
                array_push($msg,'未找到模型记录');
            }
            $id=$modelVersion->id;
            $model_id=$modelVersion->model_id;
            $model_name=$modelVersion->model_name;
            $max_iters=$modelVersion->max_iters;
            $step_iters=$modelVersion->step_iters;

            $model=Models::find($model_id);
            $brand_id=$model->brand_id;
            $store_id=$model->store_id;
            $queue_id=ModelQueue::where('model_version_id',$model_version_id)->first()->id;

            //获得数据集ID Array
            $dataSetId=ServerCallback::getDataSetId($model_version_id);
            if(!is_array($dataSetId)){
                $bool=false;
                array_push($msg,'获得数据集ID错误');
            }

            //获得数据集Zip包文件
            $dataList=ServerCallback::getDataSetZip($dataSetId);
            if(!is_array($dataList)){
                $bool=false;
                array_push($msg,'获得数据集Zip包文件错误');
            }

            //获得标签记录
            $dataLabel=ServerCallback::getDataSetLabel($dataSetId);
            if(!is_array($dataLabel)){
                $bool=false;
                array_push($msg,'获得标签记录错误');
            }

            //获得产品列表
            $skuMapping=ServerCallback::getSKUMapping($store_id,$dataLabel);
            if(!is_array($skuMapping)){
                $bool=false;
                array_push($msg,'获得产品列表错误');
            }
            $response=[
                'status'=>$bool,
                'msg'=>$msg,
                'data_list'=>$dataList,
                'data_label'=>$dataLabel,
                'sku_mapping'=>$skuMapping,
                'model_version_id'=>$id,
                'queue_id'=>$queue_id,
                'brand_id'=>$brand_id,
                'store_id'=>$store_id,
                'model_name'=>$model_name,
                'max_iters'=>$max_iters,
                'step_iters'=>$step_iters,
                'timestamp'=>time()
            ];

            return $response;
        }
        catch (\Exception $exception){
            Log::error($exception);
            return ['status'=>false,'msg'=>$exception->getMessage()];
        }
    }

    /**
     * @no.2
     * @des 获得数据集ID array
     * @param $model_version_id
     * @return bool|\Illuminate\Support\Collection|string  dataset_id
     */
    private static function getDataSetId($model_version_id)
    {
        try{
            if(intval($model_version_id)<=0){
                return '参数错误';
            }
            //获得数据集ID
            $dataSetId=VersionDataset::where('model_version_id',$model_version_id)
                ->where('status','<>',3)
                ->select('dataset_id')
                ->groupBy('dataset_id')
                ->pluck('dataset_id')
                ->toArray();

            if(array_count_values($dataSetId)==0){
                return '获得数据集ID为空';
            }
            return $dataSetId;
        }
        catch (\Exception $exception){
            Log::error($exception);
            return false;
        }
    }

    /**
     * @no. 3
     * @des 获得数据集Zip包文件
     * @param array $dataSet_id
     * @return bool|\Illuminate\Support\Collection|string  data_list
     */
    private static function getDataSetZip($dataSet_id=[])
    {
        try{

            //获得附件ID
            $attachId=DatasetDetail::whereIn('dataset_id',$dataSet_id)
                ->select('attach_id')
                ->groupBy('attach_id')
                ->pluck('attach_id')
                ->toArray();
            if(array_count_values($attachId)==0){
                return '获得附件ID为空';
            }

            //获得ZIP包，数组
            $attachList=Attachment::whereIn('id',$attachId)
                ->select('path')
                ->groupBy('path')
                ->pluck('path')
                ->toArray();

            if(array_count_values($attachList)==0){
                return '未获得ZIP包记录';
            }
            else{
                return $attachList;
            }
        }
        catch (\Exception $exception){
            Log::error($exception);
            return false;
        }
    }

    /**
     * @no. 4
     * @des 获得标签记录
     * @param array $dataSet_id
     * @return array|bool|string  data_label
     */
    private static function getDataSetLabel($dataSet_id=[])
    {
        try{
            if(!is_array($dataSet_id)){
                return '只接受数组参数';
            }

            $datasetLabel=DatasetLabel::whereIn('dataset_id',$dataSet_id)
                ->select('name')
                ->groupBy('name')
                ->pluck('name')
                ->toArray();

            if(array_count_values($datasetLabel)==0){
                return '未获得标签记录';
            }
            else{
                return $datasetLabel;
            }
        }
        catch (\Exception $exception){
            Log::error($exception);
            return false;
        }
    }


    /**
     * @no. 5
     * @des 获得产品列表
     * @param $store_id
     * @param array $dataLabel
     * @return array|bool|string sku_mapping
     */
    private static function getSKUMapping($store_id,$dataLabel=[])
    {
        try{
            if(!is_array($dataLabel)){
                return '只接受数组参数';
            }
            if(intval($store_id)>0){
                $sku=Sku::where('store_id',$store_id)
                    ->where('status',1)
                    ->whereIn('identify',$dataLabel)
                    ->select('identify','code','name','unit','price','weight')
                    ->groupBy(['identify','code','name','unit','price','weight'])
                    ->get()
                    ->toArray();
                if(!$sku){
                    return '产品表无记录';
                }
                return $sku;
            }
            return false;
        }
        catch (\Exception $exception){
            Log::error($exception);
            return false;
        }
    }

}