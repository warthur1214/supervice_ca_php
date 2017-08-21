/**
 * Created by hongxun.wang on 2016/11/28.
 */
var mytable;
$(function () {
    /*获取表格列表*/
    mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: "/Home/Policy/policyListAjax", //表格列表数据
        ajaxdata: {},
        tableOpts: {
            data: {
                "car_id": { title: '<label for="currentPage"><input id="currentPage" type="checkbox">全选本页</label>',
                    render: function(data, type, row, meta) {
                        var _data = JSON.stringify(row);
                        var str = '';
                        return str += '<input class="checkbox" type="checkbox" car_id="'+data+'" name="checkData">';
                    }
                },
                "tel": { title: "手机号"},
                "v_code": { title: "车架号" },
                "bd_num": { title: "保单号" },
                "bd_date": { title: "申请日期" }
            },
            operate: {
                "title": '操作', //自定义操作列
                render: function(data, type, row, meta) {
                    if(row.is_acceapt=="0"){
                        var str = '';
                        str += '<span elID="'+row.car_id+'" class="btn btn-xs btn-success" onclick="successApply( this )">通过</span>'
                            + ' <span elID="'+row.car_id+'" onclick="refuseApply( this )" class="btn btn-xs btn-danger">拒绝</span> ';
                        return str;
                    }
                    if(row.is_acceapt=="1"){
                        var str = '<span elID="'+row.car_id+'" class="btn btn-xs btn-success" onclick="successApply( this )">通过</span>';
                        return str;
                    }
                    if(row.is_acceapt=="2"){
                        var str = ' <span elID="'+row.car_id+'" onclick="refuseApply( this )" class="btn btn-xs btn-danger">拒绝</span> ';
                        return str;
                    }
                }
            }
        }
    });
    /*重新加载表格*/
    function reloadTableData(obj) {
        var _data = $('#submit_form').serialize();
        data= decodeURIComponent(_data,true);
        var paramsData = conveterParamsToJson(data);
        var postData = $.extend({},paramsData,{"is_acceapt":"0"},obj);
        mytable.reloadByParam(postData);
        $("#currentPage").prop("checked",false);
        // $("#allPage").prop("checked",false);
    }
    var status = 0;
    /*点击搜索*/
    $('#searchBtn').bind('click', function(){
        $("#willCheck").parents().addClass("active").siblings().removeClass("active");
        reloadTableData({"is_acceapt":"0"});
        status = 0;
    });
    /*点击待审核*/
    $('#willCheck').bind('click', function(){
        reloadTableData({"is_acceapt":"0"});
        status = 0;
    });
    /*点击审核失败*/
    $('#checkFill').bind('click', function(){
        reloadTableData({"is_acceapt":"1"});
        status = 1;
    });
    /*点击审核成功*/
    $('#checkAccess').bind('click', function(){
        reloadTableData({"is_acceapt":"2"});
        status = 2;
    });
    /*点击导出本页数据*/
    $("#fileOutPage").on('click',function () {
        var str = '';
        /*if($("#allPage").is(':checked')){
            str = 'all';
        }
        else{*/
            var outPutArr = [];
            $("#list input:checked:not(#currentPage)").each(function () {
                outPutArr.push($(this).attr("car_id"));
            });
            str = outPutArr.join(",");
            if(str.length==0){
                Alert("请选择要导出的数据");
                return false;
            }
        /*}*/
        var _data = $('#submit_form').serialize();
        data= decodeURIComponent(_data,true);
        var urlStr = '?fileOut=1&arr='+str+'&is_acceapt='+status+'&'+data;
        var _href = "/Home/Policy/policyListAjax"+urlStr;
        $(this).attr('href', _href);
    })


    /*点击导出所有数据*/
    $("#fileOut").bind('click',function () {
        var str = '';
        var _data = $('#submit_form').serialize();
        data= decodeURIComponent(_data,true);
        var urlStr = '?fileOut=1&arr='+str+'&is_acceapt='+status+'&'+data;
        var _href = "/Home/Policy/policyListAjax"+urlStr;
        $(this).attr('href', _href);
    })
});
    /*通过申请*/
function successApply(el) {
    var _id = $(el).attr("elID");
    Confirm('确认通过该条申请吗？', function( flag ){
        if( flag ){
            AjaxJson('/Home/Policy/passPolicy/id/' + _id, function( res ){
                AlertHide( res.msg, function(){
                    if( res.status == '1' ){
                        mytable.refresh();
                    };
                });
            });
        };
    });
}
    /*拒绝申请*/
function refuseApply(el) {
    var _id = $(el).attr("elID");
    Confirm('确认拒绝该条申请吗？', function( flag ){
        if( flag ){
            AjaxJson('/Home/Policy/refusePolicy/id/' + _id, function( res ){
                AlertHide( res.msg, function(){
                    if( res.status == '1' ){
                        // reloadTableData({"is_acceapt":"-1"});
                        mytable.refresh();
                    };
                });
            });
        };
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

