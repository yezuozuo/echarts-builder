<?php
/**
 * @since  2016-03-21
 */

header('Content-Type: text/html; charset=utf-8');
require_once __DIR__.'/lib/ZocoCharts.php';

$chart = new ZocoCharts();
$chart->tooltip->show = true;
$chart->legend->data[] = '销量';
$chart->xAxis[] = array(
    'type' => 'category',
    'data' => array(
        "衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子",
    ),
);
$chart->yAxis[] = array(
    'type' => 'value',
);
$chart->series[] = array(
    'name' => '销量',
    'type' => 'bar',
    'data' => array(5, 20, 40, 10, 10, 20),
);

echo $chart->render('simple-custom-id');