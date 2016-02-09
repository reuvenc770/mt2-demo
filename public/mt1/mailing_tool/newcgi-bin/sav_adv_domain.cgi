#!/usr/bin/perl

# *****************************************************************************************
# sav_adv_domain.cgi
#
# this page updates information in the brand_advertiser_info table
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
my $sid;
my $i;
my $class;
my $errmsg;
my $images = $util->get_images_url;
my $pmesg="";
my @url_array;
my $cnt;
my $curl;
my $temp_cnt;
my $btype;
my $nl_id;

$cnt=0;
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>save</title></head>
<body>
<br>
end_of_html
#
# Remove old url information
#
my $bid = $query->param('bid');
my $upd= $query->param('upd');
my $adv_id = $query->param('adv_id');
my @deldom= $query->param('deldom');
$sql="select brand_type,nl_id from client_brand_info where brand_id=$bid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($btype,$nl_id)=$sth->fetchrow_array();
$sth->finish();

foreach my $sid (@deldom)
{
    $sql="delete from brand_advertiser_info where brand_id=$bid and advertiser_id=$adv_id and domain_name='$sid'";
    my $rows=$dbhu->do($sql);
	print "Domain <b>$sid</b> deleted<br>\n";
	if (($btype eq "Newsletter") and ($nl_id > 0) and ($upd eq "Y"))
	{
    	$sql="delete from brand_advertiser_info where brand_id=(select brand_id from client_brand_info where nl_id=$nl_id and status='A') and advertiser_id=$adv_id and domain_name='$sid'";
    	my $rows=$dbhu->do($sql);
	}
}
#
# Get the information about the user from the form 
#
my $url_list= $query->param('domain');
$url_list =~ s/[\n\r\f\t]/\|/g ;
$url_list =~ s/\|{2,999}/\|/g ;
@url_array = split '\|', $url_list;
foreach $curl (@url_array)
{
	$curl =~ s/'/''/g;
	$curl =~ s/\x96/-/g;
    $curl =~ s/\//g;
    $curl =~ s/\xc2//g;
    $curl =~ s/\xa0//g;
    $curl =~ s/\xb7//g;
    $curl =~ s/\x85//g;
    $curl =~ s/\x95//g;
    $curl =~ s/\xae//g;
    $curl =~ s/\x99//g;
    $curl =~ s/\xa9//g;
    $curl =~ s/\x92//g;
    $curl =~ s/\x93//g;
    $curl =~ s/\x94//g;
    $curl =~ s/\x95//g;
    $curl =~ s/\x96//g;
    $curl =~ s/\x97//g;
    $curl =~ s/\x82//g;
    $curl =~ s/\x85//g;
	$sql="select count(*) from brand_available_domains where domain=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($curl);
	($temp_cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($temp_cnt > 0)
	{
		print "Domain <b>$curl</b> not added - already in brand_available_domains<br>\n";
		next;
	}
	$sql="select count(*) from brand_url_info where url=?"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($curl);
	($temp_cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($temp_cnt > 0)
	{
		print "Domain <b>$curl</b> not added - already in brand_url_info<br>\n";
		next;
	}
	if (($btype eq "Newsletter") and ($nl_id > 0) and ($upd eq "Y"))
	{
		$sql="select count(*) from brand_advertiser_info,client_brand_info where brand_advertiser_info.domain_name=? and brand_advertiser_info.brand_id=client_brand_info.brand_id and nl_id=$nl_id and client_brand_info.status='A'";
	}
	else
	{
		$sql="select count(*) from brand_advertiser_info where domain_name=? and brand_id != $bid";
	}
	$sth=$dbhu->prepare($sql);
	$sth->execute($curl);
	($temp_cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($temp_cnt > 0)
	{
		print "Domain <b>$curl</b> not added - already defined for advertiser<br>\n";
		next;
	}
	$sql="insert into brand_advertiser_info(brand_id,advertiser_id,domain_name) values($bid,$adv_id,'$curl')";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		my $errmsg = $dbhu->errstr();
	}
	else
	{
		$cnt++;
		if (($btype eq "Newsletter") and ($nl_id > 0) and ($upd eq "Y"))
		{
			$sql="insert into brand_advertiser_info(brand_id,advertiser_id,domain_name) select brand_id,$adv_id,'$curl' from client_brand_info where nl_id=$nl_id and status='A' and brand_id != $bid";
			$sth = $dbhu->do($sql);
		}
	}
}
print<<"end_of_html";
<h3> <b>$cnt</b> domains added</h3>
</body>
</html>
end_of_html
exit(0);
