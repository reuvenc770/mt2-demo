#!/usr/bin/perl
#===============================================================================
# Purpose: Allows setting of company information for advertiser 
# Name   : company_infoa.cgi 
#
#--Change Control---------------------------------------------------------------
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
my $phone;
my $email;
my $company;
my $manager_name;
my $aim;
my $website;
my $tracking_id;
my $username;
my $password;
my $notes;
my $company_id;
my $website_id;
my $contact_id;
my $addr;
my $cname;
my $cemail;
my $affiliate_id;
my $affiliate_name;
my $aid = $query->param('aid');


#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();

$sql="select company_id,website_id,contact_id,tracking_id from advertiser_info where advertiser_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
($company_id,$website_id,$contact_id,$tracking_id)=$sth->fetchrow_array();
$sth->finish();

$sql = "select company_name,contact_notes,physical_addr,manager_name,affiliate_id from company_info,CampaignManager where company_info.manager_id=CampaignManager.manager_id and company_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($company_id);
($company,$notes,$addr,$manager_name,$affiliate_id) = $sth->fetchrow_array();
$sth->finish();

$sql = "select name from AffiliatePlatform where affiliate_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($affiliate_id);
($affiliate_name) = $sth->fetchrow_array();
$sth->finish();

$sql = "select website,username,password from company_info_website where website_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($website_id);
($website,$username,$password) = $sth->fetchrow_array();
$sth->finish();

$sql = "select contact_name,contact_phone,contact_email,contact_aim from company_info_contact where contact_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($contact_id);
($cname,$phone,$cemail,$aim) = $sth->fetchrow_array();
$sth->finish();

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Contact Info</title>
<script language="JavaScript">
function updcontact()
{
    var selObj = document.getElementById('company_id');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.contact_id.length;
    while (selLength>0)
    {
        campform.contact_id.remove(selLength-1);
        selLength--;
    }
    campform.contact_id.length=0;
    var selLength = campform.website_id.length;
    while (selLength>0)
    {
        campform.website_id.remove(selLength-1);
        selLength--;
    }
    parent.frames[1].location="/newcgi-bin/company_info_upd.cgi?cid="+selObj.options[selIndex].value;
}
function addContact(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.contact_id.add(newOpt);
}
function addWebsite(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.website_id.add(newOpt);
}
</script>
</head>

<body>
<form method=post name="campform" action="/cgi-bin/adv_company_info_upd.cgi" target=_top>
<input type=hidden value=$aid name="aid">
<b>Company:</b><br>
<select name=company_id onChange="javascript:updcontact();">
end_of_html
$sql="select company_id,company_name from company_info where status='A' order by company_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cid;
my $tname;
while (($cid,$tname)=$sth->fetchrow_array())
{
	if ($cid == $company_id)
	{
		print "<option selected value=$cid>$tname</option>\n";
	}
	else
	{
		print "<option value=$cid>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br>
<b>Contact:</b><br>
<select name=contact_id>
end_of_html
$sql="select contact_id,contact_name from company_info_contact where company_id=? order by contact_name";
$sth=$dbhu->prepare($sql);
$sth->execute($company_id);
my $cid;
my $tname;
while (($cid,$tname)=$sth->fetchrow_array())
{
	if ($cid == $contact_id)
	{
		print "<option selected value=$cid>$tname</option>\n";
	}
	else
	{
		print "<option value=$cid>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br>
<b>Reporting Website:</b><br>
<select name=website_id>
end_of_html
$sql="select website_id,website from company_info_website where company_id=? order by website";
$sth=$dbhu->prepare($sql);
$sth->execute($company_id);
my $cid;
my $tname;
while (($cid,$tname)=$sth->fetchrow_array())
{
	if ($cid == $website_id)
	{
		print "<option selected value=$cid>$tname</option>\n";
	}
	else
	{
		print "<option value=$cid>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br>
<b>Tracking:</b><br>
<select name=tracking_id>
end_of_html
$sql="select tracking_id,name,link_params from company_info_tracking cit join AffiliatePlatform af on cit.affiliate_id=af.affiliate_id where company_id=? order by name";
$sth=$dbhu->prepare($sql);
$sth->execute($company_id);
my $tid;
my $tname;
my $link_params;
while (($tid,$tname,$link_params)=$sth->fetchrow_array())
{
	if ($tid == $tracking_id)
	{
		print "<option selected value=$tid>$tname - $link_params</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname - $link_params</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br><br>
<br>
<table border="1" width="59%" id="table1">
	<tr>
		<td width="119"><b>Company Name</b></td>
		<td>$company</td>
	</tr>
	<tr>
		<td width="119"><b>Campaign Manager</b></td>
		<td>$manager_name</td>
	</tr>
	<tr>
		<td width="119"><b>Main Contact</b></td>
		<td>$cname</td>
	</tr>
	<tr>
		<td width="119"><b>Phone</b></td>
		<td>$phone</td>
	</tr>
	<tr>
		<td width="119"><b>Email</b></td>
		<td><a href="mailto:$cemail">$cemail</a></td>
	</tr>
	<tr>
		<td width="119"><b>AIM</b></td>
		<td>$aim</td>
	</tr>
	<tr>
		<td width="119"><b>Reporting Website</b></td>
		<td><a href="$website" target=_blank>$website</a></td>
	</tr>
	<tr>
		<td width="119"><b>Username</b></td>
		<td>$username</td>
	</tr>
	<tr>
		<td width="119"><b>Password</b></td>
		<td>$password</td>
	</tr>
	<tr>
		<td width="119"><b>Physical Address</b></td>
		<td>$addr</td>
	</tr>
	<tr>
		<td width="119"><b>Notes</b></td>
		<td>$notes</td>
	</tr>
	<tr>
		<td width="119"><b>Affiliate Platform</b></td>
		<td>$affiliate_name</td>
	</tr>
</table>
<table>
<p>
											
											<input type="image" height="22" src="/images/save_rev.gif" width="81" border="0"><a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid" target=_top><img src="/images/cancel_blkline.gif" border=0></a></p>
</form>
</body>

</html>
end_of_html
