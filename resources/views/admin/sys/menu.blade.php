@extends('admin.layout.index')

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

@section('nav')
    <li><a href="#" class="am-icon-home">系统管理</a></li>
    <li class="am-active">菜单管理</li>
@stop

@section('content')
    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-select v-model="param.keyword" size="small">
                <el-option  label="后台菜单" value="10"></el-option>
                <el-option  label="品牌菜单" value="0"></el-option>
            </el-select>
            <el-button size="small" type="primary" icon="el-icon-refresh" @click="search2" >刷新</el-button>
            <el-button icon="el-icon-plus" size="small" @click="openDialog">新增主菜单</el-button>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column type="expand">
                            <template slot-scope="props"  v-if="props.row.child.length>0">
                                <h4 style="margin-bottom: 2px;border-bottom: 1px solid #eee;">子菜单列表</h4>
                                <el-table  :data="props.row.child" stripe style="width: 100%;" size="small">
                                    {{--<el-table-column prop="id" label="主键" width="60"></el-table-column>--}}
                                    <el-table-column prop="menu_code" label="编号" width="70"></el-table-column>
                                    <el-table-column prop="name" label="名称" width="80"></el-table-column>
                                    <el-table-column prop="byname" label="byName"></el-table-column>
                                    <el-table-column prop="url" label="地址" ></el-table-column>
                                    <el-table-column prop="remark" label="描述" ></el-table-column>
                                    <el-table-column width="100" label="操作">
                                        <template slot-scope="scope" >
                                            <el-dropdown @command="handleSubCommand(scope.row,$event)"  size="small" trigger="click" >
                                                <el-button  size="mini" type="primary">
                                                    菜单<i class="el-icon-arrow-down el-icon--right"></i>
                                                </el-button>
                                                <el-dropdown-menu slot="dropdown">
                                                    <el-dropdown-item command="edit">编辑</el-dropdown-item>
                                                    <el-dropdown-item command="delete" >删除</el-dropdown-item>
                                                </el-dropdown-menu>
                                            </el-dropdown>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </template>
                        </el-table-column>
                        {{--<el-table-column prop="id" label="主键" width="60"></el-table-column>--}}
                        <el-table-column prop="menu_code" label="编号" width="70"></el-table-column>
                        <el-table-column prop="name" label="名称" width="80"></el-table-column>
                        <el-table-column prop="byname" label="byName" ></el-table-column>
                        <el-table-column prop="url" label="地址" ></el-table-column>
                        <el-table-column prop="remark" label="描述" ></el-table-column>
                        <el-table-column width="100" label="操作">
                            <template slot-scope="scope" >
                                <el-dropdown @command="handleCommand(scope.row,$event)" size="small" trigger="click">
                                    <el-button  size="mini">
                                        菜单<i class="el-icon-arrow-down el-icon--right"></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item command="edit">编辑</el-dropdown-item>
                                        <el-dropdown-item command="delete">删除</el-dropdown-item>
                                        <el-dropdown-item command="add" divided :disabled="scope.row.url!=null">新增子菜单</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
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
            <el-form-item label="id" :label-width="formLabelWidth" v-show="false">
                <el-col :span="20">
                    <el-input v-model="formData.parent_id"  ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="编号" :label-width="formLabelWidth" prop="menu_code">
                <el-col :span="20">
                    <el-input v-model="formData.menu_code" auto-complete="off" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="名称" :label-width="formLabelWidth" prop="name">
                <el-col :span="20">
                    <el-input v-model="formData.name" auto-complete="off" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="ByName" :label-width="formLabelWidth" prop="byname">
                <el-col :span="20">
                    <el-input v-model="formData.byname" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="URL" :label-width="formLabelWidth" prop="url">
                <el-col :span="20">
                    <el-input v-model="formData.url" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="排序" :label-width="formLabelWidth" >
                <el-col :span="20">
                    <el-input-number v-model="formData.sort" auto-complete="off" :min="1"></el-input-number>
                </el-col>
            </el-form-item>
            <el-form-item label="是否显示" :label-width="formLabelWidth" >
                <el-col :span="20">
                    <el-checkbox v-model="formData.is_show"> </el-checkbox>
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