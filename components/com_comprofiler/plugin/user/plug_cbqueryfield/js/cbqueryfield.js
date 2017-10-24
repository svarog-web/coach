(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbqueryfield = $( $this ).data( 'cbqueryfield' );

				if ( cbqueryfield ) {
					return; // cbqueryfield is already bound; so no need to rebind below
				}

				cbqueryfield = {};
				cbqueryfield.options = options;
				cbqueryfield.defaults = $.fn.cbqueryfield.defaults;
				cbqueryfield.settings = $.extend( true, {}, cbqueryfield.defaults, cbqueryfield.options );
				cbqueryfield.element = $( $this );

				if ( cbqueryfield.settings.useData ) {
					$.each( cbqueryfield.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbqueryfield.element.data( 'cbqueryfield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbqueryfield.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbqueryfield.element.data( 'cbqueryfield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbqueryfield.settings[key] = dataValue;
								}
							}
						}
					});
				}

				if ( ! ( cbqueryfield.settings.selectors || $( cbqueryfield.settings.selectors ).length ) ) {
					return;
				}

				cbqueryfield.element.triggerHandler( 'cbqueryfield.init.before', [cbqueryfield] );

				if ( ! cbqueryfield.settings.init ) {
					return;
				}

				if ( ( cbqueryfield.settings.post === null ) || ( ( typeof cbqueryfield.settings.post != 'object' ) && ( ! $.isArray( cbqueryfield.settings.post ) ) ) ) {
					cbqueryfield.settings.post = {};
				}

				cbqueryfield.target = findTarget.call( cbqueryfield.element );
				cbqueryfield.timer = null;

				var valueCache = {};

				cbqueryfield.changeHandler = function( event ) {
					// Ignore any change events that don't have query update bindings:
					if ( typeof event.data.queryUpdateTarget == 'undefined' ) {
						return;
					}

					var name = $( this ).attr( 'name' );
					var value = getValue.call( event.data.queryUpdateTarget );

					if ( ! name.length ) {
						return;
					}

					var targetName = cbqueryfield.target.attr( 'name' );
					var targetValue = getValue.call( cbqueryfield.target );

					var post = cbqueryfield.settings.post;

					post[name] = value;

					if ( targetName.length ) {
						post[targetName] = targetValue;
					}

					var timerDelay = 400;

					if ( event.type == 'keyup' ) {
						timerDelay = 800;
					}

					if ( cbqueryfield.timer ) {
						clearTimeout( cbqueryfield.timer );
					}

					cbqueryfield.timer = setTimeout( function() {
						if ( typeof valueCache[name] != 'undefined' ) {
							var oldValue = ( $.isArray( valueCache[name] ) ? valueCache[name].toString() : valueCache[name] );
							var newValue = ( $.isArray( value ) ? value.toString() : value );

							if ( oldValue === newValue ) {
								return;
							}
						}

						valueCache[name] = value;

						$.ajax({
							url: cbqueryfield.settings.url,
							type: 'POST',
							dataType: 'html',
							cache: false,
							data: post,
							beforeSend: function( jqXHR, textStatus, errorThrown ) {
								cbqueryfield.element.find( '.cbUpdateOptions' ).remove();
								cbqueryfield.target.after( '<span class="cbFieldIcons cbUpdateOptions"><span class="cbSpinner fa fa-spinner fa-spin-fast"></span></span>' );

								cbqueryfield.element.triggerHandler( 'cbqueryfield.edit.send', [cbqueryfield, jqXHR, textStatus, errorThrown] );
							},
							error: function( jqXHR, textStatus, errorThrown ) {
								cbqueryfield.element.find( '.cbUpdateOptions' ).remove();

								cbqueryfield.element.triggerHandler( 'cbqueryfield.edit.error', [cbqueryfield, jqXHR, textStatus, errorThrown] );
							},
							success: function( data, textStatus, jqXHR ) {
								cbqueryfield.element.find( '.cbUpdateOptions' ).remove();

								var dataHtml = $( '<div />' ).html( data );

								// Only replace options for select inputs:
								if ( cbqueryfield.target.is( 'select' ) ) {
									cbqueryfield.target.empty().append( dataHtml.find( 'select:first' ).children() );
								}

								cbqueryfield.element.triggerHandler( 'cbqueryfield.edit.success', [cbqueryfield, data, textStatus, jqXHR] );
							}
						});
					}, timerDelay );
				};

				$( cbqueryfield.settings.selectors ).each( function() {
					var target = findTarget.call( this );

					target.on( 'keyup change', {queryUpdateTarget: target}, cbqueryfield.changeHandler );
				});

				// Destroy the cbqueryfield element:
				cbqueryfield.element.on( 'remove destroy.cbqueryfield', function() {
					cbqueryfield.element.cbqueryfield( 'destroy' );
				});

				// Rebind the cbqueryfield element to pick up any data attribute modifications:
				cbqueryfield.element.on( 'rebind.cbqueryfield', function() {
					cbqueryfield.element.cbqueryfield( 'rebind' );
				});

				// If the cbqueryfield element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbqueryfield.element.on( 'modified.cbqueryfield', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbqueryfield.element.cbqueryfield( 'rebind' );
					}
				});

				// If the cbqueryfield is cloned we need to rebind it back:
				cbqueryfield.element.on( 'cloned.cbqueryfield', function( e, oldId ) {
					destroyPlugin.call( this, cbqueryfield );

					$( this ).cbqueryfield( cbqueryfield.options );
				});

				cbqueryfield.element.triggerHandler( 'cbqueryfield.init.after', [cbqueryfield] );

				// Bind the cbqueryfield to the element so it's reusable and chainable:
				cbqueryfield.element.data( 'cbqueryfield', cbqueryfield );

				// Add this instance to our instance array so we can keep track of our cbqueryfield instances:
				instances.push( cbqueryfield );
			});
		},
		rebind: function() {
			var cbqueryfield = $( this ).data( 'cbqueryfield' );

			if ( ! cbqueryfield ) {
				return this;
			}

			cbqueryfield.element.cbqueryfield( 'destroy' );
			cbqueryfield.element.cbqueryfield( cbqueryfield.options );

			return this;
		},
		destroy: function() {
			var cbqueryfield = $( this ).data( 'cbqueryfield' );

			if ( ! cbqueryfield ) {
				return false;
			}

			destroyPlugin.call( cbqueryfield.element, cbqueryfield );

			cbqueryfield.element.triggerHandler( 'cbqueryfield.destroyed', [cbqueryfield] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function destroyPlugin( cbqueryfield ) {
		var element = ( this.jquery ? this : $( this ) );

		element.off( 'destroy.cbqueryfield' );
		element.off( 'rebind.cbqueryfield' );
		element.off( 'cloned.cbqueryfield' );
		element.off( 'modified.cbqueryfield' );
		element.removeData( 'cbqueryfield' );

		if ( cbqueryfield.timer ) {
			clearTimeout( cbqueryfield.timer );
		}

		$( cbqueryfield.settings.selectors ).each( function() {
			findTarget.call( this ).off( 'keyup change', cbqueryfield.changeHandler );
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

	$.fn.cbqueryfield = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.cbqueryfield = function( options ) {
		if ( options == 'instances' ) {
			return instances;
		}

		return this;
	};

	$.fn.cbqueryfield.defaults = {
		init: true,
		useData: false,
		selectors: null,
		url: null,
		post: null
	};
})(jQuery);