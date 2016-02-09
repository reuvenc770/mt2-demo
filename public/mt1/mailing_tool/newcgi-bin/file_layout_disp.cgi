#!/usr/bin/perl
#===============================================================================
# Purpose: Displays and Add/Updates the FILE_LAYOUT rec for a specific user.
# File   : file_layout_disp.cgi
#
# Input  :
#
# Output :
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 9/10/01  Created.
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
my ($sth, $sql, $dbh, $reccnt ) ;
my ($sth2, $sql2) ;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my ($text_color1, $text_color2); 
my ($cat_id, $field_position, %file_fields, $fidx ) ;
my ($category, $db_field, $html_disp_order, $nbr_cols, $checked);

$text_color1 = "black" ;
#----- connect to the util database -----
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

#----- check for login --------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

util::header("Add/Update File Layout");    # Print HTML Header

&disp_file_layout();
&disp_java();

$util->footer();
$util->clean_up();
exit(0);


#===============================================================================
# Sub: disp_file_layout
#===============================================================================
sub disp_file_layout
{

	print << "end_of_html";

	<!-- Begin FILE TO UPLOAD Tbls ------------------------------ -->
	<FORM name="sub_add_form" action=file_layout_upd.cgi method=post>

	<TABLE cellSpacing=0 cellPadding=5 width=660 border=0>
	<TBODY>
	<TR>
	<TD align=middle>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
			<TR bgColor=#509C10 height=15>
				<TD align=middle width="100%" height=15>
				<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
				<B>Define File Layout</B> </FONT></TD>
			</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
			<TR bgColor="$light_table_bg" height=15>
				<TD align=left width="100%" height=15>
				<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>
				<br>
				Define the 'File Layout' for batch uploads by selecting from the desired 
				demographics listed below.  The 'File Layout' will be used to upload 
				Email data from ascii files.<br><br>
				</FONT></TD>
			</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
			<TR bgColor="$light_table_bg" height=15>
				<TD valign=top align=right width="05%" height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>
					1.&nbsp;&nbsp;</FONT></TD>
				<TD align=left width="95%" height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>
					Select/De-Select items by clicking the check boxes<br><br></FONT></TD>
			</TR>
			<TR bgColor="$light_table_bg" height=15>
				<TD valign=top align=right width="05%" height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>
					2.&nbsp;&nbsp;</FONT></TD>
				<TD align=left width="95%" height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>
					The Field Position represents the order in which the data must appear in the
					file.  The Field Numbers are automatically generated when you click the check boxes or
					you may specified the order by entering the Filed Position manualy.<br><br>
			</TR>
		</TBODY>
		</TABLE>

end_of_html

	#===== Perl Code =======================================================
	#---- Load Hash w/data from FILE_LAYOUT tbl (use to display data for current layout) ---------
	$sql = qq{select cat_id, field_position from file_layout where user_id = $user_id } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while ( ($cat_id, $field_position) = $sth->fetchrow_array() )
	{
		$reccnt++;
		$file_fields{$cat_id} = $field_position ;
	}
	$sth->finish();

	print qq{
		<!-- Begin Tbl Defn to display ChkBox, FldPos, DemoText ---------------------- -->
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
			<TR bgColor="$light_table_bg" height=15>
				<TD colspan=9 align=left width="100%" height=15>
				&nbsp;
				</TD>
			</TR>
	}; # end print-qq statement

	#--- Email Address is REQUIRED and ALWAYS the 1st item in the file -------------
	if ( $reccnt == 0 )
	{
		$reccnt = 1 ;   # If no recs set to 1 for Email-Addr
	}
	$sql2 = qq{select cat_id, category, db_field, html_disp_order from demo_cat where demo_cat.db_field = 'email_addr' } ;
	$sth2 = $dbhq->prepare($sql2) ;
	$sth2->execute();
	($cat_id, $category, $db_field, $html_disp_order) = $sth2->fetchrow_array();
	print qq{
		<TR bgColor="$light_table_bg">

		<TD colspan=1 width="02%" align="center" valign="bottom">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		<input type=hidden name=user_id  value="$user_id" ><br>
		<input type=hidden name=field_cnt  value="$reccnt" ><br>
		<input  readonly CHECKED value="$db_field|$cat_id" type="checkbox" name="chkbox" OnClick="set_field_pos('0');">
		</font>
		</td>

		<TD colspan=1 width="03%" align="center" valign="bottom">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=1>
		<input readonly type="input" name="fldpos" maxlength="4" size="3" value="1">
		</font>
		</td>

		<TD colspan=1 width="20%" align="left" valign="bottom">
		<FONT face="verdana,arial,helvetica,sans serif" color="#000000" size=2>
		&nbsp;Email Address
		</font>
		</td>
	}; # end print-qq stmt
	$sth2->finish();

	$nbr_cols = 3 ;
	$sql = qq{select cat_id, category, db_field, html_disp_order from demo_cat where demo_cat.db_field != 'email_addr' order by html_disp_order, category } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 1 ;   
	while ( ($cat_id, $category, $db_field, $html_disp_order) = $sth->fetchrow_array() )
	{
		$reccnt++;
		$fidx = $reccnt - 1;  # Field Index - used to html arrays chkbox and fldpos
		if ( ($reccnt % $nbr_cols) == 1  && $reccnt != 1 )
		{
			print qq{<tr bgColor="$light_table_bg"> \n };
		}

		if (exists($file_fields{$cat_id}) )
		{
			$field_position = $file_fields{$cat_id} ;
			$checked = "CHECKED" ;
		}
		else
		{
			$field_position = "";
			$checked = "" ;
		}

		print qq{
			<TD colspan=1 width="02%" align="center" valign="bottom">
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			<input $checked value="$db_field|$cat_id" type="checkbox" name="chkbox" OnClick="set_field_pos('$fidx');">
			</font>
			</td>

			<TD colspan=1 width="03%" align="center" valign="bottom">
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=1>
			<input type="input" name="fldpos" maxlength="4" size="3" value="$field_position" OnChange="chk_chkbox('$fidx');">
			</font>
			</td>

			<TD colspan=1 width="20%" align="left" valign="bottom">
			<FONT face="verdana,arial,helvetica,sans serif" color="#000000" size=2>
			&nbsp;$category
			</font>
			</td>
			};   # end print qq statement

		if ( ($reccnt % $nbr_cols) == 0 )
		{
			print qq{</tr> \n} ; 
		}

	} # end while reading DEMO_CAT

	$sth->finish();

	if ( ($reccnt % $nbr_cols) != 0 )
	{
		print "</tr> \n" ; 
	}

	print qq{<TR bgColor="$light_table_bg">};  # blank line for spacing.....
	print qq{<TD colspan=9> <br>};
	print qq{</TD>};
	print qq{<TR>};

	print qq{<TR bgColor="$light_table_bg">};
	print qq{<TD align="center" colspan=9>};
	print qq{<input type=button name=BtnSave value="Save"         ONCLICK="return save();">&nbsp;&nbsp;};
	print qq{<input type=button name=BtnHome value="Home"         ONCLICK="return goto_url('mainmenu.cgi');">&nbsp;&nbsp;};
#	print qq{<input type=button name=BtnDoc  value="Instructions" ONCLICK="return goto_url('file_layout_instructions.cgi');">&nbsp;&nbsp;};
	print qq{<input type=button name=BtnSeq  value="Re-Sequence"  ONCLICK="return resequence_fldpos();">};
	print qq{<!--		<input type=button name=BtnUpdate   value="Update Layout"     ONCLICK="return goto_url('file_layout_upd.cgi');">   -->};
	print qq{</TD>};
	print qq{<TR>};

	print qq{</TBODY> </TABLE> <!-- close out table displaying chkboxes, fldpos and text ----------------- --> };

	print qq{</TD>};
	print qq{</TR>};
	print qq{</TBODY>};
	print qq{</TABLE>};
	print qq{</FORM>};

} # end sub disp_file_layout


