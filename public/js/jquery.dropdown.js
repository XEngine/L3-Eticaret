/* 
Easy Drop Down for jQuery
appgrinders.github.com/jquery-easy-drop-down 
*/

(function( $ ){

	/* Cache a combined list of all dropdown elements, so each doc click isn't a DOM search */
	var $dropdowns = $([]);
	// Create a document click handler that will close any open dropdowns:
	$(document).click(function(e) {$dropdowns.find('.dropdown-panel:visible').hide();});
	console.log('anan');
	var methods = {
		
		init : function( options ) { 
	
			// Add this set to our combined cache:
			$dropdowns = $dropdowns.add(this);
	
			// Create some defaults, extending them with any options that were provided
			var settings = $.extend( {}, options);
	
	    	return this.each(function() {        

	      		// dropdown plugin code here
				var $this = $(this);
				
				if (settings.width) {
					$this.find('.dropdown-panel').css('min-width',settings.width);
					$this.find('.dropdown-panel').css('width',settings.width);
				}
				if (settings.maxHeight) {
					$this.find('.dropdown-panel').css('max-height',settings.maxHeight);
				}
				
				/* NOTE: 2 ways to get id:
				var id = this.id;
				var id = $this.attr('id');
				*/
				
				// Only proceed with click-event handler if the plugin
				// has not already been initialized on this given element:
				if ( $this.data('initialized') )
					return;
				else
					$this.data('initialized', true);
				
				$this.click(function(e) {
					
					e.stopPropagation();
				    $dropdowns.not(this).find('.dropdown-panel:visible').hide()
				    var $target = $(e.target);

					// If the click is on the dropdown button, 
					if ($target.is('.dropdown-button, .dropdown-icon')) {
				        $(this).find('.dropdown-panel').slideToggle('fast');
				    }
					// Else, if it's in the panel, do the callback.
					else {
						if (settings.callback) {
							callback = settings.callback;
							callback($target);
						}
					}
				});

			});
	
		},
		show : function( ) {
			
			return this.each(function() {
				$(this).find('.dropdown-panel:hidden').slideDown('fast');
			});
		},
		hide : function( ) { 
			
			return this.each(function() {
				$(this).find('.dropdown-panel:visible').slideUp('fast');
			});	
		},
		toggle : function( ) { 
			
			return this.each(function() {
				$(this).find('.dropdown-panel').slideToggle('fast');
			});	
		}

	};

	$.fn.dropcart = function( method ) {
    
		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.dropdown' );
		}
		
	};



})( jQuery );

