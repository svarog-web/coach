(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbgallery = $( $this ).data( 'cbgallery' );

				if ( cbgallery ) {
					return; // cbgallery is already bound; so no need to rebind below
				}

				cbgallery = {};
				cbgallery.options = options;
				cbgallery.defaults = $.fn.cbgallery.defaults;
				cbgallery.settings = $.extend( true, {}, cbgallery.defaults, cbgallery.options );
				cbgallery.element = $( $this );

				if ( cbgallery.settings.useData ) {
					$.each( cbgallery.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbgallery.element.data( 'cbgallery' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbgallery.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbgallery.element.data( 'cbgallery' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbgallery.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbgallery.element.trigger( 'cbgallery.init.before', [cbgallery] );

				if ( ! cbgallery.settings.init ) {
					return;
				}

				if ( ( cbgallery.settings.request === null ) || ( ( typeof cbgallery.settings.request != 'object' ) && ( ! $.isArray( cbgallery.settings.request ) ) ) ) {
					cbgallery.settings.request = {};
				}

				$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
					options.async = true;
				});

				if ( cbgallery.settings.mode == 'modal' ) {
					if ( ! cbgallery.settings.url ) {
						return;
					}

					if ( cbgallery.settings.previous ) {
						cbgallery.settings.request['previous'] = cbgallery.settings.previous;
					}

					if ( cbgallery.settings.next ) {
						cbgallery.settings.request['next'] = cbgallery.settings.next;
					}

					cbgallery.navigationHandler = function( e ) {
						var cbtooltip = cbgallery.element.data( 'cbtooltip' );

						if ( ( ! cbtooltip ) || $( e.target ).is( 'input,textarea' ) ) {
							return;
						}

						switch( e.which ) {
							case 27: // escape
								var close = cbtooltip.tooltip.qtip( 'api' ).elements.content.find( '.galleryModalClose' );

								if ( close.length ) {
									close.click();
								} else {
									return;
								}
								break;
							case 37: // left
								var previous = cbtooltip.tooltip.qtip( 'api' ).elements.content.find( '.galleryModalScrollLeftIcon' );

								if ( previous.length ) {
									previous.click();
								} else {
									return;
								}
								break;
							case 39: // right
								var next = cbtooltip.tooltip.qtip( 'api' ).elements.content.find( '.galleryModalScrollRightIcon' );

								if ( next.length ) {
									next.click();
								} else {
									return;
								}
								break;
							default:
								return;
						}

						e.preventDefault();
					};

					cbgallery.actionHandler = function( e ) {
						var cbtooltip = cbgallery.element.data( 'cbtooltip' );

						if ( ! cbtooltip ) {
							return;
						}

						e.preventDefault();

						var url = $( e.target ).attr( 'href' );
						var ajax = null;

						cbtooltip.tooltip.qtip( 'api' ).set( 'content.text', function( e, api ) {
							if ( ajax == null ) {
								ajax = $.ajax({
									url: url,
									type: 'GET',
									dataType: 'html',
									cache: false,
									beforeSend: function( jqXHR, textStatus, errorThrown ) {
										cbgallery.element.triggerHandler( 'cbgallery.modal.action.send', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
									}
								}).fail( function( jqXHR, textStatus, errorThrown ) {
									cbgallery.element.triggerHandler( 'cbgallery.modal.action.error', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
								}).done( function( data, textStatus, jqXHR ) {
									if ( ! api.destroyed ) {
										$( document ).off( 'keydown', cbgallery.navigationHandler );
										$( document ).off( 'click', '.galleryModalAction', cbgallery.actionHandler );

										var response = null;

										try {
											response = JSON.parse( data );
										} catch( e ) {
											response = { status: true, message: data };
										}

										var modal = cbgallery.displayHandler( e, cbtooltip );

										modal.done( function( data, textStatus, jqXHR ) {
											if ( response && ( typeof response.status !== 'undefined' ) ) {
												if ( response.status === true ) {
													if ( ( typeof response.message !== 'undefined' ) && response.message ) {
														if ( api.elements.content ) {
															api.elements.content.find( '.galleryModalDisplay' ).prepend( '<div class="galleryModalAlert alert alert-sm alert-success">' + response.message + '</div>' );
														}
													}

													cbgallery.element.trigger( 'cbgallery.modal.action.success', [cbgallery, cbtooltip, response, textStatus, jqXHR] );
												} else if ( response.status === false ) {
													if ( ( typeof response.message !== 'undefined' ) && response.message ) {
														if ( api.elements.content ) {
															api.elements.content.find( '.galleryModalDisplay' ).prepend( '<div class="galleryModalAlert alert alert-sm alert-danger">' + response.message + '</div>' );
														}
													}

													cbgallery.element.trigger( 'cbgallery.modal.action.failed', [cbgallery, cbtooltip, response, textStatus, jqXHR] );
												}
											}
										});
									}

									cbgallery.element.triggerHandler( 'cbgallery.modal.action.always', [cbgallery, cbtooltip, data, textStatus, jqXHR] );
								});
							}

							return '<div class="galleryModalLoading text-center"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>';
						});

						cbgallery.element.trigger( 'cbgallery.modal.action', [cbgallery, cbtooltip, ajax, e] );

						return ajax;
					};

					cbgallery.closeHandler = function() {
						$( document ).off( 'keydown', cbgallery.navigationHandler );
						$( document ).off( 'click', '.galleryModalAction', cbgallery.actionHandler );

						$( 'body' ).removeClass( 'galleryModalOpen' );
						$( 'body > .galleryHeadersScripts' ).remove();
					};

					cbgallery.element.on( 'cbtooltip.hidden', cbgallery.closeHandler );

					cbgallery.moveHandler = function( e, cbtooltip, event, api ) {
						if ( api.elements.tooltip ) {
							api.elements.content.find( '.galleryModalItem,.galleryModalLoading' ).css( 'line-height', api.elements.content.css( 'max-height' ) );
							api.elements.content.find( '.galleryRotate90,.galleryRotate270' ).css( 'max-width', api.elements.content.css( 'max-height' ) );
						}
					};

					cbgallery.element.on( 'cbtooltip.move', cbgallery.moveHandler );

					cbgallery.displayHandler = function( e, cbtooltip ) {
						var ajax = null;

						cbtooltip.tooltip.qtip( 'api' ).set( 'content.text', function( e, api ) {
							if ( ajax == null ) {
								ajax = $.ajax({
									url: cbgallery.settings.url,
									type: 'GET',
									dataType: 'html',
									cache: false,
									data: cbgallery.settings.request,
									beforeSend: function( jqXHR, textStatus, errorThrown ) {
										cbgallery.element.triggerHandler( 'cbgallery.modal.send', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
									}
								}).fail( function( jqXHR, textStatus, errorThrown ) {
									if ( ! api.destroyed ) {
										api.hide();
									}

									cbgallery.element.triggerHandler( 'cbgallery.modal.error', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
								}).done( function( data, textStatus, jqXHR ) {
									if ( ! api.destroyed ) {
										var dataHtml = $( data );
										var loadScripts = parseHeaders.call( dataHtml, cbgallery );

										api.set( 'content.text', dataHtml.html() );

										parseScripts.call( api.elements.content, cbgallery, loadScripts );

										if ( api.elements.content ) {
											api.elements.content.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

											api.elements.content.find( '.galleryModalScrollLeftIcon' ).on( 'click', function () {
												var previous = $( this ).data( 'cbgallery-previous' );

												if ( previous ) {
													api.toggle( false );

													$( previous ).find( '.galleryModalToggle' ).click();
												}
											});

											api.elements.content.find( '.galleryModalScrollRightIcon' ).on( 'click', function () {
												var next = $( this ).data( 'cbgallery-next' );

												if ( next ) {
													api.toggle( false );

													$( next ).find( '.galleryModalToggle' ).click();
												}
											});
										}

										$( document ).on( 'keydown', cbgallery.navigationHandler );
										$( document ).on( 'click', '.galleryModalAction', cbgallery.actionHandler );

										$( 'body' ).addClass( 'galleryModalOpen' );
									}

									cbgallery.element.triggerHandler( 'cbgallery.modal.success', [cbgallery, cbtooltip, data, textStatus, jqXHR] );
								});
							}

							return '<div class="galleryModalLoading text-center"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>';
						});

						cbgallery.element.trigger( 'cbgallery.modal', [cbgallery, cbtooltip, ajax, e] );

						return ajax;
					};

					cbgallery.element.on( 'cbtooltip.render', cbgallery.displayHandler );
				} else if ( cbgallery.settings.mode == 'share' ) {
					if ( ! cbgallery.settings.url ) {
						return;
					}

					cbgallery.element.find( '.galleryShareUpload' ).fileupload({
						url: cbgallery.settings.url,
						dataType: 'html',
						sequentialUploads: true,
						dropZone: cbgallery.element.find( '.galleryShareUploadDropZone' ),
						pasteZone: $( document ),
						add: function( e, data ) {
							if ( cbgallery.settings.callback.upload.add ) {
								cbgallery.element.find( '.galleryShareUploadProgress' ).removeClass( 'hidden' );
							}

							$.each( data.files, function( index, file ) {
								file.error = null;
								file.context = null;

								if ( cbgallery.settings.callback.upload.add ) {
									file.context = cbgallery.settings.callback.upload.add.call( cbgallery.element, cbgallery, data, file );
								}

								if ( file.context ) {
									file.context.find( '.galleryShareUploadProgressCancel' ).on( 'click', function() {
										data.abort();

										file.context.find( '.progress-bar' ).css( 'width', '100%' ).removeClass( 'progress-bar-striped active' ).addClass( 'progress-bar-warning' );
										file.context.find( '.galleryShareUploadProgressClear' ).removeClass( 'hidden' );
										file.context.find( '.galleryShareUploadProgressCancel' ).remove();
									});

									file.context.find( '.galleryShareUploadProgressClear' ).on( 'click', function() {
										file.context.next( '.galleryShareUploadProgressError' ).remove();
										file.context.remove();

										if ( ! cbgallery.element.find( '.galleryShareUploadProgressRow' ).length ) {
											cbgallery.element.find( '.galleryShareUploadProgress' ).addClass( 'hidden' );
										}
									});

									file.context.appendTo( '.galleryShareUploadProgressRows' );
								}

								cbgallery.element.trigger( 'cbgallery.upload.add.file', [cbgallery, file, data] );
							});

							data.process().done( function () {
								data.submit();
							});

							cbgallery.element.trigger( 'cbgallery.upload.add', [cbgallery, data] );
						},
						progress: function( e, data ) {
							var file = data.files[0].context;

							if ( file ) {
								file.find( '.progress-bar' ).css( 'width', parseInt( ( ( data.loaded / data.total ) * 100 ), 10 ) + '%' );
							}

							cbgallery.element.trigger( 'cbgallery.upload.progress', [cbgallery, file, data] );
						},
						fail: function( e, data ) {
							cbgallery.element.trigger( 'cbgallery.upload.error', [cbgallery, file, data] );
						},
						done: function( e, data ) {
							var response = null;

							try {
								response = JSON.parse( data.result );
							} catch( e ) {
								response = { status: true, message: data.result };
							}

							var file = data.files[0].context;

							if ( file ) {
								var progressBar = file.find( '.progress-bar' );

								file.find( '.galleryShareUploadProgressClear' ).removeClass( 'hidden' );
								file.find( '.galleryShareUploadProgressCancel' ).remove();

								progressBar.css( 'width', '100%' ).removeClass( 'progress-bar-striped active' );
							}

							if ( response && ( typeof response.status !== 'undefined' ) ) {
								if ( response.status === true ) {
									if ( file ) {
										progressBar.addClass( 'progress-bar-success' );
									}

									if ( ( typeof response.message !== 'undefined' ) && response.message ) {
										var dataHtml = $( response.message ).hide();
										var loadScripts = parseHeaders.call( dataHtml, cbgallery );

										cbgallery.element.find( '.galleryShareEdit' ).removeClass( 'hidden' ).append( dataHtml );

										parseScripts.call( dataHtml, cbgallery, loadScripts );

										dataHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

										if ( file ) {
											file.fadeOut( 'slow', function() {
												file.find( '.galleryShareUploadProgressClear' ).click();
											});
										}

										dataHtml.fadeIn( 'slow' );
									}

									cbgallery.element.trigger( 'cbgallery.upload.success', [cbgallery, file, data] );
								} else if ( response.status === false ) {
									if ( file ) {
										progressBar.addClass( 'progress-bar-danger' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											if ( cbgallery.settings.callback.upload.error ) {
												file.error = cbgallery.settings.callback.upload.error.call( cbgallery.element, cbgallery, response, file, data );
											}

											if ( file.error ) {
												file.after( file.error );
											}
										}
									}

									cbgallery.element.trigger( 'cbgallery.upload.failed', [cbgallery, file, data] );
								}
							}

							cbgallery.element.trigger( 'cbgallery.upload.always', [cbgallery, file, data] );
						}
					});

					cbgallery.dropZoneHandler = function( e ) {
						if ( ! $( e.target ).is( 'input' ) ) {
							e.preventDefault();

							cbgallery.element.find( '.galleryShareUpload' ).click();
						}
					};

					cbgallery.element.find( '.galleryShareUploadDropZone' ).on( 'click', cbgallery.dropZoneHandler );

					cbgallery.linkSaveHandler = function( e ) {
						e.preventDefault();

						var button = $( this );
						var link = cbgallery.element.find( '.galleryShareLink' );

						if ( link.val() ) {
							cbgallery.settings.request['value'] = link.val();

							$.ajax({
								url: cbgallery.settings.url,
								type: 'POST',
								dataType: 'html',
								cache: false,
								data: cbgallery.settings.request,
								beforeSend: function( jqXHR, textStatus, errorThrown ) {
									link.prop( 'disabled', true );
									button.prop( 'disabled', true );

									cbgallery.element.find( '.galleryShareLinkLoading' ).removeClass( 'hidden' );
									cbgallery.element.find( '.galleryShareLinkArea' ).removeClass( 'has-error' );
									cbgallery.element.find( '.galleryShareLinkError' ).remove();

									cbgallery.element.triggerHandler( 'cbgallery.link.send', [cbgallery, jqXHR, textStatus, errorThrown] );
								}
							}).fail( function( jqXHR, textStatus, errorThrown ) {
								cbgallery.element.find( '.galleryShareLinkLoading' ).addClass( 'hidden' );
								cbgallery.element.find( '.galleryShareLinkArea' ).addClass( 'has-error' );

								cbgallery.element.triggerHandler( 'cbgallery.link.error', [cbgallery, jqXHR, textStatus, errorThrown] );
							}).done( function( data, textStatus, jqXHR ) {
								var response = null;

								try {
									response = JSON.parse( data );
								} catch( e ) {
									response = { status: true, message: data };
								}

								cbgallery.element.find( '.galleryShareLinkLoading' ).addClass( 'hidden' );

								link.prop( 'disabled', false );
								button.prop( 'disabled', false );

								if ( response && ( typeof response.status !== 'undefined' ) ) {
									if ( response.status === true ) {
										link.val( '' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var dataHtml = $( response.message ).hide();
											var loadScripts = parseHeaders.call( dataHtml, cbgallery );

											cbgallery.element.find( '.galleryShareEdit' ).removeClass( 'hidden' ).append( dataHtml );

											parseScripts.call( dataHtml, cbgallery, loadScripts );

											dataHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

											dataHtml.fadeIn( 'slow' );
										}

										cbgallery.element.trigger( 'cbgallery.link.success', [cbgallery, response, textStatus, jqXHR] );
									} else if ( response.status === false ) {
										cbgallery.element.find( '.galleryShareLinkArea' ).addClass( 'has-error' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var error = null;

											if ( cbgallery.settings.callback.link.error ) {
												error = cbgallery.settings.callback.link.error.call( cbgallery.element, cbgallery, response, data );
											}

											if ( error ) {
												cbgallery.element.find( '.galleryShareLinkArea' ).append( error );
											}
										}

										cbgallery.element.trigger( 'cbgallery.link.failed', [cbgallery, response, textStatus, jqXHR] );
									}
								}

								cbgallery.element.triggerHandler( 'cbgallery.link.always', [cbgallery, response, textStatus, jqXHR] );
							});
						} else {
							cbgallery.element.find( '.galleryShareLinkArea' ).addClass( 'has-error' );
						}
					};

					cbgallery.element.find( '.galleryShareLinkSave' ).on( 'click', cbgallery.linkSaveHandler );
				} else if ( cbgallery.settings.mode == 'edit' ) {
					cbgallery.deleteHandler = function( e ) {
						e.preventDefault();

						var deleteUrl = $( this ).data( 'cbgallery-delete-url' );

						if ( ! deleteUrl ) {
							return false;
						}

						$.cbconfirm( $( this ).data( 'cbgallery-delete-message' ) ).done( function() {
							$.ajax({
								url: deleteUrl,
								type: 'POST',
								dataType: 'html',
								cache: false,
								beforeSend: function( jqXHR, textStatus, errorThrown ) {
									cbgallery.element.removeClass( 'panel-danger' ).addClass( 'panel-default' );
									cbgallery.element.find( '.galleryEditError' ).remove();
									cbgallery.element.find( '.galleryEditLoading' ).removeClass( 'hidden' ).css( 'line-height', cbgallery.element.outerHeight() + 'px' );

									cbgallery.element.triggerHandler( 'cbgallery.delete.send', [cbgallery, jqXHR, textStatus, errorThrown] );
								}
							}).fail( function( jqXHR, textStatus, errorThrown ) {
								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );
								cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );

								cbgallery.element.triggerHandler( 'cbgallery.delete.error', [cbgallery, jqXHR, textStatus, errorThrown] );
							}).done( function( data, textStatus, jqXHR ) {
								var response = null;

								try {
									response = JSON.parse( data );
								} catch( e ) {
									response = { status: true, message: data };
								}

								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );

								if ( response && ( typeof response.status !== 'undefined' ) ) {
									if ( response.status === true ) {
										cbgallery.element.fadeOut( 'slow', function() {
											$( this ).remove();
										});

										cbgallery.element.trigger( 'cbgallery.delete.success', [cbgallery, response, textStatus, jqXHR] );
									} else if ( response.status === false ) {
										cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var error = null;

											if ( cbgallery.settings.callback.delete.error ) {
												error = cbgallery.settings.callback.delete.error.call( cbgallery.element, cbgallery, response, data, textStatus, jqXHR );
											}

											if ( error ) {
												cbgallery.element.prepend( error );
											}
										}

										cbgallery.element.trigger( 'cbgallery.delete.failed', [cbgallery, response, textStatus, jqXHR] );
									}
								}

								cbgallery.element.triggerHandler( 'cbgallery.delete.always', [cbgallery, response, textStatus, jqXHR] );
							});
						});

						return false;
					};

					cbgallery.element.find( '.galleryEditDelete' ).on( 'click', cbgallery.deleteHandler );

					cbgallery.editHandler = function( e ) {
						e.preventDefault();

						cbgallery.element.find( '.galleryEditForm' ).ajaxSubmit({
							type: 'POST',
							dataType: 'html',
							beforeSerialize: function( form, options ) {
								cbgallery.element.triggerHandler( 'cbgallery.save.serialize', [cbgallery, form, options] );
							},
							beforeSubmit: function( formData, form, options ) {
								var validator = cbgallery.element.data( 'cbvalidate' );

								if ( validator ) {
									if ( ! validator.element.cbvalidate( 'validate' ) ) {
										return false;
									}
								}

								cbgallery.element.removeClass( 'panel-danger' ).addClass( 'panel-default' );
								cbgallery.element.find( '.galleryEditError' ).remove();
								cbgallery.element.find( '.galleryEditLoading' ).removeClass( 'hidden' ).css( 'line-height', cbgallery.element.outerHeight() + 'px' );

								cbgallery.element.triggerHandler( 'cbgallery.save.submit', [cbgallery, formData, form, options] );
							},
							error: function( jqXHR, textStatus, errorThrown ) {
								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );
								cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );

								cbgallery.element.triggerHandler( 'cbgallery.save.error', [cbgallery, jqXHR, textStatus, errorThrown] );
							},
							success: function( data, textStatus, jqXHR ) {
								var response = null;

								try {
									response = JSON.parse( data );
								} catch( e ) {
									response = { status: true, message: data };
								}

								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );

								if ( response && ( typeof response.status !== 'undefined' ) ) {
									if ( response.status === true ) {
										var dataHtml = $( data ).hide();
										var loadScripts = parseHeaders.call( dataHtml, cbgallery );

										cbgallery.element.replaceWith( dataHtml );

										parseScripts.call( dataHtml, cbgallery, loadScripts );

										dataHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

										dataHtml.fadeIn( 'slow' );

										cbgallery.element.triggerHandler( 'cbgallery.save.success', [cbgallery, response, data, textStatus, jqXHR] );
									} else if ( response.status === false ) {
										cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var error = null;

											if ( cbgallery.settings.callback.edit.error ) {
												error = cbgallery.settings.callback.edit.error.call( cbgallery.element, cbgallery, response, data, textStatus, jqXHR );
											}

											if ( error ) {
												cbgallery.element.prepend( error );
											}
										}

										cbgallery.element.triggerHandler( 'cbgallery.save.failed', [cbgallery, response, data, textStatus, jqXHR] );
									}
								}
							}
						});

						return false;
					};

					cbgallery.element.find( '.galleryEditSave' ).on( 'click', cbgallery.editHandler );
				}

				// Destroy the cbgallery element:
				cbgallery.element.on( 'remove destroy.cbgallery', function() {
					cbgallery.element.cbgallery( 'destroy' );
				});

				// Rebind the cbgallery element to pick up any data attribute modifications:
				cbgallery.element.on( 'rebind.cbgallery', function() {
					cbgallery.element.cbgallery( 'rebind' );
				});

				// If the cbgallery element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbgallery.element.on( 'modified.cbgallery', function( e, orgId, oldId, newId ) {
					if ( oldId != newId ) {
						cbgallery.element.cbgallery( 'destroy' );
						cbgallery.element.cbgallery( cbgallery.options );
					}
				});

				// If the cbgallery is cloned we need to rebind it back:
				cbgallery.element.on( 'cloned.cbgallery', function( e, oldId ) {
					$( this ).off( 'destroy.cbgallery' );
					$( this ).off( 'rebind.cbgallery' );
					$( this ).off( 'cloned.cbgallery' );
					$( this ).off( 'modified.cbgallery' );

					if ( cbgallery.settings.mode == 'modal' ) {
						$( document ).off( 'keydown', cbgallery.navigationHandler );
						$( document ).off( 'click', '.galleryModalAction', cbgallery.actionHandler );

						$( this ).off( 'cbtooltip.hidden', cbgallery.closeHandler );
						$( this ).off( 'cbtooltip.move', cbgallery.moveHandler );
						$( this ).off( 'cbtooltip.render', cbgallery.displayHandler );
					} else if ( cbgallery.settings.mode == 'share' ) {
						$( this ).find( '.galleryShareUpload' ).fileupload( 'destroy' );
						$( this ).find( '.galleryShareUploadDropZone' ).off( 'click', cbgallery.dropZoneHandler );
						$( this ).find( '.galleryShareLinkSave' ).off( 'click', cbgallery.linkSaveHandler );
					} else if ( cbgallery.settings.mode == 'edit' ) {
						$( this ).find( '.galleryEditDelete' ).on( 'click', cbgallery.deleteHandler );
						$( this ).find( '.galleryEditSave' ).on( 'click', cbgallery.editHandler );
					}

					$( this ).removeData( 'cbgallery' );
					$( this ).cbgallery( cbgallery.options );
				});

				cbgallery.element.trigger( 'cbgallery.init.after', [cbgallery] );

				// Bind the cbgallery to the element so it's reusable and chainable:
				cbgallery.element.data( 'cbgallery', cbgallery );

				// Add this instance to our instance array so we can keep track of our cbgallery instances:
				instances.push( cbgallery );
			});
		},
		rebind: function() {
			var cbgallery = $( this ).data( 'cbgallery' );

			if ( ! cbgallery ) {
				return this;
			}

			cbgallery.element.cbgallery( 'destroy' );
			cbgallery.element.cbgallery( cbgallery.options );

			return this;
		},
		destroy: function() {
			var cbgallery = $( this ).data( 'cbgallery' );

			if ( ! cbgallery ) {
				return false;
			}

			cbgallery.element.off( 'destroy.cbgallery' );
			cbgallery.element.off( 'rebind.cbgallery' );
			cbgallery.element.off( 'cloned.cbgallery' );
			cbgallery.element.off( 'modified.cbgallery' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbgallery.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			if ( cbgallery.settings.mode == 'modal' ) {
				$( document ).off( 'keydown', cbgallery.navigationHandler );
				$( document ).off( 'click', '.galleryModalAction', cbgallery.actionHandler );

				cbgallery.element.off( 'cbtooltip.hidden', cbgallery.closeHandler );
				cbgallery.element.off( 'cbtooltip.move', cbgallery.moveHandler );
				cbgallery.element.off( 'cbtooltip.render', cbgallery.displayHandler );
			} else if ( cbgallery.settings.mode == 'share' ) {
				cbgallery.element.find( '.galleryShareUpload' ).fileupload( 'destroy' );
				cbgallery.element.find( '.galleryShareUploadDropZone' ).off( 'click', cbgallery.dropZoneHandler );
				cbgallery.element.find( '.galleryShareLinkSave' ).off( 'click', cbgallery.linkSaveHandler );
			} else if ( cbgallery.settings.mode == 'edit' ) {
				cbgallery.element.find( '.galleryEditDelete' ).on( 'click', cbgallery.deleteHandler );
				cbgallery.element.find( '.galleryEditSave' ).on( 'click', cbgallery.editHandler );
			}

			cbgallery.element.removeData( 'cbgallery' );
			cbgallery.element.trigger( 'cbgallery.destroyed', [cbgallery] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function urlFilename( url ) {
		if ( typeof url != 'string' ) {
			return url;
		}

		return url.split( '/' ).pop().replace( /(?:&|\?).+/g, "" );
	}

	function parseHeaders( cbgallery ) {
		var element = ( this.jquery ? this : $( this ) );
		var headers = element.find( '.galleryHeaders' );

		if ( ! headers.length ) {
			return [];
		}

		var head = $( 'head' );
		var loadedCSS = [];
		var loadedScripts = [];

		if ( cbgallery.settings.mode == 'modal' ) {
			$( 'body > .galleryHeadersScripts' ).remove();
		}

		head.find( 'link' ).each( function() {
			var cssUrl = $( this ).attr( 'href' );

			if ( typeof cssUrl != 'undefined' ) {
				loadedCSS.push( urlFilename( cssUrl ) )
			}
		});

		head.find( 'script' ).each( function() {
			var scriptUrl = $( this ).attr( 'src' );

			if ( typeof scriptUrl != 'undefined' ) {
				loadedScripts.push( urlFilename( scriptUrl ) )
			}
		});

		headers.children( 'link' ).each( function() {
			var cssUrl = $( this ).attr( 'href' );

			if ( ( typeof cssUrl != 'undefined' ) && ( loadedCSS.indexOf( urlFilename( cssUrl ) ) !== -1 ) ) {
				$( this ).remove();
			}
		});

		var loadScripts = [];

		headers.children( 'script' ).each( function() {
			var scriptUrl = $( this ).attr( 'src' );

			if ( typeof scriptUrl == 'undefined' ) {
				loadScripts.push( this );
			} else {
				if ( loadedScripts.indexOf( urlFilename( scriptUrl ) ) === -1 ) {
					loadScripts.push( this );
				}
			}

			$( this ).remove();
		});

		return loadScripts;
	}

	function parseScripts( cbgallery, loadScripts ) {
		if ( ! loadScripts.length ) {
			return;
		}

		var element = ( this.jquery ? this : $( this ) );
		var scripts = $( '<div class="galleryHeadersScripts" style="position: absolute; display: none; height: 0; width: 0; z-index: -999;" />' );

		var loadScript = function( i ) {
			var nextScript = ( i + 1 );
			var scriptUrl = $( this ).attr( 'src' );

			if ( scriptUrl ) {
				$.ajax({
					url: scriptUrl,
					dataType: 'script'
				}).always( function() {
					scripts.append( '<script type="text/javascript" src="' + scriptUrl + '"></script>' );

					if ( typeof loadScripts[nextScript] != 'undefined' ) {
						loadScript.call( loadScripts[nextScript], nextScript );
					}
				});
			} else {
				scripts.append( '<script type="text/javascript">' + $( this ).text() + '</script>' );

				if ( typeof loadScripts[nextScript] != 'undefined' ) {
					loadScript.call( loadScripts[nextScript], nextScript );
				}
			}
		};

		loadScript.call( loadScripts[0], 0 );

		if ( cbgallery.settings.mode == 'modal' ) {
			$( 'body' ).append( scripts );
		} else {
			element.find( '.galleryHeaders' ).append( scripts );
		}
	}

	$.fn.cbgallery = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbgallery.defaults = {
		init: true,
		useData: true,
		mode: 'modal',
		url: null,
		previous: null,
		next: null,
		request: null,
		callback: {
			upload: {
				add: null,
				error: null
			},
			link: {
				error: null
			},
			delete: {
				error: null
			},
			edit: {
				error: null
			}
		}
	};
})(jQuery);