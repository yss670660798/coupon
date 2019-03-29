@extends('admin.layout.layout')

@section('style')
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
    </style>
@stop

@section('content')

    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-date-picker v-model="param.date" type="date" placeholder="查询日期" value-format="yyyy-MM-dd" size="small" style="width: 150px;"></el-date-picker>
            <el-input v-model="param.keyword" placeholder="提领名称" size="small" style="width: 200px;"></el-input>
            <el-button size="small" type="primary" icon="el-icon-search" @click="search" >查询</el-button>
            <el-button size="small" icon="el-icon-plus" @click="addActivity" >添加</el-button>

        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="tableLoading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column prop="name" label="名称"></el-table-column>
                        <el-table-column prop="start_time" label="开始时间" :formatter="dateFormat"></el-table-column>
                        <el-table-column prop="end_time" label="结束时间":formatter="dateFormat"></el-table-column>
                        <el-table-column label="预告图" >
                            <template slot-scope="scope">
                                <el-button size="" type="text" icon="el-icon-picture" title="点击查看预告图" @click="showImg(scope.row.notice_img)"></el-button>
                            </template>
                        </el-table-column>
                        <el-table-column label="提领图" >
                            <template slot-scope="scope">
                                <el-button type="text" icon="el-icon-picture" title="点击查看提领图" @click="showImg(scope.row.title_img)"></el-button>
                            </template>
                        </el-table-column>
                        <el-table-column label="完结图" >
                            <template slot-scope="scope">
                                <el-button type="text" icon="el-icon-picture" title="点击查看完结图" @click="showImg(scope.row.end_img)"></el-button>
                            </template>
                        </el-table-column>
                        <el-table-column prop="status" label="状态">
                            <template slot-scope="scope">
                                <el-tag v-if="scope.row.status==1" type="success">已启用</el-tag>
                                <el-tag v-if="scope.row.status==2" type="warning">已停用</el-tag>
                                <el-tag v-if="scope.row.status==0" type="info">未启用</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="created_at" label="创建时间" ></el-table-column>
                        <el-table-column label="操作" width="180">
                            <template slot-scope="scope" >
                                <el-button size="mini" type="primary" icon="el-icon-edit" @click="editActivity(scope.row)" title="修改"></el-button>
                                <el-button size="mini" type="warning" v-if="scope.row.status==1" icon="el-icon-close" @click="statusChange(scope.row,2)" title="停用"></el-button>
                                <el-button size="mini" type="success" v-if="scope.row.status!=1" icon="el-icon-check" @click="statusChange(scope.row,1)" title="启用"></el-button>
                                <el-button size="mini" type="danger" icon="el-icon-delete" @click="deleteActivity(scope.row)" title="删除"></el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div style="margin-top: 10px;">
                        <el-pagination layout="prev, pager, next,total" :current-page.sync="param.page" :total="tableCount" :disabled="tableLoading" @current-change="currentChange" ></el-pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <el-dialog :title="imgTitle" :visible.sync="dialogImg">
        <img :src="imgUrl">
    </el-dialog>

@stop

@section('javascript')
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop