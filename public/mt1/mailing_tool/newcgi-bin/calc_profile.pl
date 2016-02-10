#!/usr/bin/perl 
#
use strict;
use DBI;
use Sys::Hostname;
use MIME::Base64;
use vars qw($hrINIT $DBHU $hrSVR $hrADV $FILE $LIST);
use lib "/var/www/bin";
use mta;
my $mta= mta->new;
my $total_cnt;
my $open_cnt;
my $DAY_SECONDS=24*3600;
my $ret_stat;
my $sortfile;
my $DUP={};
my $old_creativeid;
my $start_date;
my $end_date;
my $ostart_date;
my $oend_date;
my $cstart_date;
my $cend_date;

$|=1;
my $campaign_str;
my $temp_id;
my $got_deal;
my $camp_id;
my $sql;
my $sth;
my $sent_seeds;
my $send_adv_seeds;
my $domain_id;
my $MAX_CAMPS=1;
my $camp_cnt;
my $close;
my $FLATDIR="/var/www/util/data";
my $lsdName;
my $qSel;
my $qSelLst;
my $sthLst;
my $all_recs;

$|=1;
my $campaign_str;
my $temp_id;
my $got_deal;
my $camp_id;
my $sql;
my $sth;
my $sent_seeds;
my $send_adv_seeds;
my $domain_id;
my $MAX_CAMPS=1;
my $camp_cnt;
my $close;
my $FLATDIR="/var/www/util/data";
my $lsdName;
my $qSel;
my $qSelLst;
my $sthLst;
my $all_recs;
my $temp_flag;
my $temp_max;
my $old_total;
my $articleid;
my $blurbid;
my $headerid;
my $darr_cnt;
my @CLASS;
my $priority;
my $class_name;
my $master_str;
my $LINKID;
my $startmonth;
my $endmonth;
my $monthcnt;
my $EXISP={};
my $hrProf;

$FILE=$LIST='';
my $host=hostname();

#set log file
$hrINIT->{'log'} = setLogFile();

$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to db: " . DBI->errstr, 1);

cleanup_old_ones();

$sql="select check_id,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date from UniqueCheck where status='A' and type='New' limit 1 for update";
eval
{
	$DBHU->begin_work;
	my $sth=prep_and_exec($sql);
	my $err_msg = $DBHU->errstr();
		
	if (($hrProf) = $sth->fetchrow_hashref())
	{
		$sth->finish();
	}
	else
	{
		$sth->finish();
		$hrProf->{check_id}=0;
	}
	if ($hrProf->{check_id} > 0)
	{
		##  set selected campaign to pending
		my $qUpdStat1=qq^UPDATE UniqueCheck SET status='P' WHERE check_id=$hrProf->{check_id}^;
		log_or_execute($qUpdStat1);
	}
	else
	{
		exit();
	}
	$DBHU->commit;
};
if ($@)
{
	local $DBHU->{RaiseError}=0;
	$DBHU->rollback;
	log_notice("ERROR: rolling back",0);
	exit();
}

## Get number of email classes
$sql="select lower(class_name) from email_class,UniqueCheckIsp where email_class.status='Active' and email_class.class_id=UniqueCheckIsp.class_id and UniqueCheckIsp.check_id=?";
my $sth1=prep_and_exec($sql,[$hrProf->{check_id}]);
my $cind;
my $class_id;
$cind=0;
while (($class_id)=$sth1->fetchrow_array())
{
	$CLASS[$cind]=$class_id;
	$cind++;
}
$sth1->finish();
log_notice("INFO: Number of classes - $#CLASS",0);

