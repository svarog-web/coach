(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbmoreless = $( $this ).data( 'cbmoreless' );

				if ( cbmoreless ) {
					return; // cbmoreless is already bound; so no need to rebind below
				}

				cbmoreless = {};
				cbmoreless.options = ( typeof options != 'undefined' ? options : {} );
				cbmoreless.defaults = $.fn.cbmoreless.defaults;
				cbmoreless.settings = $.extend( true, {}, cbmoreless.defaults, cbmoreless.options );
				cbmoreless.element = $( $this );

				if ( cbmoreless.settings.useData ) {
					$.each( cbmoreless.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbmoreless.element.data( 'cbmoreless' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbmoreless.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbmoreless.element.data( 'cbmoreless' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbmoreless.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbmoreless.element.triggerHandler( 'cbmoreless.init.before', [cbmoreless] );

				if ( ! cbmoreless.settings.init ) {
					return;
				}

				if ( ! cbmoreless.settings.height ) {
					cbmoreless.settings.height = 100;
				}

				if ( cbmoreless.element.children( '.cbMoreLessOpen.fade-edge,.cbMoreLessClose.fade-edge' ).length ) {
					// Only bother with edge fading if specified:
					var fadeColor = null;

					cbmoreless.element.parents().each( function() {
						var bgColor = $( this ).css( 'background-color' );

						if ( bgColor && ( ( bgColor != '' ) && ( bgColor != 'transparent' ) && bgColor != 'rgba(0, 0, 0, 0)' ) ) {
							fadeColor = bgColor;

							return false;
						}
					});

					if ( fadeColor ) {
						cbmoreless.element.children( '.cbMoreLessOpen.fade-edge,.cbMoreLessClose.fade-edge' ).css( 'background-image', 'linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, ' + fadeColor + ' 100%)' );
						cbmoreless.element.children( '.cbMoreLessOpen,.cbMoreLessClose' ).find( '.cbMoreLessButton' ).css( 'background-color', fadeColor );
					}
				}

				cbmoreless.openHandler = function( e ) {
					e.preventDefault();

					openToggle.call( $this, cbmoreless );
				};

				cbmoreless.closeHandler = function( e ) {
					e.preventDefault();

					closeToggle.call( $this, cbmoreless );
				};

				cbmoreless.element.children( '.cbMoreLessOpen' ).on( 'click', cbmoreless.openHandler );
				cbmoreless.element.children( '.cbMoreLessClose' ).on( 'click', cbmoreless.closeHandler );

				if ( ! cbmoreless.element.hasClass( 'cbMoreLessOpened' ) ) {
					cbmoreless.element.removeClass( 'cbMoreLessClosed' );
					cbmoreless.element.children( '.cbMoreLessOpen,.cbMoreLessClose' ).addClass( 'hidden' );

					var content = cbmoreless.element.children( '.cbMoreLessContent' );

					cbmoreless.height = content.height();

					if ( cbmoreless.settings.height ) {
						content.css( 'max-height', cbmoreless.settings.height );
						content.css( 'height', cbmoreless.settings.height );
					}

					if ( typeof content.get( 0 ) != 'undefined' ) {
						var height = content.height();
						var scrollHeight = content.prop( 'scrollHeight' );

						if ( content.is( ':hidden' ) && ( scrollHeight == 0 ) ) {
							// The element is hidden and has no scroll height so we need to make its parents temporarily visible to grab the scroll height:
							var parents = content.parents( ':hidden' );

							parents.each( function() {
								$( this ).addClass( 'cbForceDisplay' );
							});

							height = content.height();
							scrollHeight = content.prop( 'scrollHeight' );

							parents.each( function() {
								$( this ).removeClass( 'cbForceDisplay' );
							});
						}

						cbmoreless.height = scrollHeight;

						if ( height < scrollHeight ) {
							closeToggle.call( this, cbmoreless );
						} else {
							openToggle.call( this, cbmoreless );
						}
					} else {
						openToggle.call( this, cbmoreless );
					}

					if ( cbmoreless.settings.height ) {
						content.css( 'height', '' );
					}
				}

				// Destroy the cbmoreless element:
				cbmoreless.element.on( 'remove destroy.cbmoreless', function() {
					cbmoreless.element.cbmoreless( 'destroy' );
				});

				// Rebind the cbmoreless element to pick up any data attribute modifications:
				cbmoreless.element.on( 'rebind.cbmoreless', function() {
					cbmoreless.element.cbmoreless( 'rebind' );
				});

				// If the cbmoreless element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbmoreless.element.on( 'modified.cbmoreless', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbmoreless.element.cbmoreless( 'rebind' );
					}
				});

				// If the cbmoreless is cloned we need to rebind it back:
				cbmoreless.element.on( 'cloned.cbmoreless', function() {
					if ( cbmoreless.settings.height ) {
						$( this ).children( '.cbMoreLessContent' ).css( 'max-height', 'none' );
					}

					$( this ).off( 'destroy.cbmoreless' );
					$( this ).off( 'rebind.cbmoreless' );
					$( this ).off( 'modified.cbmoreless' );
					$( this ).off( 'cloned.cbmoreless' );
					$( this ).children( '.cbMoreLessOpen' ).off( 'click', cbmoreless.openHandler ).addClass( 'hidden' );
					$( this ).children( '.cbMoreLessClose' ).off( 'click', cbmoreless.closeHandler ).addClass( 'hidden' );
					$( this ).removeClass( 'cbMoreLessOpened cbMoreLessClosed' );
					$( this ).removeData( 'cbmoreless' );
					$( this ).cbmoreless( cbmoreless.options );
				});

				cbmoreless.element.triggerHandler( 'cbmoreless.init.after', [cbmoreless] );

				// Bind the cbmoreless to the element so it's reusable and chainable:
				cbmoreless.element.data( 'cbmoreless', cbmoreless );

				// Add this instance to our instance array so we can keep track of our cbmoreless instances:
				instances.push( cbmoreless );
			});
		},
		open: function() {
			var cbmoreless = $( this ).data( 'cbmoreless' );

			if ( ! cbmoreless ) {
				return this;
			}

			openToggle.call( this, cbmoreless );

			return this;
		},
		close: function() {
			var cbmoreless = $( this ).data( 'cbmoreless' );

			if ( ! cbmoreless ) {
				return this;
			}

			closeToggle.call( this, cbmoreless );

			return this;
		},
		rebind: function() {
			var cbmoreless = $( this ).data( 'cbmoreless' );

			if ( ! cbmoreless ) {
				return this;
			}

			cbmoreless.element.cbmoreless( 'destroy' );
			cbmoreless.element.cbmoreless( cbmoreless.options );

			return this;
		},
		destroy: function() {
			var cbmoreless = $( this ).data( 'cbmoreless' );

			if ( ! cbmoreless ) {
				return this;
			}

			if ( cbmoreless.settings.height ) {
				cbmoreless.element.children( '.cbMoreLessContent' ).css( 'max-height', 'none' );
			}

			cbmoreless.element.off( 'destroy.cbmoreless' );
			cbmoreless.element.off( 'rebind.cbmoreless' );
			cbmoreless.element.off( 'modified.cbmoreless' );
			cbmoreless.element.off( 'cloned.cbmoreless' );
			cbmoreless.element.children( '.cbMoreLessOpen' ).off( 'click', cbmoreless.openHandler ).addClass( 'hidden' );
			cbmoreless.element.children( '.cbMoreLessClose' ).off( 'click', cbmoreless.closeHandler ).addClass( 'hidden' );
			cbmoreless.element.removeClass( 'cbMoreLessOpened cbMoreLessClosed' );
			cbmoreless.element.removeData( 'cbmoreless' );
			cbmoreless.element.triggerHandler( 'cbmoreless.destroyed', [cbmoreless] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	function openToggle( cbmoreless ) {
		if ( cbmoreless.settings.height ) {
			var height = 'none';

			if ( cbmoreless.settings.stepped ) {
				height = '+=' + cbmoreless.settings.height + 'px';
			}

			cbmoreless.element.children( '.cbMoreLessContent' ).css( 'max-height', height );
		}

		if ( cbmoreless.settings.stepped ) {
			var currentHeight = cbmoreless.element.children( '.cbMoreLessContent' ).height();

			if ( currentHeight >= cbmoreless.height ) {
				cbmoreless.element.removeClass( 'cbMoreLessClosed cbMoreLessStepped' ).addClass( 'cbMoreLessOpened' );
				cbmoreless.element.children( '.cbMoreLessOpen' ).addClass( 'hidden' );
				cbmoreless.element.children( '.cbMoreLessClose' ).removeClass( 'hidden' );
			} else if ( currentHeight <= cbmoreless.height ) {
				cbmoreless.element.removeClass( 'cbMoreLessOpened cbMoreLessClosed' ).addClass( 'cbMoreLessStepped' );
				cbmoreless.element.children( '.cbMoreLessClose' ).removeClass( 'hidden' );
			}
		} else {
			cbmoreless.element.removeClass( 'cbMoreLessClosed' ).addClass( 'cbMoreLessOpened' );
			cbmoreless.element.children( '.cbMoreLessOpen' ).addClass( 'hidden' );
			cbmoreless.element.children( '.cbMoreLessClose' ).removeClass( 'hidden' );
		}
	}

	function closeToggle( cbmoreless ) {
		if ( cbmoreless.settings.height ) {
			var height = cbmoreless.settings.height;

			if ( cbmoreless.settings.stepped && ( cbmoreless.element.children( '.cbMoreLessContent' ).height() > cbmoreless.settings.height ) ) {
				height = '-=' + cbmoreless.settings.height + 'px';
			}

			cbmoreless.element.children( '.cbMoreLessContent' ).css( 'max-height', height );
		}

		if ( cbmoreless.settings.stepped ) {
			var currentHeight = cbmoreless.element.children( '.cbMoreLessContent' ).height();

			if ( currentHeight <= cbmoreless.settings.height ) {
				cbmoreless.element.removeClass( 'cbMoreLessOpened cbMoreLessStepped' ).addClass( 'cbMoreLessClosed' );
				cbmoreless.element.children( '.cbMoreLessOpen' ).removeClass( 'hidden' );
				cbmoreless.element.children( '.cbMoreLessClose' ).addClass( 'hidden' );
			} else if ( currentHeight <= cbmoreless.height ) {
				cbmoreless.element.removeClass( 'cbMoreLessOpened cbMoreLessClosed' ).addClass( 'cbMoreLessStepped' );
				cbmoreless.element.children( '.cbMoreLessOpen' ).removeClass( 'hidden' );
			}
		} else {
			cbmoreless.element.removeClass( 'cbMoreLessOpened' ).addClass( 'cbMoreLessClosed' );
			cbmoreless.element.children( '.cbMoreLessOpen' ).removeClass( 'hidden' );
			cbmoreless.element.children( '.cbMoreLessClose' ).addClass( 'hidden' );
		}
	}

	$.fn.cbmoreless = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbmoreless.defaults = {
		init: true,
		useData: true,
		stepped: false,
		height: 100
	};
})(jQuery);