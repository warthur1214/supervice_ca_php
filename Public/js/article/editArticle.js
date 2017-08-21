$(function() {
    var formValidate = InitValidateForm( $('#info_form') ); 
    var _articleId = location.href.split('id/')[1];

    var editorObj = UE.getEditor('myEditor', {
        toolbars: [
            ['fullscreen', 'source', 'undo', 'redo', '|', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'simpleupload', 'insertimage', 'emotion'],
            ['fontsize', 'bold', 'italic', 'underline', 'fontborder', '|',
            'justifyleft','justifyright', 'justifycenter','justifyjustify', '|','strikethrough', 'superscript', 'subscript', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc']
        ],
        autoClearinitialContent: true,
        wordCount: false,
        elementPathEnabled: false,
        initialFrameHeight: 300,
        initialFrameWidth: '100%', 
        serverUrl: "/Public/plugins/ueditor/php/controller.php"
    });

    AjaxJson('/Home/Article/getInfo/id/' + _articleId , function( res ){
        formValidate.assignForm( res );  
        var t = setInterval(function(){
            if(editorObj.body){
                editorObj.setContent( res.article_content ); 
                clearInterval(t);  
            };
        },300); 
        
    });

    $('#submit').bind('click', function() { 
        if( formValidate.validnew() ){
            var _postData = $.extend({}, formValidate.serializeObject() );
            _postData.article_content = editorObj.getContent(); 
            _postData.article_id = _articleId;

            delete _postData.editorValue; 

            AjaxJson('/Home/Article/editArticleAjax', _postData, function( res ){

                AlertHide( res.msg, function(){

                    if( res.status == '1' ){
                        HrefTo('/Home/Article/articleList');
                    };
                });
            });
        };
    });

 
    var mobiView = InitMobiView(); 
    $('#btnView').bind('click', function() {
        var _html = '<h1>'+ $('[name="article_title"]').val() +'</h1>'
        + editorObj.getContent();
        mobiView.setContent( _html ); 
    });  
});
