<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>松林卡券系统--登录</title>
    <meta name="description" content="这是一个 index 页面">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />
    <link rel="stylesheet" href="/css/lib/amazeui.min.css" />
    <link rel="stylesheet" href="/css/lib/admin.css">
    <link rel="stylesheet" href="/css/lib/app.css">
    <link rel="stylesheet" href="/css/lib/elementui.css">
</head>

<body data-type="login">

<div class="am-g myapp-login">
    <div class="myapp-login-logo-block  tpl-login-max">
        <div class="myapp-login-logo-text">
            <div class="myapp-login-logo-text">
                松林卡券系统
            </div>
        </div>
        <div class="am-u-sm-10 login-am-center">
            <div id="app">
                <form class="am-form">
                    <fieldset>
                        <div class="am-form-group">
                            <input type="text" class="" id="username" v-model="param.username" placeholder="用户名">
                        </div>
                        <div class="am-form-group">
                            <input type="password" class="" id="password" v-model="param.password" placeholder="密码">
                        </div>
                        <p><button type="button" class="am-btn am-btn-default" @click="login">登录</button></p>
                    </fieldset>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="/js/lib/jquery.min.js"></script>
<script src="/js/lib/amazeui.min.js"></script>
<script src="/js/lib/app.js"></script>
<script src="/js/lib/axios.min.js"></script>
<script src="/js/lib/vue.js"></script>
<script src="/js/lib/elementui.js"></script>
<script type="text/javascript">
    document.onkeyup=function(event){
        if(event.keyCode == 13){
            vm.login();
        }
    }

    var vm=new Vue({
        el: '#app',
        data: function() {
            return {
                param:{
                    username:'',
                    password:'',
                },
            }
        },
        methods:{
            login:function () {
                var self=this;

                var url='/api/brand/sku'+self.requestUrl;


                axios({
                    method: 'post',
                    url: '/api/login',
                    data:self.param
                }).then(function (response) {
                    var res=response.data;
                    if(res.code!=0){
                        self.$message.error('用户名或密码错误');
                        return false;
                    }
                    window.location.href=res.url;
                }).catch(function (error) {
                    self.$message.error('服务器出错，请联系管理员');
                    console.log(error);
                });



            },

        },

    });
</script>
</body>

</html>