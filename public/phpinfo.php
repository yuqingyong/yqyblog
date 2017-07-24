<?php 
$str = 'adfasdfasdfasdfddfffads';


$s = str_split($str);
print_r($s);
echo "</br>";
$new = array_fill_keys(array_values($s), 0);
print_r($new);
echo "</br>";
foreach($s as $v)
{
    $new[$v]++;
}



print_r($new);



 ?>