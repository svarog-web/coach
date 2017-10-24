(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbactivity = $( $this ).data( 'cbactivity' );

				if ( cbactivity ) {
					return; // cbactivity is already bound; so no need to rebind below
				}

				cbactivity = {};
				cbactivity.options = ( typeof options != 'undefined' ? options : {} );
				cbactivity.defaults = $.fn.cbactivity.defaults;
				cbactivity.settings = $.extend( true, {}, cbactivity.defaults, cbactivity.options );
				cbactivity.element = $( $this );

				if ( cbactivity.settings.useData ) {
					$.each( cbactivity.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbactivity.element.data( 'cbactivity' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbactivity.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbactivity.element.data( 'cbactivity' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbactivity.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbactivity.element.triggerHandler( 'cbactivity.init.before', [cbactivity] );

				if ( ! cbactivity.settings.init ) {
					return;
				}

				$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
					options.async = true;
				});

				cbactivity.actinHandler = function( e ) {
					e.preventDefault();

					streamAction.call( this, cbactivity );
				};

				$( document ).delegate( '.streamItemAction', 'click', cbactivity.actinHandler );

				cbactivity.displayEditHandler = function( e ) {
					e.preventDefault();

					displayStreamEdit.call( this, cbactivity );
				};

				$( document ).delegate( '.streamItemEditDisplay', 'click', cbactivity.displayEditHandler );

				cbactivity.cancelNewHandler = function( e ) {
					e.preventDefault();

					cancelStreamNew.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemNewCancel', 'click', cbactivity.cancelNewHandler );

				cbactivity.saveHandler = function( e ) {
					e.preventDefault();

					saveStream.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemForm', 'submit', cbactivity.saveHandler );

				cbactivity.cancelEditHandler = function( e ) {
					e.preventDefault();

					cancelStreamEdit.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemEditCancel', 'click', cbactivity.cancelEditHandler );

				cbactivity.closeNoticeHandler = function( e ) {
					e.preventDefault();

					closeStreamNotice.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemNoticeClose', 'click', cbactivity.closeNoticeHandler );

				cbactivity.revertNoticeHandler = function( e ) {
					e.preventDefault();

					revertStreamNotice.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemNoticeRevert', 'click', cbactivity.revertNoticeHandler );

				cbactivity.displayNewHandler = function( e ) {
					displayStreamNew.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemNew .streamInputMessage', 'click', cbactivity.displayNewHandler );

				cbactivity.messageLimitHandler = function( e ) {
					streamMessageLimit.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamInputMessage', 'keyup input change', cbactivity.messageLimitHandler );

				cbactivity.scrollerLeftHandler = function( e ) {
					e.preventDefault();

					streamScrollLeft.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemScrollLeft', 'click', cbactivity.scrollerLeftHandler );

				cbactivity.scrollerRightandler = function( e ) {
					e.preventDefault();

					streamScrollRight.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamItemScrollRight', 'click', cbactivity.scrollerRightandler );

				cbactivity.locationHandler = function( e ) {
					e.preventDefault();

					streamLocation.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamFindLocation', 'click', cbactivity.locationHandler );

				cbactivity.toggleHandler = function( e ) {
					e.preventDefault();

					streamToggle.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamToggle', 'click', cbactivity.toggleHandler );

				cbactivity.toggleSelectHandler = function() {
					streamToggle.call( this, cbactivity );
				};

				cbactivity.element.delegate( 'select.streamInputSelect', 'change', cbactivity.toggleSelectHandler );

				cbactivity.moreHandler = function( e ) {
					e.preventDefault();

					streamMore.call( this, cbactivity );
				};

				cbactivity.element.delegate( '.streamMore', 'click', cbactivity.moreHandler );

				bindContainer.call( $this, cbactivity, true );

				// Destroy the cbactivity element:
				cbactivity.element.on( 'remove destroy.cbactivity', function() {
					cbactivity.element.cbactivity( 'destroy' );
				});

				// Rebind the cbactivity element to pick up any data attribute modifications:
				cbactivity.element.on( 'rebind.cbactivity', function() {
					cbactivity.element.cbactivity( 'rebind' );
				});

				// If the cbactivity element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbactivity.element.on( 'modified.cbactivity', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbactivity.element.cbactivity( 'rebind' );
					}
				});

				// If the cbactivity is cloned we need to rebind it back:
				cbactivity.element.on( 'cloned.cbactivity', function() {
					destroyStream.call( this, cbactivity );

					$( this ).cbactivity( cbactivity.options );
				});

				cbactivity.element.triggerHandler( 'cbactivity.init.after', [cbactivity] );

				// Bind the cbactivity to the element so it's reusable and chainable:
				cbactivity.element.data( 'cbactivity', cbactivity );

				// Add this instance to our instance array so we can keep track of our cbactivity instances:
				instances.push( cbactivity );
			});
		},
		rebind: function() {
			var cbactivity = $( this ).data( 'cbactivity' );

			if ( ! cbactivity ) {
				return this;
			}

			cbactivity.element.cbactivity( 'destroy' );
			cbactivity.element.cbactivity( cbactivity.options );

			return this;
		},
		destroy: function() {
			var cbactivity = $( this ).data( 'cbactivity' );

			if ( ! cbactivity ) {
				return this;
			}

			destroyStream.call( cbactivity.element, cbactivity );

			cbactivity.element.triggerHandler( 'cbactivity.destroyed', [cbactivity] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	function destroyStream( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );

		element.off( 'destroy.cbactivity' );
		element.off( 'rebind.cbactivity' );
		element.off( 'modified.cbactivity' );
		element.off( 'cloned.cbactivity' );
		element.removeData( 'cbactivity' );

		$( document ).off( 'click', cbactivity.actinHandler );
		$( document ).off( 'click', cbactivity.displayEditHandler );

		element.off( 'click', cbactivity.locationHandler );
		element.off( 'click', cbactivity.cancelNewHandler );
		element.off( 'submit', cbactivity.saveHandler );
		element.off( 'click', cbactivity.cancelEditHandler );
		element.off( 'click', cbactivity.closeNoticeHandler );
		element.off( 'click', cbactivity.revertNoticeHandler );
		element.off( 'click', cbactivity.scrollerLeftHandler );
		element.off( 'click', cbactivity.scrollerRightandler );
		element.off( 'click', cbactivity.toggleHandler );
		element.off( 'change', cbactivity.toggleSelectHandler );
		element.off( 'click', cbactivity.moreHandler );
		element.off( 'click', cbactivity.displayNewHandler );
		element.off( 'keyup input change', cbactivity.messageLimitHandler );

		filterSelector.call( element.find( '.cbMoreLess' ), cbactivity ).cbmoreless( 'destroy' );
		filterSelector.call( element.find( '.cbRepeat' ), cbactivity ).cbrepeat( 'destroy' );
		filterSelector.call( element.find( '.streamInputAutosize' ), cbactivity ).trigger( 'autosize.destroy' );
		filterSelector.call( element.find( 'select.streamInputSelect' ), cbactivity ).cbselect( 'destroy' );
		filterSelector.call( element.find( '.streamInputMessageLimit' ), cbactivity ).remove();

		filterSelector.call( element.find( '.streamItem' ), cbactivity ).each( function() {
			var activeClasses = $( this ).data( 'cbactivity-active-classes' );
			var inactiveClasses = $( this ).data( 'cbactivity-inactive-classes' );

			if ( typeof activeClasses != 'undefined' ) {
				$( this ).removeClass( activeClasses );
			}

			if ( typeof inactiveClasses != 'undefined' ) {
				$( this ).addClass( inactiveClasses );
			}
		});
	}

	function filterSelector( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );

		return	element.filter( function() {
					return $( this ).closest( '.streamContainer' ).is( cbactivity.element );
				});
	}

	function findContainer( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = filterSelector.call( element.closest( '.streamItem' ), cbactivity );

		if ( ! container.length ) {
			var containerId = element.data( 'cbactivity-container' );

			if ( typeof containerId != 'undefined' ) {
				container = filterSelector.call( $( containerId ), cbactivity );
			}
		}

		return container;
	}

	function bindContainer( cbactivity, init ) {
		var element = ( this.jquery ? this : $( this ) );

		if ( init === true ) {
			var message = filterSelector.call( element.find( '.streamInputMessage' ), cbactivity );

			message.each( function() {
				if ( ! $( this ).siblings( '.streamInputMessageLimit' ).length ) {
					var messageLimit = $( this ).data( 'cbactivity-input-limit' );

					if ( typeof messageLimit != 'undefined' ) {
						$( this ).trigger( 'change' );

						$( this ).before( '<div class="streamInputMessageLimit small hidden"><div class="streamInputMessageLimitCurrent">' + $( this ).val().length + '</div> / <div class="streamInputMessageLimitMax">' + messageLimit + '</div></div>' );
					}
				}
			});

			filterSelector.call( element.find( '.cbMoreLess' ), cbactivity ).cbmoreless();
			filterSelector.call( element.find( '.cbRepeat' ), cbactivity ).cbrepeat();

			var formatSelectIcon = function( option ) {
				var icon = $( option.element ).data( 'cbactivity-option-icon' );

				if ( typeof icon != 'undefined' ) {
					return $( '<span><span class="cb_template streamSelectOptionIcon">' + icon + '</span>' + option.text + '</span>' );
				} else {
					return option.text;
				}
			};

			filterSelector.call( element.find( 'select.streamInputSelect' ), cbactivity ).on( 'cbselect.init.before', function( e, cbselect ) {
				if ( cbselect.element.val() > 0 ) {
					var target = cbselect.element.data( 'cbactivity-toggle-target' );

					if ( typeof target == 'undefined' ) {
						return;
					}

					var container = findContainer.call( cbselect.element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					var selected = cbselect.element.find( ':selected' );
					var placeholder = selected.data( 'cbactivity-toggle-placeholder' );
					var label = filterSelector.call( container.find( target ).find( '.streamInputSelectToggleLabel' ), cbactivity );

					if ( label.length ) {
						label.html( selected.text() );
					}

					if ( typeof placeholder != 'undefined' ) {
						var input = filterSelector.call( container.find( target ).find( '.streamInputSelectTogglePlaceholder' ), cbactivity );

						if ( label.length ) {
							input.attr( 'placeholder', placeholder );
						}
					}
				}
			}).on( 'cbselect.init.after', function( e, cbselect ) {
				var icon = cbselect.element.data( 'cbactivity-toggle-icon' );

				if ( typeof icon == 'undefined' ) {
					return;
				}

				cbselect.container.find( '.select2-selection' ).addClass( icon );
			}).cbselect({
				width: 'auto',
				height: 'auto',
				minimumResultsForSearch: Infinity,
				templateSelection: formatSelectIcon,
				templateResult: formatSelectIcon
			});

			element.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();
		} else {
			filterSelector.call( element.find( '.streamInputMessageLimit' ), cbactivity ).removeClass( 'hidden' );

			filterSelector.call( element.find( '.streamInputAutosize:visible' ), cbactivity ).trigger( 'autosize.destroy' ).autosize({
				append: '',
				resizeDelay: 0,
				placeholder: false
			});
		}

		cbactivity.element.triggerHandler( 'cbactivity.bind', [cbactivity, element, init] );
	}

	function streamMessageLimit( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var input = filterSelector.call( container.find( '.streamInputMessage' ), cbactivity );
		var inputLimit = $( this ).data( 'cbactivity-input-limit' );

		if ( typeof inputLimit != 'undefined' ) {
			var inputLength = $( this ).val().length;

			if ( inputLength > inputLimit ) {
				$( this ).val( $( this ).val().substr( 0, inputLimit ) );
			} else {
				input.siblings( '.streamInputMessageLimit' ).find( '.streamInputMessageLimitCurrent' ).html( $( this ).val().length );
			}
		}

		if ( $( this ).val() ) {
			filterSelector.call( container.find( '.streamItemNewSave,.streamItemEditSave' ), cbactivity ).removeClass( 'disabled' ).prop( 'disabled', false );
		} else {
			filterSelector.call( container.find( '.streamItemNewSave,.streamItemEditSave' ), cbactivity ).addClass( 'disabled' ).prop( 'disabled', true );
		}
	}

	function streamScrollLeft( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var active = element.siblings( '.streamItemScrollContent:not(.hidden)' );
		var previous = active.prevAll(  '.streamItemScrollContent.hidden:first' );

		if ( ! previous.length ) {
			previous = element.siblings( '.streamItemScrollContent.hidden:last' );
		}

		if ( ! previous.length ) {
			return;
		}

		previous.removeClass( 'hidden' );
		active.addClass( 'hidden' );

		bindContainer.call( container, cbactivity );
	}

	function streamScrollRight( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var active = element.siblings( '.streamItemScrollContent:not(.hidden)' );
		var next = active.nextAll(  '.streamItemScrollContent.hidden:first' );

		if ( ! next.length ) {
			next = element.siblings( '.streamItemScrollContent.hidden:first' );
		}

		if ( ! next.length ) {
			return;
		}

		next.removeClass( 'hidden' );
		active.addClass( 'hidden' );

		bindContainer.call( container, cbactivity );
	}

	function streamLocation( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var target = element.data( 'cbactivity-location-target' );
		var allowFilter = ( element.data( 'cbactivity-location-filter' ) != false );

		if ( typeof target == 'undefined' ) {
			return;
		}

		if ( typeof navigator.geolocation == 'undefined' ) {
			return;
		}

		navigator.geolocation.getCurrentPosition( function( position ) {
			var location = position.coords.latitude + ',' + position.coords.longitude;
			var containerTarget = container.find( target );

			if ( allowFilter ) {
				containerTarget = filterSelector.call( containerTarget, cbactivity );
			}

			if ( containerTarget.is( 'input' ) ) {
				containerTarget.val( location );
			} else {
				containerTarget.html( location );
			}
		});
	}

	function streamToggle( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var cbselect = ( $.fn.cbselect && ( typeof element.data( 'cbselect' ) != 'undefined' ) );

		var target = element.data( 'cbactivity-toggle-target' );
		var activeClasses = element.data( 'cbactivity-toggle-active-classes' );
		var inactiveClasses = element.data( 'cbactivity-toggle-inactive-classes' );

		var allowOpen = ( element.data( 'cbactivity-toggle-open' ) != false );
		var allowClose = ( element.data( 'cbactivity-toggle-close' ) != false );
		var allowFilter = ( element.data( 'cbactivity-toggle-filter' ) != false );

		var open = false;

		if ( cbselect || element.is( 'select' ) ) {
			var value = element.val();

			if ( cbselect ) {
				value = element.cbselect( 'get' );
			}

			open = ( ( ! value ) || ( value == 0 ) || ( value == '' ) );
		} else {
			if ( element.hasClass( 'streamToggleOpen' ) ) {
				open = true;
			}
		}

		if ( open ) {
			if ( allowClose ) {
				element.removeClass( 'streamToggleOpen' );

				if ( cbselect ) {
					element.cbselect( 'container' ).removeClass( 'streamToggleOpen' );
				}

				if ( typeof activeClasses != 'undefined' ) {
					element.removeClass( activeClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).removeClass( activeClasses );
					}
				}

				if ( typeof inactiveClasses != 'undefined' ) {
					element.addClass( inactiveClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).addClass( inactiveClasses );
					}
				}

				if ( typeof target != 'undefined' ) {
					if ( allowFilter ) {
						filterSelector.call( container.find( target ), cbactivity ).addClass( 'hidden' );
						filterSelector.call( container.find( target ).find( 'input,textarea,select' ), cbactivity ).prop( 'disabled', true );
					} else {
						container.find( target ).addClass( 'hidden' );
						container.find( target ).find( 'input,textarea,select' ).prop( 'disabled', true );
					}
				}
			}
		} else {
			if ( allowOpen ) {
				element.addClass( 'streamToggleOpen' );

				if ( cbselect ) {
					element.cbselect( 'container' ).addClass( 'streamToggleOpen' );
				}

				if ( typeof activeClasses != 'undefined' ) {
					element.addClass( activeClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).addClass( activeClasses );
					}
				}

				if ( typeof inactiveClasses != 'undefined' ) {
					element.removeClass( inactiveClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).removeClass( inactiveClasses );
					}
				}

				if ( typeof target != 'undefined' ) {
					if ( allowFilter ) {
						filterSelector.call( container.find( target ), cbactivity ).removeClass( 'hidden' );
						filterSelector.call( container.find( target ).find( 'input,textarea,select' ), cbactivity ).prop( 'disabled', false );
					} else {
						container.find( target ).removeClass( 'hidden' );
						container.find( target ).find( 'input,textarea,select' ).prop( 'disabled', false );
					}
				}
			}
		}

		if ( element.hasClass( 'streamInputSelect' ) && ( typeof target != 'undefined' ) ) {
			var selected = element.find( ':selected' );
			var placeholder = selected.data( 'cbactivity-toggle-placeholder' );
			var label = filterSelector.call( container.find( target ).find( '.streamInputSelectToggleLabel' ), cbactivity );

			if ( label.length ) {
				label.html( selected.text() );
			}

			if ( typeof placeholder != 'undefined' ) {
				var input = filterSelector.call( container.find( target ).find( '.streamInputSelectTogglePlaceholder' ), cbactivity );

				if ( label.length ) {
					input.attr( 'placeholder', placeholder );
				}
			}
		}
	}

	function streamAction( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var actionTarget = element.data( 'cbactivity-action-target' );
		var actionOutput = element.data( 'cbactivity-action-output' );
		var confirmMessage = element.data( 'cbactivity-confirm' );
		var confirmButton = element.data( 'cbactivity-confirm-button' );
		var target = null;

		if ( typeof actionTarget != 'undefined' ) {
			if ( actionTarget == 'self' ) {
				target = element;
			} else {
				actionTarget = filterSelector.call( container.find( actionTarget ), cbactivity );

				if ( actionTarget.length ) {
					target = actionTarget;
				}
			}
		}

		var callback = function() {
			$.ajax({
				url: element.attr( 'href' ),
				type: 'POST',
				dataType: 'html',
				beforeSend: function( jqXHR, textStatus, errorThrown ) {
					if ( target ) {
						element.addClass( 'hidden' );
						element.after( '<span class=\"streamActionLoading fa fa-spinner fa-pulse\"></span>' );
					} else {
						container.addClass( 'overlay' );
					}

					cbactivity.element.triggerHandler( 'cbactivity.action.send', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					if ( target ) {
						element.removeClass( 'hidden' );
						element.siblings( '.streamActionLoading' ).remove();
					} else {
						container.removeClass( 'overlay' );
					}

					cbactivity.element.triggerHandler( 'cbactivity.action.error', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
				},
				success: function( data, textStatus, jqXHR ) {
					var newContent = null;

					if ( target ) {
						newContent = $( data );

						element.removeClass( 'hidden' );
						element.siblings( '.streamActionLoading' ).remove();

						switch( actionOutput ) {
							case 'before':
								target.before( newContent );
								break;
							case 'after':
								target.after( newContent );
								break;
							case 'prepend':
								target.prepend( newContent );
								break;
							case 'append':
								target.append( newContent );
								break;
							case 'replace':
							default:
								target.replaceWith( newContent );
								break;
						}

						element.remove();
					} else {
						container.removeClass( 'overlay' );

						if ( data != '' ) {
							newContent = $( '<div class=\"streamItemNotice\"><div class=\"streamItemNoticeMessage\">' + data + '</div><a href=\"javascript:void(0);\" class=\"streamItemNoticeClose\"><span class=\"streamIconClose fa fa-times\"></span></a></div>' );

							container.children().addClass( 'hidden' );
							container.children( '.streamItemNotice' ).remove();
							container.append( newContent );
						} else {
							if ( element.hasClass( 'streamItemNoticeRevert' ) ) {
								container.children().removeClass( 'hidden' ).hide().fadeIn( 'slow' );
								container.children( '.streamItemNotice' ).remove();
							} else {
								container.siblings( '.streamItemHeaders' ).remove();

								container.remove();
							}
						}
					}

					if ( newContent ) {
						newContent.hide().fadeIn( 'slow' );
					}

					cbactivity.element.triggerHandler( 'cbactivity.action.success', [cbactivity, element, container, data, textStatus, jqXHR] );

					if ( newContent ) {
						bindContainer.call( container, cbactivity, true );
					}
				}
			});
		};

		if ( confirmMessage ) {
			$.cbconfirm( confirmMessage, { buttonYes: confirmButton } ).done( callback );
		} else {
			callback();
		}
	}

	function saveStream( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return false;
		}

		var isNew = ( typeof container.data( 'cbactivity-id' ) == 'undefined' );
		var errorClass = container.data( 'cbactivity-error-classes' );

		element.ajaxSubmit({
			type: 'POST',
			dataType: 'html',
			beforeSend: function( jqXHR, textStatus, errorThrown ) {
				container.addClass( 'overlay' );

				if ( errorClass ) {
					container.removeClass( errorClass );
				}

				cbactivity.element.triggerHandler( 'cbactivity.save.send', [cbactivity, element, container, isNew, jqXHR, textStatus, errorThrown] );
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				container.removeClass( 'overlay' );

				if ( errorClass ) {
					container.addClass( errorClass );
				}

				cbactivity.element.triggerHandler( 'cbactivity.save.error', [cbactivity, element, container, isNew, jqXHR, textStatus, errorThrown] );
			},
			success: function( data, textStatus, jqXHR ) {
				container.removeClass( 'overlay' );

				if ( errorClass ) {
					container.removeClass( errorClass );
				}

				var newContainer = $( data );

				if ( isNew ) {
					cancelStreamNew.call( element, cbactivity );

					var items = container.siblings( '.streamItems' );

					if ( cbactivity.settings.direction ) {
						newContainer.appendTo( items );
					} else {
						newContainer.prependTo( items );
					}
				} else {
					container.siblings( '.streamItemHeaders' ).remove();

					container.replaceWith( newContainer );
				}

				newContainer.hide().fadeIn( 'slow' );

				container = newContainer;

				cbactivity.element.triggerHandler( 'cbactivity.save.success', [cbactivity, element, container, isNew, data, textStatus, jqXHR] );

				bindContainer.call( container, cbactivity, true );
			}
		});

		return false;
	}

	function displayStreamNew( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var display = filterSelector.call( container.find( '.streamItemDisplay' ), cbactivity );

		if ( ! display.hasClass( 'hidden' ) ) {
			return;
		}

		var input = filterSelector.call( container.find( '.streamInputMessage' ), cbactivity );
		var size = input.data( 'cbactivity-input-size' );

		if ( typeof size != 'undefined' ) {
			input.attr( 'rows', size );
		}

		display.removeClass( 'hidden' );

		cbactivity.element.triggerHandler( 'cbactivity.new.display', [cbactivity, element, container] );

		bindContainer.call( container, cbactivity );
	}

	function cancelStreamNew( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		filterSelector.call( container.find( '.streamInputMessage' ), cbactivity ).attr( 'rows', 1 );
		filterSelector.call( container.find( '.streamItemDisplay' ), cbactivity ).addClass( 'hidden' );

		filterSelector.call( container.find( 'input,textarea,select' ), cbactivity ).each( function() {
			var type = $( this ).attr( 'type' );

			if ( ( type == 'checkbox' ) || ( type == 'radio' ) ) {
				if ( ( type == 'radio' ) && ( ( $( this ).siblings( 'input[type="radio"]' ).length + 1 ) == 2 ) && ( $( this ).val() == 0 ) ) {
					$( this ).prop( 'checked', true );
				} else {
					$( this ).prop( 'checked', false );
				}
			} else {
				if ( $( this ).is( 'select' ) ) {
					var value = '';

					if ( ( ! $( this ).children( 'option[value=""]:first' ).length ) && ( ! $( this ).hasClass( 'streamInputTags' ) ) ) {
						value = $( this ).children( 'option[value!=""]:first' ).val();
					}

					if ( $.fn.cbselect && ( typeof $( this ).data( 'cbselect' ) != 'undefined' ) ) {
						$( this ).cbselect( 'set', value );
					} else {
						$( this ).val( value );
					}
				} else {
					$( this ).val( '' );
				}
			}

			$( this ).trigger( 'change' );
		});

		filterSelector.call( container.find( '.streamToggleOpen' ), cbactivity ).each( function() {
			streamToggle.call( this, cbactivity );
		});

		filterSelector.call( container.find( '.cbRepeat' ), cbactivity ).cbrepeat( 'reset' );

		cbactivity.element.triggerHandler( 'cbactivity.new.cancel', [cbactivity, element, container] );

		bindContainer.call( container, cbactivity );

		filterSelector.call( container.find( '.streamInputMessageLimit' ), cbactivity ).addClass( 'hidden' );
	}

	function displayStreamEdit( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		filterSelector.call( container.find( '.streamItemEdit' ), cbactivity ).removeClass( 'hidden' );
		filterSelector.call( container.find( '.streamItemDisplay' ), cbactivity ).addClass( 'hidden' );

		cbactivity.element.triggerHandler( 'cbactivity.edit.display', [cbactivity, element, container] );

		bindContainer.call( container, cbactivity );
	}

	function cancelStreamEdit( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		filterSelector.call( container.find( '.streamItemEdit' ), cbactivity ).addClass( 'hidden' );
		filterSelector.call( container.find( '.streamItemDisplay' ), cbactivity ).removeClass( 'hidden' );

		cbactivity.element.triggerHandler( 'cbactivity.edit.cancel', [cbactivity, element, container] );

		bindContainer.call( container, cbactivity );
	}

	function closeStreamNotice( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );

		if ( element.hasClass( 'streamItemAction' ) ) {
			return;
		}

		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		container.siblings( '.streamItemHeaders' ).remove();

		container.remove();

		cbactivity.element.triggerHandler( 'cbactivity.notice.close', [cbactivity, element, container] );
	}

	function revertStreamNotice( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );

		if ( element.hasClass( 'streamItemAction' ) ) {
			return;
		}

		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		container.children().removeClass( 'hidden' );
		container.children( '.streamItemNotice' ).remove();

		cbactivity.element.triggerHandler( 'cbactivity.notice.revert', [cbactivity, element, container] );
	}

	function streamMore( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = filterSelector.call( element.closest( '.streamContainer' ), cbactivity );

		if ( ! container.length ) {
			return;
		}

		var items = filterSelector.call( container.find( '.streamItems' ), cbactivity );
		var url = element.attr( 'href' );

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'html',
			beforeSend: function( jqXHR, textStatus, errorThrown ) {
				element.addClass( 'disabled' ).prop( 'disabled', true ).html( '<span class=\"streamIconMoreLoading fa fa-spinner fa-pulse\"></span>' );

				cbactivity.element.triggerHandler( 'cbactivity.more.send', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				element.remove();

				cbactivity.element.triggerHandler( 'cbactivity.more.error', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
			},
			success: function( data, textStatus, jqXHR ) {
				element.remove();

				var newData = $( '<div />' ).html( data );

				newData.find( '.streamItem' ).each( function() {
					if ( filterSelector.call( container.find( '.streamItem[data-cbactivity-id="' + $( this ).data( 'cbactivity-id' ) + '"]' ), cbactivity ).length ) {
						$( this ).remove();
					}
				});

				var dataHtml = $( newData.html() );

				if ( cbactivity.settings.direction ) {
					dataHtml.prependTo( items );

					filterSelector.call( container.find( '.streamMore' ), cbactivity ).insertBefore( items );
				} else {
					dataHtml.appendTo( items );

					filterSelector.call( container.find( '.streamMore' ), cbactivity ).insertAfter( items );
				}

				dataHtml.hide().fadeIn( 'slow' );

				cbactivity.element.triggerHandler( 'cbactivity.more.success', [cbactivity, element, container, data, textStatus, jqXHR] );

				bindContainer.call( dataHtml, cbactivity, true );
			}
		});
	}

	$.fn.cbactivity = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbactivity.defaults = {
		init: true,
		useData: true,
		direction: 0
	};
})(jQuery);