#!/usr/bin/perl

# *****************************************************************************************
# dosmonos_deploy_it.cgi
#
# this page displays the Dos Monos deployed page 
#
# History
# Jim Sobeck, 02/24/06, Creation
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
my $dbh;
my $url_id;
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
my $new_camp_id;
my $mon;
my $day;
my $year;
my $mailer_name;
my $temp_cid;
my $profile_id;
my $rows;
my $suppression_path;
my $creative_path;
my $ftp_ip;
my $ftp_username;
my $ftp_password;
my $dl_id;
my $dl_bid;
my $id;
my $bid;
my $client_id;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
$sql="select third_party_id from third_party_defaults where mailer_name='Dos Monos'";
$sth = $dbh->prepare($sql);
$sth->execute();
($id)=$sth->fetchrow_array();
$sth->finish();
$sql="select user_id from user where company='Domain Lanes'";
$sth = $dbh->prepare($sql);
$sth->execute();
($dl_id)=$sth->fetchrow_array();
$sth->finish();
$sql="select brand_id from schedule_info where slot_type='3' and third_party_id=$id and client_id=$dl_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($dl_bid)=$sth->fetchrow_array();
$sth->finish();

my $old_id= $query->param('old_id');
if ($old_id eq "")
{
	$old_id=0;
}
my $tid0= $query->param('tid0');
my $tid1= $query->param('tid1');
my $am_pm= $query->param('am_pm');
my $cname = $query->param('campaign_name');
my $aid = $query->param('advertiser_id');
$catid = $query->param('catid');
if ($am_pm eq "PM")
{
	my $t = $tid1 + 12;
	if ($t >= 24)
	{
		$t = "12";
	}
	($mon,$day,$year) = split('\/',$tid0);
	$sdate = $year ."-" . $mon . "-" . $day . " " . $t . ":00";
}
else
{
	($mon,$day,$year) = split('\/',$tid0);
	if ($tid1 == 12)
	{
		$sdate = $year . "-" . $mon . "-" . $day . " " . "00:00";
	}
	else
	{
		$sdate = $year . "-" . $mon . "-" . $day . " " . $tid1 . ":00";
	}
}
#
#	Get information about the mailer
#
$sql = "select mailer_name,num_subject,num_from,num_creative,name_replace,loc_replace,date_replace,email_replace,cid_replace,emailid_replace,capture_replace,ip_replace,include_unsubscribe,suppression_path,creative_path,mailer_ftp,ftp_username,ftp_password,include_images from third_party_defaults where third_party_id=$id";
$sth = $dbh->prepare($sql);
$sth->execute();
($mailer_name,$num_subject,$num_from,$num_creative,$rname,$rloc,$rdate,$remail,$rcid,$remailid,$rcdate,$rip,$unsub_flag,$suppression_path,$creative_path,$ftp_ip,$ftp_username,$ftp_password,$include_images)=$sth->fetchrow_array();
$sth->finish();
#
$sql = "select advertiser_name,category_id,unsub_link,unsub_image,vendor_supp_list_id from advertiser_info where advertiser_id=$aid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($aname,$catid,$unsub_url,$unsub_img,$vid)=$sth->fetchrow_array();
$sth->finish();
$sql = "select company from user where user_id=$dl_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($network_name)=$sth->fetchrow_array();
$sth->finish();
#
# Create Directory for stuff
#
my $dir1 = $mailer_name . $network_name . $aname . $tid0 . $tid1 . $am_pm;
$dir1 =~ s/ //g;
$dir1 =~ s/\(//g;
$dir1 =~ s/\)//g;
$dir1 =~ s/\$//g;
$dir1 =~ s/\//-/g;
$dir1 =~ s/\://g;
$dir1 =~ s/\$//g;
$dir1 =~ s/\.//g;
$dir1 =~ s/\!//g;
$dir1 =~ s/\?/-/g;
$dir1 =~ s/\&/-/g;
$dir2 = "/data3/3rdparty/" . $dir1;
mkdir $dir2;
if ($old_id > 0)
{
	$sql="select client_id,profile_id,brand_id from schedule_info where slot_type='3' and third_party_id=$id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($client_id,$profile_id,$bid)=$sth1->fetchrow_array())
	{
		$sql="update 3rdparty_campaign set third_party_id=$id, client_id=$client_id, brand_id=$bid,catid=$catid, advertiser_id=$aid, deploy_name='$cname', scheduled_datetime='$sdate' where id=$old_id";
		$new_camp_id = $old_id;
		$rows=$dbh->do($sql);
	}
	$sth1->finish();
}
else
{
	$old_id = 0;
	open(OUTFILE,">$dir2/links.csv");
	print OUTFILE "Network,Brand,Profile,Advertiser,URL,Advertiser Unsub,Mailer Unsub,Privacy\n";
	$sql="select client_id,profile_id,brand_id from schedule_info where slot_type='3' and third_party_id=$id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($client_id,$profile_id,$bid)=$sth1->fetchrow_array())
	{
    	$sql = "insert into campaign(campaign_name,user_id,status,created_datetime,advertiser_id,creative1_id,brand_id,scheduled_datetime,sent_datetime,profile_id) values('$cname',1,'C',now(),$aid,0,$bid,'$sdate','$sdate',$profile_id)";
		$rows=$dbh->do($sql);
		$sql="select max(campaign_id) from campaign where campaign_name='$cname' and status='C' and scheduled_datetime='$sdate'"; 
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($temp_cid)=$sth->fetchrow_array();
		$sth->finish();
		$sql="insert into 3rdparty_campaign(third_party_id,client_id,brand_id,catid,advertiser_id,deploy_name,scheduled_datetime,campaign_id) values($id,$client_id,$bid,$catid,$aid,'$cname','$sdate',$temp_cid)";
		$rows=$dbh->do($sql);
		$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($temp_cid,'$sdate',$client_id)";
		$rows=$dbh->do($sql);
		#
		# Builds the links.csv file
		#
		my $temp_bid;
		my $adv_unsub_link;
		my $client_unsub_link;
		my $privacy_unsub_link;
		my $company_name;
		my $adv_link;
		my $profile_name;
		$sql="select company from user where user_id=$client_id"; 
		$sth = $dbh->prepare($sql) ;
		$sth->execute();
		($company_name) = $sth->fetchrow_array();
		$sth->finish();
		$sql="select profile_name from list_profile where profile_id=$profile_id"; 
		$sth = $dbh->prepare($sql) ;
		$sth->execute();
		($profile_name) = $sth->fetchrow_array();
		$sth->finish();
		$sql="select brand_name from client_brand_info where brand_id=$bid"; 
	    $sth = $dbh->prepare($sql) ;
	    $sth->execute();
	    ($bname) = $sth->fetchrow_array();
	    $sth->finish();
		$sql="select url,url_id from brand_url_info where brand_id=$bid and url_type='O'";
		$sth = $dbh->prepare($sql) ;
		$sth->execute();
		($redir_domain,$url_id) = $sth->fetchrow_array();
		$sth->finish();
		#
		$sql="select brandsubdomain_info.subdomain_id,subdomain_name from category_brand_info,brandsubdomain_info where category_id=$catid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=$bid";
	    $sth = $dbh->prepare($sql);
	    $sth->execute();
	    ($sid,$subdomain_name) = $sth->fetchrow_array();
		$sth->finish();
		$subdomain_name =~ s/{{BRAND}}/$bname/g;
		($footer_subdomain,$rest_str) = split '\.',$subdomain_name;
		#
		$sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=$client_id and daily_deal='N'";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($link_id) = $sth->fetchrow_array();
		$sth->finish();
		my $tlink_id;
        $sql = "select link_id from links where refurl='$unsub_url'";
        $sth = $dbh->prepare($sql);
        $sth->execute();
        ($tlink_id) = $sth->fetchrow_array();
        $sth->finish();
		$adv_link = "http:\/\/$redir_domain\/cgi-bin\/redir1.cgi?eid=$remailid&cid=${temp_cid}&em=$remail&id=$link_id&f=&s=&c=";
		$adv_unsub_link="http://${footer_subdomain}.${redir_domain}/cgi-bin/redir1.cgi?eid=$remailid&cid=0&em=$remail&id=$tlink_id"; 
		$client_unsub_link="http://${footer_subdomain}.${redir_domain}/cgi-bin/redir1.cgi?eid=$remailid&cid=${temp_cid}&em=$remail&id=42&nid=$client_id";
		$privacy_unsub_link="http://${footer_subdomain}.${redir_domain}/cgi-bin/redir1.cgi?eid=$remailid&cid=0&em=$remail&id=51&nid=$client_id";
		print OUTFILE "$company_name,$bname,$profile_name,$aname,$adv_link,$adv_unsub_link,$client_unsub_link,$privacy_unsub_link\n";
	}
	$sth1->finish();
	close(OUTFILE);
}
if ($old_id == 0)
{
	$sql="select max(id) from 3rdparty_campaign where deploy_name='$cname' and scheduled_datetime='$sdate'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($new_camp_id)=$sth->fetchrow_array();
	$sth->finish();
}
if ($old_id > 0)
{
	$sql="select campaign_id from 3rdparty_campaign where id=$old_id"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($temp_cid)=$sth->fetchrow_array();
	$sth->finish();
	$sql="update campaign set scheduled_datetime='$sdate',campaign_name='$cname',advertiser_id=$aid,sent_datetime='$sdate' where campaign_id=$temp_cid"; 
	$rows=$dbh->do($sql);
}
#
$sql = "select list_name from vendor_supp_list_info where list_id=$vid";
$sth = $dbh->prepare($sql);
$sth->execute();
($supp_name)=$sth->fetchrow_array();
$sth->finish();
$supp_name =~ s/ //g;
$supp_name =~ s/\)//g;
$supp_name =~ s/\(//g;
$supp_name =~ s/\&//g;
$supp_name =~ s/\://g;
$supp_name =~ s/\$//g;
$supp_name =~ s/\.//g;
$supp_name =~ s/\!//g;
#
$sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=$dl_id and daily_deal='N'";
$sth = $dbh->prepare($sql);
$sth->execute();
($link_id) = $sth->fetchrow_array();
$sth->finish();
#
$sql = "select brand_name,header_text,footer_text from client_brand_info where brand_id=$dl_bid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($bname,$header_text,$footer_text)=$sth->fetchrow_array();
$sth->finish();
#
	$sql="select url,url_id from brand_url_info where brand_id=$dl_bid and url_type='O'"; 
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($redir_domain,$url_id) = $sth1->fetchrow_array();
	$sth1->finish();
	$sql="select url from brand_url_info where brand_id=$dl_bid and url_type='OI'"; 
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($img_domain) = $sth1->fetchrow_array();
	$sth1->finish();
    $sql="select brandsubdomain_info.subdomain_id,subdomain_name from category_brand_info,brandsubdomain_info where category_id=$catid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=$dl_bid";
    $sth1 = $dbh->prepare($sql);
    $sth1->execute();
    ($sid,$subdomain_name) = $sth1->fetchrow_array();
    $sth1->finish();
    $subdomain_name =~ s/{{BRAND}}/$bname/g;
    ($footer_subdomain,$rest_str) = split '\.',$subdomain_name;
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
						<td align="left"><b><font face="Arial" size="2">&nbsp;Deployed Creative</font></b></td>
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
rif" color="#509c10" size="3"><b>$cname</b></font><table border="0" width="100%" id="table9">
							<tr>
								<td><b>
													Mailer</b></td>
								<td>
													<b>$mailer_name</b></td>
							</tr>
							<tr>
								<td><b>
													Advertiser</b></td>
								<td>
													<b>$aname</b></td>
							</tr>
							<tr>
								<td><b>Network:</b></td>
								<td>
													<b>
													$network_name</b></td>
							</tr>
							<tr>
								<td><b>
													Brand:</b></td>
								<td>
													<b>$bname</b></td>
							</tr>
							<tr>
								<td><b>
													Scheduled:</b></td>
								<td>
													<b>$tid0 $tid1${am_pm}</b></td>
							</tr>
							<tr>
								<td><b>Download:</b></td>
								<td><b><a href="/downloads/$dir1.zip">
								$dir1.zip</a></b></td>
							</tr>
							<tr>
								<td><b>Suppression:</b></td>
								<td><a href="/supp/${supp_name}.zip"><b>
								$supp_name</b></a><b></b></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<img height="3" src="/images/spacer.gif"></td>
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
$sql="select advertiser_subject,sum(open_cnt)/sum(sent_cnt) from advertiser_subject,subject_log where advertiser_subject.subject_id=subject_log.subject_id and campaign_id in (select campaign_id from campaign where advertiser_id=$aid and deleted_date is null) and advertiser_subject.status='A' and advertiser_subject.advertiser_id=$aid group by advertiser_subject order by 2 desc limit $num_subject";
$sth = $dbh->prepare($sql);
$sth->execute();
open(OUTFILE,">$dir2/subject.txt");
while (($subject_str,$temp_val)=$sth->fetchrow_array())
{
	$subject_str =~ s/{{NAME}}/$rname/g;
	$subject_str =~ s/{{LOC}}/$rloc/g;
	print "<br>$subject_str\n";
	print OUTFILE "$subject_str\n";
}
close (OUTFILE);
$sth->finish();
open(OUTFILE,">$dir2/suppression.txt");
print OUTFILE "${supp_name}.zip\n";
close (OUTFILE);
print<<"end_of_html";
					<br><br>
					<b><u>From Line(s):</u></b>
end_of_html
$sql="select advertiser_from,sum(open_cnt)/sum(sent_cnt) from advertiser_from,from_log where advertiser_from.from_id=from_log.from_id and campaign_id in (select campaign_id from campaign where advertiser_id=$aid and deleted_date is null) and advertiser_from.advertiser_id=$aid group by advertiser_from order by 2 desc limit $num_from";
$sth = $dbh->prepare($sql);
$sth->execute();
open(OUTFILE,">$dir2/from.txt");
while (($subject_str,$temp_val)=$sth->fetchrow_array())
{
	$subject_str =~ s/{{FOOTER_SUBDOMAIN}}/$footer_subdomain/g;
	print "<br>$subject_str\n";
	print OUTFILE "$subject_str\n";
}
close (OUTFILE);
$sth->finish();
print<<"end_of_html";
					<br><br>
end_of_html
$sql="select creative_log.creative_id,creative.creative_name,((sum(click_cnt)/sum(sent_cnt))*100000) from creative_log,creative where creative_log.creative_id=creative.creative_id and trigger_flag='N' and creative.status='A' and advertiser_id=$aid group by 1,2 order by 3 desc limit $num_creative";
$sth = $dbh->prepare($sql);
open(LOG,">/tmp/k.");
print LOG "<$sql>\n";
close(LOG);
$sth->execute();
my ($copen,$cclick,$cindex);
$camp_cnt = 1;
while (($cid,$creative_name,$temp_val)=$sth->fetchrow_array())
{
    $sql = "select (sum(open_cnt)/sum(sent_cnt)*100),(sum(click_cnt)/sum(open_cnt)*100),((sum(click_cnt)/sum(sent_cnt))*100000) from creative_log where creative_id=$cid";
    $sth1 = $dbh->prepare($sql) ;
    $sth1->execute();
    ($copen,$cclick,$cindex) = $sth1->fetchrow_array();
    $sth1->finish();
#
	$sql="select approved_flag,original_flag,html_code from creative where creative_id=$cid";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($aflag,$oflag,$html_code) = $sth1->fetchrow_array();
	$sth1->finish();

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
	print "<b><u>Creative $camp_cnt: $creative_name ($copen% Open - $cclick% Click - $temp_val Index - $temp_str</u></b><br>\n";
	#
	# Make changes to html_code
	#
    $html_code =~ s\<BODY\<body\;
    my $pos1 = index($html_code, "<body");
    my $pos2 = index($html_code, ">",$pos1);
    substr($html_code,$pos1,$pos2-$pos1+1) = "<body>";
    $tracking_str = "<IMG SRC=\"http://{{DOMAIN}}/cgi-bin/open_email1.cgi?id={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;f={{FID}}&amp;s={{SID}}&amp;c={{CRID}}&amp;\" border=0 height=1 width=1 alt=\"open\">";
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
    	if ($unsub_img ne "")
        {
        	my $tlink_id;
            $sql = "select link_id from links where refurl='$unsub_url'";
            $sth1 = $dbh->prepare($sql);
            $sth1->execute();
            ($tlink_id) = $sth1->fetchrow_array();
            $sth1->finish();
            $temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$tlink_id\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
         }
    }
	if ($unsub_flag eq "Y")
	{
    	$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?cid=0&amp;eid={{EMAIL_USER_ID}}&amp;id=51&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
	}
	else
	{
		$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str\g;	
	}

#
	my $footer_str = $dl_bid . "_" . $sid . "_" . $url_id;
	$html_code =~ s/{{HEADER_TEXT}}/$header_text/g;
	$html_code =~ s/{{FOOTER_TEXT}}/$footer_text/g;
    $html_code =~ s/{{URL}}/http:\/\/$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid={{CID}}&em={{EMAIL_ADDR}}&id=$link_id&f=&s=&c=$cid/g;
    $html_code =~ s/{{UNSUB_LINK}}/http:\/\/{{FOOTER_SUBDOMAIN}}.$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42/g;
	$html_code =~ s/{{FOOTER_SUBDOMAIN}}/$footer_subdomain/g;
	$html_code =~ s/{{FOOTER_STR}}/$footer_str/g;
	$html_code =~ s/{{NAME}}/$rname/g;
	$html_code =~ s/{{EMAIL_ADDR}}/$remail/g;
	$html_code =~ s/{{EMAIL_USER_ID}}/$remailid/g;
	$html_code =~ s/{{LOC}}/$rloc/g;
	$html_code =~ s/{{DATE}}/$rdate/g;
	$html_code =~ s/{{CID}}/$temp_cid/g;
	$html_code =~ s/{{CRID}}/$cid/g;
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
	$html_code =~ s/{{IMG_DOMAIN}}/$img_domain/g;
	$html_code =~ s/{{DOMAIN}}/$redir_domain/g;
	print "<b><textarea name=html_code$camp_cnt rows=15 cols=100>$html_code</textarea><br>\n";
print<<"end_of_html";
							<a href="/cgi-bin/camp_preview.cgi?campaign_id=$cid&format=H" target=_blank>
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

my @args = ("/var/www/html/newcgi-bin/stuff_3rdparty.sh $dir1");
system(@args) == 0 or die "system @args failed: $?";
#
# Check to see if need to copy creative and advertiser suppression file
#
if ($suppression_path ne "")
{
	my $tfilename=${supp_name} . ".zip";
	my $mesg=ftp_file($ftp_ip,$ftp_username,$ftp_password,$suppression_path,"/data3/supp/",$tfilename);
	if ($mesg ne "")
	{
		print "<script language=JavaScript>alert('$mesg');</script>\n";
	}
}
if ($creative_path ne "")
{
	my $tfilename=$dir1 . ".zip";
	my $mesg=ftp_file($ftp_ip,$ftp_username,$ftp_password,$creative_path,"/data3/3rdparty/",$tfilename);
	if ($mesg ne "")
	{
		print "<script language=JavaScript>alert('$mesg');</script>\n";
	}
}
print<<"end_of_html";
					</b><br>
					&nbsp;<table id="table8" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/mainmenu.cgi">
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							&nbsp;</td>
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

</body>

</html>
end_of_html
exit(0);

sub ftp_file
{
	my ($ftp_ip,$ftp_username,$ftp_password,$remote_path,$mydir,$tfilename) = @_;
	my $filename;
	my $to_dir;
	my $mesg="";

	$filename=$mydir . $tfilename;
	my $ftp = Net::FTP->new("$ftp_ip", Timeout => 120, Debug => 0, Passive => 0) or $mesg="Cannot connect to $ftp_ip: $@";
	if ($ftp)
	{
	    $ftp->login($ftp_username,$ftp_password) or $mesg="Cannot login $ftp_ip"; 
	    $ftp->cwd($remote_path);
	    $ftp->binary();
	    $ftp->put($filename) or $mesg="put failed for $filename to $remote_path on $ftp_ip"; 
	}
	$ftp->quit;
	return $mesg;
}

sub cb 
{
     my($tag, $url1, $url2, %links) = @_;
	my $query1;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $ext;
	my $scheme;
	my $auth;
	my $path;
	my $query1;
	my $frag;
	my $suffix;
	my $name;

     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background"))
     {
        $_ = $url2;
        if ((/DOMAIN/) || (/IMG_DOMAIN/))
        {
          	$url2=~s/{{DOMAIN}}/affiliateimages.com/g;  
          	$url2=~s/{{IMG_DOMAIN}}/affiliateimages.com/g;  
        }
        #
        # Get directory and filename
        #
        ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
        ($name,$frag,$suffix) = fileparse($path);
		if ($name ne "open_email1\.cgi")
		{
	        my $repl_url = $scheme . "://" . $auth . $frag;
			my $time_str = time();
	        if ($query1 ne "")
	        {
	        	$repl_url = $repl_url . $name . "?" . $query1;
	        }
	        else
	        {
	        	my $temp_str;
	            ($temp_str,$ext) = split('\.',$name);
	        }
	        my $curl = WWW::Curl::easy->new();
	        $curl->setopt(CURLOPT_NOPROGRESS, 1);
	        $curl->setopt(CURLOPT_MUTE, 0);
	        $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
	        $curl->setopt(CURLOPT_TIMEOUT, 30);
	        open HEAD, ">head.out";
	        $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
	        open BODY, "> $dir2/images/$name";
	        $curl->setopt(CURLOPT_FILE,*BODY);
	        $curl->setopt(CURLOPT_URL, $url2);
	        my $retcode=$curl->perform();
	        if ($retcode == 0)
	        {
	        }
	        else
	        {
	        }
	        close HEAD;
		}
	}
}
