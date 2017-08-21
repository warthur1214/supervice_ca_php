/**
 * Created by hongxun.wang on 2016/11/25.
 */
$(function(){
    var _id = location.href.split('id=')[1];
    var accountStr = '';
    var deviceStr = '';
    var carStr = '';
    var simStr = '';
    AjaxJson('/Home/User/userInfoAjax/id/' + _id, function( res ){
        $("#imgPic").attr("src",res.portrait);
        $("#userName").html(res.nickname);

        accountStr += '<li><a href="###">手机号 <span class="pull-right">'+res.tel+'</span></a></li>'
            +'<li><a href="#">是否有硬件 <span class="pull-right">'+res.is_bind+'</span></a></li>'
            +'<li><a href="#">注册日期 <span class="pull-right">'+res.user_create_time+'</span></a></li>'
            +'<li><a href="#">经销商id <span class="pull-right">'+res.sale_id+'</span></a></li>';


        deviceStr += '<li><a href="#">设备号 <span class="pull-right">'+res.device_no+'</span></a></li>'
            +'<li><a href="#">设备类型 <span class="pull-right">'+res.device_type_name+'</span></a></li>'
            +'<li><a href="#">激活状态 <span class="pull-right">'+res.active_status+'</span></a></li>'
            +'<li><a href="#">入库时间 <span class="pull-right">'+res.v_create_time+'</span></a></li>';



        carStr+='<li><a href="#">车系 <span class="pull-right">'+res.car_band+'</span></a></li>'
            +'<li><a href="#">车型 <span class="pull-right">'+res.car_serious+'</span></a></li>'
            +'<li><a href="#">车架号 <span class="pull-right">'+res.v_code+'</span></a></li>'
            +'<li><a href="#">车主姓名 <span class="pull-right">'+res.owner+'</span></a></li>'
            +'<li><a href="#">车牌号 <span class="pull-right">'+res.car_no+'</span></a></li>'
            +'<li><a href="#">发动机号 <span class="pull-right">'+res.e_code+'</span></a></li>'

        simStr+='<li><a href="#">IMSI标识码 <span class="pull-right">'+res.imsi+'</span></a></li>'
            +'<li><a href="#">sim卡状态 <span class="pull-right">'+res.bind_status+'</span></a></li>'
            +'<li><a href="#">激活时间 <span class="pull-right">'+res.active_time+'</span></a></li>'
            +'<li><a href="#">失效时间 <span class="pull-right">'+res.use_time+'</span></a></li>'
            +'<li><a href="#">套餐年限 <span class="pull-right">'+res.package_month+'</span></a></li>'
            +'<li><a href="#">总流量 <span class="pull-right">'+res.total_flow+'（GB）</span></a></li>';


        $("#accountMsg").html(accountStr);
        $("#deviceMsg").html(deviceStr);
        $("#carMsg").html(carStr);
        $("#simMsg").html(simStr);
    });
});