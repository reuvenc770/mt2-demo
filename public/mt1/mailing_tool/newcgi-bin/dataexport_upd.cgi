#!/usr/bin/perl
# *****************************************************************************************
# dataexport_upd.cgi
#
# this page adds a updates DataExport 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $userid;
my $dname;
my $fields="";

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}
my $BusinessUnit;
$sql="select BusinessUnit from UserAccounts where user_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($user_id);
($BusinessUnit)=$sth->fetchrow_array();
$sth->finish();

# get fields from the form
my $pid= $query->param('pid');
my $seeds=$query->param('seeds');
my $exportType= $query->param('exportType');
if ($exportType eq "")
{
	$exportType="Regular";
}
my @aid= $query->param('aid');

my $filename= $query->param('pname');
my $outputFilename= $query->param('outname');
my $suppressedFilename = $query->param('suppname');
my $ConfirmEmail= $query->param('ConfirmEmail');
my $ftpFolder= $query->param('ftpFolder');
my $ftpServer = $query->param('ftpServer');
my $ftpUser = $query->param('ftpUser');
my $ftpPassword = $query->param('ftpPassword');
my $SendToEmail= $query->param('SendToEmail');
my $NumberOfFiles = $query->param('NumberOfFiles');
if ($NumberOfFiles eq "")
{
	$NumberOfFiles=1;
}
if ($filename eq "")
{
	util::logerror("Filename cannot be blank");
    $pms->clean_up();
    exit(0);
}
if ($exportType eq "Cleanse")
{
	$ftpFolder="Outgoing";
	if ($outputFilename eq "")
	{
		$outputFilename=$filename;
	}
	if ($#aid < 0)
	{
		print "Content-Type: text/html\n\n";
		print<<"end_of_html";
<html><body><center><font color=red><h3>You must select at least one Advertiser from the Advertiser Suppression List.</h3></font></center></body></html>
end_of_html
		exit();
	}
	
}
my $gid= $query->param('gid');
my $profileid= $query->param('profileid');
my $IronCladGroupID= $query->param('IronCladGroupID');
if ($IronCladGroupID eq "")
{
	$IronCladGroupID=0;
}
my $sendBluehornet =$query->param('sendBluehornet');
if ($sendBluehornet eq "")
{
	$sendBluehornet="N";
}
my $fullPostalOnly =$query->param('fullPostalOnly');
if ($fullPostalOnly eq "")
{
	$fullPostalOnly="N";
}
my $addressOnly =$query->param('addressOnly');
if ($addressOnly eq "")
{
	$addressOnly="N";
}
my $frequency=$query->param('frequency');
if ($frequency eq "")
{
	$frequency="Daily";
}
my $doubleQuoteFields=$query->param('doubleQuoteFields');
if ($doubleQuoteFields eq "")
{
	$doubleQuoteFields="N";
}
my $includeHeaders=$query->param('includeHeaders');
if ($includeHeaders eq "")
{
	$includeHeaders="N";
}
my $repull=$query->param('repull');
if ($repull eq "")
{
	$repull="N";
}
my $otherField=$query->param('otherField');
$otherField=~s/'/''/g;
my $otherValue=$query->param('otherValue');
$otherValue=~s/'/''/g;
my $SendToImpressionwiseDays="NNNNNNN";
my $imp_monday = $query->param('imp_monday');
my $imp_tuesday = $query->param('imp_tuesday');
my $imp_wednesday = $query->param('imp_wednesday');
my $imp_thursday = $query->param('imp_thursday');
my $imp_friday = $query->param('imp_friday');
my $imp_saturday = $query->param('imp_saturday');
my $imp_sunday = $query->param('imp_sunday');
if ($imp_monday ne "")
{
	substr($SendToImpressionwiseDays,0,1) = 'Y';
}
if ($imp_tuesday ne "")
{
	substr($SendToImpressionwiseDays,1,1) = 'Y';
}
if ($imp_wednesday ne "")
{
	substr($SendToImpressionwiseDays,2,1) = 'Y';
}
if ($imp_thursday ne "")
{
	substr($SendToImpressionwiseDays,3,1) = 'Y';
}
if ($imp_friday ne "")
{
	substr($SendToImpressionwiseDays,4,1) = 'Y';
}
if ($imp_saturday ne "")
{
	substr($SendToImpressionwiseDays,5,1) = 'Y';
}
if ($imp_sunday ne "")
{
	substr($SendToImpressionwiseDays,6,1) = 'Y';
}
my @F= $query->param('fields');
if ($exportType eq "Cleanse")
{
	$F[0]="email_addr";
}
my @ESP= $query->param('esp');
foreach my $fld (@F)
{
	$fields.=$fld.",";
}
chop($fields);
my @scatid = $query->param('scatid');
my @scountryID = $query->param('scountryID');

my $aidcnt=$#aid;
$aidcnt++;
if ($pid > 0)
{
	$sql = "update DataExport set fileName='$filename',client_group_id=$gid,profile_id=$profileid,advertiser_id=$aidcnt,fieldsToExport='$fields',ftpFolder='$ftpFolder',SendToEmail='$SendToEmail',includeHeaders='$includeHeaders',otherField='$otherField',otherValue='$otherValue',SendToImpressionwiseDays='$SendToImpressionwiseDays',IronCladGroupID=$IronCladGroupID,sendBluehornet='$sendBluehornet',NumberOfFiles=$NumberOfFiles,ftpServer='$ftpServer',ftpUser='$ftpUser',ftpPassword='$ftpPassword',BusinessUnit='$BusinessUnit',fullPostalOnly='$fullPostalOnly',addressOnly='$addressOnly',doubleQuoteFields='$doubleQuoteFields',frequency='$frequency'";
	if ($repull eq "Y")
	{
		$sql.=",lastUpdated=date_sub(curdate(),interval 1 day),serverID=0,pid=0";
	}
	$sql.=" where exportID=$pid";
}
else
{
	if ($exportType ne "Cleanse")
	{
		$sql = "insert into DataExport(fileName,client_group_id,profile_id,advertiser_id,fieldsToExport,exportType,ftpFolder,includeHeaders,otherField,otherValue,SendToImpressionwiseDays,IronCladGroupID,sendBluehornet,SendToEmail,NumberOfFiles,ftpServer,ftpUser,ftpPassword,BusinessUnit,fullPostalOnly,addressOnly,doubleQuoteFields,frequency) values('$filename',$gid,$profileid,$aidcnt,'$fields','$exportType','$ftpFolder','$includeHeaders','$otherField','$otherValue','$SendToImpressionwiseDays',$IronCladGroupID,'$sendBluehornet','$SendToEmail',$NumberOfFiles,'$ftpServer','$ftpUser','$ftpPassword','$BusinessUnit','$fullPostalOnly','$addressOnly','$doubleQuoteFields','$frequency')"; 
	}
	else
	{
		$sql = "insert into DataExport(fileName,client_group_id,profile_id,advertiser_id,fieldsToExport,exportType,ftpFolder,includeHeaders,otherField,otherValue,ftpServer,ftpUser,ftpPassword,SendToImpressionwiseDays,outputFilename,sendBluehornet,SendToEmail,NumberOfFiles,fullPostalOnly,suppressedFilename,addressOnly,doubleQuoteFields,ConfirmEmail) values('$filename',0,0,$aidcnt,'email_addr','$exportType','$ftpFolder','N','','','ftp.aspiremail.com','espkenaspiremail','pHAquv2f','$SendToImpressionwiseDays','$outputFilename','$sendBluehornet','$SendToEmail',$NumberOfFiles,'$fullPostalOnly','$suppressedFilename','$addressOnly','$doubleQuoteFields','$ConfirmEmail')"; 
	}
}
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating DataExport record $sql: $errmsg");
	exit(0);
}
if ($pid == 0)
{
	$sql="select LAST_INSERT_ID()";
	my $sth=$dbhu->prepare($sql);
	$sth->execute();
	($pid)=$sth->fetchrow_array();
	$sth->finish();
}
else
{
	$sql="delete from DataExportAdvertiser where exportID=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from DataExportSeed where exportID=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from DataExportCategory where exportID=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from DataExportCountry where exportID=$pid";
	$rows=$dbhu->do($sql);
	if ($exportType eq "ESP")
	{
		$sql="delete from DataExportESP where exportID=$pid";
		$rows=$dbhu->do($sql);
	}
}
foreach my $c (@scatid)
{
	$sql="insert into DataExportCategory(exportID,categoryID) values($pid,$c)";
	$rows=$dbhu->do($sql);
}
foreach my $c (@scountryID)
{
	$sql="insert into DataExportCountry(exportID,countryID) values($pid,$c)";
	$rows=$dbhu->do($sql);
}
foreach my $a (@aid)
{
	$sql="insert into DataExportAdvertiser(exportID,advertiser_id) values($pid,$a)";
	$rows=$dbhu->do($sql);
}
if ($seeds ne '')
{
    $seeds =~ s/[ \n\r\f\t]/\|/g ;
    $seeds =~ s/\|{2,999}/\|/g ;
    my @em= split '\|', $seeds;
    foreach my $e (@em)
    {
        $sql="insert into DataExportSeed(exportID,email_addr) values($pid,'$e')";
        $rows=$dbhu->do($sql);
    }
}
if ($exportType eq "ESP")
{
	foreach my $e (@ESP)
	{
		$sql="insert into DataExportESP(exportID,espID) values($pid,$e)";
		$rows=$dbhu->do($sql);
	}
}
print "Location: dataexport_list.cgi?exportType=$exportType\n\n";
$pms->clean_up();
exit(0);
