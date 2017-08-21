/**
 * Created by hongxun.wang on 2016/11/25.
 */
$(function(){
    /*获取用户列表*/
    mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: "/Home/Integral/integralListAjax", //表格列表数据
        ajaxdata: {},
        scrollX: true, //是否显示横向滚动条
        tableOpts: {
            data: {//初始化表格的时候，指定列的排序规则,不需要显示的列定义visible： false "aaSorting": asc | desc,orderable: true,"aaSorting": "desc"
                "tel": { title: "手机号" },
                "usable_integral": { title: "可用积分",orderable: true,"aaSorting": "desc"},
                "total_integral": { title: "积分总数",orderable: true,"aaSorting": "desc"},
                "occur_time": { title: "更新时间" }
            },
            operate: {
                "title": '积分日志', //自定义操作列
                render: function(data, type, row, meta) {
                    var str = '';
                    str += '<span class="btn-group">' +
                        '<a href="integralInfo?id='+row.user_id+'&tel='+row.tel+'" checkID="'+row.user_id+'" class="btn btn-xs btn-info">查看</a href="userInfo"> ' +
                        '</span>';
                    return str;
                }
            }
        }
    });

    /*点击搜索*/
    var status = false;
    $('#searchBtn').bind('click', function(){
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