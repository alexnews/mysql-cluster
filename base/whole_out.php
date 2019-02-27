<?php
  require("/usr/local/www/peoples.ru/req/utils_new.class");
  $my=new class_utils;
  $my->sql_connect_worlds(); $i=0;
  $date_now=date("d.m.Y");

//$query="SELECT * FROM $nBase  WHERE date_in=now()";

$bad_photo="";
$query="SELECT * FROM $my->nBase_whistory";
$result=mysql_query($query);
while ($row = mysql_fetch_array($result)):
	$i++; //ECHO $i;
	$fh=nl2br ($row['histories']);
	$kol=strlen($fh);
	$NumHistory=$row['history_id'] ;
	$OEpigraph=nl2br ($row['epigraph']);
	$OAuthor_name=$row['author_name'];
	$OAuthor_2name=$row['author_2name'];
	$OSource=$row['source'];
	$OArticle=$row['article'];
	$OSource_foto=$row['source_foto'];
	$OFile_name=$row['file_name'];
	$time_work=$row['date_pub'];
	$Kod=$row['kod'];
	$file_for_new="";
	$russian_name=$my->Crussian($Kod);
	$english_name=$my->Cenglish($Kod);

	$wwwquery="SELECT * FROM $my->nBase_wcountry WHERE kod='$Kod'";
	$wwwresult=mysql_query($wwwquery);
	while ($wwwrow = mysql_fetch_array($wwwresult)):
		$PathToCount = $wwwrow['url'];
	endwhile;
	$PathToCountry=split("http://worlds.ru/", $PathToCount);
	$NewPath= $my->PATH_WWW_WORLDS."/".$PathToCountry[1];
//ECHO "$NewPath\n";
	$article_url = split ("http", $OArticle) ;
	$sit_ishodnik = split ("http", $OSource) ;
	if (empty($sit_ishodnik[1])==true) { $sit_ishodnik=$article_url; }
	$url_foto = split ("http", $OSource_foto) ;
	$path_for_pic=$my->PATH_WWW_WORLDS."/photo/";
	$http_for_pic=$my->PATH_HTTP_WORLDS."/photo/";
///////////////	$file_for_new = $NewPath."history".$OFile_name.".shtml";
	$file_for_new = $NewPath."history-".$my->filenameforworlds($article_url[0]);
	$fileforarchive="http://www.worlds.ru/".$PathToCountry[1]."history-".$my->filenameforworlds($article_url[0]);
	$rSQL  = "UPDATE $my->nBase_whistory SET full_file_name='$fileforarchive' WHERE history_id='$NumHistory'";
	$rQResult = mysql_query($rSQL);
	if (mysql_error()) { print(mysql_error()); }

	if (file_exists($file_for_new)) { unlink($file_for_new); }

	$fh_out = fopen($file_for_new,"w") or die ("File ($file_for_new) does not exist!") ;
	//// создание шапки файлика истории
	$txt_top="<html>\n\n<head>\n\n<title>$russian_name ($english_name) - $article_url[0]</title>\n<meta name='Description' content='$russian_name ($english_name) $article_url[0] рассказы, страны, интересно, путешествие, фотографии, приключения'>\n<meta name='Keywords' content='$russian_name $english_name $article_url[0] рассказы страны интересно путешествие фотографии приключения'>\n\n<meta name='author' content='Alex'>\n<meta name='email' content='http://dart-studio.com'>\n<meta name='copyright' content='dArt Studio (c) 2000-2006'>\n<meta name='designdate' content='$date_now'>\n<meta name='designversion' content='2.00'>\n<meta name='date' content='$date_now'>\n<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n\n</head>\n<!--#include virtual='/ssi/top.shtml' -->\n<TABLE><TR>\n";
	//<link href='/style/default.css' rel='STYLESHEET' type='text/css'>\n
	$txt_head="<h3>$article_url[0]</h3>\n\n<P><em>$OEpigraph</em></P><BR>\n";
	if (empty($OAuthor_2name)<>true):
		$txt_head=$txt_head."<P>Автор: $OAuthor_name $OAuthor_2name </P>\n" ;
	endif ;
	if (empty($article_url[0])<>true):
		$txt_head=$txt_head."<P>Статья: <B><a href='http$article_url[1]' target='new'>$article_url[0]</a> </B></P>\n";
	endif ;
	if (empty($sit_ishodnik[0])<>true):
		$txt_head=$txt_head."<P>Сайт: <B><a href='http$sit_ishodnik[1]' target='new'>$sit_ishodnik[0]</a></B></P>\n";
	endif ;
	if (empty($url_foto[0])<>true):
		$txt_head=$txt_head."<P>Фото: <B><a href='http$url_foto[1]' target='new'>$url_foto[0]</a></B></P>\n";
	endif ;
