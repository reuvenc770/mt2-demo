#!/usr/bin/perl

# *****************************************************************************************
# creative_deploy_it.cgi
#
# this page displays the Export Creative page 
#
# History
# Jim Sobeck, 04/11/06, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use Net::FTP;
use CGI;
use util;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $temp_id;
##my $dbh;
my $url_id;
my $bid;
my $got_it;
my $errmsg;
my $images = $util->get_images_url;
my $curl;
my $footer_subdomain;
my $rest_str;
my $sdate1;
my $curtime;
my $sdate;
my $aname;
my $supp_name;
my $pexicom_last_upload;
my $bname;
my $header_text;
my $footer_text;
my $network_name;
my $tracking_str;
my $camp_cnt;
my $num_subject;
my $unsub_flag;
my $include_images;
my $physical_addr;
my $temp_val;
my $num_from;
my $num_creative;
my $subject_str;
my $cid;
my $dir2;
my $creative_name;
my $oflag;
my $html_code;
my $aflag;
my $rname;
my $rloc;
my $rdate;
my $remail;
my $remailid;
my $rcdate;
my $rip;
my $rcid;
my $link_id;
my $redir_domain;
my $img_domain;
my $sid;
my $catid;
my $vid;
my $subdomain_name;
my $unsub_url;
my $unsub_img;
my $advertiser_url;
my $new_camp_id;
my $mon;
my $day;
my $year;
my $mailer_name;
my $temp_cid;
my $profile_id;
my $pname;
my $suppression_path;
my $creative_path;
my $ftp_ip;
my $ftp_username;
my $ftp_password;
my $mflag_str;
my $unsub_use;
my $unsub_text;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
my $aid= $query->param('aid');
#
#	Get information about the advertiser 
#
$sql = " select advertiser_name,vendor_supp_list_id,unsub_link,unsub_image,advertiser_url,unsub_use,unsub_text from advertiser_info where advertiser_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
($aname,$temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text)=$sth->fetchrow_array();
$sth->finish();

$sql="select list_name from vendor_supp_list_info where list_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($temp_id);
($supp_name)=$sth->fetchrow_array();
$sth->finish();
#
$supp_name =~ s/ //g;
$supp_name =~ s/\)//g;
$supp_name =~ s/\(//g;
$supp_name =~ s/\&//g;
$supp_name =~ s/\://g;
$supp_name =~ s/\$//g;
$supp_name =~ s/\.//g;
$supp_name =~ s/\!//g;
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Deployed Creative</title>
</head>

<body>
<form method=post action="/cgi-bin/save_creative.cgi">
<input type=hidden name=aid value=$aid>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table2">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table3">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table4">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Export Creative</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="TEXT-DECORATION: none" href="/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table5">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>$aname</b></font><table border="0" width="100%" id="table9">
							<tr>
								<td><b>
													Advertiser</b></td>
								<td>
													<b>$aname</b></td>
							</tr>
							<tr><td><b>Advertiser URL: </b></td><td><b>$advertiser_url</b></td></tr>
							<tr>
								<td><b>Suppression:</b></td>
								<td><b>
								<a href="http://www.mediactivate.com:83/supp/$supp_name.zip">
								http://www.mediactivate.com:83/supp/$supp_name.zip</a> </b></td>
							</tr>
							<tr>
								<td><b>Suppression<br>D/L HTML:</b></td>
								<td><b> 
					<textarea name="html_code7" rows="6" cols="59"><html>
<head>
<meta http-equiv="refresh" content="1; URL=http://www.mediactivate.com:83/supp/$supp_name.zip"> 
</head>
<body>
Downloading: http://www.mediactivate.com:83/supp/$supp_name.zip
</body>
</html>
</textarea></b></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<img height="3" src="images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
					<tr>
						<td colSpan="10">
						&nbsp;</td>
					</tr>
				</table>
					<b><u>Subject Line(s):</u></b>
end_of_html
$got_it=0;
$sql="select advertiser_subject,ss.open_cnt/ss.sent_cnt from advertiser_subject,subject_stat ss where advertiser_subject.subject_id=ss.subject_id and advertiser_subject.status='A' and advertiser_subject.advertiser_id=$aid and advertiser_subject.approved_flag='Y' group by advertiser_subject order by 2 desc"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($subject_str,$temp_val)=$sth->fetchrow_array())
{
	print "<br>$subject_str\n";
	$got_it=1;
}
$sth->finish();
if ($got_it == 0)
{
	$sql="select advertiser_subject from advertiser_subject where advertiser_subject.status='A' and advertiser_subject.advertiser_id=$aid and approved_flag='Y' order by advertiser_subject desc"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($subject_str)=$sth->fetchrow_array())
{
	print "<br>$subject_str\n";
	$got_it=1;
}
$sth->finish();
}
print<<"end_of_html";
					<br><br>
					<b><u>From Line(s):</u></b>
