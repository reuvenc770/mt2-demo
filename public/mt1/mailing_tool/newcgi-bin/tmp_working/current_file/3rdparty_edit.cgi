#!/usr/bin/perl

# *****************************************************************************************
# 3rdparty_edit.cgi
#
# this page displays the 3rd party edit defaults page 
#
# History
# Jim Sobeck, 12/20/05, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $curl;
my @url_array;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
my $id= $query->param('id');
my $mailer_name;
my $num_subject;
my $num_from;
my $num_creative;
my $rname;
my $rloc;
my $rdate;
my $remail;
my $rip;
my $rcdate;
my $rcid;
my $remailid;
my $unsub_flag;
my $include_images;
my ($mailer_ftp,$ftp_username,$ftp_password,$record_path,$suppression_path,$aol_seed,$yahoo_seed,$hotmail_seed,$other_seed,$seed_first_name,$seed_city,$seed_state,$seed_gender,$seed_dob,$fname_seq,$city_seq,$state_seq,$dbo_seq,$eid_seq,$export_format,$filenaming_convention,$export_freq,$gender_seq,$email_seq,$ip_seq,$date_seq,$seed_ip,$seed_date,$creative_path,$list_suppression_path);
my $contact;
my $phone;
my $email;
my $username;
my $password;
my $website;
my $notes;
my $send_data;
my $build_zip;
#
$sql = "select mailer_name,num_subject,num_from,num_creative,name_replace,loc_replace,date_replace,email_replace,cid_replace,include_unsubscribe,emailid_replace,mailer_ftp,ftp_username,ftp_password,record_path,suppression_path,aol_seed,yahoo_seed,hotmail_seed,other_seed,seed_first_name,seed_city,seed_state,seed_gender,seed_dob,fname_seq,city_seq,state_seq,dbo_seq,eid_seq,export_format,filenaming_convention,export_freq,gender_seq,email_seq,contact,phone,email,username,password,website,notes,seed_ip,seed_date,ip_seq,date_seq,creative_path,list_suppression_path,capture_replace,ip_replace,include_images,build_zip,send_data from third_party_defaults where third_party_id=$id";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($mailer_name,$num_subject,$num_from,$num_creative,$rname,$rloc,$rdate,$remail,$rcid,$unsub_flag,$remailid,$mailer_ftp,$ftp_username,$ftp_password,$record_path,$suppression_path,$aol_seed,$yahoo_seed,$hotmail_seed,$other_seed,$seed_first_name,$seed_city,$seed_state,$seed_gender,$seed_dob,$fname_seq,$city_seq,$state_seq,$dbo_seq,$eid_seq,$export_format,$filenaming_convention,$export_freq,$gender_seq,$email_seq,$contact,$phone,$email,$username,$password,$website,$notes,$seed_ip,$seed_date,$ip_seq,$date_seq,$creative_path,$list_suppression_path,$rcdate,$rip,$include_images,$build_zip,$send_data) = $sth->fetchrow_array();
$sth->finish();
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Third Party Mailer</title>
</head>

<body>

<b><font face="Verdana">3rd Party Mailer Defaults</font></b><font face="Verdana"><b>:</b></font><br>
&nbsp;<form name="campform" method="post" action="/cgi-bin/3rdparty_upd.cgi">
<input type=hidden name=id value=$id>
	<table id="table1" width="48%" border="1">
		<tr>
			<td><b><font face="Verdana" size="2">3rd Party Mailer:</font></b></td>
			<td width="320"><input type=text name=mailer_name maxlength=30 value="$mailer_name">
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Contact:</font></b></td>
			<td width="392">
													<input maxLength="80" size="40" name="contact" value="$contact"> </td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Phone #:</font></b></td>
			<td width="392">
													<input maxLength="30" size="40" name="phone" value="$phone"> </td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Email:</font></b></td>
			<td width="392"><input maxLength="50" size="40" name="email" value="$email"> </td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Website:</font></b></td>
			<td width="392">
													<input maxLength="255" size="80" name="website" value="$website"> </td>
		</tr>

<tr>
			<td><b><font face="Verdana" size="2">Username:</font></b></td>
			<td width="392">
													<input maxLength="30" size="40" name="username" value="$username"> </td>
		</tr>

		<tr>
			<td><b><font face="Verdana" size="2">Password:</font></b></td>
			<td width="392">
													<input maxLength="30" size="40" name="password" value="$password"> </td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Build Zip:</font></b></td>
			<td width="320"><font size="1"><select name="build_zip">
