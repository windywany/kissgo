var edButtons = new Array();
var edLinks = new Array();
var edOpenTags = new Array();

function edButton(id, display, tagStart, tagEnd, access, open) {
	this.id = id; // used to name the toolbar button
	this.display = display; // label on button
	this.tagStart = tagStart; // open tag
	this.tagEnd = tagEnd; // close tag
	this.access = access; // set to -1 if tag does not need to be
	// closed
	this.open = open; // set to -1 if tag does not need to be closed
}

edButtons.push(new edButton('ed_bold', 'b', '<strong>', '</strong>', 'b'));

edButtons.push(new edButton('ed_italic', 'i', '<em>', '</em>', 'i'));

edButtons.push(new edButton('ed_link', 'link', '', '</a>', 'a')); // special
// case
edButtons.push(new edButton('ed_ins', 'ins', '<ins>', '</ins>'));

edButtons.push(new edButton('ed_del', 'del', '<del>', '</del>'));
edButtons.push(new edButton('ed_img', 'img', '', '', 'm', -1)); // special case

edButtons.push(new edButton('ed_ul', 'ul', '<ul>\n', '</ul>\n\n', 'u'));

edButtons.push(new edButton('ed_ol', 'ol', '<ol>\n', '</ol>\n\n', 'o'));

edButtons.push(new edButton('ed_li', 'li', '\t<li>', '</li>\n', 'l'));

edButtons.push(new edButton('ed_block', 'b-quote', '<blockquote>', '</blockquote>', 'q'));
edButtons.push(new edButton('ed_h1', 'h1', '<h1>', '</h1>\n\n', '1'));

edButtons.push(new edButton('ed_h2', 'h2', '<h2>', '</h2>\n\n', '2'));

edButtons.push(new edButton('ed_code', 'code', '<code>', '</code>', 'c'));

edButtons.push(new edButton('ed_pre', 'pre', '<pre>', '</pre>'));
var extendedStart = edButtons.length;

function edLink(display, URL, newWin) {
	this.display = display;
	this.URL = URL;
	if (!newWin) {
		newWin = 0;
	}
	this.newWin = newWin;
}

edLinks[edLinks.length] = new edLink('alexking.org', 'http://www.alexking.org/');

function edShowButton(which, button, i, toolbar) {
	var accesskey, tpl;
	if (button.access) {
		accesskey = ' accesskey = "' + button.access + '"';
	} else {
		accesskey = '';
	}
	switch (button.id) {
	case 'ed_img':
		tpl = '<input type="button" id="' + button.id + '_' + which + '" ' + accesskey + ' class="ed_button" onclick="edInsertImage(\'' + which + '\');" value="' + button.display
				+ '" />';
		break;
	case 'ed_link':
		tpl = '<input type="button" id="' + button.id + '_' + which + '" ' + accesskey + ' class="ed_button" onclick="edInsertLink(\'' + which + '\', ' + i + ');" value="'
				+ button.display + '" />';
		break;
	case 'ed_ext_link':
		tpl = '<input type="button" id="' + button.id + '_' + which + '" ' + accesskey + ' class="ed_button" onclick="edInsertExtLink(\'' + which + '\', ' + i + ');" value="'
				+ button.display + '" />';
		break;
	case 'ed_footnote':
		tpl = '<input type="button" id="' + button.id + '_' + which + '" ' + accesskey + ' class="ed_button" onclick="edInsertFootnote(\'' + which + '\');" value="'
				+ button.display + '" />';
		break;
	case 'ed_via':
		tpl = '<input type="button" id="' + button.id + '_' + which + '" ' + accesskey + ' class="ed_button" onclick="edInsertVia(\'' + which + '\');" value="' + button.display
				+ '" />';
		break;
	default:
		tpl = '<input type="button" id="' + button.id + '_' + which + '" ' + accesskey + ' class="ed_button" onclick="edInsertTag(\'' + which + '\', ' + i + ');" value="'
				+ button.display + '"  />';
		break;
	}
	if (toolbar) {
		toolbar.prepend($(tpl));
	} else {
		document.write(tpl);
	}
}

function edShowLinks() {
	var tempStr = '<select onchange="edQuickLink(this.options[this.selectedIndex].value, this);"><option value="-1" selected>(Quick Links)</option>';
	for ( var i = 0; i < edLinks.length; i++) {
		tempStr += '<option value="' + i + '">' + edLinks[i].display + '</option>';
	}
	tempStr += '</select>';
	document.write(tempStr);
}

