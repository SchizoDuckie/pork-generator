/**
 * Smooth scroller to any element
 */
var Scroller = Fx.Scroll.extend({
	initialize: function(el){
		this.element = $(el);
		this.toElement(this.element);
	}
});