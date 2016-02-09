#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser data (eg 'user' table).
# Name   : advertiser_disp.cgi (edit_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/05/04  Jim Sobeck  Creation
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

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
$util->db_connect();
$dbh = $util->get_dbh;
#
$sql = "select contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes from advertiser_contact_info where advertiser_id=$aid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($name,$phone,$email,$company,$aim,$website,$username,$password,$notes) = $sth->fetchrow_array();
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
</head>

<body>
<form method=post action="/cgi-bin/upd_contact.cgi">
<input type=hidden value=$aid name="aid">
<p><b>Contact:</b><br>
											<input maxLength="255" size="50" name="name" value="$name"><br>
<b>Phone:</b><br>
											<input maxLength="255" size="50" name="phone" value="$phone"><br>
<b>Email:</b><br>
											<input maxLength="255" size="50" name="email" value="$email"><br>
<b>Company Name:</b><br>
											<input maxLength="255" size="50" name="company" value="$company"><br>
<b>AIM:</b><br>
											<input maxLength="255" size="50" name="aim" value="$aim"><br>
<b>Reporting Website:</b><br>
											<input maxLength="255" size="50" name="website" value="$website"><br>
<b>Username:</b><br>
											<input maxLength="255" size="50" name="username" value="$username"><br>
<b>Password:</b><br>
											<input maxLength="255" size="50" name="password" type=text value="$password"><br><br>
<b>Notes:&nbsp; </b><br>
											<textarea name="notes" rows="7" cols="82">$notes
</textarea></p>
<p>
											
											<input type="image" height="22" src="/images/save_rev.gif" width="81" border="0"><a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid"><img src="/images/cancel_blkline.gif" border=0></a></p>
</form>
</body>

</html>
end_of_html