//  $fh = str_replace("<", "'", $fh) ;
//  $fh = str_replace(">", "'", $fh) ;
//  $fh = nl2br ($fh) ;
	$width_photo=120;
	$txt_photo="";
	$aquery="SELECT * FROM $my->nBase_wphoto WHERE kod_history='$NumHistory'";
	$aresult=mysql_query($aquery);
	while ($arow = mysql_fetch_array($aresult)):
		$Pkod=$arow['kod'];
		$Pname_photo=$arow['name_photo'];
		$Pdescription_photo=$arow['description_photo'];
		$Pkod_history=$arow['kod_history'];
		if (is_file($path_for_pic.$Pname_photo)==false):
			$bad_photo=$bad_photo."$Pname_photo\n";
		else:
			if (getimagesize($path_for_pic.$Pname_photo)==true):
				$size = GetImageSize($path_for_pic.$Pname_photo);
				$txt_photo=$txt_photo."<IMG ALT='$Pdescription_photo' IMG SRC='$http_for_pic$Pname_photo' HSPACE='5' VSPACE='5' BORDER='0' WIDTH='$width_photo'><BR><small>$Pdescription_photo</small><HR>\n";  
			else:
				$bad_photo=$bad_photo."$Pname_photo pic don't have size\n";
			endif;
		endif;
	endwhile;
	$aSQL  = "SELECT count(*) from $my->nBase_wphoto WHERE kod_history='$NumHistory'";
	$aQResult = mysql_query($aSQL);
	$row = mysql_fetch_array ($aQResult);
	if (mysql_error()) { print(mysql_error()); }
	$iph=$row[0];

	if ($iph>2):
		$txt_head=$txt_top."<TD COLSPAN=2>".$txt_head."</TD></TR><TR><TD><table width=100%><tr><td ALIGN=CENTER VALIGN=TOP><!--#include virtual='/ssi/adsense160x600.shtml' -->\n</td><TD><CENTER>";
		$txt_head.="<script type=\"text/javascript\"><!--\ngoogle_ad_client = \"pub-0102157485191784\";\ngoogle_ad_slot = \"3779071089\";\ngoogle_ad_width = 728;\ngoogle_ad_height = 90;\n//-->\n</script>\n<script type=\"text/javascript\"\nsrc=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">\n</script>\n";
		$txt_head.="</CENTER><DIV ALIGN='JUSTIFY'>$fh</DIV>\n</TD></TR></TABLE>\n</TD><TD VALIGN='TOP' ALIGN='CENTER' VALIGN='TOP' WIDTH='$width_photo'>\n$txt_photo\n</TD></TR></TABLE>\n<!--кончало текста-->\n<!--#include virtual='/ssi/foot.shtml' -->";
	else:
		$txt_head=$txt_top."<TD>$txt_head</TD><TD VALIGN='TOP' ALIGN='RIGHT' WIDTH='$width_photo'>\n$txt_photo\n</TD></TR><TR><TD COLSPAN=2 VALIGN='TOP'><table width=100%><tr><td ALIGN=CENTER VALIGN=TOP><!--#include virtual='/ssi/adsense160x600.shtml' -->\n</td><TD><CENTER>";
		$txt_head.="<script type=\"text/javascript\"><!--\ngoogle_ad_client = \"pub-0102157485191784\";\ngoogle_ad_slot = \"3779071089\";\ngoogle_ad_width = 728;\ngoogle_ad_height = 90;\n//-->\n</script>\n<script type=\"text/javascript\"\nsrc=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">\n</script>\n";
		$txt_head.="</CENTER><DIV ALIGN='JUSTIFY'>$fh</DIV>\n</TD></TR></TABLE></TD></TR></TABLE>\n<!--кончало текста-->\n<!--#include virtual='/ssi/foot.shtml' -->\n";
	endif;
	$fh_out = fopen($file_for_new, "w") or die ("File ($file) does not exist!") ;
	$success = fputs($fh_out, $txt_head) ;  
	fclose($fh_out);
endwhile;

$fh_out = fopen($my->PATH_WWW_WORLDS."/aadmin/bad_photo.txt", "w") or die ("File ($file) does not exist!") ;
$success = fputs($fh_out, $bad_photo) ;  
fclose($fh_out);


