<?
if(!isadmin()){forcenoaccess();};

switch ($op)
{
    case "rate_list":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Manage SMS rate</h2>
	    <p>
	    <input type=button value=\"Add rate\" onClick=\"javascript:linkto('menu.php?inc=rate_mgmnt&op=rate_add')\" class=\"button\" />
    <table cellpadding=1 cellspacing=2 border=0 width=100%>
    <tr>
        <td class=box_title width=5>*</td>
        <td class=box_title width=300>Destination</td>
        <td class=box_title width=>Prefix</td>
        <td class=box_title width=>Rate</td>
        <td class=box_title width=75>Action</td>
    </tr>		    
	";
	$i=0;
	$db_query = "SELECT * FROM "._DB_PREF_."_tblRate ORDER BY dst";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result))
	{
	    $i++;
            $td_class = ($i % 2) ? "box_text_odd" : "box_text_even";
	    $action = "<a href=menu.php?inc=rate_mgmnt&op=rate_edit&rateid=$db_row[id]>$icon_edit</a>";
	    $action .= "<a href=\"javascript: ConfirmURL('Are you sure you want to delete rate for destination `$db_row[dst]` with prefix `$db_row[prefix]` ?','menu.php?inc=rate_mgmnt&op=rate_del&rateid=$db_row[id]')\">$icon_delete</a>";
	    $content .= "
    <tr>
	<td class=$td_class>&nbsp;$i.</td>
	<td class=$td_class>$db_row[dst]</td>
	<td class=$td_class>$db_row[prefix]</td>	
	<td class=$td_class>$db_row[rate]</td>	
	<td class=$td_class align=center>$action</td>
    </tr>
    ";
	}
	$content .= "</table>";
	echo $content;
	echo "
	    <p>
	    <input type=button value=\"Add rate\" onClick=\"javascript:linkto('menu.php?inc=rate_mgmnt&op=rate_add')\" class=\"button\" />
	";
	break;
    case "rate_del":
	$rateid = $_REQUEST[rateid];
	$dst = rateid2dst($rateid);
	$prefix = rateid2prefix($rateid);
	$error_string = "Fail to delete destination `$dst` with prefix `$prefix` !";
	$db_query = "DELETE FROM "._DB_PREF_."_tblRate WHERE id='$rateid'";
	if (@dba_affected_rows($db_query))
	{
	    $error_string = "Destination `$dst` with prefix `$prefix` has been deleted!";
	}
	header ("Location: menu.php?inc=rate_mgmnt&op=rate_list&err=".urlencode($error_string));
	break;
    case "rate_edit":
	$rateid = $_REQUEST[rateid];
	$dst = rateid2dst($rateid);
	$prefix = rateid2prefix($rateid);
	$rate = rateid2rate($rateid);
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Edit rate</h2>
	    <p>
	    <form action=menu.php?inc=rate_mgmnt&op=rate_edit_save method=post>
	    <input type=hidden name=rateid value=\"$rateid\">
	<table width=100% cellpadding=1 cellspacing=2 border=0>
	    <tr>
		<td>Destination</td><td>:</td><td><input type=text size=30 maxlength=30 name=up_dst value=\"$dst\"></td>
	    </tr>
	    <tr>
		<td>Prefix</td><td>:</td><td><input type=text size=30 maxlength=30 name=up_prefix value=\"$prefix\"></td>
	    </tr>
	    <tr>
		<td>Rate</td><td>:</td><td><input type=text size=30 maxlength=30 name=up_rate value=\"$rate\"></td>
	    </tr>
	</table>	    
	    <p><input type=submit class=button value=save>
	    </form>
	";
	echo $content;
	break;
    case "rate_edit_save":
	$rateid = $_POST[rateid];
	$up_dst = $_POST[up_dst];
	$up_prefix = $_POST[up_prefix];
	$up_rate = $_POST[up_rate];
	$error_string = "No changes made!";
	if ($rateid && $up_dst && $up_prefix && $up_rate)
	{
	    $db_query = "UPDATE "._DB_PREF_."_tblRate SET c_timestamp='".mktime()."',dst='$up_dst',prefix='$up_prefix',rate='$up_rate' WHERE id='$rateid'";
	    if (@dba_affected_rows($db_query))
	    {
	        $error_string = "Rate for destination `$up_dst` with prefix `$up_prefix` has been saved";
	    }
	    else
	    {
	        $error_string = "Fail to save rate for destination `$up_dst` with prefix `$up_prefix`";
	    }
	}
	else
	{
	    $error_string = "Empty field is not allowed";
	}
	header ("Location: menu.php?inc=rate_mgmnt&op=rate_edit&rateid=$rateid&err=".urlencode($error_string));
	break;
    case "rate_add":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Add rate</h2>
	    <p>
	    <form action=menu.php?inc=rate_mgmnt&op=rate_add_yes method=post>
	<table width=100% cellpadding=1 cellspacing=2 border=0>
	    <tr>
		<td width=175>Destination</td><td width=5>:</td><td><input type=text size=30 maxlength=30 name=add_dst value=\"$add_dst\"></td>
	    </tr>
	    <tr>
		<td>Prefix</td><td>:</td><td><input type=text size=30 maxlength=30 name=add_prefix value=\"$add_prefix\"></td>
	    </tr>
	    <tr>
		<td>Rate</td><td>:</td><td><input type=text size=30 maxlength=30 name=add_rate value=\"$add_rate\"></td>
	    </tr>
	</table>	    
	    <p><input type=submit class=button value=Add>
	    </form>
	";
	echo $content;
	break;
    case "rate_add_yes":
	$add_dst = $_POST[add_dst];
	$add_prefix = $_POST[add_prefix];
	$add_rate = $_POST[add_rate];
	if ($add_dst && $add_prefix && $add_rate && ($add_rate >= 0))
	{
	    $db_query = "SELECT * FROM "._DB_PREF_."_tblRate WHERE prefix='$add_prefix'";
	    $db_result = dba_query($db_query);
	    if ($db_row = dba_fetch_array($db_result))
	    {
		$error_string = "Rate to destination `$db_row[dst]` with prefix `$db_row[prefix]` already exists!";
	    }
	    else
	    {
		$db_query = "
		    INSERT INTO "._DB_PREF_."_tblRate (dst,prefix,rate)
		    VALUES ('$add_dst','$add_prefix','$add_rate')
		";
		if ($new_uid = @dba_insert_id($db_query))
		{
		    $error_string = "Rate to destination `$add_dst` with prefix `$add_prefix` has been added";
		}
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=rate_mgmnt&op=rate_add&err=".urlencode($error_string));
	break;
}

?>