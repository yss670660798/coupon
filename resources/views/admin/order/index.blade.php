@extends('admin.layout.layout')

@section('style')
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
        .el-step__title{
            font-size: 10px;
        }
        .el-step__title.is-process{
            color:red;
        }
        .el-step__head.is-process{
            color: red;
        }
        .el-step__description{
            font-size: 10px;
        }
        .el-step__description.is-process{
            color: red;
        }
    </style>
@stop


@section('content')

    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-date-picker v-model="param.date" type="date" placeholder="查询日期" value-format="yyyy-MM-dd" size="small" style="width: 150px;"></el-date-picker>
            <el-select v-model="param.goods_id" placeholder="卡券" size="small" clearable>
                <el-option v-for="item in couponData" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>
            <el-select v-model="param.status" placeholder="订单状态" size="small" style="width: 100px;" clearable>
                <el-option label="已接单" value="1"></el-option>
                <el-option label="已发货" value="2"></el-option>
                <el-option label="已完成" value="3"></el-option>
            </el-select>
            <el-input v-model="param.keyword" placeholder="订单号/联系人/电话/卡券号" size="small" style="width: 200px;" clearable></el-input>
            <el-button size="small" type="primary" icon="el-icon-search" @click="search" >查询</el-button>

            <el-button-group  style="float: right;">
                <el-button type="primary" icon="el-icon-download" size="small" title="导出订单" @click="download"></el-button>
                <el-button type="primary" icon="el-icon-upload2" size="small" title="导入运单号"></el-button>
            </el-button-group>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column prop="code" label="订单编号"></el-table-column>
                        <el-table-column prop="coupon_card.coupon.name" label="卡券名称" ></el-table-column>
                        <el-table-column prop="coupon_card.sn" label="卡券号" width="90"></el-table-column>
                        <el-table-column prop="contact" label="联系人" width="80"></el-table-column>
                        <el-table-column prop="mobile" label="电话" ></el-table-column>
                        <el-table-column prop="address" label="地址" ></el-table-column>
                        <el-table-column prop="status" label="状态" width="80">
                            <template slot-scope="scope">
                                <el-tag v-if="scope.row.status==1">已确认</el-tag>
                                <el-tag v-if="scope.row.status==2">已发货</el-tag>
                                <el-tag v-if="scope.row.status==3" type="success">已完成</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="created_at" label="下单时间" ></el-table-column>
                        <el-table-column prop="logistic_code" label="快递单号" >
                            <template slot-scope="scope">
                                <el-button type="text" @click="getTrack(scope.row)">@{{ scope.row.logistic_code }}</el-button>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="100">
                            <template slot-scope="scope" >
                                <el-button type="text" size="mini" icon="el-icon-setting" @click="setExpress(scope.row)" title="绑定单号" style="font-size: 20px;"></el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div style="margin-top: 10px;">
                        <el-pagination layout="prev, pager, next,total" :current-page.sync="param.page" :total="count" :disabled="loading" @current-change="currentChange" ></el-pagination>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{--绑定运单--}}
    <el-dialog :title="dialogTitle" :visible.sync="dialogShow" :append-to-body="true" :close-on-click-modal="false" width="500px">
        <el-form :model="formData" ref="express" :rules="rules" size="small">
            <el-form-item label="选择快递" :label-width="formLabelWidth" prop="shipper_code">
                <el-col :span="20">
                    <el-select v-model="formData.shipper_code" placeholder="选择快递" size="small" style="width: 100%;">
                        <el-option label="顺丰快递" value="SF"></el-option>
                        <el-option label="圆通快递" value="YTO"></el-option>
                        <el-option label="中通快递" value="ZTO"></el-option>
                        <el-option label="申通快递" value="STO"></el-option>
                        <el-option label="韵达快递" value="YD"></el-option>
                        <el-option label="百世快递" value="HTKY"></el-option>
                    </el-select>
                </el-col>
            </el-form-item>
            <el-form-item label="快递单号" :label-width="formLabelWidth" prop="logistic_code">
                <el-col :span="20">
                    <el-input v-model="formData.logistic_code" auto-complete="off" placeholder="快递单号" ></el-input>
                </el-col>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="dialogShow = false">取 消</el-button>
            <el-button size="small" type="primary" @click="save">确 定</el-button>
        </div>
    </el-dialog>

    {{--导出--}}
    <el-dialog title="正在导出" :visible.sync="dialogDownload" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" width="165px">
        <el-progress type="circle" :percentage="downloadPercentage"></el-progress>
    </el-dialog>

    <el-dialog :visible.sync="dialogTrack" top="9vh">
        <div style="min-height: 100px;" v-loading="trackLoading">
            <el-steps direction="vertical" :active="0" space="50px" style="font-size: 10px;">
                <el-step v-for="item in trackData" :title="item.AcceptStation" icon="el-icon-location" :description="item.AcceptTime"></el-step>
            </el-steps>
        </div>
    </el-dialog>

@stop

@section('javascript')
    <script type="text/javascript"  src="/js/lib/xlsx.full.min.js"></script>
    <script type="text/javascript"  src="/js/lib/Blob.js"></script>
    <script type="text/javascript"  src="/js/lib/FileSaver.js"></script>
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop