#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser subject data (eg 'advertiser_subject' table).
# Name   : subject.cgi (subject.cgi)
#
#--Change Control---------------------------------------------------------------
# 12/16/04  Jim Sobeck  Creation
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
my $subject_str;
my $subject;
my $temp_str;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>From &amp; Subject Lines</title>
</head>
<body>
<p><b>Current Subjects: </b><b>(Check each box to perform selected function - <font color=red>WARNING - Deletes cannot be undone</font>)</b></br>
<form action="/cgi-bin/add_subject.cgi" method="post" accept-charset="UTF-8">
Action: <select name=iaction><option value=Activate>Activate</option>
<option value=Approve>Approve</option>
<option value=Delete>Delete</option>
<option value=Inactivate>Inactivate</option>
</select><br><br>
end_of_html
$sql = "select subject_id,advertiser_subject,approved_flag,original_flag,copywriter from advertiser_subject where advertiser_id=$aid and status in ('A','I') order by advertiser_subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
$subject_str = "";
my $csubject;
my $sid;
my $aflag;
my $oflag;
my $copywriter;
my $copyname;
while (($sid,$csubject,$aflag,$oflag,$copywriter) = $sth->fetchrow_array())
{
    $temp_str = $sid . " - ". $csubject. " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
	if ($copywriter eq "Y")
	{
        $temp_str = $temp_str . "C ";
	}
	$sql="select count(*) from advertiser_setup where class_id = 4 and (subject1=? or subject2=? or subject3=? or subject4=? or subject5=? or subject6=? or subject7=? or subject8=? or subject9=? or subject10=? or subject11=? or subject12=? or subject13=? or subject14=? or subject15=? or subject16=? or subject17=? or subject18=? or subject19=? or subject20=? or subject21=? or subject22=? or subject23=? or subject24=? or subject25=? or subject26=? or subject27=? or subject28=? or subject29=? or subject30=?)";
	my $cnt;
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid,$sid);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
    if ($aflag eq "Y")
    {
		if ($cnt > 0)
		{
        	$temp_str = $temp_str . " - R)";
		}
		else
		{
        	$temp_str = $temp_str . ")";
		}
    }
    else
    {
		if ($cnt > 0)
		{
        	$temp_str = $temp_str . "- NA! - R)";
		}
		else
		{
        	$temp_str = $temp_str . "- NA!)";
		}
    }
	print "&nbsp;&nbsp;&nbsp;<input type=checkbox name=delsubject value=$sid>$temp_str &nbsp;&nbsp;<a href=\"/cgi-bin/edit_subject.cgi?aid=$aid&sid=$sid\" target=_blank>e</a><br>\n";
}
$sth->finish();
print<<"end_of_html";
<p><b>Subject: (Hit ENTER after each one) </b><br>
<input type=hidden name=aid value="$aid">
<textarea name="csubject" rows="7" cols="82"></textarea></p>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">&nbsp;&nbsp;
<b>Inactive Date(MM/DD/YY):</b><input type=text name=idate maxlength=8 size=9>&nbsp;&nbsp;&nbsp;
											Approved </b>
<input type="checkbox" name="aflag" size="40" maxlength="90" value="Y"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
											Original </b>
<input type="checkbox" name="oflag" size="40" maxlength="90" value="Y">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                            Copywriter</b>
<input type="checkbox" name="copywriter" size="40" maxlength="90" value="Y">&nbsp;&nbsp;<b>Copywriter Name:</b><select name=copywriter_name>
end_of_html
my $copyname="";
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
</select></p>
</form>
</body>
</html>
end_of_html
