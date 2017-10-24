(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbcodefield = $( $this ).data( 'cbcodefield' );

				if ( cbcodefield ) {
					return; // cbcodefield is already bound; so no need to rebind below
				}

				cbcodefield = {};
				cbcodefield.options = options;
				cbcodefield.defaults = $.fn.cbcodefield.defaults;
				cbcodefield.settings = $.extend( true, {}, cbcodefield.defaults, cbcodefield.options );
				cbcodefield.element = $( $this );

				if ( cbcodefield.settings.useData ) {
					$.each( cbcodefield.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbcodefield.element.data( 'cbcodefield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbcodefield.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbcodefield.element.data( 'cbcodefield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbcodefield.settings[key] = dataValue;
								}
							}
						}
					});
				}

				if ( ! ( cbcodefield.settings.selectors || $( cbcodefield.settings.selectors ).length ) ) {
					return;
				}

				cbcodefield.element.triggerHandler( 'cbcodefield.init.before', [cbcodefield] );

				if ( ! cbcodefield.settings.init ) {
					return;
				}

				if ( ( cbcodefield.settings.post === null ) || ( ( typeof cbcodefield.settings.post != 'object' ) && ( ! $.isArray( cbcodefield.settings.post ) ) ) ) {
					cbcodefield.settings.post = {};
				}

				cbcodefield.target = findTarget.call( cbcodefield.element );
				cbcodefield.timer = null;

				var valueCache = {};

				cbcodefield.changeHandler = function( event ) {
					// Ignore any change events that don't have code update bindings:
					if ( typeof event.data.codeUpdateTarget == 'undefined' ) {
						return;
					}

					var name = $( this ).attr( 'name' );
					var value = getValue.call( event.data.codeUpdateTarget );

					if ( ! name.length ) {
						return;
					}

					var targetName = cbcodefield.target.attr( 'name' );
					var targetValue = getValue.call( cbcodefield.target );

					var post = cbcodefield.settings.post;

					post[name] = value;

					if ( targetName.length ) {
						post[targetName] = targetValue;
					}

					var timerDelay = 400;

					if ( event.type == 'keyup' ) {
						timerDelay = 800;
					}

					if ( cbcodefield.timer ) {
						clearTimeout( cbcodefield.timer );
					}

					cbcodefield.timer = setTimeout( function() {
						if ( typeof valueCache[name] != 'undefined' ) {
							var oldValue = ( $.isArray( valueCache[name] ) ? valueCache[name].toString() : valueCache[name] );
							var newValue = ( $.isArray( value ) ? value.toString() : value );

							if ( oldValue === newValue ) {
								return;
							}
						}

						valueCache[name] = value;

						$.ajax({
							url: cbcodefield.settings.url,
							type: 'POST',
							dataType: 'html',
							cache: false,
							data: post,
							beforeSend: function( jqXHR, textStatus, errorThrown ) {
								cbcodefield.element.find( '.cbUpdateOptions' ).remove();
								cbcodefield.target.after( '<span class="cbFieldIcons cbUpdateOptions"><span class="cbSpinner fa fa-spinner fa-spin-fast"></span></span>' );

								cbcodefield.element.triggerHandler( 'cbcodefield.edit.send', [cbcodefield, jqXHR, textStatus, errorThrown] );
							},
							error: function( jqXHR, textStatus, errorThrown ) {
								cbcodefield.element.find( '.cbUpdateOptions' ).remove();

								cbcodefield.element.triggerHandler( 'cbcodefield.edit.error', [cbcodefield, jqXHR, textStatus, errorThrown] );
							},
							success: function( data, textStatus, jqXHR ) {
								cbcodefield.element.find( '.cbUpdateOptions' ).remove();

								var dataHtml = $( '<div />' ).html( data );

								// Only replace options for select inputs:
								if ( cbcodefield.target.is( 'select' ) ) {
									cbcodefield.target.empty().append( dataHtml.find( 'select:first' ).children() );
								}

								cbcodefield.element.triggerHandler( 'cbcodefield.edit.success', [cbcodefield, data, textStatus, jqXHR] );
							}
						});
					}, timerDelay );
				};

				$( cbcodefield.settings.selectors ).each( function() {
					var target = findTarget.call( this );

					target.on( 'keyup change', {codeUpdateTarget: target}, cbcodefield.changeHandler );
				});

				// Destroy the cbcodefield element:
				cbcodefield.element.on( 'remove destroy.cbcodefield', function() {
					cbcodefield.element.cbcodefield( 'destroy' );
				});

				// Rebind the cbcodefield element to pick up any data attribute modifications:
				cbcodefield.element.on( 'rebind.cbcodefield', function() {
					cbcodefield.element.cbcodefield( 'rebind' );
				});

				// If the cbcodefield element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbcodefield.element.on( 'modified.cbcodefield', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbcodefield.element.cbcodefield( 'rebind' );
					}
				});

				// If the cbcodefield is cloned we need to rebind it back:
				cbcodefield.element.on( 'cloned.cbcodefield', function( e, oldId ) {
					destroyPlugin.call( this, cbcodefield );

					$( this ).cbcodefield( cbcodefield.options );
				});

				cbcodefield.element.triggerHandler( 'cbcodefield.init.after', [cbcodefield] );

				// Bind the cbcodefield to the element so it's reusable and chainable:
				cbcodefield.element.data( 'cbcodefield', cbcodefield );

				// Add this instance to our instance array so we can keep track of our cbcodefield instances:
				instances.push( cbcodefield );
			});
		},
		rebind: function() {
			var cbcodefield = $( this ).data( 'cbcodefield' );

			if ( ! cbcodefield ) {
				return this;
			}

			cbcodefield.element.cbcodefield( 'destroy' );
			cbcodefield.element.cbcodefield( cbcodefield.options );

			return this;
		},
		destroy: function() {
			var cbcodefield = $( this ).data( 'cbcodefield' );

			if ( ! cbcodefield ) {
				return false;
			}

			destroyPlugin.call( cbcodefield.element, cbcodefield );

			cbcodefield.element.triggerHandler( 'cbcodefield.destroyed', [cbcodefield] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function destroyPlugin( cbcodefield ) {
		var element = ( this.jquery ? this : $( this ) );

		element.off( 'destroy.cbcodefield' );
		element.off( 'rebind.cbcodefield' );
		element.off( 'cloned.cbcodefield' );
		element.off( 'modified.cbcodefield' );
		element.removeData( 'cbcodefield' );

		if ( cbcodefield.timer ) {
			clearTimeout( cbcodefield.timer );
		}

		$( cbcodefield.settings.selectors ).each( function() {
			findTarget.call( this ).off( 'keyup change', cbcodefield.changeHandler );
		});

		$.each( instances, function( i, instance ) {
			if ( instance.element == element ) {
				instances.splice( i, 1 );

				return false;
			}

			return true;
		});
	}

	function findTarget() {
		var element = ( this.jquery ? this : $( this ) );
		var target = null;

		if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
			target = element;
		} else {
			target = element.find( 'input,select,textarea' ).first();

			if ( target.is( ':checkbox' ) || target.is( ':radio' ) ) {
				target = element.find( 'input[name="' + target.attr( 'name' ) + '"]' );
			}
		}

		return target;
	}

	function getValue() {
		var element = ( this.jquery ? this : $( this ) );
		var value = null;

		if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
			if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
				value = [];

				element.each( function() {
					if ( $( this ).is( ':checked' ) ) {
						value.push( $( this ).val() );
					}
				});
			} else if ( element.is( 'select[multiple]' ) ) {
				value = element.val();

				if ( value && ( ! $.isArray( value ) ) ) {
					value = value.split( ',' );
				}
			} else {
				value = element.val();
			}
		}

		return value;
	}

	$.fn.cbcodefield = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.cbcodefield = function( options ) {
		if ( options == 'instances' ) {
			return instances;
		}

		return this;
	};

	$.fn.cbcodefield.defaults = {
		init: true,
		useData: false,
		selectors: null,
		url: null,
		post: null
	};
})(jQuery);