/// вывод первых страниц history
$query="SELECT * FROM $my->nBase_wcountry";
$result=mysql_query($query);
while ($row = mysql_fetch_array($result)):
  $russian_name = $row['countrus'];
  $english_name = $row['country'];
  $PathToCount = $row['url'];
  $Odescription = nl2br($row['description']);
  $Kod = $row['kod'];
  $PathToCountry=split("http://worlds.ru/", $PathToCount);
  $NewPath= $my->PATH_WWW_WORLDS."/".$PathToCountry[1];
  $Date_now=date("d.m.Y");
//index out
  $txt="<html>\n<head>\n<title>$russian_name ($english_name)</title>\n<meta name='keywords' content='$russian_name, $english_name, истории, рассказы, анекдоты, юмор, фото, люди'>\n<meta name='description' content='Все о $russian_name (All about $english_name)'>\n<meta name='author' content='Alex Kargin'>\n<meta name='email' content='http://dart-studio.com'>\n<meta name='copyright' content='dArt Studio (c) 2006 http://ezhe.ru/fri/145/'>\n<meta name='designdate' content='$Date_now'>\n<meta name='designversion' content='1.0'>\n\n<meta name='date' content='$Date_now'>\n<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n\n</head>\n<!--#include virtual='/ssi/top.shtml' -->\n<!----start-->\n<h2>$russian_name</h2><H4>$english_name</H4>\n<DIV ALIGN='JUSTIFY'>$Odescription</DIV>\n<!----end-->\n<!--#include virtual='/ssi/foot.shtml' -->\n";  
  $fh_out = fopen($NewPath."index.shtml","w") or die ("File ($file_for_new) does not exist!") ;
  $success = fputs($fh_out, $txt) ;  
  fclose($fh_out);
//humour out
  $string=""; $txt_out="";
  $hSQL  = "SELECT * FROM $my->nBase_whumor WHERE kod='$Kod'";
  $hQResults = mysql_query($hSQL);
  if (mysql_error()) { print(mysql_error()); }
  while ($hrow = mysql_fetch_array($hQResults)):
	$OTitle=stripslashes($hrow['Title']);
	$OFacts_id=stripslashes($hrow['Facts_id']);
	$OFacts_txt=nl2br(stripslashes($hrow['Facts_txt']));

	$file_for_new = $NewPath."humor-".$my->filenameforworlds($OTitle);
	$fileforarchive="http://www.worlds.ru/".$PathToCountry[1]."humor-".$my->filenameforworlds($OTitle);

	$txt="<html>\n<head>\n<title>$OTitle ($russian_name / $english_name)</title>\n<meta name='keywords' content='смешные истории, $OTitle, $russian_name, $english_name, юмор, рассказы'>\n<meta name='description' content='$OTitle $russian_name (Humor about $english_name)'>\n<meta name='author' content='Alex Kargin'>\n<meta name='email' content='http://dart-studio.com'>\n<meta name='copyright' content='dArt Studio (c) 2009'>\n<meta name='designdate' content='$Date_now'>\n<meta name='designversion' content='1.0'>\n\n<meta name='date' content='$Date_now'>\n<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n\n</head>\n<!--#include virtual='/ssi/top.shtml' -->\n<!----start-->\n<h1>$OTitle ($russian_name / $english_name)</h1>\n$OFacts_txt\n<!----end-->\n<!--#include virtual='/ssi/foot.shtml' -->\n";  
	$fh_out = fopen($file_for_new,"w") or die ("File ($file_for_new) does not exist!") ;
	$success = fputs($fh_out, $txt) ;  
	fclose($fh_out);

	$string=$string."\n<LI><a href=\"$fileforarchive\"><STRONG title=\"$OTitle\">$OTitle</strong></A>";

	$rSQL  = "UPDATE $my->nBase_whumor SET full_file_name='$fileforarchive' WHERE Facts_id='$OFacts_id'";
