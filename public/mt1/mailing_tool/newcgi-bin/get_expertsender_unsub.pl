#!/usr/bin/perl
#################################################################
####   ExpertSender.pm  - utility package for ExpertSender feed 
####
#################################################################

use strict;
use LWP 5.64;
use XML::Simple;
use Data::Dumper;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true
my $util = util->new;
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
my $util = util->new;
my $browser = LWP::UserAgent->new;
my $cid;
my $listid;
my $LISTID;
my $sql;
my $rows;
my $sth;
my $sth2;
my $sdate;
my $edate;

if ($ARGV[0] ne "")
{
	$sdate=$ARGV[0];
	$edate=$ARGV[1];
}
else
{
	$sql="select date_sub(curdate(),interval 1 day),date_sub(curdate(),interval 1 day)";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($sdate,$edate)=$sth->fetchrow_array();
	$sth->finish();
}

$LISTID->{1099}=9;
$LISTID->{1163}=6;
$LISTID->{1095}=10;
$LISTID->{1087}=11;
$LISTID->{1164}=19;
$LISTID->{1067}=23;
$LISTID->{1101}=24;
$LISTID->{1103}=29;
$LISTID->{1091}=77;
$LISTID->{1085}=104;
$LISTID->{1117}=124;
$LISTID->{1093}=135;
$LISTID->{1119}=148;
$LISTID->{1086}=174;
$LISTID->{1109}=185;
$LISTID->{1069}=236;
$LISTID->{1071}=299;
$LISTID->{1110}=310;
$LISTID->{1104}=319;
$LISTID->{1105}=320;
$LISTID->{1106}=322;
$LISTID->{1107}=323;
$LISTID->{1108}=325;
$LISTID->{1084}=357;
$LISTID->{1072}=482;
$LISTID->{1102}=503;
$LISTID->{1096}=504;
$LISTID->{1097}=531;
$LISTID->{1113}=563;
$LISTID->{1114}=564;
$LISTID->{1094}=586;
$LISTID->{1089}=600;
$LISTID->{1090}=601;
$LISTID->{1075}=607;
$LISTID->{1100}=614;
$LISTID->{1076}=616;
$LISTID->{1124}=619;
$LISTID->{1079}=643;
$LISTID->{1080}=644;
$LISTID->{1069}=254;
$LISTID->{1115}=262;
$LISTID->{1092}=212;
$LISTID->{1082}=301;
$LISTID->{1111}=366;
$LISTID->{1074}=373;
$LISTID->{1073}=385;
$LISTID->{1078}=470;
$LISTID->{1116}=397;
$LISTID->{1070}=506;
$LISTID->{1088}=527;
$LISTID->{1081}=183;

  	my $url2="http://service.expertsender.com/Api/Removals?apiKey=a8c3at41ABunAhu33srj&startDate=".$sdate."&endDate=".$edate;
	my $response = $browser->get( $url2);
    my $parser=XML::Simple->new();
    my $data = $parser->XMLin($response->content);
	my $i=0;
	my $notdone=1;
	while ($notdone)
	{
		if ($data->{Data}->{Removals}->{Removal}[$i]->{Email})
		{
			remove_user($data->{Data}->{Removals}->{Removal}[$i]->{Email},$data->{Data}->{Removals}->{Removal}[$i]->{ListId});
			$i++;
		}
		else
		{
			$notdone=0;
		}
	}
#	foreach my $arr (\$data->{Data}->{Removals}->{Removal})
#	{
#		print "$arr->{Email}\n";
#	}
#	print Dumper($data);
sub remove_user
{
	my ($em,$listid)=@_;
	my $emailTable;	
	my $cid=$LISTID->{$listid};
	my $emailid;
	my $cstatus;
	my $user_id;

	if ($cid)
	{
		print "Client $cid - $em\n";
		$sql = "select email_user_id,email_list.status,user_id, '1' as emailTable from email_list,list where email_addr= '$em' and email_list.list_id=list.list_id and list.user_id=$cid UNION select email_user_id,international_email_list.status,user_id, '2' as emailTable from international_email_list,list where email_addr= '$em' and international_email_list.list_id=list.list_id and list.user_id=$cid";
		$sth2 = $dbhu->prepare($sql) ;
		$sth2->execute();
		while (($emailid,$cstatus,$user_id,$emailTable) = $sth2->fetchrow_array())
		{
			if ($cstatus eq "A") 
			{
				my $emailList = 'email_list';
			
				if($emailTable == 2 ){
					$emailList = 'international_email_list';
				}
			
				$sql = "update $emailList set status = 'U', unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$emailid and status ='A'"; 
				$rows = $dbhu->do($sql) ;
			
				$sql = "insert into unsub_log(email_addr,unsub_date,client_id) values('$em',curdate(),$user_id)";
				$rows = $dbhu->do($sql);
			
			}
		}
		$sth2->finish();
	}
}