#===============================================================================
# Sub: disp_java
#===============================================================================
sub disp_java
{
	print << "end_of_html" ;

	<script language="JavaScript">

	//--------------------------------------------------------------------------
	// Un check the chkbox field if the value has been deleted
	//--------------------------------------------------------------------------
	function save() 
	{ 
		resequence_fldpos();
		document.sub_add_form.submit();
        return true;
	}
	

	//--------------------------------------------------------------------------
	// Un check the chkbox field if the value has been deleted
	//--------------------------------------------------------------------------
	function chk_chkbox(fidx) 
	{ 
		if ( document.sub_add_form.fldpos[fidx].value == "" )
		{
			document.sub_add_form.chkbox[fidx].checked = false ;
		}
		else
		{
			document.sub_add_form.chkbox[fidx].checked = true ;
		}
	} 


	//--------------------------------------------------------------------------
	// Re-Sets Field Position values to begin w/1 thru N where N is the last
	// field w/a value.  Field Pos values are integers and do NOT skip a value.
	// (eg 1, 2, 3, 4 etc).
	//--------------------------------------------------------------------------
	function resequence_fldpos() 
	{ 
		var Array1, Array2, ArrayLen, fidx, fpos, npos;
		var ArrayStr, reg3, reccnt, pipe_pos;

		Array1 = new Array();
		Array2 = new Array();

		reg3 = /^0/ ;         // from 1st pipe to end of string

		//--------------------------------------------------------------------------
		// Create a string w/values separated via the PIPE char as: FldPos|HtmlIndex
		//--------------------------------------------------------------------------
		ArrayLen = document.sub_add_form.fldpos.length ;
		for (i=0; i <= (ArrayLen -1); i++)
		{
			if ( document.sub_add_form.fldpos[i].value != "" )
			{	// Zero Padd field for total length of 4 chars - so it sorts correctly
				npos = zero_pad(document.sub_add_form.fldpos[i].value, ( 4 - document.sub_add_form.fldpos[i].value.length)) ;
				ArrayStr = npos + "|" + i ;
				Array1.push(ArrayStr);
			}
		}

		Array2 = Array1.sort();          // sort Array1 to get in order of Field Position
		ArrayLen = Array2.length ;
		reccnt = 0;
		for (i=0; i <= (ArrayLen -1); i++)
		{
			reccnt++;
			ArrayStr = Array2.shift();

			//----- Find Location of PIPE char -------------------
			for(x=0; x <= (ArrayStr.length -1); x++)
			{
				if ( ArrayStr.substr(x,1) == "|" )
				{
					pipe_pos = x ;
				}
			}

			//---- Set fidx(eg indexes chkbox, fldpos), fpos(eg Filed Pos value)
   			fidx = ArrayStr.substr((pipe_pos + 1), (ArrayStr.length -1) - pipe_pos );
   			fpos = ArrayStr.substr(0,pipe_pos);      // Start at Zero for length of pipe_pos

   			fpos = fpos.replace(reg3, "");        //Remove leading Zero
   			fpos = fpos.replace(reg3, "");        //Remove leading Zero
   			fpos = fpos.replace(reg3, "");        //Remove leading Zero

			document.sub_add_form.fldpos[fidx].value = reccnt;
		}

	document.sub_add_form.field_cnt.value = reccnt;
	return 1;
	} 


	//--------------------------------------------------------------------------
	// Zero Pad (eg left) the Field Position value to get correct sort
	// (eg so 9 sorts after 10 etc.)
	//--------------------------------------------------------------------------
	function zero_pad(NbrIn, PadLen) 
	{ 
		var padstr, y;
		padstr = "";
		for(y=1; y <= PadLen; y++)
		{
			padstr = padstr + "0" ;
		}
		padstr = padstr + NbrIn ;
		return padstr ;
	}


	//--------------------------------------------------------------------------
	// Set Field Position value 
	//  1. If checked and value is null then set to last field cnt + 1
	//  2. If un-checked then set Field Position value to null
	//
	//  Note: Field Position values are re-sequenced prior to Add/Update
	//--------------------------------------------------------------------------
	function set_field_pos(fidx) 
	{ 
		var FieldPos;
		var ChkBox;

		if ( fidx == 0 )
		{	// fidx of 0 = Email Address - this should always be checked
			document.sub_add_form.chkbox[fidx].checked = true ;
		}

 		if ( document.sub_add_form.chkbox[fidx].checked == true && fidx != 0 )
  		{
			if ( document.sub_add_form.fldpos[fidx].value  ==  "" )
			{
   				FieldPos = document.sub_add_form.field_cnt.value ;
   				FieldPos++;
   				document.sub_add_form.fldpos[fidx].value = FieldPos ;
   				document.sub_add_form.field_cnt.value = FieldPos ;
			}
  		}
  		else
  		{
			if ( fidx != 0 )   // Dont change Email Address
			{
    			document.sub_add_form.fldpos[fidx].value = "" ;
			}
  		}

		return 1 ;  // TRUE
	}
	

	function goto_url(h) 
	{ 
		top.location.href = h; 
		return 0;  
	} 

	</script>

end_of_html

} # end sub disp_java

