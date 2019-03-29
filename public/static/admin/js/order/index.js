


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
                date:'',
                goods_id:'',
                limit:10,
                status:'',
                page:1
            },
            formData:{
                id:'',
                shipper_code:'',
                logistic_code:'',
            },
            fileList:[],
            tableData:[],
            couponData:[],
            excelData:[],
            trackData:[],
            dialogShow:false,
            dialogDownload:false,
            dialogTrack:false,
            count:0,
            loading: true,
            trackLoading:false,
            formLabelWidth:'120px',
            fullScreenLoading:false,
            ll:null,
            dialogTitle:'绑定快递单号',
            requestUrl:'add',
            requestType:'post',
            rules: {
                shipper_code: [
                    { required: true, message: '请选择快递公司', trigger: 'change' }
                ],
                logistic_code: [
                    { required: true, message: '请填写快递单号', trigger: 'change' }
                ],
            },
            downloadPercentage:0
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
            _this.pageDisabled=true;

            axios.get('/api/order',{params:_this.param}).then(function (response) {
                var res=response.data;

                _this.tableData=res.data;
                _this.loading=false;

                _this.count=res._count;
            });
        },
        getTrack:function (row) {
            var _this=this;
            _this.trackData=[];
            _this.trackLoading=true;
            axios.get('/api/order/track/'+row.id,{params:{}}).then(function (response) {
                var res=response.data;
                if(res.code!=0){
                    _this.$message.error(res.msg);
                    _this.dialogTrack=false;
                    return false;
                }
                if(res.data.length<=0){
                    _this.dialogTrack=false;
                    _this.$message('暂无物流信息');
                    return false;
                }
                _this.trackData=res.data;
                _this.trackLoading=false;
            });
            _this.dialogTrack=true;
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
        //调整时间格式
        format1:function (row,column,cellValue,index) {
            return cellValue.substring(0,19);
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

            var url='/api/order/logistic';

            _this.$refs['express'].validate(function (valid) {
                if (valid) {
                    axios({method: 'put', url: url,data:_this.formData}).then(function (response) {
                        var res=response.data;
                        if(res.code!=0){
                            _this.$message.error(res.msg);
                            return false;
                        }
                        _this.dialogShow=false;
                        _this.$message.success('更新成功');
                        _this.search();

                    }).catch(function (error) {
                        _this.$message.error('服务器出错，请联系管理员');
                    });
                }
            })
        },
        setExpress:function (row) {
            this.formData.id=row.id;
            this.formData.shipper_code='';
            this.formData.logistic_code='';
            this.dialogShow=true;
        },
        download:function () {
            var _this=this;
            this.dialogDownload=true;
            this.downloadPercentage=0;
            var total=0; //记录总数
            var times=0; //需要请求次数

            axios.get('/api/order',{params:_this.param}).then(function (response) {
                var res=response.data;
                total=res._count;
                times=Math.ceil(total/1000);
                _this.excelData=[];
                _this.getExcelData(total,times);
            });
        },
        getExcelData:function (total,times) {
            var _this=this;
            var complete_count=0;

            for(var i=0;i<times;i++){
                var func = new Promise(function (resolve, reject){
                    var data=[];
                    axios.get('/api/order',{params:{
                            status:_this.param.status,
                            keyword:_this.param.keyword,
                            goods_id:_this.param.goods_id,
                            date:_this.param.date,
                            limit:1000,
                            page:i+1
                        },}).then(function (response) {
                        var res=response.data;
                        if(res.code==0){
                            for(var j=0;j<res.data.length;j++){
                                var item=[];
                                item.push(res.data[j].code);
                                item.push(res.data[j].coupon_card.coupon.name);
                                item.push(res.data[j].coupon_card.sn);
                                item.push(res.data[j].contact);
                                item.push(res.data[j].mobile);
                                item.push(res.data[j].address);
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

                                item.push(res.data[j].created_at);
                                item.push(res.data[j].logistic_code);
                                data.push(item);
                            }
                            complete_count++; //成功请求次数+1
                            _this.downloadPercentage = parseFloat((100 * complete_count / times).toFixed(2)); //设置当前进度百分比

                            resolve(data)
                        }else {
                            reject();
                        }
                    }).catch(function (error) {
                        console.log(error);
                        resolve([]);
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
                    ['订单编号','卡券名称','提货码','联系人','电话','地址','状态','下单时间','快递号'] //第一行标题
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