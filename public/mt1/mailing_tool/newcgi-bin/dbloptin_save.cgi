#!/usr/bin/perl
# *****************************************************************************************
# dbloptin_save.cgi
#
# this page is for saving Double Option campaigns 
#
# History
# Jim Sobeck, 03/31/08, Creation
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
my $rows;
my $camp_id;
my ($uid,$fname,$company);
my $upload_dir="/var/www/util/tmp";

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
        print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
my $id=$query->param('id');
my $cname=$query->param('cname');
$cname=$dbhq->quote($cname);
my $client_id=$query->param('client_id');
my $cday=$query->param('cday');
my $subject=$query->param('subject');
$subject=$dbhq->quote($subject);
my $fromline=$query->param('fromline');
$fromline=$dbhq->quote($fromline);
my $header_image=$query->param('header_image');
$header_image =~ s/.*[\/\\](.*)/$1/;
my $content_str=$query->param('content_str');
$content_str=$dbhq->quote($content_str);
my $template_id=$query->param('template_id');
my $submit=$query->param('submit');

if ($submit eq "delete img")
{
	$sql="update double_optin set header_image='' where id=$id";
	$rows=$dbhu->do($sql);
	$header_image="";
}
if ($header_image ne "")
{
    my $upload_filehandle = $query->upload("header_image");
	my $a;
	my $ext;
	($a,$ext)=split('\.',$header_image);
    $header_image = $client_id . "_". $cday . "_" . $a. "." . $ext; 
    open UPLOADFILE, ">$upload_dir/$header_image";
    while ( <$upload_filehandle> )
    {
        print UPLOADFILE;
    }
    close UPLOADFILE;
	my $BASE_DIR;
	my $sth1;
	$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($BASE_DIR) = $sth1->fetchrow_array();
	$sth1->finish;
    my @args = ("${BASE_DIR}newcgi-bin/cp_dbloptin.sh $header_image");
    system(@args) == 0 or die "system @args failed: $?";
}
if ($id > 0)
{
	$sql="update double_optin set campaign_name=$cname,cday=$cday,subject=$subject,fromline=$fromline,content_str=$content_str,template_id=$template_id,client_id=$client_id where id=$id";
	$rows=$dbhu->do($sql);
}
else
{
	$sql="insert into double_optin(campaign_name,client_id,cday,subject,fromline,content_str,template_id) values($cname,$client_id,$cday,$subject,$fromline,$content_str,$template_id)";
	$rows=$dbhu->do($sql);
	#
	# Get the id for the record just added
	#
	$sql="select max(id) from double_optin where client_id=$client_id and cday=$cday and campaign_name=$cname";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($id)=$sth->fetchrow_array();
	$sth->finish();
	#
	# Create campaign record
	#
	$sql="insert into campaign(campaign_name,status,created_datetime,campaign_type) values($cname,'C',now(),'DAILY')";
	$rows=$dbhu->do($sql);
	#
	$sql="select max(campaign_id) from campaign where campaign_name=$cname and status='C'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($camp_id)=$sth->fetchrow_array();
	$sth->finish();
	$sql="update double_optin set campaign_id=$camp_id where id=$id";
	$rows=$dbhu->do($sql);
}
if ($header_image ne "")
{
	$sql="update double_optin set header_image='$header_image' where id=$id";
	$rows=$dbhu->do($sql);
}
#
if ($submit eq "preview it")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/dbloptin_preview.cgi?id=$id", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/dbloptin_list.cgi";
</script>
</body></html>
end_of_html
}
else
{
	print "Location: /cgi-bin/dbloptin_list.cgi\n\n";
}
