#!/usr/bin/perl

# *****************************************************************************************
# mainmenu.cgi
#
# History
# *****************************************************************************************

#------------- include Perl Modules ------------------
use strict;
use CGI;
use HTML::Template;
use util;

#------- get some objects to use later ---------------
my $util = util->new;
my $query = CGI->new;
my $t=$query->param('t');
my $username;

my $images = $util->get_images_url;
my $TEMPLATEDIR="/var/www/html/newcgi-bin/templates/";

#------ connect to the util database -------------------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
	print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

#--------------------------------------
# Get User_Type, UserName from user 
#--------------------------------------
my $dataExportTool;
my $BusinessUnit;
my $SetupDeploys;
my $SetupLists;
my $SetupAdvertisers;
my $sql = "select username,dataExportTool,BusinessUnit,SetupDeploys,SetupLists,SetupAdvertisers from UserAccounts where user_id = ?";
my $sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username,$dataExportTool,$BusinessUnit,$SetupDeploys,$SetupLists,$SetupAdvertisers) = $sth->fetchrow_array();
$sth->finish();
if ($SetupDeploys eq "Y")
{
	$SetupDeploys=0;
}
else
{
	$SetupDeploys=1;
}
if ($SetupAdvertisers eq "Y")
{
	$SetupAdvertisers=0;
}
else
{
	$SetupAdvertisers=1;
}
if ($SetupLists eq "Y")
{
	$SetupLists=0;
}
else
{
	$SetupLists=1;
}

if ($BusinessUnit eq "Data")
{
	$TEMPLATEDIR="/var/www/html/newcgi-bin/templates_data/";
	if ($t eq "")
	{
		$t="lists";
	}
}
if ($t eq "")
{
	$t="menu";
}
my $template_file=$TEMPLATEDIR.$t.".tmpl";
if (-e "$template_file")
{
    # open the html template
    my $template = HTML::Template->new(die_on_bad_params => '0', filename => $template_file);
    $template->param(USERNAME => $username);
    
    $template->param('isZeta' => 0);
    $template->param('hasdataExport' => 0);
    
    if($username eq 'zeta')
    {
    	$template->param('isZeta' => 1);	
    }
	if ($dataExportTool eq "Y")
	{
    	$template->param('hasdataExport' => 1);
	}
	my $espStr="";
	my $sql="select espName,espLabel from ESP where espStatus='A' and userDisplay = 1 order by espLabel";
	my $sth=$dbhu->prepare($sql);
	$sth->execute();
	my $espName;
	my $espLabel;
	while (($espName,$espLabel)=$sth->fetchrow_array())
	{
		$espStr.=qq^<a href="/cgi-bin/expertsender_main.cgi?esp=$espName">Create $espLabel HTML</a>^;
	}
	$sth->finish();
   	$template->param('ESPS' => $espStr);
    
    $template->param('isExternalUser' => $util->getUserData({'userID' => $user_id})->{'isExternalUser'});
	if ($BusinessUnit eq "Orange")
	{
   		$template->param('SetupDeploys' => $SetupDeploys);
    	$template->param('SetupAdvertisers' => $SetupAdvertisers);
	}
    $template->param('SetupLists' => $SetupLists);
    # send the obligatory Content-Type and print the template output
    print "Content-Type: text/html\n\n", $template->output;
}

