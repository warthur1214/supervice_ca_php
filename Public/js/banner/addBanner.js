$(function() {
    var formValidate = InitValidateForm($('#info_form'));

    var picUrl = InitUploader('pictureUrl', '/Home/MyUpload/index/path/Banner', 1);

    AjaxJson("/Home/Article/articleListAjax", function(res) {

        InitAutocomplete({
            dataSource: res.data,
            $el: $("#native_name"),
            valueKey: 'article_id',
            labelKey: 'article_title'
        });
    });

    //banner链接类型
    $('[name="active_url_type"]').on('ifChecked', function(event) {
        var _val = $(this).val(); 
        var $activeName = $("#native_name");
        var $nativeNameBox = $('.native-name-box');

        switch (_val) {

            case '1': //勾选链接
                
                $activeName.val('');
                $nativeNameBox.addClass('hide');
                $('[name="active_url"]').val('');
                $('[name="active_url"]').prop('type', 'text');

                break;

            case '2': //勾选文章
                $('[name="active_url"]').val('');
                $('[name="active_url"]').prop('type', 'hidden');

                $activeName.val(''); 
                $nativeNameBox.removeClass('hide');
                break;
        };
    });

    //点击提交按钮
    $('#submit').bind('click', function() {
        if (formValidate.validnew()) {
            var _postData = $.extend({}, formValidate.serializeObject()); 
            _postData.native_name = formValidate.serializeObject().article_id;
            _postData.native_type = 2;
            delete _postData.article_id;

            if( picUrl.getImagesNum() == 0 ){
                Alert('请上传图片');
                return;
            };
            _postData.picture_url = picUrl.getImages();  

            AjaxJson('/Home/Banner/addBannerAjax', _postData, function(res) {

                AlertHide(res.msg, function() {

                    if (res.status == '1') {
                        HrefTo('/Home/Banner/bannerList');
                    };
                });
            });
        };
    });

});
