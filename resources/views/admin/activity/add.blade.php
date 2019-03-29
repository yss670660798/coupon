@extends('admin.layout.layout')

@section('style')
    <style type="text/css">
        .el-upload__tip{
            margin-top: 0;
        }
        .el-upload-list--picture {
            width: 350px;
        }
    </style>
@stop
@section('content')

    <div class="tpl-portlet-components">

        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-form ref="activity" :model="formData" label-width="200px">
                        <el-form-item label="提领名称">
                            <el-input v-model="formData.name" placeholder="提领名称" style="width: 350px;" clearable></el-input>
                        </el-form-item>
                        <el-form-item label="提领时间">
                            <el-date-picker v-model="formData.date" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" value-format="yyyy-MM-dd" :disabled="is_active"></el-date-picker>
                        </el-form-item>
                        <el-form-item label="预告图">
                            <el-upload
                                    class="upload-demo"
                                    :action="uploadUrl"
                                    :limit="1"
                                    :file-list="fileList1"
                                    :on-success="uploadSuccess"
                                    :on-error="uploadError"
                                    :on-exceed="uploadExceed"
                                    list-type="picture">
                                <el-button size="small" type="primary" @click="uploadClick('notice_img')">点击上传</el-button>
                                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                            </el-upload>
                        </el-form-item>
                        <el-form-item label="提领图">
                            <el-upload
                                    class="upload-demo"
                                    :action="uploadUrl"
                                    :limit="1"
                                    :file-list="fileList2"
                                    :before-upload="beforeUpload"
                                    :on-success="uploadSuccess"
                                    :on-error="uploadError"
                                    :on-exceed="uploadExceed"
                                    list-type="picture">
                                <el-button size="small" type="primary" @click="uploadClick('title_img')">点击上传</el-button>
                                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                            </el-upload>
                        </el-form-item>
                        <el-form-item label="结束图">
                            <el-upload
                                    class="upload-demo"
                                    :action="uploadUrl"
                                    :limit="1"
                                    :file-list="fileList3"
                                    :before-upload="beforeUpload"
                                    :on-success="uploadSuccess"
                                    :on-error="uploadError"
                                    :on-exceed="uploadExceed"
                                    list-type="picture">
                                <el-button size="small" type="primary" @click="uploadClick('end_img')">点击上传</el-button>
                                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                            </el-upload>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="save" :loading="saveLoading">提交</el-button>
                            <el-button @click="cancel" :disabled="saveLoading">取消</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop