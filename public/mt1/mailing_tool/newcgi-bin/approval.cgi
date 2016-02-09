#!/usr/bin/perl
#===============================================================================
# Purpose: Send Approval Email 
# Name   : approval.cgi 
#
#--Change Control---------------------------------------------------------------
# 02/28/04  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $cemail;
my $aid = $query->param('aid');
my $internal= $query->param('i');
if ($internal eq "")
{
	$internal=0;
}
my $aname;
my $tid;
my $url;
my $cid;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="select advertiser_name,company_id from advertiser_info where advertiser_id=$aid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($aname,$cid)=$sth->fetchrow_array();
$sth->finish();
my $sname;
    srand(rand time());
    my @c=split(/ */, "bcdfghjklmnprstvwxyz");
    my @v=split(/ */, "aeiou");
    $sname = $c[int(rand(20))];
    $sname = $sname . $v[int(rand(5))];
    $sname = $sname . $c[int(rand(20))];
    $sname = $sname . $v[int(rand(5))];
    $sname = $sname . $c[int(rand(20))];
    $sname = $sname . int(rand(999999));
    $sql = "delete from approval_list where advertiser_id=$aid and date_added < date_sub(curdate(),interval 7 day)";
    my $rows=$dbhu->do($sql);
    $sql = "insert into approval_list(advertiser_id,uid,date_added) values($aid,'$sname',now())";
    my $rows=$dbhu->do($sql);
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>E-mail</title>
<script language="JavaScript">
function chkdrop()
{
if (sendapp.textads.selectedIndex == -1)
{
	alert('An item must be selected in the drop down menu');
	return false;
}
else
{
	return true;
}
}
</script>
</head>

<body>
<form action="send_approval.cgi" method="post" name="sendapp" onSubmit="return chkdrop();">
<input type=hidden name=aid value=$aid>
<input type=hidden name=uid value=$sname>
<input type=hidden name=i value=$internal>
<b>To: </b>
end_of_html
if ($internal == 1)
{
	print "<select name=cemail><option value=ALL>ALL </option>\n";
	print "<option value=\"group.approvals\@zetainteractive.com\">group.approvals\@zetainteractive.com</option>\n";
	$sql="select cm.email_addr from CampaignManager cm,company_info ci where ci.manager_id=cm.manager_id and ci.manager_id !=0 and ci.company_id=$cid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cemail) = $sth->fetchrow_array())
{
	print "<option value=$cemail>$cemail</option>\n";
}
$sth->finish();
}
else
{
	print "<select name=cemail><option value=ALL>ALL </option>\n";
$sql="select email_addr from company_approval where company_id=$cid order by email_addr"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cemail) = $sth->fetchrow_array())
{
	print "<option value=$cemail>$cemail</option>\n";
}
$sth->finish();
}
print<<"end_of_html";
</select><br><br>
<b>DROP DOWN OPTIONS: </b><select multiple name="textads">
<option value="CREATIVE - IMAGE AD(S)">CREATIVE - IMAGE AD(S)</option>
<option value="CREATIVE - TEXT AD(S)">CREATIVE - TEXT AD(S)</option>
<option value="SUBJECT LINE(S)">SUBJECT LINE(S)</option>
<option value="FROM LINE(S)">FROM LINE(S)</option>
<option value="ADVERTISER URL">ADVERTISER URL</option>
<option value="OPT OUT URL">OPT OUT URL</option>
<option value="PRE POP SUPPORTED">PRE POP SUPPORTED</option>
<option value="REDIRECT URL">REDIRECT URL</option>
<option value="TRACKING PIXEL">TRACKING PIXEL</option>
<option value="INACTIVE NOTIFICATION">INACTIVE NOTIFICATION</option>
</select>&nbsp;&nbsp;<font color=red>Use CTRL + click to make multiple selections</font><br><br>
<input type="submit" value="Send" name="B21"><br></p>

<hr>

<p class="MsoNormal" align="left">
end_of_html
	my $temp_email = "approval\@zetainteractive.com";
	#my $the_email = "This approval e-mail has been sent to you from XL Marketing, regarding the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT><FONT color=#000000>campaign. </p><P class=MsoNormal align=left>We have modified the&nbsp;following campaign attributes:</P> <UL>";
	my $the_email = "This approval e-mail has been sent to you, regarding the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT><FONT color=#000000>campaign. </p><P class=MsoNormal align=left>We have modified the&nbsp;following campaign attributes:</P> <UL>";
	print $the_email;
	print "<LI><DIV class=MsoNormal align=left><EM><FONT color=#800000><STRONG>DROP DOWN OPTIONS</STRONG></FONT></EM></DIV></LI>\n";
	my $aspireurl=$util->getAspireURL();
	print "</ul><OL><LI><DIV class=MsoNormal align=left>Please click on the <a target='blank' href=\"${aspireurl}cgi-bin/advapproval.cgi?aid=$aid&amp;uid=$sname&amp;i=$internal\">advertiser approval page</a></DIV><LI><DIV class=MsoNormal align=left>Check the radio boxes to approval your campaign components</DIV><LI><DIV class=MsoNormal align=left>Scroll to the bottom of the page </DIV><LI><DIV class=MsoNormal align=left>Enter your information in the Approved By and E-mail fields and then click the <EM>Submit</EM> Button</DIV></LI></OL><DIV class=MsoNormal align=left>You may also email <a href=\"mailto:$temp_email\">$temp_email</a> or call Neal at 212.880.2510 x3411 with questions or concerns.  Please complete this advertiser approval form as soon as possible - we cannot send traffic to your offer until we receive your approval.</p><DIV class=MsoNormal align=left>&nbsp;</DIV><DIV class=MsoNormal align=left>Thanks!</DIV>";
print<<"end_of_html";
</p>
</form>
</body>

</html>
end_of_html
