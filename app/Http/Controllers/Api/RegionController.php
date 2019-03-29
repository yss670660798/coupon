<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2019/1/8
 * Time: 22:06
 */

namespace App\Http\Controllers\Api;


use App\Libs\ReturnCode;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function regionList(Request $request)
    {
        $provinces=Region::where('type',1)->whereNotNull('code')->get();
        $provinceList=[];
        foreach ($provinces as $province){
            $provinceList[$province->code]=$province->name;
        }

        $citys=Region::where('type',2)->whereNotNull('code')->get();
        $cityList=[];
        foreach ($citys as $city){
            $cityList[$city->code]=$city->name;
        }

        $countys=Region::where('type',3)->whereNotNull('code')->get();
        $countyList=[];
        foreach ($countys as $county){
            $countyList[$county->code]=$county->name;
        }

        return response(ReturnCode::success(['province_list'=>$provinceList,'city_list'=>$cityList,'county_list'=>$countyList]));
    }
    /**
     * 更新省市县编号
     * @return int
     * @author yss
     * @date  2019/1/8 22:34
     */
    public function updateRegionCode()
    {
        $code1=11;
        $code2=0;
        $code3=0;

        $count=0;
        $provinces=Region::where('type',1)->get();
        foreach ($provinces as $province){
            $province->code=$code1.'0000';
            $province->save();
            $count++;
            $citys=Region::where('parent_id',$province->id)->get();
            $code2=0;
            foreach ($citys as $city){
                $code2+=1;
                $cityCode=substr('000'.$code2,-2);
                $city->code=$code1.$cityCode.'00';
                $city->save();
                $count++;
                $countys=Region::where('parent_id',$city->id)->get();
                $code3=0;
                foreach ($countys as $county){
                    $code3+=1;
                    $countyCode=substr('000'.$code3,-2);
                    $county->code=$code1.$cityCode.$countyCode;
                    $county->save();
                    $count++;
                }
            }

            $code1+=1;
        }

        return $count;
    }
}