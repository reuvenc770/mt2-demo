#!/usr/bin/perl
#===============================================================================
# Purpose: Suppression List Rename 
# File   : supplist_rename.cgi
#
#--Change Control---------------------------------------------------------------
# Jim Sobeck, 05/02/07  Created.
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $aid;
my $aname;
my $list_id;
my $list_name;
my $last_updated;
my $daycnt;
my $reccnt;
my $vid= $query->param('vid');
my $sortby= $query->param('sortby');
my $f= $query->param('f');

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
$sql="select list_name from vendor_supp_list_info where list_id=$vid";
$sth=$dbhq->prepare($sql);
$sth->execute();
($list_name)=$sth->fetchrow_array();
$sth->finish();

    print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<title>Rename a suppression list</title>
<script language='javascript' src='/sv-admin.js'></script>
<style type="text/css">

* { margin: 0; }

p { margin-top: 1em; }

a:active {  text-decoration: none; color: #3333FF}
a:link {  text-decoration: none; color: #3333FF}
a:visited {  text-decoration: none; color: #3333FF}
a:hover {  text-decoration: underline; color: #666699}

.txtS { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 9px }
.txt { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 11px }
.txtL { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 13px }

.txtSB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 9px; font-weight: bold }
.txtB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 11px; font-weight: bold }
.txtLB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 13px; font-weight: bold }

.errSB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 9px; font-weight: bold; color: #FF0000 }
.errB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 11px; font-weight: bold; color: #FF0000 }
.errLB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 13px; font-weight: bold; color: #FF0000 }

.title { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 14px }
.titleB { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 14px; font-weight: bold }

.headline { font-family: lucida grande, trebuchet ms, verdana, sans-serif; font-size: 16px }

.txtWhite { font-family: tahoma, trebuchet ms; font-size: 11px; color: #FFFFFF; font-weight: bold }
.img { border: none }

.dotted { border-color: #acacac; border-style: dotted; border-width: 1px; }

.white { background-color: #efefef; }
.grey { background-color: #dfdfdf; }

.list { list-style: none; margin: 0; padding: 0; }
.list ul, li { list-style: none; margin: 0; padding: 0; }

</style>

</head>

<body>
<table width="100%" cellspacing="0" cellpadding="10" id="titlebar">
  <tr>
    <td width="100%" bgcolor="#DFDFDF">
<font class="title">
<b>Suppression List Manager</b></font><br>
<font class="txtB"><a href="/newcgi-bin/mainmenu.cgi">go home &raquo;</a></font> 
    </td>
  </tr>
</table>

<form method=post action="supplist_rename_save.cgi">
<input type=hidden name=vid value=$vid>
<input type=hidden name=sortby value=$sortby>
<input type=hidden name=f value=$f>
<table class="txt" align="center" width="100%" height="50%">
<tr>
	<td width="100%" align="center" valign="middle">
	<p class="title"><b>Rename suppression list:</b><br>$list_name</p>
	<p>enter a new name for this list: <input class="txt" type="text" name="list_name" size="25"> <input type="submit" name=btn value="rename" class="txtS"> <input type="submit" name=btn value="cancel" class="txtS"></p></td>
</tr>
</table>
</form>

</body>

</html>
end_of_html
