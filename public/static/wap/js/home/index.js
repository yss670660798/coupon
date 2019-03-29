



new Vue({
    el: '#app',
    created:function () {
        this.getRegion();
    },
    data:function () {
        return {
            param:{
                card_no:'',
                password:'',
                v_code:''
            },
            selectAddress:false,
            newAddress:false,
            chosenAddressId:'',
            addressList: [
                {
                    id: '1',
                    name: '张三',
                    tel: '13000000000',
                    address: '浙江省杭州市西湖区文三路 138 号东方通信大厦 7 楼 501 室'
                },
                {
                    id: '2',
                    name: '李四',
                    tel: '1310000000',
                    address: '浙江省杭州市拱墅区莫干山路 50 号'
                }
            ],
            regionList:{}
        }
    },
    methods: {
        checkCard:function () {
            this.selectAddress=true;
        },
        hideAddrList:function () {
            this.selectAddress=false;
        },
        addAddress:function () {
            this.newAddress=true;
        },
        hideNewAddr:function () {
            this.newAddress=false;
        },
        changeAddress:function (item,index) {
            //console.log(item,index);
            var _this=this;
            _this.$dialog.confirm({
                message: '确定选择这个地址吗'
            }).then(function() {
                // on confirm
            }).catch(function(){
                _this.chosenAddressId='';
            });
        },
        getRegion:function(){
            var _this=this;
            axios.get('/api/region/list',{params:{}}).then(function (response) {
                var res=response.data;
                if(res.code==0){
                    _this.regionList=res.data;
                }
            }).catch(function (error) {
                console.log(error);
            });
        }
    }
});