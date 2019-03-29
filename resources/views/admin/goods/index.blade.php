@extends('admin.layout.layout')

@section('style')
    <style type="text/css">
        .el-dialog__body{
            padding-top: 5px;
        }
    </style>
@stop

@section('nav')
    <li><a href="#" class="am-icon-home">商品管理</a></li>
    <li class="am-active">商品列表</li>
@stop

@section('content')

    <div class="tpl-portlet-components">
        <div class="portlet-title">
            <el-input size="small" v-model="param.keyword" placeholder="商品名称/编号" style="width: 250px;" clearable></el-input>
            <el-button size="small" type="primary" icon="el-icon-search" @click="search2" >查询</el-button>
            <el-button icon="el-icon-plus" size="small" @click="addGood">新增</el-button>
        </div>
        <div class="tpl-block">
            <div class="am-g">
                <div class="am-u-sm-12">
                    <el-table v-loading="loading" :data="tableData" stripe style="width: 100%;" size="small">
                        <el-table-column label="图片">
                            <template slot-scope="scope">
                                <img :src="'<?php echo $url ?>api/img/'+scope.row.image_id" width="80">
                            </template>
                        </el-table-column>
                        <el-table-column prop="sn" label="编号"></el-table-column>
                        <el-table-column prop="name" label="名称" ></el-table-column>
                        <el-table-column prop="price" label="价格" ></el-table-column>
                        <el-table-column prop="stock" label="库存" ></el-table-column>
                        <el-table-column label="操作">
                            <template slot-scope="scope" >
                                <el-button size="mini" icon="el-icon-edit" type="primary" @click="editGood(scope.row)" title="修改"></el-button>
                                <el-button size="mini" icon="el-icon-plus" @click="addStock(scope.row)" title="添加库存"></el-button>
                                <el-button size="mini" icon="el-icon-delete" type="danger" @click="deleteGood(scope.row)" title="删除"></el-button>
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

    {{--添加和修改--}}
    <el-dialog :title="dialogTitle" :visible.sync="dialogShow" :append-to-body="true" :close-on-click-modal="false" width="500px" top="9vh">
        <el-form :model="formData" ref="goods" :rules="rules" size="small">
            <el-form-item label="编号" :label-width="formLabelWidth" prop="sn">
                <el-col :span="20">
                    <el-input v-model="formData.sn" auto-complete="off" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="名称" :label-width="formLabelWidth" prop="name">
                <el-col :span="20">
                    <el-input v-model="formData.name" auto-complete="off" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="售价" :label-width="formLabelWidth">
                <el-col :span="20">
                    <el-input v-model="formData.price" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="售价" :label-width="formLabelWidth">
                <el-col :span="20">
                    <el-input type="textarea" v-model="formData.desc" auto-complete="off"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="商品图片" :label-width="formLabelWidth">
                <el-col :span="20">
                    <el-upload
                            class="upload-demo"
                            action="<?php echo $url ?>api/upload"
                            :on-success="uploadSuccess"
                            :on-error="uploadError"
                            :on-exceed="uploadExceed"
                            :file-list="fileList"
                            accept=".jpg,.png,.jpeg"
                            :limit="1"
                            list-type="picture">
                        <el-button size="small" type="primary">点击上传</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                    </el-upload>
                </el-col>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="dialogShow = false">取 消</el-button>
            <el-button size="small" type="primary" @click="save">确 定</el-button>
        </div>
    </el-dialog>

    {{--库存--}}
    <el-dialog title="添加库存" :visible.sync="dialogStock" :append-to-body="true" :close-on-click-modal="false" width="500px" top="9vh">
        <el-form :model="stockForm" ref="stock" :rules="stockRules" size="small" :label-width="formLabelWidth">
            <el-form-item label="商品名称">
                <el-col :span="20">
                    @{{stockForm.name}}
                </el-col>
            </el-form-item>
            <el-form-item label="当前库存">
                <el-col :span="20">
                    @{{stockForm.stock}}
                </el-col>
            </el-form-item>
            <el-form-item label="新增库存">
                <el-col :span="20">
                    <el-input-number v-model="stockForm.add_stock" :min="1" style="width: 100%;"></el-input-number>
                </el-col>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="dialogStock = false">取 消</el-button>
            <el-button size="small" type="primary" @click="saveStock">确 定</el-button>
        </div>
    </el-dialog>

@stop

@section('javascript')
    <script src="/static/admin/js/<?php echo $js_file?>"></script>
@stop