function edAddTag(which, button) {
	if (edButtons[button].tagEnd != '') {
		edOpenTags[which][edOpenTags[which].length] = button;
		document.getElementById(edButtons[button].id + '_' + which).value = '/' + document.getElementById(edButtons[button].id + '_' + which).value;
	}
}

function edRemoveTag(which, button) {
	for ( var i = 0; i < edOpenTags[which].length; i++) {
		if (edOpenTags[which][i] == button) {
			edOpenTags[which].splice(i, 1);
			document.getElementById(edButtons[button].id + '_' + which).value = document.getElementById(edButtons[button].id + '_' + which).value.replace('/', '');
		}
	}
}

function edCheckOpenTags(which, button) {
	var tag = 0, i;
	for (i = 0; i < edOpenTags[which].length; i++) {
		if (edOpenTags[which][i] == button) {
			tag++;
		}
	}
	if (tag > 0) {
		return true; // tag found
	} else {
		return false; // tag not found
	}
}

function edCloseAllTags(which) {
	var count = edOpenTags[which].length;
	for ( var o = 0; o < count; o++) {
		edInsertTag(which, edOpenTags[which][edOpenTags[which].length - 1]);
	}
}

function edQuickLink(i, thisSelect) {
	if (i > -1) {
		var newWin = '';
		if (edLinks[i].newWin == 1) {
			newWin = ' target="_blank"';
		}
		var tempStr = '<a href="' + edLinks[i].URL + '"' + newWin + '>' + edLinks[i].display + '</a>';
		thisSelect.selectedIndex = 0;
		edInsertContent(edCanvas, tempStr);
	} else {
		thisSelect.selectedIndex = 0;
	}
}

function edToolbar(which, wrapper) {
	if (wrapper) {
		var toolbar = '<div id="ed_toolbar_' + which + '" class="quicktags-toolbar"><span>' + '<input type="button" id="ed_close_' + which
				+ '" class="ed_button" onclick="edCloseAllTags(\'' + which + '\');" value="关闭标签" />' + '</span></div>';
		toolbar = $(toolbar);
		wrapper.prepend(toolbar);
		for ( var i = 0; i < extendedStart; i++) {
			edShowButton(which, edButtons[i], i, toolbar);
		}
	} else {
		document.write('<div id="ed_toolbar_' + which + '" class="quicktags-toolbar"><span>');
		for ( var i = 0; i < extendedStart; i++) {
			edShowButton(which, edButtons[i], i);
		}
		document.write('<input type="button" id="ed_close_' + which + '" class="ed_button" onclick="edCloseAllTags(\'' + which + '\');" value="关闭标签" />');

		document.write('</span>');
		// edShowLinks();
		document.write('</div>');
	}

	edOpenTags[which] = new Array();
}

// insertion code

function edInsertTag(which, i) {
	myField = document.getElementById(which);
	// IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		if (sel.text.length > 0) {
			sel.text = edButtons[i].tagStart + sel.text + edButtons[i].tagEnd;
		} else {
			if (!edCheckOpenTags(which, i) || edButtons[i].tagEnd == '') {
				sel.text = edButtons[i].tagStart;
				edAddTag(which, i);
			} else {
				sel.text = edButtons[i].tagEnd;
				edRemoveTag(which, i);
			}
		}
		myField.focus();
	}
	// MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var cursorPos = endPos;
		var scrollTop = myField.scrollTop;
		if (startPos != endPos) {
			myField.value = myField.value.substring(0, startPos) + edButtons[i].tagStart + myField.value.substring(startPos, endPos) + edButtons[i].tagEnd
					+ myField.value.substring(endPos, myField.value.length);
			cursorPos += edButtons[i].tagStart.length + edButtons[i].tagEnd.length;
		} else {
			if (!edCheckOpenTags(which, i) || edButtons[i].tagEnd == '') {
				myField.value = myField.value.substring(0, startPos) + edButtons[i].tagStart + myField.value.substring(endPos, myField.value.length);
				edAddTag(which, i);
				cursorPos = startPos + edButtons[i].tagStart.length;
			} else {
				myField.value = myField.value.substring(0, startPos) + edButtons[i].tagEnd + myField.value.substring(endPos, myField.value.length);
				edRemoveTag(which, i);
				cursorPos = startPos + edButtons[i].tagEnd.length;
			}
		}
		myField.focus();
		myField.selectionStart = cursorPos;
		myField.selectionEnd = cursorPos;
		myField.scrollTop = scrollTop;
	} else {
		if (!edCheckOpenTags(which, i) || edButtons[i].tagEnd == '') {
			myField.value += edButtons[i].tagStart;
			edAddTag(which, i);
		} else {
			myField.value += edButtons[i].tagEnd;
			edRemoveTag(which, i);
		}
		myField.focus();
	}
}

