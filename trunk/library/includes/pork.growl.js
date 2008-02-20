Growl = new Class(
{
	initialize: function(options)
	{
		this.setOptions(options);
		this.preload();
	},
	
	options: {
		onComplete: false,
		growlWidth: '336px',
		growlHeight: '154px',
		growlImage: './images/growl.png',
		styles: {
			position: 'absolute', zIndex: '1000', backgroundColor: 'transparent', display:'block', overflow:'hidden', margin: '0 auto', font:'18px/25px "Lucida Grande", Arial', color: 'white', textAlign:'center', verticalAlign: 'middle'	
		}
	},

	growl: function(message, options)
	{
			this.setOptions(options);
			this.Container = new Element('p').setStyles(this.options.styles).setStyles({width: this.options.growlWidth, height: this.options.growlHeight, lineHeight:this.options.growlHeight, background:'transparent url("'+this.options.growlImage+ '") no-repeat' }).setHTML(message).injectInside(document.body);
			var top = window.getScrollTop() + ((window.getHeight() /2) - parseInt(this.options.growlHeight) /2 ) ; 
			var left = window.getScrollLeft() + ((window.getWidth() /2) - parseInt(this.options.growlWidth) /2 );
			if(this.Container) 	this.Container.setStyles({ 'left': left, 'top': top}); 
			this.Container.setOpacity(0.85).effect('width', {duration: 500, onComplete:function(){setTimeout(function(){this.hide() }.bind(this), 500) }.bind(this)}).hide().custom(0, this.options.growlWidth);
	},

	preload:function()
	{
		var img = new Image();
		img.src = this.options.growlImage;
		window.Growl = function(msg, options) { this.growl(msg, options); }.bind(this);
	},

	
	hide: function()
	{	
		if(this.Container)	 this.Container.effect('width', {duration: 200, onComplete: function(){ this.cleanUp(); }.bind(this)}).custom(this.options.growlWidth, 0);
	},

	cleanUp: function()
	{
			this.Container.remove();
			if(this.options.onComplete){ this.options.onComplete.call(this); }
	}

});

Growl.implement(new Options);

new Growl();




