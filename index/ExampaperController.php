<?php




session_start();
class ExampaperController extends CommonController {

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header('Access-Control-Allow-Headers:Origin,Content-Type,Accept,token,X-Requested-With,device');
    }
    public $columData = [
		'id','addtime'
        ,'name'
        ,'time'
        ,'status'
    ];


    /**
     * 分页，列表
     * get
     */
    public function page(){
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>401,'msg'=>"你还没有登录。"]));
        $userid = $tokens['id'];
		$where = " where 1 ";//查询条件
        $orwhere = '';
        
		
        $page = isset($_REQUEST['page'])?$_REQUEST['page']:"1";
        $limt = isset($_REQUEST['limit'])?$_REQUEST['limit']:"10";
        $sort = isset($_REQUEST['sort'])?$_REQUEST['sort']:"id";
        $order = isset($_REQUEST['order'])?$_REQUEST['order']:"asc";
        foreach ($_REQUEST as $k => $val){
			if(in_array($k, $this->columData)){
                if ($val != ''){
                    $where.= " and ".$k." like '".$val."'";
                }
			}
        }
        
        $sql = "select * from `exampaper` ".$where;
        $count = table_sql($sql);
        if ($count->num_rows < 1){
            $numberCount = 0;
        }else{
            $numberCount = $count->num_rows;
        }
        $page_count = ceil($numberCount/$limt);//页数
        $startCount = ($page-1)*$limt;
        $where .= empty($orwhere) ? '' : "and (".$orwhere.")";
        $lists = "select * from `exampaper` ".$where." order by ".$sort." ".$order." limit ".$startCount.",".$limt;
        $result = table_sql($lists);
        $arrayData = array();
        if ($result->num_rows > 0) {
            while ($datas = $result->fetch_assoc()){
                array_push($arrayData,$datas);
            }
        }
        exit(json_encode([
            'code'=>0,
            'data' => [
                "total" => $numberCount,
                "pageSize" => $limt,
                "totalPage" => $page_count,
                "currPage" => $page,
                "list" => $arrayData
            ]
        ]));
    }

        /**
     * 分页，列表list
     * get
     */
    public function lists(){
        $sql = "select * from `exampaper`";
        $result = table_sql($sql);
        $arrayData = array();
        if ($result->num_rows > 0) {
            while ($datas = $result->fetch_assoc()){
                array_push($arrayData,$datas);
            }
        }
        exit(json_encode([
            'code'=>0,
            'data' =>$arrayData
        ]));
    }
    /**
     * 分页，列表list
     * get
     */
    public function list(){
        $page = isset($_REQUEST['page'])?$_REQUEST['page']:"1";
        $limt = isset($_REQUEST['limit'])?$_REQUEST['limit']:"10";
        $sort = isset($_REQUEST['sort'])?$_REQUEST['sort']:"id";
        $order = isset($_REQUEST['order'])?$_REQUEST['order']:"asc";
        $refid = isset($_REQUEST['refid']) ? $_REQUEST['refid'] : "0";
		$where = " where 1 ";//查询条件
        if(isset($_REQUEST['goodid'])) {
            $where .= " and goodid = ".$_REQUEST['goodid']." ";
        }
        $sorts = explode(",", $sort);
        $orders = explode(",", $order);
        $sortorders = "";
        foreach ($sorts as $index => $value) {
            if($index == count($sorts)-1){
                $sortorders =$sortorders.$value." ".$orders[$index];
            }else{
                $sortorders =$sortorders.$value." ".$orders[$index].",";
            }
        }
		foreach ($_REQUEST as $k => $val){
			if(in_array($k, $this->columData)){
				$where.= " and ".$k." like '".$val."'";
			}
        }
        
        $sql = "select * from `exampaper`".$where;
        $count = table_sql($sql);
        if ($count->num_rows < 1){
            $numberCount = 0;
        }else{
            $numberCount = $count->num_rows;
        }
        $page_count = ceil($numberCount/$limt);//页数
        $startCount = ($page-1)*$limt;
        $lists = "select * from `exampaper` ".$where." order by ".$sortorders." limit ".$startCount.",".$limt;
        $result = table_sql($lists);
        $arrayData = array();
        if ($result->num_rows > 0) {
            while ($datas = $result->fetch_assoc()){
                array_push($arrayData,$datas);
            }
        }
        exit(json_encode([
            'code'=>0,
            'data' => [
                "total" => $numberCount,
                "pageSize" => $limt,
                "totalPage" => $page_count,
                "currPage" => $page,
                "list" => $arrayData
            ]
        ]));
    }



    /**
     * 新增数据接口
     * post
     */
    public function save(){
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>401,'msg'=>"你还没有登录。"]));
        $uid = $tokens['id'];
        $keyArr = $valArr = array();
        $tmpData = strval(file_get_contents("php://input"));//Content-Type: application/json 需要用到php://input 处理输入流
        
        if (!empty($tmpData)&& isset($tmpData)){
            $postData = json_decode($tmpData,true);
            foreach ($postData as $key => $value){
                if (in_array($key, $this->columData)){
                    if(!empty($value) || $value === 0) {
                        if ($key == 'id') {
                            continue;
                        }
                        array_push($keyArr,"`".$key."`");
                        if($key == 'clicktime') {
                            array_push($valArr,"'".date('Y-m-d h:i:s', time())."'");
                        } else {
                            array_push($valArr,"'".$value."'");
                        }
                    }
                }
            }

    }
        $k = implode(',',$keyArr);
        $v = implode(',',$valArr);
        $sql = "INSERT INTO `exampaper` (".$k.") VALUES (".$v.")";
        $result = table_sql($sql);
        exit(json_encode(['code'=>0]));
    }
    /**
     * 新增数据接口 add
     * post
     */
    public function add(){
        $keyArr = $valArr = array();
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>401,'msg'=>"你还没有登录。"]));
        $uid = $tokens['id'];

        $tmpData = strval(file_get_contents("php://input"));
        if (!empty($tmpData)&& isset($tmpData)){
            $postData = json_decode($tmpData,true);
            foreach ($postData as $key => $value){
                if (in_array($key, $this->columData)){
                    if(!empty($value) || $value === 0) {
                        if ($key == 'id') {
                            continue;
                        }
                        array_push($keyArr,"`".$key."`");
                        if($key == 'clicktime') {
                            array_push($valArr,"'".date('Y-m-d h:i:s', time())."'");
                        } else {
                            array_push($valArr,"'".$value."'");
                        }
                    }
                }
            }

        }
        $k = implode(',',$keyArr);
        $v = implode(',',$valArr);
        $sql = "INSERT INTO `exampaper` (".$k.") VALUES (".$v.")";
        $result = table_sql($sql);
        exit(json_encode(['code'=>0]));
    }
    /**
     * 更新接口
     * post
     */
    public function update(){
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>401,'msg'=>"你还没有登录。"]));
        $uid = $tokens['id'];
        $tmpData = strval(file_get_contents("php://input"));
        $postData = json_decode($tmpData,true);
        $v = array();
        foreach ($postData as $key => $value){
            if (in_array($key, $this->columData)){
                if ($key == "id"){
                    $id = $value;
                }
                if(!empty($value) || $value === 0) {
                    array_push($v,$key." = '".$value."'");
                }
            }
        }
        $value = implode(',',$v);
        $sql = "UPDATE exampaper SET ".$value." where id = ".$id;
        $result = table_sql($sql);

        exit(json_encode(['code'=>0]));
    }
    /**
     * 删除
     * post
     */
    public function delete(){
        $ids = strval(file_get_contents("php://input"));//发现接收的是字符串
        preg_match_all('/\d+/',$ids,$arr);
        $str = implode(',',$arr[0]);//拼接字符，
        $sql = "delete from exampaper WHERE id in({$str})";
        $result = table_sql($sql);
        exit(json_encode(['code'=>0]));
    }
    /**
     * 查询一条数据
     * get
     */
    public function info($id=false){

        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>401,'msg'=>"你还没有登录。"]));
        $userid = $tokens['id'];
        $name = isset($_REQUEST['name'])? $_REQUEST['name']:"";
        if (!empty($id)){
            $where = "`id` = ".$id;
        }else{
            $where = "`name` = ".$name;
        }
        $sql = "select * from `exampaper` where ".$where;
        $result = table_sql($sql);
        if ($result->num_rows > 0) {
            // 输出数据
            while($row = $result->fetch_assoc()) {
                $lists = $row;
            }
        }
        exit(json_encode([
            'code'=>0,
            'data'=> $lists
        ]));
    }
    /**
     * 查询一条数据
     * get
     */
    public function detail($id=false){
        $name = isset($_REQUEST['name'])? $_REQUEST['name']:"";
        if ($id){
            $where = "`id` = ".$id;
        }else{
            $where = "`name` = ".$name;
        }
        $sql = "select * from `exampaper` where ".$where;
        $result = table_sql($sql);
        if (!$result) exit(json_encode(['code'=>500,'msg'=>"查询数据发生错误。"]));
        if ($result->num_rows > 0) {
            // 输出数据
            while($row = $result->fetch_assoc()) {
                $lists = $row;
            }
        }
        exit(json_encode([
            'code'=>0,
            'data'=> $lists
        ]));
    }

    /**
     * 按值统计接口
     **/
    public function value(){
        $url = explode('?',$_SERVER['REQUEST_URI']);
        $request = explode('/',$url[0]);
        $xColumnName = $request[4];
        $yColumnName = $request[5];
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        $where = " where 1 ";
        $sql = "SELECT ".$xColumnName.",sum(".$yColumnName.") total FROM exampaper ".$where." group by ".$xColumnName;
        if(urldecode($request[6]) == '日') {
            $sql = "SELECT DATE_FORMAT(".$xColumnName.", '%Y-%m-%d') ".$xColumnName.", sum(".$yColumnName.") total FROM exampaper ".$where."  GROUP BY DATE_FORMAT(".$xColumnName.", '%Y-%m-%d')";
        }
        if(urldecode($request[6]) == '月') {
            $sql = "SELECT DATE_FORMAT(".$xColumnName.", '%Y-%m') ".$xColumnName.", sum(".$yColumnName.") total FROM exampaper ".$where."  GROUP BY DATE_FORMAT(".$xColumnName.", '%Y-%m')";
        }
        if(urldecode($request[6]) == '年') {
            $sql = "SELECT DATE_FORMAT(".$xColumnName.", '%Y') ".$xColumnName.", sum(".$yColumnName.") total FROM exampaper ".$where."  GROUP BY DATE_FORMAT(".$xColumnName.", '%Y')";
        }
        $result = table_sql($sql);
        if ($result->num_rows > 0) {
            // 输出数据
            $total = array();
            while($row = $result->fetch_assoc()) {
                array_push($total,array('total' => intval($row['total']),$xColumnName => $row[$xColumnName]));
            }
        }
        exit(json_encode(['code'=>0,'data'=>$total]));
    }

    /**
     * (按值统计）时间统计类型(多)
     **/
    public function valueMul(){
        $url = explode('?',$_SERVER['REQUEST_URI']);
        $request = explode('/',$url[0]);
        $xColumnName = $request[4];
        $yColumnName = isset($_REQUEST['yColumnNameMul'])? $_REQUEST['yColumnNameMul']:"";
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        $where = " where 1 ";
        $valueData = array();

        foreach(explode(",", $yColumnName) as $item){
            $sql = "SELECT ".$xColumnName.",sum(".$item.") total FROM exampaper ".$where." group by ".$xColumnName." LIMIT 10";
            if(urldecode($request[6]) == '日') {
                $sql = "SELECT DATE_FORMAT(".$xColumnName.", '%Y-%m-%d') ".$xColumnName.", sum(".$item.") total FROM exampaper ".$where."  GROUP BY DATE_FORMAT(".$xColumnName.", '%Y-%m-%d') LIMIT 10";
            }
            if(urldecode($request[6]) == '月') {
                $sql = "SELECT DATE_FORMAT(".$xColumnName.", '%Y-%m') ".$xColumnName.", sum(".$item.") total FROM exampaper ".$where."  GROUP BY DATE_FORMAT(".$xColumnName.", '%Y-%m') LIMIT 10";
            }
            if(urldecode($request[6]) == '年') {
                $sql = "SELECT DATE_FORMAT(".$xColumnName.", '%Y') ".$xColumnName.", sum(".$item.") total FROM exampaper ".$where."  GROUP BY DATE_FORMAT(".$xColumnName.", '%Y') LIMIT 10";
            }
            $result = table_sql($sql);
            if ($result->num_rows > 0) {// 输出数据
                $total = array();
                while($row = $result->fetch_assoc()) {
                    array_push($total,array('total' => intval($row['total']),$xColumnName => $row[$xColumnName]));
                }
                $valueData[] = $total;
            }
        }

        exit(json_encode(['code'=>0,'data'=>$valueData]));
    }




    /**
     * 获取需要提醒的记录数接口
     * get
     */
    public function remind($columnName,$type){
        $remindStart = isset($_GET['remindstart'])?$_GET['remindstart']:"";
        $remindEnd = isset($_GET['remindend'])?$_GET['remindend']:"";
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        $where = " where 1 ";//查询条件
        if ($type == 1){//数字
            if ($remindStart && $remindEnd){
                $where .= " and ".$columnName."<='".$remindEnd."' and ".$columnName.">='".$remindStart."'";
            }elseif($remindStart){
                $where .= " and ".$columnName.">='".$remindStart."'";
            }elseif($remindEnd){
                $where .= " and ".$columnName."<='".$remindEnd."'";
            }
        }else{
            if ($remindStart && $remindEnd){
                $where .= " and ".$columnName."<='".date("Y-m-d",strtotime("+".$remindEnd." day"))."' and ".$columnName.">='".date("Y-m-d",strtotime("+".$remindStart." day"))."'";
            }elseif($remindStart){
                $where .= " and ".$columnName.">='".date("Y-m-d",strtotime("+".$remindStart." day"))."'";
            }elseif($remindEnd){
                $where .= " and ".$columnName."<='".date("Y-m-d",strtotime("+".$remindEnd." day"))."'";
            }
            
        }
        $sql = "select * from `exampaper` ".$where;
        $result = table_sql($sql);
        exit(json_encode(['code'=> 0 ,'count' => $result->num_rows]));
    }







    public function group($columnName){
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        $where = " where 1 ";
        $sql = "SELECT ".$columnName.",count(".$columnName.") as total FROM exampaper ".$where." GROUP BY ".$columnName." ORDER BY $columnName asc";
        $result = table_sql($sql);
        if ($result->num_rows > 0) {
            // 输出数据
            $total = array();
            while($row = $result->fetch_assoc()) {
                array_push($total,array('total' => $row['total'],$columnName => $row[$columnName]));
            }
        }
        exit(json_encode(['code'=>0,'data'=>$total]));
    }











    // 组卷接口
    public function compose(){
        $token = $this->token();
        $tokens = json_decode(base64_decode($token),true);
        if (!isset($tokens['id']) || empty($tokens['id'])) exit(json_encode(['code'=>401,'msg'=>"你还没有登录。"]));
        $paperid = isset($_REQUEST['paperid'])?$_REQUEST['paperid']:"0";
        $papername = isset($_REQUEST['papername'])?$_REQUEST['papername']:"";
        $radioNum = isset($_REQUEST['radioNum'])?$_REQUEST['radioNum']:"0";
        $multipleChoiceNum = isset($_REQUEST['multipleChoiceNum'])?$_REQUEST['multipleChoiceNum']:"0";
        $determineNum = isset($_REQUEST['determineNum']) ? $_REQUEST['determineNum'] : "0";
        $fillNum = isset($_REQUEST['fillNum']) ? $_REQUEST['fillNum'] : "0";
        $subjectivityNum = isset($_REQUEST['subjectivityNum']) ? $_REQUEST['subjectivityNum'] : "0";

        $records = "select * from `examrecord` where `paperid` = ".$paperid;
        $recordResult = table_sql($records);
        if($recordResult && mysqli_num_rows($recordResult)>0){
            exit(json_encode([
                'code'=>10001,
                'msg'=>"已存在考试记录，无法重新组卷",
            ]));
        }

        //组卷之前删除该试卷之前的所有题目
        $delSql = "delete from examquestion WHERE `paperid` =".$paperid;
        $delResult = table_sql($delSql);

        $questionList = array();
        # 单选题
        if ($radioNum > 0) {
            $where = " `type` = 0 ";
            $radioSizeQuery = "SELECT COUNT(*) AS count FROM examquestionbank WHERE ".$where;
            $radiocount = table_sql($radioSizeQuery);
            if($radiocount->num_rows < 0 || $radiocount->fetch_assoc()['count']<$radioNum) {
                exit(json_encode([
                    'code'=>10001,
                    'msg'=>"单选题库不足",
                ]));
            } else {
                // 随机选择指定数量的单选题
                $radioListQuery = "SELECT * FROM examquestionbank WHERE ".$where." ORDER BY RAND() LIMIT $radioNum";
                $radioList = table_sql($radioListQuery);
                if ($radioList->num_rows > 0) {
                    while ($datas = $radioList->fetch_assoc()){
                        array_push($questionList,$datas);
                    }
                }
            }
        }
        # 多选题
        if ($multipleChoiceNum > 0) {
            $where = " `type` = 1 ";
            $multipleChoiceNumSizeQuery = "SELECT COUNT(*) AS count FROM examquestionbank WHERE ".$where;
            $multipleChoiceNumcount = table_sql($multipleChoiceNumSizeQuery);
            if($multipleChoiceNumcount->num_rows < 0 || $multipleChoiceNumcount->fetch_assoc()['count']<$multipleChoiceNum) {
                exit(json_encode([
                    'code'=>10001,
                    'msg'=>"多选题库不足",
                ]));
            } else {
                // 随机选择指定数量的多选题
                $multipleChoiceNumListQuery = "SELECT * FROM examquestionbank WHERE ".$where." ORDER BY RAND() LIMIT $multipleChoiceNum";
                $multipleChoiceNumList = table_sql($multipleChoiceNumListQuery);
                if ($multipleChoiceNumList->num_rows > 0) {
                    while ($datas = $multipleChoiceNumList->fetch_assoc()){
                        array_push($questionList,$datas);
                    }
                }
            }
        }
        # 判断题
        if ($determineNum > 0) {
            $where = " `type` = 2 ";
            $determineNumSizeQuery = "SELECT COUNT(*) AS count FROM examquestionbank WHERE ".$where;
            $determineNumcount = table_sql($determineNumSizeQuery);
            if($determineNumcount->num_rows < 0 || $determineNumcount->fetch_assoc()['count']<$determineNum) {
                exit(json_encode([
                    'code'=>10001,
                    'msg'=>"判断题库不足",
                ]));
            } else {
                // 随机选择指定数量的判断题
                $determineNumListQuery = "SELECT * FROM examquestionbank WHERE ".$where." ORDER BY RAND() LIMIT $determineNum";
                $determineNumList = table_sql($determineNumListQuery);
                if ($determineNumList->num_rows > 0) {
                    while ($datas = $determineNumList->fetch_assoc()){
                        array_push($questionList,$datas);
                    }
                }
            }
        }
         # 填空题
         if ($fillNum > 0) {
            $where = " `type` = 3 ";
            $fillNumSizeQuery = "SELECT COUNT(*) AS count FROM examquestionbank WHERE ".$where;
            $fillNumcount = table_sql($fillNumSizeQuery);
            if($fillNumcount->num_rows < 0 || $fillNumcount->fetch_assoc()['count']<$fillNum) {
                exit(json_encode([
                    'code'=>10001,
                    'msg'=>"填空题库不足",
                ]));
            } else {
                // 随机选择指定数量的填空题
                $fillNumListQuery = "SELECT * FROM examquestionbank WHERE ".$where." ORDER BY RAND() LIMIT $fillNum";
                $fillNumList = table_sql($fillNumListQuery);
                if ($fillNumList->num_rows > 0) {
                    while ($datas = $fillNumList->fetch_assoc()){
                        array_push($questionList,$datas);
                    }
                }
            }
        }
        # 主观题
        if ($subjectivityNum > 0) {
            $where = " `type`  = 4 ";
            $subjectivityNumSizeQuery = "SELECT COUNT(*) AS count FROM examquestionbank WHERE ".$where;
            $subjectivityNumcount = table_sql($subjectivityNumSizeQuery);
            if($subjectivityNumcount->num_rows < 0 || $subjectivityNumcount->fetch_assoc()['count']<$subjectivityNum) {
                exit(json_encode([
                    'code'=>10001,
                    'msg'=>"填空题库不足",
                ]));
            } else {
                // 随机选择指定数量的填空题
                $subjectivityNumListQuery = "SELECT * FROM examquestionbank WHERE ".$where." ORDER BY RAND() LIMIT $subjectivityNum";
                $subjectivityNumList = table_sql($subjectivityNumListQuery);
                if ($subjectivityNumList->num_rows > 0) {
                    while ($datas = $subjectivityNumList->fetch_assoc()){
                        array_push($questionList,$datas);
                    }
                }
            }
        }

        if (!empty($questionList)) {
            $seq = 0;
            $currentTimestamp = time();
            foreach ($questionList as $q) {
                $creat_dict = array(
                    "id" => $currentTimestamp + rand(1, 1000),
                    "paperid" => $paperid,
                    "papername" => $papername,
                    "questionname" => $q['questionname'],
                 
                );
                $id = $currentTimestamp + rand(1, 1000);
                $questionname= $q['questionname'];
                $options= $q['options'];
                $score= $q['score'];
                $answer= $q['answer'];
                $analysis=$q['analysis'];
                $type= $q['type'];
                $sequence=$seq + 1;
                $sql = "INSERT INTO `examquestion` (`id`,`paperid`,`papername`,`questionname`,`options`,`score`,`answer`,`analysis`,`type`,`sequence`) VALUES ($id,$paperid,'$papername','$questionname','$options',$score,'$answer','$analysis',$type,$sequence)";

                $result = table_sql($sql);
                $seq++;
            }
        }
        exit(json_encode([
            'code'=>0,
            'msg'=>"组卷成功"
        ]));
    }





}

