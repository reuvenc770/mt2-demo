#################################################################
####   util_template.pm  - 									 ####
#################################################################

package util_template;

use strict;
require Exporter;

use vars qw($VERSION @ISA @EXPORT @EXPORT_OK);

$VERSION	= 1.00;
@ISA		= qw(Exporter);
@EXPORT		= qw(&print_leftbar);

sub print_leftbar ()
{
	my ($dbh,$campaign_id,$type,$article_num) = @_;

	my $show_image_url;
	my $show_title;
	my $show_subtitle;
	my $show_date_str;
	my $show_greeting;
	my $show_introduction;
	my $show_closing;
	my $num_articles;
	my $show_promo;
	my $sql;
	my $sth;
	my $k;
	my $template_id;
	my @articles = ("Zero","First","Second","Third","Fourth","Fifth");
    my $alt_light_table_bg = "#D6C6FF";    # purple

	# read info about this campaigns template

	$sql = "select template_id from campaign where campaign_id = $campaign_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($template_id) = $sth->fetchrow_array();
	$sth->finish();

	# read info about this campaigns template

	$sql = "select show_image_url, show_title, show_subtitle, show_date_str,
    	show_greeting, show_introduction, show_closing, num_articles, show_promo from template
		where template_id = $template_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($show_image_url, $show_title, $show_subtitle, $show_date_str,
    	$show_greeting, $show_introduction, $show_closing, $num_articles, 
		$show_promo) = $sth->fetchrow_array();
	$sth->finish();

	# removed promo stuff, 10/15/01
	$show_promo = "N";

	# print out Javascript used to save the changes and go to the next page

	print qq { <script language="Javascript">\n
        function SaveFunc(btn)\n
        {\n
            if (SaveFunc.arguments.length == 2)\n
            {\n
                document.campform.article.value = SaveFunc.arguments[1];\n
            }\n
            document.campform.nextfunc.value = btn;\n
            document.campform.submit();\n
        }\n
        </script> \n };

	# print out the html 

	print qq { <TABLE cellSpacing=0 cellPadding=0 width=190 border=0> <TBODY> \n };

	if ($type eq "INTRODUCTION")
	{
		print qq { <TR bgColor=$alt_light_table_bg>
			<TD vAlign=top align=left width=9 height=7><IMG height=7 
				src="/images/yellow_tl.gif" width=8 border=0></TD>
			<TD vAlign=top align=right width="100%"><IMG height=7 
				src="/images/yellow_tr.gif" width=8 border=0></TD>
			</TR>
			<TR bgColor=$alt_light_table_bg>
			<TD vAlign=bottom colSpan=2 height=7>&nbsp;&nbsp; <FONT 
				face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=2>
				<B>Introduction </B></FONT></TD>
			</TR>
			<TR bgColor=$alt_light_table_bg>
			<TD>&nbsp;&nbsp;&nbsp;&nbsp; </TD>
			<TD vAlign=bottom height=12><FONT face=Verdana,Arial,Helvetica,sans-serif 
				color=#509C10 size=1>Catch your reader's attention with a compelling message 
				<BR></FONT></TD>
			</TR>
			<TR bgColor=$alt_light_table_bg>
			<TD vAlign=bottom align=left height=7><IMG height=7 src="/images/yellow_bl.gif" 
				width=8 border=0></TD>
			<TD vAlign=bottom align=right><IMG height=7 src="/images/yellow_br.gif" 
				width=8 border=0></TD>
			</TR>\n };
	}
	else
	{
		print qq { <TR bgColor=#E3FAD1>\n
			<TD vAlign=top align=left width=9 height=7>\n
			<IMG height=7 src="/images/lt_purp_tl.gif" width=8 border=0></TD>\n
			<TD vAlign=top align=right width="100%"><font face="Arial">\n
			<IMG height=7 src="/images/lt_purp_tr.gif" width=8 border=0></font></TD>\n
			</TR>\n
			<TR bgColor=#E3FAD1>\n
			<TD vAlign=bottom colSpan=2 height=7><font face="Arial">&nbsp;&nbsp;\n
			<a href="JavaScript:SaveFunc('introduction');"><FONT color=#000000 size=2><B>\n
			Introduction</B></FONT></a>\n
			</font>\n
			</TD>\n
			</TR>\n
			<TR bgColor=#E3FAD1>\n
			<TD vAlign=bottom align=left height=7><font face="Arial">\n
			<IMG height=7 src="/images/lt_purp_bl.gif" width=8 border=0></font></TD>\n
			<TD vAlign=bottom align=right><font face="Arial">\n
			<IMG height=7 src="/images/lt_purp_br.gif" width=8 border=0></font></TD>\n
			</TR>\n
		};
	}

	print qq {	</TBODY> </TABLE> <IMG height=7 src="/images/spacer.gif" width=190> \n };

# not using Promotion any more, commented by Grady, 10/19/2001
#	if ($show_promo eq "Y")
#	{
#		if ($type eq "PROMOTION")
#		{
#			print qq { <TABLE cellSpacing=0 cellPadding=0 width=190 border=0> <TBODY>
#				<TR bgColor=$alt_light_table_bg>
#				<TD vAlign=top align=left width=9 height=7><font face="Arial"><IMG height=7
#    				src="/images/yellow_tl.gif" width=8 border=0></font></TD>
#				<TD vAlign=top align=right width="100%"><font face="Arial"><IMG height=7 
#					src="/images/yellow_tr.gif" width=8 border=0></font></TD>
#				</TR>
#				<TR bgColor=$alt_light_table_bg>
#				<TD vAlign=bottom colSpan=2 height=7><font face="Arial">&nbsp;&nbsp; <FONT 
#					color=#509C10 size=2><B>Promotion </B></FONT></font></TD>
#				</TR>
#				<TR bgColor=$alt_light_table_bg>
#				<TD><font face="Arial">&nbsp;&nbsp;&nbsp;&nbsp;</font> </TD>
#				<TD vAlign=bottom height=12><FONT face=Arial color=#509C10 size=1>Promote your 
#					latest product or service special <BR></FONT></TD>
#				</TR>
#				<TR bgColor=$alt_light_table_bg>
#				<TD vAlign=bottom align=left height=7><font face="Arial"><IMG height=7 
#					src="/images/yellow_bl.gif" width=8 border=0></font></TD>
#				<TD vAlign=bottom align=right>&nbsp; </TD>
#				</TR>
#				</TBODY> </TABLE> \n };
#		}
#		else
#		{
#			print qq { <TABLE cellSpacing=0 cellPadding=0 width=190 border=0> <TBODY>
#				<TR bgColor=#E3FAD1>
#				<TD vAlign=top align=left width=9 height=7><IMG height=7 
#					src="/images/lt_purp_tl.gif" width=8 border=0></TD>
#				<TD vAlign=top align=right width="100%"><IMG height=7 
#					src="/images/lt_purp_tr.gif" width=8 border=0></TD></TR>
#				<TR bgColor=#E3FAD1>
#				<TD vAlign=bottom colSpan=2 height=7>&nbsp;&nbsp;
#					<a href="JavaScript:SaveFunc('promo');"> 
#					<FONT face=Verdana,Arial,Helvetica,sans-serif color=#000000 size=2>
#					<B>Promotion</B></FONT></a></TD>
#				</TR>
#				<TR bgColor=#E3FAD1>
#				<TD vAlign=bottom align=left height=7><IMG height=7 
#					src="/images/lt_purp_bl.gif" width=8 border=0></TD>
#				<TD vAlign=bottom align=right><IMG height=7 src="/images/lt_purp_br.gif" 
#					width=8 border=0></TD>
#				</TR>
#				</TBODY> </TABLE> \n };
#		}
#		print qq { <IMG height=7 src="/images/spacer.gif" width=190> \n };
#	}

	for ($k=1 ; $k<=$num_articles ; $k++)
	{
		if ($k == $article_num)
		{
			print qq { <TABLE cellSpacing=0 cellPadding=0 width=190 border=0><TBODY>\n
				<TR bgColor=$alt_light_table_bg>\n
				<TD vAlign=top align=left width=9 height=7><font face="Arial">\n
				<IMG height=7 src="/images/yellow_tl.gif" width=8 border=0></font></TD>\n
				<TD vAlign=top align=right width="100%"><font face="Arial">\n
				<IMG height=7 src="/images/yellow_tr.gif" width=8 border=0></font></TD>\n
				</TR>\n
				<TR bgColor=$alt_light_table_bg>\n
				<TD vAlign=bottom colSpan=2 height=7><font face="Arial">&nbsp;&nbsp; \n
				<FONT color=#509C10 size=2><B>Article $k</B></FONT></font>\n
				</TD>\n
				</TR>\n
				<TR bgColor=$alt_light_table_bg>\n
				<TD><font face="Arial">&nbsp;&nbsp;&nbsp;&nbsp;</font> </TD>\n
				<TD vAlign=bottom height=12><FONT face=Arial color=#509C10 size=1>\n
				An article is a great way to share your latest news <BR></FONT></TD>\n
				</TR>\n
				<TR bgColor=$alt_light_table_bg>\n
				<TD vAlign=bottom align=left height=7><font face="Arial">\n
				<IMG height=7 src="/images/yellow_bl.gif" width=8 border=0></font></TD>\n
				<TD vAlign=bottom align=right><font face="Arial">\n
				<IMG height=7 src="/images/yellow_br.gif" width=8 border=0></font></TD>\n
				</TR>\n
				</TBODY> </TABLE>\n
				<IMG height=7 src="/images/spacer.gif" width=190>\n };
		}
		else
		{
			print qq { <TABLE cellSpacing=0 cellPadding=0 width=190 border=0><TBODY>\n
				<TR bgColor=#E3FAD1>\n
				<TD vAlign=top align=left width=9 height=7><font face="Arial">\n
				<IMG height=7 src="/images/lt_purp_tl.gif" width=8 border=0></font></TD>\n
				<TD vAlign=top align=right width="100%"><font face="Arial">\n
				<IMG height=7 src="/images/lt_purp_tr.gif" width=8 border=0></font></TD>\n
				</TR>\n
				<TR bgColor=#E3FAD1>\n
				<TD vAlign=bottom colSpan=2 height=7> <font face="Arial">&nbsp;&nbsp; \n
				<a href="JavaScript:SaveFunc('article',$k);">\n
				<FONT color=#000000 size=2><B>Article $k</B></FONT></a></font>\n
				</TD></TR>\n
				<TR bgColor=#E3FAD1>\n
				<TD vAlign=bottom align=left height=7><font face="Arial">\n
				<IMG height=7 src="/images/lt_purp_bl.gif" width=8 border=0></font></TD>\n
				<TD vAlign=bottom align=right><font face="Arial">\n
				<IMG height=7 src="/images/lt_purp_br.gif" width=8 border=0></font></TD>\n
				</TR>\n
				</TBODY></TABLE>\n
				<IMG height=7 src="/images/spacer.gif" width=190>\n };
		}
	}

	if ($type eq "CONTACT")
	{
		print qq { <TABLE cellSpacing=0 cellPadding=0 width=190 border=0> <TBODY>
			<TR bgColor=$alt_light_table_bg>
			<TD vAlign=top align=left width=9 height=7>
			<IMG height=7 src="/images/yellow_tl.gif" width=8 border=0></TD>
			<TD vAlign=top align=right width="100%">
			<IMG height=7 src="/images/yellow_tr.gif" width=8 border=0></TD></TR>
			<TR bgColor=$alt_light_table_bg>
			<TD vAlign=bottom colSpan=2 height=7>&nbsp;&nbsp;
			<FONT face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=2><B>Contact Info
			</B></FONT></TD></TR>
			<TR bgColor=$alt_light_table_bg>
			<TD>&nbsp;&nbsp;&nbsp;&nbsp; </TD>
			<TD vAlign=bottom height=12>
			<FONT face=Verdana,Arial,Helvetica,sans-serif color=#509C10 size=1>Make it easy for your
			readers to contact you <BR></FONT></TD></TR>
			<TR bgColor=$alt_light_table_bg>
			<TD vAlign=bottom align=left height=7>
			<IMG height=7 src="/images/yellow_bl.gif" width=8 border=0></TD>
			<TD vAlign=bottom align=right>
			<IMG height=7 src="/images/yellow_br.gif" width=8 border=0></TD></TR>
			</TBODY></TABLE>\n };
	}
	else
	{
		print qq { <TABLE cellSpacing=0 cellPadding=0 width=190 border=0> <TBODY>
			<TR bgColor=#E3FAD1>
			<TD vAlign=top align=left width=9 height=7><IMG height=7 
				src="/images/lt_purp_tl.gif" width=8 border=0></TD>
			<TD vAlign=top align=right width="100%"><IMG height=7 
				src="/images/lt_purp_tr.gif" width=8 border=0></TD>
			</TR>
			<TR bgColor=#E3FAD1>
			<TD vAlign=bottom colSpan=2 height=7>&nbsp;&nbsp; 
				<A href="JavaScript:SaveFunc('contact');">
				<FONT face=Verdana,Arial,Helvetica,sans-serif color=#000000 size=2>
				<B>Contact Info </B></FONT></A></TD>
			</TR>
			<TR bgColor=#E3FAD1>
			<TD vAlign=bottom align=left height=7><IMG height=7 
				src="/images/lt_purp_bl.gif" width=8 border=0></TD>
			<TD vAlign=bottom align=right><IMG height=7 
				src="/images/lt_purp_br.gif" width=8 border=0></TD>
			</TR>
			</TBODY></TABLE> \n };
	}
}

1;

