#!/usr/bin/perl
#===============================================================================
# Purpose: Edit company trackin gdata 
# Name   : company_tracking.cgi 
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
my $name;
my $sql;
my $sth;
my $dbh;
my $phone;
my $email;
my $company;
my $aim;
my $submit;
my $password;
my $notes;
my $company_id = $query->param('company_id');
my $tracking_id= $query->param('tracking_id');
my $addr;
my $email_addr;
my $link_params;
my $name;
my $number_of_params;
my $tid;
my $default_flag;
my $affiliate_id;

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
$sql = "select company_name,affiliate_id from company_info where company_id=$company_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($company,$affiliate_id) = $sth->fetchrow_array();
$sth->finish();

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
<body>
<b>Company Name:</b>: $company</b><br>
<center>
<table width=70% border=1>
<tr><th>Affiliate Platform</th><th>Link Params</th><th>Number of Params</th><th>Default</th><th></th></tr>
end_of_html
$sql="select tracking_id,name,link_params,number_of_params,default_flag from company_info_tracking cit join AffiliatePlatform af on cit.affiliate_id=af.affiliate_id where company_id=$company_id order by name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($tid,$name,$link_params,$number_of_params,$default_flag) = $sth->fetchrow_array())
{
	print "<tr><td><a href=\"company_tracking.cgi?company_id=$company_id&tracking_id=$tid\">$name</a></td><td>$link_params</td><td>$number_of_params</td><td>$default_flag</td><td><a href=\"company_tracking_delete.cgi?company_id=$company_id&tracking_id=$tid\">Delete</a></td></tr>\n";
}
$sth->finish();

if ($tracking_id ne "")
{
	$sql="select tracking_id,affiliate_id,link_params,number_of_params,default_flag from company_info_tracking where tracking_id=$tracking_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($tid,$affiliate_id,$link_params,$number_of_params,$default_flag) = $sth->fetchrow_array();
	$sth->finish();
	$submit="Update";
}
else
{
	$link_params="";
	$number_of_params=0;
	$default_flag="N";
	$submit="Add";
}
print<<"end_of_html";
</table>
<p>
<form method=post name="company" action="/cgi-bin/company_add_tracking.cgi">
<input type=hidden value=$company_id name="company_id">
<input type=hidden value=$tracking_id name="tracking_id">
<table width=70%>
<tr><td><b>Affiliate Platform:</b><select name=affiliate_id>
end_of_html
$sql="select affiliate_id,name from AffiliatePlatform order by name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $mid;
my $mname;
while (($mid,$mname) = $sth->fetchrow_array())
{
    if ($mid == $affiliate_id)
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
</select>
</td>
<td><b>Link Params:</b><input maxLength="255" size="80" name="link_params" value="$link_params"></td></tr>
<tr><td><b>Number of Params:</b><select name=number_of_params>
end_of_html
my $i=0;
while ($i <= 9)
{
	if ($i == $number_of_params)
	{
		print "<option value=$i selected>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}
	$i++;
}
print "</select></td>";
if ($default_flag eq "Y")
{
	print "<td><b>Default: </b><input type=radio value=Y checked name=default_flag>Yes&nbsp;&nbsp;<input type=radio value=N name=default_flag>No</td></tr>\n";
}
else
{
	print "<td><b>Default: </b><input type=radio value=Y name=default_flag>Yes&nbsp;&nbsp;<input type=radio value=N checked name=default_flag>No</td></tr>\n";
}
print<<"end_of_html";
<tr><td colspan=2 align=middle><input type="submit" name=submit value="$submit"></td></tr>
</table>
</form>
<center>
<a href=company_list.cgi>Home</a>
</center>
</body>

</html>
end_of_html
