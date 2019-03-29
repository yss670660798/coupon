@extends('wap.layout.index')

@section('title')订单详情@stop

@section('link')
    <style type="text/css">
        .trans{
            font-size: 10px;
            margin-top: 5px;
            height: calc(100vh - 185px);
            overflow-y: scroll;
        }
    </style>
@stop

@section('content')
    <div style="width: 100vw;">
        <van-cell-group>
            <van-cell title="订单号" value="{{$order['code']}}" ></van-cell>
            <van-cell title="订单时间" value="{{$order['created_at']}}" ></van-cell>
            <van-cell title="订单状态" value="{{$statusName[$order['status']]}}" ></van-cell>
            <van-cell title="快递单号" value="{{$order['logistic_code']}}" ></van-cell>
        </van-cell-group>
    </div>
    <div class="trans">
        <van-pull-refresh v-model="isLoading" @refresh="onRefresh" style="height: 100%;overflow-y: scroll;">
            <div v-show="loading" style="width: 100vw;margin-top: 20px;">
                <van-loading  type="spinner" color="white" style="margin: 0 auto;" />
            </div>
            <div v-if="loading==false && transData.length<=0" style="width: 100%;text-align: center;margin-top: 20px;">
                暂无物流信息
            </div>
            <van-steps v-if="transData.length>0" direction="vertical" :active="0" active-color="#f44">
                <van-step v-for="item in transData">
                    <p>@{{item['AcceptStation']}}</p>
                    <p>@{{item['AcceptTime']}}</p>
                </van-step>
            </van-steps>
        </van-pull-refresh>

    </div>

@stop


@section('javascript')
    <script type="text/javascript">
        var orderId='{{$order['id']}}';
    </script>
    <script src="/static/wap/js/order/detail.js"></script>
@stop