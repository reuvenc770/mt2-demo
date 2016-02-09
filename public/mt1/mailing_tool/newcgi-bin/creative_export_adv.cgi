#!/usr/bin/perl

# *****************************************************************************************
# creative_export_adv.cgi
#
# this page allows selection of creatives to export 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sql;
my $cnt;
my $rows;
my $errmsg;
my $aname;
my $aid= $query->param('aid');
my $taid;
$taid=$aid;
my $images = $util->get_images_url;

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

# read the email address for this user

$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($aname) = $sth->fetchrow_array();
$sth->finish();

print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Download All Creative Selection</title>
<script language="JavaScript">
function selectall()
{
    refno=/cid/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/cid/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = false;
        }
    }
}
</script>
</head>
<body>
<center><b>Advertiser: </b>$aname</center><br><br>
<form method=post action=export_adv.cgi name=campform>
<input type=hidden name=aid value=$aid>
<table width=50%>
<tr><th></th><th>Creative ID</th><th>Creative Name</th></tr>
<tr><td colspan=3 align=center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></td></tr>
end_of_html
# Get the email template and substitute all the field data
$sql = "select creative_id,creative_name from creative where advertiser_id = $aid and status='A' order by creative_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cname;
my $campaign_id;
while (($campaign_id,$cname) = $sth->fetchrow_array())
{
	print "<tr><td><input type=checkbox name=cid value=$campaign_id></td><td>$campaign_id</td><td>$cname</td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
<tr><td colspan=3 align=center><input type=submit value="Download Creatives"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);

