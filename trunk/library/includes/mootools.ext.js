Ajax.implement({
	onComplete: function(){
		if (this.options.update) $(this.options.update).setHTML(this.response.text); 
		if (this.options.multiple) this.updateMultiple();
		if (this.options.updateMultiple) this.updateMultiple();
		if (this.options.onComplete && this.response.text.indexOf('addMessage') == -1) 
		{
			this.fireEvent('onComplete', [this.response.text, this.response.xml], 20);
		}
		setTimeout(function(){
		this.evalScripts();
		}.bind(this), 500);
	
	},

	updateMultiple: function(){
		try	{ var hasscript = false; eval("var inputObject = "+ this.response.text);	// we're expecting a JSON object, eval it to inputObject
					for (var i in inputObject) { if (i == 'script') { hasscript = true; } // check if we passed some javascript along too
						else {if ( elm = $(i)) { elm.innerHTML = inputObject[i]; }  } // if it's not script, update the corresponding div
					} if (hasscript) eval(inputObject['script']); // some on-the-fly-javascript exchanging support too
				} 
		catch (e) {
			alert('There was an error ( '+e.message+') processing: '+this.transport.responseText); 
		} // in case of an error					
	}
});



var ScrollDing = Fx.Scroll.extend({
	initialize: function(el){
		this.element = window;
		this.now = [];
		this.toElement($(el));
	},

	toElement: function(el){
		var target = $(el).getPosition();
		return this.scrollTo(target.x, target.y );
	}
});