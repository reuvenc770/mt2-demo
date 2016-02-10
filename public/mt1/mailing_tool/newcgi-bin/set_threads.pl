#!/usr/bin/perl
#
#------------------------------------------------------------------------
# Purpose: This program gets the currently scheduled deals for tomorrow
#		   by default.  You can pass a date to get a different days
#			campaigns and places them in the current_campaigns table
#------------------------------------------------------------------------ 
use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $rows;
my $sql;
my $sth;

# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

my $reset=$ARGV[0];
if ($reset == 1)
{
	$sql="update server_config set threadsTR=20 where server in ('mtadata03')";
	$rows=$dbhu->do($sql);
	$sql="update server_config set threadsTR=30 where server in ('mtadata01','mtadata02')";
	$rows=$dbhu->do($sql);
	$sql="update server_config set threadsTR=45 where server in ('mtadata06','mtadata07')";
	$rows=$dbhu->do($sql);
}
else
{
	$sql="update server_config set threadsTR=31 where server in ('mtadata03')";
	$rows=$dbhu->do($sql);
	$sql="update server_config set threadsTR=31 where server in ('mtadata01','mtadata02')";
	$rows=$dbhu->do($sql);
	$sql="update server_config set threadsTR=46 where server in ('mtadata06','mtadata07')";
	$rows=$dbhu->do($sql);
}
