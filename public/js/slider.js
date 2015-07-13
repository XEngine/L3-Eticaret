/*
 * jQuery UI widget jstyler SliderJS v1.0
 *
 * Copyright (c) 2012 All Right Reserved, jstyler (http://www.jstyler.net)
 * 
 * This file is part of the SliderJS Component of jstyler (http://www.jstyler.net). The use, modification or distribution
 * of this file is subject to the licence information available at http://www.jstyler.net
 */
(function($) {$.widget('jstyler.SliderJS', {
	options: {},

	_create: function() {
		var self = this;
		var externalOptions = null;

		$.fn.reverse = [].reverse;

		// check to see if a settingsFile option is provided
		if(typeof(self.options.settingsFile) !== 'undefined' && self.options.settingsFile !== null)
			var settingsFile = this.options.settingsFile;
		else
			var settingsFile = '../slider/slidersettings.json';

		$.getJSON(settingsFile + '?' + new Date().getTime(), function(data) {
			externalOptions = data;

			// extend our options object with the external options
			if(typeof(externalOptions) !== 'undefined' && externalOptions !== null)
				self.options = $.extend(true, {}, externalOptions, self.options);

			self._initialize();
			self._writeConsoleMessage('The settings file for the SliderJS was loaded successfully!');
		})
		.error(function() { self._writeConsoleMessage('There was an error loading the settings file for the SliderJS!'); });

		return true;
	},

	_initialize: function() {
		var self = this;
		var options = self.options;

		if(options.generateSliderPath == true && typeof(options.settingsFile) !== 'undefined' && options.settingsFile !== null) {
			options.sliderPath = options.settingsFile.substring(0, options.settingsFile.indexOf('assets/settings/'));
		}
		
		self._sliderID = self._generateID();
		self._isFirstKey = false; // alt key
		self._isSecondKey = false; // grave accent key
		self._sliderAdminOpened = false;
		self._sliderAdmin = null;
		self._debug = false;
		self._sliderAdminContainer = null;
		self._sliderDialogue = null;
		self._sliderDialogueModal = null;
		self._originalOverflowSetting = 'visible';
		self._sliderContainer = null;
		self._slidesContainer = null;
		self._thumbnailsNavigationComponent = null;
		self._isThumbnailsNavigationComponentVisible = false;
		self._arrowsNavigationComponent = null;
		self._isArrowsNavigationComponentVisible = false;
		self._isPrevArrowVisible = false;
		self._isNextArrowVisible = false;
		self._anchorsNavigationComponent = null;
		self._isAnchorsNavigationComponentVisible = false;
		self._slideInfoComponent = null;
		self._slides = [];
		self._visibleSlidesData = [];
		self._visibleSlidesImageData = [];
		self._currentSlideIndex = 0;
		self._prevSlideIndex = 0;
		self._slideZIndexBase = self._getInt(options.slideZIndexBase, 10);
		self._transitionEffectsTimeouts = [];
		self._transitionEffectsFinishFunction = null;
		self._transitionEffects = ['usenoeffect', 'scrollhorizontal','scrollvertical','scrollup','scrollright','scrolldown','scrollleft','slidehorizontal','slidevertical','slideup','slideright','slidedown','slideleft','fade','cliphorizontal','clipvertical','slices-vertical-dropfromleft','slices-vertical-dropfromright','slices-vertical-vdroprandom','slices-vertical-raisefromleft','slices-vertical-raisefromright','slices-vertical-raiserandom','slices-vertical-dropaltfromleft','slices-vertical-dropaltfromright','slices-vertical-dropaltrandom','slices-vertical-slidefromleft','slices-vertical-slidefromright','slices-vertical-sliderandom','slices-vertical-fadefromleft','slices-vertical-fadefromright','slices-vertical-faderandom','slices-vertical-altallatonce','slices-horizontal-sliderightfromtop','slices-horizontal-sliderightfrombottom','slices-horizontal-sliderightrandom','slices-horizontal-slideleftfromtop','slices-horizontal-slideleftfrombottom','slices-horizontal-slideleftrandom','slices-horizontal-slidealtfromtop','slices-horizontal-slidealtfrombottom','slices-horizontal-slidealtrandom','slices-horizontal-dropfromtop','slices-horizontal-dropfrombottom','slices-horizontal-hdroprandom','slices-horizontal-fadefromtop','slices-horizontal-fadefrombottom','slices-horizontal-faderandom','slices-horizontal-altallatonce','boxes-fade','boxes-fadereverse','boxes-faderandom','boxes-centergrow','boxes-centergrowreverse','boxes-centergrowrandom','boxes-diagonal','boxes-diagonalreverse','boxes-diagonalgrow','boxes-reversediagonalgrow'];
		self._latestTransitionEffect = null;
		self._cssAllowedAnimateProperties = ['background-color','border-color','border-top-color','border-right-color','border-bottom-color','border-left-color','color','outline-color'];
		self._cssNotAllowedAnimateProperties = ['border','background'];
		self._isRotating = null;
		self._stoppedByAutoPauseRotation = null;
		self._stoppedByUserInteraction = null;
		self._lastSlideChangeTimestamp = new Date().getTime();
		self._lastSlideScrollChangeTimestamp = new Date().getTime();

		// create the slider components
		self.createSliderComponents();

		// bind the "sliderjs.gotoslide" event
		self.element.on('sliderjs.gotoslide', function(event, hash) { self._goToSlide(hash); });

		self._trigger(".initialized", null);
		
		/*if(self._sliderAdmin == null) {
			if($('#SliderJSAdminStyle').length <= 0) $('<link id="SliderJSAdminStyle" rel="stylesheet" type="text/css" href="/css/SliderJSAdmin.css" media="all" />').appendTo('head');
			setTimeout(function(){self._createSliderAdmin();}, 500);
		}*/
		
		/*self.element.on('contextmenu', {'slider': self}, function(event){
			if(event.altKey == true || event.which == 18) {
				if(self._sliderAdminOpened == false) {
					setTimeout(function(){self._createSliderAdmin();}, 500);
				}
				return false;
        	}
		});*/

		return true;
	},

	_createSliderAdmin: function() {
		var self = this;
		var options = self.options;

		// include the admin script
		if($('#SliderJSAdminScript').length <= 0)
			$('<script id="SliderJSAdminScript" type="text/javascript" src="/js/SliderJSAdmin.js"></script>').appendTo('head');

		// create the slider admin container and add some default styles
		self._sliderAdminContainer = $('<div id="sliderAdmin' + self._sliderID + '" class="sliderAdminContainer"></div>').css({'position': 'fixed', 'width': '290px', 'height': '56px'});

		// append the slider admin container to the body
		self._sliderAdminContainer.appendTo(document.body);

		// create the slider admin
		$(document).ready(function() {
			self._sliderAdmin = $(self._sliderAdminContainer).SliderJSAdmin({"slider": $(self.element)});
			if(self._sliderAdmin) self._sliderAdminOpened = true;
		});
	},

	getSliderID: function() {
		return this._sliderID;
	},
	
	adminLoaded: function() {
		var self = this;
		self._trigger(".adminLoaded", null);
	},

	adminClose: function() {
		var self = this;
		self._sliderAdmin = null;
		self._sliderAdminOpened = false;
		self._trigger(".adminClose", null);
	},

	getOptions: function() {
		return this.options;
	},

	setOptions: function(newOptions) {
		if(typeof(newOptions) === 'undefined' || newOptions === null) return false;
		this.options = jQuery.extend(true, {}, newOptions);
		return true;
	},

	/*** SLIDER CONTAINER ***/

	_modifyTheSliderContainer: function () {
		var self = this;
		var options = self.options;

		self._sliderContainer = self.element;

		// empty the slider container
		self._sliderContainer.empty();

		// modify the slider container
		self._sliderContainer.addClass('sliderContainer').css(self._CSSParseUrls(options.sliderContainerStyle));

		return true;
	},

	_revertTheSliderContainer: function() {
		var self = this;
		var options = self.options;

		self._sliderContainer = self.element;

		// empty the slider container
		self._sliderContainer.empty();

		// modify the slider container
		self._sliderContainer.removeClass('sliderContainer').removeAttr('style');

		return true;
	},

	_addComponentToTheSliderContainer: function (componentToAdd) {
		var self = this;
		var options = self.options;

		self._sliderContainer.append(componentToAdd);
		self._updateSliderContainerSize();

		return true;
	},

	_updateSliderContainerSize: function() {
		var self = this;
		var options = self.options;

		var sliderContainerWidth = 0;
		var sliderContainerHeight = 0;

		var maxChildWidth = 0;
		var maxChildHeight = 0;

		// search all the children of the slider container (that are components of the slider) and select the highest combination of dimension + coordinate offset
		self._sliderContainer.children().each(function(index) {
			if($(this).hasClass('slidesContainer') || $(this).hasClass('thumbnailsNavigationComponent') || $(this).hasClass('arrowsNavigationComponent') || $(this).hasClass('anchorsNavigationComponent') || $(this).hasClass('slideInfoComponent')) {
				var childWidth = self._getOuterWidth($(this), true) + self._getInt($(this).css('left'));
				var childHeight = self._getOuterHeight($(this), true) + self._getInt($(this).css('top'));

				if(childWidth > maxChildWidth) maxChildWidth = childWidth;
				if(childHeight > maxChildHeight) maxChildHeight = childHeight;
			}
		});

		sliderContainerWidth = maxChildWidth;
		sliderContainerHeight = maxChildHeight;

		// modify the actual dimensions of the slider container
		self._sliderContainer.css({'width': sliderContainerWidth + 'px'});
		self._sliderContainer.css({'height': sliderContainerHeight + 'px'});

		$('.ui-effects-wrapper', self._sliderContainer).remove();

		return true;
	},

	/*** SLIDER COMPONENTS ***/

	createSliderComponents: function () {
		var self = this;
		var options = self.options;

		// if the bindArrowKeys option is set to true than bind these events
		if(options.bindArrowKeysEvents === true) {
			$(document).on(
				'keyup.' + self._sliderID,
				function(event){
					if(options.bindArrowKeysEvents === true) {
						if (event.which == 37) {
							self._goToPrevSlide();
							self._trigger(".prevkeypressed", null);
							return false;
						}
						else if (event.which == 39) {
							self._goToNextSlide();
							self._trigger(".nextkeypressed", null);
							return false;
						}
					}
				}
			);
		}

		// if the bindScroll option is set to true than bind these events
		if(options.bindScrollingEvents === true) {
			self.element.on({
				'mousewheel': function(event, delta) {
					if(options.bindScrollingEvents === true) {
						var currentTimestamp = new Date().getTime();
						if(currentTimestamp - self._lastSlideScrollChangeTimestamp < 700) return false;
						self._lastSlideScrollChangeTimestamp = currentTimestamp;
						
						(delta > 0) ? self._goToPrevSlide() : self._goToNextSlide();
						self._trigger(".scrollaction", null);
						return false;
					}
				}
			});
		}

		self._isRotating = false;
		self._stoppedByAutoPauseRotation = false;
		self._stoppedByUserInteraction = false;
		// if the autoPauseRotation option is set to true than bind these events
		if(options.autoPauseRotation === true) {
			self.element.on({
				'mouseenter': function(event) {
					if(options.autoPauseRotation === true) {
						if(self._isRotating === true) {
							self._stopRotation();
							self._stoppedByAutoPauseRotation = true;

							self._trigger(".paused", null);
						}
						return false;
					}
				},
				'mouseleave': function(event) {
					if(options.autoPauseRotation === true) {
						if(self._isRotating === false && self._stoppedByAutoPauseRotation === true && self._stoppedByUserInteraction === false) {
							self._startRotation();
							self._stoppedByAutoPauseRotation = false;

							self._trigger(".resumed", null);
						}

						return false;
					}
				}
			});
		}

		// if the bindTouchEvents option is set to true than bind these events
		if(options.bindTouchEvents === true) {
			self.element.TouchSupport({
				swipeUp: function(e, t) {self._goToPrevSlide(); self._trigger(".slidetoprev", null);},
				swipeRight: function(e, t) {self._goToPrevSlide(); self._trigger(".slidetoprev", null);},
				swipeDown: function(e, t) {self._goToNextSlide(); self._trigger(".slidetonext", null);},
				swipeLeft: function(e, t) {self._goToNextSlide(); self._trigger(".slidetonext", null);}
			});
		}

		// modify the outside container
		self._modifyTheSliderContainer();

		// create the slides container
		self._createSlidesContainer();

		// if ThumbnailsNavigationComponent is available, generate it according to its options
		if(options.thumbnailsNavigationComponent.available === true) self.createThumbnailsNavigationComponent();

		// if ArrowsNavigationComponent is available, generate it according to its options
		if(options.arrowsNavigationComponent.available === true) self.createArrowsNavigationComponent();

		// if AnchorsNavigationComponent is available, generate it according to its options
		if(options.anchorsNavigationComponent.available === true) self.createAnchorsNavigationComponent();

		// if slideInfoComponent is available, generate it according to its options
		if(options.slideInfoComponent.available === true) self.createSlideInfoComponent();

		return true;
	},

	destroySliderComponents: function() {
		var self = this;
		var options = self.options;

		$(document).off('keyup.' + self._sliderID);
		self.element.off('mousewheel mouseenter mouseleave');

		// destroy the slides container
		self._destroySlidesContainer();

		// if ThumbnailsNavigationComponent is available, destroy it
		if(options.thumbnailsNavigationComponent.available === true) self.destroyThumbnailsNavigationComponent();

		// if ArrowsNavigationComponent is available, destroy it
		if(options.arrowsNavigationComponent.available === true) self.destroyArrowsNavigationComponent();

		// if AnchorsNavigationComponent is available, destroy it
		if(options.anchorsNavigationComponent.available === true) self.destroyAnchorsNavigationComponent();

		// if slideInfoComponent is available, destroy it
		if(options.slideInfoComponent.available === true) self.destroySlideInfoComponent();

		// revert the outside container
		self._revertTheSliderContainer();

		return true;
	},

	/*** SLIDES COMPONENT ***/

	_generateSlides: function() {
		var self = this;
		var options = self.options;

		// get the visible slides array
		self._visibleSlidesData = [];
		$.each(options.slidesData, function(index, slide) {
			if(slide.visible === true) self._visibleSlidesData.push(slide);
		});

		if(self._visibleSlidesData && (self._visibleSlidesData.length > 0)) {
			for (var key in self._visibleSlidesData) {
				var slide = $('<div></div>');
				var slideImage = $('<img />');

				if(self._visibleSlidesData[key]) {
					// generate and hide every slide
					slide.attr('id', 'slide-' + key)
						.addClass('sliderSlide')
						.css({
							'position': 'absolute',
							'width': 	self._getInt(options.width),
							'height': 	self._getInt(options.height),
							'top': 		'0',
							'left': 	'0'
						})
						.css("z-index", self._slideZIndexBase)
						.addClass('hide')
						.hide();

					if(self._visibleSlidesData[key].url.length > 0) {
						slide.addClass('link');
						slide.on('click.slidesContainer', function() {
							window.location.href = self._visibleSlidesData[parseInt($(this).attr('id').substr(6))].url;
						});
					}

					slideImage.attr('id', 'slideImage-' + key)
						.addClass('slideImage')
						.attr('src', self._getPath(self._visibleSlidesData[key].src, 'slides'))
						.css('position', 'absolute');

					slide.html(slideImage.get(0));
					self._slides.push(slide.get(0));
					key++;
				}
			}

			// add the created slides to the slider
			self._slidesContainer.append(self._slides);

			// after the image loads take its size and calculate its final size and position based on the fitMode setting
			$('img.slideImage', self._sliderContainer).ImageLoaded(function(width, height){
				var sizeAndPosition = self._calculateImageSizeAndPosition(self.options.width, self.options.height, width, height, self.options.fitMode);
				$(this).css(sizeAndPosition);
				self._visibleSlidesImageData[$(this).attr('id').substr(11)] = sizeAndPosition;
			});

			self._createSlices();
			self._createBoxes();
		}

		return true;
	},

    _createSlices: function(){
    	var self = this;
		var options = self.options;

		var slideSlicesX = $('<div class="slideSlices slideSlicesX"></div>').css({'width':self._getInt(options.width),'height':self._getInt(options.height)});
		for(var i = 0; i < options.effectsSlicesX; i++) {
			var sliceIndentUnit = Math.round(options.width/options.effectsSlicesX);
			var sliceWidth = sliceIndentUnit;
			var sliceImageOffset = - i * sliceWidth;
			if(i == options.effectsSlicesX - 1) sliceWidth = options.width - sliceWidth * i;

			var slice = $('<div class="slideSlice" data-top="0" data-left="' + (sliceIndentUnit * i) + '" data-width="' + sliceWidth + '" data-height="' + options.height + '"><img class="sliceImage" src="" data-top="0" data-left="' + sliceImageOffset + '" style="left: ' + sliceImageOffset + 'px;" /></div>').css({
				top: '0px',
				left: (sliceIndentUnit * i) + 'px',
				width: sliceWidth + 'px',
				height: options.height
			}).appendTo(slideSlicesX);
		}
		slideSlicesX.css("z-index", self._slideZIndexBase + 2).appendTo(self._slidesContainer);

		var slideSlicesY = $('<div class="slideSlices slideSlicesY"></div>').css({'width':self._getInt(options.width),'height':self._getInt(options.height)});
		for(var i = 0; i < options.effectsSlicesY; i++) {
			var sliceIndentUnit = Math.round(options.height/options.effectsSlicesY);
			var sliceHeight = sliceIndentUnit;
			var sliceImageOffset = - i * sliceHeight;
			if(i == options.effectsSlicesY - 1) sliceHeight = options.height - sliceHeight * i;

			var slice = $('<div class="slideSlice" data-top="' + (sliceIndentUnit * i) + '" data-left="0" data-width="' + options.width + '" data-height="' + sliceHeight + '"><img class="sliceImage" src="" data-top="' + sliceImageOffset + '" data-left="0" style="top: ' + sliceImageOffset + 'px" /></div>').css({
				top: (sliceIndentUnit * i) + 'px',
				left: '0px',
				width: options.width,
				height: sliceHeight + 'px'
			}).appendTo(slideSlicesY);
		}
		slideSlicesY.css("z-index", self._slideZIndexBase + 2).appendTo(self._slidesContainer);
    },

    _createBoxes: function(){
    	var self = this;
		var options = self.options;

		var slideBoxes = $('<div class="slideBoxes"></div>').css({'width':self._getInt(options.width),'height':self._getInt(options.height)});
		var boxIndentUnitX = Math.round(options.width/options.effectsBoxesX);
		var boxIndentUnitY = Math.round(options.height/options.effectsBoxesY);
		var boxHeight = boxIndentUnitY;
		for(var i = 0; i < options.effectsBoxesY; i++) {
			var boxWidth = boxIndentUnitX;
			var boxImageOffsetY = - i * boxHeight;
			if(i == options.effectsBoxesY - 1) boxHeight = options.height - boxHeight * i;
			for(var j = 0; j < options.effectsBoxesX; j++) {
				var boxImageOffsetX = - j * boxWidth;
				if(j == options.effectsBoxesX - 1) boxWidth = options.width - boxWidth * j;

				var box = $('<div class="slideBox slideBox-' + i + '-' + j + '" data-top="' + (boxIndentUnitY * i) + '" data-left="' + (boxIndentUnitX * j) + '" data-width="' + boxWidth + '" data-height="' + boxHeight + '"><img class="boxImage" src="" data-top="' + boxImageOffsetY + '" data-left="' + boxImageOffsetX + '" style="top: ' + boxImageOffsetY + 'px; left: ' + boxImageOffsetX + 'px" /></div>').css({
					top: (boxIndentUnitY * i) + 'px',
					left: (boxIndentUnitX * j) + 'px',
					width: boxWidth + 'px',
					height: boxHeight + 'px'
				}).appendTo(slideBoxes);
			}
		}
		slideBoxes.css("z-index", self._slideZIndexBase + 2).appendTo(self._slidesContainer);
    },

	_calculateImageSizeAndPosition: function(viewportWidth, viewportHeight, imageWidth, imageHeight, fitMode) {
		var finalWidth = 0;
		var finalHeight = 0;
		var finalTop = 0;
		var finalLeft = 0;

		// just in case fitMode is not set to an allowed value, make it 'noscale'
		if($.inArray(fitMode, ['bestfit', 'boxfit', 'noscale', 'forcefit']) == -1) fitMode = 'bestfit';

		switch(fitMode) {
			case 'forcefit':
				finalWidth = viewportWidth;
				finalHeight = viewportHeight;
				break;
			case 'boxfit':
			case 'bestfit':
				var widthRatio = viewportWidth / imageWidth;
				var heightRatio = viewportHeight / imageHeight;
				var ratio = widthRatio;

				if(fitMode === 'boxfit' && heightRatio < ratio) ratio = heightRatio;
				if(fitMode === 'bestfit' && heightRatio > ratio) ratio = heightRatio;

				finalWidth = Math.ceil(imageWidth * ratio);
				finalHeight = Math.ceil(imageHeight * ratio);
				break;
			case 'noscale':
			default:
				finalWidth = imageWidth;
				finalHeight = imageHeight;
		}

		finalTop = Math.round((viewportHeight - finalHeight)/2);
		finalLeft = Math.round((viewportWidth - finalWidth)/2);

		return {'width': finalWidth, 'height': finalHeight, 'top': finalTop, 'left': finalLeft};
	},

	_createSlidesContainer: function () {
		var self = this;
		var options = self.options;

		self._slidesContainer = $('<div></div>').addClass('slidesContainer');
		self._slidesContainer.css(self._CSSParseUrls(options.slidesContainerStyle))
			.css({
				'top': self._getInt(options.top),
				'left': self._getInt(options.left),
				'width': self._getInt(options.width),
				'height': self._getInt(options.height)
			});

		self._addComponentToTheSliderContainer(self._slidesContainer);

		// generate and add the slides to the slider
		self._generateSlides();

		$(self._slides[self._currentSlideIndex]).show().removeClass('hide').addClass('show');

		if(options.autoStartRotation) {
			self._writeConsoleMessage('Started auto-rotation!');
			self._startRotation();
			self._trigger(".autorotatestart", null);
		}

		self._trigger(".loaded", null);

		return true;
	},

	_destroySlidesContainer: function () {
		var self = this;
		var options = self.options;

		if(self._slidesContainer !== null) {
			self._writeConsoleMessage('Stopped auto-rotation!');

			self._sliderContainer.off(".slidesContainer");

			self._stopRotation();
			self._trigger(".autorotatestop", null);

			self._slidesContainer.empty();
			self._slides = [];
			self._currentSlideIndex = 0;
			self._prevSlideIndex = 0;

			self._trigger(".unloaded", null);

			$('.slidesContainer *', self._sliderContainer).off();
			$('.slidesContainer', self._sliderContainer).off();
			$('.slidesContainer', self._sliderContainer).remove();
			self._slidesContainer = null;

			self._updateSliderContainerSize();

			return true;
		}

		return false;
	},

	/*** THUMBNAILS NAVIGATION COMPONENT ***/

	createThumbnailsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		if(options.thumbnailsNavigationComponent.available === true) {
			// generate the component if it doesn't already exist
			if(self._thumbnailsNavigationComponent === null) {
				self._generateThumbnailsNavigationComponent();

				// add the component to the slider
				self._addComponentToTheSliderContainer(self._thumbnailsNavigationComponent);

				// after the thumbnail loads calculate its final size and position with bestfit option
				$('img.thumbnail-image', self._sliderContainer).ImageLoaded(function(width, height){
					var sizeAndPosition = self._calculateImageSizeAndPosition(options.thumbnailsNavigationComponent.thumbnail.width, options.thumbnailsNavigationComponent.thumbnail.height, width, height, 'bestfit');
					$(this).css(sizeAndPosition);
				});

				self._thumbnailsNavigationComponent.SmoothScroller({orientation: options.thumbnailsNavigationComponent.orientation, contentId: 'thumbnailsNavigationComponentContent'});

				// if the bindTouchEvents option is set to true than bind these events
				if(options.bindTouchEvents === true) {
					if(options.thumbnailsNavigationComponent.orientation === 'vertical')
						self._thumbnailsNavigationComponent.TouchSupport({
							scrollV: function(e, t, offset) {self._thumbnailsNavigationComponent.SmoothScroller('modifyPosition', -offset);},
							momentumV: function(e, t, offset, time) {self._thumbnailsNavigationComponent.SmoothScroller('modifyPosition', -offset, time);}
						});
					else
						self._thumbnailsNavigationComponent.TouchSupport({
							scrollH: function(e, t, offset) {self._thumbnailsNavigationComponent.SmoothScroller('modifyPosition', -offset);},
							momentumH: function(e, t, offset, time) {self._thumbnailsNavigationComponent.SmoothScroller('modifyPosition', -offset, time);}
						});
				}

				self._markSelectedThumbnail(true);

				return true;
			}
		}

		return false;
	},

	destroyThumbnailsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		if(self._thumbnailsNavigationComponent !== null) {
			self._isThumbnailsNavigationComponentVisible = false;
			self._thumbnailsNavigationComponent.SmoothScroller('destroy');

			self._sliderContainer.off(".thumbnailsNavigationComponent");

			$('.thumbnailsNavigationComponent *', self._sliderContainer).off();
			$('.thumbnailsNavigationComponent', self._sliderContainer).off();
			$('.thumbnailsNavigationComponent', self._sliderContainer).remove();
			self._thumbnailsNavigationComponent = null;

			self._updateSliderContainerSize();

			return true;
		}

		return false;
	},

	_generateThumbnailsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		self._writeConsoleMessage('Generating Thumbnails-Navigation-Component!');

		// create the navigationComponent
		self._thumbnailsNavigationComponent = $('<div></div>').addClass('thumbnailsNavigationComponent');
		self._thumbnailsNavigationComponent.css(self._CSSParseUrls(options.thumbnailsNavigationComponent.style));

		var thumbnailsNavigationComponentContent = $('<div></div>').addClass('thumbnailsNavigationComponentContent').attr('id', 'thumbnailsNavigationComponentContent');

		self._thumbnailsNavigationComponent.append(thumbnailsNavigationComponentContent);

		// create an anchor to get the anchor width and anchor height
		var thumbnail = $('<div></div>')
			.css({
				'width': 	self._getInt(options.thumbnailsNavigationComponent.thumbnail.width),
				'height': 	self._getInt(options.thumbnailsNavigationComponent.thumbnail.height)
			})
			.css(self._CSSParseUrls(options.thumbnailsNavigationComponent.thumbnail.style));

		thumbnail.appendTo(document);
		var thumbnailWidth = self._getOuterWidth(thumbnail, true);
		var thumbnailHeight = self._getOuterHeight(thumbnail, true);
		thumbnail.remove();

		// destroy the anchor object as we don't need it anymore
		thumbnail = null;

		var thumbnails = [];
		for (var key in self._visibleSlidesData) {
			var thumbnail = $('<div></div>')
				.attr('id', 'thumbnail-' + key)
				.addClass('thumbnail')
				.css({
					'width': 	self._getInt(options.thumbnailsNavigationComponent.thumbnail.width),
					'height': 	self._getInt(options.thumbnailsNavigationComponent.thumbnail.height)
				})
				.css(self._CSSParseUrls(options.thumbnailsNavigationComponent.thumbnail.style));

			var thumbnailImage = $('<img />')
				.attr('id', 'thumbnail-image-' + key)
				.addClass('thumbnail-image')
				.css({
					'width': 	self._getInt(options.thumbnailsNavigationComponent.thumbnail.width),
					'height': 	self._getInt(options.thumbnailsNavigationComponent.thumbnail.height)
				});

			// an especially defined thumbnail exists for this slide so use this one
			if((typeof(self._visibleSlidesData[key].thumbSrc) !== 'undefined') && (self._visibleSlidesData[key].thumbSrc.length > 0)) {
				thumbnailImage.attr('src', self._getPath(self._visibleSlidesData[key].thumbSrc, 'slides'));
			}
			// use the image of the slide
			else {
				thumbnailImage.attr('src', self._getPath(self._visibleSlidesData[key].src, 'slides'));
			}

			thumbnail.html(thumbnailImage);

			thumbnail.on({
				"mouseenter.thumbnailsNavigationComponent": function(){
					if(!$(this).hasClass('selected'))
						if(options.thumbnailsNavigationComponent.thumbnail.animateStyles === true) {
							$(this).stop(true, true);
							$(this).animate(
								self._CSSAnimate(options.thumbnailsNavigationComponent.thumbnail.hoverStyle),
								self._getInt(options.thumbnailsNavigationComponent.thumbnail.animateStyleDuration, 500),
								function() {$(this).css(self._CSSDoNotAnimate(options.thumbnailsNavigationComponent.thumbnail.hoverStyle));}
							);
						}
						else
							$(this).css(self._CSSParseUrls(options.thumbnailsNavigationComponent.thumbnail.hoverStyle));
				},
				"mouseleave.thumbnailsNavigationComponent": function() {
					if(!$(this).hasClass('selected'))
						if(options.thumbnailsNavigationComponent.thumbnail.animateStyles === true) {
							$(this).stop(true, true);
							$(this).animate(
								self._CSSAnimate(options.thumbnailsNavigationComponent.thumbnail.style),
								self._getInt(options.thumbnailsNavigationComponent.thumbnail.animateStyleDuration, 500),
								function() {$(this).css(self._CSSDoNotAnimate(options.thumbnailsNavigationComponent.thumbnail.style));}
							);
						}
						else
							$(this).css(self._CSSParseUrls(options.thumbnailsNavigationComponent.thumbnail.style));
				},
				"click.thumbnailsNavigationComponent": function() {
					var slideIndex = self._getInt($(this).attr('id').substr(10));

					self._trigger(".navclick", null);

					self._trigger(".gotoslide", null, {
						'slideIndex': slideIndex,
						'dispatcher': 'thumbnail',
						'scrollThumbnailsNavigationComponent': false
					});
				}
			});

			// if the bindTouchEvents option is set to true than bind these events
			if(options.bindTouchEvents === true) thumbnail.TouchSupport({stopPropagation: false, tap: function(e, t) {t.click();}});

			if(options.thumbnailsNavigationComponent.orientation === 'vertical')
				thumbnail.css({ 'top': key * thumbnailHeight, 'left': 0 });
			else
				thumbnail.css({ 'top': 0, 'left': key * thumbnailWidth });

			thumbnails.push(thumbnail.get(0));
		}
		thumbnailsNavigationComponentContent.append(thumbnails);

		// calculate and set the size of the component
		self._calculateThumbnailsNavigationComponentSize();

		// calculate and set the position of the component
		self._calculateComponentPosition(self._thumbnailsNavigationComponent, options.thumbnailsNavigationComponent);

		self._thumbnailsNavigationComponent.hide();

		switch(options.thumbnailsNavigationComponent.availability) {
			case 'mouseover':
				self._sliderContainer.on({
					"mouseenter.thumbnailsNavigationComponent": function(event) {
						if(self._isThumbnailsNavigationComponentVisible == false) {
							self._isThumbnailsNavigationComponentVisible = true;
							self._runStageEffectOnComponent(self._thumbnailsNavigationComponent, options.thumbnailsNavigationComponent.stageDisplayHideEffect, self._getInt(options.thumbnailsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					},
					"mouseleave.thumbnailsNavigationComponent": function() {
						if(self._isThumbnailsNavigationComponentVisible == true) {
							self._isThumbnailsNavigationComponentVisible = false;
							self._runStageEffectOnComponent(self._thumbnailsNavigationComponent, options.thumbnailsNavigationComponent.stageDisplayHideEffect, self._getInt(options.thumbnailsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					}
				});
				break;
			case 'always':
			case 'default':
				if(self._isThumbnailsNavigationComponentVisible == false) {
					self._isThumbnailsNavigationComponentVisible = true;
					// run the stage display effect with a little delay if the availability is always
					setTimeout(function(){
						self._runStageEffectOnComponent(self._thumbnailsNavigationComponent, options.thumbnailsNavigationComponent.stageDisplayHideEffect, self._getInt(options.thumbnailsNavigationComponent.stageDisplayHideEffectDuration, 500));
					}, 10);
				}
		}

		return true;
	},

	_markSelectedThumbnail: function(scrollNavigationComponent) {
		var self = this;
		var options = self.options;

		if(typeof(scrollNavigationComponent) === 'undefined') scrollNavigationComponent = false;

		$('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).addClass('selected');

		if(options.thumbnailsNavigationComponent.thumbnail.animateStyles === true) {
			$('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).stop(true);
			$('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).animate(
				self._CSSAnimate(options.thumbnailsNavigationComponent.thumbnail.selectedStyle),
				self._getInt(options.thumbnailsNavigationComponent.thumbnail.animateStyleDuration, 500),
				function() {$(this).css(self._CSSDoNotAnimate(options.thumbnailsNavigationComponent.thumbnail.selectedStyle));}
			);
		}
		else
			$('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).css(self._CSSParseUrls(options.thumbnailsNavigationComponent.thumbnail.selectedStyle));

		$('.thumbnail', self._thumbnailsNavigationComponent).each(function(){
			if($(this).attr('id') !== 'thumbnail-' + self._currentSlideIndex) {
				$(this).removeClass('selected');

				if(options.thumbnailsNavigationComponent.thumbnail.animateStyles === true) {
					$(this).stop(true);
					$(this).animate(
						self._CSSAnimate(options.thumbnailsNavigationComponent.thumbnail.style),
						self._getInt(options.thumbnailsNavigationComponent.thumbnail.animateStyleDuration, 500),
						function() {$(this).css(self._CSSDoNotAnimate(options.thumbnailsNavigationComponent.thumbnail.style));}
					);
				}
				else
					$(this).css(self._CSSParseUrls(options.thumbnailsNavigationComponent.thumbnail.style));
			}
		});

		if(!scrollNavigationComponent) return;

		var position = 0;
		switch(options.thumbnailsNavigationComponent.orientation) {
			case 'vertical':
				position = self._getInt($('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).css('top')) + Math.round(self._getOuterHeight($('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).first(), true) / 2);
				break;
			case 'horizontal':
			default:
				position = self._getInt($('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).css('left')) + Math.round(self._getOuterWidth($('#thumbnail-' + self._currentSlideIndex, self._thumbnailsNavigationComponent).first(), true) / 2);
		}

		if(self._thumbnailsNavigationComponent.hasClass('SmoothScroller'))
			self._thumbnailsNavigationComponent.SmoothScroller('centerPosition', position);

		return true;
	},

	_calculateThumbnailsNavigationComponentSize: function() {
		var self = this;
		var options = self.options;

		var thumbnail = $('.thumbnail', self._thumbnailsNavigationComponent).first();

		var thumbnailWidth = self._getOuterWidth(thumbnail, true);
		var thumbnailHeight = self._getOuterHeight(thumbnail, true);

		// calculate the total dimensions of the arrows (including margins)
		var thumbnailsNavigationComponentWidth = 0;
		var thumbnailsNavigationComponentHeight = 0;

		var thumbnailsNavigationComponentContentWidth = 0;
		var thumbnailsNavigationComponentContentHeight = 0;

		// calculate minimum component dimensions taking orientation into account
		switch(options.thumbnailsNavigationComponent.orientation) {
			case 'vertical':
				thumbnailsNavigationComponentWidth = thumbnailWidth;
				thumbnailsNavigationComponentHeight = 3 * thumbnailHeight;

				thumbnailsNavigationComponentContentWidth = thumbnailWidth;
				thumbnailsNavigationComponentContentHeight = self._visibleSlidesData.length * thumbnailHeight;
				break;
			case 'horizontal':
			default:
				thumbnailsNavigationComponentWidth = 3 * thumbnailWidth;
				thumbnailsNavigationComponentHeight = thumbnailHeight;

				thumbnailsNavigationComponentContentWidth = self._visibleSlidesData.length * thumbnailWidth;
				thumbnailsNavigationComponentContentHeight = thumbnailHeight;
		}

		switch(options.thumbnailsNavigationComponent.relativeToSlides) {
			case false:
				thumbnailsNavigationComponentWidth = Math.max(thumbnailsNavigationComponentWidth, self._getInt(options.thumbnailsNavigationComponent.width));
				thumbnailsNavigationComponentHeight = Math.max(thumbnailsNavigationComponentHeight, self._getInt(options.thumbnailsNavigationComponent.height));
				break;
			case true:
			default:
				if(options.thumbnailsNavigationComponent.orientation === 'horizontal') thumbnailsNavigationComponentWidth = self._getInt(options.width);
				if(options.thumbnailsNavigationComponent.orientation === 'vertical') thumbnailsNavigationComponentHeight = self._getInt(options.height);
		}

		self._thumbnailsNavigationComponent.css({
			'width': 	thumbnailsNavigationComponentWidth,
			'height': 	thumbnailsNavigationComponentHeight
		});

		self._thumbnailsNavigationComponent.children(':first').css({
			'width': 	thumbnailsNavigationComponentContentWidth,
			'height': 	thumbnailsNavigationComponentContentHeight
		});

		return true;
	},

	_runStageEffectOnComponent: function(component, effect, duration) {
		var self = this;
		var options = self.options;

		component.stop(true, true);

		switch (effect) {
			case 'fade':
				component.toggle('fade', {}, duration);
				break;
			case 'slideup':
				component.toggle('slide', {direction: "down"}, duration);
				break;
			case 'slidedown':
				component.toggle('slide', {direction: "up"}, duration);
		}

		return true;
	},

	/*** ARROWS NAVIGATION COMPONENT ***/

	createArrowsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		if(options.arrowsNavigationComponent.available === true) {
			// generate the component if it doesn't already exist
			if(self._arrowsNavigationComponent === null) {
				self._generateArrowsNavigationComponent();

				// add the component to the slider
				self._addComponentToTheSliderContainer(self._arrowsNavigationComponent);

				// update the component
				self._updateArrowsNavigationComponentState();

				return true;
			}
		}

		return false;
	},

	destroyArrowsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		if(self._arrowsNavigationComponent !== null) {
			self._isArrowsNavigationComponentVisible = false;
			self._isPrevArrowVisible = false;
			self._isNextArrowVisible = false;
			self._sliderContainer.off(".arrowsNavigationComponent");

			$('.arrowsNavigationComponent *', self._sliderContainer).off();
			$('.arrowsNavigationComponent', self._sliderContainer).off();
			$('.arrowsNavigationComponent', self._sliderContainer).remove();
			self._arrowsNavigationComponent = null;

			self._updateSliderContainerSize();

			return true;
		}

		return false;
	},

	_generateArrowsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		self._writeConsoleMessage('Generating Arrows-Navigation-Component!');

		// create the arrowsComponent
		self._arrowsNavigationComponent = $('<div></div>').addClass('arrowsNavigationComponent');
		self._arrowsNavigationComponent.css(self._CSSParseUrls(options.arrowsNavigationComponent.style));

		// create the previous arrow
		var prevArrowHitarea = $('<div></div>')
			.addClass('prevArrowHitarea')
			.css({
				'width': 	self._getInt(options.arrowsNavigationComponent.prevArrow.hitarea.width),
				'height': 	self._getInt(options.arrowsNavigationComponent.prevArrow.hitarea.height)
			})
			.css(self._CSSParseUrls(options.arrowsNavigationComponent.prevArrow.hitarea.style))
			.css({
				'top': 	0,
				'left': 0
			});

		if(options.arrowsNavigationComponent.prevArrow.arrow.available === true) {
			var prevArrowImage = $('<img />')
				.attr('src', self._getPath(options.arrowsNavigationComponent.prevArrow.arrow.imageSrc, 'arrows'))
				.addClass('prevArrowImage')
				.css({
					'width': 	self._getInt(options.arrowsNavigationComponent.prevArrow.arrow.width),
					'height': 	self._getInt(options.arrowsNavigationComponent.prevArrow.arrow.height)
				})
				.css(self._CSSParseUrls(options.arrowsNavigationComponent.prevArrow.arrow.style))
				.css({
					'top': 	0,
					'left': 0
				});

			prevArrowHitarea.append(prevArrowImage);

			prevArrowImage.css(self._calculatePosition(prevArrowImage, prevArrowHitarea, 'center', 'center'));
		}

		// create the next arrow
		var nextArrowHitarea = $('<div></div>')
			.addClass('nextArrowHitarea')
			.css({
				'width': 	self._getInt(options.arrowsNavigationComponent.nextArrow.hitarea.width),
				'height': 	self._getInt(options.arrowsNavigationComponent.nextArrow.hitarea.height)
			})
			.css(self._CSSParseUrls(options.arrowsNavigationComponent.nextArrow.hitarea.style))
			.css({
				'bottom': 	0,
				'right': 	0
			});

		if(options.arrowsNavigationComponent.nextArrow.arrow.available === true) {
			var nextArrowImage = $('<img />')
				.attr('src', self._getPath(options.arrowsNavigationComponent.nextArrow.arrow.imageSrc, 'arrows'))
				.addClass('nextArrowImage')
				.css({
					'width': 	self._getInt(options.arrowsNavigationComponent.nextArrow.arrow.width),
					'height': 	self._getInt(options.arrowsNavigationComponent.nextArrow.arrow.height)
				})
				.css(self._CSSParseUrls(options.arrowsNavigationComponent.nextArrow.arrow.style))
				.css({
					'bottom': 	0,
					'right': 	0
				});

			nextArrowHitarea.append(nextArrowImage);

			nextArrowImage.css(self._calculatePosition(nextArrowImage, nextArrowHitarea, 'center', 'center'));
		}

		// append the arrows to the component
		self._arrowsNavigationComponent.append([prevArrowHitarea.get(0), nextArrowHitarea.get(0)]);

		// calculate and set the size of the component
		self._calculateArrowsNavigationComponentSize();

		// calculate and set the position of the component
		self._calculateComponentPosition(self._arrowsNavigationComponent, self.options.arrowsNavigationComponent);

		// add click eventHandler for prevArrowHitarea and mouseenter and mouseleave eventHandlers for prevArrowImage
		prevArrowHitarea.on({
			"click.arrowsNavigationComponent": function() {
				self._writeConsoleMessage('Previous-Arrow was clicked!');
				if(!prevArrowHitarea.hasClass('disabled')) self._goToPrevSlide();

				self._trigger(".prevarwclick", null);
			},
			"mouseenter.arrowsNavigationComponent": function() {
				$(this).stop(true, true);
				$(this).children().stop(true, true);
				if(!prevArrowImage.hasClass('disabled')) {
					self._runHoverEffectOnPreviousNextArrows('mouseenter', options.arrowsNavigationComponent.arrowHoverEffect, prevArrowHitarea);
				}
			},
			"mouseleave.arrowsNavigationComponent": function() {
				$(this).stop(true, true);
				$(this).children().stop(true, true);
				if(!prevArrowHitarea.hasClass('disabled'))
					self._runHoverEffectOnPreviousNextArrows('mouseleave', options.arrowsNavigationComponent.arrowHoverEffect, prevArrowHitarea);
			}
		});

		// add click eventHandler for nextArrowHitarea and mouseenter and mouseleave eventHandlers for nextArrowImage
		nextArrowHitarea.on({
			"click.arrowsNavigationComponent": function() {
				self._writeConsoleMessage('Next-Arrow was clicked!');
				if(!nextArrowHitarea.hasClass('disabled')) self._goToNextSlide();

				self._trigger(".nextarwclick", null);
			},
			"mouseenter.arrowsNavigationComponent": function() {
				$(this).stop(true, true);
				$(this).children().stop(true, true);
				if(!nextArrowImage.hasClass('disabled')) {
					self._runHoverEffectOnPreviousNextArrows('mouseenter', options.arrowsNavigationComponent.arrowHoverEffect, nextArrowHitarea);
				}
			},
			"mouseleave.arrowsNavigationComponent": function() {
				$(this).stop(true, true);
				$(this).children().stop(true, true);
				if(!nextArrowImage.hasClass('disabled'))
					self._runHoverEffectOnPreviousNextArrows('mouseleave', options.arrowsNavigationComponent.arrowHoverEffect, nextArrowHitarea);
			}
		});

		// if the bindTouchEvents option is set to true than bind these events
		if(options.bindTouchEvents === true) {
			prevArrowHitarea.TouchSupport({stopPropagation: false, tap: function(e, t) {t.click();} });
			nextArrowHitarea.TouchSupport({stopPropagation: false, tap: function(e, t) {t.click();} });
		}

		switch(options.arrowsNavigationComponent.availability) {
			case 'mouseoverslider':
				self._arrowsNavigationComponent.hide();

				self._sliderContainer.on({
					"mouseenter.arrowsNavigationComponent": function() {
						if(self._isArrowsNavigationComponentVisible == false) {
							self._isArrowsNavigationComponentVisible = true;
							self._runStageEffectOnComponent(self._arrowsNavigationComponent, options.arrowsNavigationComponent.stageDisplayHideEffect, self._getInt(options.arrowsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					},
					"mouseleave.arrowsNavigationComponent": function() {
						if(self._isArrowsNavigationComponentVisible == true) {
							self._isArrowsNavigationComponentVisible = false;
							self._runStageEffectOnComponent(self._arrowsNavigationComponent, options.arrowsNavigationComponent.stageDisplayHideEffect, self._getInt(options.arrowsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					}
				});
				break;
			case 'mouseoverhitarea':
				prevArrowImage.hide();
				nextArrowImage.hide();

				prevArrowHitarea.on({
					"mouseenter.arrowsNavigationComponent": function() {
						if(self._isPrevArrowVisible == false) {
							self._isPrevArrowVisible = true;
							self._runStageEffectOnComponent(prevArrowImage, options.arrowsNavigationComponent.stageDisplayHideEffect, self._getInt(options.arrowsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					},
					"mouseleave.arrowsNavigationComponent": function() {
						if(self._isPrevArrowVisible == true) {
							self._isPrevArrowVisible = false;
							self._runStageEffectOnComponent(prevArrowImage, options.arrowsNavigationComponent.stageDisplayHideEffect, self._getInt(options.arrowsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					}
				});

				nextArrowHitarea.on({
					"mouseenter.arrowsNavigationComponent": function() {
						if(self._isNextArrowVisible == false) {
							self._isNextArrowVisible = true;
							self._runStageEffectOnComponent(nextArrowImage, options.arrowsNavigationComponent.stageDisplayHideEffect, self._getInt(options.arrowsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					},
					"mouseleave.arrowsNavigationComponent": function() {
						if(self._isNextArrowVisible == true) {
							self._isNextArrowVisible = false;
							self._runStageEffectOnComponent(nextArrowImage, options.arrowsNavigationComponent.stageDisplayHideEffect, self._getInt(options.arrowsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					}
				});
				break;
			case 'always':
			case 'default':
				self._arrowsNavigationComponent.hide();
				self._isArrowsNavigationComponentVisible = true;
				self._isPrevArrowVisible = true;
				self._isNextArrowVisible = true;
				self._runStageEffectOnComponent(self._arrowsNavigationComponent, options.arrowsNavigationComponent.stageDisplayHideEffect, self._getInt(options.arrowsNavigationComponent.stageDisplayHideEffectDuration, 500));
		}

		return true;
	},

	_calculateArrowsNavigationComponentSize: function() {
		var self = this;
		var options = self.options;

		// calculate the total dimensions of the arrows (including margins)
		var prevArrow = $('.prevArrowHitarea', self._arrowsNavigationComponent).first();
		var nextArrow = $('.nextArrowHitarea', self._arrowsNavigationComponent).first();

		var prevArrowWidth		= self._getOuterWidth(prevArrow, true);
		var prevArrowHeight 	= self._getOuterHeight(prevArrow, true);
		var nextArrowWidth		= self._getOuterWidth(nextArrow, true);
		var nextArrowHeight 	= self._getOuterHeight(nextArrow, true);

		var arrowsNavigationComponentWidth = 0;
		var arrowsNavigationComponentHeight = 0;

		// calculate minimum component dimensions taking orientation into account
		switch(options.arrowsNavigationComponent.orientation) {
			case 'vertical':
				arrowsNavigationComponentWidth = Math.max(prevArrowWidth, nextArrowWidth);
				arrowsNavigationComponentHeight = prevArrowHeight + nextArrowHeight;
				break;
			case 'horizontal':
			default:
				arrowsNavigationComponentWidth = prevArrowWidth + nextArrowWidth;
				arrowsNavigationComponentHeight = Math.max(prevArrowHeight, nextArrowHeight);
		}

		switch(options.arrowsNavigationComponent.relativeToSlides) {
			case false:
				arrowsNavigationComponentWidth = Math.max(arrowsNavigationComponentWidth, self._getInt(options.arrowsNavigationComponent.width));
				arrowsNavigationComponentHeight = Math.max(arrowsNavigationComponentHeight, self._getInt(options.arrowsNavigationComponent.height));
				break;
			case true:
			default:
				if(options.arrowsNavigationComponent.orientation === 'horizontal') arrowsNavigationComponentWidth = self._getInt(options.width);
				if(options.arrowsNavigationComponent.orientation === 'vertical') arrowsNavigationComponentHeight = self._getInt(options.height);
		}

		self._arrowsNavigationComponent.css({
			'width': 	arrowsNavigationComponentWidth,
			'height': 	arrowsNavigationComponentHeight
		});
		
		return true;
	},

	_calculateComponentPosition: function(component, componentOptions) {
		var self = this;
		var options = self.options;

		var position = { 'top': 0, 'left': 0 };

		// check if we need to position the component relative to the slides container
		if(typeof(componentOptions.relativeToSlides) !== 'undefined' && componentOptions.relativeToSlides === true) {
			// calculate the vertical position
			var verticalCoordinate = 0;
			switch(componentOptions.relativeToSlidesVertical) {
				case 'top':
					verticalCoordinate = self._getInt(self._slidesContainer.css('top'));
					break;
				case 'center':
					verticalCoordinate = self._getInt(self._slidesContainer.css('top')) + Math.round((self._getOuterHeight(self._slidesContainer) / 2) - (self._getOuterHeight(component, true) / 2));
					break;
				case 'bottom':
				default:
					verticalCoordinate = self._getInt(self._slidesContainer.css('top')) + self._getOuterHeight(self._slidesContainer) - self._getOuterHeight(component, true);
			}
			position.top = verticalCoordinate > 0 ? verticalCoordinate : 0;

			// calculate the horizontal position
			var horizontalCoordinate = 0;
			switch(componentOptions.relativeToSlidesHorizontal) {
				case 'left':
					horizontalCoordinate = self._getInt(self._slidesContainer.css('left'));
					break;
				case 'center':
					horizontalCoordinate = self._getInt(self._slidesContainer.css('left')) + Math.round((self._getOuterWidth(self._slidesContainer) / 2) - (self._getOuterWidth(component, true) / 2));
					break;
				case 'right':
				default:
					horizontalCoordinate = self._getInt(self._slidesContainer.css('left')) + self._getOuterWidth(self._slidesContainer) - self._getOuterWidth(component, true);
			}
			position.left = horizontalCoordinate > 0 ? horizontalCoordinate : 0;
		}
		else {
			position.top = self._getInt(componentOptions.top);
			position.left = self._getInt(componentOptions.left);
		}

		component.css(position);

		return position;
	},

	_calculatePosition: function(component, referenceComponent, verticalPosition, horizontalPosition) {
		var self = this;
		var options = self.options;

		var position = { 'top': 0, 'left': 0 };

		// calculate the vertical position
		var verticalCoordinate = 0;
		switch(verticalPosition) {
			case 'top':
				verticalCoordinate = self._getInt(referenceComponent.css('top'));
				break;
			case 'center':
				verticalCoordinate = self._getInt(referenceComponent.css('top')) + Math.round((self._getOuterHeight(referenceComponent) / 2) - (self._getOuterHeight(component, true) / 2));
				break;
			case 'bottom':
				verticalCoordinate = self._getInt(referenceComponent.css('top')) + self._getOuterHeight(referenceComponent) - self._getOuterHeight(component, true);
		}
		position.top = verticalCoordinate > 0 ? verticalCoordinate : 0;

		// calculate the horizontal position
		var horizontalCoordinate = 0;
		switch(horizontalPosition) {
			case 'left':
				horizontalCoordinate = self._getInt(referenceComponent.css('left'));
				break;
			case 'center':
				horizontalCoordinate = self._getInt(referenceComponent.css('left')) + Math.round((self._getOuterWidth(referenceComponent) / 2) - (self._getOuterWidth(component, true) / 2));
				break;
			case 'right':
				horizontalCoordinate = self._getInt(referenceComponent.css('left')) + self._getOuterWidth(referenceComponent) - self._getOuterWidth(component, true);
		}
		position.left = horizontalCoordinate > 0 ? horizontalCoordinate : 0;

		component.css(position);

		return position;
	},

	_updateArrowsNavigationComponentState: function() {
		var self = this;
		var options = self.options;

		if(options.arrowsNavigationComponent.available === false)
			return false;

		switch(options.arrowsNavigationComponent.whenArrowUnavailable) {
			case 'showDisabled':
				if (!self._prevSlideExists()) {
					$('.prevArrowHitarea', self._arrowsNavigationComponent).addClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.prevArrow.hitarea.disabledStyle));
					$('.prevArrowImage', self._arrowsNavigationComponent).addClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.prevArrow.arrow.disabledStyle));
				}
				else {
					$('.prevArrowHitarea', self._arrowsNavigationComponent).removeClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.prevArrow.hitarea.style));
					$('.prevArrowImage', self._arrowsNavigationComponent).removeClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.prevArrow.arrow.style));
				}

				if (!self._nextSlideExists()) {
					$('.nextArrowHitarea', self._arrowsNavigationComponent).addClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.nextArrow.hitarea.disabledStyle));
					$('.nextArrowImage', self._arrowsNavigationComponent).addClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.nextArrow.arrow.disabledStyle));
				}
				else {
					$('.nextArrowHitarea', self._arrowsNavigationComponent).removeClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.nextArrow.hitarea.style));
					$('.nextArrowImage', self._arrowsNavigationComponent).removeClass('disabled').css(self._CSSParseUrls(options.arrowsNavigationComponent.nextArrow.arrow.style));
				}
				break;
			case 'hide':
			default:
				(!self._prevSlideExists()) ? $('.prevArrowHitarea', self._arrowsNavigationComponent).hide() : $('.prevArrowHitarea', self._arrowsNavigationComponent).show();
				(!self._nextSlideExists()) ? $('.nextArrowHitarea', self._arrowsNavigationComponent).hide() : $('.nextArrowHitarea', self._arrowsNavigationComponent).show();
		}

		return true;
	},

	_runHoverEffectOnPreviousNextArrows: function(action, effect, arrow) {
		var self = this;
		var options = self.options;

		var arrowImage;
		if(arrow.hasClass("prevArrowHitarea")) {
			arrowOptions = options.arrowsNavigationComponent.prevArrow;
			arrowImage = $('.prevArrowImage', arrow);
		}
		else {
			arrowOptions = options.arrowsNavigationComponent.nextArrow;
			arrowImage = $('.nextArrowImage', arrow);
		}

		arrow.stop(true, true);

		switch (effect) {
			case 'image':
				switch (action) {
					case 'mouseenter':
						// the arrow should be the first child of the hitarea
						arrow.children(':first').attr("src", self._getPath(arrowOptions.arrow.imageHoverSrc, 'arrows'));
						break;
					case 'mouseleave':
					default:
						// the arrow should be the first child of the hitarea
						arrow.children(':first').attr("src", self._getPath(arrowOptions.arrow.imageSrc, 'arrows'));
				}
				break;
			case 'css':
			default:
				switch (action) {
					case 'mouseenter':
						arrow.animate(
							self._CSSAnimate(arrowOptions.hitarea.hoverStyle),
							self._getInt(options.arrowsNavigationComponent.arrowHoverEffectDuration, 500),
							function() {$(this).css(self._CSSDoNotAnimate(arrowOptions.hitarea.hoverStyle));}
						);
						arrowImage.animate(
							self._CSSAnimate(arrowOptions.arrow.hoverStyle),
							self._getInt(options.arrowsNavigationComponent.arrowHoverEffectDuration, 500),
							function() {$(this).css(self._CSSDoNotAnimate(arrowOptions.arrow.hoverStyle));}
						);
						break;
					case 'mouseleave':
					default:
						arrow.animate(
							self._CSSAnimate(arrowOptions.hitarea.style),
							self._getInt(options.arrowsNavigationComponent.arrowHoverEffectDuration, 500),
							function() {$(this).css(self._CSSDoNotAnimate(arrowOptions.hitarea.style));}
						);
						arrowImage.animate(
							self._CSSAnimate(arrowOptions.arrow.style),
							self._getInt(options.arrowsNavigationComponent.arrowHoverEffectDuration, 500),
							function() {$(this).css(self._CSSDoNotAnimate(arrowOptions.arrow.style));}
						);
				}
		}

		return true;
	},

	/*** ANCHORS NAVIGATION COMPONENT ***/

	createAnchorsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		if(options.anchorsNavigationComponent.available === true) {
			// generate the component if it doesn't already exist
			if(self._anchorsNavigationComponent === null) {
				self._generateAnchorsNavigationComponent();

				// add the component to the slider
				self._addComponentToTheSliderContainer(self._anchorsNavigationComponent);

				// after the thumbnail loads calculate its final size and position with bestfit option
				$('img.anchor-image', self._anchorsNavigationComponent).ImageLoaded(function(width, height){
					var sizeAndPosition = self._calculateImageSizeAndPosition(options.anchorsNavigationComponent.anchor.width, options.anchorsNavigationComponent.anchor.height, width, height, 'bestfit');
					$(this).css(sizeAndPosition);
				});

				self._anchorsNavigationComponent.SmoothScroller({orientation: options.anchorsNavigationComponent.orientation, contentId: 'anchorsNavigationComponentContent'});

				// if the bindTouchEvents option is set to true than bind these events
				if(options.bindTouchEvents === true) {
					if(options.anchorsNavigationComponent.orientation === 'vertical')
						self._anchorsNavigationComponent.TouchSupport({
							scrollV: function(e, t, offset) {self._anchorsNavigationComponent.SmoothScroller('modifyPosition', -offset);},
							momentumV: function(e, t, offset, time) {self._anchorsNavigationComponent.SmoothScroller('modifyPosition', -offset, time);}
						});
					else
						self._anchorsNavigationComponent.TouchSupport({
							scrollH: function(e, t, offset) {self._anchorsNavigationComponent.SmoothScroller('modifyPosition', -offset);},
							momentumH: function(e, t, offset, time) {self._anchorsNavigationComponent.SmoothScroller('modifyPosition', -offset, time);}
						});
				}

				self._markSelectedAnchor(true);

				return true;
			}
		}

		return false;
	},

	destroyAnchorsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		if(self._anchorsNavigationComponent !== null) {
			self._isAnchorsNavigationComponentVisible = false;
			self._anchorsNavigationComponent.SmoothScroller('destroy');

			self._sliderContainer.off(".anchorsNavigationComponent");

			$('.anchorsNavigationComponent *', self._sliderContainer).off();
			$('.anchorsNavigationComponent', self._sliderContainer).off();
			$('.anchorsNavigationComponent', self._sliderContainer).remove();
			self._anchorsNavigationComponent = null;

			self._updateSliderContainerSize();

			return true;
		}

		return false;
	},

	_generateAnchorsNavigationComponent: function() {
		var self = this;
		var options = self.options;

		self._writeConsoleMessage('Generating Anchors-Navigation-Component!');

		// create the navigationComponent
		self._anchorsNavigationComponent = $('<div></div>').addClass('anchorsNavigationComponent');
		self._anchorsNavigationComponent.css(self._CSSParseUrls(options.anchorsNavigationComponent.style));

		var anchorsNavigationComponentContent = $('<div></div>').addClass('anchorsNavigationComponentContent').attr('id', 'anchorsNavigationComponentContent');

		self._anchorsNavigationComponent.append(anchorsNavigationComponentContent);

		// create an anchor to get the anchor width and anchor height
		var anchor = $('<div></div>')
			.css({
				'width': 	self._getInt(options.anchorsNavigationComponent.anchor.width),
				'height': 	self._getInt(options.anchorsNavigationComponent.anchor.height)
			})
			.css(self._CSSParseUrls(options.anchorsNavigationComponent.anchor.style));

		anchor.appendTo(document);
		var anchorWidth = self._getOuterWidth(anchor, true);
		var anchorHeight = self._getOuterHeight(anchor, true);
		anchor.remove();

		// destroy the anchor object as we don't need it anymore
		anchor = null;

		var anchors = [];
		for (var key in self._visibleSlidesData) {
			var anchor = $('<div></div>')
				.addClass('anchor')
				.attr('id', 'anchor-' + key)
				.css({
					'width': 	self._getInt(options.anchorsNavigationComponent.anchor.width),
					'height': 	self._getInt(options.anchorsNavigationComponent.anchor.height)
				})
				.css(self._CSSParseUrls(options.anchorsNavigationComponent.anchor.style));

			var anchorImage = $('<img />')
				.attr('id', 'anchor-image-' + key)
				.addClass('anchor-image')
				.css({
					'width': 	self._getInt(options.anchorsNavigationComponent.anchor.width),
					'height': 	self._getInt(options.anchorsNavigationComponent.anchor.height)
				});

			anchor.append(anchorImage);

			// the anchors are using the same image with multiple states options
			anchorImage.attr('src', self._getPath(options.anchorsNavigationComponent.anchor.anchorImageNormalSrc, 'anchors'));
			anchor.on({
				"mouseenter.anchorsNavigationComponent": function() { if(!$(this).hasClass('selected')) $(this).find('img').attr('src', self._getPath(options.anchorsNavigationComponent.anchor.anchorImageHoverSrc, 'anchors')); },
				"mouseleave.anchorsNavigationComponent": function() { if(!$(this).hasClass('selected')) $(this).find('img').attr('src', self._getPath(options.anchorsNavigationComponent.anchor.anchorImageNormalSrc, 'anchors')); }
			});

			anchor.css(self._CSSParseUrls(options.anchorsNavigationComponent.anchor.style));

			anchor.on({
				"mouseenter.anchorsNavigationComponent": function() {
					if(!$(this).hasClass('selected'))
						if(options.anchorsNavigationComponent.anchor.animateStyles === true) {
							$(this).stop(true);
							$(this).animate(
								self._CSSAnimate(options.anchorsNavigationComponent.anchor.hoverStyle),
								self._getInt(options.anchorsNavigationComponent.anchor.animateStyleDuration, 500),
								function() {$(this).css(self._CSSDoNotAnimate(options.anchorsNavigationComponent.anchor.hoverStyle));}
							);
						}
						else $(this).css(self._CSSParseUrls(options.anchorsNavigationComponent.anchor.hoverStyle));
				},
				"mouseleave.anchorsNavigationComponent": function() {
					if(!$(this).hasClass('selected'))
						if(options.anchorsNavigationComponent.anchor.animateStyles === true) {
							//$(this).stop(true);
							$(this).animate(
								self._CSSAnimate(options.anchorsNavigationComponent.anchor.style),
								self._getInt(options.anchorsNavigationComponent.anchor.animateStyleDuration, 500),
								function() {$(this).css(self._CSSDoNotAnimate(options.anchorsNavigationComponent.anchor.style));}
							);
						}
						else $(this).css(self._CSSParseUrls(options.anchorsNavigationComponent.anchor.style));
				},
				"click.anchorsNavigationComponent": function() {
					var slideIndex = self._getInt($(this).attr('id').substr(7));

					self._trigger(".navclick", null);

					self._trigger(".gotoslide", null, {
						'slideIndex': slideIndex,
						'dispatcher': 'anchor',
						'scrollAnchorsNavigationComponent': false
					});
				}
			});

			// if the bindTouchEvents option is set to true than bind these events
			if(options.bindTouchEvents === true) anchor.TouchSupport({stopPropagation: false, tap: function(e, t) {t.click();}});

			if(options.anchorsNavigationComponent.orientation === 'vertical')
				anchor.css({ 'top': key * anchorHeight, 'left': 0 });
			else
				anchor.css({ 'top': 0, 'left': key * anchorWidth });

			anchors.push(anchor.get(0));
		}

		anchorsNavigationComponentContent.append(anchors);

		// calculate and set the size of the component
		self._calculateAnchorsNavigationComponentSize();

		// calculate and set the position of the component
		self._calculateComponentPosition(self._anchorsNavigationComponent, options.anchorsNavigationComponent);

		self._anchorsNavigationComponent.hide();

		switch(options.anchorsNavigationComponent.availability) {
			case 'mouseover':
				self._sliderContainer.on({
					"mouseenter.anchorsNavigationComponent": function() {
						if(self._isAnchorsNavigationComponentVisible == false) {
							self._isAnchorsNavigationComponentVisible = true;
							self._runStageEffectOnComponent(self._anchorsNavigationComponent, options.anchorsNavigationComponent.stageDisplayHideEffect, self._getInt(options.anchorsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					},
					"mouseleave.anchorsNavigationComponent": function() {
						if(self._isAnchorsNavigationComponentVisible == true) {
							self._isAnchorsNavigationComponentVisible = false;
							self._runStageEffectOnComponent(self._anchorsNavigationComponent, options.anchorsNavigationComponent.stageDisplayHideEffect, self._getInt(options.anchorsNavigationComponent.stageDisplayHideEffectDuration, 500));
						}
					}
				});
				break;
			case 'always':
			case 'default':
				if(self._isAnchorsNavigationComponentVisible == false) {
					self._isAnchorsNavigationComponentVisible = true;
					// run the stage display effect with a little delay if the availability is always
					setTimeout(function(){
						self._runStageEffectOnComponent(self._anchorsNavigationComponent, options.anchorsNavigationComponent.stageDisplayHideEffect, self._getInt(options.anchorsNavigationComponent.stageDisplayHideEffectDuration, 500));
					}, 10);
				}
		}

		return true;
	},

	_markSelectedAnchor: function(scrollAnchorsNavigationComponent) {
		var self = this;
		var options = self.options;

		if(typeof(scrollAnchorsNavigationComponent) === 'undefined') scrollAnchorsNavigationComponent = false;

		$('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).addClass('selected');

		if(options.anchorsNavigationComponent.anchor.animateStyles === true) {
			$('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).stop(true);
			$('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).animate(
				self._CSSAnimate(options.anchorsNavigationComponent.anchor.selectedStyle),
				self._getInt(options.anchorsNavigationComponent.anchor.animateStyleDuration, 500),
				function() {$(this).css(self._CSSDoNotAnimate(options.anchorsNavigationComponent.anchor.selectedStyle));}
			);
		}
		else
			$('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).css(self._CSSParseUrls(options.anchorsNavigationComponent.anchor.selectedStyle));

		$('.anchor', self._anchorsNavigationComponent).each(function(){
			if($(this).attr('id') !== 'anchor-' + self._currentSlideIndex) {
				$(this).removeClass('selected');

				if(options.anchorsNavigationComponent.anchor.animateStyles === true) {
					$(this).stop(true);
					$(this).animate(
						self._CSSAnimate(options.anchorsNavigationComponent.anchor.style),
						self._getInt(options.anchorsNavigationComponent.anchor.animateStyleDuration, 500),
						function() {$(this).css(self._CSSDoNotAnimate(options.anchorsNavigationComponent.anchor.style));}
					);
				}
				else
					$(this).css(self._CSSParseUrls(options.anchorsNavigationComponent.anchor.style));
			}
		});

		$('.anchor', self._anchorsNavigationComponent).each(function(){ $(this).find('img').attr('src', self._getPath(options.anchorsNavigationComponent.anchor.anchorImageNormalSrc, 'anchors')); });

		$('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).find('img').attr('src', self._getPath(options.anchorsNavigationComponent.anchor.anchorImageSelectedSrc, 'anchors'));

		if(!scrollAnchorsNavigationComponent) return;

		var position = 0;
		switch(options.anchorsNavigationComponent.orientation) {
			case 'vertical':
				position = self._getInt($('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).css('top')) + Math.round(self._getOuterHeight($('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).first(), true) / 2);
				break;
			case 'horizontal':
			default:
				position = self._getInt($('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).css('left')) + Math.round(self._getOuterWidth($('#anchor-' + self._currentSlideIndex, self._anchorsNavigationComponent).first(), true) / 2);
		}

		if(self._anchorsNavigationComponent.hasClass('SmoothScroller'))
			self._anchorsNavigationComponent.SmoothScroller('centerPosition', position);

		return true;
	},

	_calculateAnchorsNavigationComponentSize: function() {
		var self = this;
		var options = self.options;

		var anchor = $('.anchor', self._anchorsNavigationComponent).first();

		var anchorWidth = self._getOuterWidth(anchor, true);
		var anchorHeight = 	self._getOuterHeight(anchor, true);

		// calculate the total dimensions of the arrows (including margins)
		var anchorsNavigationComponentWidth = 0;
		varanchorsNavigationComponentHeight = 0;

		var anchorsNavigationComponentContentWidth = 0;
		var anchorsNavigationComponentContentHeight = 0;

		// calculate minimum component dimensions taking orientation into account
		switch(options.anchorsNavigationComponent.orientation) {
			case 'vertical':
				anchorsNavigationComponentWidth = anchorWidth;
				anchorsNavigationComponentHeight = 3 * anchorHeight;

				anchorsNavigationComponentContentWidth = anchorWidth;
				anchorsNavigationComponentContentHeight = self._visibleSlidesData.length * anchorHeight;
				break;
			case 'horizontal':
			default:
				anchorsNavigationComponentWidth = 3 * anchorWidth;
				anchorsNavigationComponentHeight = anchorHeight;

				anchorsNavigationComponentContentWidth = self._visibleSlidesData.length * anchorWidth;
				anchorsNavigationComponentContentHeight = anchorHeight;
		}

		switch(options.anchorsNavigationComponent.relativeToSlides) {
			case false:
				anchorsNavigationComponentWidth = Math.max(anchorsNavigationComponentWidth, self._getInt(options.anchorsNavigationComponent.width));
				anchorsNavigationComponentHeight = Math.max(anchorsNavigationComponentHeight, self._getInt(options.anchorsNavigationComponent.height));
				break;
			case true:
			default:
				if(options.anchorsNavigationComponent.orientation === 'horizontal') anchorsNavigationComponentWidth = self._getInt(options.width);
				if(options.anchorsNavigationComponent.orientation === 'vertical')anchorsNavigationComponentHeight = self._getInt(options.height);
		}

		self._anchorsNavigationComponent.css({
			'width': 	anchorsNavigationComponentWidth,
			'height': 	anchorsNavigationComponentHeight
		});

		self._anchorsNavigationComponent.children(':first').css({
			'width': 	anchorsNavigationComponentContentWidth,
			'height': 	anchorsNavigationComponentContentHeight
		});

		return true;
	},

	/*** SLIDE INFO COMPONENT ***/

	createSlideInfoComponent: function() {
		var self = this;
		var options = self.options;

		if(options.slideInfoComponent.available === true) {
			// generate the component if it doesn't already exist
			if(self._slideInfoComponent === null) {
				self._generateSlideInfoComponent();

				// add the component to the slider
				self._addComponentToTheSliderContainer(self._slideInfoComponent);

				self._showSlideInfo(self._currentSlideIndex);

				return true;
			}
		}

		return false;
	},

	destroySlideInfoComponent: function() {
		var self = this;
		var options = self.options;

		if(self._slideInfoComponent !== null) {
			self._sliderContainer.off(".slideInfoComponent");

			$('.slideInfoComponent *', self._sliderContainer).off();
			$('.slideInfoComponent', self._sliderContainer).off();
			$('.slideInfoComponent', self._sliderContainer).remove();
			self._slideInfoComponent = null;

			self._updateSliderContainerSize();

			return true;
		}

		return false;
	},

	_generateSlideInfoComponent: function() {
		var self = this;
		var options = self.options;

		self._writeConsoleMessage('Generating Slide-Info-Component!');

		var slideInfoBox = $('<div></div>')
			.css({
				'width': 	self._getInt(options.slideInfoComponent.width),
				'height': 	self._getInt(options.slideInfoComponent.height)
			})
			.css(self._CSSParseUrls(options.slideInfoComponent.style));
		
		slideInfoBox.appendTo(document);
		var slideInfoBoxWidth = self._getOuterWidth(slideInfoBox, true);
		var slideInfoBoxHeight = self._getOuterHeight(slideInfoBox, true);
		slideInfoBox.remove();
		
		// create the navigationComponent
		self._slideInfoComponent = $('<div></div>')
			.addClass('slideInfoComponent')
			.css({
				'top': self._getInt(options.slideInfoComponent.top),
				'left': self._getInt(options.slideInfoComponent.left),
				'width': 	slideInfoBoxWidth,
				'height': 	slideInfoBoxHeight
			});

		var slideInfoBoxes = [];
		for(var i = 0; i < self._visibleSlidesData.length; i++) {
			if(
				((typeof(self._visibleSlidesData[i].title) !== 'undefined') && (self._visibleSlidesData[i].title.length > 0)) ||
				((typeof(self._visibleSlidesData[i].description) !== 'undefined') && (self._visibleSlidesData[i].description.length > 0))
			) {
				var slideInfoBox = $('<div></div>')
					.addClass('slideInfoBox')
					.attr('id', 'slideInfoBox-' + i)
					.css({
						'top': 		0,
						'left': 	0,
						'width': 	self._getInt(options.slideInfoComponent.width),
						'height': 	self._getInt(options.slideInfoComponent.height)
					})
					.css(self._CSSParseUrls(options.slideInfoComponent.style))
					.hide();

				slideInfoBox.on({
					"mouseenter.slideInfoBox": function() {
						if(options.slideInfoComponent.animateStyles === true) {
							$(this).stop(true, true);
							$(this).animate(
								self._CSSAnimate(options.slideInfoComponent.hoverStyle),
								self._getInt(options.slideInfoComponent.animateStyleDuration, 500),
								function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.hoverStyle));}
							);
						}
						else
							$(this).css(self._CSSParseUrls(options.slideInfoComponent.hoverStyle));
					},
					"mouseleave.slideInfoBox": function() {
						if(options.slideInfoComponent.animateStyles === true) {
							$(this).stop(true, true);
							$(this).animate(
								self._CSSAnimate(options.slideInfoComponent.style),
								self._getInt(options.slideInfoComponent.animateStyleDuration, 500),
								function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.style));}
							);
						}
						else
							$(this).css(self._CSSParseUrls(options.slideInfoComponent.style));
					}
				});

				if((typeof(self._visibleSlidesData[i].title) !== 'undefined') && (self._visibleSlidesData[i].title.length > 0)) {
					var title = $('<div></div>').addClass('slideInfoBoxTitle');
					title.html(self._visibleSlidesData[i].title)
						.css('width', self._getInt(options.slideInfoComponent.slideInfoTitle.width))
						.css('height', self._getInt(options.slideInfoComponent.slideInfoTitle.height))
						.css('fontFamily', options.slideInfoComponent.slideInfoTitle.fontFamily)
						.css('fontSize', self._getInt(options.slideInfoComponent.slideInfoTitle.fontSize) + 'px')
						.css('color', options.slideInfoComponent.slideInfoTitle.fontColor)
						.css('fontWeight', options.slideInfoComponent.slideInfoTitle.fontWeight)
						.css('fontStyle', options.slideInfoComponent.slideInfoTitle.fontStyle)
						.css('fontVariant', options.slideInfoComponent.slideInfoTitle.fontVariant)
						.css(self._CSSParseUrls(options.slideInfoComponent.slideInfoTitle.style));
					slideInfoBox.append(title);

					$('a', description).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoTitle.linksStyle));
					
					$('a', title).on({
						"mouseenter.slideInfoTitleLink": function() {
							if(options.slideInfoComponent.slideInfoTitle.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoTitle.linksHoverStyle),
									self._getInt(options.slideInfoComponent.slideInfoTitle.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoTitle.linksHoverStyle));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoTitle.linksHoverStyle));
						},
						"mouseleave.slideInfoTitleLink": function() {
							if(options.slideInfoComponent.slideInfoTitle.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoTitle.linksStyle),
									self._getInt(options.slideInfoComponent.slideInfoTitle.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoTitle.linksStyle));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoTitle.linksStyle));
						}
					});
					
					title.on({
						"mouseenter.slideInfoTitle": function() {
							if(options.slideInfoComponent.slideInfoTitle.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoTitle.hoverStyle),
									self._getInt(options.slideInfoComponent.slideInfoTitle.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoTitle.hoverStyle));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoTitle.hoverStyle));
						},
						"mouseleave.slideInfoTitle": function() {
							if(options.slideInfoComponent.slideInfoTitle.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoTitle.style),
									self._getInt(options.slideInfoComponent.slideInfoTitle.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoTitle.style));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoTitle.style));
						}
					});
				}

				if((typeof(self._visibleSlidesData[i].description) !== 'undefined') && (self._visibleSlidesData[i].description.length > 0)) {
					var description = $('<div></div>').addClass('slideInfoBoxDescription');
					description.html(self._visibleSlidesData[i].description)
						.css('width', self._getInt(options.slideInfoComponent.slideInfoDescription.width))
						.css('height', self._getInt(options.slideInfoComponent.slideInfoDescription.height))
						.css('fontFamily', options.slideInfoComponent.slideInfoDescription.fontFamily)
						.css('fontSize', self._getInt(options.slideInfoComponent.slideInfoDescription.fontSize) + 'px')
						.css('color', options.slideInfoComponent.slideInfoDescription.fontColor)
						.css('fontWeight', options.slideInfoComponent.slideInfoDescription.fontWeight)
						.css('fontStyle', options.slideInfoComponent.slideInfoDescription.fontStyle)
						.css('fontVariant', options.slideInfoComponent.slideInfoDescription.fontVariant)
						.css(self._CSSParseUrls(options.slideInfoComponent.slideInfoDescription.style));
					slideInfoBox.append(description);
					
					$('a', description).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoDescription.linksStyle));
					
					$('a', description).on({
						"mouseenter.slideInfoDescriptionLink": function() {
							if(options.slideInfoComponent.slideInfoDescription.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoDescription.linksHoverStyle),
									self._getInt(options.slideInfoComponent.slideInfoDescription.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoDescription.linksHoverStyle));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoDescription.linksHoverStyle));
						},
						"mouseleave.slideInfoDescriptionLink": function() {
							if(options.slideInfoComponent.slideInfoDescription.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoDescription.linksStyle),
									self._getInt(options.slideInfoComponent.slideInfoDescription.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoDescription.linksStyle));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoDescription.linksStyle));
						}
					});

					description.on({
						"mouseenter.slideInfoDescription": function() {
							if(options.slideInfoComponent.slideInfoDescription.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoDescription.hoverStyle),
									self._getInt(options.slideInfoComponent.slideInfoDescription.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoDescription.hoverStyle));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoDescription.hoverStyle));
						},
						"mouseleave.slideInfoDescription": function() {
							if(options.slideInfoComponent.slideInfoDescription.animateStyles === true) {
								$(this).stop(true, true);
								$(this).animate(
									self._CSSAnimate(options.slideInfoComponent.slideInfoDescription.style),
									self._getInt(options.slideInfoComponent.slideInfoDescription.animateStyleDuration, 500),
									function() {$(this).css(self._CSSDoNotAnimate(options.slideInfoComponent.slideInfoDescription.style));}
								);
							}
							else
								$(this).css(self._CSSParseUrls(options.slideInfoComponent.slideInfoDescription.style));
						}
					});
				}

				slideInfoBoxes.push(slideInfoBox.get(0));
			}
		}

		self._slideInfoComponent.append(slideInfoBoxes);

		// calculate and set the position of the component
		self._calculateComponentPosition(self._slideInfoComponent, options.slideInfoComponent);

		return true;
	},

	/*** OTHERS ***/

	_rotation: null,

	_startRotation: function() {
		var self = this;
		var options = self.options;

		self._isRotating = true;
		self.element.on("sliderjs.show", {"slider": self}, self._makeRotation);

		if(options.autoStopRotation) {
			self.element.on("sliderjs.prevarwclick", {"slider": self}, self._stopRotation );
			self.element.on("sliderjs.nextarwclick", {"slider": self}, self._stopRotation );
			self.element.on("sliderjs.navclick", {"slider": self}, self._stopRotation );
			self.element.on("sliderjs.prevkeypressed", {"slider": self}, self._stopRotation );
			self.element.on("sliderjs.nextkeypressed", {"slider": self}, self._stopRotation );
			self.element.on("sliderjs.scrollaction", {"slider": self}, self._stopRotation );
		}

		self._makeRotation();
	},
	_makeRotation: function(event) {
		if(typeof(event) === "undefined") var self = this;
		else var self = event.data.slider;
		var options = self.options;

		clearTimeout(self._rotation);
		self._rotation = setTimeout(function() {
			if(self._nextSlideExists()) self._goToNextSlide();
			else self._stopRotation();
		}, self._getInt(options.timer, 5000));

		if(event) event.stopPropagation();
	},
	_stopRotation: function(event) {
		if(typeof(event) === "undefined") var self = this;
		else var self = event.data.slider;
		var options = self.options;

		self._isRotating = false;

		clearTimeout(self._rotation);
		self.element.off("sliderjs.show", self._makeRotation);

		if(event) {
			self._stoppedByUserInteraction = true;
			
			if(options.autoStopRotation) {
				self.element.off("sliderjs.prevarwclick", self._stopRotation );
				self.element.off("sliderjs.nextarwclick", self._stopRotation );
				self.element.off("sliderjs.navclick", self._stopRotation );
				self.element.off("sliderjs.prevkeypressed", self._stopRotation );
				self.element.off("sliderjs.nextkeypressed", self._stopRotation );
				self.element.off("sliderjs.scrollaction", self._stopRotation );
			}
		}

		if(event) event.stopPropagation();

		self._writeConsoleMessage('Stopped auto-rotation!');
		self._trigger(".autorotatestop", null);
	},

	_showSlideInfo: function(slideIndex) {
		var self = this;
		var options = self.options;

		var slideInfoBox = $('#slideInfoBox-' + slideIndex, self._slideInfoComponent).first();

		// run the stage display effect with a little delay
		setTimeout(function(){
			self._runStageEffectOnComponent(slideInfoBox, options.slideInfoComponent.stageDisplayHideEffect, self._getInt(options.slideInfoComponent.stageDisplayHideEffectDuration, 500));
		}, 10);

		return true;
	},

	_hideSlideInfo: function(slideIndex) {
		var self = this;
		var options = self.options;

		var slideInfoBox = $('#slideInfoBox-' + slideIndex, self._slideInfoComponent).first();

		// run the stage display effect with a little delay
		setTimeout(function(){
			self._runStageEffectOnComponent(slideInfoBox, options.slideInfoComponent.stageDisplayHideEffect, self._getInt(options.slideInfoComponent.stageDisplayHideEffectDuration, 500));
		}, 10);

		return true;
	},

	// move the slider to a certain slide using the index for that slide
	_goToSlide: function (data) {
		var self = this;
		var options = self.options;

		var currentTimestamp = new Date().getTime();
		if(currentTimestamp - self._lastSlideChangeTimestamp < 300) return;
		self._lastSlideChangeTimestamp = currentTimestamp;
		
		// if there's no data -> return
		if(typeof(data) === 'undefined') return;

		// if there's no slide index -> return
		if(typeof(data.slideIndex) === 'undefined') return;

		// if we are already on this tab so nothing else
		if(data.slideIndex == self._currentSlideIndex) return;

		self._writeConsoleMessage('Slide ' + data.slideIndex + ' is displayed!');

		var dispatcher = false;
		if(typeof(data.dispatcher) !== 'undefined') dispatcher = data.dispatcher;

		// update the previous and current slide indexes to the new index
		self._prevSlideIndex = self._currentSlideIndex;
		self._currentSlideIndex = data.slideIndex;

		// show the slide we are interested in
		self._showHideSlide(self._currentSlideIndex, self._prevSlideIndex, dispatcher);

		if(options.thumbnailsNavigationComponent.available === true && (self._thumbnailsNavigationComponent !== null)) {
			var scroll = true;
			if(typeof(data.scrollThumbnailsNavigationComponent) !== 'undefined' && (data.scrollThumbnailsNavigationComponent === false))
				scroll = false;

			self._markSelectedThumbnail(scroll);
		}

		if(options.anchorsNavigationComponent.available === true && (self._anchorsNavigationComponent !== null)) {
			var scroll = true;
			if(typeof(data.scrollAnchorsNavigationComponent) !== 'undefined' && (data.scrollAnchorsNavigationComponent === false))
				scroll = false;

			self._markSelectedAnchor(scroll);
		}

		self._trigger(".show", null, {'slideIndex': self._currentSlideIndex});

		self._updateArrowsNavigationComponentState();

		return true;
	},

	// show the slide with the index `currentSlideIndex` and hide the slide with the index `prevSlideIndex` (can also apply any effect here)
	_showHideSlide: function(currentSlideIndex, prevSlideIndex, dispatcher) {
		var self = this;
		var options = self.options;

		if(options.slideInfoComponent.available === true) {
			self._hideSlideInfo(prevSlideIndex);
			self._showSlideInfo(currentSlideIndex);
		}

		var finalizeSlicesBoxesEffects = function() {
			// clear any existing effect timeouts and reset the array
			$.each(self._transitionEffectsTimeouts, function(index, value){clearTimeout(value);});
			self._transitionEffectsTimeouts = [];
			// call the finishing effect function if it exists
			if(self._transitionEffectsFinishFunction != null) self._transitionEffectsFinishFunction();
			self._transitionEffectsFinishFunction = null;
		};
		// container type can be 'box' or 'slice'
		var loadImages = function(containerType) {
			$('.' + containerType + 'Image', self._slidesContainer).attr('src', self._getPath(self._visibleSlidesData[currentSlideIndex].src, 'slides'))
				.each(function(index, image){
					$(image).css({
						top: parseInt(self._visibleSlidesImageData[currentSlideIndex].top + $(image).data('top')) + 'px',
						left: parseInt(self._visibleSlidesImageData[currentSlideIndex].left + $(image).data('left')) + 'px',
						width: self._visibleSlidesImageData[currentSlideIndex].width + 'px',
						height: self._visibleSlidesImageData[currentSlideIndex].height + 'px'
					});
				});
		};

		var prevTransitionEffectLabel = (self._latestTransitionEffect !== null)?self._latestTransitionEffect.split('-')[0]:'';
		if(prevTransitionEffectLabel == 'boxes') {
			$('.slideBox', self._slidesContainer).stop(true, true);
			finalizeSlicesBoxesEffects();
		}
		else if(prevTransitionEffectLabel == 'slices') {
			$('.slideSlice', self._slidesContainer).stop(true, true);
			finalizeSlicesBoxesEffects();
		}
		else {
			$(self._slides).stop(true, true);
		}

		var prevSlide = $(self._slides[prevSlideIndex]);
		var currentSlide = $(self._slides[currentSlideIndex]);
		$(self._slides).css("z-index", self._slideZIndexBase);
		currentSlide.css("z-index", self._slideZIndexBase + 1);

		var transitionEffect = self._visibleSlidesData[currentSlideIndex].transitionEffect;
		// if the SliderJS runs on a mobile device
		if(	navigator.userAgent.match(/Android/i) 	||
			navigator.userAgent.match(/webOS/i) 	||
			navigator.userAgent.match(/iPhone/i) 	||
			navigator.userAgent.match(/iPad/i) 		||
			navigator.userAgent.match(/iPod/i) 		||
			navigator.userAgent.match(/BlackBerry/i)
		) {
			if(options.mobileTransitionEffect != 'usethesame')
				transitionEffect = options.mobileTransitionEffect;
		}
		if(transitionEffect == 'usedefault') transitionEffect = options.defaultTransitionEffect;
		if(transitionEffect == 'random') transitionEffect = self._transitionEffects[Math.floor(Math.random() * self._transitionEffects.length)];
		if($.inArray(transitionEffect, self._transitionEffects) < 0) transitionEffect = "fade";
		self._latestTransitionEffect = transitionEffect;
		var transitionEffectDuration = self._getInt(self._visibleSlidesData[currentSlideIndex].transitionEffectDuration, 500);

		var transitionEffectLabel = transitionEffect.split('-')[0];
		switch(transitionEffectLabel) {
			case 'boxes':
				loadImages('box');

				// show the current slide
				currentSlide.show().removeClass('hide').addClass('show');
				var slideImage = $('.slideImage', currentSlide).hide();
				var boxesContainer = $('.slideBoxes', self._slidesContainer).show();
				var boxes = $('.slideBox', boxesContainer).hide();

				var transitionEffectName = transitionEffect.split('-')[1];
				if(transitionEffectName.indexOf('reverse') >= 0) boxes.reverse();
				else if(transitionEffectName.indexOf('random') >= 0) boxes = self._shuffle(boxes);

				self._transitionEffectsFinishFunction = function() {
					boxesContainer.hide().find('.slideBox').hide();
					slideImage.show();
					prevSlide.hide().removeClass('show').addClass('hide');
					self._transitionEffectsFinishFunction = null;
				};

				var initialStyle = {'opacity': 0};
				var finalStyle = {'opacity': 1};

				var boxTransitionDuration = (transitionEffectDuration / 2);
				var boxTransitionDelay = 100;
				var boxTransitionDelayBase = (boxTransitionDuration - boxTransitionDelay) / boxes.length;

				if(transitionEffectName.indexOf('diagonal') >= 0) {
					var boxTransitionDelayBase = (boxTransitionDuration - boxTransitionDelay) / Math.max(options.effectsBoxesX, options.effectsBoxesY);

					var i = 0, j = 0, boxesMatrix = new Array();
					boxesMatrix[i] = new Array();
					boxes.each(function(){
						boxesMatrix[i][j++] = $(this);
						if(j == options.effectsBoxesX){
							i++; j = 0;
							boxesMatrix[i] = new Array();
						}
					});

					var nrBoxes = 0;
					var maxAxisSize = 2*Math.max(options.effectsBoxesX, options.effectsBoxesY);

					for(var i = 0; i < maxAxisSize; i++) {
						var k = i;
						for(var j = 0; j < maxAxisSize; j++) {
							if(k >= 0 && typeof(boxesMatrix[k]) !== 'undefined' && typeof(boxesMatrix[k][j]) !== 'undefined') {
								var box = boxesMatrix[k][j];
								box.css({'top':box.data('top') + 'px', 'left':box.data('left') + 'px', 'width':box.data('width') + 'px', 'height':box.data('height') + 'px'});

								if(transitionEffectName.indexOf('diagonalgrow') >= 0) {
									var boxMeasures = {'top': parseInt(box.css('top')), 'left': parseInt(box.css('left')), 'width': parseInt(box.css('width')), 'height': parseInt(box.css('height'))};
									finalStyle = {'width': boxMeasures.width + 'px', 'height': boxMeasures.height + 'px', 'opacity': 1};
									initialStyle = {'width': '0px', 'height': '0px', 'opacity': 0};
								}

								(function(box, boxTransitionDelay, boxTransitionDuration, finalStyle){
									box.css(initialStyle).show();
									if(nrBoxes == boxes.length - 1) self._transitionEffectsTimeouts.push(setTimeout(function(){box.animate(finalStyle, boxTransitionDuration, '', self._transitionEffectsFinishFunction);}, boxTransitionDelay));
									else self._transitionEffectsTimeouts.push(setTimeout(function(){box.animate(finalStyle, boxTransitionDuration);}, boxTransitionDelay));
								})(box, boxTransitionDelay, boxTransitionDuration, finalStyle);
								nrBoxes++;
							}
							k--;
						}
						boxTransitionDelay += boxTransitionDelayBase;
					}
				}
				else {
					$.each(boxes, function(index, value){
						var box = $(value);
						box.css({'top':box.data('top') + 'px', 'left':box.data('left') + 'px', 'width':box.data('width') + 'px', 'height':box.data('height') + 'px'});

						if(transitionEffectName.indexOf('centergrow') >= 0) {
							var boxMeasures = {'top': parseInt(box.css('top')), 'left': parseInt(box.css('left')), 'width': parseInt(box.css('width')), 'height': parseInt(box.css('height'))};
							finalStyle = {'top': boxMeasures.top + 'px', 'left': boxMeasures.left + 'px', 'width': boxMeasures.width + 'px', 'height': boxMeasures.height + 'px', 'opacity': 1};
							initialStyle = {'top': Math.round(boxMeasures.top + boxMeasures.height/2) + 'px', 'left': Math.round(boxMeasures.left + boxMeasures.width/2) + 'px', 'width': '0px', 'height': '0px', 'opacity': 0};
						}

						(function(box, boxTransitionDelay, boxTransitionDuration, finalStyle){
							box.css(initialStyle).show();
							if(index == boxes.length - 1) self._transitionEffectsTimeouts.push(setTimeout(function(){box.animate(finalStyle, boxTransitionDuration, '', self._transitionEffectsFinishFunction);}, boxTransitionDelay));
							else self._transitionEffectsTimeouts.push(setTimeout(function(){box.animate(finalStyle, boxTransitionDuration);}, boxTransitionDelay));
						})(box, boxTransitionDelay, boxTransitionDuration, finalStyle);

						boxTransitionDelay += boxTransitionDelayBase;
					});
				}
				break;
			case 'slices':
				loadImages('slice');

				// show the current slide
				currentSlide.show().removeClass('hide').addClass('show');
				var slideImage = $('.slideImage', currentSlide).hide();
				var transitionEffectOrientation = transitionEffect.split('-')[1];
				if(transitionEffectOrientation == 'vertical') var slicesContainer = $('.slideSlicesX', self._slidesContainer).show();
				if(transitionEffectOrientation == 'horizontal') var slicesContainer = $('.slideSlicesY', self._slidesContainer).show();
				var slices = $('.slideSlice', slicesContainer).hide();

				var transitionEffectName = transitionEffect.split('-')[2];
				if(transitionEffectName.indexOf('fromright') >= 0 || transitionEffectName.indexOf('frombottom') >= 0) slices.reverse();
				else if(transitionEffectName.indexOf('random') >= 0) slices = self._shuffle(slices);

				self._transitionEffectsFinishFunction = function() {
					slicesContainer.hide().find('.slideSlice').hide();
					slideImage.show();
					prevSlide.hide().removeClass('show').addClass('hide');
					self._transitionEffectsFinishFunction = null;
				};

				var initialStyle = [{'opacity': 0},{'opacity': 0}];
				var finalStyle = [{'opacity': 1},{'opacity': 1}];
				var addDelayBetweenSlicesEffects = true;
				if(transitionEffectName.indexOf('dropfromleft') >= 0 || transitionEffectName.indexOf('dropfromright') >= 0 || transitionEffectName.indexOf('vdroprandom') >= 0) {
					finalStyle = [{'top': '0px', 'opacity': 1},{'top': '0px', 'opacity': 1}];
					initialStyle = [{'top': -options.height + 'px', 'opacity': 0.35},{'top': -options.height + 'px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('raise') >= 0) {
					finalStyle = [{'top': '0px', 'opacity': 1},{'top': '0px', 'opacity': 1}];
					initialStyle = [{'top': options.height + 'px', 'opacity': 0.35},{'top': options.height + 'px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('dropaltfrom') >= 0 || transitionEffectName.indexOf('dropaltrandom') >= 0) {
					finalStyle = [{'top': '0px', 'opacity': 1},{'top': '0px', 'opacity': 1}];
					initialStyle = [{'top': -options.height + 'px', 'opacity': 0.35},{'top': options.height + 'px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('slidefrom') >= 0 || transitionEffectName.indexOf('sliderandom') >= 0) {
					var sliceWidth = Math.round(options.width/slices.length);
					finalStyle = [{'width': sliceWidth + 'px', 'opacity': 1},{'width': sliceWidth + 'px', 'opacity': 1}];
					initialStyle = [{'width': '0px', 'opacity': 0.35},{'width': '0px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('sliderightfrom') >= 0 || transitionEffectName.indexOf('sliderightrandom') >= 0) {
					finalStyle = [{'left': '0px', 'opacity': 1},{'left': '0px', 'opacity': 1}];
					initialStyle = [{'left': -options.width + 'px', 'opacity': 0.35},{'left': -options.width + 'px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('slideleftfrom') >= 0 || transitionEffectName.indexOf('slideleftrandom') >= 0) {
					finalStyle = [{'left': '0px', 'opacity': 1},{'left': '0px', 'opacity': 1}];
					initialStyle = [{'left': options.width + 'px', 'opacity': 0.35},{'left': options.width + 'px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('slidealtfrom') >= 0 || transitionEffectName.indexOf('slidealtrandom') >= 0) {
					finalStyle = [{'left': '0px', 'opacity': 1},{'left': '0px', 'opacity': 1}];
					initialStyle = [{'left': -options.width + 'px', 'opacity': 0.35},{'left': options.width + 'px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('dropfromtop') >= 0 || transitionEffectName.indexOf('dropfrombottom') >= 0 || transitionEffectName.indexOf('hdroprandom') >= 0) {
					var sliceHeight = Math.round(options.height/slices.length);
					finalStyle = [{'height': sliceHeight + 'px', 'opacity': 1},{'height': sliceHeight + 'px', 'opacity': 1}];
					initialStyle = [{'height': '0px', 'opacity': 0.35},{'height': '0px', 'opacity': 0.35}];
				}
				else if(transitionEffectName.indexOf('fadefrom') >= 0  || transitionEffectName.indexOf('faderandom') >= 0) {
					finalStyle = [{'opacity': 1},{'opacity': 1}];
					initialStyle = [{'opacity': 0},{'opacity': 0}];
				}
				else if(transitionEffectName == 'altallatonce') {
					if(transitionEffectOrientation == 'vertical') {
						finalStyle = [{'top': '0px', 'opacity': 1},{'top': '0px', 'opacity': 1}];
						initialStyle = [{'top': -options.height + 'px', 'opacity': 0.35},{'top': options.height + 'px', 'opacity': 0.35}];
					}
					else if(transitionEffectOrientation == 'horizontal') {
						finalStyle = [{'left': '0px', 'opacity': 1},{'left': '0px', 'opacity': 1}];
						initialStyle = [{'left': -options.width + 'px', 'opacity': 0.35},{'left': options.width + 'px', 'opacity': 0.35}];
					}
					addDelayBetweenSlicesEffects = false;
				}

				var sliceTransitionDuration = (transitionEffectDuration / 2);
				var sliceTransitionDelay = 100;
				var sliceTransitionDelayBase = (sliceTransitionDuration - sliceTransitionDelay) / slices.length;
				if(addDelayBetweenSlicesEffects == false) {
					sliceTransitionDuration = transitionEffectDuration;
					sliceTransitionDelay = 0;
					sliceTransitionDelayBase = 0;
				}
				$.each(slices, function(index, value){
					var slice = $(value);
					slice.css({'top':slice.data('top') + 'px', 'left':slice.data('left') + 'px', 'width':slice.data('width') + 'px', 'height':slice.data('height') + 'px'});

					(function(slice, sliceTransitionDelay, sliceTransitionDuration, finalStyle){
						slice.css(initialStyle[index%2]).show();
						if(index == slices.length - 1) self._transitionEffectsTimeouts.push(setTimeout(function(){slice.animate(finalStyle[index%2], sliceTransitionDuration, '', self._transitionEffectsFinishFunction);}, sliceTransitionDelay));
						else self._transitionEffectsTimeouts.push(setTimeout(function(){slice.animate(finalStyle[index%2], sliceTransitionDuration);}, sliceTransitionDelay));
					})(slice, sliceTransitionDelay, sliceTransitionDuration, finalStyle);

					sliceTransitionDelay += sliceTransitionDelayBase;
				});
				break;
			default:
				switch (transitionEffect) {
					case "scrollhorizontal":
						if(dispatcher == "nextArrow" || ((dispatcher == "thumbnail" || dispatcher == "anchor") && prevSlideIndex < currentSlideIndex)) {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "right"}, transitionEffectDuration).removeClass('hide').addClass('show');
							$(self._slides[prevSlideIndex]).hide("slide", {direction: "left"}, transitionEffectDuration).removeClass('show').addClass('hide');
						}
						else {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "left"}, transitionEffectDuration).removeClass('hide').addClass('show');
							$(self._slides[prevSlideIndex]).hide("slide", {direction: "right"}, transitionEffectDuration).removeClass('show').addClass('hide');
						}
						break;
					case "scrollvertical":
						if(dispatcher == "nextArrow" || ((dispatcher == "thumbnail" || dispatcher == "anchor") && prevSlideIndex < currentSlideIndex)) {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "down"}, transitionEffectDuration).removeClass('hide').addClass('show');
							$(self._slides[prevSlideIndex]).hide("slide", {direction: "up"}, transitionEffectDuration).removeClass('show').addClass('hide');
						}
						else {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "up"}, transitionEffectDuration).removeClass('hide').addClass('show');
							$(self._slides[prevSlideIndex]).hide("slide", {direction: "down"}, transitionEffectDuration).removeClass('show').addClass('hide');
						}
						break;
					case "scrollup":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "down"}, transitionEffectDuration).removeClass('hide').addClass('show');
						$(self._slides[prevSlideIndex]).hide("slide", {direction: "up"}, transitionEffectDuration).removeClass('show').addClass('hide');
						break;
					case "scrollright":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "left"}, transitionEffectDuration).removeClass('hide').addClass('show');
						$(self._slides[prevSlideIndex]).hide("slide", {direction: "right"}, transitionEffectDuration).removeClass('show').addClass('hide');
						break;
					case "scrolldown":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "up"}, transitionEffectDuration).removeClass('hide').addClass('show');
						$(self._slides[prevSlideIndex]).hide("slide", {direction: "down"}, transitionEffectDuration).removeClass('show').addClass('hide');
						break;
					case "scrollleft":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "right"}, transitionEffectDuration).removeClass('hide').addClass('show');
						$(self._slides[prevSlideIndex]).hide("slide", {direction: "left"}, transitionEffectDuration).removeClass('show').addClass('hide');
						break;
					case "slidehorizontal":
						if(dispatcher == "nextArrow" || ((dispatcher == "thumbnail" || dispatcher == "anchor") && prevSlideIndex < currentSlideIndex)) {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "right"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						}
						else {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "left"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						}
						break;
					case "slidevertical":
						if(dispatcher == "nextArrow" || ((dispatcher == "thumbnail" || dispatcher == "anchor") && prevSlideIndex < currentSlideIndex)) {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "down"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						}
						else {
							$(self._slides[currentSlideIndex]).show("slide", {direction: "up"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						}
						break;
					case "slideup":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "down"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						break;
					case "slideright":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "left"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						break;
					case "slidedown":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "up"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						break;
					case "slideleft":
						$(self._slides[currentSlideIndex]).show("slide", {direction: "right"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						break;
					case "fade":
						$(self._slides[currentSlideIndex]).show("fade", {}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						break;
					case "cliphorizontal":
						$(self._slides[currentSlideIndex]).show("clip", {direction: "horizontal"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						break;
					case "clipvertical":
						$(self._slides[currentSlideIndex]).show("clip", {direction: "vertical"}, transitionEffectDuration, function() {$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');}).removeClass('hide').addClass('show');
						break;
					default:
						$(self._slides[currentSlideIndex]).show().removeClass('hide').addClass('show');
						$(self._slides[prevSlideIndex]).hide().removeClass('show').addClass('hide');
				}
		}

		return true;
	},

	// check if there exists a next slide in line
	_nextSlideExists: function() {
		var self = this;
		var options = self.options;

		// carousel option is on so there's always a next slide
		if(self.options.carousel) return true;

		// the current position is at the end of the slides list and there is no next slide
		if(self._currentSlideIndex == (self._visibleSlidesData.length - 1)) return false;

		// in any other case a next slide exists
		return true;
	},

	// check if there exists a previous slide in line
	_prevSlideExists: function() {
		var self = this;
		var options = self.options;

		// carousel option is on so there's always a previous slide
		if(self.options.carousel) return true;

		// the current position is at the start of the slides list and there is no previous slide
		if(self._currentSlideIndex == 0) return false;

		// in any other case a previous slide exists
		return true;
	},

	// get the index of the next slide in line
	_getNextSlideIndex: function() {
		var self = this;
		var options = self.options;

		// if there's no next slide, return false
		if(!self._nextSlideExists()) return false;

		return (self._currentSlideIndex + 1) % self._visibleSlidesData.length;
	},

	// get the index of the previous slide in line
	_getPrevSlideIndex: function() {
		var self = this;
		var options = self.options;

		// if there's no previous slide, return false
		if(!self._prevSlideExists()) return false;

		return (self._currentSlideIndex - 1 + self._visibleSlidesData.length) % self._visibleSlidesData.length;
	},

	// moves the slider to the next slide
	_goToNextSlide: function () {
		var self = this;
		var options = self.options;

		// if there's no next slide display, exit
		if(!self._nextSlideExists()) return;

		self._trigger(".gotoslide", null, {
			'slideIndex': self._getNextSlideIndex(),
			'dispatcher': 'nextArrow'
		});

		return true;
	},

	// moves the slider to the previous slide
	_goToPrevSlide: function () {
		var self = this;
		var options = self.options;

		// if there's no previous slide display, exit
		if(!self._prevSlideExists()) return;

		self._trigger(".gotoslide", null, {
			'slideIndex': self._getPrevSlideIndex(),
			'dispatcher': 'prevArrow'
		});

		return true;
	},

	// utility function that returns the parsed integer or a replacement value or 0 if an integer couldn't be parsed
	_getInt: function(intCandidate, intReplacement) {
		var result = parseInt(intCandidate);
		var replacement = isNaN(parseInt(intReplacement)) ? 0 : parseInt(intReplacement);

		if(isNaN(result)) return replacement;

		return result;
	},

	// utility function to get outer 'width' of an element
	_getOuterWidth: function(element, includeMargins) {
		var self = this;
		var options = self.options;

		if(typeof(includeMargins) === 'undefined') includeMargins = false;

		var result =
			self._getInt(element.css('border-left-width')) +
			self._getInt(element.css('padding-left')) +
			self._getInt(element.css('width')) +
			self._getInt(element.css('padding-right')) +
			self._getInt(element.css('border-right-width'));


		if(includeMargins === true)
			result +=
				self._getInt(element.css('margin-left')) +
				self._getInt(element.css('margin-right'));

		return result;
	},

	// utility function to get outer 'height' of an element
	_getOuterHeight: function(element, includeMargins) {
		var self = this;
		var options = self.options;

		if(typeof(includeMargins) === 'undefined') includeMargins = false;

		var result =
			self._getInt(element.css('border-top-width')) +
			self._getInt(element.css('padding-top')) +
			self._getInt(element.css('height')) +
			self._getInt(element.css('padding-bottom')) +
			self._getInt(element.css('border-bottom-width'));

		if(includeMargins === true)
			result +=
				self._getInt(element.css('margin-top')) +
				self._getInt(element.css('margin-bottom'));

		return result;
	},

	// converts the src given into a valid path, depending on the type (anchors, arrows, general, settings, slides)
	_getPath: function(src, type) {
		var self = this;
		var options = self.options;

		// the src is in fact an absolute path and should be returned as it is
		if(src.toLowerCase().indexOf('/') >= 0) return src;

		switch(type) {
			case 'anchors': return options.sliderPath + options.anchorsPath + src;
			case 'arrows': return options.sliderPath + options.arrowsPath + src;
			case 'general': return options.sliderPath + options.generalPath + src;
			case 'settings': return options.sliderPath + options.settingsPath + src;
			case 'slides': return options.sliderPath + options.slidesPath + src;
		}
	},

	_CSSParseUrls: function(cssObject) {
		var self = this;
		var options = self.options;

		var cssString = self._CSSFromObject(cssObject);
		var cssObject = self._CSSToObject(cssString);

		$.each(cssObject, function(property, value) {
			if(property == 'background-image' && value.indexOf('/') < 0) {
				var string = value.replace('"', "'");
				if(string.indexOf("'") >= 0) string = string.substring(string.indexOf("'") + 1, string.lastIndexOf("'"));
				else string = string.substring(string.indexOf("(") + 1, string.lastIndexOf(")"));
				cssObject[property] = "url('" + self._getPath(string, 'general') + "')";
			}
		});

		return cssObject;
	},

	_CSSAnimate: function(cssObject) {
		var self = this;
		var cssString = self._CSSFromObject(cssObject);
		var cssObject = self._CSSToObject(cssString);

		var animate = {};
		// filter numeric and color properties
		$.each(cssObject, function(property, value) {
			if($.inArray(property, self._cssAllowedAnimateProperties) >= 0) {
				animate[property] = value;
			}
			else if(!isNaN(parseInt(value)) && $.inArray(property, self._cssNotAllowedAnimateProperties) == -1) {
				animate[property] = value;
			}
		});

		return animate;
	},
	_CSSDoNotAnimate: function(cssObject) {
		var self = this;
		var cssString = self._CSSFromObject(cssObject);
		var cssObject = self._CSSToObject(cssString);

		var doNotAnimate = {};
		// filter numeric and color properties
		$.each(cssObject, function(property, value) {
			if($.inArray(property, self._cssAllowedAnimateProperties) == -1 && isNaN(parseInt(value)) || $.inArray(property, self._cssNotAllowedAnimateProperties) >= 0) {
				doNotAnimate[property] = value;
			}
		});

		return self._CSSParseUrls(doNotAnimate);
	},

	_CSSToObject: function(cssString) {
		var self = this;
		var rules = new Object();
		cssString = self._CSSParseRemoveComments(cssString);

		var declarations = $.trim(cssString).split(';');
		$.each(declarations, function(index, declaration) {
			declaration = $.trim(declaration);
			if(declaration.length > 0) {
				var splitCharacterPosition = declaration.indexOf(':');
				var property = $.trim(declaration.substring(0, splitCharacterPosition));
				var value = $.trim(declaration.substring(splitCharacterPosition + 1));

				if (property.length > 0 && value.length > 0) rules[property] = value;
			}
		});

		return rules;
	},

	_CSSFromObject: function(cssObject) {
		var self = this;

		var cssString = '';
		$.each(cssObject, function(property, value) {
			cssString += property + ': ' + value + ';\n';
		});

		return cssString;
	},

	_CSSParseRemoveComments: function(css) {
		return css.replace(/\/\*(\r|\n|.)*\*\//g,"");
	},

	_shuffle: function(array) {
		for(var j, x, i = array.length; i; j = parseInt(Math.random() * i), x = array[--i], array[i] = array[j], array[j] = x);
		return array;
	},

	_generateID: function(length) {
		if (length == null) length = 10;
		var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
		var id = '';
		for (var i=0; i < length; i++) {
			var random = Math.floor(Math.random() * chars.length);
			id += chars.substring(random,random+1);
		}
		return id;
	},

	_writeConsoleMessage: function (message) {
		var self = this;
		var options = self.options;

		if(self._debug) console.log(message);

		return true;
	}
});})(jQuery);

/*! Copyright (c) 2011 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.6
 *
 * Requires: 1.2.2+
 */
(function($){var types=['DOMMouseScroll','mousewheel'];if($.event.fixHooks){for(var i=types.length;i;){$.event.fixHooks[types[--i]]=$.event.mouseHooks;}}$.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var i=types.length;i;){this.addEventListener(types[--i],handler,false);}}else{this.onmousewheel=handler;}},teardown:function(){if(this.removeEventListener){for(var i=types.length;i;){this.removeEventListener(types[--i],handler,false);}}else{this.onmousewheel=null;}}};$.fn.extend({mousewheel:function(fn){return fn?this.bind("mousewheel",fn):this.trigger("mousewheel");},unmousewheel:function(fn){return this.unbind("mousewheel",fn);}});function handler(event){var orgEvent=event||window.event,args=[].slice.call(arguments,1),delta=0,returnValue=true,deltaX=0,deltaY=0;event=$.event.fix(orgEvent);event.type="mousewheel";if(orgEvent.wheelDelta){delta=orgEvent.wheelDelta/120;}if(orgEvent.detail){delta=-orgEvent.detail/3;}deltaY=delta;if(orgEvent.axis!==undefined&&orgEvent.axis===orgEvent.HORIZONTAL_AXIS){deltaY=0;deltaX=-1*delta;}if(orgEvent.wheelDeltaY!==undefined){deltaY=orgEvent.wheelDeltaY/120;}if(orgEvent.wheelDeltaX!==undefined){deltaX=-1*orgEvent.wheelDeltaX/120;}args.unshift(event,delta,deltaX,deltaY);return($.event.dispatch||$.event.handle).apply(this,args);}})(jQuery);