end_of_html
if ($build_zip eq "Y")
{
	print "<option value=Y selected>Y</option>\n";
	print "<option value=N>N</option>\n";
}
else
{
	print "<option value=Y >Y</option>\n";
	print "<option value=N selected>N</option>\n";
}
print<<"end_of_html";
			</select></font></td></tr>
		<tr>
			<td><b><font face="Verdana" size="2">Send Data:</font></b></td>
			<td width="320"><font size="1"><select name="send_data">
end_of_html
if ($send_data eq "Y")
{
	print "<option value=Y selected>Y</option>\n";
	print "<option value=N>N</option>\n";
}
else
{
	print "<option value=Y >Y</option>\n";
	print "<option value=N selected>N</option>\n";
}
print<<"end_of_html";
			</select></font></td></tr>
<tr>
			<td height="121"><b><font face="Verdana" size="2">Notes:</font></b></td>
			<td width="392" height="121">
													<textarea name="notes" rows="15" cols="100">$notes</textarea>
 </td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Subject Line(s):</font></b></td>
			<td width="320"><font size="1"><select name="num_subject">
end_of_html
my $i=1;
while ($i <= 20) 
{
	if ($i == $num_subject)
	{
			print "<option value=$i selected>$i</option>\n";
	}
	else
	{
			print "<option value=$i>$i</option>\n";
    }
	$i++;
}		
print<<"end_of_html";
			</select></font></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">From Line(s):</font></b></td>
			<td width="320"><font size="1"><select name="num_from">
end_of_html
my $i=1;
while ($i <= 20) 
{
	if ($i == $num_from)
	{
			print "<option value=$i selected>$i</option>\n";
	}
	else
	{
			print "<option value=$i>$i</option>\n";
    }
	$i++;
}		
print<<"end_of_html";
			</select></font></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Creative(s):</font></b></td>
			<td width="320"><font size="1"><select name="num_creative">
end_of_html
my $i=1;
while ($i <= 20) 
{
	if ($i == $num_creative)
	{
			print "<option value=$i selected>$i</option>\n";
	}
	else
	{
			print "<option value=$i>$i</option>\n";
    }
	$i++;
}		
print<<"end_of_html";
			</select></font></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Replace {{NAME}} with:</font></b></td>
			<td width="320">
											<input maxLength="30" size="36" name="rname" value="$rname"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Replace {{LOC}} with:</font></b></td>
			<td width="320">
											<input maxLength="30" size="36" name="rloc" value="$rloc"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Replace {{DATE}} with:</font></b></td>
			<td width="320">
											<input maxLength="30" size="36" name="rdate" value="$rdate"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Replace {{EMAIL_ADDR}} with:</font></b></td>
			<td width="320">
											<input maxLength="30" size="36" name="remail" value="$remail"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Replace {{CID}} with:</font></b></td>
			<td width="320">
											<input maxLength="30" size="36" name="rcid" value="$rcid"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Replace {{EMAIL_USER_ID}} with:</font></b></td>
			<td width="320">
											<input maxLength="30" size="36" name="remailid" value="$remailid"></td>
		</tr>
        <tr>
            <td><b><font face="Verdana" size="2">Replace {{CAPTURE_DATE}} with:</font></b></td>
            <td width="320">
            <input maxLength="30" size="36" name="rcdate" value="$rcdate"></td>
        </tr>
        <tr>
            <td><b><font face="Verdana" size="2">Replace {{IP}} with:</font></b>
</td>
            <td width="320">
           <input maxLength="30" size="36" name="rip" value="$rip"></td>
        </tr>
		<tr>
			<td><b><font face="Verdana" size="2">Include Unsubscribe:</font></b></td>
			<td width="320"><font size="1"><select name="unsub_flag">
end_of_html
if ($unsub_flag eq "Y")
{
	print "<option value=Y selected>Y</option>\n";
	print "<option value=N>N</option>\n";
}
else
{
	print "<option value=Y >Y</option>\n";
	print "<option value=N selected>N</option>\n";
}
print<<"end_of_html";
			</select></font></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Include Images:</font></b></td>
			<td width="320"><font size="1"><select name="include_images">
end_of_html
if ($include_images eq "Y")
{
	print "<option value=Y selected>Y</option>\n";
	print "<option value=N>N</option>\n";
}
else
{
	print "<option value=Y >Y</option>\n";
	print "<option value=N selected>N</option>\n";
}
print<<"end_of_html";
			</select></font></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Mailer FTP:</font></b></td>
			<td width="290">
											<input maxLength="20" size="40" name="ip_addr" value="$mailer_ftp"></td>
		</tr>
				<tr>
			<td><b><font face="Verdana" size="2">Mailer FTP Username:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="ftp_username" value="$ftp_username"></td>
		</tr>

