


var vm=new Vue({
    el: '#app',
    created:function () {
        //$('#bg').hide();
        //this.param.page=1;
        var self=this;

        self.search();
    },
    data: function() {
        return {
            param:{
                keyword:'10',
                limit:10,
                page:1
            },
            formData:{
                menu_code:'',
                menu_level:1,
                name:'',
                byname:'',
                parent_id:null,
                url:'',
                sort:999,
                is_show:true
            },
            tableData:[],
            dialogShow:false,
            count:0,
            loading: true,
            formLabelWidth:'120px',
            pageDisabled:true,
            ll:null,
            dialogTitle:'添加门店',
            requestUrl:'add',
            requestType:'post',
            rules: {
                menu_code: [
                    { required: true, message: '请填写编号', trigger: 'blur' },
                    { min: 3, max: 30, message: '长度在 3 到 30 个字符', trigger: ['blur','change'] },
                    { pattern: /^[0-9]+$/, message: '0-9数字，不允许特殊符号' , trigger: ['blur','change']}
                ],
                name: [
                    { required: true, message: '请填写名称', trigger: ['blur','change'] }
                ],
                byname: [
                    { required: true, message: '请填写', trigger: ['blur','change'] }
                ],
                url:[
                    // { required: true, message: '请填写', trigger: ['blur','change'] }
                ],
            }

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
            var self=this;
            self.loading=true;
            self.pageDisabled=true;
            axios.get('/api/admin/sys/menu',{params:self.param}).then(function (value) {
                var data=value.data.data;
                self.tableData=data;
                self.loading=false;
                self.count=value.data._count;
                self.pageDisabled=false;
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
        //添加主菜单
        openDialog:function(){
            var _this=this;
            this.dialogTitle='添加主菜单';
            _this.addMenu(_this.param.keyword);
        },
        //添加子菜单
        addSubMenu:function(parentId){
            var _this=this;
            _this.dialogTitle='添加子菜单';
            _this.addMenu(parentId);
        },
        //添加
        addMenu:function (parentId) {

            this.formData.menu_code='';
            this.formData.menu_level=1;
            this.formData.name='';
            this.formData.byname='';
            this.formData.parent_id=parentId;
            this.formData.url='';
            this.formData.sort=null;
            this.formData.is_show=true;
            this.dialogShow=true;
            this.requestUrl='';
            this.requestType='post';
        },
        //编辑
        editMenu:function (row) {

            this.formData.menu_code=row.menu_code;
            this.formData.menu_level=row.menu_level;
            this.formData.name=row.name;
            this.formData.byname=row.byname;
            this.formData.parent_id=row.parent_id;
            this.formData.url=row.url;
            this.formData.sort=row.sort;
            this.formData.is_show=row.is_show==1?true:false;

            this.dialogTitle='编辑菜单';

            this.dialogShow=true;

            this.requestUrl='/'+row.id;
            this.requestType='put';
        },
        //删除
        deleteMenu:function (row) {
            var self=this;
            self.$confirm('确定要删除吗?','提示',{type: 'warning'}).then(function (value) {
                axios.delete('/api/admin/menu/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        self.$message.error(res.msg);
                        return false;
                    }
                    self.$message.success('删除成功');
                    self.search2();
                });
            }).catch(function (err) {
            });
        },
        //保存数据
        save:function () {
            var self=this;
            var url='/api/admin/sys/menu'+self.requestUrl;
            self.$refs['store'].validate(function (valid) {
                if (valid) {
                    axios({method: self.requestType, url: url,data:self.formData}).then(function (response) {
                        var res=response.data;
                        if(res.code!=0){
                            self.$message.error(res.msg);
                            return false;
                        }
                        self.dialogShow=false;
                        self.$message.success('提交成功');
                        self.search2();
                    }).catch(function (error) {
                        self.$message.error('服务器出错，请联系管理员');
                    });
                }
            })
        },
        handleCommand:function (row,command) {
            switch (command){
                case 'edit':
                    this.editMenu(row);
                    break;
                case 'delete':
                    this.deleteMenu(row);
                    break;
                case 'add':
                    this.addSubMenu(row.id)
                    break;
            }
        },
        handleSubCommand:function (row,command) {
            switch (command){
                case 'edit':
                    this.editMenu(row);
                    break;
                case 'delete':
                    this.deleteMenu(row);
                    break;
            }
        },

    },

});