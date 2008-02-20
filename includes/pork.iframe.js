/*
Pork.Iframe 1.1

This submits an iframe invisibly to the server, and expects a JSON object in return
handy, so that you do not have to care about posting forms, urlencoding, file uploads etc.
usage: <form method='post' onsubmit='return new iframe(this).request();'>

changelog:
02-04-07: mootoolsified :Y)
17-01-06: initial release
18-01-06: added options initiator
          added IE5 support
		  added Opera support
01-03-06: added WORKING safari support! Major thanks to Charles Hinshaw and Phil Barrett!

*/
document.iframeLoaders = {};

iframe = new Class({
	
	initialize: function(form, options){
		this.form = $(form);
		this.setOptions(this.defaultOptions,options);
		this.options.uniqueId = new Date().getTime();
		document.iframeLoaders[this.options.uniqueId] = this;
		this.transport = this.getTransport();
		this.request();
	},
	defaultOptions:
	{
		onComplete: null,
		update: null,
		updateMultiple: null,
		uniqueId : null
	},

	request: function() {
		this.form.target= 'frame_'+this.options.uniqueId;
		this.form.setAttribute("target", 'frame_'+this.options.uniqueId); // in case the other one fails.
		this.form.submit();
		return false;
	},
	
	getResponse: function()
	{
		var response = 'Failed to get response document';
		try { var response = this.transport.contentDocument.document.body.innerHTML; this.transport.contentDocument.document.close(); }	// For NS6
		catch (e){ 
			try{ var response = this.transport.contentWindow.document.body.innerHTML; this.transport.contentWindow.document.close(); } // For IE5.5 and IE6
			 catch (e){
				 try { var response = this.transport.document.body.innerHTML; this.transport.document.body.close(); } // for IE5
					catch (e) {
						try	{ var response = window.frames['frame_'+this.options.uniqueId].document.body.innerText; } // for really nasty browsers
						catch (e) { } // forget it.
				 }
			}
		}
		return response;
	},

	onStateChange: function(){
		
		this.transport.responseText = this.getResponse();
		if (this.options.onComplete) setTimeout( function(){this.options.onComplete(this)}.bind(this), 10);
		if (this.options.update)  setTimeout(function(){ $(this.options.update).innerHTML = this.transport.responseText;}.bind(this), 10);
		if (this.options.updateMultiple){ 
			setTimeout(function(){ // JSON support!
				try	{ var hasscript = false; eval("var inputObject = "+this.transport.responseText);	// we're expecting a JSON object, eval it to inputObject
					for (var i in inputObject) { if (i == 'script') { hasscript = true; } // check if we passed some javascript along too
						else {if ( elm = $(i)) { elm.innerHTML = inputObject[i]; } else { alert("element "+i+" not found!"); } } // if it's not script, update the corresponding div
					} if (hasscript) eval(inputObject['script']); // some on-the-fly-javascript exchanging support too
				} catch (e) { } // in case of an error					
			}.bind(this), 10);
		}	
	},

	getTransport: function() 
	{
		var divElm = new Element('DIV', {'styles':	{ "position" : "absolute",	"top":  0,	"marginLeft" : -10000	}});
		FrameEl = new Element("iframe", {"name": "frame_"+this.options.uniqueId, "id": "frame_"+this.options.uniqueId, "events": { "load" : function(){ this.onStateChange(); }.bind(this) }}).injectInside(divElm);
		divElm.injectInside(document.body);
		return $('frame_'+this.options.uniqueId);
	}

});

iframe.implement(new Options);