//将Ueditor二次加工
UE.registerUI('myblockquote',function(editor,uiName){
    editor.registerCommand(uiName,{
        execCommand:function(){
            this.execCommand('inserthtml',"##答案[]##");
        }
    }); 
    var btn = new UE.ui.Button({
        name:uiName,
        title:'插入填空空格',
        cssRules :"background-position: -660px -40px;",
        onclick:function () {
           editor.execCommand(uiName);
        }
    });
    editor.addListener('selectionchange', function () {
        var state = editor.queryCommandState('blockquote');
        if (state == -1) {
            btn.setDisabled(true);
            btn.setChecked(false);
        } else {
            btn.setDisabled(false);
            btn.setChecked(state);
        }
    });
    return btn;
});

UE.registerUI('myblockquote1',function(editor,uiName){
    editor.registerCommand(uiName,{
        execCommand:function(){
            this.execCommand('inserthtml',"#——#");
        }
    }); 
    var btn = new UE.ui.Button({
        name:uiName,
        title:'填空题插入下划线',
        cssRules :"background-position: -240px -40px;",
        onclick:function () {
           editor.execCommand(uiName);
        }
    });
    editor.addListener('selectionchange', function () {
        var state = editor.queryCommandState('blockquote');
        if (state == -1) {
            btn.setDisabled(true);
            btn.setChecked(false);
        } else {
            btn.setDisabled(false);
            btn.setChecked(state);
        }
    });
    return btn;
});


UE.registerUI('myblockquote2',function(editor,uiName){
    editor.registerCommand(uiName,{
        execCommand:function(){
            this.execCommand('inserthtml',"_______");
        }
    }); 
    var btn = new UE.ui.Button({
        name:uiName,
        title:'选择题干插入下划线',
        cssRules :"background-position: -240px -40px;",
        onclick:function () {
           editor.execCommand(uiName);
        }
    });
    editor.addListener('selectionchange', function () {
        var state = editor.queryCommandState('blockquote');
        if (state == -1) {
            btn.setDisabled(true);
            btn.setChecked(false);
        } else {
            btn.setDisabled(false);
            btn.setChecked(state);
        }
    });
    return btn;
});