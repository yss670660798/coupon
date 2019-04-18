<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>松林卡券系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    {{--<link rel="icon" type="image/png" href="assets/i/favicon.png">--}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">

    <link rel="stylesheet" href="/css/lib/app.css">
    <link rel="stylesheet" href="/css/lib/elementui.css">
    @yield('style')
    <style type="text/css">
        html,body{
            margin: 0;width:100%;height:100%;background: url(/img/timg.jpg) no-repeat;background-size:100% 100%;
        }
        .header{
            background-color: #424F63;
            display: inline;
            /*border-bottom: solid 1px #424F63;*/
            height: 74px;
        }
        .main{
            /*height: calc(100vh - 65px);*/
            position: absolute;
            top: 65px;
            left: 10px;
            bottom: 10px;
            right: 10px;
            overflow-y: scroll;
            padding: 5px;
        }

        .main::-webkit-scrollbar{
            display: none;
        }

        .menu{
            float: left;
            width: 70%;
            height: 50px;
        }
        .el-menu-item a{
            text-decoration:none;
            color: inherit;
        }
        .el-dropdown-menu__item{
            padding: 0;
        }
        .el-dropdown-menu__item a{
            text-decoration:none;
            color: inherit;
            width: 96px;
            padding: 0 20px;
        }
        .logo{
            height: 60px;
            width: 160px;
            float: left;
            line-height: 50px;
        }

        .logo img{
            width: 100%;
            vertical-align: middle;
        }
        .el-menu--horizontal{
            border: none;
            height: 55px;
        }

        .bg-loading{
            position: fixed;
            left:0;
            top:0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 1);
            z-index:99999;
        }
        .bg-loading span{
            display: block;
            position: fixed;
            top:50%;
            left: 50%;
            transform: translate(-50%,-50%);
            font-size:3em;
            color: #c5c8c7;
        }

    </style>
</head>
    <body>
    <div id="app">
        <div class="bg-loading" v-show="bg_loading">
            <span class="el-icon-loading"></span>
        </div>
        <el-container>
        <el-header class="header">
                <div class="logo">
                    <img src="/img/logo2.png" alt="">
                </div>
                <div class="menu">
                    <el-menu default-active="{{$sub_active}}" mode="horizontal" background-color="#424F63" text-color="#fff" active-text-color="#ffd04b">
                        @foreach($menu as $val)
                            @if(empty($val['child']))
                                <el-menu-item index="{{$val['byname']}}">
                                    <a href="{{$val['url']}}">
                                        <div style="height: 60px;text-align: center;">
                                            {{$val['name']}}
                                        </div>
                                    </a>
                                </el-menu-item>
                            @else
                                <el-submenu index="{{$val['byname']}}">
                                    <template slot="title">{{$val['name']}}</template>
                                    @foreach($val['child'] as $child)
                                        <el-menu-item index="{{$child['byname']}}">
                                            <a href="{{$child['url']}}">{{$child['name']}}</a>
                                        </el-menu-item>
                                    @endforeach
                                </el-submenu>
                            @endif
                        @endforeach
                    </el-menu>
                </div>
                <div style="float: right;height: 60px;line-height: 60px;cursor: pointer;" >
                    {{--trigger="click"--}}
                    <el-dropdown>
                        <span class="el-dropdown-link" style="color: white;">
                            {{$user['name']}}
                            <i class="el-icon-caret-bottom el-icon--right"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item>
                                <a href="/logout">基本资料</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="/logout">修改密码</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="/logout">退出</a>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </el-header>
            <el-main class="main">
                @yield('content')
            </el-main>
        </el-container>
    </div>
    </body>
    <script src="/js/lib/vue.js"></script>
    <script src="/js/lib/elementui.js"></script>
    <script src="/js/lib/axios.min.js"></script>
    {{--<script src="/js/main.js"></script>--}}

    <script type="text/javascript">
        var common={
            getQueryString:function (name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var reg_rewrite = new RegExp("(^|/)" + name + "/([^/]*)(/|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                var q = window.location.pathname.substr(1).match(reg_rewrite);
                if(r != null){
                    return unescape(r[2]);
                }else if(q != null){
                    return unescape(q[2]);
                }else{
                    return null;
                }
            },
            parseParam:function(param, key){
                var paramStr="";
                if(param instanceof String||param instanceof Number||param instanceof Boolean){
                    paramStr+="&"+key+"="+encodeURIComponent(param);
                }else{
                    $.each(param,function(i,v){
                        var val=v==undefined?'':v;
                        paramStr+='&'+i+'='+val;
                    });
                }
                return paramStr.substr(1);
            },
            parseQueryString: function (url) {
                var reg_url = /^[^\?]+\?([\w\W]+)$/,
                    reg_para = /([^&=]+)=([\w\W]*?)(&|$|#)/g,
                    arr_url = reg_url.exec(url),
                    ret = {};
                if (arr_url && arr_url[1]) {
                    var str_para = arr_url[1], result;
                    while ((result = reg_para.exec(str_para)) != null) {
                        ret[result[1]] = decodeURI(result[2]);
                    }
                }
                return ret;
            },
            getUrlPath : function(key, def) {
                var path = window.location.pathname;
                if(path) {
                    var arr = path.split('/');
                    if(arr[key]) {
                        return arr[key];
                    } else {
                        return '';
                    }
                } else {
                    return def || '';
                }
            },

        };
    </script>
@yield('javascript')
</html>
