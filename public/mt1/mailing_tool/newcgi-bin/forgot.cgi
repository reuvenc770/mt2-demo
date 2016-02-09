#!/usr/bin/perl
# *****************************************************************************************
# forgot.cgi
#
# this page is used when someone forgot their password 
#
# History
# Grady Nash, 10/24/2001, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use util;

# get some objects to use later

my $util = util->new;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $images = $util->get_images_url;

# print out Forgot Your Password page

print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<title>Forgot Your Password</title>
</head>
<body>

<table cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<tr>
<td valign="top" align="left">

    <table border="0" cellpadding="0" cellspacing="0" width="660">
    <tr>
    <td><img border="0" src="$images/header.gif"></td>
    <td><b><font face="Arial" size="2">Forgot Your Password</font></b></td>
    </tr>
    </table>

</td>
</tr>
<tr>
<td valign="top" align="left">

	<FORM action="forgot_save.cgi" method="post">

    <TABLE cellSpacing=0 cellPadding=10 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD align=left><FONT face="Verdana,Arial" color=#509C10 size=2>
			<b>Forgot Your Password?</b></td>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
        <TD align=left><FONT face="Verdana,Arial" color=#509C10 size=2>
  			Enter your email address below and click Send and
			we will send you an email containing your password.</FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		<tr>
		<td align="center">

        	<TABLE cellSpacing=0 cellPadding=0 width="400" border=0>
        	<TBODY>
        	<TR align=top bgColor="$table_header_bg">
            <TD vAlign=top align=left height=15>
				<IMG src="$images/blue_tl.gif" border=0 width="7" height="7"></TD>
            <TD align=center>
				<b><font face="Verdana,Arial" color="white" size="2">
				Enter Your Email Address</font></b></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15>
				<IMG src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR bgColor="$light_table_bg">
			<TD colSpan=3><IMG height="10" src="$images/spacer.gif" border=0></TD>
			</TR>
			<TR bgColor="$light_table_bg">
			<TD>&nbsp;</td>
			<TD><input type="text" size="40" name="email_addr"></td>
			<TD>&nbsp;</td>
			</tr>
			<TR bgColor="$light_table_bg">
			<TD colSpan=3><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
			</TR>
			<TR bgColor="$light_table_bg"> 
			<TD vAlign=bottom align=left>
				<IMG height=7 src="$images/lt_purp_bl.gif" width=7 border=0></TD>
			<TD>&nbsp;</td>
			<TD vAlign=bottom align=right>
				<IMG height=7 src="$images/lt_purp_br.gif" width=7 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

		</td>
		</tr>
        <tr>
        <td><IMG height=5 src="$images/spacer.gif"></TD>
		</tr>
		<tr>
		<td align="center"><INPUT type="submit" value="Send"></TD>
		</TR>
		</TBODY>
		</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

	</FORM>

</TD>
</TR>
<TR>
<TD noWrap align=center height=17><br>
    <img border="0" src="$images/footer.gif"></TD>
</TR>
</TABLE>
</body>
</html>
end_of_html

exit(0);
