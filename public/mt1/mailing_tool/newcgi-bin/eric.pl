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
		($domain,@rest_of_line) = split('\|', $line) ;
		$domain =~ tr/[A-Z]/[a-z]/;
		$sql="select distinct cb.brand_id,brand_name from client_brand_info cb,brand_url_info b where cb.brand_id=b.brand_id and url=? and status='A' and url_type in ('OI','YI')";
		$sth=$dbhu->prepare($sql);
		$sth->execute($domain);
		if (($bid,$bname)=$sth->fetchrow_array())
		{
			print "$bid,$bname,$domain\n";
		}
		$sth->finish();
	} 
	close SAVED;
} # end of sub
