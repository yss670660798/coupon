<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>训练平台登录</title>
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
</head>

<body data-type="login">

<div class="am-g myapp-login">
    <div class="myapp-login-logo-block  tpl-login-max">
        <div class="myapp-login-logo-text">
            <div class="myapp-login-logo-text">
                训练平台<span> 登录</span> <i class="am-icon-skyatlas"></i>
            </div>
        </div>
        <div class="am-u-sm-10 login-am-center">
            <form class="am-form">
                <fieldset>
                    <div class="am-form-group">
                        <input type="text" class="" id="username" placeholder="用户名">
                    </div>
                    <div class="am-form-group">
                        <input type="password" class="" id="password" placeholder="密码">
                    </div>
                    <p><button type="button" class="am-btn am-btn-default">登录</button></p>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<script src="/js/lib/jquery.min.js"></script>
<script src="/js/lib/amazeui.min.js"></script>
<script src="/js/lib/app.js"></script>
<script src="/js/lib/axios.min.js"></script>
<script type="text/javascript">
    $('.am-btn').click(function () {

        axios({
            method: 'post',
            url: '/api/login',
            data:{username:$('#username').val(),password:$('#password').val()}
        }).then(function (response) {
            var res=response.data;
            if(res.code!=0){
                return false;
            }
            window.location.href=res.url;

        }).catch(function (error) {

            console.log(error);
        });
    });
</script>
</body>

</html>