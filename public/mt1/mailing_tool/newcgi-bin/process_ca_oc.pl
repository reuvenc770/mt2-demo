#!/usr/bin/perl
use strict;

use Net::FTP;
use DBI;
use Data::Dumper;
use Date::Manip;
use lib "/var/www/html/newcgi-bin";
use util;

$| = 1;    # don't buffer output for debugging log
my $dbh;
my $em;
my $cdate;
my $odate;
my $esp;
my $E;
my $util=util->new();
my $host = "23.92.22.64";
my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('lvlcty4','$L1g#J$ILqpi') or print "Cannot login ", $ftp->message;
	$ftp->ascii();
	$ftp->cwd("CA-OC");
	my @remote_files=$ftp->ls();
	my $cnt=0;
	foreach my $file (@remote_files)
	{
		$cnt++;
		if ($cnt == 1)
		{
    		$dbh = DBI->connect("DBI:mysql:new_mail:masterdb.i.routename.com","db_user","sp1r3V");
			my $sql="select espID,espName from ESP where espStatus='A'";
			my $sth=$dbh->prepare($sql);
			$sth->execute();
			my $id;
			my $name;
			while (($id,$name)=$sth->fetchrow_array())
			{
				print "<$name> <$id>\n";
				$E->{$name}=$id;
			}
			$sth->finish();
		}
		print "$file\n";
		my $output_file="/var/www/util/data/ca/".$file;
		$ftp->get($file,$output_file) or die "Get Failed ",$ftp->message;
		$ftp->delete($file);
	}
    $ftp->quit;
	#
    unless ($dbh && $dbh->ping) {
           $dbh = DBI->connect("DBI:mysql:new_mail:masterdb.i.routename.com","db_user","sp1r3V");
	}
	foreach my $file (@remote_files)
	{
		my $output_file="/var/www/util/data/ca/".$file;
		my $output_file1="/var/www/util/data/ca/processed/".$file;
		open(IN,"<$output_file");
		while (<IN>)
		{
			my $line=$_;
			chomp($line);
			($em,$cdate,$odate,$esp)=split(",",$line);
			if ($em eq "Email")
			{
				next;
			}
			$esp=~s///g;
			if ($cdate ne "")
			{
				$cdate=UnixDate($cdate,"%Y-%m-%d %H:%M:%S");
				print "Click: $E->{$esp} $em $cdate\n";
				processAction(2,$em,$cdate,$E->{$esp});
			}
			if ($odate ne "")
			{
				$odate=UnixDate($odate,"%Y-%m-%d %H:%M:%S");
				print "Open: $E->{$esp} $em $odate\n";
				processAction(1,$em,$odate,$E->{$esp});
			}
		}
		close(IN);
		my $cmd="mv $output_file $output_file1";
		print "$cmd\n";
		system($cmd);
	}
}

sub processAction
{
	my ($action,$email,$cdate,$espID)=@_;
	my $actionTypeID;
	my $eid;

    unless ($dbh && $dbh->ping) {
           $dbh = DBI->connect("DBI:mysql:new_mail:masterdb.i.routename.com","db_user","sp1r3V");
	}
	my $sql="select email_user_id,emailUserActionTypeID from email_list el join user u on u.user_id=el.client_id where el.email_addr=? and el.status='A' and OrangeClient='Y'";
	my $sth=$dbh->prepare($sql);
	$sth->execute($email);
	if (($eid,$actionTypeID)=$sth->fetchrow_array())
	{
		if (($actionTypeID == 4)  or ($actionTypeID < $action))
		{
			$sql="update email_list set emailUserActionTypeID=$action,emailUserActionDate='$cdate' where email_user_id=$eid";
			print "$sql\n";
			my $rows=$dbh->do($sql);
		}
		elsif ($actionTypeID <= $action)
		{
			$sql="update email_list set emailUserActionTypeID=$action,emailUserActionDate='$cdate' where email_user_id=$eid and emailUserActionDate < '$cdate'";
			print "$sql\n";
			my $rows=$dbh->do($sql);
		}
		$sql="insert ignore into EspUserAction(emailUserID,espActionTypeID,espID,espUserActionDateTime) values($eid,$action,$espID,'$cdate')";
		print "$sql\n";
		my $rows=$dbh->do($sql);
	}
	$sth->finish();
}