#
# check dates
#
if (($hrProf->{opener_start_date} ne '') and ($hrProf->{opener_start_date} ne "0000-00-00"))
{
	if (($hrProf->{opener_end_date} ne '') and ($hrProf->{opener_end_date} ne "0000-00-00"))
	{
		$sql="select datediff(curdate(),'$hrProf->{opener_end_date}'),datediff(curdate(),'$hrProf->{opener_start_date}')";
	}
	else
	{
		$sql="select -1,datediff(curdate(),'$hrProf->{opener_start_date}')";
	}
	my $sthServp = prep_and_exec($sql);
	($hrProf->{opener_start},$hrProf->{opener_end})=$sthServp->fetchrow_array();
	$sthServp->finish();
}
if (($hrProf->{clicker_start_date} ne '') and ($hrProf->{clicker_start_date} ne "0000-00-00"))
{
	if (($hrProf->{clicker_end_date} ne '') and ($hrProf->{clicker_end_date} ne "0000-00-00"))
	{
		$sql="select datediff(curdate(),'$hrProf->{clicker_end_date}'),datediff(curdate(),'$hrProf->{clicker_start_date}')";
	}
	else
	{
		$sql="select -1,datediff(curdate(),'$hrProf->{clicker_start_date}')";
	}
	my $sthServp = prep_and_exec($sql);
	($hrProf->{clicker_start},$hrProf->{clicker_end})=$sthServp->fetchrow_array();
	$sthServp->finish();
}
if (($hrProf->{deliverable_start_date} ne '') and ($hrProf->{deliverable_start_date} ne "0000-00-00"))
{
	if (($hrProf->{deliverable_end_date} ne '') and ($hrProf->{deliverable_end_date} ne "0000-00-00"))
	{
		$sql="select datediff(curdate(),'$hrProf->{deliverable_end_date}'),datediff(curdate(),'$hrProf->{deliverable_start_date}')";
	}
	else
	{
		$sql="select -1,datediff(curdate(),'$hrProf->{deliverable_start_date}')";
	}
	my $sthServp = prep_and_exec($sql);
	($hrProf->{deliverable_start},$hrProf->{deliverable_end})=$sthServp->fetchrow_array();
	$sthServp->finish();
}

