#!/usr/bin/perl
#===============================================================================
# Name   : adv_search.cgi 
#
#--Change Control---------------------------------------------------------------
# 07/05/05  Jim Sobeck  Creation
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
my $id;
my $aim;
my $website;
my $username;
my $password;

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
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Weekly</title>
</head>

<body><!-- <font face="Verdana"><b>Search Criteria:</b></font><br> -->
<form method=post action="/cgi-bin/advertiser_list.cgi" target="middle">
<input type=hidden name=search value="Y">
<table border="1" width="100%" id="table2">
	<tr>
			<td width="134"><b><font face="Verdana" size="2">Network:</font></b></td>
		<td>
											<font face="Arial" color="#509c10" size="2">
											<select name="client_id">
<option value="0" selected>ALL</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
											</select></font></td>
	</tr>
	<tr>
			<td width="134"><b><font face="Verdana" size="2">Category:</font></b>
		</td>
		<td colspan=3>
											<select name="catid">
end_of_html
$sql="select category_id,category_name from category_info order by category_name"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	if ($id == 58)
	{
		print "<option value=$id selected>$company</option>\n";
	}
	else
	{
		print "<option value=$id>$company</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
											</select><font face="Verdana" size="2">or</font><select name="catid1">
end_of_html
$sql="select category_id,category_name from category_info order by category_name"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	if ($id == 58)
	{
		print "<option value=$id selected>$company</option>\n";
	}
	else
	{
		print "<option value=$id>$company</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
											</select><font face="Verdana" size="2">or</font><select name="catid2">
end_of_html
$sql="select category_id,category_name from category_info order by category_name"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	if ($id == 58)
	{
		print "<option value=$id selected>$company</option>\n";
	}
	else
	{
		print "<option value=$id>$company</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
											</select></td>
	</tr>
	<tr>
		<td width="134">
<b><font face="Verdana" size="2">Last Run:</font></b></td>
		<td>
									<select name="last_run1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="last_run2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="last_run3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="last_run4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		<td width="134">
<b><font face="Verdana" size="2">Rotation Modified:</font></b></td>
		<td>
									<select name="rotation_modified1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="rotation_modified2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="rotation_modified3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="rotation_modified4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		</tr>
	<tr>
		<td width="134">
<b><font face="Verdana" size="2">Creative Modified:</font></b></td>
		<td>
									<select name="creative_modified1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="creative_modified2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="creative_modified3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="creative_modified4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		<td width="134">
<b><font face="Verdana" size="2">Approved:</font></b></td>
		<td>
									<select name="approved1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="approved2">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>
									
</select><font face="Verdana" size="2">and</font><select name="approved3">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value="<">></option>
									<option value=">"><</option>
</select><select name="approved4">
					<option value="" selected></option>
					                <option value="0">Never</option>
									<option value="3">3 Days</option>
									<option value="4">4 Days</option>
									<option value="5">5 Days</option>
									<option value="6">6 Days</option>
									<option value="7">1 Week</option>
								    <option value="14">2 Weeks</option>
								    <option value="30">1 Month</option>								    
</select></td>
		</tr>
	<tr>
			<td width="134"><b><font face="Verdana" size="2">Payout:</font></b></td>
		<td><select name="payout">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" value="" name="payout_value"><font face="Verdana" size="2">and</font><select name="payout1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="payout1_value"></td>
			<td width="134"><b><font face="Verdana" size="2">AOL Complaint %:</font></b></td>
		<td><select name="aol_comp">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" value="" name="aol_comp_value"><font face="Verdana" size="2">and</font><select name="aol_comp1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="aol_comp1_value"></td>
		</tr>
	<tr>
			<td width="134"><b>eCPM:</b></td>
			<td><select name="ecpm">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="ecpm_value"><font face="Verdana" size="2">and</font><select name="ecpm1">
					<option value="" selected></option>
									<option value="=">=</option>
									<option value=">">></option>
									<option value="<"><</option>
</select><input maxLength="255" size="9" name="ecpm1_value"></td>
			<td width="134"><b>Advertiser Rating:</b></td>
			<td><select name="adv_rating">
                    <option value="" selected></option>
                                    <option value="=">=</option>
                                    <option value=">">></option>
                                    <option value="<"><</option>
</select><select name="adv_rating_value">
					<option value="" ></option>
					<option value="0">None</option>
					                <option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
</select></td>
		</tr>
			<tr>
			<td width="134"><b>Pixel Verified</b></td>
			<td><select name="pixel_verified">
					<option value="" selected></option>
									<option value="Y">Y</option>
									<option value="N">N</option>
									<option value="?">?</option>
				</select>
		</td>
			<td width="134"><b>Advertiser Name</b></td>
			<td><input type=text size=20 maxlength=30 name=adname>
		</td>
		</tr>
			<tr>
			<td width="134"><b>Company Name</b></td>
			<td><input type=text size=20 maxlength=30 name=cname>
		</td>
		<td>
		<input type="submit" value="Submit" name="B27"></td>
		<td>&nbsp;</td>
			</tr>
			</table>
</form>
</body>
</html>
end_of_html
