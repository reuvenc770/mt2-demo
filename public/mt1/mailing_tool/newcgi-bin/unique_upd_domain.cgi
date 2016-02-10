#!/usr/bin/perl

# ******************************************************************************
# unique_upd_domain.cgi
#
# this page updates select domains and ips based on nl_id 
#
# History
# ******************************************************************************

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
my $bid;
my $bname;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $nid= $query->param('nid');
my $t1 = $query->param('t1');
my $uid = $query->param('uid');
my $sid = $query->param('sid');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
function doit(value,text)
{
	parent.main.addDomain(value,text);
}
function doita(value)
{
	parent.main.addDomain(value,value);
}
function doitac(value)
{
	parent.main.addCDomain(value,value);
}
function doit1(value,text)
{
	parent.main.addIP(value,text);
}
function doit2(value,text)
{
	parent.main.addProfile(value,text);
}
function upd_creative()
{
	parent.main.upd_creative(1);
}
function set_fields(did)
{
	parent.main.set_domain_fields(did);
}
function set_cfields(did)
{
	parent.main.set_cdomain_fields(did);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql="select brand_id from client_brand_info where client_id=64 and status='A' and nl_id=?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($nid);
($bid)=$sth->fetchrow_array();
$sth->finish();
	print "doit(\"ALL\",\"ROTATE ALL\");\n";
#	print "doit1(\"ALL\",\"ROTATE ALL\");\n";
$sql="select distinct domain from brand_available_domains where brandID=? and domain != 'arthuradvertising.com' union select distinct url from brand_url_info where brand_id=? and url_type in ('O','Y') order by 1";
$sth = $dbhq->prepare($sql);
$sth->execute($bid,$bid);
my $cdomain;
while (($cdomain) = $sth->fetchrow_array())
{
	print "doita(\"$cdomain\");\n";
}
$sth->finish();
$sql="select distinct domain from brand_available_domains where brandID=5197 order by domain"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cdomain;
while (($cdomain) = $sth->fetchrow_array())
{
	print "doitac(\"$cdomain\");\n";
}
$sth->finish();
if ($t1 == 1)
{
	my $did;
	if ($uid > 0)
	{
		$sql="select mailing_domain from UniqueDomain where unq_id=? order by mailing_domain desc";
		$sth=$dbhq->prepare($sql);
		$sth->execute($uid);
		while (($did)=$sth->fetchrow_array())
		{
			print "set_fields(\"$did\");\n";
		}
		$sth->finish();
		$sql="select domain_name from UniqueContentDomain where unq_id=? order by domain_name desc";
		$sth=$dbhq->prepare($sql);
		$sth->execute($uid);
		while (($did)=$sth->fetchrow_array())
		{
			print "set_cfields(\"$did\");\n";
		}
		$sth->finish();
	}
	elsif ($sid > 0)
	{
		$sql="select mailing_domain from UniqueSlotDomain where slot_id=? order by mailing_domain desc";
		$sth=$dbhq->prepare($sql);
		$sth->execute($sid);
		while (($did)=$sth->fetchrow_array())
		{
			print "set_fields(\"$did\");\n";
		}
		$sth->finish();
		$sql="select domain_name from UniqueSlotContentDomain where slot_id=? order by domain_name desc";
		$sth=$dbhq->prepare($sql);
		$sth->execute($sid);
		while (($did)=$sth->fetchrow_array())
		{
			print "set_cfields(\"$did\");\n";
		}
		$sth->finish();
	}
	print "upd_creative();\n";
}
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
