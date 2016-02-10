#!/usr/bin/perl

# *****************************************************************************************
# client_brand_upd.cgi
#
# this page updates information for a client brand 
#
# History
# JES	11/01/06	Added logic for Newsletter Brands
# JES	01/04/07	Added logic for Newsletter VSGs
# JES	01/16/07	Added logic for creative selection and Exclude Subdomain
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use App::WebAutomation::ImageHoster;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $aid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $sth1;
my $upload_dir = "/var/www/util/creative";

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $data={};
$data->{'imageCollectionID'}="000000000000001";
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.key";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);
my $bid;
my $old_bname;

my $cid = $query->param('cid');
my $bid = $query->param('bid');
my $rdns_urls= $query->param('rdns_urls');
$rdns_urls=~ s/[\n\r\f\t]/\|/g ;
$rdns_urls=~ s/\|{2,999}/\|/g ;
my @rdns= split '\|', $rdns_urls;
my $newsletter_font = $query->param('newsletter_font');
my $nl_id = $query->param('nl_id');
my $client_type= $query->param('client_type');
if ($nl_id eq "")
{
	$nl_id=0;
}
my $updateall = $query->param('updateall');
if ($updateall eq "")
{
	$updateall="N";
}
my $bname = $query->param('brandname');
$bname=~tr/[0-9][a-z][A-Z]\-_/ /c;
$bname=~s/ //g;
my $bfname = $query->param('brandfname');
my $from_address = $query->param('from_address');
my $display_name = $query->param('display_name');
my $subject= $query->param('subject');
$subject=~s/'/''/g;
my $ons1 = $query->param('ons1');
my $ons2 = $query->param('ons2');
my $yns1 = $query->param('yns1');
my $yns2 = $query->param('yns2');
my $cns1 = $query->param('cns1');
my $cns2 = $query->param('cns2');
my $o_host = $query->param('o_host');
my $dns_host = $query->param('dns_host');
my $clean_host = $query->param('clean_host');
my $y_host = $query->param('y_host');
my $oip = $query->param('oip');
my $yip = $query->param('yip');
my $addr1 = $query->param('addr1');
my $addr2 = $query->param('addr2');
my $phone = $query->param('phone');
my $whois_email = $query->param('whois_email');
my $abuse_email = $query->param('abuse_email');
my $personal_email = $query->param('personal_email');
my $brand_type = $query->param('brand_type');
my $third_party_id = $query->param('third_party_id');
my $footer_text = $query->param('footer_text');
my $font_type=$query->param('font_type');
my $font_size=$query->param('font_size');
my $align=$query->param('align');
my $tag=$query->param('tag');
my $upd_tag=$query->param('upd_tag');
my $c_vsg=$query->param('c_vsg');
my $uc_vsg=$query->param('uc_vsg');
$footer_text =~ s/'/''/g;
my $header_text = $query->param('header_text');
$header_text =~ s/'/''/g;
my $notes= $query->param('notes');
$notes=~ s/'/''/g;

my $replace_domain = $query->param('enable_replace');
my $brand_priority = $query->param('brand_priority');
my $purpose = $query->param('purpose');
my $generateSpf = $query->param('generateSpf');
if ($purpose eq "")
{
	$purpose="Normal";
}
if ($generateSpf eq "")
{
	$generateSpf="Y";
}
my $num_domains_rotate= $query->param('num_domains_rotate');
if ($num_domains_rotate eq "")
{
	$num_domains_rotate=1;
}

my $aol_comments = $query->param('aol_comments');
my $aolw_flag = $query->param('aolw_flag');
if ($aolw_flag eq "")
{
	$aolw_flag = "N";
}
$aol_comments =~ s/'/''/g;
my $vid = $query->param('vid');
if ($vid eq "")
{
	$vid =0;
}
my $color_id = $query->param('color_id');
if ($color_id eq "")
{
	$color_id=0;
}
my $bg_color_id = $query->param('bg_color_id');
my $font_id = $query->param('font_id');
if ($font_id eq "")
{
	$font_id=0;
}
my $creative_selection=$query->param('creative_selection');
my $exclude_subdomain=$query->param('exclude_subdomain');
my @template_id=$query->param('template_id');
my $include_privacy=$query->param('include_privacy');
my $exclude_thirdparty=$query->param('exclude_thirdparty');
my $exclude_wiki=$query->param('exclude_wiki');
my $ignore_mta_template_settings=$query->param('ignore_mta_template_settings');
my $doubleConfirmDisclaimer = $query->param('disclaimer');
my $doubleConfirmContent = $query->param('content');

