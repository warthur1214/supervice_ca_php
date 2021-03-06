function InitRoleTree(opts) {
    var _defaults = {
        $el: '',
        dataSource: '',
        textKey: '',
        valKey: '',
        childrenKey: '',
        className: '',
        checkbox: false,
        checkboxName: '',
        _cacheData: {}
    };
    var _html = '';
    var _opts = $.extend({}, _defaults, opts);
    var _initOrgTree = {
        init: function(opts) {
            var me = this; 
            for (var i in opts) {
                me[i] = opts[i];
            };
            var res = me.dataSource; 
            if (res) {
                var _checkbox = '';
                var _checkboxName = me.checkboxName;

                if (me.checkbox) {
                    _checkbox = '<input role="all" type="checkbox" value="" name="' + _checkboxName + '" /> ';
                };
                me.$el.html('');
                me.renderList(res);
                me.$el.html('<ul class="' + me.className + '">' + _html + '</ul>');
                me.bindEvent();
            };
        },
        renderList: function(res) {
            var me = this;
            var _children = me.childrenKey;
            var _val = me.valKey;
            var _text = me.textKey;
            var _checkbox = '';
            var _checkboxName = me.checkboxName;

            for (var i = 0, l = res.length; i < l; i++) {
                var _d = res[i];
                var _dataval = _d[_val];

                me._cacheData[ _dataval ] = _d;

                if (me.checkbox) {
                    _checkbox = '<input type="checkbox" value="' + _dataval + '" name="' + _checkboxName + '" /> ';
                };
                if (_d[_children]) {
                    _html += '<li class="tree-parent" data-id="' + _dataval + '"><i class="fa fa-collapse fa-chevron-down"></i>' + _checkbox + '<span class="tree-node">' + _d[_text] + '</span><ul>';
                    me.renderList(_d.son);
                    _html += '</ul></li>';
                } else {
                    _html += '<li class="tree-leaf" data-id="' + _dataval + '">' + _checkbox + '<span class="tree-node">' + _d[_text] + '</span></li>';
                };
            };
        },
        bindEvent: function() {
            var me = this;
            var $collapse = me.$el.find('.fa-collapse');
            var $treeNode = me.$el.find('.tree-node');
            $collapse.bind('click', function() {
                var $this = $(this);

                if ($this.hasClass('fa-chevron-down')) {
                    $this.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                    $this.siblings('ul').slideUp();
                } else {
                    $this.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                    $this.siblings('ul').slideDown();
                };
            });

            $treeNode.bind('click', function() {
                var $this = $(this);
                var _data = $this.parent('li').attr('data-id');

                me.$el.find('.active').removeClass('active');
                $this.addClass('active');

                var _id = $this.parent('li').attr('data-id'); 
                me.onClickNode( me._cacheData[ _id ] ); 
            });

            if (me.checkbox) {
                var $checkbox = me.$el.find(':checkbox');

                $checkbox.filter('[role="all"]').bind('change', function() {

                    if ($(this).prop('checked')) { //勾选 

                        $checkbox.prop('checked', 'checked');

                    } else { //取消勾选
                        $checkbox.prop('checked', '');
                    };
                });
            };
        },
        onClickNode: function($el) {

        }
    };
    _initOrgTree.init(_opts);
    return _initOrgTree;
};
