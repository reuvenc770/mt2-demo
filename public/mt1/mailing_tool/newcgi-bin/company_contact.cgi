#!/usr/bin/perl
#===============================================================================
# Purpose: Edit company contact data 
# Name   : company_contact.cgi 
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
my $default_flag;
my $website;
my $username;
my $password;
my $submit;
my $cid;
my $notes;
my $company_id = $query->param('company_id');
my $old_cid = $query->param('cid');
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
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select company_name from company_info where company_id=$company_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($company) = $sth->fetchrow_array();
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
<tr><th>Name</th><th>Email</th><th>Phone</th><th>AIM</th><th>Default</th><th></th></tr>
end_of_html
$sql="select contact_id,contact_name,contact_phone,contact_email,contact_aim,default_flag from company_info_contact where company_id=$company_id order by contact_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$name,$phone,$email,$aim,$default_flag) = $sth->fetchrow_array())
{
	print "<tr><td><a href=\"company_contact.cgi?company_id=$company_id&cid=$cid\">$name</a></td><td>$email</td><td>$phone</td><td>$aim</td><td align=center>$default_flag</td><td><a href=\"company_contact_delete.cgi?company_id=$company_id&id=$cid\">Delete</a></tr>\n";
}
$sth->finish();

if ($old_cid ne "")
{
	$sql="select contact_id,contact_name,contact_phone,contact_email,contact_aim,default_flag from company_info_contact where contact_id=$old_cid";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($cid,$name,$phone,$email,$aim,$default_flag)=$sth->fetchrow_array();
	$sth->finish();
	$submit="Update";
}
else
{
	$name="";
	$phone="";
	$email="";
	$aim="";
	$submit="Add";
	$default_flag="N";
} 
print<<"end_of_html";
</table>
<p>
<form method=post name="company" action="/cgi-bin/company_add_contact.cgi">
<input type=hidden value=$company_id name="company_id">
<input type=hidden name=cid value=$old_cid>
<table width=70%>
<tr><td><b>Contact:</b><input maxLength="255" size="50" name="name" value="$name"></td>
<td><b>Email Addr:</b><input maxLength="255" size="50" name="email_addr" value="$email"></td></tr>
<tr><td><b>Phone:</b><input maxLength="255" size="50" name="phone" value="$phone"></td>
<td><b>AIM:</b><input maxLength="255" size="50" name="aim" value="$aim"></td></tr>
end_of_html
if ($default_flag eq "Y")
{
	print "<tr><td><b>Default: </b><input type=radio value=Y checked name=default_flag>Yes&nbsp;&nbsp;<input type=radio value=N name=default_flag>No</td></tr>\n";
}
else
{
	print "<tr><td><b>Default: </b><input type=radio value=Y name=default_flag>Yes&nbsp;&nbsp;<input type=radio value=N checked name=default_flag>No</td></tr>\n";
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
