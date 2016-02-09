#!/usr/bin/perl
#
# Process a file formatted as:
# BID:DOMAIN NAME
#
#	Deletes all records from brand_available_domains and brand_url_info and 
#	then adds in all domains for the brand
#
use strict;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

main();

sub main
{


my $util = util->new;
my $dbh;
# my $rows;
my $sql;
# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
&process_file($dbhu) ;
$util->clean_up();


}

#===============================================================================
# Sub: process_file
#  1. Open file
#  2. Loop - Read File til EOF 
#  3. Update 'list_member' for Logical Delete/Remove (eg set status = R )
#      - set proper counts (eg good, bad, total)
#===============================================================================
sub process_file 
{
    my ($dbhu)	= @_;

    my $domainsByBrand	= getDomainsByBrand();

    my $brandsProcessed	= 0;
    
    for my $bid (sort {$a <=> $b} keys(%$domainsByBrand))
    {
	print "Processing brand id $bid\n";
	my $sql1	="delete from brand_available_domains where brandID=$bid";
	my $rows = executeQuery($dbhu, $sql1);
	my $sql2	="delete from brand_url_info where brand_id=$bid"; 
	my $rows = executeQuery($dbhu, $sql2);
	
	my @domains	= @{$domainsByBrand->{$bid}};

	my $domainsProcessed	= 1;

	for my $domain (@domains)
	{
	    print "\tProcessing domain <$domain>\n";
	    if($domainsProcessed == 1)
	    {
		my $sql3	="insert into brand_url_info(brand_id,url_type,url) values($bid,'O','$domain')";
		my $rows = executeQuery($dbhu, $sql3);
		my $sql4	="insert into brand_url_info(brand_id,url_type,url) values($bid,'Y','$domain')";
		my $rows = executeQuery($dbhu, $sql4);
		my $sql5	="insert into brand_url_info(brand_id,url_type,url) values($bid,'OI','$domain')";
		my $rows = executeQuery($dbhu, $sql5);
		my $sql6	="insert into brand_url_info(brand_id,url_type,url) values($bid,'YI','$domain')";
		my $rows = executeQuery($dbhu, $sql6);
		my $sql7	="insert into brand_available_domains(brandID,domain,type,rank,inService) values($bid,'$domain','O',$domainsProcessed,1)";	
		my $rows = executeQuery($dbhu, $sql7);
	    }
	    else
	    {
		my $sql8	="insert into brand_available_domains(brandID,domain,type,rank,inService) values($bid,'$domain','O',$domainsProcessed,0)";	
		my $rows = executeQuery($dbhu, $sql8);
	    }
	    $domainsProcessed++;

	}

	$brandsProcessed++;
    } 
} # end of sub

sub executeQuery
{
    my ($dbh, $query)	= @_;
    
    my $rows;

    if(testEnabled())
    {
	print qq|TEST MODE: not executing: $query\n|;
    }
    else
    {
	$rows	=	$dbh->do($query);
    }

    return($rows);
}

sub getDomainsByBrand
{
	my $file_in = $ARGV[0] ;
	open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

	my $domainsByBrand	= {};

	while (<SAVED>) 
	{
	    chomp;                       # remove Carriage Return (if exists)
	    my $line = $_;
	    $line =~ s///g ;      # remove ^M from Email Addr (if exists)
	    $line =~ s/[\t,]/|/g ;
	    
	    my ($brand, $domain)	= split(/:/, $line);
	    ## ignore blank lines
	    if($line =~ /^\S*$)
	    {
		## do nothing
	    }
	    elsif($brand !~ /^\d+$/)
	    {
		die("Bad Brand: $brand.  Please fix and re-run\n");
	    }elsif($domain !~ /\w+\.\w+/)
	    {
		die("Bad Domain: $domain.  Please fix and re-run\n");
	    }
	    else
	    {
		push(@{$domainsByBrand->{$brand}}, $domain);
	    }
	    
	}	

	close(SAVED);

	return($domainsByBrand);
}

sub testEnabled
{
    return($ENV{'TEST'});
}

