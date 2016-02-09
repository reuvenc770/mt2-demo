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

	my $file_in = $ARGV[0] ;
	open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($bname, $domain,@rest_of_line) = split('\|', $line) ;
		$domain =~ tr/[A-Z]/[a-z]/;
		$sql="select brand_id from client_brand_info where brand_name=? and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($bname);
		if (($bid)=$sth->fetchrow_array())
		{
			$sql="select max(rank) from brand_available_domains where brandID=?";
			my $sth1=$dbhu->prepare($sql);
			$sth1->execute($bid);
			if (($rank)=$sth1->fetchrow_array())
			{
			}
			else
			{
				$rank=1;
			}
			$sth1->finish();
			if ($rank eq "")
			{
				$rank=1;
			}
			$sql="insert into brand_available_domains(brandID,domain,type,rank,inService) values($bid,'$domain','O',$rank,0)";	
			my $rows=$dbhu->do($sql);
		}
		else
		{
			print "Brand $bname not found\n";
		}
		$sth->finish();
	} 
	close SAVED;
} # end of sub
