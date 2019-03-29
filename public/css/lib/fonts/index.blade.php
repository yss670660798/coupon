@extends('layout.index')

@section('style')
    <style type="text/css">
        .el-button-span {
            white-space: normal!important;
            text-align: left;
        }
        .el-upload__tip{
            color:red
        }

        .el-pagination__sizes .el-input__inner{
            height: 28px!important;
        }

        .el-table--small td, .el-table--small th{
            padding: 2px;
        }

        .el-table .cell,.el-table th div{
            padding: 2px;
        }

        .el-tag--small{
            width: 65px;
            text-align: center;
        }
    </style>
@stop
@section('link')
@stop
@section('head')
    <a href="">项目管理</a>
    <a href="">项目立项</a>
@stop

@section('content')

    <div class="layui-card-header" style="height: 60px;line-height: 60px;">
        <el-date-picker size="small" v-model="dateRange"  type="daterange" start-placeholder="开始日期" end-placeholder="结束日期" style="width:250px;" value-format="yyyy-MM-dd"> </el-date-picker>
        <el-select v-model="param.customer_name" clearable filterable placeholder="客户名称" size="small">
            <el-option
                    v-for="item in customers"
                    :key="item.value"
                    :label="item.name"
                    :value="item.name">
            </el-option>
        </el-select>
        <el-input size="small" v-model="param.keyword" placeholder="项目编号/名称" style="width: 150px;" clearable></el-input>
        <el-button size="small" type="primary" icon="el-icon-search" v-on:click="search2" >查询</el-button>
        {{--<el-button icon="el-icon-plus" @click="cProject">新建项目</el-button>--}}
        <el-col :span="8" style="float: right;">
            <el-upload  ref="upload2" style="float: right;"
                        :data="uploadData"
                        :limit="1"
                        action="/api/project/upload"
                        :before-upload="onUpload"
                        :on-success="uploadSuccess"
                        :on-error="uploadError"
                        :show-file-list="false">
                <el-button size="small" slot="trigger" icon="el-icon-upload2" @click="uploadClick">上传立项单</el-button>
                <el-button size="small" icon="el-icon-download" @click="download"  type="success" >下载模版</el-button>
            </el-upload>
        </el-col>


    </div>
    <div class="layui-card-body">
        <el-table v-loading="loading" :data="tableData" stripe style="width: 100%"  size="small">
            <el-table-column type="expand">
                <template slot-scope="props">
                    <el-table v-loading="loading" size="mini" :data="props.row.annex" stripe style="width: 100%" >
                        <el-table-column prop="id" label="附件编号"></el-table-column>
                        <el-table-column prop="name" label="附件类型" ></el-table-column>
                        <el-table-column prop="file_name" label="文件名" ></el-table-column>
                        <el-table-column prop="created_at" label="上传时间" :formatter="format1" width="140"></el-table-column>
                        <el-table-column width="130" label="操作">
                            <template slot-scope="scope">
                                <el-button size="mini" icon="el-icon-download" title="下载" @click="downloadAnnex(scope.row.id)"></el-button>
                                <el-button v-if="scope.row.type!=1" size="mini" icon="el-icon-delete" type="danger" title="删除" @click="removeAnnex(scope.row.id)"></el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </template>
            </el-table-column>
            <el-table-column prop="code" label="项目编号" width="100"></el-table-column>
            <el-table-column label="项目名称" >
                <template slot-scope="scope">
                    <el-button type="text" class="el-button-span"  @click="hrefClick(scope.row.id)">@{{ scope.row.name }}</el-button>
                </template>
            </el-table-column>
            <el-table-column prop="version.project_person" label="项目经理" width="70"></el-table-column>
            <el-table-column prop="version.customer_name" label="客户名称" width="200"></el-table-column>
            <el-table-column prop="created_at" label="上传时间" width="135" :formatter="format1"></el-table-column>
            <el-table-column label="审批状态" width="155">
                <template slot-scope="scope">
                    <el-tag :type="statusClass[scope.row.approve_status]" close-transition size="small">
                        @{{statusName[scope.row.approve_status]}}
                    </el-tag>
                    @{{scope.row.approve_text}}
                </template>
            </el-table-column>
            <el-table-column prop="annex.length" label="附件数" width="64"></el-table-column>
            <el-table-column width="160" label="操作">
                <template slot-scope="scope" >
                    <el-button v-if="false" size="mini" @click="editProject(scope.row)">编辑</el-button>
                    <el-popover v-if="scope.row.approve_status==1" placement="top" width="160" v-model="scope.row.popover">
                        <p>确定删除吗？</p>
                        <div style="text-align: right; margin: 0">
                            <el-button size="mini" type="text" @click="scope.row.popover= false">取消</el-button>
                            <el-button type="primary" size="mini" @click="deleteProject(scope.$index,scope.row)">确定</el-button>
                        </div>
                        <el-button size="mini" slot="reference"   type="danger" @click="deleteTip = true" >删除</el-button>
                    </el-popover>
                    <el-button size="mini" v-if="scope.row.approve_status==1||scope.row.approve_status==3 || scope.row.approve_status==4" type="info" @click="uploadClick(scope.row)" >上传附件</el-button>
                </template>
            </el-table-column>
        </el-table>
        {{--<el-pagination background layout="prev, pager, next" :total="1000"></el-pagination>--}}
        <div class="block" style="margin-top: 10px;">
            <el-pagination layout="prev, pager, next,total,sizes" :current-page.sync="param.page" :total="count" :disabled="pageDisabled" @current-change="currentChange" @size-change="sizeChange" :page-sizes="[10, 20, 50]"></el-pagination>
        </div>
    </div>

    <el-dialog :title="dialogTitle" :visible.sync="createProject" :append-to-body="true" :close-on-click-modal="false" width="460px">
        <el-form :model="projectInfo" size="small">
            <el-form-item label="项目编号" :label-width="formLabelWidth">
                <el-col :span="18">
                    <el-input v-model="projectInfo.code" auto-complete="off" :disabled="codeDisabled"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="项目名称" :label-width="formLabelWidth">
                <el-col :span="18">
                    <el-input v-model="projectInfo.name" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="项目经理" :label-width="formLabelWidth">
                <el-col :span="18">
                    <el-select v-model="projectInfo.project_person" clearable placeholder="请选择项目经理" style="width: 100%;">
                        <el-option
                                v-for="item in persons"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-col>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="createProject = false">取 消</el-button>
            <el-button size="small" type="primary" @click="addProject">确 定</el-button>
        </div>
    </el-dialog>

    <el-dialog title="上传附件" :visible.sync="annex" :append-to-body="true" :close-on-click-modal="false" width="460px" @close="handleClose">
        <el-form :model="projectInfo" size="small">
            <el-form-item label="附件类型" :label-width="formLabelWidth" required>
                <el-col :span="20">
                    <el-select v-model="uploadData.type" clearable placeholder="请选择附件类型" style="width: 100%;">
                        <el-option
                                v-for="item in annex_types"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                    <el-upload style="width:100%;margin: 0;padding: 4px 0;display: inline-block;" ref="upload"
                               :data="uploadData"
                               :limit="1" :on-exceed="onExceed"
                               action="/api/project/upload" :before-upload="onUpload"
                               :on-success="uploadSuccess" :on-error="uploadError"
                               :file-list="fileList" :auto-upload="false">
                        <el-button size="small" slot="trigger" type="primary" >选择文件</el-button>
                        <el-button size="small" type="success" @click="submitUpload" >上传文件</el-button>
                        <div slot="tip" class="el-upload__tip">
                            ☆ 必须选择附件类型才能上传<br>
                            ☆ 项目报价单必须上传<br>
                            ☆ 确认邮件/项目合同/PO单，三者必须有其一
                        </div>
                    </el-upload>
                </el-col>
            </el-form-item>
        </el-form>
    </el-dialog>


@stop

@section('javascript')
    {{--<script src="/js/project/index.js"></script>--}}
    <script type="text/javascript">
        document.write("<script src='/js/project/index.js?v=" + Math.round(Math.random() * 10000) + "'><" + "/script>");
    </script>
@stop