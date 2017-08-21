/**
 * Created by hongxun.wang on 2016/11/25.
 */

//监听车系改变
$('#car_band').change(function() {
    var proID = $(this).children('option:selected').val();
    if(proID){
        fillCity(proID);
    }
    else{
        var sortStr = '<option value="">全部</option>';
        $("#city_id").html(sortStr);
    }
});

//车型
function fillCity(prov){
    $.ajax({
        url:'/Home/user/getCarName/id/'+prov,
        dataType:'json',
        type:'post',
        data:'',
        success:function (res) {
            var sortStr = '';
            sortStr += '<option value="">全部</option>';
            for(var i=0,len=res.length;i<len;i++){
                sortStr +=	'<option value="'+res[i]['car_series_id']+'">'+res[i]['car_series']+'</option>';
            }
            $("#city_id").html(sortStr);
        }
    });
}
$(function(){

    InitAutoComplete({
        $el: $('#distributorID'),
        url: '/Home/User/serviceId',
        text: 'service_provider_code',
        val: 'service_provider_code'
    });


    /*获取用户列表*/
    mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: "/Home/User/userListAjax", //表格列表数据
        ajaxdata: {},
        scrollX: true, //是否显示横向滚动条
        tableOpts: {
            data: {//初始化表格的时候，指定列的排序规则,不需要显示的列定义visible： false "aaSorting": asc | desc,orderable: true,"aaSorting": "desc"
                "tel": { title: "手机号"},
                "car_band": { title: "车系"},
                "v_code": { title: "车架号"},
                "is_device": { title: "是否有硬件" },
                "sale_id": { title: "经销商ID" },
                "create_time": { title: "注册日期" }
            },
            operate: {
                "title": '详细信息', //自定义操作列
                render: function(data, type, row, meta) {
                    var str = '';
                    str += '<span class="btn-group">' +
                        '<a href="userInfo?id='+row.user_id+'" checkID="'+row.user_id+'" class="btn btn-xs btn-info btn-primary">查看</a> ' +
                        '</span>';
                    return str;
                }
            }
        }
    });

    /*点击搜索*/
    var status = false;
    $('#searchBtn').bind('click', function(){
        $('input[type="checkbox"]').each(function () {
            $(this).prev().val('');
        });
        $('input[type="checkbox"]:checked').each(function () {
            $(this).prev().val('1');
        });

        status = true;
        var _data = $('#submit_form').serialize();
        data= decodeURIComponent(_data,true);
        var paramsData = conveterParamsToJson(data);
        mytable.reloadByParam(paramsData);
    });
});

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