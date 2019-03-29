


var vm=new Vue({
    el: '#app',
    created:function () {
        var self=this;

        self.search();
    },
    data: function() {
        return {
            bg_loading:false,
            param:{
                keyword:'',
                date:'',
                limit:10,
                page:1
            },
            tableData:[],
            count:0,
            loading: true,
            pageDisabled:true,
            fullScreenLoading:false,
            ll:null
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

            axios.get('/api/admin/sys/log',{params:self.param}).then(function (value) {

                var data=value.data.data;

                self.tableData=data;
                self.loading=false;
                self.count=value.data._count;
                self.pageDisabled=false;
            }).catch(function (err) {

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
        //删除产品
        deleteLog:function (row) {
            var self=this;

            self.$confirm('确定要删除吗?','提示',{type: 'warning'}).then(function (value) {
                axios.delete('/api/admin/sys/log/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        self.$message.error(res.msg);
                        return false;
                    }
                    self.$message.success('删除成功');
                    self.search2();
                }).catch(function (err) {
                    
                });
            }).catch(function (c) {
                
            });

        }
    },

});