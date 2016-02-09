#!/usr/bin/perl

# *****************************************************************************************
# 3rdparty_show_camp.cgi
#
# this page displays the 3rd party campaign page 
#
# History
# Jim Sobeck, 12/22/05, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $url_id;
my $cname;
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
my $physical_addr;
my $temp_val;
my $num_from;
my $num_creative;
my $subject_str;
my $cid;
my $creative_name;
my $oflag;
my $html_code;
my $aflag;
my $rname;
my $rloc;
my $rdate;
my $remail;
my $remailid;
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
my $aid;
my $bid;
my $temp_cid;
my $profile_id;
my $id;
my $tid0;
my $tid1;
my $am_pm;
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
my $camp_id= $query->param('campaign_id');

$sql="select third_party_id,campaign_id,advertiser_id,brand_id,deploy_name,date_format(scheduled_datetime,'%m/%d/%Y'),date_format(scheduled_datetime,'%h'),date_format(scheduled_datetime,'%p'),client_id from 3rdparty_campaign where id=$camp_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($id,$temp_cid,$aid,$bid,$cname,$tid0,$tid1,$am_pm,$client_id)=$sth->fetchrow_array();
$sth->finish();
#
#	Get information about the mailer
#
$sql = " select mailer_name from third_party_defaults where third_party_id=$id";
$sth = $dbh->prepare($sql);
$sth->execute();
($mailer_name)=$sth->fetchrow_array();
$sth->finish();
#
$sql = "select advertiser_name,category_id,unsub_link,unsub_image,vendor_supp_list_id from advertiser_info where advertiser_id=$aid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($aname,$catid,$unsub_url,$unsub_img,$vid)=$sth->fetchrow_array();
$sth->finish();
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
$sql = "select company from user where user_id=$client_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($network_name)=$sth->fetchrow_array();
$sth->finish();
#
# Create Directory for stuff
#
my $t_tid1=$tid1;
$t_tid1=~ s/^0//;
my $dir1 = $mailer_name . $network_name . $aname . $tid0 . $t_tid1 . $am_pm;
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
my $dir2 = "/data3/3rdparty/" . $dir1;
#mkdir $dir2;
#
$sql = "select brand_name,header_text,footer_text from client_brand_info where brand_id=$bid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($bname,$header_text,$footer_text)=$sth->fetchrow_array();
$sth->finish();
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
													<b>$tid0 $tid1 $am_pm</b></td>
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
							<tr>
								<td><b>Advertiser:</b></td>
								<td><a href="/cgi-bin/rep_adv_subject_creative.cgi?aid=$aid" target=_blank><b>Stats</b></a><b></b></td>
							</tr>
							<tr>
								<td><b>Advertiser:</b></td>
								<td><a href="/cgi-bin/view_thumbnails.cgi?aid=$aid" target=_blank><b>Thumbnails</b></a><b></b></td>
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
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</body>

</html>
end_of_html
