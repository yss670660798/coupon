

new Vue({
    el: '#app',
    created:function () {
        this.onRefresh();
    },
    data:function () {
        return {
            isLoading:true,
            loading:true,
            count:0,
            transData:[]
        }
    },
    methods: {
        handleClick: function() {
            var _this=this;
            //this.$toast('Hello world!');
            _this.$indicator.open();

            setTimeout(function () {
                _this.$indicator.close();
            },5000);
        },
        onRefresh:function () {
            //this.isLoading=true;
            this.getTrans();
        },
        getTrans:function () {
            var _this=this;
            axios.get('/api/my/order/'+orderId,{params:{}}).then(function (response) {
                _this.loading=false;
                _this.listLoading=false;
                var res=response.data;
                if(res.code!=0){
                    _this.$toast(res.msg);
                    return false;
                }

                _this.transData=res.data;
                _this.isLoading=false;
                _this.loading=false;

            }).catch(function (error) {
                _this.$toast(error);
                console.log(error);
            });
        }
    }
});