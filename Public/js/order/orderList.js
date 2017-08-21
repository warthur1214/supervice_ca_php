/**
 * Created by hongxun.wang on 2017/01/16.
 */
var mytable;
var dataObj = {};
$(function () {
    /*联通套餐包*/
    AjaxJson('/Home/Set/unicomSet', function( res ){
        // console.log(res);
        var unicomArr = res.data;
        var optionStr = '<option value="">不限</option>';
        for(var i=0 ,len=unicomArr.length;i<len;i++){
            optionStr += '<option value="'+unicomArr[i].id+'">'+unicomArr[i].unicom_set+'</option>'
        }
        $("#unicomSet").html(optionStr);
    });
    /*获取表格列表*/

    mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: "/Home/Order/orderListAjax", //表格列表数据
        ajaxdata: {},
        tableOpts: {
            data: {
                "car_id": { title: '<label for="currentPage"><input id="currentPage" type="checkbox">全选本页</label>',
                    render: function(data, type, row, meta) {
                        var _data = JSON.stringify(row);
                        var str = '';
                        // console.log(data);
                        return str += '<input elID="'+row.order_no+'" class="checkbox" type="checkbox" car_id="'+data+'" name="checkData">';
                    }
                },
                "tel": { title: "手机号"},
                "order_no": { title: "订单编号" },
                "sim_iccid": { title: "ICCID" },
                "unicom_set": { title: "对应联通套餐" },
                "integral_cost": { title: "消耗积分" },
                "money": { title: "订单金额(元)" },/*,
                "status_time": { title: "订单创建时间" }*/
                "status_time":{
                    title:'订单状态时间'
                }
            },

            operate: {
                "title": '操作', //自定义操作列
                render: function(data, type, row, meta) {
                    // console.log(row);
                    var _id = row.id;
                    dataObj[_id] = row.order_status;
                    if(row.status==1){
                        var str = '';

                        str += '<span elID="'+row.order_no+'" class="btn btn-xs btn-success" onclick="successApply( this )">处理</span>'
                            + ' <span elID="'+row.id+'" onclick="checkMsg( this )" class="btn btn-xs btn-info btn-primary">查看</span> ';
                        return str;
                    }
                    else{
                        var str = '';
                        str += ' <span elID="'+row.id+'" onclick="checkMsg( this )" class="btn btn-xs btn-info btn-primary">查看</span> ';
                        return str;
                    }

                }
            }
        }
    });
    /*重新加载表格*/
    function reloadTableData(obj) {
        if($("#active_time").val()){
            $("#hiddenData").val($("#active_time").val()+' - '+$("#active_finish_time").val());
        }
        var _data = $('#submit_form').serialize();
        data= (decodeURIComponent(_data,true)).replace(/\+/g," ");
        var paramsData = conveterParamsToJson(data);
        var postData = $.extend({},paramsData,{"status":"1"},obj);
        mytable.reloadByParam(postData);
        $("#currentPage").prop("checked",false);

    }
    var status = 1;
    /*点击搜索*/
    $('#searchBtn').bind('click', function(){
        if(status==1){
            $("#willCheck").parents().addClass("active").siblings().removeClass("active");
            reloadTableData({"status":"1"});
        }
        if(status==0){
            $("#checkFill").parents().addClass("active").siblings().removeClass("active");
            reloadTableData({"status":"0"});
        }
        if(status==2){
            $("#checkAccess").parents().addClass("active").siblings().removeClass("active");
            reloadTableData({"status":"2"});
        }
        if(status==3){
            $("#finish").parents().addClass("active").siblings().removeClass("active");
            reloadTableData({"status":"3"});
        }


    });
    /*点击待处理*/
    $('#willCheck').bind('click', function(){
        $("#deal").removeClass("hide");
        reloadTableData({"status":"1"});
        status = 1;
    });
    /*点击待支付*/
    $('#checkFill').bind('click', function(){
        $("#deal").addClass("hide");
        reloadTableData({"status":"0"});
        status = 0;
    });
    /*点击已取消*/
    $('#checkAccess').bind('click', function(){
        $("#deal").addClass("hide");
        reloadTableData({"status":"2"});
        status = 2;
    });
    /*点击已完成*/
    $('#finish').bind('click', function(){
        $("#deal").addClass("hide");
        reloadTableData({"status":"3"});
        status = 3;
    });
    /*点击批量处理*/
    $("#deal").on('click',function () {
        var str = '';
        var outPutArr = [];
        $("#list input:checked:not(#currentPage)").each(function () {
            outPutArr.push($(this).attr("elID"));
        });
        str = outPutArr.join(",");
        if(str.length==0){
            Alert("请选择要处理的数据");
            return false;
        }
        Confirm('确认要批量处理？', function( flag ){
            if( flag ){
                AjaxJson('/Home/Order/orderHandle/status/more/id/' + str, function( res ){
                    AlertHide( res.msg, function(){
                        if( res.status == '1' ){
                            mytable.refresh();
                        };
                    });
                });
            };
        });
    });
    /*点击导出本页数据*/
    $("#fileOutPage").on('click',function () {
        var str = '';

        var outPutArr = [];
        $("#list input:checked:not(#currentPage)").each(function () {
            outPutArr.push($(this).attr("elID"));
        });
        str = outPutArr.join(",");
        if(str.length==0){
            Alert("请选择要导出的数据");
            return false;
        }

        var _data = $('#submit_form').serialize();
        data= (decodeURIComponent(_data,true)).replace(/\+/g," ");
        var urlStr = '?fileOut=1&arr='+str+'&status='+status+'&'+data;
        var _href = "/Home/Order/orderListAjax"+urlStr;
        $(this).attr('href', _href);
    });


    /*点击导出所有数据*/
    $("#fileOut").bind('click',function () {
        var str = 'all';
        var _data = $('#submit_form').serialize();
        data= (decodeURIComponent(_data,true)).replace(/\+/g," ");
        // data= _data;
        var urlStr = '?fileOut=1&arr='+str+'&status='+status+'&'+data;
        var _href = "/Home/Order/orderListAjax"+urlStr;
        $(this).attr('href', _href);
    })
});
/*通过申请*/
function successApply(el) {
    var _id = $(el).attr("elID");
    Confirm('确认处理该订单？', function( flag ){
        if( flag ){
            AjaxJson('/Home/Order/orderHandle/id/' + _id, function( res ){
                AlertHide( res.msg, function(){
                    if( res.status == '1' ){
                        mytable.refresh();
                    };
                });
            });
        };
    });
}
/*查看信息*/
function checkMsg(el) {
    var _id = $(el).attr("elID");
    var msg = dataObj[_id];
    var content = '<div style="width:300px;margin:0 auto;margin-top:10px;">';
    for(var i=0,len=msg.length;i<len;i++){
        content += '<p><strong style="display:inline-block;width: 110px;">'+msg[i].status_type+'</strong>： <strong>'+msg[i].status_time+'</strong></p>';

    }
    content += '</div>'

    layer.open({
        type: 1,
        title: false,
        shadeClose: false,
        shade: 0.2,
        area: ['350px', '130px'],
        content: content
    });


}
//表单序列化后转成对象
function conveterParamsToJson(paramsAndValues) {
    var jsonObj = {};
    var param = paramsAndValues.split("&");
    for ( var i = 0; param != null && i < param.length; i++) {
        var para = param[i].split("=");
        jsonObj[para[0]] = para[1];
    }
    return jsonObj;
}
/*将日期对象输出成指定格式的字符串*/
function DateFormat( sdate, format) {
    var format;
    var date = {
        "M+": sdate.getMonth() + 1,
        "d+": sdate.getDate(),
        "h+": sdate.getHours()-8,
        "m+": sdate.getMinutes(),
        "s+": sdate.getSeconds(),
        "q+": Math.floor((sdate.getMonth() + 3) / 3),
        "S+": sdate.getMilliseconds()
    };
    if (/(y+)/i.test(format)) {
        format = format.replace(RegExp.$1, (sdate.getFullYear() + '').substr(4 - RegExp.$1.length));
    };
    for (var k in date) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
        };
    };
    return format;
};
