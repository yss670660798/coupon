@extends('admin.layout.layout')

@section('style')
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
    </style>
@stop

@section('nav')
    <li><a href="#" class="am-icon-home">系统管理</a></li>
    <li class="am-active">日志管理</li>
@stop

@section('content')

    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-date-picker size="small" v-model="param.date" type="date" placeholder="查询日期" value-format="yyyy-MM-dd" clearable style="width: 230px;"></el-date-picker>
            <el-input size="small" v-if="false" v-model="param.keyword" placeholder="未定义" style="width: 200px;" clearable></el-input>
            <el-button size="small" type="primary" icon="el-icon-search" @click="search2" >查询</el-button>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column prop="id" label="id" width="50"></el-table-column>
                        <el-table-column prop="user.name" label="用户" width="100"></el-table-column>
                        <el-table-column prop="method" label="请求类型" width="80"></el-table-column>
                        <el-table-column prop="ip" label="ip地址" width="100"></el-table-column>
                        <el-table-column prop="path" label="路由" ></el-table-column>
                        <el-table-column  label="参数" width="80">
                            <template slot-scope="scope">
                                <el-popover ref="popover"  placement="left" title="参数"  width="200" trigger="click" >
                                    <pre>@{{ scope.row.params }}</pre>
                                </el-popover>
                                <el-button size="mini" v-popover:popover>查看</el-button>
                            </template>
                        </el-table-column>
                        {{--<el-table-column prop="fun" label="功能" width="60"></el-table-column>--}}
                        <el-table-column prop="created_at" label="创建日期" width="140"></el-table-column>
                        <el-table-column width="80" label="操作">
                            <template slot-scope="scope" >
                                <el-button size="mini" type="danger" @click="deleteLog(scope.row)" >删除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div style="margin-top: 10px;">
                        <el-pagination layout="prev, pager, next,total,sizes" :current-page.sync="param.page" :total="count" :disabled="pageDisabled" @current-change="currentChange"  @size-change="sizeChange" :page-sizes="[10, 20, 50]"></el-pagination>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop