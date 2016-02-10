#!/usr/bin/perl
#===============================================================================
# File   : kenzei_unsub.pl 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
#my $query = CGI->new;
my $dbh;
my $rows;
my $sql;

# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
my $filename="/tmp/kenzei_unsubs_".$ARGV[0].".txt";
open(LOG,">$filename");
$sql="select email_addr from unsub_log,PartnerClientInfo where PartnerClientInfo.partner_id=7 and PartnerClientInfo.client_id=unsub_log.client_id and unsub_date = date_sub(curdate(),interval 1 day)";
my $sth=$dbhu->prepare($sql);
$sth->execute();
while (($em)=$sth->fetchrow_array())
{
	print LOG "$em\n";
}
$sth->finish();
close(LOG);
$util->clean_up();
exit(0) ;