/*
 * jQuery Image Loaded Plug-in
 *
 * Copyright (c) 2012 All Right Reserved, jstyler (http://www.jstyler.net)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */
(function($){$.fn.ImageLoaded=function(callback){var empty='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';function executeCallback(targetImage){var width=0;var height=0;var myImage=new Image();myImage.src=$(targetImage).attr('src');width=myImage.width;height=myImage.height;if(callback)callback.call(targetImage,width,height);}return this.each(function(){var self=$(this);self.on('load error',function(){var currentImage=this;setTimeout(function(){executeCallback(currentImage);});});if(self.complete||typeof self.complete==='undefined'){var src=self.attr('src');self.attr('src',empty);self.attr('src',src);}return this;});};})(jQuery);

/*
 * jQuery Smooth Scroller Plug-in
 *
 * Copyright (c) 2012 All Right Reserved, jstyler (http://www.jstyler.net)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */
(function($){var ScrollTo=function ScrollTo(changingProperty){this._changingProperty=changingProperty;this._currentPosition=0;this._finalPosition=0;this._isMoving=false;this._pase=0.05;this.moveToFinalPosition=function(element,position){var self=this;self._isMoving=false;self._currentPosition=parseInt(element.css(self._changingProperty));self._finalPosition=position;element.stop(true);self.moveToPosition(element,self._finalPosition);};this.animateToFinalPosition=function(element,position,time){var self=this;element.stop(true);var animation={};animation[self._changingProperty]=position;element.animate(animation,time);};this.scrollToFinalPosition=function(element,position){var self=this;self._currentPosition=parseInt(element.css(self._changingProperty));self._finalPosition=position;element.stop(true);if(self._isMoving)return null;self._isMoving=true;return self.scroll(element);};this.moveToPosition=function(element,position){var self=this;self._currentPosition=position;element.css(self._changingProperty,position);};this.scroll=function(element){var self=this;self._currentPosition+=(self._finalPosition-self._currentPosition)*self._pase;self.moveToPosition(element,self._currentPosition);if(self._isMoving==true&&Math.floor(self._currentPosition)!==self._finalPosition&&Math.ceil(self._currentPosition)!==self._finalPosition)return setTimeout(function(){return self.scroll(element);},15);self.moveToPosition(element,self._finalPosition);return self._isMoving=false;};};var methods={init:function(options){var settings={orientation:'horizontal',contentId:'',inactiveSpace:75};var options=$.extend({},settings,options);return this.each(function(){var self=$(this);var data=self.data('SmoothScroll');if(!data){var data={};data['container']=$(this);if(typeof(options.contentId)!=='undefined'&&(options.contentId.length>0))data['content']=data['container'].children('#'+options.contentId);else data['content']=data['container'].children().first();data['container'].addClass('SmoothScroller');if(options.orientation=='vertical'){data['changingProperty']='top';data['containerDimension']=data['container'].outerHeight(true);data['contentDimension']=data['content'].outerHeight(true);}else{data['changingProperty']='left';data['containerDimension']=data['container'].outerWidth(true);data['contentDimension']=data['content'].outerWidth(true);}if(data['containerDimension']<2*options.inactiveSpace)options.inactiveSpace=parseInt(data['containerDimension']/3);data['scrollTo']=new ScrollTo(data['changingProperty']);data['scrollUnit']=data['contentDimension']/(data['containerDimension']-(2*options.inactiveSpace));data['coordinateConversionRatio']=data['containerDimension']/(data['containerDimension']-(2*options.inactiveSpace));$(this).data('SmoothScroll',data);}return data['container'].mousemove(function(e){data['offset']=data['container'].offset()[data['changingProperty']];if(data['contentDimension']<=data['containerDimension'])return false;var coordinates={left:e.pageX,top:e.pageY};var coordinate=coordinates[data['changingProperty']]-data['offset'];if(coordinate<=options.inactiveSpace){position=0;}else if(coordinate>(data['containerDimension']-options.inactiveSpace)){position=data['containerDimension']-data['contentDimension'];}else{coordinate-=options.inactiveSpace;var position=coordinate*data['coordinateConversionRatio']-coordinate*data['scrollUnit'];if(position>0)position=0;if(position<(data['containerDimension']-data['contentDimension']))position=data['containerDimension']-data['contentDimension'];}data['scrollTo'].scrollToFinalPosition(data['content'],position);return data['container'];});});},destroy:function(){var self=$(this);self.unbind('mousemove');},centerPosition:function(position){var self=this;var data=self.data('SmoothScroll');if(data.contentDimension<=data.containerDimension){var desiredPosition=Math.round(data.containerDimension/2-data.contentDimension/2);data.scrollTo.moveToFinalPosition(data.content,desiredPosition);return false;}var elementPosition=position;var centerPosition=Math.round(data.containerDimension/2);var desiredPosition=-elementPosition+centerPosition;if(desiredPosition>0)desiredPosition=0;if(desiredPosition<(data.containerDimension-data.contentDimension))desiredPosition=data.containerDimension-data.contentDimension;data.scrollTo.scrollToFinalPosition(data.content,desiredPosition);},modifyPosition:function(offset,time){var self=this;var data=self.data('SmoothScroll');if(typeof(time)=='undefined')var time=false;if(data.contentDimension<=data.containerDimension){var position=Math.round(data.containerDimension/2-data.contentDimension/2);data.scrollTo.moveToFinalPosition(data.content,position);return false;}var position=parseInt(data.content.css(data.changingProperty))+parseInt(offset);var speed=Math.abs(offset)/time;if(position>0){position=0;time=Math.abs(parseInt(data.content.css(data.changingProperty)))*speed;}if(position<(data.containerDimension-data.contentDimension)){position=data.containerDimension-data.contentDimension;time=(Math.abs(data.containerDimension-data.contentDimension)-Math.abs(parseInt(data.content.css(data.changingProperty))))*speed;}if(time)data.scrollTo.animateToFinalPosition(data.content,position,time);else data.scrollTo.moveToFinalPosition(data.content,position);}};$.fn.SmoothScroller=function(method){if(methods[method])return methods[method].apply(this,Array.prototype.slice.call(arguments,1));if(typeof method==='object'||!method)return methods.init.apply(this,arguments);$.error('The method "'+method+'" does not exist in jQuery.smoothScroll!');};})(jQuery);

