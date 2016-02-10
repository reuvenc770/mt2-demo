#!/usr/bin/perl
#===============================================================================
# Name   : add_article.cgi 
#
#--Change Control---------------------------------------------------------------
# 11/01/06  Jim Sobeck  Creation
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
my $nl_id = $query->param('nl_id');
my $article;

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
<title>Newsletter Articles</title>
</head>
<body>
<p><b>Currently Assigned Articles : </b></br>
end_of_html
$sql = "select article_name from nl_article,article where nl_id=$nl_id and nl_article.article_id=article.article_id and status='A' order by article_name"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($article) = $sth->fetchrow_array())
{
	print "&nbsp;&nbsp;&nbsp;$article<br><br>\n";
}
$sth->finish();
print<<"end_of_html";
<form method=post action="/cgi-bin/add_article_save.cgi">
<input type=hidden name=nl_id value="$nl_id">
<table cellSpacing="0" cellPadding="0" width="100%" bgColor="#e3fad1" border="0" id="table1">
	<tr>
		<td colSpan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colSpan="3">&nbsp;&nbsp;
		<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
		<b>Available Articles</b> - Selected articles will be added to rotation for newsletter campaigns<br>
&nbsp; <a href="/cgi-bin/article_list.cgi" target=_top>add/edit articles</a></font><br><br></td>
	</tr>
	<tr>
		<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <select multiple size="5" name="articles">
		<option value="0">-- select one or more articles to add --</option>
end_of_html
$sql = "select article_id,article_name from article where status='A' and article_id not in (select article_id from nl_article where nl_id=$nl_id)";
$sth = $dbhu->prepare($sql);
$sth->execute();
my $article_id;
my $article_name;
while (($article_id,$article_name) = $sth->fetchrow_array())
{
	print "<option value=$article_id>$article_name</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
	</tr>
	<tr>
		<td colSpan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
		<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table2">
			<tr>
				<td align="middle" width="50%">
				<input type="image" height="22" width="81" src="/images/save.gif" border="0" name="I1"></td>
				<td align="middle" width="50%">
				<a href="/newcgi-bin/newsletter_disp.cgi?pmode=U&nl_id=$nl_id">
				<img src="/images/cancel.gif" border="0" target="_top"></a></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
end_of_html
