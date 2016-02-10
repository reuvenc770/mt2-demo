#!/usr/bin/perl
# *****************************************************************************************
# brand_save.cgi
#
# this page saves the copy brand changes 
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
my $rows;
my $errmsg;
my $newcid;
my $fld;
my $oldaid = $query->param('oldaid');
my $aname = $query->param('brand_name');
$aname=~tr/[0-9][a-z][A-Z]\-_/ /c;
$aname=~s/ //g;
my $client_id= $query->param('cid');
my $tag_to= $query->param('tag_to');
my $newaid;

my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $i=0;
while ($i <= 10)
{
	$fld="newcid".$i;
	$newcid=$query->param($fld);
	if ($newcid > 0)
	{
		$sql = "insert into client_brand_info(client_id,brand_name,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,others_host,yahoo_host,header_text,footer_text,footer_variation,status,footer_font_id,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,brand_type,third_party_id,unsub_img,exclude_subdomain ,creative_selection,exclude_thirdparty,template_id,include_privacy,from_address ,display_name,exclude_wiki,replace_domain,brand_priority,num_domains_rotate,client_type,purpose,ignore_mta_template_settings,subject) select $newcid,'$aname',others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,others_host,yahoo_host,header_text,footer_text,footer_variation,status,footer_font_id,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,brand_type,third_party_id,unsub_img,exclude_subdomain,creative_selection,exclude_thirdparty,template_id,include_privacy,from_address,display_name,exclude_wiki,replace_domain,brand_priority,num_domains_rotate,client_type,purpose,ignore_mta_template_settings,subject from client_brand_info where brand_id=$oldaid"; 
		$rows = $dbhu->do($sql);
		if ($dbhu->err() != 0)
		{
			$errmsg = $dbhu->errstr();
			util::logerror("Updating client_brand_info $sql: $errmsg");
			exit(0);
		}
		$sql = "select max(brand_id) from client_brand_info where brand_name='$aname'";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($newaid) = $sth->fetchrow_array();
		$sth->finish();
		if ($tag_to) {
			my $qTag=qq|UPDATE client_brand_info SET tag='$oldaid' WHERE brand_id='$newaid'|;
			$dbhu->do($qTag);
		}
	
		$sql = "insert into brand_url_info(brand_id,url_type,url) select $newaid,url_type,LCASE(url) from brand_url_info where brand_id=$oldaid and url_type!='C'";
		$rows=$dbhu->do($sql);
		$sql = "insert into brand_available_domains(brandID,domain,type,rank,inService) select $newaid,LCASE(domain),type,rank,inService from brand_available_domains where brandID=$oldaid";
		$rows=$dbhu->do($sql);
		$sql = "insert into brand_host(brand_id,server_name,server_type) select $newaid,server_name,server_type from brand_host where brand_id=$oldaid and server_type!='C'";
		$rows=$dbhu->do($sql);
		$sql = "insert into brand_ip(brandID,ip) select $newaid,ip from brand_ip where brandID=$oldaid";
		my $rows=$dbhu->do($sql);
	
		my $from_bid;
		$sql="select max(brand_id) from client_brand_info where brand_name='DirectEmailSolution'";
		$sth=$dbhq->prepare($sql);
		$sth->execute();
		($from_bid)=$sth->fetchrow_array();
		$sth->finish();
		$sql = "insert into category_brand_info(brand_id,subdomain_id) select $newaid,subdomain_id from category_brand_info where brand_id=$from_bid";
		my $rows=$dbhu->do($sql);
	}
	$i++;
}
#
print "Location: client_list.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
