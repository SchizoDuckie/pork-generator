
window.FormValidator = new Class({
	initialize: function(form)
	{
		if(!form) return;
		this.form = form;
		this.validatables = Array();
		
		// find the form tags by hand to work around stupid ie behavior
		this.gatherElements()
	},

	rules : { // available regex validations.
				'date' : "^[0-9]{1,2}[-/][0-9]{1,2}[-/][0-9]{4}$",
				'email' : "^[0-9a-zA-Z._-]*[@][0-9a-zA-Z._-]*[.][a-z]{2,4}$", 
				'amount' : "^[-]?[0-9]+$",
				'number' : "^[-]?[0-9,]+$",
				'alfanum' : "^[0-9a-zA-Z ,.-_\\s\?\!]+$",
				'not_empty' : "[a-z0-9A-Z]+",
				'words' : "^[A-Za-z]+[A-Za-z \\s]*$",
				'phone' : "^[0-9\-]{10,}$",
				'zipcode' : "^[1-9][0-9]{3}[ ]?[a-zA-Z]{2}$",
				'plate' : "^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}$",
				'price' : "^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?$",
				'2digitopt' : "^\d+(\,\d{2})?$",
				'2digitforce' : "^\d+\,\d\d$",
				'anything' : "^.{1,}$"
	},

	messages : {
				'date' : "Dit is een ongeldige datum",
				'email' : "Dit is geen geldig email address (user@domain.tld)", 
				'amount' : "Dit is geen geldige aantal",
				'number' : "Dit is geen geldige getal",
				'alfanum' : "Dit is geen geldige alfanumerieke waarde",
				'not_empty' : "Dit veld mag niet leeg zijn",
				'words' : "Dit is zijn geen geldige (paar) woorden",
				'phone' : "Dit is geen geldig telefoonnummer",
				'zipcode' : "Dit is geen geldige postcode",
				'plate' : "Dit is geen geldige kentekenplaat",
				'price' : "Dit is geen geldige prijs",
				'2digitopt' : "Dit is geen geldig getal (met optionele decimalen)",
				'2digitforce' : "Dit is geen geldig getal met 2 decimalen",
				'anything' : ''
	},

	gatherElements: function()
	{
		for (var i =0; i<this.form.elements.length; i++) { // loop through the form's elements. 
		 	try {
				if(this.form.elements[i].getAttribute("validation") != null) { this.validatables[this.validatables.length] = this.form.elements[i]; } // does it have a 'validation' attribute? then validate later
			} catch (e) { } // shhh any errors
		}
	},
	
	validate: function()
	{
		var returnValue = true;
		for (var i in this.validatables) { // loop through available to-validate elements
			validateList = (this.validatables[i].getAttribute("validation").indexOf("|") > 0) ? this.validatables[i].getAttribute("validation").split("|") : Array(this.validatables[i].getAttribute("validation")); // find out if we need to do more than one
			if (this.validatables[i].getAttribute("validation").indexOf('not_empty') > -1 || this.validatables[i].value.length >0)  //only at not_empty or non-empty element value
			for (var val in validateList) {		// loop them
				if (this.rules[validateList[val]] != undefined) 
					{ // if it's one of the available rules
						validated = (new RegExp(this.rules[validateList[val]]).exec(this.validatables[i].value)) ? true : false; // match it against element.value
						if (!validated) {
							returnValue = validated; 
							this.addMessage(this.validatables[i]); // not validated: add errormessage
						} else {
							this.clearMessage(this.validatables[i]); // validated: clear existing errormessage;
						}
					} 
					else { // not one of the standard functions. check for manual validation function.
						if (validateList[val] == "checked") { validated=this.validateChecked(this.validatables[i]); 
							if (!validated) {
								returnValue = validated; 
								this.addMessage(this.validatables[i]); // not validated: add errormessage
							} else {
								this.clearMessage(this.validatables[i]); // validated: clear existing errormessage;
							}
						}
					}
				}
		}
		return returnValue;
	},

	getMessage: function(validations)
	{
		validateList = (validations.indexOf("|") > 0) ? validations.split("|") : Array(validations); 
		message = '';
		for(var i=0; i<validateList.length; i++)
		{
			message += '<span class="message">'+this.messages[validateList[i]]+'</span>';
		}
		return message;
	},

	addMessage: function(element, validations) // adds an errormessage to an element
	{
		element = $(element);
		if(!element.msgElm)
		{ // create new one
			element.msgElm = new Element('div', {id: element.name+'_validationmsg', className :'validationMessage' }).injectInside(element.getParent());
			element.msgElm.innerHTML = this.getMessage(validations);
			element.effect = new Fx.Slide(element.msgElm, {duration: 200}).hide();
		}
		element.addClass('unvalidated');
		if(element.effect.now[1] == 0) { element.effect.toggle() }		
	},
	clearMessage: function(element) // cleans out existing messages and added classname.
	{
		element = $(element);
		if(element && element.msgElm)
		{
			if(element.effect.now[1] > 0)  element.effect.toggle();
			element.removeClass('unvalidated');
		}		
	},

	validateChecked:function(element)
	{
		returnValue = false;
		var elements = document.getElementsByName(element.name);
		for(var i =0; i<elements.length; i++)
		{
			if ( elements[i].checked == true || elements[i].checked=="checked" || elements[i].selected=="selected" || elements[i].selected==true) returnValue = true;
		}
		return returnValue;
	}
});
