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
    <link rel="stylesheet" href="/css/lib/amazeui.min.css" />
    <link rel="stylesheet" href="/css/lib/admin.css">
    <link rel="stylesheet" href="/css/lib/app.css">
    <link rel="stylesheet" href="/css/lib/elementui.css">
    @yield('style')
    <style type="text/css">
        html.body{
            background-color: #e9ecf3;
            overflow: inherit;
        }
        .tpl-page-header-fixed{
            margin-top: -15px;
        }
        .tpl-left-nav-title{
            height: 20px;
            padding: 5px;
        }
    </style>
</head>

    <body data-type="index">
        <header class="am-topbar am-topbar-inverse admin-header" style="">
            <div class="am-topbar-brand">
                <a href="javascript:;" class="tpl-logo">
                    <img src="/img/logo2.png" alt="">
                </a>
            </div>
            <div class="am-icon-list tpl-header-nav-hover-ico am-fl am-margin-right"></div>

            <div class="am-collapse am-topbar-collapse" id="topbar-collapse" >
                {{--<div style="border: 1px solid red;">--}}
                {{--@yield('nav')--}}
                {{--</div>--}}
                <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list tpl-header-list">
                    <li class="am-hide-sm-only"><a href="javascript:;" id="admin-fullscreen" class="tpl-header-list-link"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">开启全屏</span></a></li>
                    <li class="am-dropdown" data-am-dropdown data-am-dropdown-toggle>
                        <a class="am-dropdown-toggle tpl-header-list-link" href="javascript:;">
                            <span class="am-icon-user" style="font-size: 15px;"></span>&nbsp;<span class="tpl-header-list-user-nick">{{$user?$user['name']:'没有名字'}}</span>
                        </a>
                        <ul class="am-dropdown-content" id="main">
                            {{--<li><a href="#"><span class="am-icon-bell-o"></span> 资料</a></li>--}}
                            {{--<li><a href="#"><span class="am-icon-cog"></span> 设置</a></li>--}}
                            <li><a href="#" @click="handleUser"><span class="am-icon-user"></span> 个人信息</a></li>
                            <li><a href="#"><span class="am-icon-eye"></span> 系统权限</a></li>
                            <li><a href="http://www.fmcgbi.com/" target="_blank"><span class="am-icon-link"></span> 关于凯景</a></li>
                            <li><a href="http://www.sfims.com/" target="_blank"><span class="am-icon-link"></span> 凯景FIMS</a></li>
                            <li><a href="#" @click="handleOpen"><span class="am-icon-edit"></span> 修改密码</a></li>
                            <li><a href="/api/logout"><span class="am-icon-power-off"></span> 退出</a></li>
                            <!--修改密码-->
                            <el-dialog title="修改密码" :visible.sync="dialogFormVisible" width="30%"  :close-on-click-modal="false" :modal="false">
                                <el-form :model="form" :rules="rulesPwd" ref="formK" size="small">
                                    <el-form-item label="原密码" :label-width="formLabelWidth" prop="oldPwd">
                                        <el-input v-model="form.oldPwd" type="password" auto-complete="off"></el-input>
                                    </el-form-item>
                                    <el-form-item label="新密码" :label-width="formLabelWidth" prop="newPwd">
                                        <el-input v-model="form.newPwd" type="password" auto-complete="off"></el-input>
                                    </el-form-item>
                                    <el-form-item label="确认密码" :label-width="formLabelWidth" prop="okPwd">
                                        <el-input v-model="form.okPwd" type="password" auto-complete="off"></el-input>
                                    </el-form-item>
                                </el-form>
                                <div slot="footer" class="dialog-footer">
                                    <el-button @click="closeForm" size="small">取 消</el-button>
                                    <el-button type="primary" @click="resetPwd" size="small">确 定</el-button>
                                </div>
                            </el-dialog>

                            {{--个人信息--}}
                            <el-dialog title="个人信息" :visible.sync="dialogUserVisible" width="30%"   :close-on-click-modal="false" :modal="false">
                                <el-form :model="user"  ref="formUser" size="small" :disabled="true" :label-width="userLabelWidth" label-position="left">
                                    <el-form-item label="品牌" v-if="user.brand_name">
                                        <el-input v-model="user.brand_name" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="门店" v-if="user.store_name">
                                        <el-input v-model="user.store_name" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="名称" v-if="user.name">
                                        <el-input v-model="user.name" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="用户名" v-if="user.username">
                                        <el-input v-model="user.username" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="邮箱" v-if="user.email">
                                        <el-input v-model="user.email" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="电话" v-if="user.tel">
                                        <el-input v-model="user.tel" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="权限" v-if="user.role_name">
                                        <el-input v-model="user.role_name" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="状态" v-if="user.status">
                                        <el-input  :value="user.status==1?'启用':'冻结'" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="创建日期" v-if="user.created_at">
                                        <el-input v-model="user.created_at" ></el-input>
                                    </el-form-item>

                                </el-form>
                            </el-dialog>

                        </ul>
                    </li>
                </ul>
            </div>
        </header>

        <div class="tpl-page-container tpl-page-header-fixed">
            {{--导航--}}
            <div class="tpl-left-nav tpl-left-nav-hover">
                <div class="tpl-left-nav-title"></div>
                <div class="tpl-left-nav-list">
                    <ul class="tpl-left-nav-menu">
                        @foreach($menu as $key=>$val)
                            <li class="tpl-left-nav-item">
                                <a href="{{empty($val['url'])?'javascript:;':$val['url']}}" class="nav-link {{!empty($val['child'])?'tpl-left-nav-link-list':''}} {{$top_active==$key?'active':''}}">
                                    {{-- <i class="am-icon-table"></i>--}}
                                    <span>{{$val['name']}}</span>
                                    @if(!empty($val['child']))
                                        {{--<i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>--}}
                                        <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right {{$top_active==$key?'tpl-left-nav-more-ico-rotate':''}}"></i>
                                    @endif
                                </a>
                                @if(!empty($val['child']))
                                    <ul class="tpl-left-nav-sub-menu" style="display:{{$top_active==$key?'block':'none'}};" >
                                        @foreach($val['child'] as $subKey=>$item)
                                            <li>
                                                <a href="{{empty($item['url'])?'javascript:;':$item['url']}}" class="{{$sub_active==$subKey?'active':''}}">
                                                    <i class="am-icon-angle-right"></i>
                                                    <span>{{$item['name']}}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            {{--主体内容--}}
            <div class="tpl-content-wrapper">
                <ol class="am-breadcrumb">
                    {{--<li><a href="#" class="am-icon-home">首页</a></li>
                    <li><a href="#">分类</a></li>
                    <li class="am-active">内容</li>--}}

                </ol>
                <div id="app">
                    @yield('content')
                </div>
            </div>
        </div>

        <script src="/js/lib/jquery.min.js"></script>
        <script src="/js/lib/amazeui.min.js"></script>
        {{--<script src="/js/lib/iscroll.js"></script>--}}
        <script src="/js/lib/app.js"></script>
        <script src="/js/lib/vue.js"></script>
        <script src="/js/lib/elementui.js"></script>
        <script src="/js/lib/axios.min.js"></script>
        <script src="/js/main.js"></script>

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
    </body>

</html>
