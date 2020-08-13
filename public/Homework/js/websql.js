/**
*数据库操作辅助类,定义对象、数据操作方法都在这里定义
*/
var dbname='websql';/*数据库名*/
var version = '1.0'; /*数据库版本*/
var dbdesc = 'websql练习'; /*数据库描述*/
var dbsize = 2*1024*1024; /*数据库大小*/
var dataBase = null; /*暂存数据库对象*/
/*数据库中的表单名*/
var websqlTable = "questions";

/**
 * 打开数据库
 * @returns  dataBase:打开成功   null:打开失败
 */
function websqlOpenDB(){
    /*数据库有就打开 没有就创建*/
    dataBase = window.openDatabase(dbname, version, dbdesc, dbsize,function() {});
    if (dataBase) {
        console.log("数据库创建/打开成功!");
    } else{
        console.log("数据库创建/打开失败！");
    }
    return dataBase;
}
/**
 * 新建数据库里面的表单
 * @param tableName:表单名
 */
function websqlCreatTable(tableName){
//  chinaAreaOpenDB();
    var creatTableSQL = 'CREATE TABLE IF  NOT EXISTS '+ tableName + ' (id INTEGER,answerID text, questionID text,homeworkID text,examsID text,studentID text,classID text,isCorrect text)';
    dataBase.transaction(function (ctx,result) {
        ctx.executeSql(creatTableSQL,[],function(ctx,result){
            console.log("表创建成功 " + tableName);
        },function(tx, error){
            console.log('创建表失败:' + tableName + error.message);
        });
    });
}
/**
 * 往表单里面插入数据
 */
function websqlInsterDataToTable(tableName,id,answerID,questionID,HomeworkID,examsID,studentID,classID,isCorrect){
	websqlDeleteADataFromTable(tableName,id,answerID,questionID,HomeworkID,examsID,studentID,classID,isCorrect);
    var insterTableSQL = 'INSERT INTO ' + tableName + ' (id,answerID,questionID,homeworkID,examsID,studentID,classID,isCorrect) VALUES (?,?,?,?,?,?,?,?)';
    dataBase.transaction(function (ctx) {
        ctx.executeSql(insterTableSQL,[id,answerID,questionID,HomeworkID,examsID,studentID,classID,isCorrect],function (ctx,result){
            console.log("插入" + tableName   + "成功");
        },
        function (tx, error) {
            console.log('插入失败: ' + error.message);
        });
    });
}
/**
 * 获取数据库一个表单里面的所有数据
 * @param tableName:表单名
 * 返回数据集合
 */
function websqlGetAllData(tableName,homeworkid,studentid,classid,callback){
	var data=[];
    var selectALLSQL = 'SELECT id,isCorrect FROM ' + tableName+' where homeworkID=? and studentID=? and classID=? order by id';
    console.log(selectALLSQL);
    dataBase.transaction(function (ctx) {
        ctx.executeSql(selectALLSQL,[homeworkid,studentid,classid],function (ctx,results){
            console.log('查询成功: ' + tableName + results.rows.length);
            var data=[];
            if(results.rows && results.rows.length) {
                for(i = 0; i < results.rows.length; i++) {
                	var temp={};
                	console.log(results.rows.item(i).id);
                	temp.id=results.rows.item(i).id;
                	temp.iscorrect=results.rows.item(i).isCorrect;
                    data.push(temp);
                }
            }
            if( typeof(callback) == 'function') callback(data);
        },
        function (tx, error) {
            console.log('查询失败: ' + error.message);
        });
    });
}
/**
 * 获取数据库一个表单里面的部分数据
 * @param tableName:表单名
 * @param name:姓名
 */
function websqlGetAData(tableName,homeworkid,studentid,classid,id,summaryhtml,callback){    
    var selectSQL = 'SELECT * FROM ' + tableName + ' WHERE homeworkID=? and studentID=? and classID=? and id=?'
    dataBase.transaction(function (ctx) {
        ctx.executeSql(selectSQL,[homeworkid,studentid,classid,id],function (ctx,result){
        	console.log('查询成功: ' + result.rows.length);
            if( typeof(callback) == 'function') callback(result.rows.length);
        },
        function (tx, error) {
        	return 0;
            console.log('查询失败: ' + error.message);
        });
    });
}
/**
 * 删除表单里的全部数据
 * @param tableName:表单名
 */
function websqlDeleteAllDataFromTable(tableName){
    var deleteTableSQL = 'DELETE FROM ' + tableName;
    localStorage.removeItem(tableName);
    dataBase.transaction(function (ctx,result) {
        ctx.executeSql(deleteTableSQL,[],function(ctx,result){
            console.log("删除表成功 " + tableName);
        },function(tx, error){
            console.log('删除表失败:' + tableName + error.message);
        });
    });
}
/**
 * 根据name删除数据
 * @param tableName:表单名
 * @param name:数据的姓名
 */
function websqlDeleteADataFromTable(tableName,id,answerID,questionID,HomeworkID,examsID,studentID,classID){
    var deleteDataSQL = 'DELETE FROM ' + tableName + ' WHERE id = ? and answerID=? and questionID=? and homeworkID=? and examsID=? and studentID=? and classID=?';
    localStorage.removeItem(tableName);
    dataBase.transaction(function (ctx,result) {
        ctx.executeSql(deleteDataSQL,[id,answerID,questionID,HomeworkID,examsID,studentID,classID],function(ctx,result){
            console.log("删除成功 " + tableName );
        },function(tx, error){
            console.log('删除失败:' + tableName   + error.message);
        });
    });
}
/**
 * 根据name修改数据
 * @param tableName:表单名
 * @param name:姓名
 * @param age:年龄
 */
function websqlUpdateAData(tableName,name,age){
    var updateDataSQL = 'UPDATE ' + tableName + ' SET AGE = ? WHERE NAME = ?';
    dataBase.transaction(function (ctx,result) {
        ctx.executeSql(updateDataSQL,[age,name],function(ctx,result){
            console.log("更新成功 " + tableName + name);
        },function(tx, error){
            console.log('更新失败:' + tableName  + name + error.message);
        });
    });
}
