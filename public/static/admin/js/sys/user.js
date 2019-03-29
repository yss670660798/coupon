


var vm=new Vue({
    el: '#app',
    created:function () {
        this.getRole();
        this.search();
    },
    mounted:function () {
        this.bg_loading=false;
    },
    data: function() {
        return {
            bg_loading:true,
            param:{
                keyword:'',
                status:'',
                limit:10,
                page:1
            },
            formData:{
                username:'',
                name:'',
                password:'',
                tel:'',
                email:'',
                role_id:''
            },
            tableData:[],
            dialogShow:false,
            count:0,
            loading: true,
            formLabelWidth:'120px',
            dialogTitle:'添加门店',
            requestUrl:'add',
            requestType:'post',
            roleData:[],
            rules: {
                code: [
                    { required: true, message: '请填写品牌编号', trigger: 'blur' }
                ],
                manager: [
                    { required: true, message: '请填写负责人', trigger: 'blur' }
                ],
                name: [
                    { required: true, message: '请填写姓名', trigger: 'blur' }
                ],
                username: [
                    { required: true, message: '请填写用户名', trigger: 'blur' }
                ],
            },
        }
    },
    methods:{
        search:function(){
            var _this=this;
            _this.param.page=1;
            _this.getData();
        },
        //查询列表
        getData:function () {
            var _this=this;
            _this.loading=true;

            axios.get('/api/sys/user',{params:_this.param}).then(function (response) {

                var res=response.data;

                _this.tableData=res.data;
                _this.loading=false;

                _this.count=res._count;

            }).catch(function (err) {
                _this.loading=false;
                _this.tableData=[];
                console.log(err);
                _this.$message.error('系统错误,请联系管理员');
            });
        },
        getRole:function () {
            var _this=this;
            axios.get('/api/sys/role/list',{params:{}}).then(function (response) {
                var res=response.data;
                _this.roleData=res.data;
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
        addUser:function () {

            this.formData.username='';
            this.formData.name='';
            this.formData.role_id='';
            this.formData.tel='';
            this.formData.email='';

            this.dialogTitle='添加用户';

            this.dialogShow=true;

            this.requestUrl='';
            this.requestType='post';
        },
        //编辑
        editUser:function (row) {

            this.formData.username=row.username;
            this.formData.name=row.name;
            this.formData.role_id=parseInt(row.role_id);
            this.formData.tel=row.tel;
            this.formData.email=row.email;


            this.dialogTitle='修改用户';

            this.dialogShow=true;

            this.requestUrl='/'+row.id;
            this.requestType='put';
        },
        //删除
        delUser:function (row) {
            var _this=this;

            _this.$confirm('确定要删除吗，删除之后品牌将不可登录','提示',{
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function (value) {
                axios.delete('/api/sys/user/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        _this.$message.error(res.msg);
                        return false;
                    }
                    _this.$message({
                        message: '删除成功',
                        type: 'success'
                    });
                    _this.search();
                });
            }).catch(function () {
                _this.$message.error('请刷新后重试');
            });

        },
        //保存数据
        save:function () {
            var _this=this;

            var url='/api/sys/user'+_this.requestUrl;

            _this.$refs['user'].validate(function (valid) {
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
                        _this.search();

                    }).catch(function (error) {
                        _this.$message.error('服务器出错，请联系管理员');
                        console.log(error);
                    });
                }
            })
        },
        resetPwd:function (row) {
            var _this=this;
            _this.$confirm('确定要重置密码吗?','提示',{type: 'warning'}).then(function (value) {
                axios.put('/api/sys/user/reset/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        _this.$message.error(res.msg);
                        return false;
                    }
                    _this.$message.success('重置成功');
                }).catch(function (err) {
                    _this.$message.error(err.name+':'+err.message);
                });
            });
        }
    },

});