#!/usr/bin/perl

# *****************************************************************************************
# upd_tempalte.cgi
#
# this page updates information in the brand_template table
#
# History
# Jim Sobeck, 06/04/07, Creation
# *****************************************************************************************

# include Perl Modules
use lib('/var/www/html/newcgi-bin');
use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $images = $util->get_images_url;
my $camp_id;
my $pmesg;
my $rid;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

#
# Get the information about the user from the form 
#
my $temp_id = $query->param('temp_id');
my $s = $query->param('s');
if ($s eq "")
{
	$s=1;
}
my $backto = $query->param('backto');
my $template_name = $query->param('template_name');
$template_name=~ s/'/''/g; 
my $nl_template = $query->param('nl_template');
my $maxCharacters = $query->param('maxCharacters') || 0;
my $maxWords= $query->param('maxWords') || 0;
if ($nl_template ne "")
{
}
else
{
	$nl_template='';
}
$nl_template=~ s/'/''/g; 
#
#
if ($temp_id > 0)
{
	$sql = "update wikiTemplate set templateName='$template_name', templateCode='$nl_template', maxCharacters=$maxCharacters,maxWords=$maxWords where $userDataRestrictionWhereClause wikiID=$temp_id"; 
}
else
{
	$sql="insert into wikiTemplate(userID, templateName,dateAdded,status,templateCode,maxCharacters,maxWords) values($user_id, '$template_name',curdate(),'A','$nl_template',$maxCharacters,$maxWords)"; 
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
   	util::logerror("Updating wikiTemplate record for $sql $user_id: $errmsg");
}
else
{
	if (($temp_id == 0) and ($s == 1))
	{
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Display wiki Template</title>
<script language="JavaScript">
function doit(value,text)
{
    window.opener.addWiki(value,text);
}
function clearit()
{
	window.opener.clearWiki();
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
print "clearit();\n";
		$sql="select wikiID, templateName from wikiTemplate where $userDataRestrictionWhereClause status = 'A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		my $wid;
		my $templateName;
		while (($wid,$templateName)=$sth->fetchrow_array())
		{
			print "doit(\"$wid\",\"$templateName\");\n";
		}
		$sth->finish();
print<<"end_of_html";
document.location="index.cgi";
</script>
</body>
</html>
end_of_html
	}
	else
	{
		print "Location: index.cgi\n\n";
	}

}
$util->clean_up();
exit(0);
