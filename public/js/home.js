

var tp=new Vue({
    el:'#app',
    data:function () {
        return{
            bg_loading:true,
            topArray:{
                top1:0,
                top2:0,
                top3:0,
                top4:0
            },
            end:1000
        };
    },
    created:function(){
        var _this=this;

    },
    mounted:function(){
        var _this=this;
        _this.bg_loading=false;
    },
    methods:{
        getData:function () {
            var _this=this;
            axios.get('/api/home').then(function (res) {
                if(res.data.code==0){
                    Object.assign(_this.topArray,res.data.data);
                }
            }).catch(function (error) {});
        },
        getTop:function () {
            var _this=this;
            axios.get('/api/home/detail').then(function (res) {
                if(res.data.code==0){
                    Object.assign(_this.topArray,res.data.data);
                }
            }).catch(function (error) {});
        }
    }
});