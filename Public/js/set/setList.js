var mytable;

$(function() {
    mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: "/Home/Set/setListAjax", //表格列表数据 
        ajaxdata: {},
        scrollX: true, //是否显示横向滚动条
        tableOpts: {
            data: {
                "id": { title: "序号" },
                "set_name": { title: "套餐名称" },
                "img_url": {
                    title: "套餐图片",
                    render: function(data, type, row, meta) {  
                        var _text = '<img style="width: 110px; height: 110px;" src="'+ data +'" />';
                        return _text;
                    }
                },
                "unicom_set": { title: "对应联通套餐" },
                "market_price": { title: "原价(元)" },
                "current_price": { title: "现价(元)" },
                "exchange_integral": { title: "可兑换积分" },
                "create_time": { title: "添加日期" },
                "is_top": {
                    title: "置顶",
                    render: function(data, type, row, meta) {   
                        var _text = '<select data-id="'+ row.id +'" onchange="changeTop( this )">';
                        if( data == "0" ){ //未置顶
                            _text += '<option value="1" class="bg-green">置顶</option><option value="0" selected="selected">非置顶</option>';
                        }else{ //已经被置顶
                            _text += '<option value="1" class="bg-green" selected="selected">置顶</option><option value="0">非置顶</option>';
                        };
                        _text += '</select>';
                        return _text;
                    }
                }
            },
            operate: {
                "title": '操作', //自定义操作列 
                "width": 100,
                render: function(data, type, row, meta) {

                    if( row.is_sale == "1" ){ //上架状态,显示下架操作
                        var _salehtml = '<a data-id="'+ row.id +'" data-val="0" class="btn btn-xs btn-danger" onclick="changeSale( this )">下架</a>' ;
                    }else{
                        var _salehtml = '<a  data-id="'+ row.id +'" data-val="1" class="btn btn-xs btn-info" onclick="changeSale( this )">上架</a>' ;
                    };
                    
                    var _text = ( _salehtml
                        +' <a href="/Home/set/editSet/id/' + row.id + '" class="btn btn-xs btn-success">编辑</a>' 
                        +' <span data-id="' + row.id + '" onclick="removeRecord( this )" class="btn btn-xs btn-danger">删除</span> ');

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
    var postdata = { "is_top": el.value, "id": $(el).attr('data-id') };

    AjaxJson('/Home/Set/changeTop', postdata, function(res) {
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
/*========== 上架下架 ==========*/
function changeSale(el) {
    var postdata = { "is_sale": $(el).attr('data-val'), "id": $(el).attr('data-id') };
    var delMsg = '确定上架此套餐?';
    if($(el).attr('data-val') == '0')
    {
        delMsg = '确定下架此套餐?';
    }
    Confirm(delMsg, function(flag) {
        if (flag) {
            AjaxJson('/Home/Set/changeSale', postdata, function(res) {
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
    });
};
/*========== 删除记录 ==========*/
function removeRecord(el) {
    var _id = $(el).attr('data-id');
    Confirm('确认删除该条记录?', function(flag) {
        if (flag) {
            AjaxJson('/Home/Set/delSet/id/' + _id, function(res) {
                AlertHide(res.msg, function() {
                    if (res.status == '1') {
                        mytable.refresh();
                    };
                });
            });
        };
    });
};
