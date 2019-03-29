
var vm=new Vue({
    el:'#app',
    created:function () {
        this.search();
    },
    mounted:function () {

    },
    data:function () {
        return {
            bg_loading:false,
            param:{
                active_time:'',
                keyword:'',
                limit:10,
                page:1
            },
            tableLoading:false,
            tableData:[],
            tableCount:0,
            dialogImg:false,
            imgTitle:'',
            imgUrl:''
        }
    },
    methods:{
        search:function () {
            this.param.page=1;
            this.getData();
        },
        getData:function () {
            var _this=this;
            _this.tableLoading=true;
            axios.get('/api/activity',{params:_this.param}).then(function (response) {
                _this.tableLoading=false;
                var res=response.data;
                if(res.code!=0){
                    _this.$message.error(res.msg);
                    return false;
                }
                _this.tableData=res.data;
                _this.tableCount=res._count;
            });
        },
        currentChange:function (currentPage) {
            this.param.page=currentPage;
            this.getData();
        },
        dateFormat:function (row, column, cellValue, index) {
            return cellValue.substr(0,10);
        },
        showImg:function (imgId) {
            this.imgUrl='/api/img/'+imgId;
            this.dialogImg=true;
        },
        addActivity:function () {
            location.href='/activity/add';
        },
        editActivity:function (row) {
            location.href='/activity/edit/'+row.id;
        },
        statusChange:function (row,status) {
            var _this=this;
            if(row.is_active==1 && status==2){
                _this.$confirm('当前提领正在进行，确定要停用吗?停用之后将无法进行提领！', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(()=>{
                    _this.changeStatus(row,status);
                });
            }else {
                _this.changeStatus(row,status);
            }
        },
        changeStatus(row,status){
            var _this=this;
            axios.put('/api/activity/status/'+row.id,{status:status}).then(function (response) {
                var res=response.data;
                if(res.code!=0){
                    _this.$message.error(res.msg);
                    return false;
                }
                _this.$message.success('提交成功');
                //row.status=status;
                _this.getData();

            }).catch(function (error) {
                console.log(error);
                _this.$message.error('服务器出错，请联系管理员');
            });
        },
        deleteActivity:function (row) {
            var _this=this;
            _this.$confirm('确定要删除吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                axios.delete('/api/activity/'+row.id,{}).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        _this.$message.error(res.msg);
                        return false;
                    }
                    _this.$message.success('删除成功');
                    _this.getData();

                }).catch(function (error) {
                    console.log(error);
                    _this.$message.error('服务器出错，请联系管理员');
                });
            });
        }
    }
});