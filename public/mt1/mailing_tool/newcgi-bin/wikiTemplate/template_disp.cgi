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
my $maxCharacters;
my $maxWords;
my $nl_id = $query->param('nl_id');
my $mode = $query->param('mode');
my $s= $query->param('s');

my $editContent = '';

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
#------  connect to the util database -----------

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select templateName,templateCode,maxCharacters,maxWords from wikiTemplate where $userDataRestrictionWhereClause wikiID=$nl_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($template_name,$templateCode,$maxCharacters,$maxWords) = $sth->fetchrow_array();
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
<title>CUSTOM WIKI TEMPLATE SETUP</title>
</head>

<body>

<table align="center" id="table2">
	<tr>
		<td vAlign="top" align="left" bgColor="#ffffff">
		<form name="edit_template" method="post" action="upd_template.cgi">
		<input type=hidden name=temp_id value=$nl_id>
		<input type=hidden name=s value=$s>
		<input type=hidden name=backto value="">
			<font style="FONT-SIZE: 12px; FONT-FAMILY: Trebuchet MS, Arial">
			<p>&nbsp;</p>
			Available tags:
			<li>{{SPACES}}</li>
			<li>{{BREAK[MAX RANGE]}}</li>
end_of_html
my $cname;
$sql="select categoryName from svWikiCategory order by categoryName";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cname) = $sth->fetchrow_array())
{
	print "<li>{{$cname}}</li>\n";
}
$sth->finish();
print<<"end_of_html";
			<table id="table3" cellSpacing="0" cellPadding="0" width="100%" border="0">
				<tr>
					<td bgColor="#ffffff"><b>CUSTOM WIKI TEMPLATE SETUP</b></td>
				</tr>
			</table>
			<p><b>Template name:</b>&nbsp;&nbsp;<input type=text size=30 maxlength=50 name=template_name value="$template_name"> 
			<br/>
			<textarea name="nl_template" rows="15" cols="82">$templateCode</textarea> <br>
			<br>
			<p><b>Max Characters:</b>&nbsp;&nbsp;<input type=text size=30 maxlength=50 name=maxCharacters value="$maxCharacters">
			<br>
			<p><b>Max Words:</b>&nbsp;&nbsp;<input type=text size=30 maxlength=50 name=maxWords value="$maxWords">
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
