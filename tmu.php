<!DOCTYPE html>
<html>
<head>
<style>
    .column {
    float: left;
    width: 25%;
    }
    
    h2 {
    margin-left: 25px;
    }
    
    table {
    border-collapse: separate;
    border-spacing: 25px 5px;
    }
</style>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="row">
<meta http-equiv="refresh" content="60" >
<?php
// callsign:cid:realname:clienttype:frequency:latitude:longitude:altitude:groundspeed:planned_aircraft:planned_tascruise:planned_depairport:planned_altitude:planned_destairport:server:protrevision:rating:transponder:facilitytype:visualrange:planned_revision:planned_flighttype:planned_deptime:planned_actdeptime:planned_hrsenroute:planned_minenroute:planned_hrsfuel:planned_minfuel:planned_altairport:planned_remarks:planned_route:planned_depairport_lat:planned_depairport_lon:planned_destairport_lat:planned_destairport_lon:atis_message:time_last_atis_received:time_logon:heading:QNH_iHg:QNH_Mb:
//echo "hi";

function distance($lat1, $lon1, $lat2, $lon2)
{
  $R = 6371e3; // metres
  $phi1 = deg2rad($lat1);
  $phi2 = deg2rad($lat2);
  $dphi = deg2rad($lat2 - $lat1);
  $dlam = deg2rad($lon2 - $lon1);

  $a = sin($dphi / 2) * sin($dphi / 2) + cos($phi1) * cos($phi2) * sin($dlam / 2) * sin($dlam / 2);
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

  return round($R * $c / 1852);
}

function dec($decimal) {
    $hours = floor($decimal);
    $minutes = floor(($decimal - (int)$decimal) * 60);
 
    return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT);
}


$txt_file    = file_get_contents('http://cluster.data.vatsim.net/vatsim-data.txt');
$rows        = explode("\n", $txt_file);
array_shift($rows);

$east = array();
$north = array();
$south = array();
$west = array();

foreach($rows as $row => $data)
{
  $row_data = explode(':', $data);
  
  if (count($row_data) > 2 && strpos($row_data[13], 'KSFO') !== false && $row_data[8] > 60 && (strpos($row_data[30], 'DYAMD') !== false || strpos($row_data[30], ' MOD') !== false || strpos($row_data[30], ' ALWYS') !== false || strpos($row_data[30], ' YOSEM') !== false)) // filtering aircraft, not on ground, on arrival
  {
    if (distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) > 94 && distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) < 10000) // distance from ksfo
    {
      $dist = distance($row_data[5], $row_data[6], 37.69916111111, -120.404427777); // distance from dyamd
      $key = $row_data[0] . ' ' . $dist . ' ' . dec(round($dist / $row_data[8], 2));
      
      $east[$key] = $dist;
    }
  }
}

foreach($rows as $row => $data)
{
  $row_data = explode(':', $data);
  
  if (count($row_data) > 2 && $row_data[13] = "KSFO" && $row_data[8] > 60 && (strpos($row_data[30], 'BDEGA') !== false || strpos($row_data[30], 'PYE') !== false || strpos($row_data[30], 'STINS') !== false || strpos($row_data[30], 'STLER') !== false)) // filtering aircraft, not on ground, on arrival
  {
    if (distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) > 41 && distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) < 10000) // distance from ksfo
    {
      $dist = distance($row_data[5], $row_data[6], 38.22458888888, -122.767505555); // distance from bgglo
      $key = $row_data[0] . ' ' . $dist . ' ' . dec(round($dist / $row_data[8], 2));
      
      $north[$key] = $dist;
    }
  }
}

