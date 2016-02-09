#!/usr/bin/perl
#===============================================================================
# Purpose: Edit template data 
# Name   : template_disp.cgi 
#
#--Change Control---------------------------------------------------------------
# 05/31/07  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use lib('/var/www/html/newcgi-bin');
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $template_name;
my $templateCode;
my $nl_id = $query->param('nl_id');
my $mode = $query->param('mode');
if ($mode eq "A")
{
	$nl_id=0;
}
my $addr;
my $email_addr;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

$util->getUserData({'userID' => $user_id});

my $userDataRestrictionWhereClause = '';

my $externalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}


#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select header_name,header_code from TraceHeaders where $userDataRestrictionWhereClause header_id=$nl_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($template_name,$templateCode) = $sth->fetchrow_array();
$sth->finish();



#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>HEADER SETUP</title>
</head>

<body>

<table align="center" id="table2">
	<tr>
		<td vAlign="top" align="left" bgColor="#ffffff">
		<form name="edit_template" method="post" action="upd_template.cgi">
		<input type=hidden name=temp_id value=$nl_id>
		<input type=hidden name=backto value="">
			<font style="FONT-SIZE: 12px; FONT-FAMILY: Trebuchet MS, Arial">
			<p>&nbsp;</p>
			<table id="table3" cellSpacing="0" cellPadding="0" width="100%" border="0">
				<tr>
					<td bgColor="#ffffff"><b>CUSTOM MAIL HEADER TEMPLATE SETUP</b></td>
				</tr>
			</table>
			<p><b>Trace Header name:</b>&nbsp;&nbsp;<input type=text size=30 maxlength=50 name=template_name value="$template_name"> 
			<br/>
			<textarea name="nl_template" rows="15" cols="82">$templateCode</textarea> <br>
<br>
		</td>
	</tr>
</table>
<div align="center">
		<p class="txt" align="center">
		<input type="image" src="/images/save.gif" name="I1">
		<a href="index.cgi">
		<img src="/images/cancel.gif" border="0"></a> </p>
	</form>
	</b>
	<p></div>
</body>

</html>
end_of_html