my $BASE_DIR;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
my $header_img= $query->param('newsletter_header');
$header_img =~ s/.*[\/\\](.*)/$1/;
if ($header_img ne "")
{
	my $upload_filehandle = $query->upload("newsletter_header");
	my $params={};
	$params->{'image'}=$upload_filehandle;
	my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
	$header_img=$newImageName;
}
my $footer_img= $query->param('newsletter_footer');
$footer_img =~ s/.*[\/\\](.*)/$1/;
if ($footer_img ne "")
{
	my $upload_filehandle = $query->upload("newsletter_footer");
	my $params={};
	$params->{'image'}=$upload_filehandle;
	my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
	$footer_img=$newImageName;
}

my $unsub_img= $query->param('unsub_img');
$unsub_img =~ s/.*[\/\\](.*)/$1/;
if ($unsub_img ne "")
{
	my $upload_filehandle = $query->upload("unsub_img");
	my $params={};
	$params->{'image'}=$upload_filehandle;
	my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
	$unsub_img = $newImageName;
}
my $privacy_img= $query->param('privacy_img');
$privacy_img =~ s/.*[\/\\](.*)/$1/;
if ($privacy_img ne "")
{
	my $upload_filehandle = $query->upload("privacy_img");
	my $params={};
	$params->{'image'}=$upload_filehandle;
	my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
	$privacy_img = $newImageName;
}
#
if ($brand_type eq "Newsletter")
{
	$sql="select brand_name from client_brand_info where brand_id=$bid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($old_bname)=$sth->fetchrow_array();
	$sth->finish();
}
if ($bid > 0)
{
	
	#update double confirm disclaimer
	my $updateDisclaimerSql = qq|update DoubleConfirmDisclaimer set disclaimer = "$doubleConfirmDisclaimer", content = "$doubleConfirmContent" where brandID = $bid|;
	my $rows = $dbhu->do($updateDisclaimerSql);
	
	#insert double confirm disclaimer
	if($rows == 0){
		my $insertDisclaimerSql = qq|insert into DoubleConfirmDisclaimer values ($bid,"$doubleConfirmContent","$doubleConfirmDisclaimer")|;
		$dbhu->do($insertDisclaimerSql);
	} #end if
	
	$sql = "update client_brand_info set brand_name='$bname', brand_fullname='$bfname', others_ns1='$ons1',others_ns2='$ons2',
	yahoo_ns1='$yns1',yahoo_ns2='$yns2',others_ip='$oip',yahoo_ip='$yip',mailing_addr1='$addr1',mailing_addr2='$addr2', 
	phone='$phone',whois_email='$whois_email',abuse_email='$abuse_email',personal_email='$personal_email',dns_host='$dns_host', 
	clean_host='$clean_host', others_host='$o_host',yahoo_host='$y_host',footer_text='$footer_text',header_text='$header_text',
	footer_variation=$vid,footer_color_id=$color_id,footer_bg_color_id=$bg_color_id,cleanser_ns1='$cns1',cleanser_ns2='$cns2',
	footer_font_id=$font_id,notes='$notes',aolw_flag='$aolw_flag',aol_comments='$aol_comments',brand_type='$brand_type',
	third_party_id=$third_party_id,font_type='$font_type',font_size=$font_size,align='$align',newsletter_font='$newsletter_font',
	nl_id=$nl_id, tag='$upd_tag',c_vsgid='$c_vsg',uc_vsgid='$uc_vsg',creative_selection=$creative_selection,
	exclude_subdomain='$exclude_subdomain',exclude_thirdparty='$exclude_thirdparty',
	include_privacy='$include_privacy',from_address='$from_address', display_name='$display_name',exclude_wiki='$exclude_wiki', 
	replace_domain = $replace_domain, brand_priority = '$brand_priority',num_domains_rotate=$num_domains_rotate,purpose='$purpose',ignore_mta_template_settings='$ignore_mta_template_settings',subject='$subject',generateSpf='$generateSpf' where brand_id=$bid";
	open(LOG,">/tmp/j.");
	print LOG "<$sql>\n";
	close(LOG);
	my $rows=$dbhu->do($sql);
    $sql="delete from brand_rdns_info where brand_id=$bid";
	$rows=$dbhu->do($sql);
    $sql="delete from brand_template_join where brand_id=$bid";
	$rows=$dbhu->do($sql);
	my $i=0;
	while ($i <= $#template_id)
	{
		$sql="insert into brand_template_join(brand_id,template_id) values($bid,$template_id[$i])";
		$rows=$dbhu->do($sql);
		$i++;
	}
my $rurl;
foreach $rurl (@rdns)
{
    $sql="insert into brand_rdns_info(brand_id,rdns_domain) values($bid,'$rurl')";
    $rows=$dbhu->do($sql);
}
	if (($brand_type eq "Newsletter") && ($updateall eq "Y"))
	{
		$sql = "update client_brand_info set brand_name='$bname', brand_fullname='$bfname', 
		others_ns1='$ons1',others_ns2='$ons2',yahoo_ns1='$yns1',yahoo_ns2='$yns2',others_ip='$oip',yahoo_ip='$yip',
		mailing_addr1='$addr1',mailing_addr2='$addr2', phone='$phone',whois_email='$whois_email',
		abuse_email='$abuse_email',personal_email='$personal_email',dns_host='$dns_host', 
		clean_host='$clean_host', others_host='$o_host',yahoo_host='$y_host',
		footer_text='$footer_text',header_text='$header_text',footer_variation=$vid,footer_color_id=$color_id,
		footer_bg_color_id=$bg_color_id,cleanser_ns1='$cns1',cleanser_ns2='$cns2',footer_font_id=$font_id,
		notes='$notes',aolw_flag='$aolw_flag',aol_comments='$aol_comments',brand_type='$brand_type',
		third_party_id=$third_party_id,font_type='$font_type',font_size=$font_size,align='$align',
		newsletter_font='$newsletter_font',nl_id=$nl_id,tag='$upd_tag',c_vsgid='$c_vsg',
		uc_vsgid='$uc_vsg',creative_selection=$creative_selection,exclude_subdomain='$exclude_subdomain',
		exclude_thirdparty='$exclude_thirdparty',include_privacy='$include_privacy',
		from_address='$from_address', display_name='$display_name',exclude_wiki='$exclude_wiki', replace_domain = $replace_domain, brand_priority = '$brand_priority',num_domains_rotate=$num_domains_rotate,purpose='$purpose',ignore_mta_template_settings='$ignore_mta_template_settings',subject='$subject',generateSpf='$generateSpf' where brand_name='$old_bname' and brand_type='Newsletter'";
		my $rows=$dbhu->do($sql);
	}
}
else
{
	#insert double confirm disclaimer
	my $insertDisclaimerSql = qq|insert into DoubleConfirmDisclaimer values ($bid, "$doubleConfirmContent", "$doubleConfirmDisclaimer")|;
	$dbhu->do($insertDisclaimerSql);
	
	$sql = "insert into client_brand_info(client_id,brand_name,brand_fullname,others_ns1,others_ns2,yahoo_ns1,
	yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,phone,whois_email,abuse_email,personal_email,dns_host, 
	clean_host, others_host,yahoo_host,footer_text,header_text,footer_variation,footer_color_id,footer_bg_color_id,
	cleanser_ns1,cleanser_ns2,footer_font_id,notes,aolw_flag,aol_comments,brand_type,third_party_id,font_type,
	font_size,align,nl_id,c_vsgid,uc_vsgid,creative_selection,exclude_subdomain,exclude_thirdparty,
	include_privacry,from_address,exclude_wiki, replace_domain, brand_priority,num_domains_rotate,client_type,purpose,ignore_mta_template_settings,subject,generateSpf) values($cid,'$bname','$bfname','$ons1','$ons2',
	'$yns1','$yns2','$oip','$yip','$addr1','$addr2','$phone','$whois_email','$abuse_email','$personal_email','$dns_host', 
	'$clean_host', '$o_host','$y_host','$footer_text','$header_text',$vid,$color_id,$bg_color_id,'$cns1','$cns2',$font_id,
	'$notes','$aolw_flag','$aol_comments','$brand_type',$third_party_id,'$font_type',$font_size,'$align',$nl_id,'$c_vsg',
	'$uc_vsg',$creative_selection,'$exclude_subdomain','$exclude_thirdparty','$include_privacy','$from_address',
	'$display_name','$exclude_wiki', $replace_domain, '$brand_priority',$num_domains_rotate,'$client_type','$purpose','$ignore_mta_template_settings','$subject','$generateSpf')";
	my $rows=$dbhu->do($sql);
	$sql="select max(brand_id) from client_brand_info where brand_name='$bname' and client_id=$cid";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($bid)=$sth->fetchrow_array();
	$sth->finish();
	my $i=0;
	while ($i <= $#template_id)
	{
		$sql="insert into brand_template_join(brand_id,template_id) values($bid,$template_id[$i])";
		$rows=$dbhu->do($sql);
		$i++;
	}
my $rurl;
foreach $rurl (@rdns)
{
    $sql="insert into brand_rdns_info(brand_id,rdns_domain) values($bid,'$rurl')";
    $rows=$dbhu->do($sql);
}
	#
	# JES - 09/19 - Add copying of category information
	#
	my $from_bid;
	$sql="select max(brand_id) from client_brand_info where brand_name='DirectEmailSolution'";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($from_bid)=$sth->fetchrow_array();
	$sth->finish();
	$sql = "insert into category_brand_info(brand_id,subdomain_id) select $bid,subdomain_id from category_brand_info where brand_id=$from_bid";
	my $rows=$dbhu->do($sql);
	#
	# JES - Add newsletter logic
	#
	if ($brand_type eq "Newsletter")
	{
		$old_bname=$bname;
		if ($client_type eq "ALL")
		{
			$sql="select user_id from user where status='A'";
		}
		else
		{
			$sql="select distinct user_id from user where status='A' and (client_type='$client_type' or user_id=64)";
		}
		my $s1=$dbhq->prepare($sql);
		$s1->execute();
		my $temp_id;
		while (($temp_id) = $s1->fetchrow_array())
		{
			$sql = "insert into client_brand_info(client_id,brand_name,brand_fullname,others_ns1,others_ns2,
			yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,phone,whois_email,abuse_email,
			personal_email,dns_host, clean_host, others_host,yahoo_host,footer_text,header_text,footer_variation,footer_color_id,
			footer_bg_color_id,cleanser_ns1,cleanser_ns2,footer_font_id,notes,aolw_flag,aol_comments,brand_type,third_party_id,
			font_type,font_size,align,nl_id,from_address, display_name, replace_domain, brand_priority,num_domains_rotate,client_type,purpose,ignore_mta_template_settings,subject,generateSpf) values($temp_id,'$bname','$bfname',
			'$ons1','$ons2','$yns1','$yns2','$oip','$yip','$addr1','$addr2','$phone','$whois_email','$abuse_email',
			'$personal_email','$dns_host', '$clean_host', '$o_host','$y_host','$footer_text','$header_text',$vid,
			$color_id,$bg_color_id,'$cns1','$cns2',$font_id,'$notes','$aolw_flag','$aol_comments','$brand_type',
			$third_party_id,'$font_type',$font_size,'$align',$nl_id,'$from_address', '$display_name', $replace_domain, '$brand_priority',$num_domains_rotate,'$client_type','$purpose','$ignore_mta_template_settings','$subject','$generateSpf')";
			my $rows=$dbhu->do($sql);
			$sql="select max(brand_id) from client_brand_info where brand_name='$bname' and client_id=$temp_id";
			$sth=$dbhq->prepare($sql);
			$sth->execute();
			($bid)=$sth->fetchrow_array();
			$sth->finish();
			my $from_bid;
			$sql="select max(brand_id) from client_brand_info where brand_name='DirectEmailSolution'";
			$sth=$dbhq->prepare($sql);
			$sth->execute();
			($from_bid)=$sth->fetchrow_array();
			$sth->finish();
			$sql = "insert into category_brand_info(brand_id,subdomain_id) select $bid,subdomain_id from category_brand_info where brand_id=$from_bid";
			my $rows=$dbhu->do($sql);
		}
		$s1->finish();
	}
}
if ($header_img ne "")
{
	$sql="update client_brand_info set newsletter_header='$header_img' where brand_id=$bid";
	my $rows=$dbhu->do($sql);
	if (($brand_type eq "Newsletter") && ($updateall eq "Y"))
	{
		$sql="update client_brand_info set newsletter_header='$header_img' where brand_name='$old_bname'";
		my $rows=$dbhu->do($sql);
	}
}
if ($footer_img ne "")
{
	$sql="update client_brand_info set newsletter_footer='$footer_img' where brand_id=$bid";
	my $rows=$dbhu->do($sql);
	if (($brand_type eq "Newsletter") && ($updateall eq "Y"))
	{
		$sql="update client_brand_info set newsletter_footer='$footer_img' where brand_name='$old_bname'";
		my $rows=$dbhu->do($sql);
	}
}
if ($unsub_img ne "")
{
	$sql="update client_brand_info set unsub_img='$unsub_img' where brand_id=$bid";
	my $rows=$dbhu->do($sql);
	if (($brand_type eq "Newsletter") && ($updateall eq "Y"))
	{
		$sql="update client_brand_info set unsub_img='$unsub_img' where brand_name='$old_bname'";
		my $rows=$dbhu->do($sql);
	}
}
if ($privacy_img ne "")
{
	$sql="update client_brand_info set privacy_img='$privacy_img' where brand_id=$bid";
	my $rows=$dbhu->do($sql);
	if (($brand_type eq "Newsletter") && ($updateall eq "Y"))
	{
		$sql="update client_brand_info set privacy_img='$privacy_img' where brand_name='$old_bname'";
		my $rows=$dbhu->do($sql);
	}
}

