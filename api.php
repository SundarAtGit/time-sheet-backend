<?php

include "config.php";

header("Access-Control-Allow-Origin:*");
//header("content-type:application/text");



$json = array();

$findCommand = $fm->newFindCommand('PHP__CON');

$method = $_SERVER["REQUEST_METHOD"];


switch($method){
    // case "POST":
        // echo "GET method";
        // if(isset($_GET[""]))
            // $username = $_GET["username"];
            // $query = "SELECT * FROM table WHERE username='$username'";
            
            //  echo ExecuteSQL ( "SELECT Department FROM Employees WHERE EmpID = 1", "", "" );
        // break;
    case "GET":

        $require = $_GET["require"];

        switch ($require) {
            
            case 'login':

                $username = $_GET["username"];
                $password = $_GET["password"];
               
                
                $findCommand->addFindCriterion('Username','=='.$username);
                $result = $findCommand->execute();
                if(FileMaker::isError($result)){
                    $temp = [
                        'auth' => false,
                        'result' => "Invalid Username",
                        'status' => 0
                    ];
                    echo json_encode($temp);
                }
                else{
                    $findCommand->addFindCriterion('Password','=='.$password);
                    $result = $findCommand->execute();

                    if(FileMaker::isError($result)){
                        $temp = [
                            'auth' => false,
                            'result' => "Invalid password",
                            'status' => 0
                        ];
                        echo json_encode($temp);
                    }
                    else{
                        $record = $result -> getFirstRecord();
                        $conid = $record->getField("__kp__ConId__lsan");
                        $temp = [
                            'auth' => true,
                            'result' => "Success",
                            'status' => 1,
                            'id' => "$conid"
                        ];
                        echo json_encode($temp);
                    }
                }
                break;
              
            case 'project':
                $data = array();
                $conid = $_GET["id"];
                $findCommand = $fm->newFindCommand('PHP__PROJ');
                $findCommand->addFindCriterion('_kf__ConId__lsxn','=='.$conid);
                $result = $findCommand->execute();
                $records = $result -> getRecords();
                $count = count($records);
                foreach($records as $record)
                {
                    $title = $record -> getField("TitreAffaire");
                    $projid = $record -> getField("__kp__ProjId__lsxn");
                    $userid = $record -> getField("_kf__UsrId_current__gsxn");
                    $status = $record -> getField("IsActive");
                    
                    $temp = [
                        'title' => "$title",
                        'projId' => "$projid",
                        'userId' => "$userid",
                        'status' => "$status"
                    ];
                    array_push($data,$temp);
                }
                echo json_encode($data);
            break;

            case 'request':
                $data = array();
                $projid = $_GET["projectId"];
                $findCommand = $fm->newFindCommand('PHP__RQST');
                $findCommand->addFindCriterion('_kf__ProjId__lsxn','=='.$projid);
                $result = $findCommand->execute();
                $records = $result -> getRecords();
                // print_r($records);
                // $count = count($records);
                foreach($records as $record)
                    {
                        $rqstid = $record -> getField('__kp__RqstId__lsan');
                        $rqstprj = $record -> getField('IsActive');
                        $recieved = $record -> getField('Date_recieved');
                        $target = $record -> getField('Date_target');
                        $name = $record -> getField('Name');
                        $des = $record -> getField('Description');
                        $status = $record -> getField('IsActive');
                        $assign = $record -> getField('_kf__UserId__recievedby__lsxn');
                        $statusid = $record -> getField('_kf_Tsk_StatusId__lsxn');
                        $duration = $record -> getField('Worked_Duration');

                        $temp =
                        [
                            'id' => "$rqstid",
                            'task' => $name,
                            'status' => [
                                'msg' => $status,
                                'color' => 'success'
                            ],
                            'dueStart' => $recieved,
                            'dueEnd' => $target,
                            'duration' => $duration,
                            'assign' => $assign,
                            'rqstProj' => $rqstprj,
                            'statusId' => $statusid
                        ];
                        array_push($data,$temp);
                    }
                    echo json_encode($data);
                    // echo sizeof($data);
            break;
            
            case 'task';
                $data =array();
                $rqstid = $_GET["rqstid"];
                $findCommand = $fm->newFindCommand('PHP__RQST');
                $findCommand->addFindCriterion('__kp__RqstId__lsan','=='.$rqstid);
                $result = $findCommand->execute();
                $records = $result->getRecords();
                foreach($records as $record){
                    $status = $record -> getField('IsActive');
                    $relatedSet = $record->getRelatedSet('rqst__TSK');
                    // print_r($relatedSet);
                        foreach ($relatedSet as $relatedRow)
                        {
                            $taskid = $relatedRow->getField('rqst__TSK::__kp__TskId__lsan');
                            $daterecvd = $relatedRow->getField('rqst__TSK::Date_Recevied');
                            $datedue = $relatedRow->getField('rqst__TSK::Date_Due');
                            $desc = $relatedRow->getField('rqst__TSK::description');
                            $assign = $relatedRow -> getField('Assigned to');
                            $ex_duration = $relatedRow->getField('rqst__TSK::Duration_expected');
                            $worked_duration = $relatedRow->getField('rqst__TSK::Duration_worked');
                            $statusid = $relatedRow->getField('rqst__TSK::_kf_Tsk_StatusId__lsxn');
                            $temp = [
                                'id' => "$taskid",
                                'dueDate' => "$ex_duration",
                                'dueStart' => "$daterecvd",
                                'dueEnd' => "$datedue",
                                'status' => [
                                    'msg' => $status,
                                    'color' => 'success'
                                ],
                                'assign' => $assign,
                                'task' => "$desc",
                                'duration' => "$worked_duration",
                                'statusId' => "$statusid"
                            ];
                            array_push($data,$temp);
                        }
                        echo json_encode($data);
                }
            break;

            case 'screen':
                $data = array();
                $taskid = $_GET["taskid"];
                $findCommand = $fm->newFindCommand('PHP__SCREENSHOT');
                $findCommand->addFindCriterion('_kf__TskId__lsxn','=='.$taskid);
                $result = $findCommand->execute();
                $records = $result->getRecords();
                // print_r($records);
                // $count = count($records);
                // header('Content-type: image/jpg');

                // echo "<pre>";
                foreach($records as $record)
                    {
                        // print_r($record);
                        // echo "===================================================================";
                    $srnstid = $record -> getField('__kp__IdleScreenShotId__lsan');  
                    $projid = $record -> getField('_kf__ProjId__lsxn');  
                    $userid = $record -> getField('_kf_UserId__lsxn');
                    $rqstid = $record -> getField('__kp__RqstId__lsan');
                    $title = $record -> getField('TitreAffaire');
                    $username = $record -> getField('screenshot__USR::User_name');  
                    $srnsttime = $record -> getField('Screenshot_time');  
                    $time = $record -> getField('Date_time');
                    $two = $record -> getField('Istwoscreen');
                    $date = $record -> getField('Date');
                    // $srn1 = $record->getField('Screen1');
                    $srn1 = urlencode($record -> getField('Screen1'));
                    file_put_contents('images/img.jpg', $fm->getContainerData($srn1));
                    $srn2 = urlencode($record -> getField('Screen1'));
                    // echo $srn2;
                    file_put_contents('images/img.jpg', $fm->getContainerData($srn2));
                    // $record->getField('Screen1');
                    // echo '<img src="' . $fm->getContainerDataURL($record->getField('Screen1')) .'">';
                    // $srn2 = $record->getField('Screen2');
                    // $record->getField('Screen2');
                    // '<img src="' . $fm->getContainerDataURL($record->getField('Screen2')) .'">';
                    // $srn1 = $fm->getContainerDataURL($record->getField('Screen1'));
                    // echo '<img src="config.php?path=' . urlencode($srn1) . '">';
                    // $srn2 = $fm->getContainerDataURL($record->getField('Screen2'));
                    // $srn1 = '<img src="config.php?path=' . urlencode($record->getField('Screen1')) . '">';  
                    // echo $srn1;
                    // $srn2 = '<img src="config.php?path=' . urlencode($record->getField('Screen2')) . '">';  
                    $drive = $record -> getField('IsDriveMove');
                    $img = $record -> getField('IsImage');
                    $del = $record -> getField('IsDelete');
                    $temp = [
                        'srnstId' => "$srnstid",
                        'projId' => "$projid",
                        'userId' => "$userid",
                        'rqstId' => "$rqstid",
                        'title' => "$title",
                        'userName' => "$username",
                        'srnstTime' => "$srnsttime",
                        'time' => "$time",
                        'two' => "$two",
                        'date' => "$date",
                        'srn1' => "$srn1",
                        'srn2' => "$srn2",
                        'drive' => "$drive",
                        'img' => "$img",
                        'del' => "$del"
                        ];
                        array_push($data,$temp);
                    }
                    echo json_encode($data);
            }
            break;

            case 'tasks':
                $data = array();
                $taskid = $_GET["taskid"];
                $findCommand = $fm->newFindCommand('PHP__SCREENSHOT');
                $findCommand->addFindCriterion('_kf__TskId__lsxn','=='.$taskid);
                $result = $findCommand->execute();
                $records = $result->getRecords();
                foreach($records as $record)
                    {
                        $username = $record -> getField('screenshot__USR::User_name');
                        $date = $record -> getField('Date');

            break;
    }
    
    ?>