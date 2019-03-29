var tp=new Vue({
    data:function () {
        return{
            bg_loading:true,
            topArray:{
                top1:0,
                top2:0,
                top3:0,
                top4:0
            }
        };
    },
    watch:{
        mouse:function (bool) {
            console.log(bool);
        }
    },
    methods:{

    }
}).$mount('#app');