// bbCode control by
// subBlue design
// www.subBlue.com
// adapted for Simpleboard by the Two Shoes Module Factory (www.tsmf.jigsnet.com)
// adapted for ProfileBook by Beat of www.joomlapolis.com

// Startup variables
var imageTag = false;
var theSelection = false;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));

var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

// Define the bbCode tags
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[ul]','[/ul]','[ol]','[/ol]','[img]','[/img]','[url=http://www.example.com]','[/url]','[li]','[/li]');


// Replacement for arrayname.length property
function getarraysize(thearray) {
   for (i = 0; i < thearray.length; i++) {
      if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
         return i;
      }
   return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
   thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
   thearraysize = getarraysize(thearray);
   retval = thearray[thearraysize - 1];
   delete thearray[thearraysize - 1];
   return retval;
}


function bbstyle(myform, bbnumber) {
   var txtarea = myform.profilebookpostercomments;

   id = myform.getAttribute('id');
   var bbcode = eval("_"+id+"_bbcodestack");

   txtarea.focus();
   donotinsert = false;
   theSelection = false;
   bblast = 0;

   if (bbnumber == -1) { // Close all open tags & default button names
      while (bbcode[0]) {
         butnumber = arraypop(bbcode) - 1;
         txtarea.value += bbtags[butnumber + 1];
         buttn = eval('myform.addbbcode' + butnumber);
         buttn.value = buttn.value.substr(1,(buttn.value.length - 1));
      }
      imageTag = false; // All tags are closed including image tags :D
      txtarea.focus();
      return;
   }

   if ((clientVer >= 4) && is_ie && is_win)
   {
      theSelection = document.selection.createRange().text; // Get text selection
      if (theSelection) {
         // Add tags around selection
         document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
         txtarea.focus();
         theSelection = '';
         return;
      }
   }
   else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
   {
      mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
      return;
   }

   // Find last occurance of an open tag the same as the one just clicked
   for (i = 0; i < bbcode.length; i++) {
      if (bbcode[i] == bbnumber+1) {
         bblast = i;
         donotinsert = true;
      }
   }

   if (donotinsert) {      // Close all open tags up to the one just clicked & default button names
      while (bbcode[bblast]) {
            butnumber = arraypop(bbcode) - 1;
            insertAtCaret(txtarea, bbtags[butnumber + 1]);
	        buttn = eval('myform.addbbcode' + butnumber);
    	    buttn.value = buttn.value.substr(1,(buttn.value.length - 1));
            imageTag = false;
      }
      txtarea.focus();
      return;
   } else { // Open tags

      if (imageTag && (bbnumber != 14)) {    // Close image tag before adding another
         insertAtCaret(txtarea, bbtags[15]);
         lastValue = arraypop(bbcode) - 1;   // Remove the close image tag from the list
         myform.addbbcode14.value = "Img";  // Return button back to normal state
         imageTag = false;
      }

      // Open tag
      insertAtCaret(txtarea, bbtags[bbnumber]);
      if ((bbnumber == 14) && (imageTag == false)) imageTag = true; // Check to stop additional tags after an unclosed image tag
      arraypush(bbcode,bbnumber+1);
      buttn = eval('myform.addbbcode' + bbnumber);
      buttn.value = '/' + buttn.value;
      txtarea.focus();
      return;
   }
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
   var selLength = txtarea.textLength;
   var selStart = txtarea.selectionStart;
   var selEnd = txtarea.selectionEnd;
   if (selEnd == 1 || selEnd == 2) {
      selEnd = selLength;
   }
   var scrollPos = txtarea.scrollTop;
   
   var s1 = (txtarea.value).substring(0,selStart);
   var s2 = (txtarea.value).substring(selStart, selEnd);
   var s3 = (txtarea.value).substring(selEnd, selLength);
   txtarea.value = s1 + open + s2 + close + s3;

   if (txtarea.setSelectionRange) {
      if (s2.length == 0) {
   	     txtarea.setSelectionRange(selStart + open.length, selStart + open.length);
      } else {
   	     txtarea.setSelectionRange(selStart, selStart + open.length + s2.length + close.length);
      }
   	  txtarea.focus();
   }
   txtarea.scrollTop = scrollPos;

   return;
}

// Inserts at caret position, replacing the currently selected text with the passed text.
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function insertAtCaret (txtarea, text) {
	if (typeof(txtarea.caretPos) != "undefined" && txtarea.createTextRange) {
		// IE:
		var caretPos = txtarea.caretPos;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		caretPos.select();
	} else if (typeof(txtarea.selectionStart) != "undefined") {
		// Mozilla+Firefox:
		var begin = txtarea.value.substr(0, txtarea.selectionStart);
		var end = txtarea.value.substr(txtarea.selectionEnd);
		var scrollPos = txtarea.scrollTop;

		txtarea.value = begin + text + end;

		if (txtarea.setSelectionRange) {
			txtarea.focus();
			txtarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
		}
		txtarea.scrollTop = scrollPos;
	} else {
		txtarea.value += text;
		txtarea.focus(txtarea.value.length - 1);
	}
}

// set the caret to the end of a text field/text area
// http://www.faqts.com/knowledge_base/view.phtml/aid/1159/fid/130
function setCaretToEnd (control) {
  if (control.createTextRange) {
    var range = control.createTextRange();
    range.collapse(false);
    range.select();
  }
  else if (control.setSelectionRange) {
    control.focus();
    var length = control.value.length;
    control.setSelectionRange(length, length);
  }
}

// http://www.faqts.com/knowledge_base/view.phtml/aid/36476/fid/130
function setCursorPosition(oInput,oStart,oEnd) {
	if ( oInput.setSelectionRange ) {
		oInput.setSelectionRange(oStart,oEnd);
	}
	else if ( oInput.createTextRange ) {
		var range = oInput.createTextRange();
		range.collapse(true);
		range.moveEnd('character',oEnd);
		range.moveStart('character',oStart);
		range.select();
	}
}
 
// http://www.faqts.com/knowledge_base/view.phtml/aid/5140/fid/130
function bbfontstyle(txtarea, bbopen, bbclose) {

	if (typeof(txtarea.caretPos) != "undefined" && txtarea.createTextRange) {
		// this code is for an older browser...
		//alert("1");
		var caretPos = txtarea.caretPos;
		var temp_length = caretPos.text.length;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ?
							bbopen + caretPos.text + bbclose + ' ' : bbopen + caretPos.text + bbclose;

		if (temp_length == 0) {
			caretPos.moveStart("character", -bbclose.length);
			caretPos.moveEnd("character", -bbclose.length);
			caretPos.select();
		} else {
			txtarea.focus(caretPos);
		}
	} else if ((clientVer >= 4) && is_ie && is_win) {
	  // IE6:
      theSelection = document.selection.createRange().text;
      if (!theSelection) {
         txtarea.value += bbopen + bbclose;
         txtarea.focus();
         return;
      }
      document.selection.createRange().text = bbopen + theSelection + bbclose;
      txtarea.focus();
      return;
   } else if (typeof(txtarea.selectionStart) != "undefined") {
   	  // FF 1.5 + safari 2.0 + Opera 8.5 ++...:
   	  //alert("2");
      mozWrap(txtarea, bbopen, bbclose);
   } else {
   	  //alert("3");
      txtarea.focus();
      insertAtCaret(txtarea, bbopen + bbclose);
   }
}

//code not yet used in My Profile (userprofile.php)
function textCounter(field, countfield, maxlimit) {
   if(field.value.length > maxlimit){
      field.value = field.value.substring(0, maxlimit);
   }
   else{
      countfield.value = maxlimit - field.value.length;
   }
}

// Insert emoticons
function pb_emo(txtarea, e)
{
	if (typeof(txtarea.caretPos) != "undefined" && txtarea.createTextRange) {
		// IE:
		e = ' '+e+' ';
	} else if (typeof(txtarea.selectionStart) != "undefined") {
		// Mozilla+Firefox:
		if (txtarea.selectionStart > 0) {
			charbefore = txtarea.value.substr(txtarea.selectionStart - 1, 1);
			if (charbefore != ' ' && charbefore != "\n") {
				e = ' '+e;
			}
		}
		if (txtarea.selectionEnd < txtarea.value.length) {
			charafter = txtarea.value.substr(txtarea.selectionEnd, 1);
			if (charafter != ' ' && charafter != "\n") {
				e += ' ';
			}
		}
	} else {
		e = ' '+e+' ';
	}
	txtarea.focus();
	insertAtCaret(txtarea, e);
}

// From validation at submit:
function pb_submitForm(mfrm) {
	var me = mfrm.elements;
	var rChecked;

	var submitme=1;
	me['submitentry'].disabled=true;
	me['profilebookpostercomments'].readOnly = true;

	var errorMSG = '';
	var confirmMSGs = Array();

	var id = mfrm.getAttribute('id');
	validationArray = eval("_"+id+"_validations");
	for (var j=0; j < validationArray.length; j++) {
		var i = validationArray[j][0];
		if (typeof(me[i].type) == 'undefined') {
			searchLoop:
			for (var ii=0; ii < me.length; ii++) {
				if (i == me[ii].getAttribute('name')) {
					if (me[ii].type == 'radio' || me[ii].type == 'checkbox') {
						var rOptions = me[me[ii].getAttribute('name')];
						rChecked = 0;
						if(rOptions.length > 1) {
							for (var r=0; r < rOptions.length; r++) {
								if (rOptions[r].checked) {
									rChecked=1;
									break searchLoop;
								}
							}
							break searchLoop;
						} else {
							if (me[ii].checked) {
								rChecked=1;
								break searchLoop;
							}
						}
					}
				}
			}
		}
		if ((typeof(me[i].type) == 'undefined' && rChecked==0) || me[i].value == '') {
			if (validationArray[j][1]) {
				confirmMSGs.push(validationArray[j][1]);
			} else {
				errorMSG += validationArray[j][2]+'\n';
				// me[validationArray[i][0]].style.background = "red";
				submitme=0;
			}
		}
	}
	if (errorMSG != '') {
		alert(errorMSG);
	} else {
		for (var j=0; j < confirmMSGs.length; j++) {
			if (!confirm(confirmMSGs[j])) {
				submitme=0;
				break;
			}
		}
	}
	if (submitme>0) {
		return true;
	}else{
		me['submitentry'].disabled=false;
		me['profilebookpostercomments'].readOnly = false;
		return false;
	}

}

(function($) {
	$.fn.cbpbtoggleEditor = function(options) {
		this.each(function() {
			var editorDiv = $( '#' + $(this).attr('rel') );
			if ( $(this).hasClass('cbpbEditorShow') ) {
				$(this).addClass('cbpbEditorVisible');
				editorDiv.show().find('textarea.cbpbEditor').trigger('autosize.resize');
			} else {
				$(this).addClass('cbpbEditorHidden');
				editorDiv.hide();
			}
			$(this).click( function() {
				var resizeAutoSize = null;
				if ( $(this).hasClass('cbpbEditorHidden') ) {
					if ( $(this).attr('title') && ! confirm( $(this).attr('title') ) ) {
						return;
					}
					$(this).removeClass('cbpbEditorHidden').addClass('cbpbEditorVisible');
					resizeAutoSize = function () {
						$(this).find('textarea.cbpbEditor').trigger('autosize.resize');
					}
				} else {
					$(this).removeClass('cbpbEditorVisible').addClass('cbpbEditorHidden');
				}
				$( '#' + $(this).attr('rel') ).slideToggle('slow', resizeAutoSize);
				return false;
			});
		});

		return this;
	};

	$.fn.cbpbControlsHover = function(options) {
		this.each(function() {
			$(this).children(options).fadeTo(0, 0.001 );
			$(this).hover(
				function() {
					$(this).children(options).fadeTo(0, 1 );
				},
				function() {
					$(this).children(options).fadeTo('slow', 0.001 );
				}
			);
		});
		
		return this;
	};
})(jQuery);

jQuery(document).ready(function($){
	$('textarea.cbpbEditor').autosize();
	$('a.cbpbToggleEditor').cbpbtoggleEditor();
	$('.cbpbSnglMsgAll').cbpbControlsHover('.cbpbControlArea');
});