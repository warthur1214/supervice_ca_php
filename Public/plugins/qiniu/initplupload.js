/**
 * Author GuilinYu 
 * Date: 2016-09-01
 */
/**======== 封装过的图片上传组件=========================
 * @param[String] btnId: 触发文件选择对话框的按钮，为那个元素id
 * @param[String] url: 服务器端的上传页面地址 
 * @param[String] num: 允许上传的图片数量
 */
function InitUploader(btnId, url, num, opts) {
    var $ul = $('<div class="upload-imgbox clearfix"></div>');
    var _imgWidth = opts ? opts._imgWidth : 750;
    var _imgHeight = opts ? opts._imgHeight : 182;
    $('#' + btnId).before($ul);

    inituploader();

    function inituploader() {
        var uploader = new plupload.Uploader({ //实例化一个plupload上传对象
            browse_button: btnId, //触发文件选择对话框的按钮，为那个元素id
            url: url, //服务器端的上传页面地址 
            resize: {
                width: _imgWidth,
                height: _imgHeight,
                crop: true
            },
            filters: {
                mime_types: [ //只允许上传图片文件
                    { title: "图片文件", extensions: "jpg,gif,png" }
                ]
            }
        });
        uploader.init(); //初始化

        //绑定文件添加进队列事件
        uploader.bind('FilesAdded', function(uploader, files) {

            if (files.length + _initUploader.getImagesNum() <= num) {
                for (var i = 0, len = files.length; i < len; i++) { 
                    uploader.start();
                };
            } else {
                Alert("图片不能多于" + num + "张！");
                uploader.destroy();
                inituploader();
            };
        });
        //队列中的某一个文件上传完成后触发
        uploader.bind('FileUploaded', function(uploader, file, responseObject) {
            setImgBoxModule(JSON.parse(responseObject.response).img);
        });

        //上传队列中所有文件都上传完成后触发
        uploader.bind('UploadComplete', function(uploader, files) {
            AlertHide('上传成功');
            setImgBoxSize();
            bindEvent();
        });

        //发生错误时触发
        uploader.bind('Error', function(uploader, file) {
            AlertHide('上传失败');
        });
    };

    function setImgBoxModule(imgsrc) {
        $ul.append('<div class="item-img"><div class="img-box">' +
            '<span class="label bg-red close">×</span>' +
            '<img src="' + imgsrc + '" /></div></div>');
    };

    function setImgBoxSize() {
        /*var $itemImg = $ul.find('.img-box');
        var $img = $itemImg.find('img');
        var _w = $itemImg.eq(0).width();
        var _h = Math.floor(_w * 4 / 3);

        $itemImg.css({
            "height": _h
        });

        $img.load(function() {

            for (var i = 0, l = $img.length; i < l; i++) {
                var imgDom = $img[i];
                var _imgW = imgDom.width;
                var _imgH = imgDom.height;

                if (_imgW / _imgH > 3 / 4) { //图片偏宽,高度不够

                    _imgW = (_imgW > _w ? _w : _imgW);

                    $(imgDom).css({
                        "width": _imgW
                    });

                } else { //高度偏大

                    _imgH = (_imgH > _h ? _h : _imgH);

                    $(imgDom).css({
                        "height": _imgH
                    });
                };
                $(imgDom).css({
                    "marginTop": (_h - _imgH) / 2
                });
            };
        });*/
    };

    function bindEvent() {
        var $close = $ul.find('.close');
        $close.bind('click', function() {
            $(this).parents(".item-img").remove();
            _initUploader.getImagesNum();
        });
        _initUploader.getImagesNum();
    };
    var _initUploader = {
        /* 获取图片个数
         * @return[Number] 图片数量
         */
        getImagesNum: function() {
            var $img = $ul.find('.img-box img');
            return $img.length;
        },
        /* 获取图片地址
         * @return[Array] 图片地址数组
         */
        getImages: function() {
            var imgsrcArry = [];
            var $img = $ul.find('.img-box img');

            for (var i = 0, l = $img.length; i < l; i++) {
                var _src = $img[i].src;
                imgsrcArry.push(_src);
            };

            return imgsrcArry.join(',');
        },
        /* 设置图片*/
        setImages: function(imgsrc) {
            if (_imgArry == "") {
                return;
            };
            var _imgArry = imgsrc.split(',');
            for (var i = 0, l = _imgArry.length; i < l; i++) {
                setImgBoxModule(_imgArry[i]);
            };

            setImgBoxSize();
            bindEvent();
        }
    };
    return _initUploader;
};
