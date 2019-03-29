@extends('admin.layout.layout')

@section('style')
    <style type="text/css">

        .box-card{
            width: 100%;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .box-card-div{
            width: 100%;height: 100px;text-align: center;font-size: 1.2em;
        }

    </style>
@stop

@section('nav')
    <li><a href="#" class="am-icon-home">首页</a></li>
@stop

@section('content')
    <div class="row" style="display: none;">
        <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
            <div class="dashboard-stat blue">
                <div class="visual">
                    <i class="am-icon-comments-o"></i>
                </div>
                <div class="details">
                    <div class="number"> @{{ topArray.top1 }} </div>
                    <div class="desc"> 上线品牌数 </div>
                </div>
                <a class="more" href="/admin/brand"> 查看更多
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>
        <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
            <div class="dashboard-stat red">
                <div class="visual">
                    <i class="am-icon-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number"> @{{ topArray.top2 }} </div>
                    <div class="desc"> 门店数 </div>
                </div>
                <a class="more" href="#"> 查看更多
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>
        <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
            <div class="dashboard-stat green">
                <div class="visual">
                    <i class="am-icon-apple"></i>
                </div>
                <div class="details">
                    <div class="number"> @{{ topArray.top3 }} </div>
                    <div class="desc"> 设备数 </div>
                </div>
                <a class="more" href="/admin/device"> 查看更多
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>
        <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
            <div class="dashboard-stat purple">
                <div class="visual">
                    <i class="am-icon-android"></i>
                </div>
                <div class="details">
                    <div class="number"> @{{ topArray.top4 }} </div>
                    <div class="desc"> 在线设备数 </div>
                </div>
                <a class="more" href="/admin/device"> 查看更多
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>



    </div>
    <el-row :gutter="20">
        <el-col :span="6">
            <el-card class="box-card">
                <div class="box-card-div">
                    <p>
                        <v-countup start-value="1" :end-value="end"></v-countup><br>
                        <span>卡券总数</span>
                    </p>
                </div>
            </el-card>
        </el-col>
        <el-col :span="6">
            <el-card class="box-card">
                <div class="box-card-div">
                <p>
                    <v-countup start-value="1" :end-value="end"></v-countup><br>
                    <span>本月新增卡券</span>
                </p>
                </div>
            </el-card>
        </el-col>
        <el-col :span="6">
            <el-card class="box-card">
                <div class="box-card-div">
                    <p>
                        <v-countup start-value="1" :end-value="end"></v-countup><br>
                        <span>累计使用卡券</span>
                    </p>
                </div>
            </el-card>
        </el-col>
        <el-col :span="6">
            <el-card class="box-card">
                <div class="box-card-div">
                    <p>
                        <v-countup start-value="1" :end-value="end"></v-countup><br>
                        <span>本月使用卡券</span>
                    </p>
                </div>
            </el-card>
        </el-col>
    </el-row>
@stop

@section('javascript')
    <script src="/js/lib/vue-countup.min.js"></script>
    <script src="/js/home.js"></script>
@stop