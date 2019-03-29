


var vm=new Vue({
    el: '#app',
    created:function () {
        //$('#bg').hide();
        this.search();
    },
    mounted:function () {
        //this.bg_loading=false;
    },
    data: function() {
        return {
            bg_loading:false,
            param:{
                keyword:'',
                limit:10,
                page:1
            },
            formData:{
                sn:'',
                name:'',
                image_id:0,
                price:'',
                desc:''
            },
            stockForm:{
                name:'',
                stock:0,
                add_stock:1,
            },
            fileList:[],
            tableData:[],
            dialogShow:false,
            dialogStock:false,
            count:0,
            loading: true,
            formLabelWidth:'120px',
            fullScreenLoading:false,
            ll:null,
            dialogTitle:'添加门店',
            requestUrl:'add',
            requestType:'post',
            rules: {
                sn: [
                    { required: true, message: '请填写商品编码', trigger: 'blur' },
                ],
                price: [
                    { required: true, message: '请填写商品售价', trigger: 'blur' }
                ],
                name: [
                    { required: true, message: '请填写商品名称', trigger: 'blur' }
                ],
            },
            stockRules:{

            }
        }
    },
    methods:{
        search2:function(){
            var _this=this;
            _this.param.page=1;
            _this.search();
        },
        //查询列表
        search:function () {
            var _this=this;
            _this.loading=true;

            axios.get('/api/goods',{params:_this.param}).then(function (response) {
                _this.loading=false;
                var res=response.data;
                if(res.code!=0){
                    _this.$message.error(res.msg);
                    return false;
                }
                _this.tableData=res.data;
                _this.count=res._count;

            }).catch(function (error) {
                _this.$message.error('服务器错误，请刷新重试');
                console.log(error);
            });
        },
        //分页查询
        currentChange:function (currentPage) {
            this.param.page=currentPage;
            this.search();
        },
        //分页条数改变
        sizeChange:function (pageSize) {
            this.param.limit=pageSize;
            this.param.page=1;
            this.search();
        },
        //调整时间格式
        format1:function (row,column,cellValue,index) {
            return cellValue.substring(0,19);
        },
        //添加产品
        addGood:function () {

            this.formData.sn='';
            this.formData.name='';
            this.formData.price='';
            this.formData.desc='';
            this.fileList=[];
            this.dialogTitle='添加商品';

            this.dialogShow=true;

            this.requestUrl='';
            this.requestType='post';
        },
        addStock:function (row) {

            this.stockForm.name=row.name;
            this.stockForm.stock=row.stock;
            this.stockForm.add_stock=1;

            this.requestUrl='/'+row.id;

            this.dialogStock=true;
        },
        //编辑产品
        editGood:function (row) {
            this.formData.sn=row.sn;
            this.formData.name=row.name;
            this.formData.image_id=row.image_id;
            this.formData.price=row.price;
            this.formData.desc=row.desc;

            this.dialogTitle='编辑商品';
            this.fileList=[];

            if(row.img){
                this.fileList.push({
                    name:row.img.file_name,
                    url:row.img.file_url+'img/'+row.image_id
                });
            }

            this.dialogShow=true;

            this.requestUrl='/'+row.id;
            this.requestType='put';
        },
        //删除产品
        deleteGood:function (row) {
            var _this=this;

            _this.$confirm('确定要删除吗?','提示',{
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function (value) {
                axios.delete('/api/goods/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        _this.$message.error(res.msg);
                        return false;
                    }
                    _this.$message({
                        message: '删除成功',
                        type: 'success'
                    });
                    _this.search2();
                });
            });
        },
        uploadSuccess:function (res, file, fileList) {
            var _this=this;
            if(res.code==0){
                _this.formData.image_id=res.data.id;
            }
        },
        uploadError:function (error, file, fileList) {
            console.log(fileList);
        },
        uploadExceed:function (files, fileList) {
            this.$message.error('请先删除旧的文件');
        },
        //保存数据
        save:function () {
            var _this=this;

            var url='/api/goods'+_this.requestUrl;

            _this.$refs['goods'].validate(function (valid) {
                if (valid) {
                    axios({method: _this.requestType, url: url,data:_this.formData}).then(function (response) {
                        var res=response.data;
                        if(res.code!=0){
                            _this.$message.error(res.msg);
                            return false;
                        }
                        _this.dialogShow=false;
                        _this.$message.success('添加库存成功');
                        _this.search2();

                    }).catch(function (error) {
                        _this.$message.error('服务器出错，请联系管理员');
                    });
                }
            })
        },
        saveStock:function () {
            var _this=this;

            var url='/api/goods/stock'+_this.requestUrl;

            _this.$refs['stock'].validate(function (valid) {
                if (valid) {
                    axios.put(url,_this.stockForm).then(function (response) {
                        var res=response.data;
                        if(res.code!=0){
                            _this.$message.error(res.msg);
                            return false;
                        }
                        _this.dialogStock=false;
                        _this.$message.success('提交成功');
                        _this.search();
                    }).catch(function (error) {
                        _this.$message.error('服务器错误');
                        console.log(error);
                    });
                }
            })
        }
    },

});