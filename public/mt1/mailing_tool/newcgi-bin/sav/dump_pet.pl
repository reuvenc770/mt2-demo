#!/usr/bin/perl
#===============================================================================
# Purpose: Export pet_survey information 
# File   : dump_pet.pl 
#
#--Change Control---------------------------------------------------------------
#  Mar 26, 2002	Jim Sobeck	Created 
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use pms;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true
############################################
my $pms = pms->new;
my ($sql, $sth, $dbh ) ;
my $upload_dir_unix;
my $outfile;
my $filename;
my $datestr;
my @flds;

# ----- connect to the pms database -------

$pms->db_connect();
$dbh = $pms->get_dbh;
$sql = "select date_format(date_sub(curdate(),interval 1 day),\"%m%d%Y\")";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($datestr) = $sth->fetchrow_array();
$sth->finish();

$outfile = "/home/ftpuser/pet_survey_$datestr.txt"; 
unless (open(OUTFILE, ">$outfile"))
{
	pms::logerror("There was an error opening the Output File: $outfile");
	$pms->clean_up();
	exit(0);
}

print OUTFILE "Dogs|Cats|Dog Ages|Cat Ages|Cat Food|Dog Food|Last Vet|Next Vet|Signs|Senior Health Screening|Pets Health|Food Prevents|Optin|First|Last|Email|Address|City|State|Zip\n";

$sql = "select * from pet_survey where entry_date < curdate() and entry_date >= date_sub(curdate(),interval 1 day)";
#$sql = "select * from pet_survey";
$sth = $dbh->prepare($sql) ;
$sth->execute();
while ((@flds) = $sth->fetchrow_array())
{
	print OUTFILE "$flds[1]|$flds[2]|$flds[4]|$flds[3]|$flds[6]|$flds[5]|$flds[7]/$flds[8]|$flds[9]/$flds[10]|$flds[11]|$flds[12]|$flds[13]|$flds[14]|$flds[15]|$flds[16]|$flds[17]|$flds[18]|$flds[19]|$flds[20]|$flds[21]|$flds[22]\n";
}
$sth->finish();
close OUTFILE;
$pms->clean_up();
exit(0);
