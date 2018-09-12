<?php
/**
 * @name MOOJ Proforms
 * @version 1.0
 * @package proforms
 * @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

define("M4J_IS_SEARCH", false);
require_once(M4J_INCLUDE_VALIDATE);
require_once(M4J_INCLUDE_FUNCTIONS);

$stid = JRequest::getInt("stid",null);
$fids = JRequest::getString("fids",null);
$stiid = JRequest::getInt("stiid",null);
$eid = JRequest::getInt("eid",null);
$content = JRequest::getString("content",null);

if(! $stid || !$fids) exit();
$fids = explode(",",$fids);

$query = "SELECT * FROM #__m4j_storage AS `s` ".
		 "\n LEFT JOIN #__users AS `u` ON (`s`.`user_id` = `u`.`id`)" .
		 "\n WHERE `stid`='".$stid."' LIMIT 1";  
$database->setQuery( $query );
$storage = $database->loadObject();

$format = str_replace("y","Y",JText::_("DATE_FORMAT_JS1"). " - H:i:s");
$date = date($format,strtotime($storage->date));

$changed = false;
// Edit / insert stiid
if($task=="edit"){
	
	if($stiid){
		MDB::update("#__m4j_storage_items", array("content"=>dbEscape($content)), MDB::_("stiid",$stiid));
		$changed = true;
	}else{
		if($eid){
			MDB::insert( "#__m4j_storage_items", array( "stid"=>$stid, "eid"=>$eid, "content"=>dbEscape($content) ) );
			$changed = true;
		}
	}
}


?>
<script type="text/javascript">
function docPrint(){
	mInfoTipShadow.style.display="none";
	mInfoTipArrow.style.left ="-9999em";
	mInfoTipWrap.style.left ="-9999em";

	dojo.byId("m4jHeading1").style.border = "1px solid black";
	dojo.byId("m4jHeading2").style.border = "1px solid black"; 
	dojo.byId("m4jHeading3").style.border = "1px solid black"; 
	
	var images = document.getElementsByTagName("IMG");
	var size = images.length;
	for(t=0; t< size; t++){
		images[t].style.visibility = "hidden";
	}
	window.print();

	setTimeout(dojo.hitch(this,function(){
	for(t=0; t< size; t++){
		images[t].style.visibility = "visible";
	}
	dojo.byId("m4jHeading1").style.border = "none";
	dojo.byId("m4jHeading2").style.border = "none";
	dojo.byId("m4jHeading3").style.border = "none";
	}),500);
	 		
}

function m4jFormSubmit(){
	document.m4jForm.submit(); 
}

function m4jItemEdit(stiid){
	document.m4jForm.stiid.value = stiid;
	mWindow.content({width:480,height:320});
}

function m4jGetQuestion(eid){
	dojo.byId("m4jGetQuestion").innerHTML = dojo.byId("m4jQuestion_"+ eid ).innerHTML;
}

MoojWindow.prototype.setOutline("10px solid #007ec4");

<?php 
if($changed){
	echo 'parent.m4jChangeTD('.$stid.','.$eid.',"'.str_replace('"',"&quot;",$content).'");'."\n";
}

?>

</script>

<table id="m4jRecordTable" width="100%" cellpadding="0" cellspacing="0"
	border="0" style="font-size: 1.5em; background-color: #ffffff;">
	<tbody>

		<tr style="background-color: black; color: #ffffff; height: 32px;">
			<td id="m4jHeading1" colspan="3">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody>
					<tr>
						<td><span style="padding-left: 10px;"><?php echo M4J_LANG_SERVER_INFO; ?></span></td>
						<td width="32px"><img info="<?php echo M4J_LANG_PRINT; ?>"
							src="<?php echo M4J_IMAGES; ?>/print.png"
							style="border: none; cursor: pointer;"
							onclick="javascript: docPrint();" /></td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
		
		<tr onmouseover="javscript: this.style.backgroundColor ='#ededed';"
			onmouseout="javscript: this.style.backgroundColor ='#ffffff';">
			<td><?php echo M4J_LANG_CONFIRMATION; ?></td>
			<td colspan="2"><?php echo $storage->optin ? M4J_LANG_CONFIRMED : M4J_LANG_NOT_CONFIRMED; ?></td>
		</tr>

		<tr onmouseover="javscript: this.style.backgroundColor ='#ededed';"
			onmouseout="javscript: this.style.backgroundColor ='#ffffff';">
			<td><?php echo M4J_LANG_RECEIVED; ?></td>
			<td colspan="2"><?php echo $date; ?></td>
		</tr>

		<tr onmouseover="javscript: this.style.backgroundColor ='#ededed';"
			onmouseout="javscript: this.style.backgroundColor ='#ffffff';">
			<td><?php echo JText::_("User"); ?></td>
			<td colspan="2"><?php 
			if($storage->username){
				$userLink = '<a href="mailto:'.$storage->email.'">'.$storage->username.'</a>';
			}else{
				$userLink = "Guest";
			}
			echo $userLink ; 
			?></td>
		</tr>

		<tr onmouseover="javscript: this.style.backgroundColor ='#ededed';"
			onmouseout="javscript: this.style.backgroundColor ='#ffffff';">
			<td width="300px">IP</td>
			<td width="500px" colspan="2"><?php 
			$user_ip = $storage->user_ip ? $storage->user_ip : "NO DATA";
	
			if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$user_ip)){
				$ipWhois = '<a href="http://whois.domaintools.com/'.$user_ip.'" target="_blank">'.$user_ip."</a>";
			}else{
				$ipWhois = $user_ip;
			}
			echo $ipWhois; 
			?></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>


		<tr style="background-color: black; color: #ffffff; height: 32px;">
			<td id="m4jHeading2" ><span style="padding-left: 10px;"><?php echo M4J_LANG_QUESTION; ?></span></td>
			<td id="m4jHeading3" colspan="2"><span style="padding-left: 10px;"><?php echo M4J_LANG_ANSWER; ?></span></td>
		</tr>


		<?php
		foreach ($fids as $fid){
			$fid = (int) $fid;
			$query = "SELECT `question`,`alias` ,`eid`,`form`, `active` FROM #__m4j_formelements ".
				  "\n WHERE `fid`='".$fid."' AND NOT (`form` = 50 ) AND `active` = '1' ORDER BY `slot`,`sort_order` ASC";  
			$database->setQuery( $query );
			$questions = $database->loadObjectList();
			foreach($questions as $q){
				$query = "SELECT * FROM #__m4j_storage_items ".
					  "\n WHERE `stid`='".$stid."' AND `eid` = '".$q->eid."' LIMIT 1";  
				$database->setQuery( $query );
				$storage = $database->loadObject();

				$content = (isset($storage->content)) ? MReady::_(str_replace("\n","<br/>",$storage->content)) : " - ";
			
				
				$question = (trim($q->question) != "") ? $q->question : $q->alias ;
				echo '<tr onmouseover="javscript: this.style.backgroundColor =\'#cdcdcd\';" onmouseout="javscript: this.style.backgroundColor =\'#ffffff\';">'."\n";
				echo '<td align="left" valign="top" style="border-bottom: 1px solid gray; margin-bottom:3px;" id="m4jQuestion_'.$q->eid.'">'.$question,'</td>'."\n";

				echo '<td align="left" valign="top" style="border-bottom: 1px solid gray; margin-bottom:3px;">';
				if($q->form == 40){
					if(trim($content)== "") {
						echo " - ";
					}else{
						echo '<a class="m4jAttachment" href="'.M4J_DOWNLOAD.$storage->stiid.'">'. $content. "</a>";
					}
				}else if($q->form>=10 && $q->form<20){
					echo $content;
				} else {
					echo HTML_HELPERS_m4j::checkMailURL ($content,1) ;
				}
				echo '</td>'."\n";
				echo '<td align="left" valign="top" style="border-bottom: 1px solid gray; margin-bottom:3px;width: 16px;">';
				if($q->form != 40){
					
					
					
					$_stiid = "0";
					$contentQuery = "";
					if(isset($storage->stiid)){
						$_stiid = $storage->stiid;
						echo'<div id="raw_'.$storage->stiid.'" style="display:none;">'.stripslashes($storage->content).'</div>'."\n";
						$contentQuery = "_('content').innerHTML= _('raw_".$storage->stiid."').innerHTML;";
					}
					echo '<img onclick="javascript: mWindow.content({width:480,height:320}); m4jGetQuestion('.$q->eid.');  document.m4jForm.stiid.value = '. $_stiid .'; document.m4jForm.eid.value = '.$q->eid.'; '.$contentQuery.'" src="'.M4J_IMAGES.'/pen-small.png" style="border:none; cursor: pointer;" title="'.M4J_LANG_EDIT.'" />';
				}
				echo '</td>'."\n";
				echo '</tr>'."\n";
				//		echo '<script type="text/javascript"> parent.m4jChangeSTIID('.$storage->stiid.',"depp");</script>	'."\n";
			}
		}


		?>
	</tbody>
</table>
<div id="changeContainer"
	style="display: none; ">
<center>
<div style="display:block; text-align:left; width: 90%; height: 50px; margin-bottom: 10px; overflow:hidden; margin-top:7px;">
	<span id="m4jGetQuestion" ></span><br />
</div>



<form id="m4jForm" name="m4jForm" method="post"
	action="<?php echo M4J_STORAGE_VIEW.$stid.'&fids='.implode(",",$fids).'&tmpl=component'; ?>">

<input type="hidden" name="task" value="edit"></input> 
<input type="hidden" name="stiid" value=""></input> 
<input type="hidden" name="eid" value=""></input> 

<textarea id="content" name="content"
	style="width: 90%; height: 200px; outline: 1px solid gray;"></textarea>
	
<table width="90%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td valign="top" align="left"><a id="m4jSaveItem" class="m4jSelect" style="margin-top: 10px;" onclick="javascript: m4jFormSubmit(); return false;">
			<div class="m4jSelectExtend"
				style="font-size: 12px; padding-top: 3px;"><?php echo M4J_LANG_SAVE; ?></div>
			</a></td>
			<td valign="top" align="left"><a id="m4jCancelSave" class="m4jSelect m4jSelectDisabled" style="margin-top: 10px;" onclick="javascript: mWindow.close(); return false;">
			<div class="m4jSelectExtend"
				style="font-size: 12px; padding-top: 3px;"><?php echo M4J_LANG_CANCEL; ?></div>
			</a></td>
	
	</tbody>
</table>


</form>
</center>
</div>

<script type="text/javascript"> 
var changeContainer = dojo.byId("changeContainer");
MoojWindow.prototype.setCode(changeContainer.innerHTML);
changeContainer.innerHTML = "";
</script>

		<?php
		echo'
	  <script type="text/javascript" src="'.M4J_JS.'proforms-footer.js"></script>
	  <script type="text/javascript" src="'.M4J_JS_INFO.'"></script>
	  <script type="text/javascript" src="'.M4J_JS_PREVIEW.'"></script>
	  <script type="text/javascript" src="'.M4J_JS.'parsing.js"></script>
	';
?>