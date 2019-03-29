


var vm=new Vue({
    el: '#app',
    created:function () {
        this.getMenu();
        this.search();
    },
    mounted:function(){
        this.bg_loading=false;
    },
    data: function() {
        return {
            param:{
                keyword:'',
                limit:10,
                page:1
            },
            roleData:{
                name:'',
                note:'',
                resource:[],
                half_resource:[]
            },
            tableData:[],
            dialogShow:false,
            count:0,
            bg_loading: true,
            loading: false,
            formLabelWidth:'120px',
            pageDisabled:true,
            dialogTitle:'添加角色',
            requestUrl:'add',
            requestType:'post',
            rules: {
                name: [
                    { required: true, message: '请填写角色名称', trigger: ['blur','change'] }
                ]
            },
            menuData:[],
            defaultProps:{
                children: 'child',
                label: 'name'
            },
            defaultChecked:[]
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
            axios.get('/api/sys/role',{params:self.param}).then(function (response) {
                _this.loading=false;
                var res=response.data;
                if(res.code!=0){
                    _this.$message.error(res.msg);
                    _this.tableData=[];
                    _this.count=0;
                    return false;
                }
                _this.tableData=res.data;
                _this.count=res._count;

            });
        },
        //分页查询
        currentChange:function (currentPage) {
            this.param.page=currentPage;
            this.getData();
        },
        //分页条数改变
        sizeChange:function (pageSize) {
            this.param.limit=pageSize;
            this.search();
        },
        //添加
        addRole:function (parentId) {

            this.roleData.name='';
            this.roleData.note='';
            this.roleData.resource=[];
            this.roleData.half_resource=[];
            this.dialogTitle='添加角色';
            this.dialogShow=true;
            this.requestUrl='';
            this.requestType='post';
        },
        //编辑
        editRole:function (row) {

            this.roleData.name=row.name;
            this.roleData.note=row.note;
            this.roleData.resource=row.resource;
            //this.roleData.name=row.name;

            this.dialogTitle='修改角色';
            this.dialogShow=true;

            this.requestUrl='/'+row.id;
            this.requestType='put';

            this.defaultChecked=row.resource;
            this.$refs.resource.setCheckedKeys(row.resource);
        },
        //删除
        delRole:function (row) {
            var _this=this;
            _this.$confirm('确定要删除吗?','提示',{type: 'warning'}).then(function (value) {
                axios.delete('/api/sys/role/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        _this.$message.error(res.msg);
                        return false;
                    }
                    _this.$message.success('删除成功');
                    _this.search();
                });
            }).catch(function (err) {
                _this.$message.error('服务器出错，请联系管理员');
            });
        },
        //保存数据
        save:function () {
            var _this=this;
            var url='/api/sys/role'+_this.requestUrl;

            _this.roleData.resource=_this.$refs.resource.getCheckedKeys();
            _this.roleData.half_resource=_this.$refs.resource.getHalfCheckedKeys();

            console.log(_this.roleData);
            if(_this.roleData.resource.length<=0){
                _this.$message.error('请选择权限');
            }

            _this.$refs['role'].validate(function (valid) {
                if (valid) {
                    axios({
                        method: _this.requestType,
                        url: url,
                        data:_this.roleData
                    }).then(function (response) {
                        var res=response.data;
                        if(res.code!=0){
                            _this.$message.error(res.msg);
                            return false;
                        }
                        _this.dialogShow=false;
                        _this.$message.success('提交成功');
                        _this.search();
                    }).catch(function (error) {
                        _this.$message.error('服务器出错，请联系管理员');
                    });
                }
            })
        },
        getMenu:function () {
            var _this=this;
            axios.get('/api/sys/menu',{params:self.param}).then(function (response) {
                var res=response.data;
                _this.menuData=res.data;
            });
        }

    },

});