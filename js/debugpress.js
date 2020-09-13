/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global debugpress_data*/

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.dev4press = window.wp.dev4press || {};

    window.wp.dev4press.debugpress = {
        ajax: false,
        tab: "",
        status: {
            sql_sort: "order",
            sql_order: "asc"
        },
        counts: {
            total: 0,
            errors: 0,
            doingitwrong: 0,
            deprecated: 0
        },
        sort_by: function(source, attr, order, cls) {
            var i, elms = [], list = [], keys = [], key;

            $(source + " div." + cls).each(function(index) {
                key = $(this).attr(attr);

                elms[key] = this;
                keys.push(key);
            });

            if (order === "asc") {
                keys.sort(function(a, b) {
                    return a - b;
                });
            } else {
                keys.sort(function(a, b) {
                    return b - a;
                });
            }

            for (i in keys) {
                list.push(elms[keys[i]]);
            }

            $(source).empty();

            $.each(list, function(index, div) {
                $(source).append(div);
            });
        },
        init: function(counts, ajax) {
            wp.dev4press.debugpress.counts = counts;
            wp.dev4press.debugpress.ajax = ajax;

            if (wp.dev4press.debugpress.counts.total > 0) {
                var button = $(".gdpet-debug-dialog-button"),
                    extra = wp.dev4press.debugpress.counts.errors > 0 ? 'gdpet-debug-has-errors' : 'gdpet-debug-has-warnings';

                $(".gdpet-debug-has-errors", button).show().html(wp.dev4press.debugpress.counts.total);

                button.addClass(extra);
            }

            $("#gdpet-debugger-content-wrapper").smartAniPopup({
                settings: {
                    style: "gdpet-style-popup",
                    modal: true,
                    effect: "fade",
                    closeEscape: true,
                    onLoad: false,
                    width: "95%",
                    height: "90%",
                    headerContent: $("#gdpet-debugger-content-header"),
                    footerContent: $("#gdpet-debugger-content-footer"),
                    buttonXContent: "<i aria-hidden=\"true\" class=\"debugpress-icon debugpress-icon-power-off\"></i>",
                    xContentSize: true
                },
                callbacks: {
                    afterOpen: function() {
                        $("#gdpet-debugger-tabs .gdpet-tab-active a").focus();
                    }
                }
            });

            $(document).on("click", ".gdpet-debug-dialog-button a", function(e) {
                e.preventDefault();

                $("#gdpet-debugger-content-wrapper").smartAniPopup("open");
            });

            $(document).on("change", "#gdpet-debugger-select", function() {
                wp.dev4press.debugpress.tab_change($(this).val());
            });

            $(document).on("click", ".gdpet-querie-sidebar-control span", function(e) {
                e.preventDefault();

                var tab = $(".gdpet-tab-content.gdpet-tab-active"),
                    state = $(this).data("state");

                if (state === "open") {
                    tab.addClass("gdpet-queries-sidebar-closed");
                    $(this).data("state", "closed");
                    $("i", this).removeClass("debugpress-icon-chevron-left").addClass("debugpress-icon-chevron-right");
                } else {
                    tab.removeClass("gdpet-queries-sidebar-closed");
                    $(this).data("state", "open");
                    $("i", this).removeClass("debugpress-icon-chevron-right").addClass("debugpress-icon-chevron-left");
                }
            });

            $(document).on("click", ".gdpet-debugger-panel-block-title span", function(e) {
                e.preventDefault();

                var block = $(this).parent().next();

                if ($(this).hasClass("block-open")) {
                    $(this).removeClass("block-open");
                    $(this).find("i").attr("class", "debugpress-icon debugpress-icon-plus");

                    block.hide();
                } else {
                    $(this).addClass("block-open");
                    $(this).find("i").attr("class", "debugpress-icon debugpress-icon-minus");

                    block.show();
                }
            });

            $(document).on("click", "#gdpet-debugger-content-wrapper .gdpet-events-log-toggle", function(e) {
                e.preventDefault();

                var opened = $(this).hasClass("gdpet-events-log-toggle-opened");

                if (opened) {
                    $(this).html(gdpet_debugger_data.events_show_details);

                    $(this).removeClass("gdpet-events-log-toggle-opened");
                    $(this).next().removeClass("gdpet-events-log-toggler-opened");
                } else {
                    $(this).html(gdpet_debugger_data.events_hide_details);

                    $(this).addClass("gdpet-events-log-toggle-opened");
                    $(this).next().addClass("gdpet-events-log-toggler-opened");
                }
            });

            $(document).on("keydown", '#gdpet-debugger-tabs [role="tab"]', function(e) {
                var first = $(this), current, theid,
                    prev = $(this).parents('li').prev().children('[role="tab"]'),
                    next = $(this).parents('li').next().children('[role="tab"]');

                switch (e.keyCode) {
                    case 37:
                        current = prev;
                        break;
                    case 39:
                        current = next;
                        break;
                    default:
                        current = false;
                        break;
                }

                if (current.length) {
                    first.attr({
                        'tabindex': '-1',
                        'aria-selected': null
                    });
                    current.attr({
                        'tabindex': '0',
                        'aria-selected': true
                    }).focus();

                    theid = $(document.activeElement).attr('href').substring(1);

                    $('#gdpet-debugger-content-wrapper [role="tabpanel"]').attr('aria-hidden', 'true');
                    $('#gdpet-debugger-content-wrapper #' + theid).attr('aria-hidden', null);

                    wp.dev4press.debugpress.tab_change(theid);
                }
            });

            $(document).on("click", ".gdp_r a.gdp_r_c", function(e) {
                e.preventDefault();

                if ($(this).hasClass("gdp_r_aa")) {
                    var button = $(this).find(".gdp_r_a");
                    var branch = $("#" + $(this).data("branch"));

                    if (branch) {
                        if (branch.hasClass("gdp_r_open")) {
                            button.html(debugpress_data.icon_right);
                        } else {
                            button.html(debugpress_data.icon_down);
                        }

                        branch.toggleClass("gdp_r_open");
                    }
                }
            });

            $("#gdpet-debugger-tabs li a").click(function(e) {
                e.preventDefault();

                wp.dev4press.debugpress.tab_change($(this).attr("href").substr(1));
            });

            if (wp.dev4press.debugpress.ajax) {
                wp.dev4press.debugpress.tabs.ajax.init();
            }

            wp.dev4press.debugpress.tabs.queries.init();
        },
        tabs: {
            ajax: {
                init: function() {
                    $(document).ajaxSuccess(function(event, response, options) {
                        var ajax = {
                            url: options.url,
                            data: options.data.toString(),
                            type: options.type,
                            response: options.dataType,
                            headers: wp.dev4press.debugpress.tabs.ajax.headers(response)
                        };

                        wp.dev4press.debugpress.tabs.ajax.render(ajax);
                    });
                },
                render: function(ajax) {
                    var el = $("#gdpet-debugger-ajax-wrapper"),
                        tab = $("#gdpet-debugger-tab-ajax-li"),
                        button = $(".gdpet-debug-dialog-button"),
                        count = parseInt(el.data("calls")),
                        render = '';

                    if (count === 0) {
                        el.html("");
                    }

                    count++;
                    el.data("calls", count);
                    $("a span", tab).html(count);

                    render = '<h5 class="gdpet-debugger-panel-block-title">[' + ajax.type + ' => ' + ajax.response.toUpperCase() + '] ' + ajax.url + '<span class="block-open"><i class="debugpress-icon debugpress-icon-minus"></i></span></h5>';
                    render += '<div class="gdpet-debugger-panel-block" style="display: block;"><table class="gdpet-debugger-table"><thead><tr><th scope="col" class="" style="">Name</th><th scope="col" class="" style="">Value</th></tr></thead><tbody>';

                    $.each(ajax.headers, function(key, val) {
                        render += '<tr><td>' + key + '</td><td>' + val + '</td></tr>';
                    });

                    render += '</tbody></table></div>';

                    el.append(render);

                    $(".gdpet-debug-has-ajax", button).show().html(count);
                    button.fadeOut(50).fadeIn(50).fadeOut(100).fadeIn(100).fadeOut(50).fadeIn(50);
                },
                headers: function(response) {
                    var raw = response.getAllResponseHeaders(),
                        list = raw.trim().split(/[\r\n]+/), headers = {};

                    $.each(list, function(idx, line) {
                        var parts = line.split(': '),
                            header = parts.shift(),
                            value = parts.join(': ');

                        if (header.substring(0, 13).toLowerCase() === 'x-debugpress-') {
                            header = header.substring(13);
                            headers[header] = value;
                        }
                    });

                    return headers;
                }
            },
            queries: {
                filters: {
                    total: {
                        queries: 0,
                        total: 0
                    },
                    all: {
                        caller: [],
                        types: [],
                        tables: []
                    },
                    active: {
                        show: 'all',
                        caller: [],
                        types: [],
                        tables: []
                    }
                },
                init: function() {
                    this.prepare();
                    this.events();
                },
                prepare: function() {
                    var caller = $(".sqlq-option-caller"),
                        types = $(".sqlq-option-type"),
                        tables = $(".sqlq-option-table");

                    wp.dev4press.debugpress.tabs.queries.filters.total.queries = parseInt($("#sqlq-stats-filter-queries").html());
                    wp.dev4press.debugpress.tabs.queries.filters.total.total = parseFloat($("#sqlq-stats-filter-total").html());

                    caller.each(function() {
                        var caller = parseInt($(this).data("caller"));

                        wp.dev4press.debugpress.tabs.queries.filters.active.caller.push(caller);
                        wp.dev4press.debugpress.tabs.queries.filters.all.caller.push(caller);
                    });

                    types.each(function() {
                        wp.dev4press.debugpress.tabs.queries.filters.active.types.push($(this).data("type"));
                        wp.dev4press.debugpress.tabs.queries.filters.all.types.push($(this).data("type"));
                    });

                    tables.each(function() {
                        var table = parseInt($(this).data("table"));

                        wp.dev4press.debugpress.tabs.queries.filters.active.tables.push(table);
                        wp.dev4press.debugpress.tabs.queries.filters.all.tables.push(table);
                    });
                },
                filter: function() {
                    var sqls = $(".sql-query-list > .sql-query"),
                        stats = {
                            queries: 0,
                            total: 0,
                            min: false,
                            max: false,
                            avg: 0
                        },
                        ratio = {
                            queries: 0,
                            total: 0
                        };

                    sqls.each(function() {
                        var caller = $(this).data("caller"),
                            tables = ($(this).data("tables") + "").split(","),
                            type = $(this).data("type"),
                            speed = $(this).data("speed"),
                            timer = parseFloat($(this).data("time")),
                            filters = wp.dev4press.debugpress.tabs.queries.filters.active,
                            on = true, i, c;

                        if (filters.show !== "all" && filters.show !== speed) {
                            on = false;
                        }

                        if (on && filters.caller.indexOf(caller) === -1) {
                            on = false;
                        }

                        if (on && filters.types.indexOf(type) === -1) {
                            on = false;
                        }

                        if (on) {
                            c = 0;

                            for (i = 0; i < tables.length; i++) {
                                if (filters.tables.indexOf(parseInt(tables[i])) > -1) {
                                    c++;
                                }
                            }

                            if (c === 0) {
                                on = false;
                            }
                        }

                        if (on) {
                            if (stats.min === false) {
                                stats.min = timer;
                            }

                            if (stats.max === false) {
                                stats.max = timer;
                            }

                            stats.queries++;
                            stats.total += timer;

                            if (stats.min > timer) {
                                stats.min = timer;
                            }

                            if (stats.max < timer) {
                                stats.max = timer;
                            }

                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });

                    if (stats.queries > 0) {
                        stats.avg = stats.total / stats.queries;

                        ratio.queries = ((stats.queries / wp.dev4press.debugpress.tabs.queries.filters.total.queries) * 100).toFixed(0);
                        ratio.total = ((stats.total / wp.dev4press.debugpress.tabs.queries.filters.total.total) * 100).toFixed(0);
                    } else {
                        stats.min = 0;
                        stats.max = 0;
                    }

                    $.each(stats, function(idx, value) {
                        if (idx !== "queries") {
                            value = parseFloat(value).toFixed(6);
                        }

                        $("#sqlq-stats-filter-" + idx).html(value);
                    });

                    $.each(ratio, function(idx, value) {
                        $("#sqlq-stats-ratio-" + idx).html(value + "%");
                    });
                },
                events: function() {
                    $(document).on("click", ".sql-calls-button-expander", function(e) {
                        var parent = $(this).parent(),
                            query = parent.parent(),
                            full = parent.hasClass("sql-calls-full")

                        if (full) {
                            parent.hide();
                            $(".sql-calls-compact", query).show();
                        } else {
                            parent.hide();
                            $(".sql-calls-full", query).show();
                        }
                    });

                    $(document).on("click", ".sqlq-option-calls", function(e) {
                        e.preventDefault();

                        if ($(this).hasClass("sqlq-option-off")) {
                            var on = $(this).attr("id").substr(9);
                            var off = on === "full" ? "compact" : "full";

                            $(".sql-calls-" + on).show();
                            $(".sql-calls-" + off).hide();

                            $(".sqlq-option-calls").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                            $(this).removeClass("sqlq-option-off").addClass("sqlq-option-on");
                        }

                    });

                    $(document).on("click", ".sqlq-option-sort", function(e) {
                        e.preventDefault();

                        if ($(this).hasClass("sqlq-option-off")) {
                            wp.dev4press.debugpress.status.sql_sort = $(this).attr("id").substr(9);

                            wp.dev4press.debugpress.sort_by(".sql-query-list", "data-" + wp.dev4press.debugpress.status.sql_sort, wp.dev4press.debugpress.status.sql_order, "sql-query");

                            $(".sqlq-option-sort").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                            $(this).removeClass("sqlq-option-off").addClass("sqlq-option-on");
                        } else {
                            wp.dev4press.debugpress.status.sql_order = wp.dev4press.debugpress.status.sql_order === "asc" ? "desc" : "asc";
                            wp.dev4press.debugpress.sort_by(".sql-query-list", "data-" + wp.dev4press.debugpress.status.sql_sort, wp.dev4press.debugpress.status.sql_order, "sql-query");
                        }
                    });

                    $(document).on("click", ".sqlq-option-show", function(e) {
                        e.preventDefault();

                        if ($(this).hasClass("sqlq-option-off")) {
                            wp.dev4press.debugpress.tabs.queries.filters.active.show = $(this).data("show");

                            $(".sqlq-option-show").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                            $(this).removeClass("sqlq-option-off").addClass("sqlq-option-on");

                            wp.dev4press.debugpress.tabs.queries.filter();
                        }
                    });

                    $(document).on("click", ".sqlq-option-reset", function(e) {
                        e.preventDefault();

                        var on = $(this).attr("id").substr(10);

                        if (on === "hide") {
                            wp.dev4press.debugpress.tabs.queries.filters.active.tables = [];

                            $(".sqlq-option-table").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        } else {
                            wp.dev4press.debugpress.tabs.queries.filters.active.tables = wp.dev4press.debugpress.tabs.queries.filters.all.tables;

                            $(".sqlq-option-table").addClass("sqlq-option-on").removeClass("sqlq-option-off");
                        }

                        $(".sqlq-option-reset").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        $(this).removeClass("sqlq-option-off").addClass("sqlq-option-on");

                        wp.dev4press.debugpress.tabs.queries.filter();
                    });

                    $(document).on("click", ".sqlq-option-table", function(e) {
                        e.preventDefault();

                        var id = $(this).data("table");

                        if ($(this).hasClass("sqlq-option-off")) {
                            wp.dev4press.debugpress.tabs.queries.filters.active.tables.push(id);
                            $(this).addClass("sqlq-option-on").removeClass("sqlq-option-off");
                        } else {
                            wp.dev4press.debugpress.tabs.queries.filters.active.tables = wp.dev4press.debugpress.tabs.queries.filters.active.tables.filter(function(e) {
                                return e !== id;
                            });
                            $(this).addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        }

                        wp.dev4press.debugpress.tabs.queries.filter();
                    });

                    $(document).on("click", ".sqlq-option-caller", function(e) {
                        e.preventDefault();

                        var id = $(this).data("caller");

                        if ($(this).hasClass("sqlq-option-off")) {
                            wp.dev4press.debugpress.tabs.queries.filters.active.caller.push(id);
                            $(this).addClass("sqlq-option-on").removeClass("sqlq-option-off");
                        } else {
                            wp.dev4press.debugpress.tabs.queries.filters.active.caller = wp.dev4press.debugpress.tabs.queries.filters.active.caller.filter(function(e) {
                                return e !== id;
                            });
                            $(this).addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        }

                        wp.dev4press.debugpress.tabs.queries.filter();
                    });

                    $(document).on("click", ".sqlq-option-type", function(e) {
                        e.preventDefault();

                        var id = $(this).data("type");

                        if ($(this).hasClass("sqlq-option-off")) {
                            wp.dev4press.debugpress.tabs.queries.filters.active.types.push(id);
                            $(this).addClass("sqlq-option-on").removeClass("sqlq-option-off");
                        } else {
                            wp.dev4press.debugpress.tabs.queries.filters.active.types = wp.dev4press.debugpress.tabs.queries.filters.active.types.filter(function(e) {
                                return e !== id;
                            });
                            $(this).addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        }

                        wp.dev4press.debugpress.tabs.queries.filter();
                    });

                    $(document).on("click", ".sqlq-caller-reset", function(e) {
                        e.preventDefault();

                        var on = $(this).attr("id").substr(11);

                        if (on === "hide") {
                            wp.dev4press.debugpress.tabs.queries.filters.active.caller = [];

                            $(".sqlq-option-caller").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        } else {
                            wp.dev4press.debugpress.tabs.queries.filters.active.caller = wp.dev4press.debugpress.tabs.queries.filters.all.caller;

                            $(".sqlq-option-caller").addClass("sqlq-option-on").removeClass("sqlq-option-off");
                        }

                        $(".sqlq-caller-reset").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        $(this).removeClass("sqlq-option-off").addClass("sqlq-option-on");

                        wp.dev4press.debugpress.tabs.queries.filter();
                    });

                    $(document).on("click", ".sqlq-types-reset", function(e) {
                        e.preventDefault();

                        var on = $(this).attr("id").substr(10);

                        if (on === "hide") {
                            wp.dev4press.debugpress.tabs.queries.filters.active.types = [];

                            $(".sqlq-option-type").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        } else {
                            wp.dev4press.debugpress.tabs.queries.filters.active.types = wp.dev4press.debugpress.tabs.queries.filters.all.types;

                            $(".sqlq-option-type").addClass("sqlq-option-on").removeClass("sqlq-option-off");
                        }

                        $(".sqlq-types-reset").addClass("sqlq-option-off").removeClass("sqlq-option-on");
                        $(this).removeClass("sqlq-option-off").addClass("sqlq-option-on");

                        wp.dev4press.debugpress.tabs.queries.filter();
                    });
                }
            }
        },
        tab_change: function(tab) {
            if (tab !== wp.dev4press.debugpress.tab) {
                wp.dev4press.debugpress.tab = tab;

                $("#gdpet-debugger-tabs li").removeClass("gdpet-tab-active");
                $("#" + tab + "-li").addClass("gdpet-tab-active");
                $("#gdpet-debugger-content-wrapper div.gdpet-tab-content").removeClass("gdpet-tab-active");
                $("#" + tab).addClass("gdpet-tab-active");

                $("#gdpet-debugger-tabs li a").attr({
                    'tabindex': '-1',
                    'aria-selected': null
                });

                $("#" + tab + "-li a").attr({
                    'tabindex': '0',
                    'aria-selected': true
                });

                $("#gdpet-debugger-select").val(tab);
            }
        }
    };
})(jQuery, window, document);
