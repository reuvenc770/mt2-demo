#!/usr/bin/perl
#
#use strict;
use DBI;
use Sys::Hostname;
use MIME::Base64;
use vars qw($hrINIT $DBHU $DBHQ $hrSVR $hrADV);
use lib "/var/www/bin";
use mta;
my $mta= mta->new;
my $total_cnt;
my $DAY_SECONDS=24*3600;
my $ret_stat;
my $sortfile;
my $DUP={};
my $old_creativeid;
my $gname;

$|=1;
my $campaign_str;
my $temp_id;
my $got_deal;
my $camp_id;
my $sql;
my $sth;
my $sent_seeds;
my $send_adv_seeds;
my $maxday=30;
my $minday=0;
my $ind;
my $domain_id;
my $MAX_CAMPS=1;
my $camp_cnt;
my $close;
my @CNT;
my $HCNT;
my $HCNT1;
my $ACTION;
my $FLATDIR="/var/www/util/data";
my @ISP=("aol","yahoo","gmail","aoluk","yahoouk");
my $client_group_id=$ARGV[0];
# load data on Sundays only
#
$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or die("ERROR : can't connect to update db: " . DBI->errstr, 1); 
$DBHQ=DBI->connect('DBI:mysql:new_mail:slavedb.i.routename.com', 'db_readuser', 'Tr33Wat3r',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or die("ERROR : can't connect to slavedb db: " . DBI->errstr, 1); 
my @CLIENT;
$sql="select cgc.client_id,coalesce(ua.level,999) from ClientGroupClients cgc left outer join UniqueAttribution ua on ua.client_id=cgc.client_id where client_group_id=$client_group_id order by 2";
my $sth=$DBHU->prepare($sql);
$sth->execute();
my $i=0;
my $clevel;
while (($cid,$clevel)=$sth->fetchrow_array())
{
	$CLIENT[$i]=$cid;
	$i++;
}
$sth->finish();
$sql="select group_name from ClientGroup where client_group_id=$client_group_id"; 
$sth=$DBHU->prepare($sql);
$sth->execute();
($gname)=$sth->fetchrow_array();
$sth->finish();
#
my $typeID;
my $label;
$sql="select emailUserActionTypeID,emailUserActionLabel from EmailUserActionType";
$sth=$DBHU->prepare($sql);
$sth->execute();
while (($typeID,$label)=$sth->fetchrow_array())
{
	$ACTION->{$label}=$typeID;
}
$sth->finish();

#
$total_cnt=0;
my $notify_email_addr=$ENV{'MAILTO'} || "mailops\@zetainteractive.com";
my $cc_email_addr="wshen\@zetainteractive.com,cdiederich\@zetainteractive.com,rhernandez\@zetainteractive.com,hmathieu\@zetainteractive.com,ewillett\@zetainteractive.com,mclegg\@zetainteractive.com,jherlihy\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
open (MAIL,"| /usr/sbin/sendmail -t");
print MAIL "From: $mail_mgr_addr\n";
#$notify_email_addr="jsobeck\@zetainteractive.com";
print MAIL "To: $notify_email_addr\n";
print MAIL "Cc: $cc_email_addr\n";
print MAIL "Subject: Data Counts for Client Group $client_group_id - $gname\n";
for (my $c1ind=0; $c1ind <= $#CLIENT; $c1ind++)
{
	my $client_id=$CLIENT[$c1ind];
	for (my $cind=0; $cind <= $#ISP; $cind++) 
	{
		$class_name=$ISP[$cind];
		my $tparams={};
		$tparams->{'emailActionType'}="opener";
		$tparams->{'emailClass'}=$class_name;
		$tparams->{'minDay'}=$minday;
		$tparams->{'maxDay'}=$maxday;
		my $data=$mta->getFlatFileNameList($tparams);
		my $cnt=$#{$data};
		my $i=0;
		undef @CNT;
		while ($i <= $cnt)
		{
        	my $flatFile=$data->[$i];
			$flatFile.=".gz";
        	my $datafile=$mta->getLocalFlatFile($flatFile, $CLIENT[$c1ind]);
       		@CNT=build_file($datafile,$minday,$maxday,$ACTION->{'opener'},$client_id,$class_name,@CNT);
			unlink($datafile);
			$i++;
		}
		dumpCnt("Openers",$class_name,$CLIENT[$c1ind]);

		my $tparams={};
		$tparams->{'emailActionType'}="clicker";
		$tparams->{'emailClass'}=$class_name;
		$tparams->{'minDay'}=$minday;
		$tparams->{'maxDay'}=$maxday;
		my $data=$mta->getFlatFileNameList($tparams);
		my $cnt=$#{$data};
		my $i=0;
		undef @CNT;
		while ($i <= $cnt)
		{
        	my $flatFile=$data->[$i];
			$flatFile.=".gz";
        	my $datafile=$mta->getLocalFlatFile($flatFile, $CLIENT[$c1ind]);
       		@CNT=build_file($datafile,$minday,$maxday,$ACTION->{'clicker'},$client_id,$class_name,@CNT);
			unlink($datafile);
			$i++;
		}
		dumpCnt("Clickers",$class_name,$CLIENT[$c1ind]);

		my $tparams={};
		$tparams->{'emailActionType'}="converter";
		$tparams->{'emailClass'}=$class_name;
		$tparams->{'minDay'}=$minday;
		$tparams->{'maxDay'}=$maxday;
		my $data=$mta->getFlatFileNameList($tparams);
		my $cnt=$#{$data};
		my $i=0;
		undef @CNT;
		while ($i <= $cnt)
		{
        	my $flatFile=$data->[$i];
			$flatFile.=".gz";
        	my $datafile=$mta->getLocalFlatFile($flatFile, $CLIENT[$c1ind]);
       		@CNT=build_file($datafile,$minday,$maxday,$ACTION->{'converter'},$client_id,$class_name,@CNT);
			unlink($datafile);
			$i++;
		}
		dumpCnt("Converter",$class_name,$CLIENT[$c1ind]);

		my $tparams={};
		$tparams->{'emailActionType'}="deliverable";
		$tparams->{'emailClass'}=$class_name;
		$tparams->{'minDay'}=$minday;
		$tparams->{'maxDay'}=$maxday;
		my $data=$mta->getFlatFileNameList($tparams);
		my $cnt=$#{$data};
		my $i=0;
		undef @CNT;
		while ($i <= $cnt)
		{
        	my $flatFile=$data->[$i];
			$flatFile.=".gz";
        	my $datafile=$mta->getLocalFlatFile($flatFile, $CLIENT[$c1ind]);
       		@CNT=build_file($datafile,$minday,$maxday,$ACTION->{'deliverable'},$client_id,$class_name,@CNT);
			unlink($datafile);
			$i++;
		}
		dumpCnt("Deliverables",$class_name,$CLIENT[$c1ind]);
	}
}
$mta->cleanupLocalFlatFileTempDirectory();
close(MAIL);

log_notice("Client: $client_group_id",0);

sub build_file 
{
	my ($input_file,$start,$end,$actionType,$client_id,$class_name,@RECCNT)=@_;
	my $zip;
	my $daycnt;
	my $lrListNum=[];
	my $line;
	my ($cwc3,$cwcid,$cwprogid,$cr,$landing_page);
	my $gender;
	my $age;
	my $hrInfo;

	my $qSelEm;
	log_notice("File <$input_file> <$total_cnt> <$start> <$end>",0);
	open(IN,"<$input_file") or return(@RECCNT); 
	RECORD: while (<IN>)
	{
		$line=$_;
        $line=~ s/^M//g;
        $line=~ s/\n//g;
        ($hrInfo->{email},$hrInfo->{eID},$hrInfo->{state},$hrInfo->{fname},$hrInfo->{lname},$hrInfo->{city},$zip,$daycnt,$hrInfo->{url},$hrInfo->{cdate},$hrInfo->{ip},$hrInfo->{rtime},$gender,$age) = split('\|',$line);
        $hrInfo->{rtime}=~ s/^M//g;
        $hrInfo->{rtime}=~ s/\n//g;
		$_=$hrInfo->{email};
		if (/@/)
		{
		}
		else
		{
			next RECORD;
		}
        if ($end == -1)
        {
        	next RECORD if $daycnt < $start;
        }
        else
        {
        	if (($daycnt < $start) or ($daycnt > $end))
            {
            	next RECORD;
			}
        }
		if ($daycnt == 0)
		{
			$ind=0;
		}
		else
		{
			$ind=int(($daycnt-1)/5);
		}
		$RECCNT[$ind]++;
		$total_cnt++;
	}
	close(IN);
	return(@RECCNT);
}
sub setLogFile {

	my ($date, $time)=get_current_datetime(1);
	
	return("/tmp/calc_v3.log.$date");	
	
}

sub get_current_datetime {
	my ($split)=@_;
	$split||=0;
	my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
	$month+=1; $year+=1900;
	$sec=(length($sec) < 2) ? "0$sec" : $sec;
	$min=(length($min) < 2) ? "0$min" : $min;
	$hr=(length($hr) < 2) ? "0$hr" : $hr;
	$day=(length($day) < 2) ? "0$day" : $day;
	$month=(length($month) < 2) ? "0$month" : $month;
	return ("$year-$month-$day", "$hr:$min:$sec") if $split == 1;
	return "$year-$month-$day $hr:$min:$sec";
}

sub log_or_execute {
	my ($q)=@_;
	
	log_notice("INFO : $q");
##
## 08/21/06 - JES - Added logic so only one version of code needed for internal and external servers
##
	$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to update db: " . DBI->errstr, 1) unless $DBHU->ping();
	unless ($hrINIT->{debug}) {
		$DBHU->do($q);
		if ($DBHU->err() && $DBHU->err() != 0) {
		my $err_msg=$DBHU->errstr();
			log_notice("ERROR: $err_msg <$q>");
		}
	}
}

sub prep_and_exec {
    my ($qSel, $lrParams)=@_;
    $lrParams||=[];

$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to update db: " . DBI->errstr, 1) unless $DBHU->ping();
    my $sth=$DBHU->prepare($qSel);
    $sth->execute(@$lrParams);
    return $sth;
}
sub prep_and_exec1 {
    my ($qSel, $lrParams)=@_;
    $lrParams||=[];

$DBHQ=DBI->connect('DBI:mysql:new_mail:slavedb.i.routename.com', 'db_readuser', 'Tr33Wat3r',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to slavedb db: " . DBI->errstr, 1) unless $DBHQ->ping();
    my $sth=$DBHQ->prepare($qSel);
    $sth->execute(@$lrParams);
    return $sth;
}

sub log_notice {

    my ($msg, $die)=@_;
    print "$timest ($$ ) - $msg\n";

} 

sub dumpCnt
{
	my ($ctype,$class_name,$client)=@_;
	my $crange;
	my $i=0;
	my $s1;
	my $username;
	$sql="select username from user where user_id=?";
	my $stha=prep_and_exec($sql,[$client]);
	($username)=$stha->fetchrow_array();
	$stha->finish();

	while ($i <= $#CNT)
	{
		$s1=$i*5;
		my $s2=($i+1)*5;
		$crange="$s1-$s2";
		print MAIL "$client,$username,$ctype,$class_name,$crange,$CNT[$i]\n";
		log_notice("$client,$username,$ctype,$class_name,$crange,$CNT[$i]",0);
		$i++;
	}
}