$sql="select client_id,record_cnt,record_order,open_record_cnt,click_record_cnt from UniqueCheckClient where check_id=?"; 
my $sthq=$DBHU->prepare($sql);
$sthq->execute($hrProf->{check_id});
my $tcid;
my $tclient;
my $link_id;
my $record_cnt;
my $record_order;
my $i=0;
my $client_cnt;
my $open_record_cnt;
my $click_record_cnt;
my $deliverable_record_cnt;
open(RPT,">$FLATDIR/$hrProf->{check_id}.log");
while (($tclient,$deliverable_record_cnt,$record_order,$open_record_cnt,$click_record_cnt)=$sthq->fetchrow_array())
{
	$client_cnt=0;
	$total_cnt=0;
	open(OUT,">$FLATDIR/$hrProf->{check_id}.txt");
	if ((($hrProf->{opener_start} != 0) or ($hrProf->{opener_end} != 0)) and ($open_record_cnt > 0))
	{
		foreach $class_name (@CLASS)
		{
			my $tparams={};
			$tparams->{'emailActionType'}="opener";
			$tparams->{'emailClass'}=$class_name;
			$tparams->{'minDay'}=$hrProf->{opener_start};
			$tparams->{'maxDay'}=$hrProf->{opener_end};
			my $data=$mta->getFlatFileNameList($tparams);
			my $cnt=$#{$data};
			my $i=0;
			while ($i <= $cnt)
			{
        		my $flatFile=$data->[$i];
            	my $retstat=$mta->getFlatFile($flatFile, $tclient);
       			$total_cnt=build_file("$FLATDIR/$tclient/$flatFile",$hrProf->{opener_start},$hrProf->{opener_end},$total_cnt,$open_record_cnt); 
				$i++;
			}
		}
	}

	close(OUT);
	if ($total_cnt >= $open_record_cnt)
	{	
		my $infile=$FLATDIR."/".$hrProf->{check_id}.".txt";
		my $sortfile=$FLATDIR."/".$hrProf->{check_id}.".sorted";
		log_notice("INFO : Starting sort process");
		system("sort -n -S 5M -T /tmp -T /dev/shm $infile -o $sortfile");
		log_notice("INFO : Finished sort process");
		unlink($infile);
		($ostart_date,$oend_date)=CalcStartEnd($sortfile,$tclient,$open_record_cnt,$hrProf->{opener_start_date});
	}
	else
	{
		$ostart_date="NOTENOUGH";
		$oend_date=$total_cnt;
	}

	$total_cnt=0;
	open(OUT,">$FLATDIR/$hrProf->{check_id}.txt");
	if ((($hrProf->{clicker_start} != 0) or ($hrProf->{clicker_end} != 0)) and ($click_record_cnt > 0))
	{
		foreach $class_name (@CLASS)
		{
			my $tparams={};
			$tparams->{'emailActionType'}="clicker";
			$tparams->{'emailClass'}=$class_name;
			$tparams->{'minDay'}=$hrProf->{clicker_start};
			$tparams->{'maxDay'}=$hrProf->{clicker_end};
			my $data=$mta->getFlatFileNameList($tparams);
			my $cnt=$#{$data};
			my $i=0;
			while ($i <= $cnt)
			{
        		my $flatFile=$data->[$i];
            	my $retstat=$mta->getFlatFile($flatFile, $tclient);
   				$total_cnt=build_file("$FLATDIR/$tclient/${flatFile}",$hrProf->{clicker_start},$hrProf->{clicker_end},$total_cnt,$click_record_cnt);
				$i++;
			}
		}
	}

	close(OUT);
	if ($total_cnt >= $click_record_cnt)
	{	
		my $infile=$FLATDIR."/".$hrProf->{check_id}.".txt";
		my $sortfile=$FLATDIR."/".$hrProf->{check_id}.".sorted";
		log_notice("INFO : Starting sort process");
		system("sort -n -S 5M -T /tmp -T /dev/shm $infile -o $sortfile");
		log_notice("INFO : Finished sort process");
		unlink($infile);
		($cstart_date,$cend_date)=CalcStartEnd($sortfile,$tclient,$click_record_cnt,$hrProf->{clicker_start_date});
	}
	else
	{
		$cstart_date="NOTENOUGH";
		$cend_date=$total_cnt;
	}
	$total_cnt=0;
	open(OUT,">$FLATDIR/$hrProf->{check_id}.txt");
	if (($hrProf->{deliverable_start} != 0) or ($hrProf->{deliverable_end} != 0))
	{
#
		foreach $class_name (@CLASS)
		{
			my $tparams={};
			$tparams->{'emailActionType'}="deliverable";
			$tparams->{'emailClass'}=$class_name;
			$tparams->{'minDay'}=$hrProf->{deliverable_start};
			$tparams->{'maxDay'}=$hrProf->{deliverable_end};
			my $data=$mta->getFlatFileNameList($tparams);
			my $cnt=$#{$data};
			my $i=0;
			while ($i <= $cnt)
			{
        		my $flatFile=$data->[$i];
            	my $retstat=$mta->getFlatFile($flatFile, $tclient);
   				$total_cnt=build_file("$FLATDIR/$tclient/${flatFile}",$hrProf->{deliverable_start},$hrProf->{deliverable_end},$total_cnt,$deliverable_record_cnt);
				$i++;
			}
		}
	}
	close(OUT);

	if ($total_cnt >= $deliverable_record_cnt)
	{
		my $infile=$FLATDIR."/".$hrProf->{check_id}.".txt";
		my $sortfile=$FLATDIR."/".$hrProf->{check_id}.".sorted";
		log_notice("INFO : Starting sort process");
		system("sort -n -S 5M -T /tmp -T /dev/shm $infile -o $sortfile");
		log_notice("INFO : Finished sort process");

		($start_date,$end_date)=CalcStartEnd($sortfile,$tclient,$deliverable_record_cnt,$hrProf->{deliverable_start_date});
		unlink($sortfile);
		unlink($infile);
	}
	else
	{
		$start_date="NOTENOUGH";
		$end_date=$total_cnt;
	}
	print RPT "$tclient,$ostart_date,$oend_date,$cstart_date,$cend_date,$start_date,$end_date,\n";
}
close(RPT);
$sql="update UniqueCheck set status='F' where check_id=$hrProf->{check_id}";
log_or_execute($sql);


