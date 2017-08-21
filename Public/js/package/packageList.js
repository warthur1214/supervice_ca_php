var mytable;

$(function() {
    mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: "/Home/Banner/bannerListAjax", //表格列表数据 
        ajaxdata: {},
        scrollX: true, //是否显示横向滚动条
        tableOpts: {
            data: {
                "new_banner_type": { title: "banner类型" },
                "picture_url": {
                    title: "图片",
                    render: function(data, type, row, meta) {
                        var _text = '<img width="150" src="' + data + '" />';
                        return _text;
                    }
                },
                "active_name": { title: "活动描述" },
                "active_url": { title: "活动链接" },
                "begin_date": { title: "开始日期" },
                "end_date": { title: "结束日期" },
                "create_time": { title: "添加日期" },
                "top_type": {
                    title: "状态",
                    render: function(data, type, row, meta) {
                        //0状态显示置顶，1状态显示取消
                        var _text = '';
                        var _class = '';
                        switch (data) { 
                            case '0':
                                _class = 'bg-gray';
                                _text = ('<option value="1">1</option>' +
                                    '<option value="2">2</option>' +
                                    '<option value="3">3</option>' +
                                    '<option value="4">4</option>' +
                                    '<option value="5">5</option>' +
                                    '<option value="0" selected="selected">失效</option>');
                                break;
                            case '1':
                                _class = 'bg-blue';
                                _text = ('<option value="1" selected="selected">1</option>' +
                                    '<option value="2">2</option>' +
                                    '<option value="3">3</option>' +
                                    '<option value="4">4</option>' +
                                    '<option value="5">5</option>' +
                                    '<option value="0">失效</option>');
                                break;
                            case '2':
                                _class = 'bg-blue';
                                _text = ('<option value="1">1</option>' +
                                    '<option value="2" selected="selected">2</option>' +
                                    '<option value="3">3</option>' +
                                    '<option value="4">4</option>' +
                                    '<option value="5">5</option>' +
                                    '<option value="0">失效</option>');
                                break;
                            case '3':
                                _class = 'bg-blue';
                                _text = ('<option value="1">1</option>' +
                                    '<option value="2">2</option>' +
                                    '<option value="3" selected="selected">3</option>' +
                                    '<option value="4">4</option>' +
                                    '<option value="5">5</option>' +
                                    '<option value="0">失效</option>');
                                break;
                            case '4':
                                _class = 'bg-blue';
                                _text = ('<option value="1">1</option>' +
                                    '<option value="2">2</option>' +
                                    '<option value="3">3</option>' +
                                    '<option value="4" selected="selected">4</option>' +
                                    '<option value="5">5</option>' +
                                    '<option value="0">失效</option>');
                                break;
                            case '5':
                                _class = 'bg-blue';
                                _text = ('<option value="1">1</option>' +
                                    '<option value="2">2</option>' +
                                    '<option value="3">3</option>' +
                                    '<option value="4">4</option>' +
                                    '<option value="5" selected="selected">5</option>' +
                                    '<option value="0">失效</option>');
                                break;
                        }; 
                        _text = ('<select data-id="' + row.id + '" onchange="changeTop(this)" class="'+ _class +'">' + _text + '</select>');
                        return _text;
                    }
                }
            },
            operate: {
                "title": '操作', //自定义操作列 
                "width": 100,
                render: function(data, type, row, meta) {

                    var _btnfh = ('<a href="/Home/Banner/editBanner/id/' + row.id + '" class="btn btn-xs btn-success">修改</a>');
                    var _text = (_btnfh + ' <span data-id="' + row.id + '" onclick="removeRecord( this )" class="btn btn-xs btn-danger">删除</span> ');

                    return _text;
                }
            }
        }
    });

    $('#searchBtn').bind('click', function() {
        var _val = $('#searchInput').val();
        mytable.reloadByParam({ "active_name": _val });
    });
}); 
/*========== 置顶 ==========*/
function changeTop(el) {
    var postdata = { "top": el.value, "id": $(el).attr('data-id') };

    AjaxJson('/Home/Banner/changeTop', postdata, function(res) {
        if (res.status == '0') {
            Alert(res.msg, function() {
                mytable.refresh();
            });
            return;
        };

        AlertHide(res.msg, function() {
            mytable.refresh();
        });
    });
};
/*========== 删除记录 ==========*/
function removeRecord(el) {
    var _id = $(el).attr('data-id');
    Confirm('确认删除该条记录?', function(flag) {
        if (flag) {
            AjaxJson('/Home/Banner/delBanner/id/' + _id, function(res) {
                AlertHide(res.msg, function() {
                    if (res.status == '1') {
                        mytable.refresh();
                    };
                });
            });
        };
    });
};
