<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@section('title')标题@show</title>
    <link rel="stylesheet" type="text/css" href="/static/wap/css/index.css">
    <style type="text/css">
        html,body{
            background-color: gainsboro;
        }
    </style>
    @yield('link')

</head>
<body ontouchstart >
<div id="app">
    @yield('content')
</div>
@yield('footer')
</body>
</html>
@yield('template')
<script type="text/javascript" src="/js/lib/vue.js"></script>
<script type="text/javascript" src="/static/wap/js/lib/vant.min.js"></script>
<script type="text/javascript" src="/js/lib/jquery.min.js"></script>
<script type="text/javascript" src="/js/lib/axios.min.js"></script>
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