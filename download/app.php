<!DOCTYPE html>
<html>
<head>
    <title>WeXprez - Download </title>
	<meta name="description" content="Xprez your Expression. Just Xprez it, as simple as it sounds. Xprez whatever you feel anytime, anyday, any moment of your life be it happy, sad, angry, excited, positive, negative or anything or even if you just want to voice out your opinion.">
</head>
<body>

</body>
</html>
<?php 
//Detect special conditions devices
$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Mac     = stripos($_SERVER['HTTP_USER_AGENT'],"Mac");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");

//do something with this information
// if( $iPod || $iPhone || $iPad || $Mac ){
//     echo "<h1><center>Device: iOS</center></h1>";
//     header("Location: https://apps.apple.com/us/app/wexprez/id1606886039");
// }else if($Android){
//     echo "<h1><center>Device: Android</center></h1>";
//     header("Location: https://play.google.com/store/apps/details?id=com.byvl.wexprez");
// }else{
// 	echo "<h1><center>Device: GG</center></h1>";
// 	header("Location: https://play.google.com/store/apps/details?id=com.byvl.wexprez");
// }


// if( $iPod || $iPhone || $iPad || $Mac ){
//     echo "<h1><center>Device: iOS</center></h1>";
//     header("Location: https://apps.apple.com/us/app/wexprez/id1606886039");
// }else if($Android){
//     echo "<h1><center>Device: Android</center></h1>";
//     header("Location: https://play.google.com/store/apps/details?id=com.byvl.wexprez");
// }else{
// 	echo "<h1><center>Device: GG</center></h1>";
// 	header("Location: https://play.google.com/store/apps/details?id=com.byvl.wexprez");
// }

// echo "<br><br>".$_SERVER['HTTP_USER_AGENT'];

?>

<a id="openApp" href="intent://adjustify/#Intent;scheme=https;package=com.byvl.wexprez;end">Open APP</a>