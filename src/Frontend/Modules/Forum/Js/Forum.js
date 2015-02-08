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
        jsFrontend.forum.textElement = $('#post');
        jsFrontend.forum.previewElement = $('#preview');

        // init libraries
        jsFrontend.forum.initHighlight();
        jsFrontend.forum.initPurify();
        jsFrontend.forum.initMarked();
        jsFrontend.forum.initTaboverride(jsFrontend.forum.textElement);

        // bind preview event
        jsFrontend.forum.bindPreview(jsFrontend.forum.textElement, jsFrontend.forum.previewElement);
    },

    initMarked: function() {

        if (jsFrontend.forum.marked == null) {

            // set instance
            jsFrontend.forum.marked = marked;

            // set options
            jsFrontend.forum.marked.setOptions({
                renderer: new jsFrontend.forum.marked.Renderer(),
                gfm: true,
                tables: true,
                breaks: true,
                pedantic: false,
                sanitize: true,
                smartLists: true,
                smartypants: false,
                highlight: function(code){
                    // code = jsFrontend.forum.purify.sanitize(code);
                    return jsFrontend.forum.highlight.highlightAuto(code).value;
                }
            });
        }
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

        $textElement.keyup(function(){

            // show preview
            $previewElement.html(jsFrontend.forum.marked($textElement.val()));

            // FIX/TWEAK: add hljs class to each code block
            $previewElement.find('code').addClass('hljs');
        });
    },

    initPurify: function() {

        if(jsFrontend.forum.purify == null && typeof(DOMPurify) !== 'undefined') {
            jsFrontend.forum.purify = DOMPurify;
        }
    }

};
$(jsFrontend.forum.init);
