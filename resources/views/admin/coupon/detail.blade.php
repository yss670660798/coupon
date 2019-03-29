@extends('admin.layout.layout')

@section('style')
    <link rel="stylesheet" href="/css/lib/iconfont.css">
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
    </style>
@stop

@section('nav')
    <li><a href="#" class="am-icon-home">设备管理</a></li>
    <li class="am-active">设备列表</li>
@stop

@section('content')

    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-input v-model="param.keyword" placeholder="卡券号" size="small" style="width: 150px;"></el-input>
            <el-select size="small" placeholder="卡券状态" clearable v-model="param.status" style="width: 150px;">
                <el-option label="未使用" value="1"></el-option>
                <el-option label="已使用" value="2"></el-option>
                <el-option label="已作废" value="3"></el-option>
                <el-option label="已过期" value="4"></el-option>
            </el-select>

            <el-button size="small" type="primary" icon="el-icon-search" @click="search" >查询</el-button>
            <el-button size="small" type="info" icon="el-icon-download" @click="download">导出</el-button>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column prop="coupon.name" label="卡券标题" ></el-table-column>
                        <el-table-column prop="sn" label="卡号" ></el-table-column>
                        <el-table-column prop="params" label="状态" >
                            <template slot-scope="scope">
                                <el-tag v-if="scope.row.status==1" size="mini">未使用</el-tag>
                                <el-tag v-if="scope.row.status==2" type="success" size="mini">已使用</el-tag>
                                <el-tag v-if="scope.row.status==3" type="danger" size="mini">已作废</el-tag>
                                <el-tag v-if="scope.row.status==4" type="info" size="mini">已过期</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="expired_at" label="有限期" ></el-table-column>
                        <el-table-column label="操作">
                            <template slot-scope="scope" >
                                <el-button v-if="scope.row.status==1 || scope.row.status==4" size="mini" @click="restart(scope.row)" >重设有效期</el-button>
                                <el-button v-if="scope.row.status==1" size="mini" type="warning" @click="stopCoupon(scope.row)" >作废</el-button>
                                <el-button v-if="scope.row.status==1 || scope.row.status==4" size="mini" type="danger" @click="delCoupon(scope.row)" >删除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div style="margin-top: 10px;">
                        <el-pagination layout="prev, pager, next,total" :current-page.sync="param.page" :total="count" :disabled="loading" @current-change="currentChange"></el-pagination>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <el-dialog title="修改有效期" :visible.sync="dialogShow" width="400px">
        <el-form :model="formData">
            <el-form-item label="有效期" :label-width="formLabelWidth">
                <el-date-picker v-model="formData.expired_at" type="date" placeholder="选择日期" value-format="yyyy-MM-dd"></el-date-picker>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button @click="dialogShow = false">取 消</el-button>
            <el-button type="primary" @click="save">确 定</el-button>
        </div>
    </el-dialog>

    <el-dialog title="正在导出" :visible.sync="dialogDownload" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" width="165px">
        <el-progress type="circle" :percentage="downloadPercentage"></el-progress>
    </el-dialog>
@stop

@section('javascript')
    <script type="text/javascript"  src="/js/lib/xlsx.full.min.js"></script>
    <script type="text/javascript"  src="/js/lib/Blob.js"></script>
    <script type="text/javascript"  src="/js/lib/FileSaver.js"></script>
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop