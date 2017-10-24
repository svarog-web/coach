(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbcondition = $( $this ).data( 'cbcondition' );

				if ( cbcondition ) {
					if ( options.conditions ) {
						cbcondition.element.cbcondition( 'add', options.conditions );
					}

					return; // cbcondition is already bound; so no need to rebind below
				}

				cbcondition = {};
				cbcondition.options = options;
				cbcondition.defaults = $.fn.cbcondition.defaults;
				cbcondition.settings = $.extend( true, {}, cbcondition.defaults, cbcondition.options );
				cbcondition.element = $( $this );

				if ( cbcondition.settings.useData ) {
					$.each( cbcondition.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbcondition.element.data( 'cbcondition' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbcondition.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbcondition.element.data( 'cbcondition' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbcondition.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbcondition.element.triggerHandler( 'cbcondition.init.before', [cbcondition] );

				if ( ! cbcondition.settings.init ) {
					return;
				}

				if ( ! $.isArray( cbcondition.settings.conditions ) ) {
					cbcondition.settings.conditions = [];
				}

				cbcondition.target = null;

				if ( cbcondition.element.is( 'input' ) || cbcondition.element.is( 'select' ) || cbcondition.element.is( 'textarea' ) ) {
					cbcondition.target = cbcondition.element;
				} else {
					cbcondition.target = cbcondition.element.find( 'input,select,textarea' ).first();

					if ( cbcondition.target.is( ':checkbox' ) || cbcondition.target.is( ':radio' ) ) {
						cbcondition.target = cbcondition.element.find( 'input[name="' + cbcondition.target.attr( 'name' ) + '"]' );
					}
				}

				cbcondition.changeHandler = function() {
					conditionElement.call( this, cbcondition );
				};

				cbcondition.target.on( 'keyup change condition', cbcondition.changeHandler );

				conditionElement.call( cbcondition.target, cbcondition );

				// Destroy the cbcondition element:
				cbcondition.element.on( 'remove destroy.cbcondition', function() {
					cbcondition.element.cbcondition( 'destroy' );
				});

				// Rebind the cbcondition element to pick up any data attribute modifications:
				cbcondition.element.on( 'rebind.cbcondition', function() {
					cbcondition.element.cbcondition( 'rebind' );
				});

				// If the cbcondition element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbcondition.element.on( 'modified.cbcondition', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbcondition.element.cbcondition( 'destroy' );
						cbcondition.element.cbcondition( cbcondition.options );
					}
				});

				// If the cbcondition is cloned we need to rebind it back:
				cbcondition.element.on( 'cloned.cbcondition', function( e, oldId ) {
					$( this ).off( 'destroy.cbcondition' );
					$( this ).off( 'rebind.cbcondition' );
					$( this ).off( 'cloned.cbcondition' );
					$( this ).off( 'modified.cbcondition' );
					$( this ).removeData( 'cbcondition' );

					var target = null;

					if ( $( this ).is( 'input' ) || $( this ).is( 'select' ) || $( this ).is( 'textarea' ) ) {
						target = $( this );
					} else {
						target = $( this ).find( 'input,select,textarea' ).first();

						if ( target.is( ':checkbox' ) || target.is( ':radio' ) ) {
							target = $( this ).find( 'input[name="' + target.attr( 'name' ) + '"]' );
						}
					}

					target.off( 'keyup change condition', cbcondition.changeHandler );

					$( this ).cbcondition( cbcondition.options );
				});

				cbcondition.element.triggerHandler( 'cbcondition.init.after', [cbcondition] );

				// Bind the cbcondition to the element so it's reusable and chainable:
				cbcondition.element.data( 'cbcondition', cbcondition );

				// Add this instance to our instance array so we can keep track of our cbcondition instances:
				instances.push( cbcondition );
			});
		},
		match: function( match ) {
			var cbcondition = $( this ).data( 'cbcondition' );

			if ( ! cbcondition ) {
				return false;
			}

			cbcondition.target.each( function() {
				conditionElement.call( this, cbcondition, match );
			});

			return true;
		},
		add: function( conditions ) {
			if ( ( ! conditions ) || ( ! $.isArray( conditions ) ) ) {
				return false;
			}

			var cbcondition = $( this ).data( 'cbcondition' );

			if ( ! cbcondition ) {
				return false;
			}

			cbcondition.options.conditions = $.merge( cbcondition.options.conditions, conditions );
			cbcondition.settings.conditions = $.merge( cbcondition.settings.conditions, conditions );

			cbcondition.element.data( 'cbcondition', cbcondition );

			conditionElement.call( cbcondition.target, cbcondition );

			cbcondition.element.triggerHandler( 'cbcondition.add', [cbcondition, conditions] );

			return true;
		},
		remove: function( index ) {
			var cbcondition = $( this ).data( 'cbcondition' );

			if ( ! cbcondition ) {
				return false;
			}

			var condition = null;

			if ( $.isArray( cbcondition.options.conditions ) && ( index in cbcondition.options.conditions ) ) {
				condition = cbcondition.options.conditions[index];

				cbcondition.options.conditions.splice( index, 1 );
			}

			if ( $.isArray( cbcondition.settings.conditions ) && ( index in cbcondition.settings.conditions ) ) {
				condition = cbcondition.settings.conditions[index];

				cbcondition.settings.conditions.splice( index, 1 );
			}

			if ( ! condition ) {
				return false;
			}

			revertCondition.call( this, cbcondition, condition );

			cbcondition.element.data( 'cbcondition', cbcondition );
			cbcondition.element.triggerHandler( 'cbcondition.remove', [cbcondition, condition] );

			return true;
		},
		rebind: function() {
			var cbcondition = $( this ).data( 'cbcondition' );

			if ( ! cbcondition ) {
				return this;
			}

			cbcondition.element.cbcondition( 'destroy' );
			cbcondition.element.cbcondition( cbcondition.options );

			return this;
		},
		destroy: function() {
			var cbcondition = $( this ).data( 'cbcondition' );

			if ( ! cbcondition ) {
				return false;
			}

			cbcondition.element.off( 'destroy.cbcondition' );
			cbcondition.element.off( 'rebind.cbcondition' );
			cbcondition.element.off( 'cloned.cbcondition' );
			cbcondition.element.off( 'modified.cbcondition' );
			cbcondition.target.off( 'keyup change condition', cbcondition.changeHandler );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbcondition.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			var conditions = cbcondition.settings.conditions;

			if ( $.isArray( conditions ) ) {
				var $this = this;

				$.each( conditions, function( i, condition ) {
					revertCondition.call( $this, cbcondition, condition );
				});
			}

			cbcondition.element.removeData( 'cbcondition' );
			cbcondition.element.triggerHandler( 'cbcondition.destroyed', [cbcondition] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function revertCondition( cbcondition, condition ) {
		var $this = ( this.jquery ? this : $( this ) );
		var show = condition.show;
		var hide = condition.hide;

		if ( ! $.isArray( show ) ) {
			show = ( typeof show === 'string' ? [show] : [] );
		}

		if ( ! $.isArray( hide ) ) {
			hide = ( typeof hide === 'string' ? [hide] : [] );
		}

		$.each( show, function( i, selector ) {
			showElement.call( $this, cbcondition, condition, $( selector ) );
		});

		$.each( hide, function( i, selector ) {
			showElement.call( $this, cbcondition, condition, $( selector ) );
		});
	}

	function conditionElement( cbcondition, match ) {
		var $this = ( this.jquery ? this : $( this ) );
		var conditions = cbcondition.settings.conditions;

		if ( ! $.isArray( conditions ) ) {
			conditions = [];
		}

		$.each( conditions, function( i, condition ) {
			var input = condition.input;
			var operator = condition.operator;
			var value = condition.value;
			var show = condition.show;
			var hide = condition.hide;

			if ( ! $.isArray( show ) ) {
				show = ( typeof show === 'string' ? [show] : [] );
			}

			if ( ! $.isArray( hide ) ) {
				hide = ( typeof hide === 'string' ? [hide] : [] );
			}

			if ( $this.is( 'input' ) || $this.is( 'select' ) || $this.is( 'textarea' ) ) {
				if ( $this.is( 'input[type="checkbox"]' ) || $this.is( 'input[type="radio"]' ) ) {
					input = [];

					cbcondition.target.each( function() {
						if ( $( this ).is( ':checked' ) ) {
							input.push( $( this ).val() );
						}
					});
				} else if ( $this.is( 'select[multiple]' ) ) {
					input = $this.val();

					if ( input && ( ! $.isArray( input ) ) ) {
						input = input.split( ',' );
					}
				} else {
					input = $this.val();
				}
			}

			if ( $.isArray( input ) ) {
				input = input.join( '|*|' );
			}

			var matched = false;

			if ( ( ( typeof match === 'undefined' ) && matchCondition.call( $this, cbcondition, input, operator, value ) ) || match ) {
				matched = true;

				$.each( show, function( i, selector ) {
					showElement.call( $this, cbcondition, condition, $( selector ) );
				});

				$.each( hide, function( i, selector ) {
					hideElement.call( $this, cbcondition, condition, $( selector ) );
				});

				cbcondition.element.triggerHandler( 'cbcondition.match.true', [cbcondition, input] );
			} else {
				$.each( show, function( i, selector ) {
					hideElement.call( $this, cbcondition, condition, $( selector ) );
				});

				$.each( hide, function( i, selector ) {
					showElement.call( $this, cbcondition, condition, $( selector ) );
				});

				cbcondition.element.triggerHandler( 'cbcondition.match.false', [cbcondition, input] );
			}

			if ( cbcondition.settings.debug ) {
				console.log({
					condition: condition,
					input: input,
					operator: operator,
					value: value,
					matched: matched
				});
			}
		});
	}

	function showElement( cbcondition, condition, element ) {
		if ( element.length && element.hasClass( 'cbDisplayDisabled' ) ) {
			if ( element.hasClass( 'cbTabPane' ) ) {
				var cbtabs = element.closest( '.cbTabs' );

				if ( cbtabs.data( 'cbtabs' ) ) {
					cbtabs.cbtabs( 'show', element.attr( 'id' ) );
				}
			} else if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
				if ( element.closest( '.cbSingleCntrl' ).length ) {
					element.closest( '.cbSingleCntrl' ).removeClass( 'cbDisplayDisabled hidden' );
				} else if ( element.closest( '.cbSnglCtrlLbl' ).length ) {
					element.closest( '.cbSnglCtrlLbl' ).removeClass( 'cbDisplayDisabled hidden' );
				} else if ( element.parent( 'label' ).length ) {
					element.parent( 'label' ).removeClass( 'cbDisplayDisabled hidden' );
				}
			}

			if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
				element.removeClass( 'cbValidationDisabled' ).trigger( 'condition' );
			} else if ( element.is( 'option' ) ) {
				element.prop( 'disabled', false ).trigger( 'condition' );
			} else {
				element.find( 'input,select,textarea' ).removeClass( 'cbValidationDisabled' ).trigger( 'condition' );
			}

			element.removeClass( 'cbDisplayDisabled hidden' );

			cbcondition.element.triggerHandler( 'cbcondition.show', [cbcondition, element] );
		}
	}

	function hideElement( cbcondition, condition, element ) {
		if ( element.length && ( ! element.hasClass( 'cbDisplayDisabled' ) ) ) {
			if ( element.hasClass( 'cbTabPane' ) ) {
				var cbtabs = element.closest( '.cbTabs' );

				if ( cbtabs.data( 'cbtabs' ) ) {
					cbtabs.cbtabs( 'hide', element.attr( 'id' ) );
				}
			} else if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
				if ( element.closest( '.cbSingleCntrl' ).length ) {
					element.closest( '.cbSingleCntrl' ).addClass( 'cbDisplayDisabled hidden' );
				} else if ( element.closest( '.cbSnglCtrlLbl' ).length ) {
					element.closest( '.cbSnglCtrlLbl' ).addClass( 'cbDisplayDisabled hidden' );
				} else if ( element.parent( 'label' ).length ) {
					element.parent( 'label' ).addClass( 'cbDisplayDisabled hidden' );
				}
			}

			if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
				if ( condition.reset ) {
					if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
						element.prop( 'checked', false );
					} else if ( ! element.is( 'input[type="hidden"]' ) ) {
						element.val( '' );
					}
				}

				element.addClass( 'cbValidationDisabled' ).trigger( 'condition' );
			} else if ( element.is( 'option' ) ) {
				if ( condition.reset ) {
					element.prop( 'selected', false );
				}

				element.prop( 'disabled', true ).trigger( 'condition' );
			} else {
				var elements = element.find( 'input,select,textarea' );

				if ( condition.reset ) {
					elements.each( function() {
						if ( $( this ).is( 'input[type="checkbox"]' ) || $( this ).is( 'input[type="radio"]' ) ) {
							$( this ).prop( 'checked', false );
						} else if ( ! $( this ).is( 'input[type="hidden"]' ) ) {
							$( this ).val( '' );
						}
					});
				}

				elements.addClass( 'cbValidationDisabled' ).trigger( 'condition' );
			}

			element.addClass( 'cbDisplayDisabled hidden' );

			cbcondition.element.triggerHandler( 'cbcondition.hide', [cbcondition, element] );
		}
	}

	function matchCondition( cbcondition, input, operator, value ) {
		input			=	$.trim( input );
		value			=	$.trim( value );

		var match		=	false;

		switch ( operator ) {
			case '!=':
			case '<>':
			case 1:
				match	=	( input != value );
				break;
			case '>':
			case 2:
				match	=	( input > value );
				break;
			case '<':
			case 3:
				match	=	( input < value );
				break;
			case '>=':
			case 4:
				match	=	( input >= value );
				break;
			case '<=':
			case 5:
				match	=	( input <= value );
				break;
			case 'empty':
			case 6:
				match	=	( ! input.length );
				break;
			case '!empty':
			case 7:
				match	=	( input.length );
				break;
			case 'contain':
			case 8:
				match	=	( input.indexOf( value ) != -1 );
				break;
			case '!contain':
			case 9:
				match	=	( input.indexOf( value ) == -1 );
				break;
			case 'regexp':
			case 10:
				match	=	( input.match( eval( value ) ) );
				break;
			case '!regexp':
			case 11:
				match	=	( ! input.match( eval( value ) ) );
				break;
			case '=':
			case 0:
			default:
				match	=	( input == value );
				break;
		}

		cbcondition.element.triggerHandler( 'cbcondition.match', [cbcondition, input, operator, value, match] );

		return match;
	}

	$.fn.cbcondition = function( options ) {
		if ( ! $( this ).length ) {
			// Looks like condition was called on an element that doesn't exist; this happens if you're conditioning off of a field that isn't set to display:
			return $.cbcondition( options );
		}

		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.cbcondition = function( options ) {
		if ( methods[options] ) {
			if ( options == 'instances' ) {
				return instances;
			} else {
				// Do nothing since we can't call methods for a static condition:
				return this;
			}
		}

		var cbcondition = {};

		cbcondition.options = options;
		cbcondition.defaults = $.fn.cbcondition.defaults;
		cbcondition.settings = $.extend( true, {}, cbcondition.defaults, cbcondition.options );
		cbcondition.element = ( typeof options.element === 'undefined' ? $( window ) : options.element );
		cbcondition.target = ( typeof options.target === 'undefined' ? $( window ) : options.target );

		if ( ! $.isArray( cbcondition.settings.conditions ) ) {
			cbcondition.settings.conditions = [];
		}

		conditionElement.call( window, cbcondition );

		return this;
	};

	$.fn.cbcondition.defaults = {
		init: true,
		useData: true,
		conditions: [{
			input: null,
			operator: null,
			value: null,
			show: [],
			hide: [],
			reset: true
		}],
		debug: false
	};
})(jQuery);