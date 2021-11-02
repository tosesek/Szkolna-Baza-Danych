var visible_dialogs = [];
var global_dialogid = 0;

function OpenGenericDialog(title, content, buttons=-1, callback=function(){}) {
  var iPotentialNewDialogId = global_dialogid+1;
  $.get('/panel/modules/templates/generic_dialog.html', function(data) {

    var newelement = $(data);
    newelement.find('.title').html(title);
    newelement.find('.content').html(content);

    if(buttons==-1) {
      buttons = [
        {
          "text": "Zamknij",
          "callback": function() {
            CloseDialog(iPotentialNewDialogId);
          },
          "primary": true
        }
      ];
    }

    $.each(buttons, function(k, v) {
      var newbutton = $('<button></button>');
      newbutton.html(v['text']);
      newbutton.attr('dialog-button', '');
      newbutton.click(function() { v['callback'](iPotentialNewDialogId); });

      if(v['primary']) {
        newbutton.attr('primary', '');
      }

      newelement.find('.buttons').append(newbutton);
    });


    var newdialogelement = $('<div></div>');
    newdialogelement.attr('class', 'dialog');
    newdialogelement.attr('dialogid', iPotentialNewDialogId);
    newdialogelement.append(newelement);
    newdialogelement.css('z-index', iPotentialNewDialogId+2);
    newdialogelement.css('opacity', '0');

    $('.global_dialogs_holder').append(newdialogelement);

    visible_dialogs.push(iPotentialNewDialogId);

    if(visible_dialogs.length==1) {
      $('.global_dialogs_holder').css('display', 'block');
      $('.global_dialogs_holder').animate({'opacity': '1'}, 300);
      $('.global_dialogs_holder [dialogid='+iPotentialNewDialogId+']').animate({'opacity': '1'}, 300);
    }
    else {
      $('.global_dialogs_holder [dialogid='+iPotentialNewDialogId+']').animate({'opacity': '1'}, 300);
    }

    $('.global_dialogs_holder .dialog').each(function() {
      var zindex = parseInt($(this).attr('dialogid'))+1;
      if(zindex-1!=iPotentialNewDialogId) {
        $(this).css('z-index', zindex);
      }
    });

    $('.global_dialogs_holder .background').css('z-index', iPotentialNewDialogId+1);

    global_dialogid++;
    callback(iPotentialNewDialogId);
    
    // Szukanie inputów
    if($('.global_dialogs_holder [dialogid='+iPotentialNewDialogId+'] [autofocus]').length) {
      $('.global_dialogs_holder [dialogid='+iPotentialNewDialogId+'] [autofocus]').select();
      $('.global_dialogs_holder [dialogid='+iPotentialNewDialogId+'] [autofocus]').focus();
    }
    else {
      $('.global_dialogs_holder [dialogid='+iPotentialNewDialogId+']').find('[primary]').select();
      $('.global_dialogs_holder [dialogid='+iPotentialNewDialogId+']').find('[primary]').focus();
    }

    DoAllStuff();
  });
  return iPotentialNewDialogId;
}
function OpenCustomDialog(file, customdata=-1, callback=function(){}) {
  var iPotentialNewDialogId = global_dialogid+1;

  if(customdata==-1) {
    customdata = {
      'dialogid': iPotentialNewDialogId
    };
  }
  else {
    customdata['dialogid'] = iPotentialNewDialogId;
  }

  $.get(file, customdata, function(data) {

    var newelement = $(data);

    var newdialogelement = $('<div></div>');
    newdialogelement.attr('class', 'dialog');
    newdialogelement.attr('dialogid', iPotentialNewDialogId);
    newdialogelement.append(newelement);
    newdialogelement.css('z-index', iPotentialNewDialogId+2);

    $('.global_dialogs_holder').append(newdialogelement);

    visible_dialogs.push(iPotentialNewDialogId);

    if(visible_dialogs.length==1) {
      $('.global_dialogs_holder').css('display', 'block');
      $('.global_dialogs_holder').animate({'opacity': '1'}, 300);
    }

    $('.global_dialogs_holder .dialog').each(function() {
      var zindex = parseInt($(this).attr('dialogid'))+1;
      if(zindex-1!=iPotentialNewDialogId) {
        $(this).css('z-index', zindex);
      }
    });

    $('.global_dialogs_holder .background').css('z-index', iPotentialNewDialogId+1);

    global_dialogid++;
    callback(iPotentialNewDialogId);
    DoAllStuff();
  });
  return iPotentialNewDialogId;
}
function OpenBlockingDialog(title) {
  return OpenGenericDialog(title, '<img src="//static.wteam.pl/public/images/loading.svg" height="40px" draggable="false">', []);
}
function CloseDialog(dialogid) {
  var highest_found_dialog_id = 0;

  $('.global_dialogs_holder [dialogid='+dialogid+']').animate({'opacity': '0'}, 300, function() {
    $(this).remove();

    $('.global_dialogs_holder .dialog').each(function() {
      var zindex = parseInt($(this).attr('dialogid'));
      if(zindex > highest_found_dialog_id) highest_found_dialog_id = zindex;
    });

    $('.global_dialogs_holder [dialogid='+highest_found_dialog_id+']').css('z-index', highest_found_dialog_id+2)

    console.log(highest_found_dialog_id);

    $('.global_dialogs_holder .background').css('z-index', (highest_found_dialog_id-1 <3 ? 3 : highest_found_dialog_id-1));

    visible_dialogs = jQuery.grep(visible_dialogs, function(value) {
      return value != dialogid;
    });

    if(visible_dialogs.length==0 && highest_found_dialog_id==0) {
      $('.global_dialogs_holder').animate({'opacity': '0'}, 300, function() {
        $(this).css('display', 'none');
      });
      $('[autofocus]').focus();
    }
  });
}

function CloseAllDialogs() {
  $('.global_dialogs_holder .background').css('z-index', '3');

  visible_dialogs = [];
  $('.global_dialogs_holder').animate({'opacity': '0'}, 300, function() {
    $(this).css('display', 'none');
    $('.global_dialogs_holder [dialogid]').each(function() {
      $(this).remove();
    })
  });
}













//
