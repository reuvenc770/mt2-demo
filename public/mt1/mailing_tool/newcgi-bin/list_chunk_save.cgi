#!/usr/bin/perl
# *****************************************************************************************
# list_chunk_save.cgi
#
# this page saves the list changes
#
# History
# *****************************************************************************************

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
my $rows;
my $uid;
my $errmsg;

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

my $status = $dbhq->quote($query->param('status'));

	# update the list record

	my $list_id = $query->param('list_id');
	$sql = "update list set status = $status where list_id = $list_id";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Updating list record $sql: $errmsg");
		exit(0);
	}
	if ($status eq "D")
	{
		$sql="select user_id from list where list_id=$list_id";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($uid)=$sth->fetchrow_array();
		$sth->finish();

		my $dbhu2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
		$sql = "update email_chunk_list_$uid set list_id=0 where list_id=$list_id and status='A'";
		my $rows=$dbhu2->do($sql);
	}

print "Location: list_chunk_list.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
