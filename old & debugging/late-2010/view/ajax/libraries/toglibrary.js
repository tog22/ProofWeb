var tog = {
	addElement: function(type,class,contents,parent) {
		var newDiv = document.createElement(type);
		newDiv.className = class;
		var newDivContents = document.createTextNode(contents);
		newDiv.appendChild(newDivContents);
		parent.appendChild(newDiv);
		
		// Fix for Safari2/Opera9 repaint issue
		document.documentElement.style.position = "relative";
	},
	
	showMessages: function(response) {
		var message = response.getElementsByTagName('message')[0];
		var mainDiv = document.getElementById('main');
		if (message) {
			tog.addElement('div','message',message.firstChild.wholeText,mainDiv);
		}
		var error = response.getElementsByTagName('db-error')[0];
		if (error) {
			tog.addElement('div','error',"Error on query:\n"+error.children[0].textContent+'<br/>Error was:'+error.children[1].textContent,mainDiv); // Firebug debug content suggests I can use 'query' rather than 0 but this doesn't work - ask Charlie for a JS tutorial on this?
		}
		var unexpected = response.getElementsByTagName('b')[0];
		if (unexpected) {
			alert("Unexpected response: \n"+response.responseText);
		}
	}
}