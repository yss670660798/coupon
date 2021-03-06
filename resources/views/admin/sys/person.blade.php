@extends('admin.layout.index')

@section('style')
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
    </style>
@stop

@section('nav')
    <li><a href="#" class="am-icon-home">系统管理</a></li>
    <li class="am-active">用户管理</li>
@stop

@section('content')

    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-input size="small" v-model="param.keyword" placeholder="品牌名称/编号/负责人" style="width: 250px;" clearable></el-input>
            <el-button size="small" type="primary" icon="el-icon-search" v-on:click="search2" >查询</el-button>
            <el-button icon="el-icon-plus" size="small" @click="addBrand">新增用户</el-button>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column prop="username" label="用户名" ></el-table-column>
                        <el-table-column prop="name" label="姓名" ></el-table-column>
                        <el-table-column prop="tel" label="电话" ></el-table-column>
                        <el-table-column prop="email" label="邮箱" ></el-table-column>
                        <el-table-column width="160" label="操作">
                            <template slot-scope="scope" >
                                <el-button size="mini" type="info" @click="editBrand(scope.row)">编辑</el-button>
                                <el-button size="mini" type="danger" @click="deleteBrand(scope.row)" >删除</el-button>
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

    <el-dialog :title="dialogTitle" :visible.sync="dialogShow" :append-to-body="true" :close-on-click-modal="false" width="500px" top="9vh">
        <el-form :model="formData" ref="store" :rules="rules" size="small">
            <el-form-item label="用户名" :label-width="formLabelWidth" prop="username">
                <el-col :span="20">
                    <el-input v-model="formData.username" auto-complete="off" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="姓名" :label-width="formLabelWidth" prop="name">
                <el-col :span="20">
                    <el-input v-model="formData.name" auto-complete="off" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="电话" :label-width="formLabelWidth">
                <el-col :span="20">
                    <el-input v-model="formData.tel" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="邮箱" :label-width="formLabelWidth" >
                <el-col :span="20">
                    <el-input v-model="formData.email" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="角色" :label-width="formLabelWidth">
                <el-col :span="10">
                    <el-select  placeholder="请选择角色"  v-model="formData.role_id" style="width: 100%;">
                        <el-option v-for="item in roleData"
                                   :key="item.id"
                                   :label="item.name"
                                   :value="item.id">
                        </el-option>
                    </el-select>
                </el-col>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="dialogShow = false">取 消</el-button>
            <el-button size="small" type="primary" @click="save">确 定</el-button>
        </div>
    </el-dialog>
@stop

@section('javascript')
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop