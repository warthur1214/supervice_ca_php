function InitMobiView(){
    var _initMobiView = {
        _$el: '',
        _init: function(){
            var _html = '<div class="modal-mobile hide">'+
                '<div class="inner">'+
                    '<span class="btn btn-back btn-sm">关闭</span>'+
                    '<div class="mobile-box">'+
                        '<div class="header">'+
                            '<span class="icon-circle circle1"></span>'+
                            '<div>'+
                                '<span class="icon-circle circle2"></span>'+
                                '<span class="icon-circle circle3"></span>'+
                            '</div>'+
                        '</div>'+
                        '<div class="content"></div>'+
                        '<div class="footer">'+
                            '<span class="icon-circle circle4"></span>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';
            this._$el = $(_html);
            this._$el.appendTo(top.document.body); 
            this._$el.find('.btn-back').bind('click', function(){
                _initMobiView.hide();
            });
        },
        setContent: function( str ){
            this._$el.find('.content').html( str );
            this.show();
        },
        show: function(){
            this._$el.removeClass('hide');
        },
        hide: function(){
            this._$el.addClass('hide');
        }
    };
    _initMobiView._init();
    return _initMobiView;
};