end_of_html
$got_it=0;
$sql="select advertiser_from,(ss.open_cnt)/(ss.sent_cnt) from advertiser_from,from_stat ss where advertiser_from.from_id=ss.from_id and advertiser_from.advertiser_id=$aid and advertiser_from != '{{FOOTER_SUBDOMAIN}}' and advertiser_from.approved_flag='Y' group by advertiser_from order by 2 desc";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($subject_str,$temp_val)=$sth->fetchrow_array())
{
	print "<br>$subject_str\n";
	$got_it=1;
}
$sth->finish();
if ($got_it == 0)
{
$sql="select advertiser_from from advertiser_from where advertiser_from.advertiser_id=$aid and advertiser_from != '{{FOOTER_SUBDOMAIN}}' and approved_flag='Y' order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($subject_str)=$sth->fetchrow_array())
{
	print "<br>$subject_str\n";
	$got_it=1;
}
$sth->finish();
}
print<<"end_of_html";
					<br><br>
end_of_html
$got_it=0;
my $reccnt;
$sql="select count(*) from creative_stat cs,creative where cs.creative_id=creative.creative_id and trigger_flag='N' and creative.status='A' and creative.advertiser_id=$aid and creative.approved_flag='Y'";
$sth =$dbhq->prepare($sql);
$sth->execute();
($reccnt) = $sth->fetchrow_array();
$sth->finish();
if ($reccnt > 0)
{
	$sql="select cs.creative_id,(((cs.click_cnt)/(cs.sent_cnt))*100000) from creative_stat cs,creative where cs.creative_id=creative.creative_id and trigger_flag='N' and creative.status='A' and creative.advertiser_id=$aid and creative.approved_flag='Y' group by cs.creative_id order by 2 desc";
}
else
{
	$sql="select creative_id,0 from creative where trigger_flag='N' and creative.status='A' and advertiser_id=$aid and approved_flag='Y' order by creative_id";
}
$sth =$dbhq->prepare($sql);
$sth->execute();
my ($copen,$cclick,$cindex);
my $thumbnail;
my $mflag;
$camp_cnt = 1;
while (($cid,$temp_val)=$sth->fetchrow_array())
{
	$sql="select creative.creative_name,thumbnail,approved_flag,original_flag,html_code,mediactivate_flag from creative where creative_id=?";
    $sth1 = $dbhq->prepare($sql) ;
    $sth1->execute($cid);
	($creative_name,$thumbnail,$aflag,$oflag,$html_code,$mflag)=$sth1->fetchrow_array();
	$sth1->finish();

    $sql = "select ((el.open_cnt)/(el.sent_cnt)*100),((el.click_cnt)/(el.open_cnt)*100),(((el.click_cnt)/(el.sent_cnt))*100000) from creative_stat el where el.creative_id=?";
    $sth1 = $dbhq->prepare($sql) ;
    $sth1->execute($cid);
    ($copen,$cclick,$cindex) = $sth1->fetchrow_array();
    $sth1->finish();
#
    if ($copen eq "")
    {
        $copen = "0.00";
    }
    if ($cclick eq "")
    {
        $cclick = "0.00";
    }
    if ($cindex eq "")
    {
        $cindex = "0";
    }
	my $temp_str="";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	if ($mflag eq "Y")
	{
		$mflag_str="checked";
	}
	else
	{
		$mflag_str="";
	}
	print "<b><u>Creative $camp_cnt: $creative_name ($copen% Open - $cclick% Click - $temp_val Index - $temp_str</u></b><br>\n";
	$got_it=1;
print<<"end_of_html";
	<font face="Tahoma">
<input type="checkbox" name="mflag" $mflag_str size="40" maxlength="90" value="$cid" style="font-weight: 700"></font><b><u>In 
					Mediactivate</u></b><br>
					<img src="http://www.affiliateimages.com/images/thumbnail/$thumbnail" border="0"><br>
					<u><b>Thumbnail Image:</b></u><b> http://www.affiliateimages.com/images/thumbnail/$thumbnail</b><u><br>
					</u><b> 
					<u>Description:</u><br>
					<textarea name="html_code5" rows="6" cols="59"><b>$creative_name<br>
<br>
<IMG SRC="http://www.affiliateimages.com/images/thumbnail/$thumbnail" ALT="" BORDER="0"><br></textarea><br>
end_of_html
	#
	# Make changes to html_code
	#
    $html_code =~ s\<BODY\<body\;
    my $pos1 = index($html_code, "<body");
    my $pos2 = index($html_code, ">",$pos1);
    substr($html_code,$pos1,$pos2-$pos1+1) = "<body>";
    $tracking_str = "";
    $html_code =~ s\<body>\<body><center><p STYLE="font-size:10pt; font-fami
ly:arial">{{HEADER_TEXT}}</p></center><p>${tracking_str}\;
    $html_code =~ s\<HEAD\<head\gi;
    $_ = $html_code;
    if (/<head/)
    {
        my $got_head = 1;
    }
    else
    {
        $html_code=~ s/<body>/<head><meta http-equiv="Content-Type" content="text\/html; charset=windows-1252"><title>{{EMAIL_USER_ID}}<\/title><\/head><body>/;
    }
    $html_code =~ s/{{TRACKING}}//g;
    my $content_str = "";
    $html_code =~ s/{{CONTENT_HEADER}}/$content_str/g;
    $content_str = "";
    $_ = $html_code;
    $html_code =~ s/{{CONTENT_HEADER_TEXT}}/$content_str/g;
    if (/{{TIMESTAMP}}/)
    {
        my $timestr = util::date($curtime,5);
        $html_code =~ s/{{TIMESTAMP}}/$timestr/g;
    }
    if (/{{REFID}}/)
    {
        $html_code =~ s/{{REFID}}//g;
    }
    $html_code =~ s/{{CLICK}}//g;
    # substitute end of page (closing body tag) with all the unsubscribe
    # footer stuff that must go on the bottom of every email, adding the
    # closing body tag back on
    $html_code =~ s\</BODY>\</body>\;
    $html_code =~ s\</body>\<p><HR width="90%" SIZE=1><p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=center>
<font face="Verdana,Arial" size="1">
</font>
<br>
{{UNSUBSCRIBE}}
</td></tr></table></center></p>\;

	$html_code =~ s\</HTML>\</html>\;
    $html_code =~ s\</html>\\;

	# now add end body tag to close the html email
	$html_code .= "<p STYLE=\"font-size:10pt; font-family:arial\">{{FOOTER_TEXT}}</p></body></html>";
	$temp_str="";
    if ($aid != 0)
    {
    	if ($unsub_use eq "TEXT")
        {
        	$temp_str=$unsub_text;
        }
        else
        {
    	if ($unsub_img ne "")
        {
			$_=$unsub_img;
			if ( /\// )
			{
            	$temp_str = "<a href=\"$unsub_url\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0></a><br><br>";
			}
			else
			{
            	$temp_str = "<a href=\"$unsub_url\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0></a><br><br>";
			}
         }
		}
    }
	$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str\g;	
#
	my $footer_str = $bid;
	$html_code =~ s/{{HEADER_TEXT}}//g;
	$html_code =~ s/{{FOOTER_TEXT}}//g;
    $html_code =~ s/{{URL}}/%url%/g;
    $html_code =~ s/{{UNSUB_LINK}}/http:\/\/{{FOOTER_SUBDOMAIN}}.$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42/g;
	$html_code =~ s/{{FOOTER_SUBDOMAIN}}/$footer_subdomain/g;
	$html_code =~ s/{{FOOTER_STR}}/$footer_str/g;
	$html_code =~ s/{{NAME}}/$rname/g;
	$html_code =~ s/{{EMAIL_ADDR}}/$remail/g;
	$html_code =~ s/{{EMAIL_USER_ID}}/$remailid/g;
	$html_code =~ s/{{LOC}}/$rloc/g;
	$html_code =~ s/{{DATE}}/$rdate/g;
	$html_code =~ s/{{CID}}/$temp_cid/g;
	$html_code =~ s/{{CRID}}//g;
	$html_code =~ s/{{CAPTURE_DATE}}/$rcdate/g;
	$html_code =~ s/{{IP}}/$rip/g;
	$html_code =~ s/{{FID}}//g;
	$html_code =~ s/{{NID}}//g;
	$html_code =~ s/{{SID}}//g;
	if ($include_images eq "Y")
	{
		my $tmp_dir = $dir2 . "/images"; 
		mkdir $tmp_dir;
    	my $p = HTML::LinkExtor->new(\&cb);
    	$p->parse($html_code);
	}
	$html_code =~ s/{{IMG_DOMAIN}}/publishercreative.com/g;
	$html_code =~ s/{{DOMAIN}}/$redir_domain/g;
	print "<u>Creative:</u><br>";
	print "<b><textarea name=html_code$camp_cnt rows=15 cols=100>$html_code</textarea><br>\n";
print<<"end_of_html";
							<a href="/cgi-bin/camp_preview.cgi?campaign_id=$cid&format=H&cdeploy=Y" target=_blank>
							<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a><br>
end_of_html
	$creative_name=~s/ /_/g;
	$creative_name=~tr/a-zA-Z/\^/c;
	$creative_name=~s/\^//g;
	open(OUTFILE,">$dir2/creative_${camp_cnt}_${creative_name}.html");
	print OUTFILE "$html_code";
	close (OUTFILE);
	$camp_cnt++;
}
$sth->finish();
print<<"end_of_html";
					</b><br>
					&nbsp;<table id="table8" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid">
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<input type=image src="/images/save.gif" height="22" width="81" border="0"></td>
							<td align="middle" width="50%">
							&nbsp;</td>
						</tr>
					</table>
					</b>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
end_of_html
exit(0);
