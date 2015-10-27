/**
 * Js shizzle for the forum module
 *
 * @author	Glenn Coppens <glenn.coppens@gmail.com>
 */
jsFrontend.forum =
{
    // properties
    action: null,
    textElement: null,
    previewElement: null,

    // properties that hold the different libraries
    marked: null,
    highlight: null,
    purify: null,
    tabOverride: null,

    // init, something like a constructor
    init: function()
    {
        // set the action
        if(jsFrontend.data.exists('Forum.Settings.Action')) {
            jsFrontend.forum.action = jsFrontend.data.get('Forum.Settings.Action');
        }

        // set the textarea id
        jsFrontend.forum.textElement = $('#text');
        jsFrontend.forum.previewElement = $('#preview');

        // init libraries
        jsFrontend.forum.initHighlight();
        jsFrontend.forum.initPurify();
        jsFrontend.forum.initTaboverride(jsFrontend.forum.textElement);

        // bind preview event
        jsFrontend.forum.bindPreview(jsFrontend.forum.textElement, jsFrontend.forum.previewElement);
    },

    initHighlight: function() {

        if (jsFrontend.forum.highlight == null && typeof(hljs) !== 'undefined') {

            // set instance
            jsFrontend.forum.highlight = hljs;

            // set some highlight.js options
            jsFrontend.forum.highlight.configure({
                tabReplace: '    ' // 4 spaces
            });

            // init highlight on page load
            jsFrontend.forum.highlight.initHighlightingOnLoad();
        }
    },

    initTaboverride: function($textElement) {

        if(jsFrontend.forum.tabOverride == null  && typeof(tabOverride) !== 'undefined') {

            // get instance
            jsFrontend.forum.tabOverride = tabOverride;

            // options
            jsFrontend.forum.tabOverride.tabSize(4);
            jsFrontend.forum.tabOverride.autoIndent(true);
            jsFrontend.forum.tabOverride.set($textElement);
        }
    },

    bindPreview: function($textElement, $previewElement) {
        $previewButton = $('#preview-button');
        $previewError = $('#preview-error');
        $previewError.hide();
        $previewElement.hide();

        $previewButton.click(function(e) {
            e.preventDefault();

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
                        if(jsFrontend.debug) alert(textStatus);

                        // show error message
                        $previewError.html(jsFrontend.locale.msg('PreviewError'));
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
                            jsFrontend.forum.highlight.highlightBlock( code );
                        });

                        // show preview
                        $previewElement.show();

                        // hide error message
                        $previewError.hide();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {

                    // set message
                    $previewError.html(jsFrontend.locale.msg('PreviewError'));

                    // hide preview
                    $previewElement.hide();

                    // show error message
                    $previewError.show();
                }

            });
        });
    },

    initPurify: function() {

        if(jsFrontend.forum.purify == null && typeof(DOMPurify) !== 'undefined') {
            jsFrontend.forum.purify = DOMPurify;
        }
    }

};
$(jsFrontend.forum.init);
