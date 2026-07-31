<?php
clearstatcache();
header('Content-Type:text/html;charset=UTF-8');

$mode = '';

if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = '';
}

$path = '/music';

$directory = $_SERVER['DOCUMENT_ROOT'] . $path;

$json = [];

// 打开目录
if ($handle = opendir($directory)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $extension = pathinfo($entry, PATHINFO_EXTENSION);
            if ($extension !== 'php' && $extension !== '') {
                //开始：这里需要更改代码，不同设备之间需要调试
                $filename = iconv('', 'UTF-8//IGNORE', $entry);//跟新后的不同设备兼容性已解决
                //结束：
                array_push($json, ['url' => $path . '/' . $filename]);
            }
        }
    }
    closedir($handle);
}

if ($mode === '0') {
    echo json_encode($json);
} else {
    $randomElement = getRandomElement($json);
    $url = $randomElement;
    echo $url;
}

function getRandomElement($array)
{
    $randomIndex = rand(0, count($array) - 1);
    return $array[$randomIndex];
}

?>