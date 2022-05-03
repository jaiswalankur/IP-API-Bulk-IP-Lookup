<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1); 

$error = '';
$country = '';
$region = '';
$city = '';
$zip = '';
$lat = '';
$lon = '';
$timezone = '';
$isp = '';
$org = '';
$asn = '';
$result_ip = true;
$ip_form = '';
$ip_addresses = array();
$ips = array();
$output = '';
$query_ip = '';
$i = 1;

if(!empty($_POST['ip_addresses']))
{
    $ip_form = $_POST['ip_addresses'];
    $ip_addresses = explode("\n",$ip_form);
    foreach ($ip_addresses as $ip_add) {
    $ip_add = trim($ip_add);
    if(filter_var($ip_add, FILTER_VALIDATE_IP)) //confirm that input is an IP address
    {
        //IP-API only accepts 100 IPs at once, so we will divide the IPs in 10 arrays
            if($i <= 100)
            $arrkey = 1;
            elseif($i <= 200)
            $arrkey = 2;
            elseif($i <= 300)
            $arrkey = 3;
            elseif($i <= 400)
            $arrkey = 4;
            elseif($i <= 500)
            $arrkey = 5;
            elseif($i <= 600)
            $arrkey = 6;
            elseif($i <= 700)
            $arrkey = 7;
            elseif($i <= 800)
            $arrkey = 8;
            elseif($i <= 900)
            $arrkey = 9;
            elseif($i <= 1000)
            $arrkey = 10;
        $ips[$arrkey][] = $ip_add;
        $i++;
    }
    if($i>1000)break;
}

//fetch API
$endpoint = 'https://pro.ip-api.com/batch?key=7pENc85aLkEJPLT';
$IPresponse = '';
$array = array();
$i = 1;
foreach($ips AS $ipKey => $ipBatch) {
$options = [
	'http' => [
		'method' => 'POST',
		'user_agent' => 'Batch-Example/1.0',
		'header' => 'Content-Type: application/json',
		'content' => json_encode($ipBatch)
	]
];
$IPresponse = file_get_contents($endpoint, false, stream_context_create($options));
$IPresponse_array = json_decode($IPresponse, true);

//process the Json array
foreach($IPresponse_array as $result)
{
if(!empty($result['status']) && $result['status'] == "success") //is fine
{
    //info for each IP
    $query_ip = (!empty($result['query']))?$result['query']:"Unknown";
    $country = (!empty($result['country']))?$result['country']:"Unknown";
    $region = (!empty($result['regionName']))?$result['regionName']:"Unknown";
    $city = (!empty($result['city']))?$result['city']:"Unknown";
    $lat = (!empty($result['lat']))?$result['lat']:0;
    $lon = (!empty($result['lon']))?$result['lon']:0;
    $isp = (!empty($result['isp']))?$result['isp']:"Unknown";
    $org = (!empty($result['org']))?$result['org']:"Unknown";
    $asn = (!empty($result['as']))?$result['as']:"Unknown";
    $output .= "<tr><td>$i</td><td>$query_ip</td><td>$country</td><td>$city</td><td>$region</td><td>$isp</td><td>$org</td><td>$lat</td><td>$lon</td>";
}
    elseif(!empty($result['status']) && $result['status'] == "fail")
    {
        $query_ip = (!empty($result['query']))?$result['query']:"Unknown";
        $country = (!empty($result['message']))?$result['message']:"Failed";
        $output .= "<tr><td>$i</td><td>$query_ip</td><td>$country</td><td></td><td></td><td></td><td></td><td></td><td></td>";
    }
    $i++;
}
}
}


if($error){ echo "<p><b style=\"color:red\">$error</b></p>"; } ?>
<form action="" method="post">
<p>IP Addressses:</p><textarea name="ip_addresses" rows="20" cols="100"><?php echo $ip_form;?></textarea>
<p>Maximum 1000 IPs at once. Extra IPs will be excluded.</p>
<p><input type="submit" value="Check"></p>
</form>
<?php
if($output) //display the results table if there's an output
{
echo "<h3>Result</h3>
<table class=\"iptab\"><tr><th>#</th><th>IP</th><th>Country</th><th>City</th><th>Region</th><th>ISP</th><th>Org</th><th>Latitude</th><th>Longitude</th> $output </table>";
}
?>
