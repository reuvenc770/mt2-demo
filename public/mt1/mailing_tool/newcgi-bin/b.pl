#!/usr/bin/perl
$t="xeyuv/unsub.jpg";
$_=$t;
if ( /\// )
{
	print "<$t>\n";
}
else
{
	print "<unsub/$t>\n";
}
