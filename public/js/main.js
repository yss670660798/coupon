var vs=new Vue({
    el:'#main',
    data:function () {
        var _this=this;
        var validatePass = (rule, value, callback) => {
            if (value === '') {
                callback(new Error('请输入密码'));
            } else {
                if (_this.form.okPwd !== '') {
                    _this.$refs.formK.validateField('okPwd');
                }
                callback();
            }
        };
        var validatePass2 = (rule, value, callback) => {
            if (value === '') {
                callback(new Error('请再次输入密码'));
            } else if (value !== _this.form.newPwd) {
                callback(new Error('两次输入密码不一致!'));
            } else {
                callback();
            }
        };
        return{
            formLabelWidth:'70px',
            dialogFormVisible:false,
            form:{
                oldPwd:null,
                newPwd:null,
                okPwd:null
            },
            rulesPwd: {
                oldPwd: [
                    { required: true,message:'密码不可为空', trigger: ['blur','change'] }
                ],
                newPwd: [
                    { validator: validatePass, trigger: ['blur','change'] }
                ],
                okPwd: [
                    { validator: validatePass2, trigger: ['blur','change'] }
                ],
            },
            userLabelWidth:'70px',
            dialogUserVisible:false,
            user:{
                brand_id:null,
                brand_name:null,
                store_id:null,
                store_name:null,
                name:null,
                username:null,
                role_id:null,
                role_name:null,
                email:null,
                tel:null,
                status:null,
                login_at:null,
                login_ip:null,
                created_at:null
            }
        };
    },
    methods:{
        handleOpen:function() {
            var _this=this;
            // _this.$message('修改密码');
            _this.form.oldPwd='';
            _this.form.newPwd='';
            _this.form.okPwd='';
            _this.dialogFormVisible=true;

            if (_this.$refs['formK']!==undefined) {
                _this.$refs['formK'].resetFields();
            }
        },
        resetPwd:function () {
            var _this=this;
            _this.$refs['formK'].validate(function (valid) {
                if (valid) {
                    axios.put('/api/home/password', _this.form).then(function (response) {
                        var res=response.data;
                        if(res.code!=0){
                            _this.$message.error(res.msg);
                            return false;
                        }
                        _this.dialogFormVisible=false;
                        _this.$message.success( '修改成功');

                        window.location.href='/api/logout';
                    }).catch(function (error) {
                        _this.$message.error('服务器出错，请联系管理员');
                    });
                }
            });
        },
        closeForm:function() {
            var _this=this;
            _this.$refs['formK'].resetFields();
            _this.dialogFormVisible=false;
        },
        handleUser:function () {
            var _this=this;
            axios.get('/api/home/user').then(function (res) {
                if(res.data.code==0){
                    Object.assign(_this.user,res.data.data)
                    _this.dialogUserVisible=true;
                    return;
                }
                _this.$message.error(res.data.msg);
            }).catch(function (error) {
                _this.$message.error(error);
            })
        }
    }
});