foreach($rows as $row => $data)
{
  $row_data = explode(':', $data);
  
  if (count($row_data) > 2 && $row_data[13] = "KSFO" && $row_data[8] > 60 && (strpos($row_data[30], 'SERFR') !== false || strpos($row_data[30], 'WWAVS') !== false || strpos($row_data[30], 'BSR') !== false)) // filtering aircraft, not on ground, on arrival
  {
    if (distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) > 105 && distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) < 10000) // distance from ksfo
    {
      $dist = distance($row_data[5], $row_data[6], 36.06830555555, -121.364663888); // distance from serfr
      $key = $row_data[0] . ' ' . $dist . ' ' . dec(round($dist / $row_data[8], 2));
      
      $south[$key] = $dist;
    }
  }
}

foreach($rows as $row => $data)
{
  $row_data = explode(':', $data);
  
  if (count($row_data) > 2 && $row_data[13] = "KSFO" && $row_data[8] > 60 && strpos($row_data[30], 'PIRAT') !== false) // filtering aircraft, not on ground, on arrival
  {
    if (distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) > 32 && distance($row_data[5], $row_data[6], 37.618805555556, -122.3754166666) < 10000) // distance from ksfo
    {
      $dist = distance($row_data[5], $row_data[6], 37.25765, -122.863352777); // distance from pirat
      $key = $row_data[0] . ' ' . $dist . ' ' . dec(round($dist / $row_data[8], 2));
      
      $west[$key] = $dist;
    }
  }
}

asort($east);
asort($north);
asort($west);
asort($south);

echo "<div class='column'>";

$prev = -100;
echo "<h2>DYAMD  -  " . count($east) . "</h2>";
echo "<table>";
foreach(array_keys($east) as $craft)
{
  $row = explode(" ", $craft);
  if ($row[1] - $prev < 20)
  {
    echo "<tr style='color:#ffa500'>";
  }
  else
  {
    echo "<tr>";
  }
  echo "<td><b>" . $row[0] . "</b></td>" . "<td>" . $row[1] . "</td>" . "<td>" . $row[2] . "</td>";
  echo "</tr>";
  $prev = $row[1];
}
echo "</table>";

echo "</div>";

echo "<div class='column'>";

$prev = -100;
echo "<h2>BDEGA  -  " . count($north) . "</h2>";
echo "<table>";
foreach(array_keys($north) as $craft)
{
  $row = explode(" ", $craft);
  if ($row[1] - $prev < 20)
  {
    echo "<tr style='color:#ffa500'>";
  }
  else
  {
    echo "<tr>";
  }

  echo "<td><b>" . $row[0] . "</b></td>" . "<td>" . $row[1] . "</td>" . "<td>" . $row[2] . "</td>";
  echo "</tr>";
  $prev = $row[1];
}
echo "</table>";

echo "</div>";

echo "<div class='column'>";

$prev = -100;
echo "<h2>SERFR  -  " . count($south) . "</h2>";
echo "<table>";
foreach(array_keys($south) as $craft)
{
  $row = explode(" ", $craft);
  if ($row[1] - $prev < 20)
  {
    echo "<tr style='color:#ffa500'>";
  }
  else
  {
    echo "<tr>";
  }
  
  echo "<td><b>" . $row[0] . "</b></td>" . "<td>" . $row[1] . "</td>" . "<td>" . $row[2] . "</td>";
  echo "</tr>";
  $prev = $row[1];
}
echo "</table>";

echo "</div>";

echo "<div class='column'>";

$prev = -100;
echo "<h2>PIRAT  -  " . count($west) . "</h2>";
echo "<table>";
foreach(array_keys($west) as $craft)
{
  $row = explode(" ", $craft);
  if ($row[1] - $prev < 20)
  {
    echo "<tr style='color:#ffa500'>";
  }
  else
  {
    echo "<tr>";
  }
  
  echo "<td><b>" . $row[0] . "</b></td>" . "<td>" . $row[1] . "</td>" . "<td>" . $row[2] . "</td>";
  echo "</tr>";
  $prev = $row[1];
}
echo "</table>";

echo "</div>";
?>
</div>
</body>
</html>