function edInsertContent(which, myValue) {
	myField = document.getElementById(which);
	// IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		myField.focus();
	}
	// MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var scrollTop = myField.scrollTop;
		myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
		myField.scrollTop = scrollTop;
	} else {
		myField.value += myValue;
		myField.focus();
	}
}

function edInsertLink(which, i, defaultValue) {
	myField = document.getElementById(which);
	if (!defaultValue) {
		defaultValue = 'http://';
	}
	if (!edCheckOpenTags(which, i)) {
		var URL = prompt('Enter the URL', defaultValue);
		if (URL) {
			edButtons[i].tagStart = '<a href="' + URL + '">';
			edInsertTag(which, i);
		}
	} else {
		edInsertTag(which, i);
	}
}

function edInsertExtLink(which, i, defaultValue) {
	myField = document.getElementById(which);
	if (!defaultValue) {
		defaultValue = 'http://';
	}
	if (!edCheckOpenTags(which, i)) {
		var URL = prompt('Enter the URL', defaultValue);
		if (URL) {
			edButtons[i].tagStart = '<a href="' + URL + '" rel="external">';
			edInsertTag(which, i);
		}
	} else {
		edInsertTag(which, i);
	}
}

function edInsertImage(which) {
	myField = document.getElementById(which);
	var myValue = prompt('Enter the URL of the image', 'http://');
	if (myValue) {
		myValue = '<img src="' + myValue + '" alt="' + prompt('Enter a description of the image', '') + '" />';
		edInsertContent(which, myValue);
	}
}

function edInsertFootnote(which) {
	myField = document.getElementById(which);
	var note = prompt('Enter the footnote:', '');
	if (!note || note == '') {
		return false;
	}
	var now = new Date;
	var fnId = 'fn' + now.getTime(), fnStr1, fnStr2, countx;
	var fnStart = myField.value.indexOf('<ol class="footnotes">');
	if (fnStart != -1) {
		fnStr1 = myField.value.substring(0, fnStart);
		fnStr2 = myField.value.substring(fnStart, myField.value.length);
		countx = countInstances(fnStr2, '<li id="') + 1;
	} else {
		countx = 1;
	}
	var count = '<sup><a href="#' + fnId + 'n" id="' + fnId + '" class="footnote">' + countx + '</a></sup>';
	edInsertContent(which, count);
	if (fnStart != -1) {
		fnStr1 = myField.value.substring(0, fnStart + count.length);
		fnStr2 = myField.value.substring(fnStart + count.length, myField.value.length);
	} else {
		fnStr1 = myField.value;
		fnStr2 = "\n\n" + '<ol class="footnotes">' + "\n" + '</ol>' + "\n";
	}
	var footnote = '	<li id="' + fnId + 'n">' + note + ' [<a href="#' + fnId + '">back</a>]</li>' + "\n" + '</ol>';
	myField.value = fnStr1 + fnStr2.replace('</ol>', footnote);
}

function countInstances(string, substr) {
	var count = string.split(substr);
	return count.length - 1;
}

function edInsertVia(which) {
	myField = document.getElementById(which);
	var myValue = prompt('Enter the URL of the source link', 'http://');
	if (myValue) {
		myValue = '(Thanks <a href="' + myValue + '" rel="external">' + prompt('Enter the name of the source', '') + '</a>)';
		edInsertContent(which, myValue);
	}
}

function edSetCookie(name, value, expires, path, domain) {
	document.cookie = name + "=" + escape(value) + ((expires) ? "; expires=" + expires.toGMTString() : "") + ((path) ? "; path=" + path : "")
			+ ((domain) ? "; domain=" + domain : "");
};

function edShowExtraCookie() {
	var cookies = document.cookie.split(';');
	for ( var i = 0; i < cookies.length; i++) {
		var cookieData = cookies[i];
		while (cookieData.charAt(0) == ' ') {
			cookieData = cookieData.substring(1, cookieData.length);
		}
		if (cookieData.indexOf('js_quicktags_extra') == 0) {
			if (cookieData.substring(19, cookieData.length) == 'show') {
				return true;
			} else {
				return false;
			}
		}
	}
	return false;
};
(function($) {
	$.fn.quicktags = function(editor) {
		edToolbar(editor, $(this));
	};
})(jQuery);