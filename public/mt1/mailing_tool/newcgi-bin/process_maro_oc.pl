#!/usr/bin/perl
use strict;

use Net::FTP;
use XML::Simple;
use DBI;
use Data::Dumper;
use lib "/var/www/html/newcgi-bin";
use util;

$| = 1;    # don't buffer output for debugging log
my $dbh;
my $unsub_cnt;
my $util=util->new();
my $host = "ftp.aspiremail.com";
my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('espdata','ch3frexA') or print "Cannot login ", $ftp->message;
	$ftp->ascii();
	$ftp->cwd("Arch/MARO");
	my @remote_files=$ftp->ls();
	foreach my $file (@remote_files)
	{
		print "$file\n";
		my $output_file="/var/www/util/data/maro/".$file;
		$ftp->get($file,$output_file) or die "Get Failed ",$ftp->message;
		$ftp->delete($file);
	}
    $ftp->quit;
	#
    unless ($dbh && $dbh->ping) {
           $dbh = DBI->connect("DBI:mysql:new_mail:masterdb.i.routename.com","db_user","sp1r3V");
	}
	my $parser=XML::Simple->new();
	foreach my $file (@remote_files)
	{
		my $output_file="/var/www/util/data/maro/".$file;
		my $output_file1="/var/www/util/data/maro/processed/".$file;
		my $data;
		eval
		{
			$data = $parser->XMLin($output_file);
		};
		next if $@;
		if (ref($data->{Worksheet}) eq "ARRAY")
		{
    		foreach my $l (@{$data->{Worksheet}})
    		{
				my $sheetName=$l->{'ss:Name'};
				if (($sheetName eq "Click Report") or ($sheetName eq "Open Report"))
				{
					my $action=1;
					my $cdate_pos=3;
					if ($sheetName eq "Click Report")
					{
						$action=2;
						$cdate_pos=4;
					}
					if (ref($l->{Table}->{Row}) eq "ARRAY")
					{
    				foreach my $row (@{$l->{Table}->{Row}})
    				{
						my $i=0;
						my $email;
						my $cdate;
						foreach my $cell (@{$row->{Cell}})
						{
							$i++;
							if ($i == 1)
							{
								$email=$cell->{Data}->{content};
							}
							elsif ($i == $cdate_pos)
							{
								$cdate=$cell->{Data}->{content};
							}
						}
						print "$action $email $cdate\n";
						processAction($action,$email,$cdate);
					}
					}
				}
				elsif ($sheetName eq "Unsubscribe Report") 
				{
					my $reccnt=0;
					$unsub_cnt=0;
					if (ref($l->{Table}->{Row}) eq "ARRAY")
					{
    				foreach my $row (@{$l->{Table}->{Row}})
    				{
						my $i=0;
						my $email;
						foreach my $cell (@{$row->{Cell}})
						{
							$i++;
							if ($i == 1)
							{
								$email=$cell->{Data}->{content};
							}
						}
						$reccnt++;
						print "Unsub $email\n";
						unsubscribe($email);
					}
					}
           			my $dbhr = DBI->connect("DBI:mysql:Reporting:db20.i.routename.com","db_user","sp1r3V");
					my $sql="insert into EspUnsubscribeData(espID,effectiveDate,espFileName,totalProcessedRecords,globalUnsubCnt) values(76,curdate(),'$file',$reccnt,$unsub_cnt)";
					my $rows=$dbhr->do($sql);
				}
			}
		}
		my $cmd="mv $output_file $output_file1";
		system($cmd);
	}
}

sub processAction
{
	my ($action,$email,$cdate)=@_;
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
		$sql="insert ignore into EspUserAction(emailUserID,espActionTypeID,espID,espUserActionDateTime) values($eid,$action,76,'$cdate')";
		print "$sql\n";
		my $rows=$dbh->do($sql);
	}
	$sth->finish();
}
sub unsubscribe 
{
	my ($email)=@_;
	my $eid;

    unless ($dbh && $dbh->ping) {
           $dbh = DBI->connect("DBI:mysql:new_mail:masterdb.i.routename.com","db_user","sp1r3V");
	}
	my $sql="select email_user_id from email_list el join user u on u.user_id=el.client_id where el.email_addr=? and el.status='A' and OrangeClient='Y'";
	my $sth=$dbh->prepare($sql);
	$sth->execute($email);
	if (($eid)=$sth->fetchrow_array())
	{
		$sql="update email_list set status='U',unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$eid";
		print "$sql\n";
		my $rows=$dbh->do($sql);
		$unsub_cnt++;
	}
	$sth->finish();
	my $params={};
	$params->{'suppressionReasonCode'}="EMARO";
	util::addOrangeGlobal($params);
}
