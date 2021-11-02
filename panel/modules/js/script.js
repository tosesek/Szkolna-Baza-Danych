$(document).ready(function() {
  DoAllStuff();

  $(document).click(function(e) {
    if(!$(e.target).closest(".global_header .accountpanel, .account_panel_container .container, .global_dialogs_holder").length) {
      ToggleAccountPanel(false);
    }
  });
});

var keyCodes = {
  enter: 13,
  escape: 27,
  space: 32,
  up: 38,
  down: 40,
  left: 37,
  right: 39,
  backspace: 8,
  plus: 187,
  minus: 189
}

function ValidateValue(value, checkforcharacters=false) {
  var loc_ok = true;
  if(value==undefined) {loc_ok = false;}
  if(value=='') {loc_ok = false;}
  if(value && !value.replace(/\s/g, '').length) {loc_ok = false;}
  if(value==null){loc_ok=false;}
  if(checkforcharacters) {
    var regexp = /[A-Za-z0-9]+$/;
    return regexp.test(value);
  }

  return loc_ok;
}
function ValidateInput(input_element, b_Highlight=true) {
  if(!ValidateValue(input_element.val())) {
    if(b_Highlight) input_element.attr('status', 'error');
    return false;
  }
  else {
    if(b_Highlight) input_element.attr('status', 'ok');
    return true;
  }
}
function ValidateEmail(email) {
  // Nie mam pojęcia jak to działa, ale nie ważne
  // Ważne, że działa XD
  
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
function ValidatePassword(string) {
  var regex=/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{8,}$/;
  return regex.test(string);
}
function HasSpaces(string) {
  return /\s/g.test(string);
}
function HasNumbers(string) {
  return /\d/.test(string);
}
function SetInputError(input) {
  input.attr('status', 'error');
}
function SetInputOk(input) {
  input.attr('status', 'ok');
}
function IsNumber(checkhandle) {
  if($.isNumeric(checkhandle)) {
    return true;
  }
  else {
    return false;
  }
}
function FormatPhoneNumber(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}
function BindKeyToElement(key, element, function_to_call) {
  console.info('Binding key '+key+' to element '+element);
  element.keyup(function(e) {
    e.preventDefault();
    if(e.keyCode == key) {
      function_to_call();
    }
  });
}
function DoAllStuff() {
  $('input, textarea').each(function() {
    $(this).change(function() {
      $(this).removeAttr('status');
    });
  });

  $('select').niceSelect();

  $('svg-icon').each(function() {
    var element = $(this)[0];
    var source = $(this).attr('src');
    var elementclass = $(this).attr('class');
    $.get(source, function(content) {
      var svgicon = $(content);
      svgicon.attr('class', elementclass);
      element.outerHTML = svgicon[0].outerHTML;
    }, "text");
    // $.get($(this).attr('src'), function(content) {
    //   var htmlstring = $(content).find('path').attr('fill', $(this).attr('color'));
    //   console.log(htmlstring);
    //   // content.replace('{color}', $(this).attr('color'));
    //   // element.outerHTML = $(content).html();
    // });
    
  });
}
function RemoveFromArray(array, removeitem) {
  array = jQuery.grep(array, function(value) {
    return value != removeitem;
  });
  return array;
}
function LogOut() {
  OpenGenericDialog(
    "Wyloguj się",
    "Czy na pewno chcesz się wylogować?",
    [
      {
        "text": "Anuluj",
        "callback": function(did) {
          CloseDialog(did);
        }
      },
      {
        "text": "Wyloguj",
        "primary": true,
        "callback": function(did) {
          location.assign('/panel/logout');
        }
      }
    ]
  )
}
let previousid=0;
function prevtooltip(id) {
  var tooltip = document.getElementById("Tooltip"+id);
  tooltip.innerHTML = "Kliknij aby skopiować";
}
function CopyToClipboard(id) {
  if(previousid!=0){
    prevtooltip(previousid);
  }
  var el = document.getElementById('copy'+id);
  var range = document.createRange();
  range.selectNodeContents(el);
  var sel = window.getSelection();
  sel.removeAllRanges();
  sel.addRange(range);
  document.execCommand('copy');
  sel=getSelection()
  sel.removeAllRanges()
  var tooltip = document.getElementById("Tooltip"+id);
  tooltip.innerHTML = "Skopiowano";
  previousid=id;
}
function ShowSystemAuthors() {
  OpenGenericDialog(
    "Twórcy systemu",
    'Tomasz Osesek <a href="https://github.com/Tomsonikus" target="_blank">@GitHub</a><br>'+
    'Paweł Depta <a href="https://github.com/paweldepta" target="_blank">@GitHub</a><br>'+
    'Paweł Króliczak <a href="https://github.com/czarny1259" target="_blank">@GitHub</a><br>'+
    'Kamil Rogalski <a href="https://github.com/RogalXRogal" target="_blank">@GitHub</a>'    
  )    
}
function ShowMainDirectoryOfUser(username) {
  $.getJSON('/panel/modules/requests.php', {
    'action': 'GetUserMainDirectory',
    'username': username
  }, function(data) {

    var files_list = $('<div class="admin-tool-dialog-files-list"></div>');

    $.each(data['files'], function(k, v) {
      var file_item = $('<div class="admin-tool-dialog-file-item" '+v['type']+' onclick="window.open(\'/'+username+'/'+v['name']+'\')"></div>');
      file_item.html(
        "<span class='name'>"+v['name']+"</span>"+
        "<span class='bytes'>"+(v['type']=='directory' ? '' : v['bytes'])+"</span>"
      );
      files_list.append(file_item);
    });

    OpenGenericDialog(
      "Podgląd katalogu",
      "Oto podgląd katalogu użytkownika <b>"+username+"</b><br><br>"+
      files_list[0].outerHTML
    );
  });
}
function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/panel";
}
function DisableCookies() {
  OpenGenericDialog(
    'Wyłączanie plików cookies',
    'Wybierz przeglądarkę, dla której chcesz wyłączyć pliki cookie:<br><br>'+
    '<a target="_blank" href="https://support.google.com/accounts/answer/61416?co=GENIE.Platform%3DDesktop&hl=pl">Google Chrome</a><br>'+
    '<a target="_blank" href="https://support.mozilla.org/pl/kb/wlaczanie-i-wylaczanie-ciasteczek-sledzacych">Mozilla Firefox</a><br>'+
    '<a target="_blank" href="https://support.microsoft.com/pl-pl/help/17442/windows-internet-explorer-delete-manage-cookies">Internet Explorer</a><br>'+
    '<a target="_blank" href="https://support.microsoft.com/pl-pl/help/4468242/microsoft-edge-browsing-data-and-privacy-microsoft-privacy">Microsoft Edge</a><br>'+
    '<a target="_blank" href="https://support.apple.com/pl-pl/guide/safari/sfri11471/mac">Safari</a><br>'+
    '<a target="_blank" href="https://help.opera.com/pl/latest/security-and-privacy/#clearPrivateData">Opera</a>',
    -1
  );
}
function AcceptCookies() {
  setCookie("cookiesinfo_disabled", "true", 30);
  $('.cookiesinfo_container').slideUp();
}
var accountpanelvisible = false;
function ToggleAccountPanel(visible='auto') {
  var accountpanel = $('.account_panel_container .container');
  accountpanel.stop();
  if((accountpanelvisible && visible==='auto') || visible===false) {
    accountpanel.animate({'opacity': 0}, 150, function() {
      accountpanel.css('display', 'none');
      accountpanelvisible = false;
    });
  }
  if((!accountpanelvisible && visible==='auto') || visible===true) {
    accountpanel.css('display', 'block');
    accountpanel.animate({'opacity': 1}, 300);
    accountpanelvisible = true;
  }
}