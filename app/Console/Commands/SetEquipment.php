<?php

namespace App\Console\Commands;

use App\Libs\appJMessage;
use App\Models\Coupon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetEquipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_eqp_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新设备状态';

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
        date_default_timezone_set('PRC');
        try{
            $data=Coupon::whereNotNull('jg_name')
                ->select('id','jg_name')
                ->get()->toArray();

            if($data){
                foreach ($data as $item){
                    $this->update($item['jg_name']);
                }
            }
        }
        catch (\Exception $exception){
            Log::error($exception);
        }
//        Log::info('定时器10分钟='.date('Y-m-d H:i:s',time()));
    }

    private function update($jg_name)
    {
        try{
            $stat=appJMessage::stat($jg_name);
            $online=isset($stat['body']['online'])?$stat['body']['online']:false;
            if(!$online){
                Coupon::where('jg_name',$jg_name)->update([
                    'status_id'=>Null,
                    'status'=>Null,
                    'http_on'=>Null,
                    'server_on_0'=>Null,
                    'server_on_1'=>Null
                ]);

            }
        }
        catch (\Exception $exception){
            Log::error($exception);

        }
    }

}
