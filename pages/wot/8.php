<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2008  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/ ?>
<? if(array_key_exists('error',$_SESSION['_config']) && $_SESSION['_config']['error'] != "") { ?><font color="#ff0000">ERROR: <?=$_SESSION['_config']['error']?></font><? unset($_SESSION['_config']['error']); } ?>
<form method="post" action="wot.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("My Listing")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Directory Listing")?>:</td>
    <td class="DataTD" align="left">
	<select name="listme">
		<option value="0"><?=_("I don't want to be listed")?></option>
		<option value="1"<? if($_SESSION['profile']['listme'] == 1) echo " selected"; ?>><?=_("I want to be listed")?></option>
	</select>
    </td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Contact information")?>:</td>
    <td class="DataTD"><textarea name="contactinfo" cols="40" rows="5" wrap="virtual"><?=$_SESSION['profile']['contactinfo']?></textarea></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Update")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="csrf" value="<?=make_csrf('chgcontact')?>" />
</form>
<p><?=_("Please note: All html will be stripped from the contact information box, a link to an email form will automatically be inserted to ensure your privacy.")?></p>
