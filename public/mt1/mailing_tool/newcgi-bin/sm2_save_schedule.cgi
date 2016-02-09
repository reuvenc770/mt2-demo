#!/usr/bin/perl

# ******************************************************************************
# sm2_save_schedule.cgi
#
# this page calls the appropriate routine based on the based in data 
#
# History
# Jim Sobeck, 02/26/08, Creation
# ******************************************************************************

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
my $sid;
my $rows;
my $errmsg;
my $tid;
my $cdate;

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
my $submit= $query->param('submit');
if ($submit eq "schedule it")
{
	my @f_box = $query->param('empty_0_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,'$cdate','START')";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('empty_1_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,'$cdate','START')";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('empty_2_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,'$cdate','START')";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('empty_3_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,'$cdate','START')";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('empty_4_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,'$cdate','START')";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('empty_5_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,'$cdate','START')";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('empty_6_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,'$cdate','START')";
		my $rows=$dbhu->do($sql); 
	}
    print "Location: sm2_history_schedule.cgi\n\n";
}
elsif ($submit eq "unschedule it")
{
	my @f_box = $query->param('sch_0_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="delete from test_schedule where test_id=$tid and schedule_date='$cdate' and status='START'";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('sch_1_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="delete from test_schedule where test_id=$tid and schedule_date='$cdate' and status='START'";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('sch_2_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="delete from test_schedule where test_id=$tid and schedule_date='$cdate' and status='START'";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('sch_3_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="delete from test_schedule where test_id=$tid and schedule_date='$cdate' and status='START'";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('sch_4_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="delete from test_schedule where test_id=$tid and schedule_date='$cdate' and status='START'";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('sch_5_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="delete from test_schedule where test_id=$tid and schedule_date='$cdate' and status='START'";
		my $rows=$dbhu->do($sql); 
	}
	my @f_box = $query->param('sch_6_box');
	foreach my $f1 (@f_box)
	{
		($tid,$cdate)=split('\|',$f1);
		$sql="delete from test_schedule where test_id=$tid and schedule_date='$cdate' and status='START'";
		my $rows=$dbhu->do($sql); 
	}
    print "Location: sm2_history_schedule.cgi\n\n";
}
$util->clean_up();
exit(0);
