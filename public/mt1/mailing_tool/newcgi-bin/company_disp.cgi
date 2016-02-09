#!/usr/bin/perl
#===============================================================================
# Purpose: Edit company data 
# Name   : company_disp.cgi 
#
#--Change Control---------------------------------------------------------------
# 05/17/06  Jim Sobeck  Creation
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
my $passcard;
my $aim;
my $website;
my $username;
my $password;
my $notes;
my $company_id = $query->param('company_id');
my $mode = $query->param('mode');
if ($mode eq "A")
{
	$company_id=0;
}
my $addr;
my $email_addr;
my $old_mid;
my $old_aid;

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
$sql = "select company_name,contact_notes,physical_addr,manager_id,affiliate_id,passcard from company_info where company_id=$company_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($company,$notes,$addr,$old_mid,$old_aid,$passcard) = $sth->fetchrow_array();
$sth->finish();
if ($old_mid eq "")
{
	$old_mid=0;
}
if ($old_aid eq "")
{
	$old_aid=0;
}

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Company Info</title>
</head>
<script language="JavaScript">
function approval()
{
    document.edit_company.backto.value="/cgi-bin/company_approval.cgi?company_id=$company_id";
    document.edit_company.submit();
}
function update_seeds()
{
    document.edit_company.backto.value="/cgi-bin/company_seedlist.cgi?company_id=$company_id";
    document.edit_company.submit();
}
function update_approval()
{
    document.edit_company.backto.value="/cgi-bin/company_approval.cgi?company_id=$company_id";
    document.edit_company.submit();
}
</script>
<body>
<form method=post name="edit_company" action="/cgi-bin/upd_company.cgi">
<input type=hidden value=$company_id name="company_id">
<input type=hidden value="" name="backto">
<b>Company Name:</b><br>
											<input maxLength="255" size="50" name="company" value="$company"><br>
<br><b>Campaign Manager:</b><select name="manager_id">
end_of_html
$sql="select manager_id,manager_name from CampaignManager order by manager_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $mid;
my $mname;
while (($mid,$mname) = $sth->fetchrow_array())
{
	if ($mid == $old_mid)
	{
    	print "<option selected value=$mid>$mname</option>\n";
	}
	else
	{
    	print "<option value=$mid>$mname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br>
<br><b>Affiliate Platform:</b><select name="affiliate_id">
end_of_html
$sql="select affiliate_id,name from AffiliatePlatform order by name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $mid;
my $mname;
while (($mid,$mname) = $sth->fetchrow_array())
{
	if ($mid == $old_aid)
	{
    	print "<option selected value=$mid>$mname</option>\n";
	}
	else
	{
    	print "<option value=$mid>$mname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br><br>
<b>Company Passcard:</b><input maxLength="40" size="40" name="passcard" value="$passcard"><br><br>
<b>Addr:</b><br><textarea name="addr" rows="5" cols="82">$addr</textarea><br>
end_of_html
if ($mode eq "A")
{
}
else
{
print<<"end_of_html";
<b>Seeded E-mail Addresses:</b>(get's any e-mail that goes out for this company)<br>
<select name=seeded>
end_of_html
$sql="select email_addr from company_seedlist where company_id=$company_id order by email_addr";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $email_addr;
while (($email_addr) = $sth->fetchrow_array())
{
    print "<option value=$email_addr>$email_addr</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Update Seeds" name="B22" onClick=update_seeds();><br><br>
<b><a href="javascript:approval();">Approval E-mail Addresses:</a> </b><br>
<select name=approval>
end_of_html
$sql="select email_addr from company_approval where company_id=$company_id order by email_addr";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $email_addr;
while (($email_addr) = $sth->fetchrow_array())
{
        print "<option value=$email_addr>$email_addr</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Update Approval List" name="B22" onClick=update_approval();><br><br>
end_of_html
}
print<<"end_of_html";
<b>Notes:&nbsp; </b><br>
											<textarea name="notes" rows="7" cols="82">$notes
</textarea></p>
<p>
											
											<input type="image" height="22" src="/images/save_rev.gif" width="81" border="0"><a href="/cgi-bin/company_list.cgi"><img src="/images/cancel_blkline.gif" border=0></a></p>
</form>
</body>

</html>
end_of_html
