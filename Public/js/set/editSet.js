$(function() {
    var _id = location.href.split('id/')[1];
    var formValidate = InitValidateForm($('#info_form'));
    var picUrl = InitUploader('pictureUrl', '/Home/MyUpload/index/path/Set', 1, {
        _imgWidth: 440,
        _imgHeight: 440
    });

    AjaxJson("/Home/Set/unicomSet", function(res) {
        var _data = res.data;
        if (_data) {
            var _html = '<option value="">请选择</option>';
            for (var i = 0, l = _data.length; i < l; i++) {
                var _d = _data[i];
                _html += '<option value="' + _d.id + '">' + _d.unicom_set + '</option>';
            };
            $('[name="unicom_set_id"]').html(_html);
        };

        AjaxJson("/Home/Set/getInfo/id/" + _id, function( data ) {
            formValidate.assignForm( data ); 
            if( data.limited_time_discount == "1"){
                $('#limitedCheck').iCheck('check');
            };  
            picUrl.setImages( data.img_url );
        });
    });

    

    $('#beginTime').datetimepicker({
        language: 'zh-CN',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });
    $('#endTime').datetimepicker({
        language: 'zh-CN',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });


    $('#beginTime').datetimepicker().on('changeDate', function(ev) {
        var _sdate = DateFormat( ev.date, 'yyyy-MM-dd hh:mm'); 
        $('#endTime').datetimepicker('setStartDate', _sdate ); 
    });
    $('#endTime').datetimepicker().on('changeDate', function(ev) {
        var _edate = DateFormat( ev.date, 'yyyy-MM-dd hh:mm'); 
        $('#endTime').datetimepicker('setEndDate', _edate ); 
    }); 
    

    $('#limitedCheck').on('ifChanged', function(event) {
        var $price = $('[name="discount_price"]');
        var $beginTime = $('#beginTime');
        var $endTime = $('#endTime');

        if ($(this).prop('checked')) {

            $price.removeAttr('disabled'); 
            $beginTime.removeAttr('disabled');
            $endTime.removeAttr('disabled');
            $beginTime.prop('required', true);
            $endTime.prop('required', true);

        } else {
            $price.val('').prop('disabled', 'disabled');
            $beginTime.val('').prop('disabled', 'disabled');
            $endTime.val('').prop('disabled', 'disabled');
            $beginTime.removeAttr('required');
            $endTime.removeAttr('required');
            $('#limitedBox').parents('.form-group').removeClass('has-error');
        };
    }); 

    //点击提交按钮
    $('#submit').bind('click', function() {
        if (formValidate.validnew()) {
            // var formnews = formValidate.serializeObject();
            // var re=new RegExp("\n","g");
            // formnews.set_detail = formnews.set_detail.replace(re,"br");
            var _postData = $.extend({}, formValidate.serializeObject()); 

            if (picUrl.getImagesNum() == 0) {
                Alert('请上传图片');
                return;
            };
            _postData.img_url = picUrl.getImages();

            _postData.limited_time_discount = (_postData.limited_time_discount == "on" ? 1 : 0);
            _postData.id = _id;
            AjaxJson('/Home/Set/editSetAjax', _postData, function(res) {

                AlertHide(res.msg, function() {

                    if (res.status == '1') {
                        HrefTo('/Home/Set/setList');
                    };
                });
            });
        };
    });

});
/*将日期对象输出成指定格式的字符串*/
function DateFormat( sdate, format) {
    var format;
    var date = {
        "M+": sdate.getMonth() + 1,
        "d+": sdate.getDate(),
        "h+": sdate.getHours(),
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
