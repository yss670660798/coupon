@extends('admin.layout.layout')

@section('style')
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
        .el-input-number--small .el-input-number__increase,.el-input-number--small .el-input-number__decrease {
            margin-top: 1px;
        }
    </style>
@stop

@section('content')
    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-input size="small" v-model="param.keyword" placeholder="角色名称" style="width: 200px;"></el-input>
            <el-button size="small" type="primary" icon="el-icon-search" @click="search" >查询</el-button>
            <el-button icon="el-icon-plus" size="small" @click="addRole">新增</el-button>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column prop="id" label="编号"></el-table-column>
                        <el-table-column prop="name" label="名称" ></el-table-column>
                        <el-table-column prop="note" label="描述" ></el-table-column>
                        <el-table-column width="160" label="操作">
                            <template slot-scope="scope" >
                                <el-button-group>
                                    <el-button size="mini" type="info" @click="editRole(scope.row)" >编辑</el-button>
                                    <el-button size="mini" type="danger" @click="delRole(scope.row)">删除</el-button>
                                </el-button-group>
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
        <el-form :model="roleData" ref="role" size="small">
            <el-form-item label="角色名" :label-width="formLabelWidth" :rules="[{ required: true, message: '请输入角色名', trigger: 'blur' }]">
                <el-col :span="15">
                    <el-input v-model="roleData.name" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="简介" :label-width="formLabelWidth">
                <el-col :span="15">
                    <el-input v-model="roleData.note" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="权限" :label-width="formLabelWidth">
                <el-col :span="15">
                    <el-tree :data="menuData" ref="resource" :default-expand-all="true" :default-checked-keys="defaultChecked" show-checkbox node-key="id" :props="defaultProps"></el-tree>
                </el-col>
            </el-form-item>

        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="dialogShow = false">取 消</el-button>
            <el-button size="small" type="primary" @click="save">确 定</el-button>
        </div>
    </el-dialog>@stop

@section('javascript')
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop