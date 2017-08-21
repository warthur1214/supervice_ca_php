$(function() {
    var _id = location.href.split('id/')[1]; 
    var formValidate = InitValidateForm($('#info_form'));
    var picUrl = InitUploader('pictureUrl', '/Home/MyUpload/index/path/Banner', 1);

    AjaxJson("/Home/Banner/getInfo/id/" + _id, function( res ){
        if( res.new_banner_type != "" ){
            $('[name="new_banner_type"]').filter('[value="'+ res.new_banner_type +'"]').iCheck('check');
        };
        if( res.active_url_type != "" ){
            $('[name="active_url_type"]').filter('[value="'+ res.active_url_type +'"]').iCheck('check');
        }; 
        formValidate.assignForm( res ); 
        $('[name="article_id"]').val( res.native_name );
        $('#native_name').val( res.article_title );
        $("#activeUrl").val(res.active_url);
        picUrl.setImages( res.picture_url ); 
    }); 
    /*请求文章列表数据*/
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
                $('[name="article_id"]').val('');
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
            _postData.id = _id;
            delete _postData.article_id;

            if( picUrl.getImagesNum() == 0 ){
                Alert('请上传图片');
                return;
            };
            _postData.picture_url = picUrl.getImages();  

            AjaxJson('/Home/Banner/editBannerAjax', _postData, function(res) {

                AlertHide(res.msg, function() {

                    if (res.status == '1') {
                        HrefTo('/Home/Banner/bannerList');
                    };
                });
            });
        };
    });

});
