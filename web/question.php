<?php

/**
 *  获取秒杀问答问题
 *
 */
include 'init.php';
$TEMPLATE['type'] = 'question';
$TEMPLATE['pageTitle'] = '秒杀问答';

if(!$login_user_info || !$login_user_info['uid'])
{
    $result  = [
        'error_no' => '101',
        'error_msg' => '登录之后才可以参与'
    ];
    return_result($result);
}

$uid  = $login_user_info['uid'];
$ip   = getClientIp();
$aid  = getReqInt('aid');

$question_model = new \model\Question();
$ask_list = $answer_list = [];
$question_info =$question_model->getActiveQuestion($aid);

for ($i=1; $i <=10 ; $i++) 
{ 
    if(isset($question_info['ask'. $i]) && isset($question_info['answer'. $i])
        && $question_info['ask'. $i] && $question_info['answer'. $i]
    )
    {
        $ask_list[]    = $question_info['ask'. $i];
        $answer_list[] = $question_info['answer' . $i];
    }  
}
// 随机抽取最多四个问答选项
$count = count($answer_list);
if($count > 4)
{
    $count =4;
}

$question_data = [];
while(count($question_data) <  $count)
{
    $i = rand(0, count($answer_list) -1);
    $question_data[$i] = $i;
}

$data_list_ask = $data_list_answer = [];
foreach ($question_data as $k)
{
    $data_list_ask[] = $ask_list[$k];
    $data_list_answer[] = $answer_list[$k];
}

// 从中抽取选项中的一作作为问题和正确答案
$i      = rand(0, $count -1);
$ask    = $data_list_ask[$i];
$answer = $data_list_answer[$i];


$question_info = [
    'aid'       => $aid,
    'id'        => $question_info['id'],
    'ask'       => $ask,
    'answer'    => $answer,
    'data_list' => $data_list_answer,
    'title'     => $question_info['title'],
    'uid'       => $uid,
    'ip'        => $ip,
    'now'       => time()
];

$sign = signQuestion($question_info);
$result = [
    'sign' => $sign,
    'ask' => $ask,
    'data_list' => $data_list_answer,
    'title' => $question_info['title'],
];

// TODO: 每个人获取到的问题数量是要限制的，否则很容易就被全部获取和分析，失去问题的保密性
// print_r($result);
return_result($result);






















