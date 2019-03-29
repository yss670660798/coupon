@extends('wap.layout.index')

@section('title')订单详情@stop

@section('content')

    <van-tabs v-model="active" @click="tabChange">

        <van-tab v-for="tab in tabs" :title="tab">
            <div v-show="loading" style="width: 100vw;margin-top: 20px;">
                <van-loading  type="spinner" color="white" style="margin: 0 auto;" />
            </div>
            <van-list v-model="listLoading" :finished="finished" finished-text="没有更多了" @load="nextPage">
                <van-card v-for="order in orderData" style="margin-top: 5px;"
                          :thumb="'<?php echo $url ?>api/img/'+order.goods.image_id"
                          :thumb-link="'/wap/order/detail/'+order.id"
                          :desc="order.coupon_card.coupon.name"
                          :title="order.goods.name">
                    <div slot="footer" style="width: 100vw;">
                        <span style="float: left;padding-left: 25px;">@{{order.code}}</span>
                        <van-tag v-if="order.status==1" type="primary" style="float: right;">已确认</van-tag>
                        <van-tag v-if="order.status==2" type="primary" style="float: right;">已发货</van-tag>
                        <van-tag v-if="order.status==3" type="success" style="float: right;">已完成</van-tag>
                    </div>
                </van-card>
            </van-list>
        </van-tab>
    </van-tabs>
@stop

@section('javascript')
    <script src="/static/wap/js/order/index.js"></script>
@stop
