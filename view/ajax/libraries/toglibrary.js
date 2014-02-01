var tog = {
	addElement: function (type, cssclass, contents, parent) {
		var newDiv = document.createElement(type);
		newDiv.className = cssclass;
		var newDivContents = document.createTextNode(contents);
		newDiv.appendChild(newDivContents);
		parent.appendChild(newDiv);
		
		// Fix for Safari2/Opera9 repaint issue
		document.documentElement.style.position = "relative";
	},
	
	showMessages: function (response) {
		var message = response.getElementsByTagName('message')[0];
		var mainDiv = document.getElementById('main');
		if (message) {
			tog.addElement('div','message',message.firstChild.wholeText,mainDiv);
		}
		var dberror = response.getElementsByTagName('db-error')[0];
		if (dberror) {
			tog.addElement('div','error',"Error on query:\n"+dberror.children[0].textContent+'<br/>Error was:'+dberror.children[1].textContent,mainDiv ); 
		}
		var formerror = response.getElementsByTagName('form-error')[0];
		
		if (formerror) {
			tog.addElement('div','error',"Form error:\n"+formerror.children[0].textContent,mainDiv );
		}
		var unexpected = response.getElementsByTagName('b')[0];
		if (unexpected) {
			tog.addElement('div','message',"Unexpected response: \n"+response.responseText,mainDiv);
		}
	}
};