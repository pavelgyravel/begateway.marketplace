<?php
IncludeModuleLangFile(__FILE__);
$module_id = "begateway.marketplace";
\Bitrix\Main\Loader::includeModule($module_id);

$dbSites = CSite::GetList(($b = "sort"), ($o = "asc"), Array("ACTIVE" => "Y"));
$arSites = array();
$aSubTabs = array();
while ($site = $dbSites->Fetch()) {
	$site["ID"] = htmlspecialcharsbx($site["ID"]);
	$site["NAME"] = htmlspecialcharsbx($site["NAME"]);
	$arSites[] = $site;
	$aSubTabs[] = array("DIV" => "opt_site_".$site["ID"], "TAB" => "(".$site["ID"].") ".$site["NAME"], 'TITLE' => '');
}

$subTabControl = new CAdminViewTabControl("subTabControl", $aSubTabs);

if ($REQUEST_METHOD == "GET" && $RestoreDefaults <> '' && $beMarketplacePerms == "W" && check_bitrix_sessid()) {
	COption::RemoveOption($module_id);
}

$aTabs = array(
  array("DIV" => "edit1", "TAB" => GetMessage("BEMARKETPLACE_SETTINGS"), "ICON" => "", "TITLE" => GetMessage("BEMARKETPLACE_MODULE_SETTINGS")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
echo bitrix_sessid_post();
$tabControl->BeginNextTab();
$subTabControl->Begin();
foreach ($arSites as $site) {
  $subTabControl->BeginNextTab();
  ?>
  <div class="bemarketplace_applications bemarketplace_application_<?=$site["LID"]?>" data-site-id="<?=$site["LID"]?>"></div>
  <input type="button" onclick="bemarketplace_application('<?=$site['LID']?>'); return false;" value="<?php echo GetMessage("BEMARKETPLACE_ADD_APPLICATION"); ?>">	
  <?
}
$subTabControl->End();
$tabControl->End();
?>

<script>
  function bemarketplace_application_redraw(site_id) {
    BX.ajax.loadJSON('/bitrix/admin/bemarketplace_applications.php?action=get_applications&SITE_ID='+site_id+'&<?=bitrix_sessid_get();?>', function(res) {
      if (!!res)
      {
        if (res.result == 'ok')
        {
          var html = '<div><div>ID</div><div>SITE ID</div><div>HOST</div><div>CLIENT ID</div><div>RETURN URL</div></div>';
          res.applications.forEach(function(application){
            html += "<div>";
            html += "<div>"+ application['ID'] +"</div>";
            html += "<div>"+ application['SITE_ID'] +"</div>";
            html += "<div>"+ application['HOST'] +"</div>";
            html += "<div>"+ application['CLIENT_ID'] +"</div>";
            html += "<div>"+ application['RETURN_URL'] +"</div>";
            html += '<div><a href="javascript: edit_application('+application['ID']+')" class="form-action-button action-edit" title="<?php echo GetMessage("BEMARKETPLACE_EDIT"); ?>"></a><a href="javascript: delete_application('+application['ID']+', \''+application['SITE_ID']+'\')" class="form-action-button action-delete" title="<?php echo GetMessage("BEMARKETPLACE_DELETE"); ?>"></a></div>';
            html += "</div>";
          });
          var el = document.getElementsByClassName("bemarketplace_application_"+site_id);
          el[0].innerHTML = html;
        }
        else
        {
          var el = document.getElementsByClassName("bemarketplace_application_"+site_id);
          el[0].innerHTML = "<div class='error'>"+res.error || + "</div>";
        }
      }
    });
  }

  function edit_application(application_id) {
    BX.ajax.loadJSON('/bitrix/admin/bemarketplace_applications.php?action=get_applications&ID='+application_id+'&<?=bitrix_sessid_get();?>', function(res) {
      if (!!res) {
        if (res.result == 'ok') {
          bemarketplace_application(res.applications[0]['SITE_ID'], res.applications[0]);
        } else {
          alert(res.error);
        }
      }
    });
  }

  function delete_application(application_id, site_id) {
    var popup_id = Math.random();
    var wnd = new BX.PopupWindow('popup_' + popup_id, window, {
      titleBar: {content: BX.create('DIV', {text: '<?php echo GetMessage("BEMARKETPLACE_APPLICATION_DELETING"); ?>' })},
      draggable: true,
      autoHide: false,
      closeIcon: true,
      closeByEsc: true,
      content: '<?php echo GetMessage("BEMARKETPLACE_APPLICATION_DELETING_CONFIRM"); ?>',
      overlay: {
        backgroundColor: 'black', opacity: '80'
      },
      buttons: [
        new BX.PopupWindowButton({
          text : '<?php echo GetMessage("BEMARKETPLACE_DELETE"); ?>',
          className : "popup-window-button-decline",
          events : {
            click : function(){
              wnd.close();
              BX.ajax.loadJSON('/bitrix/admin/bemarketplace_applications.php?action=delete_application&ID='+application_id+'&<?=bitrix_sessid_get();?>', function(res) {
                if (!!res) {
                  if (res.result == 'ok') {
                    bemarketplace_application_redraw(site_id);
                  } else {
                    alert(res.error);
                  }
                }
              })
            }
          }
        }),
        new BX.PopupWindowButtonLink({
          text : BX.message('JS_CORE_WINDOW_CANCEL'),
          className : "popup-window-button-link-cancel",
          events : {
            click : function() {wnd.close()}
          }
        })
      ]
    });

    wnd.show();
  }

  function bemarketplace_application(site_id, data) {
    var popup_id = Math.random();
    data = data || {}

    var content = '<div class="form-crm-settings form-crm-settings-hide-auth" id="popup_cont_'+popup_id+'"><form name="form_'+popup_id+'"><input type="hidden" name="SITE_ID" value="'+BX.util.htmlspecialchars(site_id)+'"><table cellpadding="0" cellspacing="2" border="0"><tr><td align="right">Site ID:</td><td>'+BX.util.htmlspecialchars(site_id)+'</td></tr><tr><td align="right">Client ID:</td><td><input type="text" name="CLIENT_ID" value="'+BX.util.htmlspecialchars(data.CLIENT_ID||'')+'"></td></tr><tr><td align="right">Client secret:</td><td><input type="text" name="CLIENT_SECRET" value="'+BX.util.htmlspecialchars(data.CLIENT_SECRET||'')+'"></td></tr><tr><td align="right">Host:</td><td><input type="text" name="HOST" value="'+BX.util.htmlspecialchars(data.HOST||'')+'"></td></tr></table></form></div>';
    
    var wnd = new BX.PopupWindow('popup_' + popup_id, window, {
		titleBar: {content: BX.create('DIV', {text: (data.CLIENT_ID) ? '<?php echo GetMessage("BEMARKETPLACE_APPLICATION_EDIT"); ?>' : '<?php echo GetMessage("BEMARKETPLACE_APPLICATION_CREATE"); ?>'  })},
		draggable: true,
		autoHide: false,
		closeIcon: true,
		closeByEsc: true,
		content: content,
    overlay: {
      backgroundColor: 'black', opacity: '80'
    },
		buttons: [
			new BX.PopupWindowButton({
				text : BX.message('JS_CORE_WINDOW_SAVE'),
				className : "popup-window-button-accept",
				events : {
					click : function(){bemarketplace_application_save(wnd, data, document.forms['form_'+popup_id])}
				}
			}),
			new BX.PopupWindowButtonLink({
				text : BX.message('JS_CORE_WINDOW_CANCEL'),
				className : "popup-window-button-link-cancel",
				events : {
					click : function() {wnd.close()}
				}
			})]
    });

    wnd.show();
  }

  function bemarketplace_application_save(wnd, data_old, form){
    var data = {
      SITE_ID: form.SITE_ID.value,
      CLIENT_ID: form.CLIENT_ID.value,
      CLIENT_SECRET: form.CLIENT_SECRET.value,
      HOST: form.HOST.value
    }

    if (data_old.ID) {
      data['ID'] = data_old.ID;
    }

    BX.ajax({
			method: 'POST',
			dataType: 'json',
			url: '/bitrix/admin/bemarketplace_applications.php?action=save_application&<?=bitrix_sessid_get();?>',
			data: data,
			onsuccess: function(result){
        wnd.close()
        bemarketplace_application_redraw(form.SITE_ID.value);
      }
		});

  }

  BX.ready(function() {
    var sites = document.querySelectorAll('.bemarketplace_applications');
    sites.forEach(function(site){
      bemarketplace_application_redraw(site.dataset['siteId']);
    });
  });
</script>


<style>
  .form-action-button {
    display: inline-block;
    height: 17px;
    width: 17px;
  }
  .action-edit {
    background: scroll transparent url(/bitrix/images/form/options_buttons.gif) no-repeat 0 0;
  }
  .action-delete {
    background: scroll transparent url(/bitrix/images/form/options_buttons.gif) no-repeat -29px 0;
  }

  .bemarketplace_applications > div {
    display: flex;
    border-bottom: 1px solid #DDD;
  }
  .bemarketplace_applications > div > div {
    padding: 10px 15px;
    overflow-wrap: break-word;
  }

  .bemarketplace_applications > div > div:nth-child(1) {
    width:30px;
  }

  .bemarketplace_applications > div > div:nth-child(2) {
    width:50px;
  }

  .bemarketplace_applications > div > div:nth-child(3) {
    width:250px;
  }
  
  .bemarketplace_applications > div > div:nth-child(4) {
    width:250px;
  }

  .bemarketplace_applications > div > div:nth-child(5) {
    width:300px;
  }

  .bemarketplace_applications .error {
    padding: 15px;
  }

  .popup-window-titlebar div {
      padding-top: 15px;
      font-size: 16px;
  }
</style>