<tr>
			<td><b><font face="Verdana" size="2">Mailer FTP Password:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="ftp_password" value="$ftp_password"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Record Path:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="record_path" value="$record_path"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Advertiser Suppression Path:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="suppression_path" value="$suppression_path"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Creative Path:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="creative_path" value="$creative_path"></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">List Suppression Path:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="list_suppression_path" value="$list_suppression_path"></td>
		</tr>
		

<tr>
			<td><b><font face="Verdana" size="2">AOL Seed:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="aol_seed" value="$aol_seed"></td>
		</tr>

<tr>
			<td><b><font face="Verdana" size="2">Yahoo Seed:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="yahoo_seed" value="$yahoo_seed"></td>
		</tr>

<tr>
			<td><b><font face="Verdana" size="2">Hotmail Seed:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="hotmail_seed" value="$hotmail_seed"></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Other Seed:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="other_seed" value="$other_seed"></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Seed First Name:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="seed_fname" value="$seed_first_name"></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Seed City:</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="seed_city" value="$seed_city"></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Seed State:</font></b></td>
			<td width="290">
											<input maxLength="2" size="40" name="seed_state" value="$seed_state"></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Seed Gender:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="seed_gender">
end_of_html
if ($seed_gender eq "M")
{
print<<"end_of_html";
			<option value="M" selected>M</option>
			<option value="F">F</option>
end_of_html
}
else
{
print<<"end_of_html";
			<option value="M">M</option>
			<option value="F" selected>F</option>
end_of_html
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Seed Birth Date(yyyy-mm-dd):</font></b></td>
			<td width="290">
											<input maxLength="40" size="40" name="seed_dob" value="$seed_dob"></td>
		</tr>
<tr>
            <td><b><font face="Verdana" size="2">Seed IP Addr:</font></b></td>
            <td width="290">
<input maxLength="20" size="40" name="seed_ip" value="$seed_ip"></td>
        </tr>
<tr>
            <td><b><font face="Verdana" size="2">Seed Capture Date(yyyy-mm-dd):</font></b></td>
            <td width="290">
<input maxLength="20" size="40" name="seed_date" value="$seed_date"></td>
        </tr>

<tr>
			<td><b><font face="Verdana" size="2">Sequence of Fields to Export:</font></b></td>
			<td width="290">
											&nbsp;</td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Email:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="email_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $email_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">First Name:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="fname_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $fname_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">City:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="city_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $city_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">State:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="state_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $state_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Gender:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="gender_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $gender_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
		<tr>
			<td><b><font face="Verdana" size="2">Birth Date:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="dob_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $dbo_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">{{EMAIL_USER_ID}}:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="eid_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $eid_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">IP Addr:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="ip_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $ip_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
<tr>
			<td><b><font face="Verdana" size="2">Capture Date:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="date_seq">
end_of_html
my $i=0;
while ($i <= 20)
{
    if ($i == $date_seq)
    {
            print "<option value=$i selected>$i</option>\n";
    }
    else
    {
            print "<option value=$i>$i</option>\n";
    }
    $i++;
}
print<<"end_of_html";
			</select></font></td>
		</tr>
	<tr>
			<td><b><font face="Verdana" size="2">Export Format:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="export_format">
end_of_html
if ($export_format eq "CSV")
{
print<<"end_of_html";
			<option value="CSV" selected>CSV</option>
			<option value="TAB">Tab-delimited</option>
end_of_html
}
else
{
print<<"end_of_html";
			<option value="CSV" >CSV</option>
			<option value="TAB" selected>Tab-delimited</option>
end_of_html
}
print<<"end_of_html";
			</select></font></td>
		</tr>

	<tr>
			<td><b><font face="Verdana" size="2">File Naming Convention:</font></b></td>
			<td width="290">
											<input maxLength="80" size="80" name="filenaming_convention" value="$filenaming_convention"></td>
		</tr>

		
<tr>
			<td><b><font face="Verdana" size="2">Export Frequency:</font></b></td>
			<td width="290">
											<font size="1">
											<select name="export_freq">
end_of_html
if ($export_freq eq "D")
{
print<<"end_of_html";
			<option value="D" selected>Daily</option>
			<option value="W">Weekly</option>
end_of_html
}
else
{
print<<"end_of_html";
			<option value="D" >Daily</option>
			<option value="W" selected>Weekly</option>
end_of_html
}
print<<"end_of_html";
			</select></font></td>
		</tr>
	</table>
	<p align="left">
<input type="image" name="BtnAdd" src="/images/save.gif" border=0>&nbsp;&nbsp;&nbsp;
	<a target="_top" href="/cgi-bin/mainmenu.cgi">
	<img height="23" src="/images/home_blkline.gif" width="76" border="0"></a></p>
</form>

</body>

</html>
end_of_html
