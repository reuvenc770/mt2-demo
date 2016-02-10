#!/usr/bin/perl
#===============================================================================
# Purpose: Adds any new clients added for client_type 
# File   : add_client_brand.cgi
#
# Jim Sobeck	03/04/08	Creation	
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $bid = $query->param('bid');
my $bname;
my $client_type;
my $nl_name;
my $nl_id;
my $chk1;
my $newbid;
my $rows;
my $tbid;
my ($sth, $reccnt, $sql, $dbh ) ;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
$sql="select brand_name from client_brand_info where brand_id=$bid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($bname)=$sth->fetchrow_array();
$sth->finish();

my @chkbox = $query->param('chkbox') ;
foreach $chk1 (@chkbox)
{
	$sql="select brand_id from client_brand_info where brand_name='$bname' and client_id=$chk1";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	while (($tbid)=$sth->fetchrow_array())
	{
		$sql="delete from client_brand_info where brand_id=$tbid";
		$rows=$dbhu->do($sql);
		$sql="delete from category_brand_info where brand_id=$tbid";
		$rows=$dbhu->do($sql);
		$sql="delete from brand_url_info where brand_id=$tbid";
		$rows=$dbhu->do($sql);
		$sql="delete from brand_available_domains where brandID=$tbid";
		$rows=$dbhu->do($sql);
		$sql="delete from brand_advertiser_info where brand_id=$tbid";
		$rows=$dbhu->do($sql);
	}
	$sth->finish();
	$sql = "insert into client_brand_info(client_id,brand_name,brand_fullname,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,phone,whois_email,abuse_email,personal_email,dns_host, clean_host, others_host,yahoo_host,footer_text,header_text,footer_variation,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,footer_font_id,notes,aolw_flag,aol_comments,brand_type,third_party_id,font_type,font_size,align,nl_id,from_address, display_name, replace_domain, brand_priority,num_domains_rotate,client_type,privacy_img,newsletter_header,newsletter_footer,unsub_img,subject) select $chk1,brand_name,brand_fullname,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,phone,whois_email,abuse_email,personal_email,dns_host, clean_host, others_host,yahoo_host,footer_text,header_text,footer_variation,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,footer_font_id,notes,aolw_flag,aol_comments,brand_type,third_party_id,font_type,font_size,align,nl_id,from_address, display_name, replace_domain, brand_priority,num_domains_rotate,client_type,privacy_img,newsletter_header,newsletter_footer,unsub_img,subject from client_brand_info where brand_id=$bid";
	$rows=$dbhu->do($sql);
	$sql="select max(brand_id) from client_brand_info where brand_name='$bname' and client_id=$chk1";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($newbid)=$sth->fetchrow_array();
	$sth->finish();
	$sql = "insert into category_brand_info(brand_id,subdomain_id) select $newbid,subdomain_id from category_brand_info where brand_id=$bid";
	my $rows=$dbhu->do($sql);
	$sql="insert into brand_url_info(brand_id,url_type,url) select $newbid,url_type,LCASE(url) from brand_url_info where brand_id=$bid";
	$rows=$dbhu->do($sql);
	$sql="insert into brand_available_domains(brandID,domain,type,rank,inService) select $newbid,LCASE(domain),type,rank,inService from brand_available_domains where brandID=$bid";
	$rows=$dbhu->do($sql);
	$sql="insert into brand_advertiser_info(brand_id,advertiser_id,domain_name) select $newbid,advertiser_id,domain_name from brand_advertiser_info where brand_id=$bid";
	$rows=$dbhu->do($sql);
}
print "Location: /cgi-bin/check_clients.cgi?bid=$bid\n\n";
