<?php
/**
 * @package Syon Google Analytics
 * @version 1.2
 */
/*
Plugin Name: Syon Google Analytics
Plugin URI: http://www.syonplugins.com/
Description: This is a simple plugin to add google analytics functionality to your wordpress site.
Author: Syonplugins
Version: 1.2
Author URI: 
*/

add_action('admin_menu', 'add_syon_analytics_menu');
function add_syon_analytics_menu()
{
	   add_menu_page(__('Syon Analytics','menu-test'), __('Syon Analytics','menu-test'), 'manage_options', 'manage-analytics', 'manage_analytics' );
}
function parse_file()
{
	$fname="../wp-content/plugins/".get_analytics_dir_name()."/content.txt";
	if(file_exists($fname))
	{
		$content=file_get_contents($fname);
	}
	else
	{
		return "null";
	}
	if(strlen($content)>10)
	{
		$content=str_replace("<Type>","",$content);
		$content=str_replace("</Code>","",$content);
		$explode=explode("</Type><Code>",$content);
		$returnarray[0]=ltrim(rtrim($explode[0]));
		$returnarray[1]=ltrim(rtrim($explode[1]));
		return $returnarray;
	}
	else
	{
		return "null";
	}
}
register_activation_hook(__FILE__,'analytics_install');
register_deactivation_hook(__FILE__,'analytics_uninstall');
function analytics_install()
{
	$fname="../wp-content/plugins/".get_analytics_dir_name()."/content.txt";
	$fopen=fopen($fname,"w+");
	$fwrote=fwrite($fopen,"");

}
function analytics_uninstall()
{
	$fname="../wp-content/plugins/".get_analytics_dir_name()."/content.txt";
	if(file_exists($fname))
	{
		unlink($fname);
	}
	
	
}
function get_analytics_dir_name()
{
$dirnamestr=plugin_basename(__FILE__);
$dirnamearray=explode("/",$dirnamestr);
$dirname=trim($dirnamearray[0]);
return $dirname;
}
add_action("admin_head","analytics_admin_head");
function analytics_admin_head()
{
	?>
	<script type="text/javascript">
	function view_analytics_option()
	{
		var cform=document.getElementById("cform").value;
		if(cform=="js")
		{
			document.getElementById("frmoth").style.display="none";
			document.getElementById("ckeyword").value="";
			document.getElementById("jscode").focus();
			document.getElementById("frmjs").style.display="";
			
		}
		else
		{
				document.getElementById("frmjs").style.display="none";
				document.getElementById("jscode").value="";
				document.getElementById("ckeyword").focus();
				document.getElementById("frmoth").style.display="";
		}
		
	}
	</script>
    <style type="text/css">
	.mselect{
		border-radius:10px;
		width:200px;
		font-weight:bold;
		color:#909090;
	}
	.mtextarea
	{
		border-radius:10px;
		width:380px;
		font-weight:bold;
		height:150px;
	}
	.syon-wrap h1{
	font-size:28px;
	margin:10px 0;
	padding:10px 8px;
	border-bottom:1px solid #e3e3e3;
	background-color:#fafafa;
	}
.syon-wrap h2{
	font-size:22px;
	margin:10px 0;
	padding:10px 0;
	border-bottom:1px solid #e3e3e3;
	}
.syon-wrap h1 span{
	font-size:12px;
	padding:8px 0 0 0;
	display:block;
	font-weight:normal;
	}
	</style>
	<?
}
function manage_analytics()
{
	if(isset($_POST['action']))
	{
		$action=trim($_POST['action']);
		if($action=="save")
		{
			$cform=trim($_POST['cform']);
			if($cform)
			{
				$js=$_POST['jscode'];
				$js=stripslashes($js);
				$fcontent="<Type>$cform</Type>";
				$fcontent.="<Code>$js</Code>";
				$fname="../wp-content/plugins/".get_analytics_dir_name()."/content.txt";
				$fopen=fopen($fname,"w+");
				if(fwrite($fopen,$fcontent))
				{
					?>
					<script type="text/javascript">
					window.location="<?=$_SERVER['HTTP_REFERER'];?>";
					</script>
					<?
				}
			}

		}
	}
?>
<div class="syon-wrap"> 
	<h1>Syon Google Analytics <br /><span>Ver 1.0.0</span></h1>
<table cellspacing="2" cellpadding="2" width="100%">
        <tr>
    	<td>
        <?php
		$contentarray=parse_file();
		$ctype=$contentarray[0];
		$content=$contentarray[1];
		?>
              	<form action="" method="post">
            <input type="hidden" name="action" value="save" />
            <table cellpadding="2" cellspacing="2" width="100%">
            	<tr>
                	<td width="100">	
                    	<label>Code Type </label>
                    </td>
                    <td>
                    	<select name="cform" id="cform" class="mselect" onchange="view_analytics_option()">
                        	<option value="js" <?php if($ctype=="js") { ?> selected="selected"<? } ?>>Javascript</option>
                          <?php /*?>  <option value="oth" <?php if($ctype=="oth") { ?> selected="selected"<? } ?>>Keywords</option><?php */?>
                        </select>
                    </td>
                </tr>
                <tr id="frmjs" style="display:none;">
                	<td width="100" valign="top">	
                    	Your Google Analytic Code
                    </td>
                    <td>
                    	<textarea name="jscode" id="jscode" class="mtextarea"><? if($ctype=="js"){echo $content;}?></textarea>
                        
                    </td>
                </tr>
                <tr id="frmoth" style="display:none;">
                	<td width="100"  valign="top">	
                    	<label>Code</label>
                    </td>
                    <td>
                    	<textarea name="ckeyword" id="ckeyword" class="mtextarea"><? if($ctype=="oth"){echo $content;}?></textarea>
                    </td>
                </tr>
                <tr >
                	<td width="100"  valign="top">&nbsp;	
                    
                    </td>
                    <td>
                    	<input type="submit" value="Save Code">&nbsp;<input type="reset" value="Reset Form" />
                    </td>
                </tr>
				<script type="text/javascript">
				view_analytics_option();
				</script>
            </table>
            </form>
        </td>
    </tr>
    
</table>
</div>
<?
}
add_action("wp_head","apply_analytics");
function apply_analytics()
{

	$fname="wp-content/plugins/".get_analytics_dir_name()."/content.txt";
	if(file_exists($fname))
	{
		$content=file_get_contents($fname);
	}
	else
	{
		return "null";
	}
	if(strlen($content)>10)
	{
		$content=str_replace("<Type>","",$content);
		$content=str_replace("</Code>","",$content);
		$explode=explode("</Type><Code>",$content);
		$returnarray[0]=ltrim(rtrim($explode[0]));
		$returnarray[1]=ltrim(rtrim($explode[1]));
		echo $returnarray[1];
	}
}
?>