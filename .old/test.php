<?PHP
/*$input = 'TEST';
$message = exec("/var/www/correlation/scripts/test.py '$input'");
print_r($message);
*/

echo '<hr>';
$output = exec('/var/www/correlation/scripts/test.py 2>&1');
//$output = shell_exec($command);
echo $output;
//$arr = json_decode($output);
//var_dump($arr);


/*
$command = escapeshellcmd('/var/www/correlation/scripts/test.py');
$output = shell_exec($command);
echo $output;
*/

?>