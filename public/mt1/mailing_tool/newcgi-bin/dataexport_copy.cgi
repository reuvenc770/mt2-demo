#!/usr/bin/perl
# *****************************************************************************************
# dataexport_copy.cgi
#
# this page copies a DataExport 
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
my $newid;

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

# get fields from the form

my $gid= $query->param('gid');
my $exportType = $query->param('exportType');

my $location;

$sql ="insert into DataExport(fileName,client_group_id,profile_id,advertiser_id,ftpServer,ftpUser,ftpPassword,lastUpdated,lastUpdatedTime,serverID,status,recordCount,fieldsToExport,pid,exportType,ftpFolder,includeHeaders,otherField,otherValue,sendToImpressionWise,SendToImpressionwiseDays,outputFilename,IronCladGroupID,sendBluehornet,SendToEmail,NumberOfFiles,BusinessUnit,fullPostalOnly,addressOnly,doubleQuoteFields) select fileName,client_group_id,profile_id,advertiser_id,ftpServer,ftpUser,ftpPassword,lastUpdated,lastUpdatedTime,serverID,status,recordCount,fieldsToExport,pid,exportType,ftpFolder,includeHeaders,otherField,otherValue,sendToImpressionWise,SendToImpressionwiseDays,outputFilename,IronCladGroupID,sendBluehornet,SendToEmail,NumberOfFiles,BusinessUnit,fullPostalOnly,addressOnly,doubleQuoteFields from DataExport where exportID=$gid";
$rows = $dbhu->do($sql);

$sql="select LAST_INSERT_ID()";
$sth=$dbhu->prepare($sql);
$sth->execute();
($newid)=$sth->fetchrow_array();
$sth->finish();

$sql="insert into DataExportAdvertiser(exportID,advertiser_id) select $newid,advertiser_id from DataExportAdvertiser where exportID=$gid";
$rows = $dbhu->do($sql);
$sql="insert into DataExportCategory(exportID,categoryID) select $newid,categoryID from DataExportCategory where exportID=$gid";
$rows = $dbhu->do($sql);
$sql="insert into DataExportCountry(exportID,countryID) select $newid,countryID from DataExportCountry where exportID=$gid";
$rows = $dbhu->do($sql);
$sql="insert into DataExportESP(exportID,espID) select $newid,espID from DataExportESP where exportID=$gid";
$rows = $dbhu->do($sql);
$sql="insert into DataExportSeed(exportID,email_addr) select $newid,email_addr from DataExportSeed where exportID=$gid";
$rows = $dbhu->do($sql);
$sql="insert into DataExportOtherFtp(exportID,ftpServer,ftpUser,ftpPassword,ftpFolder) select $newid,ftpServer,ftpUser,ftpPassword,ftpFolder from DataExportOtherFtp where exportID=$gid";
$rows = $dbhu->do($sql);

print "Location: dataexport_main.cgi?pid=$newid&exportType=$exportType\n\n";
$pms->clean_up();
exit(0);
