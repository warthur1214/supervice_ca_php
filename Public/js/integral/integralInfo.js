/**
 * Created by hongxun.wang on 2016/11/25.
 */
$(function(){
    var _id = location.href.split('id=')[1];
    var tel = location.href.split('tel=')[1];
    
    $("#userTel").html(' 积分日志('+tel+')');
    /*获取用户列表*/
    mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: '/Home/Integral/integralInfoAjax/id/' + _id, //表格列表数据
        ajaxdata: {},
        scrollX: true, //是否显示横向滚动条
        tableOpts: {
            data: {
                "comment": { title: "获取来源"},
                "integral": { title: "积分日志"},
                "total_integral": { title: "积分总数"},
                "create_time": { title: "积分变更时间" }
            }
        }
    });
});