//echo $rSQL."\n";
	$rQResult = mysql_query($rSQL);
	if (mysql_error()) { print(mysql_error()); }

  endwhile;
  $txt="<html>\n<head>\n<title>Смешные истории о $russian_name ($english_name)</title>\n<meta name='keywords' content='смешные истории, $russian_name, $english_name, юмор, рассказы'>\n<meta name='description' content='Все о $russian_name (All about $english_name)'>\n<meta name='author' content='Alex Kargin'>\n<meta name='email' content='http://dart-studio.com'>\n<meta name='copyright' content='dArt Studio (c) 2006 http://ezhe.ru/fri/145/'>\n<meta name='designdate' content='$Date_now'>\n<meta name='designversion' content='1.0'>\n\n<meta name='date' content='$Date_now'>\n<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n\n</head>\n<!--#include virtual='/ssi/top.shtml' -->\n<!----start-->\n<h2>Смешные истории о $russian_name ($english_name)</h2>\n<DIV ALIGN='JUSTIFY'>$string</DIV>\n<!----end-->\n<!--#include virtual='/ssi/foot.shtml' -->\n";  
  $fh_out = fopen($NewPath."humour.shtml","w") or die ("File ($file_for_new) does not exist!") ;
  $success = fputs($fh_out, $txt) ;  
  fclose($fh_out);

$my->sql_close_worlds();
$my->sql_connect();
//anekdot out
  $string="";
  $aSQL  = "SELECT * FROM $my->nBase_anekdot WHERE country='$Kod'";
  $aQResults = mysql_query($aSQL);
  if (mysql_error()) { print(mysql_error()); }
  while ($row = mysql_fetch_array($aQResults)):
    $OFacts_txt=nl2br(stripslashes($row['Anek_txt']));
    $string=$string."\n<LI>$OFacts_txt<HR>";
  endwhile;
  $txt="<html>\n<head>\n<title>Анекдоты о $russian_name ($english_name)</title>\n<meta name='keywords' content='анекдоты, $russian_name, $english_name, юмор, рассказы'>\n<meta name='description' content='Все о $russian_name (All about $english_name)'>\n<meta name='author' content='Alex Kargin'>\n<meta name='email' content='http://dart-studio.com'>\n<meta name='copyright' content='dArt Studio (c) 2008 http://ezhe.ru/fri/145/'>\n<meta name='designdate' content='$Date_now'>\n<meta name='designversion' content='1.0'>\n\n<meta name='date' content='$Date_now'>\n<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n\n</head>\n<!--#include virtual='/ssi/top.shtml' -->\n<!----start-->\n<h2>Анекдоты о $russian_name ($english_name)</h2>\n<DIV ALIGN='JUSTIFY'>$string</DIV>\n<!----end-->\n<!--#include virtual='/ssi/foot.shtml' -->\n";  
  $fh_out = fopen($NewPath."anekdot.shtml","w") or die ("File ($file_for_new) does not exist!") ;
  $success = fputs($fh_out, $txt) ;  
  fclose($fh_out);

//whole people out
  $string="";
  $aSQL  = "SELECT * FROM $my->nBase_persons WHERE KodCountry='$Kod' ORDER BY SurNameRus";
  $aQResults = mysql_query($aSQL);
  if (mysql_error()) { print(mysql_error()); }
  while ($row = mysql_fetch_array($aQResults)):
    $OURL=stripslashes($row['AllUrlInSity']);
    $ONameRus=stripslashes($row['NameRus']);
    $OSurNameRus=stripslashes($row['SurNameRus']);
    $ONameEng=stripslashes($row['NameEng']);
    $OSurNameEng=stripslashes($row['SurNameEng']);
    $OEpigraph=nl2br(stripslashes($row['Epigraph']));
    if (empty($OURL)<>true):
      $string=$string."\n<LI><a href='$OURL' TITLE='$ONameRus $OSurNameRus ($ONameEng $OSurNameEng)'>$OSurNameRus, $ONameRus</A> <small>$OEpigraph</small><BR>";
    endif;
    $OURL=""; $ONameRus=""; $OSurNameRus="";
  endwhile;
  $txt="<html>\n<head>\n<title>$russian_name ($english_name) и известные люди</title>\n<meta name='keywords' content='люди, известные личности, персоны, $russian_name, $english_name'>\n<meta name='description' content='russian_name и известные люди (All people from $english_name)'>\n<meta name='author' content='Alex Kargin'>\n<meta name='email' content='http://dart-studio.com'>\n<meta name='copyright' content='dArt Studio (c) 2006 http://ezhe.ru/fri/145/'>\n<meta name='designdate' content='$Date_now'>\n<meta name='designversion' content='1.0'>\n\n<meta name='date' content='$Date_now'>\n<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n\n</head>\n<!--#include virtual='/ssi/top.shtml' -->\n<!----start-->\n<h2>$russian_name ($english_name) и известные люди</h2>\n<DIV ALIGN='JUSTIFY'>$string</DIV>\n<!----end-->\n<!--#include virtual='/ssi/foot.shtml' -->\n";  
  $fh_out = fopen($NewPath."people.shtml","w") or die ("File ($file_for_new) does not exist!") ;
  $success = fputs($fh_out, $txt) ;  
  fclose($fh_out);
