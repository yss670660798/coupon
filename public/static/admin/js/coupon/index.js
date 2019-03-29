


var vm=new Vue({
    el: '#app',
    created:function () {
        //$('#bg').hide();
        //this.param.page=1;
        var self=this;
        self.getGoods();
        self.search();
    },
    data: function() {
        return {
            bg_loading:false,
            param:{
                good_id:'',
                keyword:'',
                limit:10,
                page:1
            },
            formData:{
                name:'',
                goods_id:'',
                expired_at:'',
            },
            goodDisabled:false,
            tableData:[],
            goodsData:[],
            dialogShow:false,
            uploadShow:false,
            count:0,
            loading: true,
            formLabelWidth:'120px',
            ll:null,
            dialogTitle:'添加门店',
            requestUrl:'add',
            requestType:'post',
            rules: {
                goods_id: [
                    { required: true, message: '请选择产品', trigger: 'change' }
                ],
                expired_at: [
                    { required: true, message: '请选择有限期', trigger: 'change' }
                ],
                name: [
                    { required: true, message: '请填写标题', trigger: 'change' }
                ]
            },
            importUrl:'/api/coupon/import/',
            fileList:[]
        }
    },
    methods:{
        search2:function(){
            var self=this;
            self.param.page=1;
            self.search();
        },
        //查询列表
        search:function () {
            var _this=this;
            _this.loading=true;

            axios.get('/api/coupon',{params:_this.param}).then(function (value) {

                var data=value.data.data;

                _this.tableData=data;
                _this.loading=false;

                _this.count=value.data._count;
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
        //添加
        addCoupon:function () {

            this.formData.goods_id='';
            this.formData.expired_at='';
            this.formData.name='';

            this.dialogTitle='添加卡券';
            this.goodDisabled=false;

            this.dialogShow=true;

            this.requestUrl='';
            this.requestType='post';
        },
        //编辑
        editCoupon:function (row) {

            this.formData.goods_id=parseInt(row.goods_id);
            this.formData.expired_at=row.expired_at;
            this.formData.name=row.name;

            this.dialogTitle='编辑卡券';
            this.goodDisabled=false;
            if(row.used_count>0)
            {
                this.goodDisabled=true;
            }

            this.dialogShow=true;

            this.requestUrl='/'+row.id;
            this.requestType='put';

        },
        //删除产品
        deleteCoupon:function (row) {
            var _this=this;

            _this.$confirm('确定要删除吗?','提示',{type: 'warning'}).then(function (value) {
                axios.delete('/api/coupon/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        _this.$message.error(res.msg);
                        return false;
                    }
                    _this.$message.success('删除成功');
                    _this.search2();
                });
            })
        },
        importCoupon:function (row) {
            this.importUrl='/api/coupon/import/'+row.id;
        },
        submitUpload:function () {
            this.ll = this.$loading({
                lock: true,
                text: '正在导入',
                spinner: 'el-icon-loading',
                background: 'rgba(0, 0, 0, 0.7)'
            });
            this.$refs.upload.submit();
        },
        uploadSuccess:function (res, file, fileList) {
            this.ll.close();
            this.fileList=[];
            if(res.code!=0){
                this.$message.error(res.msg);
                return false;
            }
            this.uploadShow=false;
            this.search();
            this.$message.success('导入成功');
        },
        uploadError:function (error, file, fileList) {
            console.log(error);
            this.ll.close();
            this.fileList=[];
            this.$message.error('导入失败');
        },
        //保存数据
        save:function () {
            var _this=this;

            var url='/api/coupon'+_this.requestUrl;

            _this.$refs['coupon'].validate(function (valid) {
                if (valid) {
                    axios({
                        method: _this.requestType,
                        url: url,
                        data:_this.formData
                    }).then(function (response) {
                        var res=response.data;
                        if(res.code!=0){
                            _this.$message.error(res.msg);
                            return false;
                        }
                        _this.dialogShow=false;
                        _this.$message({
                            message: '提交成功',
                            type: 'success'
                        });
                        _this.search2();

                    }).catch(function (error) {
                        _this.$message.error('服务器出错，请联系管理员');
                        console.log(error);
                    });
                }
            })
        },
        getGoods:function(){

            var _this=this;
            axios.get('/api/get/goods').then(function (value) {
                if(value.data.code==0){
                    _this.goodsData=value.data.data;
                }
            });
        },

    },

});