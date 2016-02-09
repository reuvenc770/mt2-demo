#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser subject data (eg 'advertiser_subject' table).
# Name   : subject.cgi (subject.cgi)
#
#--Change Control---------------------------------------------------------------
# 02/09/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $dbh;
my $phone;
my $email;
my $company;
my $aim;
my $website;
my $username;
my $password;
my $notes;
my $aid = $query->param('aid');
my $sid = $query->param('sid');
my $subject_str;
my $idate;
my $subject;
my $aflag;
my $oflag;
my $copywriter;
my $aflag_str; 
my $oflag_str;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
$aflag_str="";
$oflag_str="";
my $copywriter_str="";
my $copyname;
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
#
$sql = "select advertiser_subject,approved_flag,original_flag,date_format(inactive_date,'%m/%d/%y'),copywriter,copywriter_name from advertiser_subject where advertiser_id=$aid and subject_id=$sid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject,$aflag,$oflag,$idate,$copywriter,$copyname) = $sth->fetchrow_array();
$sth->finish();
$subject=CGI::escapeHTML($subject);
if ($aflag eq "Y")
{
	$aflag_str = "checked";
}
if ($oflag eq "Y")
{
	$oflag_str = "checked";
}
if ($copywriter eq "Y")
{
	$copywriter_str = "checked";
}
else
{
	$copyname="";
}

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Subject Line</title>
</head>
<body>
<form action="/cgi-bin/upd_subject.cgi" method="post" accept-charset="UTF-8">
<p><b>Subject: </b><input type=text value="$subject" name=csubject maxlength=80 size=50>&nbsp;&nbsp;<b>Inactive Date(MM/DD/YY):</b><input type=text value="$idate" name=idate maxlength=10 size=11><br>
<input type=hidden name=aid value="$aid">
<input type=hidden name=sid value="$sid">
<p>
<b>Approved</b> <input type="checkbox" $aflag_str name="aflag" size="40" maxlength="90" value="Y"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Original</b> <input type="checkbox" $oflag_str name="oflag" size="40" maxlength="90" value="Y"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Copywriter</b> <input type="checkbox" $copywriter_str name="copywriter" size="40" maxlength="90" value="Y">&nbsp;&nbsp;<b>Copywriter Name:</b> <select name=copywriter_name>
end_of_html
my @CWRITER=("","ao","do","ws","jw");
my $i=0;
while ($i <= $#CWRITER)
{
	if ($CWRITER[$i] eq $copyname)
	{
		print "<option selected value=$CWRITER[$i]>$CWRITER[$i]</option>\n";
	}
	else
	{
		print "<option value=$CWRITER[$i]>$CWRITER[$i]</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select>
</p>
<p>
<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</p>
</form>
</body>
</html>
end_of_html
