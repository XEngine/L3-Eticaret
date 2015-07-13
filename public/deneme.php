<?php
$prices=Array ( 500 , 500 , 520 , 540 , 551 , 599 ,601 ,601 , 650 ,681 ,750 ,750 ,851 , 871 , 871 , 900 , 990 , 999 , 1101 , 1130 , 1149 , 1151 , 1278 , 1300 , 1460 );

// round the highest price 
$lastElement=end($prices);
$highestPrice=round($lastElement, -2);
$minimumPrice=$prices[0];

$maxPRoductInRange=5;

$rangeChart=array();
$chart=array();

function calculateRange(){
global $highestPrice,$maxPRoductInRange ,$rangeChart, $prices,$minimumPrice, $chart;
// range list initialize
makeRangeChart($minimumPrice,$highestPrice,$rangeChart);


$count=count($rangeChart);

for($a=0;$a<$count;$a++){
    if(isset($rangeChart[$a+1])){
        $min=$rangeChart[$a];
        $max=$rangeChart[$a+1];
        $result=checkProductCount($min,$max,$prices);
        // if count bigger than $maxPRoductInRange create bigger rangeChart and call this function recursively
        if($result[0]>$maxPRoductInRange){
            //create bigger range chart
            makeRangeChart($min,$max,$rangeChart);
            calculateRange();
        } 

    }
}
}


function checkProductCount($min,$max,$priceList){
    global $chart;
    $count=0;
    $rest=0;

    foreach( $priceList as $price){
        if($price>=$min && $price<$max) { 
            $count++; 
        } else { $rest++; }

    }
    $chart[$min]=$count;

    return array($count,$rest);
}


function makeRangeChart($min=0,$max,&$rangeChart){
    $middleOfRange=($max+$min)/2;
    $rangeChart[]=$min;
    $rangeChart[]=$middleOfRange;
    $rangeChart[]=$max;
    $rangeChart=array_unique ($rangeChart);
    sort($rangeChart, SORT_NUMERIC );
}

function printChart(){
global $chart,$highestPrice;

$minPrices=array_keys($chart);
$count=count($minPrices);
$line='';
    for($a=0;$a<$count;$a++){
        $line.=$minPrices[$a];
        $line.=(isset($minPrices[$a+1]))?' - '.$minPrices[$a+1]:'+';
        $line.='('.$chart[$minPrices[$a]].')<br>';
    }
return $line;
}

calculateRange();
echo printChart();
?>