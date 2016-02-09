#!/usr/bin/perl
# *****************************************************************************************
# domain_exclusion_add.cgi
#
# this page adds domains to a DomainExclusion 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util= util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $userid;
my $dname;
my $gname;

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

# get fields from the form

my $paste_domains = $query->param('paste_domains');
my @domains= $query->param('sel2');
$sql = "delete from DomainExclusion";
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#domains)
{
	$sql = "insert into DomainExclusion(domain) values ('$domains[$i]')";
	$rows = $dbhu->do($sql);
	$i++;
}
$paste_domains=~ s/[ \n\r\f\t]/\|/g ;    
$paste_domains=~ s/\|{2,999}/\|/g ;           
my @domains_array = split '\|', $paste_domains;
my $list_domain;
my $cnt;
foreach $list_domain (@domains_array)
{
	$sql="select count(*) from Domain where domainName=? and active=1";
	my $sth=$dbhu->prepare($sql);
	$sth->execute($list_domain);
	($cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($cnt > 0)
	{
		$sql = "insert ignore into DomainExclusion(domain) values ('$list_domain')";
		$rows = $dbhu->do($sql);
	}
}
print "Location: domain_exclusion.cgi\n\n";
$util->clean_up();
exit(0);
