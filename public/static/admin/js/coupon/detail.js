


var vm=new Vue({
    el: '#app',
    created:function () {
        this.search();
    },
    data: function() {
        return {
            bg_loading:false,
            param:{
                status:'',
                keyword:'',
                limit:10,
                page:1
            },
            formData:{
                expired_at:'',
                id:0
            },
            tableData:[],
            dialogShow:false,
            dialogDownload:false,
            count:0,
            loading: true,
            formLabelWidth:'100px',
            dialogTitle:'参数详情',
            id:0,
            downloadPercentage:0,
            excelData:[]
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

            var id=common.getUrlPath(3);
            axios.get('/api/coupon/detail/'+id,{params:_this.param}).then(function (response) {

                _this.loading=false;

                var res=response.data;
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
            this.param.page=1;
            this.search();
        },
        restart:function (row) {
            var _this=this;

            _this.formData.expired_at=row.expired_at;
            _this.formData.id=row.id;
            _this.dialogShow=true;
        },
        stopCoupon:function (row) {
            var _this=this;

            _this.$confirm('确定要作废吗?','提示',{
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function (value) {
                axios.put('/api/coupon/card/stop/'+row.id).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        _this.$message.error(res.msg);
                        return false;
                    }
                    _this.$message({
                        message: '作废成功',
                        type: 'success'
                    });
                    _this.search();
                });
            });
        },
        delCoupon:function (row) {
            var _this=this;

            _this.$confirm('确定要删除吗?','提示',{
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function (value) {
                axios.delete('/api/coupon/card/'+row.id).then(function (response) {
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
            });
        },
        download:function (row) {
            window.location.href='http://attach.fmcgbi.com:5015/'+row.attach.path
        },
        showParam:function (row) {
            this.paramsData=row.params;
            this.dialogShow=true;
        },
        save:function () {
            var _this=this;
            axios.put('/api/coupon/card/restart/'+_this.formData.id,_this.formData).then(function (response) {
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
        },
        download:function () {
            var _this=this;
            this.dialogDownload=true;
            this.downloadPercentage=0;
            var total=0; //记录总数
            var times=0; //需要请求次数

            var id=common.getUrlPath(3);
            axios.get('/api/coupon/detail/'+id,{params:_this.param}).then(function (response) {
                var res=response.data;
                total=res._count;
                times=Math.ceil(total/100);
                _this.excelData=[];
                _this.getExcelData(total,times);
            });
        },
        getExcelData:function (total,times) {
            var _this=this;
            var id=common.getUrlPath(3);
            var complete_count=0;
            _this.param.limit=100;
            for(var i=0;i<times;i++){
                var func = new Promise(function (resolve, reject){
                    var data=[];
                    axios.get('/api/coupon/detail/'+id,{params:{
                        status:_this.param.status,
                        keyword:_this.param.keyword,
                        limit:100,
                        page:i+1
                    },}).then(function (response) {
                        var res=response.data;
                        if(res.code==0){
                            for(var j=0;j<res.data.length;j++){
                                var item=[];
                                item.push(res.data[j].coupon.name);
                                item.push(res.data[j].sn);
                                switch (res.data[j].status){
                                    case 1:
                                        item.push('未使用');
                                        break;
                                    case 2:
                                        item.push('已使用');
                                        break;
                                    case 3:
                                        item.push('已作废');
                                        break;
                                    case 4:
                                        item.push('已过期');
                                        break;
                                }

                                item.push(res.data[j].expired_at);
                                data.push(item);
                            }
                            complete_count++; //成功请求次数+1
                            _this.downloadPercentage = parseFloat((100 * complete_count / times).toFixed(2)); //设置当前进度百分比

                            resolve(data)
                        }else {
                            reject();
                        }
                    });
                });
                _this.excelData.push(func)
            }
            _this.createExcel();
        },
        createExcel:function () {
            var _this=this;
            Promise.all(_this.excelData).then(function (values) { //使用Promise.all调用funcs里面的函数，并合并数据，最后给js-xlsx生成Excel
                var aoa = [
                    ['卡券名称','卡券号','状态','有效期'] //第一行标题
                ];
                //将数据合并
                for (var i = 0; i < values.length; i++) {
                    for (var j = 0; j < values[i].length; j++) {
                        aoa.push(values[i][j]);
                    }
                }
                var ws = XLSX.utils.aoa_to_sheet(aoa);
                var wb = XLSX.utils.book_new();
                wb.SheetNames.push('sheet1');
                wb.Sheets['sheet1'] = ws;
                var wopts = { bookType: 'xlsx', bookSST: false, type: 'array' };
                var wbout = XLSX.write(wb, wopts);
                /*var timestamp=new Date().getTime();*/
                saveAs(new Blob([wbout], { type: "application/octet-stream" }), "卡券明细" + new Date().getTime() + ".xlsx");
                _this.dialogDownload=false;
                _this.param.limit=10;
                //self.export_percentge = -1;
            });
        }
    },

});