#!/usr/bin/perl
#===============================================================================
# File: get_brand_header.pl
#
#
# History
#===============================================================================

# include Perl Modules

use strict;
use File::Copy;
use util;
# declare variables

my $util = util->new;
my $dbh;
my $file;
my $sql;
my $sth;
my $bid;
my $header;
my $footer;
my $ftype;
my $fsize;
my $align;

# connect to the util database 
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

open(FILE,"> /var/www/html/templates/brand_header.dat") or die "can't open file: $!";
$sql = "select brand_id,header_text,footer_text,font_type,font_size,align from client_brand_info where status='A' order by brand_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($bid,$header,$footer,$ftype,$fsize,$align) = $sth->fetchrow_array())
{
	$header =~ s//{{CR}}/g;
	$header =~ s/\n/{{CR}}/g;
	$footer =~ s/\n/{{CR}}/g;
	$footer =~ s//{{CR}}/g;
    print FILE "$bid|$header|$footer|$ftype|$fsize|$align\n";
}
$sth->finish();
close(FILE);

$util->clean_up();
exit(0) ;				
