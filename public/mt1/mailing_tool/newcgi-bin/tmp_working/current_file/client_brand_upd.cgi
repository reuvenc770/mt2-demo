#!/usr/bin/perl

# *****************************************************************************************
# client_brand_upd.cgi
#
# this page updates information for a client brand 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
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

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;
my $bid;
my $bname;

my $cid = $query->param('cid');
my $bid = $query->param('bid');
my $bname = $query->param('brandname');
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
my $whois_email = $query->param('whois_email');
my $abuse_email = $query->param('abuse_email');
my $personal_email = $query->param('personal_email');
my $footer_text = $query->param('footer_text');
$footer_text =~ s/'/''/g;
my $header_text = $query->param('header_text');
$header_text =~ s/'/''/g;
my $notes= $query->param('notes');
$notes=~ s/'/''/g;
my $aol_comments = $query->param('aol_comments');
my $aolw_flag = $query->param('aolw_flag');
if ($aolw_flag eq "")
{
	$aolw_flag = "N";
}
$aol_comments =~ s/'/''/g;
my $vid = $query->param('vid');
my $color_id = $query->param('color_id');
my $bg_color_id = $query->param('bg_color_id');
my $font_id = $query->param('font_id');
#
if ($bid > 0)
{
	$sql = "update client_brand_info set brand_name='$bname',others_ns1='$ons1',others_ns2='$ons2',yahoo_ns1='$yns1',yahoo_ns2='$yns2',others_ip='$oip',yahoo_ip='$yip',mailing_addr1='$addr1',mailing_addr2='$addr2',whois_email='$whois_email',abuse_email='$abuse_email',personal_email='$personal_email',dns_host='$dns_host', clean_host='$clean_host', others_host='$o_host',yahoo_host='$y_host',footer_text='$footer_text',header_text='$header_text',footer_variation=$vid,footer_color_id=$color_id,footer_bg_color_id=$bg_color_id,cleanser_ns1='$cns1',cleanser_ns2='$cns2',footer_font_id=$font_id,notes='$notes',aolw_flag='$aolw_flag',aol_comments='$aol_comments' where brand_id=$bid";
}
else
{
	$sql = "insert into client_brand_info(client_id,brand_name,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,dns_host, clean_host, others_host,yahoo_host,footer_text,header_text,footer_variation,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,footer_font_id,notes,aolw_flag,aol_comments) values($cid,'$bname','$ons1','$ons2','$yns1','$yns2','$oip','$yip','$addr1','$addr2','$whois_email','$abuse_email','$personal_email','$dns_host', '$clean_host', '$o_host','$y_host','$footer_text','$header_text',$vid,$color_id,$bg_color_id,'$cns1','$cns2',$font_id,'$notes','$aolw_flag','$aol_comments')";
}
my $rows=$dbh->do($sql);
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script language="JavaScript">
end_of_html
if ($bid > 0)
{
	print "document.location=\"/cgi-bin/client_brand_list.cgi?cid=$cid\";\n";
}
else
{
	$sql = "select max(brand_id) from client_brand_info where brand_name='$bname'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($bid) = $sth->fetchrow_array();
	$sth->finish();
	print "document.location=\"/cgi-bin/edit_client_brand.cgi?bid=$bid&cid=$cid&mode=U\";\n";
}
print<<"end_of_html";
</script>
</body></html>
end_of_html
exit(0);
