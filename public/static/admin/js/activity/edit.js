

var vm=new Vue({
    el:'#app',
    created:function () {

    },
    mounted(){
        this.id=common.getUrlPath(3);
        this.getInfo(this.id);
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
            is_active:false,
            id:0
        }
    },
    methods:{
        getInfo(id){
            var _this=this;

            axios.get('/api/activity/info/'+id,{params:{}}).then(function (response) {
                var res=response.data;
                if(res.code!=0){
                    _this.$message.error(res.msg);
                    return false;
                }
                _this.formData=Object.assign(_this.formData, res.data);
                _this.formData.date=[res.data.start_time,res.data.end_time];
                _this.is_active=res.data.is_active;
                _this.fileList1=[{name:res.data.notice.file_name,url:res.data.notice.file_url+'img/'+res.data.notice_img}];
                _this.fileList2=[{name:res.data.notice.file_name,url:res.data.notice.file_url+'img/'+res.data.title_img}];
                _this.fileList3=[{name:res.data.notice.file_name,url:res.data.notice.file_url+'img/'+res.data.end_img}];

            });
        },
        uploadClick:function (type) {
            this.uploadType=type;
        },
        uploadSuccess:function (res,file) {
            console.log(file);
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
                    axios.put('/api/activity/'+_this.id,_this.formData).then(function (response) {
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