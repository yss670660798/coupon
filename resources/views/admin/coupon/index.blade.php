@extends('admin.layout.layout')

@section('style')
    <link rel="stylesheet" href="/css/lib/iconfont.css">
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
        .el-input-number--small .el-input-number__decrease, .el-input-number--small .el-input-number__increase {
           margin-top: 1px;
        }
        .hr-height{
            margin: 5px auto;
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
            <el-select size="small"  placeholder="产品" v-model="param.good_id" clearable filterable>
                <el-option v-for="item in goodsData" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>
            <el-input size="small" v-model="param.keyword" placeholder="标题" style="width: 130px;" clearable></el-input>
            <el-button size="small" type="primary" icon="el-icon-search" @click="search2" >查询</el-button>
            <el-button icon="el-icon-plus" size="small" @click="addCoupon">新增</el-button>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        {{--<el-table-column type="index" width="50"></el-table-column>--}}
                        <el-table-column prop="name" label="标题"  ></el-table-column>
                        <el-table-column prop="good.name" label="产品" ></el-table-column>
                        <el-table-column label="卡券总数" >
                            <template slot-scope="scope">
                                <a :href="'/coupon/detail/'+scope.row.id">@{{ scope.row.total }}</a>
                            </template>
                        </el-table-column>
                        <el-table-column prop="used_count" label="已使用" > </el-table-column>
                        {{--<el-table-column prop="void_count" label="作废数" > </el-table-column>--}}
                        {{--<el-table-column prop="overdue_count" label="过期数" > </el-table-column>--}}
                        <el-table-column prop="expired_at" label="有限期" > </el-table-column>
                        <el-table-column width="200" label="操作" fixed="right">
                            <template slot-scope="scope">
                                <el-button size="mini" icon="el-icon-edit" type="primary" @click="editCoupon(scope.row)"></el-button>
                                <el-button size="mini" icon="el-icon-delete" type="danger" @click="deleteCoupon(scope.row)" ></el-button>

                                <el-upload style="margin-left: 10px;padding: 10px 0;display: inline-block;"
                                           class="upload-demo"
                                           :action="importUrl"
                                           :on-success="uploadSuccess"
                                           :on-error="uploadError"
                                           accept=".xlsx,.xls"
                                           :file-list="fileList"
                                           :auto-upload="false">
                                    <el-button size="mini" icon="el-icon-upload2" @click="importCoupon(scope.row)" ></el-button>
                                </el-upload>
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

    <el-dialog :title="dialogTitle" :visible.sync="dialogShow" :append-to-body="true" :close-on-click-modal="false" width="500px" >
        <el-form :model="formData" ref="coupon" :rules="rules" size="small">
            <el-form-item label="标题" :label-width="formLabelWidth" prop="name">
                <el-col :span="20">
                    <el-input v-model="formData.name" auto-complete="off" placeholder="请填写标题"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="产品" :label-width="formLabelWidth" prop="goods_id">
                <el-col :span="20">
                    <el-select  placeholder="请选择产品" v-model="formData.goods_id" style="width: 100%;" :disabled="goodDisabled">
                        <el-option v-for="item in goodsData" :key="item.id"  :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-col>
            </el-form-item>
            <el-form-item label="有效截至日期" :label-width="formLabelWidth" prop="expired_at">
                <el-col :span="20">
                    <el-date-picker style="width: 100%;" v-model="formData.expired_at" type="date" value-format="yyyy-MM-dd" placeholder="有效截至日期"></el-date-picker>
                </el-col>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="dialogShow = false">取 消</el-button>
            <el-button size="small" type="primary" @click="save">确 定</el-button>
        </div>
    </el-dialog>

    {{--<el-dialog title="导入" :visible.sync="uploadShow" :append-to-body="true" :close-on-click-modal="false" width="500px" >--}}
        {{--<el-form :model="formData" ref="importForm" :rules="rules2" size="small">--}}
            {{--<el-form-item label="导入类型" :label-width="formLabelWidth" prop="goods_id">--}}
                {{--<el-col :span="20">--}}
                    {{--<el-select  placeholder="请选择导入类型" v-model="formData.goods_id" style="width: 100%;" :disabled="goodDisabled">--}}
                        {{--<el-option label="销售导入" value="1"></el-option>--}}
                        {{--<el-option label="作废导入" value="2"></el-option>--}}
                    {{--</el-select>--}}
                    {{--<el-upload style="width:100%;margin: 0;padding: 10px 0;display: inline-block;"--}}
                            {{--class="upload-demo"--}}
                            {{--ref="upload"--}}
                            {{--:action="importUrl"--}}
                            {{--:on-success="uploadSuccess"--}}
                            {{--:on-error="uploadError"--}}
                            {{--accept=".xlsx,.xls"--}}
                            {{--:file-list="fileList"--}}
                            {{--:auto-upload="false">--}}
                        {{--<el-button slot="trigger" size="small" type="primary">选取文件</el-button>--}}
                        {{--<el-button style="margin-left: 10px;width: 80px;" size="small" type="success" @click="submitUpload">导入</el-button>--}}
                        {{--<div slot="tip" class="el-upload__tip">只能上传excel文件</div>--}}
                    {{--</el-upload>--}}
                {{--</el-col>--}}
            {{--</el-form-item>--}}
        {{--</el-form>--}}
    {{--</el-dialog>--}}
@stop

@section('javascript')
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop