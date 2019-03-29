@extends('wap.layout.index')

@section('title')
    {{$title}}
@stop

@section('link')
    <style type="text/css">
        .van-field .van-cell__title{
            font-size: 18px;
        }
        .van-hairline--top-bottom::after{
            border: none;
        }
    </style>
@stop

@section('content')
    <div style="width: 100vw;height: 100vh;overflow: hidden;background-color: white;">
        {{--图片--}}
        <div style="height: 90px;width: 70vw;margin:0 auto;margin-top: 30px;">
            <img src="/api/img/{{$img_id}}" style="width: 100%;height: 100%;">
        </div>
        {{--表单--}}
        <div style="margin-top: 10px;">
            <van-cell-group>
                <van-field v-model="param.card_no" required clearable label="用户名" placeholder="请输入用户名"></van-field>
                <van-field v-model="param.password" required type="password" label="密码" placeholder="请输入密码" ></van-field>
                <van-field v-model="param.v_code" required clearable label="验证码" placeholder="请输入验证码">
                    <div style="width: 78px;height: 30px;" slot="button">
                        <img src="{{captcha_src('flat')}}" style="width: 100%;height: 100%;" onclick="this.src='{{captcha_src('flat')}}'+Math.random()">
                    </div>
                </van-field>
            </van-cell-group>
        </div>

        {{--按钮--}}
        <div style="width: 70vw;margin:0 auto;margin-top: 30px;">
            <van-button style="width: 100%;" type="primary" @click="checkCard">卡券校验</van-button>
        </div>
        {{--说明--}}
        <div style="width: 100vw;margin-top: 30px;">
            <van-notice-bar text="提领截至1-27"></van-notice-bar>
            <van-notice-bar text="提领截至1-27"></van-notice-bar>
            <van-notice-bar text="提领截至1-27"></van-notice-bar>
        </div>
    </div>
    {{--地址列表--}}
    <transition name="van-slide-right">
        <div v-show="selectAddress" style="width: 100vw;height: 100vh;overflow: hidden;background-color: white;z-index: 50;position: absolute;left: 0;top: 0;">
            <van-nav-bar title="选择地址" left-text="返回" left-arrow @click-left="hideAddrList" ></van-nav-bar>
            <van-address-list v-model="chosenAddressId" :list="addressList" @add="addAddress">
                <div slot="top" v-if="addressList.length<=0" style="text-align: center;width: 100vw;color: grey;margin-top: 20px;">
                    没有地址
                </div>
            </van-address-list>
        </div>
    </transition>
    {{--添加地址--}}
    <transition name="van-slide-right">
        <div v-show="newAddress" style="width: 100vw;height: 100vh;overflow: hidden;background-color: white;z-index: 60;position: absolute;left: 0;top: 0;">
            <van-nav-bar title="新增地址" left-text="返回" left-arrow @click-left="hideNewAddr" ></van-nav-bar>
            <van-address-edit show-postal></van-address-edit>
        </div>
    </transition>
@stop

@section('javascript')
    <script src="/static/wap/js/home/index.js"></script>
@stop