

var vm=new Vue({
    el:'#app',
    created:function () {

    },
    data:function () {
        return {
            bg_loading:false,
            formData:{
                name:'',
                date:[],
                notice_img:0,
                title_img:0,
                end_img:0
            },
            uploadType:'',
            uploadUrl:'/api/upload',
            fileList1:[],
            fileList2:[],
            fileList3:[],
            saveLoading:false,
            is_active:false
        }
    },
    methods:{
        uploadClick:function (type) {
            this.uploadType=type;
        },
        uploadSuccess:function (res,file) {
            if(res.code!=0){
                this.$message.error(res.msg);
                return false;
            }
            this.formData[this.uploadType]=res.data.id;
            this.$message.success('上传成功');
        },
        uploadError:function (error,file) {
            console.log(error);
            this.$message.error('上传失败');
        },
        uploadExceed(files, fileList){
            this.$message.info('请先删除图片');
        },
        save:function () {
            var _this=this;
            _this.$refs['activity'].validate(function (valid) {
                if (valid) {
                    _this.saveLoading=true;
                    axios.post('/api/activity',_this.formData).then(function (response) {
                        _this.saveLoading=false;
                        var res=response.data;
                        if(res.code!=0){
                            _this.$message.error(res.msg);
                            return false;
                        }
                        _this.$message.success('提交成功');
                        location.href='/activity';

                    }).catch(function (error) {
                        console.log(error);
                        _this.$message.error('服务器出错，请联系管理员');
                    });
                }
            });
        },
        cancel:function () {
            //location.href='/activity';
            window.history.go(-1);
        }
    }
});