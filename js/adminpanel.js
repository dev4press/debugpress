/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.dev4press = window.wp.dev4press || {};

    window.wp.dev4press.debugpressadmin = {
        init: function() {
            $(document).on("click", ".debugpress-panel-settings nav a.nav-tab-change", function(e) {
                e.preventDefault();

                var tab = $(this).attr("href").substr(1);

                $(".debugpress-panel-settings nav a.nav-tab-change").removeClass("nav-tab-active");
                $(this).addClass("nav-tab-active");

                $(".debugpress-panel-settings .tab-content").removeClass("tab-content-active");
                $(".debugpress-panel-settings .tab-content.nav-tab-content-" + tab).addClass("tab-content-active");
            });
        }
    };

    wp.dev4press.debugpressadmin.init();
})(jQuery, window, document);
