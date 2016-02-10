<?php 
$username="db_user";
$password="sp1r3V";
$database="new_mail";
$db_object = mysql_connect("updatep.pcposts.com",$username,$password);
@mysql_select_db($database) or die( "Unable to select database");
//
set_time_limit(0);
$sid=$_REQUEST['sid'];
$a=$_REQUEST['a'];
$b=$_REQUEST['b'];
$c=$_REQUEST['c'];
$r1=$_REQUEST['r1'];
$r2=$_REQUEST['r2'];
$source_name=$_REQUEST['source_name'];
	$font_id =1;
	$color_id=4;
	$bg_color_id=8;
//
//	Get red, green, and blue for colors
//
	$query = "select red,green,blue from colors where color_id=$bg_color_id"; 
	$result1=mysql_query($query);
	if ($row = mysql_fetch_assoc($result1)) 
	{
		$bg_red=$row['red'];
		$bg_green=$row['green'];
		$bg_blue=$row['blue'];
	}
	mysql_free_result($result1);
	$query = "select red,green,blue from colors where color_id=$color_id"; 
	$result1=mysql_query($query);
	if ($row = mysql_fetch_assoc($result1)) 
	{
		$red=$row['red'];
		$green=$row['green'];
		$blue=$row['blue'];
	}
	mysql_free_result($result1);
//
	$query = "select ttf_file, font_size from fonts where font_id=$font_id"; 
	$result1=mysql_query($query);
	if ($row = mysql_fetch_assoc($result1)) 
	{
		$font_size=$row['font_size'];
		$font_file=$row['ttf_file'];
	}
	mysql_free_result($result1);

	$font = "/var/www/util/data/fonts/ttf/$font_file";
	$click_font = "/var/www/util/data/fonts/ttf/$font_file";
	$im = imagecreate(800, 90);
	$bg = imagecolorallocate($im, $bg_red, $bg_green, $bg_blue);
	$textcolor = imagecolorallocate($im, $red, $green, $blue);
	$click_color = imagecolorallocate($im, 0, 0, 255);
	ImageStringAlignAndWrap($im, 0, 0, 800, $font, $textcolor, $click_font, $click_color, $source_name, $font_size, $align="c"); 
	Imagejpeg($im,"/var/www/util/creative/$a/$b/$c/${r1}_as_${r2}.jpg");
	ImageDestroy($im);
	$img_name=$r1."_as_".$r2.".jpg";
    $source_name= htmlspecialchars($source_name);
	if ($sid > 0)
	{
		$sql="update article_source set source_name='".$source_name."',image_url='".$img_name."' where source_id=".$sid;
	}
	else
	{
		$sql="insert into article_source(source_name,image_url) values('".$source_name."','".$img_name."')";
	}
    $check = mysql_query($sql);
mysql_close();
header("Location: /cgi-bin/article_source_list.cgi");

function ImageStringAlignAndWrap($im, $x, $y, $width, $font, $color, $click_font, $click_color, $text, $textSize, $align) {

	$y+=$textSize;
	$pos = strpos($text,'{{UL}}');
	if ($pos == false) {
		$text1 = $text;
	}
	else {
		$text1 = substr($text,0,$pos);
		$ctext = substr($text,$pos+6);
	}
	$words=split(" ", $text1);
	$line='';
	$LinesArray=Array();
    foreach ($words as $word) {
        $dimen=imagettfbbox($textSize, 0, $font, $line.' '.$word);
        $wordW=abs($dimen[4])-abs($dimen[0]);
        if ($wordW<$width) {
            if (preg_match('/\r/', $word)) {
                $word= str_replace("\r","{R}",$word);
                $word= str_replace("\n","",$word);
                list($before,$after)=split('{R}', $word);
                $line=$line.' '.$before;
                $LinesArray[]=trim($line);
                $line=$after;
            }
            else {
                $line=$line.' '.$word;
            }
        }
        else {
            $LinesArray[]=trim($line);
            $line=$word;
        }
    }
    $LinesArray[]=trim($line);

	$hDim=imagettfbbox($textSize, 0, $font, "MXQAjgqp1234");
	$lineH=$hDim[1]-$hDim[5];

	foreach ($LinesArray as $ln => $lword) {
		$ldem=imagettfbbox($textSize, 0, $font, $lword);
		$lWidth=$ldem[4]-$ldem[0];
		if ($align!='l') {
			if ($align=='r') {
				$locX=$x + $width - $lWidth;
			}
			else { $locX=$x + ($width/2) - ($lWidth/2); }
		}
		else {
			$locX=$x;
		}
		$locY=$y + ($ln * $lineH);
		imagettftext($im, $textSize, 0, $locX, $locY, $color, $font, $lword);
	}
    $locY+=$textSize+5;
    $clkDim=imagettfbbox($textSize, 0, $font, $ctext);
    $clkW=$clkDim[4]-$clkDim[0];
    $locX=$x + ($width/2) - ($clkW/2);
	if ($ctext) {
	    imagettftext($im, $textSize, 0, $locX, $locY, $click_color, $click_font, $ctext);
    	$SUlineDim=imagettfbbox($textSize, 0, $font, "_");
	    $sulineW=$SUlineDim[4]-$SUlineDim[0];
	    $need_=floor($clkW/$sulineW);
	    $uline="_";
	    for ($i=1; $i<=$need_; $i++) {
    	    $uline.='_';
 		}
	    $locX=$x + ($width/2) - ($clkW/2);
	    imagettftext($im, $textSize, 0, $locX, $locY, $click_color, $click_font, $uline);
	}
}
?>