/*
 * jQuery TouchSupport Plug-in
 *
 * Copyright (c) 2012 All Right Reserved, jstyler (http://www.jstyler.net)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */
(function($){var methods={init:function(options){var settings={swipeTreshhold:{x:150,y:150},swipeLimit:{x:100,y:100},scrollThreshold:{x:20,y:20},scrollPace:{x:1,y:1},tapThreshhold:{x:5,y:5},swipeUp:function(e,t){},swipeRight:function(e,t){},swipeDown:function(e,t){},swipeLeft:function(e,t){},scrollH:function(e,t,offset){},scrollV:function(e,t,offset){},momentumH:function(e,t,offset,time){},momentumV:function(e,t,offset,time){},tap:function(e,t){},stopPropagation:true,preventDefault:true},gestures={swipeH:false,swipeV:false,scrollH:false,scrollV:false,tap:false};if(typeof(options.swipeRight)!=='undefined'||typeof(options.swipeLeft)!=='undefined')gestures.swipeH=true;if(typeof(options.swipeUp)!=='undefined'||typeof(options.swipeDown)!=='undefined')gestures.swipeV=true;if(typeof(options.scrollH)!=='undefined')gestures.scrollH=true;if(typeof(options.scrollV)!=='undefined')gestures.scrollV=true;if(typeof(options.tap)!=='undefined')gestures.tap=true;var options=$.extend({},settings,options);return this.each(function(){var self=$(this);var data=self.data('TouchSupport');if(!data){var data={};data.element=self;data.cCurrent={x:0,y:0};data.cStart={x:0,y:0};data.cEnd={x:0,y:0};data.cScroll={x:0,y:0};data.cSpeed={x:0,y:0};data.tStart=new Date();data.dScrollH=null;data.dScrollV=null;data.touchStart=function touchStart(e){if(options.stopPropagation)e.stopPropagation();if(options.preventDefault)e.preventDefault();data.cCurrent={x:e.originalEvent.targetTouches[0].pageX,y:e.originalEvent.targetTouches[0].pageY};data.cStart={x:data.cCurrent.x,y:data.cCurrent.y};data.cEnd={x:data.cCurrent.x,y:data.cCurrent.y};data.cSpeed={x:data.cCurrent.x,y:data.cCurrent.y};if(gestures.scrollH||gestures.scrollV){data.tStart=new Date();data.cScroll={x:data.cCurrent.x,y:data.cCurrent.y};}};data.touchMove=function touchMove(e){if(options.stopPropagation)e.stopPropagation();if(options.preventDefault)e.preventDefault();data.cCurrent={x:e.originalEvent.targetTouches[0].pageX,y:e.originalEvent.targetTouches[0].pageY};data.cEnd.x=data.cCurrent.x;data.cEnd.y=data.cCurrent.y;var offset={x:data.cStart.x-data.cCurrent.x,y:data.cStart.y-data.cCurrent.y};if(gestures.scrollH||gestures.scrollV){var scrollOffset={x:data.cScroll.x-data.cCurrent.x,y:data.cScroll.y-data.cCurrent.y};var tCurrent=e.originalEvent.timeStamp||new Date();if(tCurrent-data.tStart>300){data.tStart=tCurrent;data.cSpeed.x=data.cCurrent.x;data.cSpeed.y=data.cCurrent.y;}}if(gestures.scrollH){if(Math.abs(offset.x)>options.scrollThreshold.x){if(scrollOffset.x>options.scrollPace.x||scrollOffset.x<(options.scrollPace.x*-1)){data.dScrollH=(scrollOffset.x>=0)?'left':'right';data.cScroll.x=data.cCurrent.x;options.scrollH(e,$(this),scrollOffset.x);}}}if(gestures.scrollV){if(Math.abs(offset.y)>options.scrollThreshold.y){if(scrollOffset.y>options.scrollPace.y||scrollOffset.y<(options.scrollPace.y*-1)){data.dScrollV=(scrollOffset.y>=0)?'up':'down';data.cScroll.y=data.cCurrent.y;options.scrollV(e,$(this),scrollOffset.y);}}}};data.touchEnd=function touchEnd(e){if(options.stopPropagation)e.stopPropagation();if(options.preventDefault)e.preventDefault();var offset={x:data.cStart.x-data.cEnd.x,y:data.cStart.y-data.cEnd.y};if(gestures.scrollH||gestures.scrollV){var duration=(e.originalEvent.timeStamp||new Date())-data.tStart;var momentum={h:{d:0,t:0},v:{d:0,t:0}};if(duration<300){momentum.h=generateMomentum(data.cEnd.x-data.cSpeed.x,duration);momentum.v=generateMomentum(data.cEnd.y-data.cSpeed.y,duration);data.cEnd.x+=momentum.h.distance;data.cEnd.y+=momentum.v.distance;}if(momentum.h.d||momentum.v.d){var tFinish=Math.max(Math.max(momentum.h.t,momentum.v.t),10);if(Math.abs(offset.x)>options.scrollThreshold.x)if(gestures.scrollH)options.momentumH(e,$(this),-momentum.h.d,tFinish);if(Math.abs(offset.y)>options.scrollThreshold.y)if(gestures.scrollV)options.momentumV(e,$(this),-momentum.v.d,tFinish);}}if(gestures.swipeH){if(offset.y<options.swipeLimit.y&&offset.y>(options.swipeLimit.y*-1)){offset.x=data.cStart.x-data.cEnd.x;if(offset.x>options.swipeTreshhold.x)options.swipeLeft(e,$(this));if(offset.x<(options.swipeTreshhold.x*-1))options.swipeRight(e,$(this));}}if(gestures.swipeV){if(offset.x<options.swipeLimit.x&&offset.x>(options.swipeLimit.x*-1)){offset.y=data.cStart.y-data.cEnd.y;if(offset.y>options.swipeTreshhold.y)options.swipeUp(e,$(this));if(offset.y<(options.swipeTreshhold.y*-1))options.swipeDown(e,$(this));}}if(gestures.tap){if(Math.abs(offset.x)<options.tapThreshhold.x&&Math.abs(offset.y)<options.tapThreshhold.y){options.tap(e,$(this));}}};data.touchCancel=function touchCancel(e){if(options.stopPropagation)e.stopPropagation();if(options.preventDefault)e.preventDefault();};data.element.on('touchstart',data.touchStart);data.element.on('touchmove',data.touchMove);data.element.on('touchend',data.touchEnd);data.element.on('touchcancel',data.touchCancel);function generateMomentum(distance,time){var deceleration=0.0006;var speed=Math.abs(distance)/time;return{d:(speed*speed)/(2*deceleration)*(distance<0?-1:1),t:Math.round(speed/deceleration)};};}});},destroy:function(){return this.each(function(){var self=$(this);var data=self.data('TouchSupport');data.element.off('touchstart',data.touchStart);data.element.off('touchmove',data.touchMove);data.element.off('touchend',data.touchEnd);data.element.off('touchcancel',data.touchCancel);});}};$.fn.TouchSupport=function(method){if(methods[method])return methods[method].apply(this,Array.prototype.slice.call(arguments,1));if(typeof method==='object'||!method)return methods.init.apply(this,arguments);$.error('The method "'+method+'" does not exist in jQuery.TouchSupport!');};})(jQuery);