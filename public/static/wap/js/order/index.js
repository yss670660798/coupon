

new Vue({
    el: '#app',
    created:function () {
        this.getOrder(0,'');
    },
    data:function () {
        return {
            active:0,
            page:1,
            loading:true,
            listLoading:false,
            finished:false,
            tabs:['全部','已确认','已发货','已完成'],
            status:['all','1','2','3'],
            orderData:[]
        }
    },
    methods: {
        tabChange:function (index, title) {
            this.orderData=[];
            this.loading=true;
            this.finished=false;
            this.page=1;
            this.getOrder(index);
        },
        getOrder: function(status) {
            var _this=this;
            axios.get('/api/my/order',{params:{status:_this.status[status],page:_this.page}}).then(function (response) {
                _this.loading=false;
                _this.listLoading=false;
                var res=response.data;
                if(res.code!=0){
                    _this.$toast(res.msg);
                    return false;
                }
                for(var i=0;i<res.data.length;i++){
                    _this.orderData.push(res.data[i]);
                }
                if(res._count<=_this.orderData.length){
                    _this.finished=true;
                }
            }).catch(function (error) {
                _this.$toast(error);
                console.log(error);
            });
        },
        nextPage:function () {
            this.listLoading=true;
            this.page+=1;
            this.getOrder(this.active);
        }
    }
});