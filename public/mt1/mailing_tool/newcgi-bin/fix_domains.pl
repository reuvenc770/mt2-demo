#!/usr/bin/perl
use strict;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $dbh;
my $rows;
my $sql;
# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
&process_file() ;
$util->clean_up();
exit(0) ;



#===============================================================================
# Sub: process_file
#  1. Open file
#  2. Loop - Read File til EOF 
#  3. Update 'list_member' for Logical Delete/Remove (eg set status = R )
#      - set proper counts (eg good, bad, total)
#===============================================================================
sub process_file 
{
	my $bname;
	my $domain;
	my $bid;
	my $line;
	my @rest_of_line;
	my $sth;
	my $rank;
	my $reccnt;
	my $rows;

	$sql="select distinct brandID from brand_available_domains where brandID not in (select brandID from brand_available_domains where inService=1)";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($bid)=$sth->fetchrow_array())
	{
		$sql="select domain from brand_available_domains where brandID=? and inService=0 order by rand() limit 1";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($bid);
		($domain)=$sth1->fetchrow_array();
		$sth1->finish();
		print "Updating $bid - $domain\n";
		$sql="update brand_available_domains set inService=1 where brandID=$bid and domain='$domain'";
		$rows=$dbhu->do($sql); 
		$sql="update brand_url_info set url='$domain' where brand_id=$bid and url_type in ('O','Y','OI','YI')";
		$rows=$dbhu->do($sql); 
	} 
	$sth->finish();	
} # end of sub