if ($tag==2 && $bid!=0 && !$upd_tag) {
	print "Content-type: text/html\n\n";
	print qq^
	<html>
	<head>
		<meta http-equiv="Content-Language" content="en-us">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>Tag Brand</title>
	</head>
<body>
<form method=get action="client_brand_upd2.cgi">
<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table1">
    <tr vAlign="top" colspan=2>
        <td noWrap align="left">
		<input type=hidden name="brandID" value="$bid">
		<input type=hidden name="clientID" value="$cid">
        <table cellSpacing="0" cellPadding="0" width="719" border="0" id="table2">
            <tr>
                <td width="248" bgColor="#ffffff" rowSpan="2">
                <img src="/mail-images/header.gif" border="0"></td>
                <td width="328" bgColor="#ffffff">&nbsp;</td>
            </tr>
            <tr>
                <td width="468">
                <table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table3">
                    <tr>
                        <td align="left"><b><font face="Arial" size="2">&nbsp;Tag Brand</font></b></td>
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
        </td>
    </tr>
	<tr>
	  <td>The Brand you want to tag to: </td>
	  <td><select name="tag_to"><option value="">Brands
	^;
	my $quer=qq|SELECT brand_name,brand_id FROM client_brand_info WHERE client_id=$cid AND status='A' AND third_party_id=10|;
	my $sth=$dbhq->prepare($quer);
	$sth->execute;
	while (my ($brand_name,$brandID)=$sth->fetchrow) {
		print qq^<option value="$brandID">$brand_name\n^;
	}
	$sth->finish;
	print qq^
		</select></td>
	</tr>
	<tr><td><input type=submit name="submit" value="Submit"></td></tr>
</table></form>
</body></html>
^;
}
else {
	print "Content-type: text/html\n\n";
	print qq^
	<html>
	<head></head>
	<body>
	<script language="JavaScript">
	^;
	if ($bid > 0)
	{
		print "document.location=\"/cgi-bin/client_list.cgi\";\n";
	}
	else
	{
		print "document.location=\"/cgi-bin/client_list.cgi\";\n";
	}
	print qq^
	</script>
	</body></html>
	^;
}
exit(0);
