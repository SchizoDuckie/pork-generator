var LightBox = new Class(
{
	initialize: function(page, options)
	{
		this.setOptions(options);
		this.page = page;
		this.Container = new Element('DIV', { 'class': 'lightbox' }).setOpacity(0).injectInside(document.body);
		this.Dialog = new Element('DIV', { 'class': 'lightboxdialog'}).injectInside(this.Container);
		this.Title = ( this.options.title) ? new Element('DIV', { 'class': 'lightboxtitle'}).appendText(this.options.title).injectInside(this.Container) : false;
		this.DialogContent = new Element('DIV').injectInside(this.Dialog);
	
		new Ajax(this.page, {update:this.DialogContent, evalScripts:true}).request();

		this.Container.effect('opacity', {duration: 500}).custom(0, 0.85);
		this.Container.effect('width', {duration: 400}).custom(0, window.getWidth());
		this.Container.effect('height', {duration: 400}).custom(0, window.getHeight());
		this.Container.effect('left',  {duration: 400}).custom(window.getWidth() / 2+window.getScrollLeft, window.getScrollLeft());
		this.Container.effect('top',  {duration: 400, onComplete:function(){this.initDialog()}.bind(this)}).custom((window.getHeight() / 2) + window.getScrollTop(), window.getScrollTop());
		this.event1 = window.addEvent('scroll', this.repaint.bind(this));
		this.event2 = window.addEvent('resize', this.repaint.bind(this));
	},
	
	options: {
		onClose: false
	},


	repaint: function()
	{
		top = window.getScrollTop(); 
		left = window.getScrollLeft(); 
		width = window.getWidth();
		height = window.getHeight();
		if(this.Container && this.Dialog)
		{
		this.Container.setStyles({ 'left': left, 'top': top, 'width': width, 'height': height}); 
		this.Dialog.setStyles({	'left': left, 'top': top, 'width': width -200, 'height': height - 200}); 
		}

	},
	initDialog: function()
	{
		this.Dialog.setStyles({	'width': (window.getWidth() - 200), 'left': window.getScrollLeft(), 'top': window.getScrollTop()}).injectInside(document.body);
		this.Dialog.effect('opacity', {duration: 800}).custom(0, 1);
		this.Dialog.effect('height',  {duration: 800, onComplete:function() { if(this.Title) this.Title.effect('opacity').custom(0,1)}.bind(this)}).custom(0, window.getHeight() - 200);
		new Element('A', { 'class': 'closeButton', 'events': { 'click': function(){this.hide();}.bind(this) }}).appendText('Sluiten').injectInside(this.Container);		
	},

	hide: function()
	{	
		if(this.Container && this.Dialog)
		{
			this.Container.effect('opacity', {duration: 500 }).custom(0.85, 0);
			this.Dialog.effect('opacity', {duration: 500, onComplete: function(){this.cleanUp(); }.bind(this)}).custom(1, 0);
		}
	},

	cleanUp: function()
	{
			this.Container.innerHTML = '';
			this.Container = false;
			this.Dialog.innerHTML = '';
			this.Dialog = false;
			this.reInitEditor();
			window.removeEvents();
			if(this.options.onClose) { this.options.onClose.call(this); }
	},

	reInitEditor: function()
	{
		if(typeof(window.FCKeditorAPI) == "object" || typeof(window.__FCKeditorNS) == 'object')
		{
			window.FCKeditorAPI = false;
			window.__FCKeditorNS = false;
		}
	}
});

LightBox.implement(new Options);
