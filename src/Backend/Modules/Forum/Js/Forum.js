/**
 * Interaction for the forum module
 *
 * @author	Glenn Coppens <glenn.coppens@gmail.com>
 */
jsBackend.forum =
{
	// init, something like a constructor
	init: function()
	{
		// variables
		$title = $('#title');

		jsBackend.forum.controls.init();

		// do meta
		if($title.length > 0) $title.doMeta();

        // set the textarea id
        jsBackend.forum.textElement = $('#text');
        jsBackend.forum.previewElement = $('#preview');

        // init libraries
        jsBackend.forum.initHighlight();
        jsBackend.forum.initPurify();
        jsBackend.forum.initTaboverride(jsBackend.forum.textElement);

        // bind preview event
        jsBackend.forum.bindPreview(jsBackend.forum.textElement, jsBackend.forum.previewElement);

        // load preview when page is refreshed on #tabPreview
        jsBackend.forum.initPreviewTab();
	},

    initPreviewTab: function() {
        var href = $('.ui-tabs-selected').find('a#preview-button').attr('href');
        $previewButton = $('#preview-button');

        if(href === '#tabPreview') {
            $previewButton.trigger('click');
        }
    },

    initHighlight: function() {

        if (jsBackend.forum.highlight == null && typeof(hljs) !== 'undefined') {

            // set instance
            jsBackend.forum.highlight = hljs;

            // set some highlight.js options
            jsBackend.forum.highlight.configure({
                tabReplace: '    ' // 4 spaces
            });

            // init highlight on page load
            jsBackend.forum.highlight.initHighlightingOnLoad();
        }
    },

    initTaboverride: function($textElement) {

        if(jsBackend.forum.tabOverride == null  && typeof(tabOverride) !== 'undefined') {

            // get instance
            jsBackend.forum.tabOverride = tabOverride;

            // options
            jsBackend.forum.tabOverride.tabSize(4);
            jsBackend.forum.tabOverride.autoIndent(true);
            jsBackend.forum.tabOverride.set($textElement);
        }
    },

    bindPreview: function($textElement, $previewElement) {
        $previewButton = $('#preview-button');
        $previewError = $('#preview-error');


        $previewButton.click(function(e) {
            e.preventDefault();

            // hide both by every click
            $previewError.hide();
            $previewElement.hide();

            // make the async parsedown call
            $.ajax(
                {
                    data:
                    {
                        fork: { module: 'Forum', action: 'ParseMarkdown' },
                        text: $textElement.val(),
                        type: 'default' // types are: 'default' or 'github'
                    },
                    success: function(json, textStatus)
                    {
                        if(json.code != 200)
                        {
                            // show error if needed
                            if(jsBackend.debug) alert(textStatus);

                            // show error message
                            $previewError.html(jsBackend.locale.msg('PreviewError'));
                        }
                        else
                        {
                            // get parsed data
                            var parsed = json.data;

                            // show preview
                            $previewElement.html(parsed);

                            // highlight code blocks
                            $previewElement.find('pre code').each(function(i, code) {
                                $(code).addClass('hljs');
                                jsBackend.forum.highlight.highlightBlock( code );
                            });

                            // show preview
                            $previewElement.show();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {

                        // set message
                        $previewError.html(jsBackend.locale.msg('PreviewError'));

                        // show error message
                        $previewError.show();
                    }

                });
        });
    },

    initPurify: function() {

        if(jsBackend.forum.purify == null && typeof(DOMPurify) !== 'undefined') {
            jsBackend.forum.purify = DOMPurify;
        }
    }
};

jsBackend.forum.controls =
{
	currentCategory: null,

	// init, something like a constructor
	init: function()
	{
		// variables
		$saveAsDraft = $('#saveAsDraft');
		$filter = $('#filter');
		$filterCategory = $('#filter #category');
		$addCategoryDialog = $('#addCategoryDialog');
		$categoryTitle = $('#categoryTitle');
		$categoryTitleError = $('#categoryTitleError');
		$categoryId = $('#categoryId');

		$saveAsDraft.on('click', function(e)
		{
			$('form').append('<input type="hidden" name="status" value="draft" />').submit();
		});

		$filterCategory.on('change', function(e)
		{
			$filter.submit();
		});

		if($addCategoryDialog.length > 0)
		{
			$addCategoryDialog.dialog(
			{
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				buttons:
				[
					{
						text: utils.string.ucfirst(jsBackend.locale.lbl('OK')),
						click: function()
						{
							// hide errors
							$categoryTitleError.hide();

							$.ajax(
							{
								data:
								{
									fork: { action: 'AddCategory' },
									value: $('#categoryTitle').val()
								},
								success: function(json, textStatus)
								{
									if(json.code != 200)
									{
										// show error if needed
										if(jsBackend.debug) alert(textStatus);

										// show message
										$categoryTitleError.show();
									}
									else
									{
										// add and set selected
										$categoryId.append('<option value="'+ json.data.id +'">'+ json.data.title +'</option>');

										// reset value
										jsBackend.forum.controls.currentCategory = json.data.id;

										// close dialog
										$addCategoryDialog.dialog('close');
									}
								}
							});
						}
					},
					{
						text: utils.string.ucfirst(jsBackend.locale.lbl('Cancel')),
						click: function()
						{
							// close the dialog
							$(this).dialog('close');
						}
					}
				],
				close: function(e, ui)
				{
					// reset value to previous selected item
					$categoryId.val(jsBackend.forum.controls.currentCategory);
				}
			});

			// bind change
			$categoryId.on('change', function(e)
			{
				// new category?
				if($(this).val() == 'new_category')
				{
					// prevent default
					e.preventDefault();

					// open dialog
					$addCategoryDialog.dialog('open');
				}

				// reset current category
				else jsBackend.forum.controls.currentCategory = $categoryId.val();
			});
		}

		jsBackend.forum.controls.currentCategory = $categoryId.val();
	}
};

$(jsBackend.forum.init);
