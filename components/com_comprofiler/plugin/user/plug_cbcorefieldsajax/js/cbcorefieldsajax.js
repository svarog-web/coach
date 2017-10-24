(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbajaxfield = $( $this ).data( 'cbajaxfield' );

				if ( cbajaxfield ) {
					return; // cbajaxfield is already bound; so no need to rebind below
				}

				cbajaxfield = {};
				cbajaxfield.options = options;
				cbajaxfield.defaults = $.fn.cbajaxfield.defaults;
				cbajaxfield.settings = $.extend( true, {}, cbajaxfield.defaults, cbajaxfield.options );
				cbajaxfield.element = $( $this );

				if ( cbajaxfield.settings.useData ) {
					$.each( cbajaxfield.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbajaxfield.element.data( 'cbajaxfield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbajaxfield.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbajaxfield.element.data( 'cbajaxfield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbajaxfield.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbajaxfield.element.trigger( 'cbajaxfield.init.before', [cbajaxfield] );

				if ( ! cbajaxfield.settings.init ) {
					return;
				}

				$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
					options.async = true;
				});

				cbajaxfield.editHandler = function( e ) {
					if ( ( ! cbajaxfield.settings.ignore ) || ( cbajaxfield.settings.ignore && ( ! $( e.target ).is( cbajaxfield.settings.ignore ) ) && ( ! $( e.target ).parents().is( cbajaxfield.settings.ignore ) ) ) ) {
						var observer = ( window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver );

						$.ajax({
							url: cbajaxfield.settings.editUrl,
							type: 'GET',
							dataType: 'html',
							cache: false,
							beforeSend: function( jqXHR, textStatus, errorThrown ) {
								$( document ).find( '.cbAjaxCancel' ).click();

								if ( ! cbajaxfield.settings.tooltip ) {
									cbajaxfield.element.find( '.cbAjaxValue' ).addClass( 'hidden' );
								}

								cbajaxfield.element.append( '<span class="cbSpinner fa fa-spinner fa-spin-fast"></span>' );

								cbajaxfield.element.triggerHandler( 'cbajaxfield.edit.send', [cbajaxfield, jqXHR, textStatus, errorThrown] );
							},
							error: function( jqXHR, textStatus, errorThrown ) {
								cbajaxfield.element.find( '.cbAjaxForm,.cbSpinner' ).remove();
								cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );

								cbajaxfield.element.triggerHandler( 'cbajaxfield.edit.error', [cbajaxfield, jqXHR, textStatus, errorThrown] );
							},
							success: function( data, textStatus, jqXHR ) {
								cbajaxfield.element.find( '.cbSpinner' ).remove();

								var dataHtml = $( data );
								var dataObserver = null;
								var dataListener = function() {
									if ( ! cbajaxfield.settings.tooltip ) {
										cbajaxfield.element.css({
											width: dataHtml.outerWidth(),
											height: dataHtml.outerHeight()
										});
									}
								};
								var dataResize = function() {
									// Reset the element:
									cbajaxfield.element.css({
										width: '',
										height: ''
									});

									dataHtml.removeClass( 'cbAjaxContainerNoFit' );

									var width = cbajaxfield.element.innerWidth();

									if ( ! width ) {
										// Can't find a element width or parent width so lets try to fill the available space:
										cbajaxfield.element.css({
											width: '100%'
										});

										width = cbajaxfield.element.innerWidth();
									}

									if ( ( ! width ) || cbajaxfield.settings.tooltip ) {
										// Still can't find a width so lets add a no-fit CSS class and style it as a tooltip:
										dataHtml.addClass( 'cbAjaxContainerNoFit' );
									}

									if ( cbajaxfield.settings.tooltip ) {
										dataHtml.css({
											minWidth: width
										});
									} else {
										dataHtml.css({
											width: width
										});
									}

									dataListener();

									var location = cbajaxfield.element.offset();

									dataHtml.css({
										top: location.top,
										left: location.left
									});
								};

								cleanHeaders.call( dataHtml );

								dataHtml.appendTo( 'body' );

								dataResize();

								$( window ).on( 'resize', dataResize );

								dataHtml.find( 'textarea' ).on( 'mouseup', dataListener );
								dataHtml.find( 'img' ).on( 'load', dataListener );

								if ( observer != null ) {
									dataObserver = new observer( dataListener );

									dataObserver.observe( dataHtml[0], {
										attributes: true,
										childList: true,
										characterData: false,
										subtree: true,
										attributeFilter: ['class','style','height','width']
									});
								} else {
									dataHtml[0].addEventListener( 'DOMSubtreeModified', dataListener, false );
								}

								dataHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

								if ( typeof $.fn.rateit !== 'undefined' ) {
									dataHtml.find( 'div.rateit:empty,span.rateit:empty' ).rateit();
								}

								dataHtml.find( '.cbAjaxCancel' ).on( 'click', function( e ) {
									$( window ).off( 'resize', dataResize );

									if ( dataObserver != null ) {
										dataObserver.disconnect();
									} else {
										dataHtml[0].removeEventListener( 'DOMSubtreeModified', dataListener, false );
									}

									dataHtml.remove();

									cbajaxfield.element.css({
										width: '',
										height: ''
									});

									cbajaxfield.element.find( '.cbSpinner' ).remove();
									cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );

									if ( typeof $.fn.mediaelementplayer !== 'undefined' ) {
										cbajaxfield.element.find( 'video.cbVideoFieldEmbed,audio.cbAudioFieldEmbed' ).mediaelementplayer( false );

										cbajaxfield.element.find( 'video.cbVideoFieldEmbed,audio.cbAudioFieldEmbed' ).each( function() {
											$( this ).prependTo( $( this ).closest( '.cbVideoField,.cbAudioField' ) ).nextAll().remove();
										});

										cbajaxfield.element.find( 'video.cbVideoFieldEmbed:visible,audio.cbAudioFieldEmbed:visible' ).mediaelementplayer();
									}

									cbajaxfield.element.trigger( 'cbajaxfield.cancel', [cbajaxfield, e] );
								});

								dataHtml.find( '.cbAjaxForm' ).off( 'submit' );

								dataHtml.find( '.cbAjaxForm' ).on( 'submit', function( e ) {
									e.preventDefault();

									$( this ).ajaxSubmit({
										type: 'POST',
										dataType: 'html',
										beforeSerialize: function( form, options ) {
											cbajaxfield.element.trigger( 'cbajaxfield.save.serialize', [cbajaxfield, form, options] );
										},
										beforeSubmit: function( formData, form, options ) {
											var validator = dataHtml.find( '.cbAjaxForm' ).data( 'cbvalidate' );

											if ( validator ) {
												if ( ! validator.element.cbvalidate( 'validate' ) ) {
													return false;
												}
											}

											$( window ).off( 'resize', dataResize );

											if ( dataObserver != null ) {
												dataObserver.disconnect();
											} else {
												dataHtml[0].removeEventListener( 'DOMSubtreeModified', dataListener, false );
											}

											dataHtml.addClass( 'hidden' );

											cbajaxfield.element.css({
												width: '',
												height: ''
											});

											cbajaxfield.element.append( '<span class="cbSpinner fa fa-spinner fa-spin-fast"></span>' );

											cbajaxfield.element.trigger( 'cbajaxfield.save.submit', [cbajaxfield, formData, form, options] );
										},
										error: function( jqXHR, textStatus, errorThrown ) {
											$( window ).off( 'resize', dataResize );

											if ( dataObserver != null ) {
												dataObserver.disconnect();
											} else {
												dataHtml[0].removeEventListener( 'DOMSubtreeModified', dataListener, false );
											}

											dataHtml.remove();

											cbajaxfield.element.css({
												width: '',
												height: ''
											});

											cbajaxfield.element.find( '.cbSpinner' ).remove();
											cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );

											cbajaxfield.element.trigger( 'cbajaxfield.save.error', [cbajaxfield, jqXHR, textStatus, errorThrown] );
										},
										success: function( data, textStatus, jqXHR ) {
											$( window ).off( 'resize', dataResize );

											if ( dataObserver != null ) {
												dataObserver.disconnect();
											} else {
												dataHtml[0].removeEventListener( 'DOMSubtreeModified', dataListener, false );
											}

											dataHtml.remove();

											dataHtml = $( '<div />' ).html( data );

											cleanHeaders.call( dataHtml );

											cbajaxfield.element.css({
												width: '',
												height: ''
											});

											cbajaxfield.element.find( '.cbSpinner' ).remove();
											cbajaxfield.element.find( '.cbAjaxValue' ).html( dataHtml.html() ).removeClass( 'hidden' );

											cbajaxfield.element.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

											if ( typeof $.fn.rateit !== 'undefined' ) {
												cbajaxfield.element.find( 'div.rateit:empty,span.rateit:empty' ).rateit();
											}

											cbajaxfield.element.trigger( 'cbajaxfield.save.success', [cbajaxfield, data, textStatus, jqXHR] );
										}
									});

									return false;
								});

								cbajaxfield.element.triggerHandler( 'cbajaxfield.edit.success', [cbajaxfield, data, textStatus, jqXHR] );
							}
						});
					}

					cbajaxfield.element.trigger( 'cbajaxfield.edit', [cbajaxfield, e] );
				};

				cbajaxfield.element.on( 'click', '.cbAjaxValue', cbajaxfield.editHandler );

				// Destroy the cbajaxfield element:
				cbajaxfield.element.on( 'remove destroy.cbajaxfield', function() {
					cbajaxfield.element.cbajaxfield( 'destroy' );
				});

				// Rebind the cbajaxfield element to pick up any data attribute modifications:
				cbajaxfield.element.on( 'rebind.cbajaxfield', function() {
					cbajaxfield.element.cbajaxfield( 'rebind' );
				});

				// If the cbajaxfield element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbajaxfield.element.on( 'modified.cbajaxfield', function( e, orgId, oldId, newId ) {
					if ( oldId != newId ) {
						cbajaxfield.element.cbajaxfield( 'destroy' );
						cbajaxfield.element.cbajaxfield( cbajaxfield.options );
					}
				});

				// If the cbajaxfield is cloned we need to rebind it back:
				cbajaxfield.element.on( 'cloned.cbajaxfield', function( e, oldId ) {
					$( this ).off( 'cloned.cbajaxfield' );
					$( this ).off( 'modified.cbajaxfield' );
					$( this ).removeData( 'cbajaxfield' );
					$( this ).find( '.cbAjaxValue' ).removeClass( 'hidden' );

					$( 'body' ).children( '.cbAjaxContainerEdit' ).remove();

					$( this ).find( '.cbSpinner' ).remove();
					$( this ).off( 'click', '.cbAjaxValue', cbajaxfield.editHandler );
					$( this ).cbajaxfield( cbajaxfield.options );
				});

				cbajaxfield.element.trigger( 'cbajaxfield.init.after', [cbajaxfield] );

				// Bind the cbajaxfield to the element so it's reusable and chainable:
				cbajaxfield.element.data( 'cbajaxfield', cbajaxfield );

				// Add this instance to our instance array so we can keep track of our cbajaxfield instances:
				instances.push( cbajaxfield );
			});
		},
		rebind: function() {
			var cbajaxfield = $( this ).data( 'cbajaxfield' );

			if ( ! cbajaxfield ) {
				return this;
			}

			cbajaxfield.element.cbajaxfield( 'destroy' );
			cbajaxfield.element.cbajaxfield( cbajaxfield.options );

			return this;
		},
		destroy: function() {
			var cbajaxfield = $( this ).data( 'cbajaxfield' );

			if ( ! cbajaxfield ) {
				return false;
			}

			cbajaxfield.element.off( 'destroy.cbajaxfield' );
			cbajaxfield.element.off( 'rebind.cbajaxfield' );
			cbajaxfield.element.off( 'cloned.cbajaxfield' );
			cbajaxfield.element.off( 'modified.cbajaxfield' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbajaxfield.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );

			$( 'body' ).children( '.cbAjaxContainerEdit' ).remove();

			cbajaxfield.element.find( '.cbSpinner' ).remove();
			cbajaxfield.element.off( 'click', '.cbAjaxValue', cbajaxfield.editHandler );
			cbajaxfield.element.removeData( 'cbajaxfield' );
			cbajaxfield.element.trigger( 'cbajaxfield.destroyed', [cbajaxfield] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function cleanHeaders() {
		var element = ( this.jquery ? this : $( this ) );
		var head = $( 'head' );
		var loadedCSS = [];
		var loadedScripts = [];

		head.find( 'link' ).each( function() {
			var cssUrl = $( this ).attr( 'href' );

			if ( typeof cssUrl != 'undefined' ) {
				loadedCSS.push( cssUrl )
			}
		});

		head.find( 'script' ).each( function() {
			var scriptUrl = $( this ).attr( 'src' );

			if ( typeof scriptUrl != 'undefined' ) {
				loadedScripts.push( scriptUrl )
			}
		});

		element.find( '.cbAjaxHeaders > link' ).each( function() {
			var cssUrl = $( this ).attr( 'href' );

			if ( typeof cssUrl != 'undefined' && ( loadedCSS.indexOf( cssUrl ) !== -1 ) ) {
				$( this ).remove();
			}
		});

		element.find( '.cbAjaxHeaders > script' ).each( function() {
			var scriptUrl = $( this ).attr( 'src' );

			if ( typeof scriptUrl != 'undefined' && ( loadedScripts.indexOf( scriptUrl ) !== -1 ) ) {
				$( this ).remove();
			}
		});
	}

	$.fn.cbajaxfield = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbajaxfield.defaults = {
		init: true,
		useData: true,
		ignore: 'a,video,audio,.mejs-controls,.mejs-overlay',
		editUrl: null,
		tooltip: null
	};
})(jQuery);