sub build_file 
{
	my ($input_file,$start,$end,$total_cnt,$record_cnt)=@_;
	my $zip;
	my $daycnt;
	my $hrInfo;
	my $line;
	my ($cwc3,$cwcid,$cwprogid,$cr,$landing_page);

	my $qSelEm;
	log_notice("File <$input_file> <$total_cnt> <$start> <$end>",0);
#	if ($total_cnt >= $record_cnt)
#	{
#		return $total_cnt;
#	}
	open(IN,"<$input_file") or return $total_cnt; 
	RECORD: while (<IN>)
	{
		$line=$_;
        $line=~ s/^M//g;
        $line=~ s/\n//g;
        ($hrInfo->{email},$hrInfo->{eID},$hrInfo->{state},$hrInfo->{fname},$hrInfo->{lname},$hrInfo->{city},$zip,$daycnt,$hrInfo->{url},$hrInfo->{cdate},$hrInfo->{ip},$hrInfo->{rtime}) = split('\|',$line);
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
		print OUT "$daycnt\n";		
		$total_cnt++;
	}
	close(IN);
	return $total_cnt;
}
sub setLogFile {

	my ($date, $time)=get_current_datetime(1);
	
	return("/tmp/calc_profile.log.$date");	
	
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
	$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to update db: " . DBI->errstr, 1) unless $DBHU->ping();
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

$DBHU=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V',{RaiseError => 1, PrintError => 0,ShowErrorStatement => 1}) or log_notice("ERROR : can't connect to update db: " . DBI->errstr, 1) unless $DBHU->ping();
    my $sth=$DBHU->prepare($qSel);
    $sth->execute(@$lrParams);
    return $sth;
}

sub CalcStartEnd
{
	my ($input_file,$tclient,$record_cnt,$tdate)=@_;
	my $cnt;
	my $end_date;
	my $start_date;
	my $sql;
	my $sth;
	my $line;

	$cnt=0;
	open(IN,"<$input_file") or return ; 
	my $notdone=1;
	while (<IN>)
	{
		$line=$_;
		chop($line);
		if ($line eq "")
		{
			next;
		}
		$cnt++;
		if ($cnt == 1)
		{
			if (($tdate ne '') and ($tdate ne "0000-00-00"))
			{
				$sql="select date_sub(curdate(),interval $line day)";
				$sth=$DBHU->prepare($sql);
				$sth->execute();
				($end_date)=$sth->fetchrow_array();
				$sth->finish();
			}
			else
			{
				$start_date=$line;
			}
		}
		if ($cnt == $record_cnt)
		{
			if (($tdate ne '') and ($tdate ne "0000-00-00"))
			{
				$sql="select date_sub(curdate(),interval $line day)";
				$sth=$DBHU->prepare($sql);
				$sth->execute();
				($start_date)=$sth->fetchrow_array();
				$sth->finish();
			}
			else
			{
				$end_date=$line;
			}
			close(IN);
			return($start_date,$end_date);
		}
	}
	close(IN);
}

sub log_notice {

    my ($msg, $die)=@_;
    open (LOG, ">>$hrINIT->{'log'}") or die "can't open log: $!";
    print LOG "($$ ) - $msg\n";
    close LOG;

    #script died but we want to capture error before close.
    if($die){
    	exit;
    } #end if

} 

sub cleanup_old_ones
{
	my $sql;
	my $sth;
	my $rows;
	my $check_id;
	
	$sql="select check_id from UniqueCheck where check_date < date_sub(curdate(),interval 3 day)";
	$sth=$DBHU->prepare($sql);
	$sth->execute();
	while (($check_id)=$sth->fetchrow_array())
	{
		$sql="delete from UniqueCheckIsp where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheckClient where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheckCustom where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheckSeed where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheckUrl where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheckZip where check_id=$check_id";
		$rows=$DBHU->do($sql);
		$sql="delete from UniqueCheck where check_id=$check_id";
		$rows=$DBHU->do($sql);
		my $tfile="$FLATDIR/$hrProf->{check_id}.txt";
		unlink($tfile);
		$tfile="$FLATDIR/$hrProf->{check_id}.log";
		unlink($tfile);
	}
	$sth->finish();
}
