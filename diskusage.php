<?php
    # Config here

    $zabURL = 'https://url';
    $zabAPIKey = 'apikey';

    $zabOptions = array('sslVerifyPeer' => false, 'sslVerifyHost' => false);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disks</title>

    <style>
        body
        {
            margin: 0;
            padding: 0;
        }

        .tab1
        {
            width: calc(100% - 10px);
            border: 3px solid Blue;
            padding: 5px;
            margin: 5px;
            font-size: 26px;
        }

        .tr1
        {
            background-color: lavender;
        }
        .tr2
        {
            background-color: wheat;
        }

        .legend
        {
            height: 30px;
            width: 100%;
        }

        .legenddiv
        {
            width: 25%;
            float: left;
            display:flex;
            justify-content:center;
            align-items:center;
            height: 30px;
            font-size: 24px;
        }



    </style>


</head>
<body>
<div class="legend">
    <div class="legenddiv" style="background-color: #7FF591;">0-50%</div>
    <div class="legenddiv" style="background-color: yellow;">50-70%</div>
    <div class="legenddiv" style="background-color: orange;">70-90%</div>
    <div class="legenddiv" style="background-color: red;">90-100%</div>
</div>

<?php


require_once "ZabbixApi.php";

use IntelliTrend\Zabbix\ZabbixApi;

$zbx = new ZabbixApi();







$zbx->loginToken($zabURL, $zabAPIKey, $zabOptions);

$params = array(
    'output' => array('hostid', 'host', 'name'),
    'selectGroups' => array('groupid', 'name'),
);


$result = $zbx->call('host.get',$params);


echo '<table class="tab1">';
echo "<tr><th>LP</th><th>Hostname</th><th>ID</th><th>Disk</th><th>Usage</th><th>Used</th><th>Free</th><th>Capacity</th></tr>";

foreach($result as $host) 
{

    echo "<tr class=\"tr1\"><td>LP</td><td>".$host['name']."</td><td>".$host['hostid']."</td><td></td><td></td><td></td><td></td><td></td></tr>";

    $params = array(
        'hostids' => $host['hostid']
    );
    
    $result2 = $zbx->call('item.get',$params);
    
    
    
    ##var_dump($result);


    $tab1 = array();
    $tab2 = array();
    $tab3 = array();
    $tab4 = array();
    
    
    foreach($result2 as $row)
    {
        if(stripos($row["name"], "Space utilization"))
        {
            array_push($tab1, str_replace(": Space utilization","",$row["name"]));
            //$tmp1 = $row["lastvalue"]." ".$row["units"];
            $tmp1 = $row["lastvalue"];
            array_push($tab2, $tmp1);
        }
        if(stripos($row["name"], "Used space"))
        {
            //$tmp1 = (round($row["lastvalue"]/1024/1024/1024, 2))." G".$row["units"];
            $tmp1 = $row["lastvalue"];
            array_push($tab3, $tmp1);
        }
        if(stripos($row["name"], "Total space"))
        {
            $tmp1 = $row["lastvalue"];
            array_push($tab4, $tmp1);
        }

    }


    for($i = 0; $i < sizeof($tab1); $i++)
    {
        

        echo "<tr class=\"tr2\" style=\"background-color: ";


        if($tab2[$i]>90)
        {
            echo "red !important;";
        }
        else if($tab2[$i]>70)
        {
            echo "orange !important;";
        }
        else if($tab2[$i]>50)
        {
            echo "yellow !important;";
        }
        else
        {
            echo "#7FF591 !important;";
        }



        echo "\"><td></td><td></td><td></td><td>".$tab1[$i]."</td><td>".$tab2[$i]." % </td><td>".round($tab3[$i]/1024/1024/1024,2)." GB"."</td><td>".round(($tab4[$i]-$tab3[$i])/1024/1024/1024,2)." GB</td><td>".round($tab4[$i]/1024/1024/1024,2)." GB</td></tr>";
    }



}



echo "</table>";


?>

    
</body>
</html>
