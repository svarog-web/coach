(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbselect = $( $this ).data( 'cbselect' );

				if ( cbselect ) {
					return; // cbselect is already bound; so no need to rebind below
				}

				cbselect = {};
				cbselect.type = ( $( $this ).prop( 'multiple' ) ? 'multiple' : 'single' );
				cbselect.options = ( typeof options != 'undefined' ? options : {} );
				cbselect.defaults = $.fn.cbselect.defaults;
				cbselect.settings = $.extend( true, {}, cbselect.defaults, cbselect.options );
				cbselect.element = $( $this );

				if ( cbselect.settings.useData ) {
					$.each( cbselect.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbselect.element.data( 'cbselect' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbselect.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbselect.element.data( 'cbselect' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbselect.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbselect.element.triggerHandler( 'cbselect.init.before', [cbselect] );

				if ( ! cbselect.settings.init ) {
					return;
				}

				if ( cbselect.settings.tags ) {
					var separators = cbselect.settings.separator;

					if ( ! separators ) {
						separators = [','];
					}

					cbselect.settings.tokenSeparators = separators;
				}

				if ( ( cbselect.type == 'multiple' ) || cbselect.settings.tags ) {
					if ( cbselect.settings.width == 'calculate' ) {
						cbselect.settings.width = 'auto'
					}

					if ( cbselect.settings.height == 'calculate' ) {
						cbselect.settings.height = 'auto'
					}
				}

				var width = null;
				var height = null;

				if ( ( cbselect.settings.width == 'calculate' ) || ( cbselect.settings.height == 'calculate' ) ) {
					width = ( cbselect.element.outerWidth() + 50 );
					height = cbselect.element.outerHeight();

					if ( cbselect.element.is( ':hidden' ) ) {
						var cssWidth = cbselect.element.css( 'width' );
						var cssHeight = cbselect.element.css( 'height' );

						var temporary = cbselect.element.clone( false ).attr({
							id: '',
							'class': ''
						}).css({
							position: 'absolute',
							display: 'block',
							visibility: 'hidden',
							width: 'auto',
							height: 'auto',
							minWidth: ( cssWidth && ( cssWidth != '0px' ) ? cssWidth : 'auto' ),
							minHeight: ( cssHeight && ( cssHeight != '0px' ) ? cssHeight : 'auto' ),
							padding: cbselect.element.css( 'padding' ),
							border: cbselect.element.css( 'border' ),
							margin: cbselect.element.css( 'margin' ),
							fontFamily: cbselect.element.css( 'font-family' ),
							fontSize: cbselect.element.css( 'font-size' ),
							fontWeight: cbselect.element.css( 'font-weight' ),
							lineHeight: cbselect.element.css( 'line-height' ),
							boxSizing: cbselect.element.css( 'box-sizing' ),
							wordSpacing: cbselect.element.css( 'word-spacing' )
						}).appendTo( 'body' );

						width = ( temporary.outerWidth() + 50 );
						height = temporary.outerHeight();

						temporary.remove();
					}

					if ( cbselect.settings.width == 'calculate' ) {
						cbselect.settings.width = width;
					}

					if ( cbselect.settings.height == 'calculate' ) {
						cbselect.settings.height = height;
					}
				}

				if ( cbselect.settings.width == 'element' ) {
					width = cbselect.element.outerWidth();

					if ( ( ! width ) || ( width == '0px' ) ) {
						width = 'auto';
					}
				} else if ( ( cbselect.settings.width == 'copy' ) || ( cbselect.settings.width == 'resolve' ) ) {
					width = cbselect.element.css( 'width' );

					if ( ( ! width ) || ( width == '0px' ) ) {
						width = 'auto';
					}
				} else if ( cbselect.settings.width == 'off' ) {
					width = null;
				} else if ( ! width ) {
					width = cbselect.settings.width;
				}

				if ( cbselect.settings.height == 'element' ) {
					height = cbselect.element.outerHeight();

					if ( ( ! height ) || ( height == '0px' ) ) {
						height = 'auto';
					}
				} else if ( ( cbselect.settings.height == 'copy' ) || ( cbselect.settings.height == 'resolve' ) ) {
					height = cbselect.element.css( 'height' );

					if ( ( ! height ) || ( height == '0px' ) ) {
						height = 'auto';
					}
				} else if ( cbselect.settings.height == 'off' ) {
					height = null;
				} else if ( ! height ) {
					height = cbselect.settings.height;
				}

				var minWidth = null;

				if ( cbselect.type == 'multiple' ) {
					if ( ( width == 'auto' ) && cbselect.settings.placeholder ) {
						var placeholderSizer = $( '<span>' + cbselect.settings.placeholder + '</span>' ).css({
							position: 'absolute',
							display: 'block',
							visibility: 'hidden',
							padding: cbselect.element.css( 'padding' ),
							border: cbselect.element.css( 'border' ),
							margin: cbselect.element.css( 'margin' ),
							fontFamily: cbselect.element.css( 'font-family' ),
							fontSize: cbselect.element.css( 'font-size' ),
							fontWeight: cbselect.element.css( 'font-weight' ),
							lineHeight: cbselect.element.css( 'line-height' ),
							boxSizing: cbselect.element.css( 'box-sizing' ),
							wordSpacing: cbselect.element.css( 'word-spacing' )
						}).appendTo( 'body' );

						minWidth = ( placeholderSizer.outerWidth() + 5 );

						placeholderSizer.remove();
					}
				} else if ( cbselect.settings.placeholder ) {
					// This is mandatory for single select with placeholder:
					cbselect.settings.allowClear = true;
				}

				var hasTooltip = cbselect.element.is( '.cbTooltip,[data-hascbtooltip=\"true\"]' );

				if ( hasTooltip ) {
					cbselect.element.attr( 'data-cbtooltip-open-target', '#cbselect_' + cbselect.element.attr( 'id' ) );
					cbselect.element.attr( 'data-cbtooltip-close-target', '#cbselect_' + cbselect.element.attr( 'id' ) );
					cbselect.element.attr( 'data-cbtooltip-position-target', '#cbselect_' + cbselect.element.attr( 'id' ) );

					cbselect.element.data( 'cbtooltip-open-target', '#cbselect_' + cbselect.element.attr( 'id' ) );
					cbselect.element.data( 'cbtooltip-close-target', '#cbselect_' + cbselect.element.attr( 'id' ) );
					cbselect.element.data( 'cbtooltip-position-target', '#cbselect_' + cbselect.element.attr( 'id' ) );
				}

				var cssClasses = [];

				$.each( cbselect.element.attr( 'class' ).split( /\s+/ ), function( i, cssClass ) {
					if ( ( cssClass != 'cbTooltip' ) && ( cssClass != 'cbSelect' ) ) {
						cssClasses.push( cssClass );
					}
				});

				var select2Settings = $.extend( true, {}, cbselect.settings );

				delete select2Settings['separator'];
				delete select2Settings['containerCssClass'];
				delete select2Settings['dropdownCssClass'];

				cbselect.element.select2( select2Settings );
				cbselect.select2 = cbselect.element.data( 'select2' );

				cbselect.container = cbselect.select2.$container;
				cbselect.dropdown = cbselect.select2.$dropdown;

				if ( minWidth ) {
					cbselect.select2.$selection.css( 'min-width', minWidth );
				}

				$.each( cssClasses, function( i, cssClass ) {
					cbselect.container.addClass( cssClass );
				});

				if ( cbselect.type == 'multiple' ) {
					cbselect.dropdown.addClass( 'select2-container--multiple' );
					cbselect.container.addClass( 'select2-container--multiple' );
				} else {
					cbselect.dropdown.addClass( 'select2-container--single' );
					cbselect.container.addClass( 'select2-container--single' );
				}

				if ( cbselect.settings.tags ) {
					cbselect.dropdown.addClass( 'select2-container--tags' );
					cbselect.container.addClass( 'select2-container--tags' );
				}

				checkSelect2Empty( cbselect );

				cbselect.container.on( 'keyup', '.select2-search__field', function() {
					checkSelect2Empty( cbselect );
				});

				cbselect.element.on( 'select2:open', function() {
					checkSelect2Empty( cbselect );

					cbselect.element.triggerHandler( 'cbselect.open', [cbselect] );
				}).on( 'select2:close', function() {
					checkSelect2Empty( cbselect );

					cbselect.element.triggerHandler( 'cbselect.close', [cbselect] );
				}).on( 'select2:select', function( e ) {
					checkSelect2Empty( cbselect );

					if ( cbselect.settings.placeholder ) {
						// Ensure the placerholder is never selected:
						cbselect.element.cbselect( 'unset', cbselect.select2.selection.placeholder.id )
					}

					cbselect.element.triggerHandler( 'cbselect.selecting', [cbselect, e.params.data.id, e.params.data.element] );
				}).on( 'select2:unselect', function( e ) {
					checkSelect2Empty( cbselect );

					if ( cbselect.settings.placeholder && ( cbselect.element.cbselect( 'get' ) === null ) ) {
						// There's a placeholder and we've no value selected so we need to fallback to placeholder:
						cbselect.element.cbselect( 'set', cbselect.select2.selection.placeholder.id )
					}

					cbselect.element.triggerHandler( 'cbselect.removing', [cbselect, e.params.data.id, e.params.data.element] );
				});

				cbselect.container.attr( 'id', 'cbselect_' + cbselect.element.attr( 'id' ) );

				if ( height && ( height != '0px' ) ) {
					cbselect.container.css( 'height', height );
				}

				if ( cbselect.settings.containerCssClass ) {
					cbselect.container.addClass( cbselect.settings.containerCssClass );
				}

				if ( cbselect.settings.dropdownCssClass ) {
					cbselect.dropdown.addClass( cbselect.settings.dropdownCssClass );
				}

				cbselect.element.on( 'change', function() {
					if ( typeof cbParamChange != 'undefined' ) {
						cbParamChange.call( $this );
					}
				});

				// Destroy the cbselect element:
				cbselect.element.on( 'remove destroy.cbselect', function() {
					cbselect.element.cbselect( 'destroy' );
				});

				// Rebind the cbselect element to pick up any data attribute modifications:
				cbselect.element.on( 'rebind.cbselect', function() {
					cbselect.element.cbselect( 'rebind' );
				});

				// If the cbselect element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbselect.element.on( 'modified.cbselect', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbselect.element.cbselect( 'rebind' );
					}
				});

				// If the cbselect is cloned we need to rebind it back:
				cbselect.element.on( 'cloned.cbselect', function() {
					$( this ).off( 'destroy.cbselect' );
					$( this ).off( 'rebind.cbselect' );
					$( this ).off( 'cloned.cbselect' );
					$( this ).off( 'modified.cbselect' );
					$( this ).removeData( 'cbselect' );

					if ( hasTooltip ) {
						$( this ).removeAttr( 'data-cbtooltip-open-target' );
						$( this ).removeAttr( 'data-cbtooltip-close-target' );
						$( this ).removeAttr( 'data-cbtooltip-position-target' );

						$( this ).removeData( 'cbtooltip-open-target' );
						$( this ).removeData( 'cbtooltip-close-target' );
						$( this ).removeData( 'cbtooltip-position-target' );
					}

					$( this ).select2( 'close' );
					$( this ).removeData( 'select2' );
					$( this ).off( '.select2' );
					$( this ).removeClass( 'select2-hidden-accessible' );
					$( this ).siblings( '.select2-container' ).remove();
					$( this ).removeAttr( 'tabindex' );
					$( this ).removeData( 'old-tabindex' );

					$( this ).show();
					$( this ).cbselect( cbselect.options );
				});

				cbselect.element.triggerHandler( 'cbselect.init.after', [cbselect] );

				// Bind the cbselect to the element so it's reusable and chainable:
				cbselect.element.data( 'cbselect', cbselect );

				// Add this instance to our instance array so we can keep track of our select2 instances:
				instances.push( cbselect );
			});
		},
		open: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.select2( 'open' );

			return this;
		},
		close: function( value ) {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.select2( 'close' );

			return this;
		},
		get: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return null;
			}

			return cbselect.element.val();
		},
		set: function( value ) {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return null;
			}

			cbselect.element.val( value ).trigger( 'change' );

			return cbselect.element.cbselect( 'get' );
		},
		unset: function( value ) {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return null;
			}

			if ( ! $.isArray( value ) ) {
				value = [value];
			}

			var existingValue = cbselect.element.cbselect( 'get' );
			var newValue = existingValue;
			var isChanged = false;

			if ( $.isArray( existingValue ) ) {
				$.each( value, function( i, v ) {
					if ( newValue.indexOf( v ) > -1 ) {
						newValue.splice( newValue.indexOf( v ), 1 );

						isChanged = true;
					}
				});
			} else {
				$.each( value, function( i, v ) {
					if ( v === existingValue ) {
						newValue = '';

						isChanged = true;

						return false;
					}
				});
			}

			if ( isChanged ) {
				return cbselect.element.cbselect( 'set', newValue );
			} else {
				// Don't bother firing the change event and changing the value if it didn't change:
				return existingValue;
			}
		},
		enable: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.prop( 'disabled', false );

			return this;
		},
		disable: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.prop( 'disabled', true );

			return this;
		},
		container: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			return cbselect.container;
		},
		dropdown: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			return cbselect.dropdown;
		},
		rebind: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.cbselect( 'destroy' );
			cbselect.element.cbselect( cbselect.options );

			return this;
		},
		destroy: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			if ( cbselect.element.is( '.cbTooltip,[data-hascbtooltip=\"true\"]' ) ) {
				cbselect.element.removeAttr( 'data-cbtooltip-open-target' );
				cbselect.element.removeAttr( 'data-cbtooltip-close-target' );
				cbselect.element.removeAttr( 'data-cbtooltip-position-target' );

				cbselect.element.removeData( 'cbtooltip-open-target' );
				cbselect.element.removeData( 'cbtooltip-close-target' );
				cbselect.element.removeData( 'cbtooltip-position-target' );
			}

			cbselect.element.select2( 'destroy' );
			cbselect.element.off( 'destroy.cbselect' );
			cbselect.element.off( 'rebind.cbselect' );
			cbselect.element.off( 'cloned.cbselect' );
			cbselect.element.off( 'modified.cbselect' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbselect.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			cbselect.element.removeData( 'cbselect' );
			cbselect.element.triggerHandler( 'cbselect.destroyed', [cbselect] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	function checkSelect2Empty( cbselect ) {
		if ( ! cbselect.element.children().length ) {
			cbselect.dropdown.addClass( 'select2-container--empty' );
			cbselect.container.addClass( 'select2-container--empty' );
		} else {
			cbselect.dropdown.removeClass( 'select2-container--empty' );
			cbselect.container.removeClass( 'select2-container--empty' );
		}
	}

	$.fn.cbselect = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbselect.defaults = {
		init: true,
		useData: true,
		tags: false,
		separator: null,
		placeholder: null,
		closeOnSelect: true,
		selectOnClose: false,
		minimumInputLength: 0,
		maximumInputLength: 0,
		maximumSelectionLength: 0,
		minimumResultsForSearch: 0,
		dropdownCssClass: null,
		containerCssClass: null,
		dropdownAutoWidth: true,
		theme: 'bootstrap',
		width: 'calculate',
		height: 'off'
	};
})(jQuery);