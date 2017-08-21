var mytable;

$(function(){
	mytable = InitDataTable({
        $el: $('#list'), //表格dom选择器
        url: "/Home/Article/articleListAjax", //表格列表数据 
        ajaxdata: {},
        tableOpts: {
            data: {
                "article_id": { title: "文章序号" }, //不需要显示的列定义visible： false
                "article_type": { title: "文章类型" },
                "article_title": { title: "文章标题" },
                "edit_time": { title: "最近编辑日期" },
                "edit_name": { title: "最近操作用户" },
                "create_name": { title: "创建用户" }    
            },
            operate: {
                "title": '操作', //自定义操作列 
                render: function(data, type, row, meta) { 

                    var _btnfh = ('<a href="/Home/Article/editArticle/id/' + row.article_id + '" class="btn btn-xs btn-success">修改</a>');
                    var _text = (_btnfh + ' <span data-id="' + row.article_id + '" onclick="removeRecord( this )" class="btn btn-xs btn-danger">删除</span> ');

                    return _text;
                }
            }
        }
    });

    $('#searchBtn').bind('click', function(){
    	var _val = $('#searchInput').val(); 
    	mytable.reloadByParam({"article_title": _val });
    });
});

function removeRecord( el ){
	var _id = $(el).attr('data-id');
	Confirm('确认删除该条记录?', function( flag ){
		if( flag ){
			AjaxJson('/Home/Article/delArticle/id/' + _id, function( res ){
				AlertHide( res.msg, function(){
					if( res.status == '1' ){
						mytable.refresh();
					};
				});
			});
		};
	}); 
};