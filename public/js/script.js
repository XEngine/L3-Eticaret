var menuItemClass = 'emenu-item';
var menuItemActiveClass = 'emenu-item-active';
var menuItemHoverClass = 'emenu-item-hover';
var menuItemStandAloneClass = 'emenu-item-sa';
var menuItemLinkClass = 'emenu-item-text';
var menuItemLinkHoverClass = 'emenu-item-text-hover';
var menuItemContentClass = 'emenu-item-container';
var effectSpaceID = 'emenu-menuItem-effectSpace';
var showDurationID = 'emenu-menuItem-showDuration';
var hideDurationID = 'emenu-menuItem-hideDuration';
var showMenuItemID = 'emenu-show-menuItem';
var showTimeOutID = 'emenu-show-timeOut';
var hideMenuItemID = 'emenu-hide-menuItem';
var emenuajax = "";

(function($) {
	$.fn.extend({
		emenu:function(arg) {
			var eOptions = {
				showDuration: 250,
				hideDuration: 150
			}

			var option = (typeof(arg)!='string') ? $.extend(eOptions,arg) : $.extend(eOptions,{});
			if($(this).data("emenu") == "done") return false;
			var $menu = $(this).addClass('emenu-root').data("emenu", "done");
			var $menuItems = $menu.children('li');

			$('body').append('<div class="emenu_effectSpace"></div>');
			var $effectSpace =  $('.emenu_effectSpace').css({display:'none',position:'absolute',width:'2px',height:'29px','background-color':'#fff','z-index':999999910});
			$.store2.setItem(effectSpaceID,$effectSpace);
			$.store2.setItem(showDurationID,option.showDuration);
			$.store2.setItem(hideDurationID,option.hideDuration);
			var uid_index = 0;
			$menuItems.each(function(){
				var $menuItem = $(this).addClass(menuItemClass);
				var $menuItemLink = $menuItem.children('a:first').addClass(menuItemLinkClass);
				var $menuItemLinkTarget = $($menuItemLink.attr('targ')).hide().addClass(menuItemContentClass);
				$menuItem.attr('uid','emenu-item-header-' + (++uid_index).toString());
				var $menuItemUniqueID = $menuItem.attr('uid');
				var $menuHideItemName = hideMenuItemID + '-' + $menuItemUniqueID;
                if($menuItemLinkTarget.attr('id') =='div_emenu_0_content')
                {
                $menuItemLinkTarget.css({position:'absolute', 'z-index':99999999 ,cursor:'default', height:'auto !important', border:'none'}).hide();
                }
                else{
                $menuItemLinkTarget.css({position:'absolute', 'z-index':99999999 ,cursor:'default', height:'auto !important', border:'none'}).hide();
                }
				
                //$menuItemLinkTarget.css({padding:'5px',position:'absolute','background-color':'#ffffff','z-index':1000, border:'1px solid black',cursor:'default'}).hide();
				$menuItemLinkTarget.appendTo($menuItem);
				var standAloneItem = ($menuItemLink.attr('standalone') != null && $menuItemLink.attr('standalone') == 'true');
				
				if (standAloneItem){
					$menuItem.removeClass(menuItemClass).addClass(menuItemStandAloneClass);
					return;
				}
					
				$menuItem.hover(function(){
					$menuItemLink.removeClass(menuItemLinkClass).addClass(menuItemLinkHoverClass);
					
					if ($menuItem.data('isActive') == null || $menuItem.data('isActive') == false)
						$menuItem.removeClass(menuItemActiveClass).removeClass(menuItemClass).addClass(menuItemHoverClass);
						
					if ($.store2.hasItem(showTimeOutID)){
						abortShowItem();
						return;
					}
					if ($.store2.hasItem($menuHideItemName + '-timeOut')){
						abortHideItem($menuHideItemName);
						return;
					}
					$.store2.setItem(showMenuItemID,$menuItem);
					$.store2.setItem(showTimeOutID,setTimeout('showItem()',100));


				},
				function(){
					$menuItemLink.removeClass(menuItemLinkHoverClass).addClass(menuItemLinkClass);
					var oldStoID = $.store2.getItem($menuHideItemName + '-timeOut');
					if(oldStoID){
						clearTimeout(oldStoID);
					}
					
					if ($.store2.hasItem(showTimeOutID)){
						var item = $.store2.getItem(showMenuItemID);
						if ($menuItemUniqueID == item.attr('uid')){
							abortShowItem();
							$menuItemLink.removeClass(menuItemLinkHoverClass).addClass(menuItemLinkClass);
							return;
						}
					}

					$.store2.setItem($menuHideItemName,$menuItem);			
					$.store2.setItem($menuHideItemName + '-timeOut',setTimeout('hideItem("' + $menuHideItemName + '")',100));
					
				});
			});
			return this;
		}
	})
})(jQuery);

	function hideItem(menuItemName){
		var menuItem = $.store2.getItem(menuItemName);
		if (menuItem != null){
			var duration = $.store2.getItem(hideDurationID);	
			menuItem.data('isActive',false);
			menuItem.children('.emenu_effectSpace').hide();
			menuItem.children('div.' + menuItemContentClass + ':first').fadeOut(duration);
			menuItem.removeClass(menuItemActiveClass).removeClass(menuItemHoverClass).addClass(menuItemClass);
			clearHideMenuVariables(menuItemName);
		}
	}

	function hideItems(){
		var menuItems = $(".hb-menu-item .emenu-item-container:visible");
		menuItems.hide();
		menuItems.parent().data('isActive',false);
		menuItems.parent().removeClass(menuItemActiveClass).removeClass(menuItemHoverClass).addClass(menuItemClass);
		
		//clearHideMenuVariables(menuItemName);
	}

	function showItem(){

		if(emenuajax != ""){
			emenuajax.abort();
		}
		var $menuItem = $.store2.getItem(showMenuItemID);
		if($menuItem == undefined) return false;
		var $menuItemLink = $menuItem.children('a:first').addClass(menuItemLinkClass);
		var $menuItemLinkTarget = $($menuItemLink.attr('targ')).hide().addClass(menuItemContentClass);
		var duration = $.store2.getItem(showDurationID);
		var effectSpace, position, positionLeft, positionTop, content;
		if($menuItem.attr("isloaded") != "true"){
			var itemid = $menuItemLink.attr("itemid");
			var url = "/category";
			var dataConfig = '{"MenuID":'+itemid+'}';
			emenuajax = $.ajax({
				url: url,
				data: "menuid="+itemid,
				dataType: 'JSON',
				type: "POST",
				success:
						function (result) {
							console.log(result.d);
							if(result.d != null)
							{
								$menuItemLinkTarget.html(result.d);
								$menuItemLinkTarget.height($menuItemLinkTarget.height());

								effectSpace = $.store2.getItem(effectSpaceID);
								position = $menuItem.position();
								positionLeft = position.left + $menuItem.width();
								offsetLeft = $menuItem.position().left + $menuItem.width();
								offsetTop = $menuItem.position().top;
								content = $menuItem.children('div.' + menuItemContentClass + ':first');

								if(($(window).height() + $("html").scrollTop() < content.height() + $menuItem.offset().top) && ($(window).height() + $("html").scrollTop() - ($menuItem.offset().top + 22) > 22)){
									positionTop = ($("html").scrollTop() + $(window).height() + position.top) - ($menuItem.offset().top + content.height() + 10);
									content.find(".productMarkaMenuItem").removeClass("productMarkaMenuItem").addClass("productMarkaTopLeft");
								}else{
									positionTop = position.top;
									content.find(".productMarkaTopLeft").removeClass("productMarkaTopLeft").addClass("productMarkaMenuItem");
								}
								
								if(content.height() > $(window).height()){
									positionTop = position.top;
									content.find(".productMarkaTopLeft").removeClass("productMarkaTopLeft").addClass("productMarkaMenuItem");
								}
								
								effectSpace.css({left: ((offsetLeft + 9).toString() + 'px') , top:(offsetTop + 1).toString() + 'px'});
								content.after(effectSpace);
								content.css({left:(positionLeft + 10).toString() + 'px',top:positionTop + 'px'});
								$menuItem.removeClass(menuItemClass).removeClass(menuItemHoverClass).addClass(menuItemActiveClass);
								effectSpace.show();
								$menuItem.data('isActive',true);
								content.fadeIn(duration);
								clearShowMenuVariables();
								
								$menuItem.attr("isloaded", "true");
							}
						},
			});
		}else{
			if ($menuItem != null){
				content = $menuItem.children('div.' + menuItemContentClass + ':first');
				position = $menuItem.position();
				if(($(window).height() + $("html").scrollTop() < content.height() + $menuItem.offset().top) && ($(window).height() + $("html").scrollTop() - ($menuItem.offset().top + 22) > 22)){
					positionTop = ($("html").scrollTop() + $(window).height() + position.top) - ($menuItem.offset().top + content.height() + 10);
					content.find(".productMarkaMenuItem").removeClass("productMarkaMenuItem").addClass("productMarkaTopLeft");
				}else{
					positionTop = position.top;
					content.find(".productMarkaTopLeft").removeClass("productMarkaTopLeft").addClass("productMarkaMenuItem");
				}
				
				if(content.height() > $(window).height()){
					positionTop = position.top;
					content.find(".productMarkaTopLeft").removeClass("productMarkaTopLeft").addClass("productMarkaMenuItem");
				}

				content.css({left:(positionLeft + 10).toString() + 'px',top:positionTop + 'px'});
				offsetLeft = $menuItem.position().left + $menuItem.width();
				offsetTop = $menuItem.position().top;
				effectSpace = $.store2.getItem(effectSpaceID);
				effectSpace.css({left: ((offsetLeft + 9).toString() + 'px') , top:(offsetTop + 1).toString() + 'px'});
				content.after(effectSpace);
				effectSpace.show();
				$menuItem.data('isActive',true);
				content.fadeIn(duration);
				clearShowMenuVariables();
			}
		}	
	}

	function abortShowItem(){
		if(emenuajax != ""){
			emenuajax.abort();
		}
		var item = $.store2.getItem(showMenuItemID);
		item.removeClass(menuItemHoverClass).removeClass(menuItemActiveClass).addClass(menuItemClass);
		clearTimeout($.store2.getItem(showTimeOutID));
		clearShowMenuVariables();
	}

	function abortHideItem(menuItemName){
		clearTimeout($.store2.getItem( menuItemName + '-timeOut'));
		clearHideMenuVariables();	
	}

	function clearShowMenuVariables(){
		$.store2.removeItem(showMenuItemID);
		$.store2.removeItem(showTimeOutID);
	}

	function clearHideMenuVariables(menuItemName)
	{
		$.store2.removeItem(menuItemName);
		$.store2.removeItem(menuItemName + '-timeOut');
	}

(function($) {
	$.store2 = {
        maxSize : 20,
		keys : new Array(),
		cache_length : 0,
		items : new Array(),
		setItem: function(pKey, pValue){
			if (typeof(pValue) != 'undefined') 
			{
				if (typeof(this.items[pKey]) == 'undefined') 
				{
					this.cache_length++;
				}
				this.keys.push(pKey);
				this.items[pKey] = pValue;
				
				if (this.cache_length > this.maxSize)
				{
					//this.removeOldestItem();
				}
			}
			return pValue;
		},
		removeItem: function(pKey){
			var tmp;
			if (typeof(this.items[pKey]) != 'undefined') 
			{
				this.cache_length--;
				var tmp = this.items[pKey];
				delete this.items[pKey];
			}
			return tmp;
		},
		getItem: function(pKey){
			return this.items[pKey];
		},
		hasItem: function(pKey){
			return typeof(this.items[pKey]) != 'undefined';
		},
		clear:function(){
			var tmp = this.cache_length;
			this.keys = new Array();
			this.cache_length = 0;
			this.items = new Array();
			return tmp;
		}
	};	
})(jQuery);