$my->sql_close();
$my->sql_connect_worlds();

//history out
  $aquery="SELECT * FROM $my->nBase_whistory WHERE kod='$Kod'";
  $aresult=mysql_query($aquery);
  $file_for_new = $NewPath."history.shtml";
//ECHO $file_for_new."$Kod\n";
  if (file_exists($file_for_new)) { unlink($file_for_new); }
//  unlink($file_for_new); 
  $txt="<html>\n\n<head>\n<title>Истории о $russian_name ($english_name)</title>\n<meta name='keywords' content='$russian_name $english_name Истории Рассказы'>\n<meta name='description' content='Рассказы и Истории о стране $russian_name $english_name'>\n\n<meta name='author' content='Alex Kargin'>\n<meta name='email' content='alex@magnet.ru'>\n<meta name='copyright' content='dArt Studio (c) 2006 http://ezhe.ru/fri/145/'>\n<meta name='designdate' content='$Date_now'>\n<meta name='designversion' content='1.0'>\n\n<meta name='date' content='$Date_now'>\n<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n\n</head>\n<!--#include virtual='/ssi/top.shtml' -->\n<!------end-->\n<h2 TITLE='Истории о $russian_name'>Истории о $russian_name</h2>\n<TABLE WIDTH=100%><TR><TD ALIGN=CENTER VALIGN=TOP><!--#include virtual='/ssi/adsense160x600.shtml' -->\n</TD><TD VALIGN=TOP>\n";  
//\n<link href='/style/default.css' rel='STYLESHEET' type='text/css'>\n
  while ($arow = mysql_fetch_array($aresult)):
    $NumHistory=$arow['history_id'] ;
    $OEpigraph=nl2br(stripslashes($arow['epigraph']));
    $OArticle=$arow['article'];
    $article_url = split ("http", $OArticle) ;
//    $OFile_name=$arow['file_name'];
//    $file_for=$PathToCount."history".$OFile_name.".shtml";
    $file_for=$arow['full_file_name'];
    $txt=$txt."<a href='$file_for'><H3>$article_url[0]</H3></a>\n<em><b><p>$OEpigraph\n</p></b></em>\n\n";
  endwhile;
  $txt="$txt\n</TD></TR></TABLE>\n\n<!-------end-->\n<!--#include virtual='/ssi/foot.shtml' -->\n";  
  $fh_out = fopen($file_for_new,"w") or die ("File ($file_for_new) does not exist!") ;
  $success = fputs($fh_out, $txt) ;  
//ECHO "$fh_out\n";
  fclose($fh_out);
endwhile;


$aSQL  = "SELECT * FROM $my->nBase_whistory WHERE date_pub BETWEEN subdate(now(), interval 10 day) and now() ORDER BY date_pub DESC";
$result=mysql_query($aSQL); $txt=""; $time_prom="";
while ($row = mysql_fetch_array($result)):
  $OArticle=$row['article'];
  $article_url = split ("http", $OArticle) ;
  $OFile_name=$row['full_file_name'];
  $Kod=$row['kod'];
  $time_work=$row['date_pub'];
  $time_russ=split("-", $time_work);
  $time_rus="$time_russ[2].$time_russ[1].$time_russ[0]";
  $russian_name=$my->Crussian($Kod);
  $english_name=$my->Cenglish($Kod);
  $PathToCount=$my->Cpath($Kod);
  if ($time_work<>$time_prom):
    $txt=$txt."<BR>";
    $time_prom=$time_work;
  endif;
  $txt=$txt."<small>$time_rus</small> <a href='$OFile_name' title='$article_url'>$article_url[0]</A> <small>[<a href='$PathToCount' title='$russian_name'>$russian_name</A>]</small>; ";
endwhile;

  $file_for_new = $my->PATH_WWW_WORLDS."/ssi/story.html";
  $fh_out = fopen($file_for_new,"w") or die ("File ($file_for_new) does not exist!") ;
  $success = fputs($fh_out, $txt) ;  
  fclose($fh_out);

$my->sql_close_worlds();
//ECHO "Все хорошо";
?>
