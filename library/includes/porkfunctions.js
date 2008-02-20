
RelationEditor = new Class(
{
	init: function()
	{
		window.Box = false;
		source = false;
	},
	
	connect: function(el, source, target, id)
	{
		el = $(el).getParent();
		if(el.editor) el.editor.setStyle('display', 'none'); 
		els = $$('.notconnected').each(function(el){el.setStyle('display','none');});
		targetel = $(source+'_'+id+'_'+target+'_notconnected').setStyle('display','block');
		targetel.innerHTML = '<img src="./images/wait.gif"><br>Bezig met laden...';
		new Ajax('./ajax/connect/'+source+'/'+target+'/'+id, {update:targetel, onComplete: function(){ RelationEditor.addHovers(); }}).request();
	},
	
	addHovers: function()
	{
		 $each($$('.notconnected LI'), function(el) {
			el.removeEvents().addEvents(RelationEditor.hoverEvents);
		  });
	
	},

	hoverEvents : {
		'mouseover': function(ev) 
		{
			this.addClass('hovering');	
		},
		'mouseout': function(ev) 
		{
			this.removeClass('hovering');			
		}
	},

	makeConnection: function(el, source, target, id, targetid)
	{
		el = $(el);
		el.getParent().setStyle('display', 'none');
		var element = $(source+'_'+id+'_'+target+'_connected');
		new Ajax('./ajax/makeconnection/'+source+'/'+target+'/'+id+'/'+targetid, {
				evalScripts:true,
				updateMultiple: true,
				onComplete:function()
				{
					$$("#"+source+"_"+id+"_"+target+"_connected LI").each(function(el)
					{
						el.addEvents(RelationEditor.linkEvents).effect('opacity').start(0, 1);
					});
				}
			}).request();
	},

	add: function(el, source, target, id)
	{
		try	{ $(source+'_'+id+'_'+target+'_notconnected').setStyle('display', 'none'); }	catch (e) {}
		el = $(el).getParent();
		if(target != 'foto')
		{
			new Ajax('./ajax/add/'+target+'/'+source+'/'+id+'/connect', {
				evalScripts:true,
				onComplete:function(inputHTML)
				{
					if(!el.editor)
					{
						var newDiv = new Element('div').injectInside(this);
						el.editor = newDiv;	
					}
					el.editor.innerHTML = inputHTML;
					el.editor.setStyles({'display':'block', 'visibility':'hidden'}).effect('opacity').start(0, 1);
					RelationEditor.attachEvents();
							
				}.bind(el)
			}).request();

		}
		else
		{
			new LightBox('./foto/upload');
		}

	},

	addDone:function(source, target, id, iframe)
	{
		eval("var inputObject = "+iframe.transport.responseText+";");
		if(inputObject.script)
		{
			eval(inputObject.script);
		}
		if(typeof(inputObject.newID) != 'undefined')
		{
			$('editorStatus').innerHTML = inputObject.editorStatus;
			var el = iframe.form;
			this.makeConnection(el, source, target, id, inputObject.newID);
		}
	
	},
	
	switchToEditor:function(newID, newType)
	{
		if(newType && newID)
		{
			gotoUrl('./'+newType+'/edit/'+newID);
		}
	},

	edit: function(el, source, target, id, targetid)
	{
		el=$(el);
		new Ajax('./ajax/editObject/'+source+'/'+target+'/'+id+'/'+targetid, {
				evalScripts:true,
				onComplete:function(inputHTML)
				{
					if(!el.editor)
					{
						var newDiv = new Element('li').injectAfter($(el).getParent().getParent());
						el.editor = newDiv;	
					}					
					el.editor.innerHTML = inputHTML;
					el.editor.setStyles({'display':'block', 'visibility':'hidden'}).effect('opacity').start(0, 1);
				}.bind(el)
			}).request();
	},

	editDone:function(source, target, id, targetid)
	{
		//////console.log(this);
		new Ajax('./ajax/getobjectrelations/'+source+'/'+target+'/'+id+'/'+targetid, {
				evalScripts:true,
				updateMultiple: true,
				onComplete:function()
				{
					$$("#"+source+"_"+id+"_"+target+"_connected LI").each(function(el)
					{
						el.addEvents(RelationEditor.linkEvents).effect('opacity').start(0, 1);
					});
				}
			}).request();
	},

	

	remove: function(el, source, target, id, targetid)
	{
//		el = $(el).getParent().getParent();
//		el.effect('opacity', {onComplete:function(){this.setStyle('display', 'none')}.bind(el)}).custom(1,0);
		new Ajax('./ajax/removeconnection/'+source+'/'+target+'/'+id+'/'+targetid, {
				evalScripts:true,
				updateMultiple: true,
				onComplete:function()
				{
					$$("#"+source+"_"+id+"_"+target+"_connected LI").each(function(el)
					{
						el.addEvents(RelationEditor.linkEvents).effect('opacity').start(0, 1);
					});
				}
			}).request();		
	},

	linkEvents: 
	{
		'mouseenter': function(ev) 
		{
			if(this.getElement('.links'))
			{
			this.fx1 = this.getElement('.links').effects(this.fxOptions).custom({'opacity':[0, 1]} ); 
			}
		},
		'mouseleave': function(ev) 
		{
			if(this.getElement('.links'))
			{
			if(this.fx1) { this.fx1.stop(); this.fx1.custom({'opacity':[this.fx1.now['opacity'], '0']}); }
			}
		}
	},
	fxOptions: 
	{
		duration:100 
	},

	
	attachEvents: function()
	{
		$$('.relations LI UL LI').each(function(element) 
		{
			if(element.getElement('.links'))
			{
				element.removeEvents(RelationEditor.linkEvents);
				element.addEvents(RelationEditor.linkEvents);
			}
		});
	}
});

var RelationEditor = new RelationEditor();

window.addEvent('domready', function()
{ 
	RelationEditor.attachEvents();	
});


function gotoUrl(newUrl)
{ 
	document.location.href= newUrl.indexOf('./') == 0 ? document.getElementsByTagName('BASE')[0].href + newUrl.substr(2) : newUrl;
}