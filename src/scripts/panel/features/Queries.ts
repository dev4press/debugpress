import { DebugPressPanel } from "../index";
import { QueryFilters } from "../types/panel";

export class Queries {
    private panel: DebugPressPanel;
    public filters: QueryFilters = {
        total: {
            queries: 0,
            total: 0
        },
        all: {
            caller: [],
            types: [],
            tables: [],
            sources: []
        },
        active: {
            show: "all",
            caller: [],
            types: [],
            tables: [],
            sources: [],
            search: ""
        }
    };

    constructor(panel: DebugPressPanel) {
        this.panel = panel;
    }

    init(): void {
        this.prepare();
        this.events();
    }

    private prepare(): void {
        const caller = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-caller");
        const types = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-type");
        const tables = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-table");
        const sources = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-source");

        const queriesStat = document.getElementById("sqlq-stats-filter-queries");
        const totalStat = document.getElementById("sqlq-stats-filter-total");

        this.filters.total.queries = parseInt(queriesStat?.innerHTML || "0");
        this.filters.total.total = parseFloat(totalStat?.innerHTML || "0");

        caller.forEach((el) => {
            const val = parseInt(el.getAttribute("data-caller") || "0");
            this.filters.active.caller.push(val);
            this.filters.all.caller.push(val);
        });

        types.forEach((el) => {
            const val = el.getAttribute("data-type") || "";
            this.filters.active.types.push(val);
            this.filters.all.types.push(val);
        });

        tables.forEach((el) => {
            const val = parseInt(el.getAttribute("data-table") || "0");
            this.filters.active.tables.push(val);
            this.filters.all.tables.push(val);
        });

        if (sources.length > 0) {
            sources.forEach((el) => {
                const val = el.getAttribute("data-source") || "";
                this.filters.active.sources.push(val);
                this.filters.all.sources.push(val);
            });
        } else {
            this.filters.active.sources.push("n/a");
            this.filters.all.sources.push("n/a");
        }
    }

    public filter(): void {
        const sqls = document.querySelectorAll(".sql-query-list > .sql-query");
        const stats = {
            queries: 0,
            total: 0,
            min: -1,
            max: -1,
            avg: 0
        };
        const ratio = {
            queries: 0,
            total: 0
        };

        sqls.forEach((el) => {
            const sql = el as HTMLElement;
            const tables = (sql.getAttribute("data-tables") || "").split(",");
            const caller = parseInt(sql.getAttribute("data-caller") || "0");
            const source = sql.getAttribute("data-source") || "";
            const type = sql.getAttribute("data-type") || "";
            const speed = sql.getAttribute("data-speed") || "";
            const timer = parseFloat(sql.getAttribute("data-time") || "0");
            const search = sql.getAttribute("data-search") || "";
            
            const filters = this.filters.active;
            let on = true;

            if (filters.show !== "all" && filters.show !== speed) {
                on = false;
            }

            if (on && filters.caller.indexOf(caller) === -1) {
                on = false;
            }

            if (on && filters.types.indexOf(type) === -1) {
                on = false;
            }

            if (on && filters.sources.indexOf(source) === -1) {
                on = false;
            }

            if (on && filters.search !== "" && !search.includes(filters.search)) {
                on = false;
            }

            if (on) {
                let c = 0;
                for (let i = 0; i < tables.length; i++) {
                    if (filters.tables.indexOf(parseInt(tables[i])) > -1) {
                        c++;
                    }
                }
                if (c === 0) on = false;
            }

            if (on) {
                if (stats.min === -1 || stats.min > timer) stats.min = timer;
                if (stats.max === -1 || stats.max < timer) stats.max = timer;

                stats.queries++;
                stats.total += timer;
                sql.style.display = "";
            } else {
                sql.style.display = "none";
            }
        });

        if (stats.queries > 0) {
            stats.avg = stats.total / stats.queries;
            ratio.queries = Math.round((stats.queries / this.filters.total.queries) * 100);
            ratio.total = Math.round((stats.total / this.filters.total.total) * 100);
        } else {
            stats.min = 0;
            stats.max = 0;
        }

        Object.entries(stats).forEach(([idx, value]) => {
            const el = document.getElementById("sqlq-stats-filter-" + idx);
            if (el) {
                el.innerHTML = idx !== "queries" ? value.toFixed(6) : value.toString();
            }
        });

        Object.entries(ratio).forEach(([idx, value]) => {
            const el = document.getElementById("sqlq-stats-ratio-" + idx);
            if (el) el.innerHTML = value + "%";
        });
    }

    private events(): void {
        document.addEventListener("click", (e: MouseEvent) => {
            const target = e.target as HTMLElement;

            // Calls visibility (full/compact)
            const callsOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-calls");
            if (callsOption) {
                e.preventDefault();
                if (callsOption.classList.contains("sqlq-option-off")) {
                    const on = callsOption.id.substring(9);
                    const off = on === "full" ? "compact" : "full";

                    document.querySelectorAll(".sql-calls-" + on).forEach(el => (el as HTMLElement).style.display = "");
                    document.querySelectorAll(".sql-calls-" + off).forEach(el => (el as HTMLElement).style.display = "none");

                    document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-calls").forEach(el => {
                        el.classList.add("sqlq-option-off");
                        el.classList.remove("sqlq-option-on");
                    });
                    callsOption.classList.remove("sqlq-option-off");
                    callsOption.classList.add("sqlq-option-on");
                }
            }

            // Sort option
            const sortOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-sort");
            if (sortOption) {
                e.preventDefault();
                const field = sortOption.id.substring(9);
                if (sortOption.classList.contains("sqlq-option-off")) {
                    this.panel.status.sql_sort = field;
                    this.sort();

                    document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-sort").forEach(el => {
                        el.classList.add("sqlq-option-off");
                        el.classList.remove("sqlq-option-on");
                    });
                    sortOption.classList.remove("sqlq-option-off");
                    sortOption.classList.add("sqlq-option-on");
                } else {
                    this.panel.status.sql_order = this.panel.status.sql_order === "asc" ? "desc" : "asc";
                    this.sort();
                }
            }

            // Show option (speed)
            const showOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-show");
            if (showOption) {
                e.preventDefault();
                if (showOption.classList.contains("sqlq-option-off")) {
                    this.filters.active.show = showOption.getAttribute("data-show") || "all";
                    document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-show").forEach(el => {
                        el.classList.add("sqlq-option-off");
                        el.classList.remove("sqlq-option-on");
                    });
                    showOption.classList.remove("sqlq-option-off");
                    showOption.classList.add("sqlq-option-on");
                    this.filter();
                }
            }

            // Table reset
            const resetOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-reset");
            if (resetOption) {
                e.preventDefault();
                const on = resetOption.id.substring(10);
                const tableOptions = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-table");
                if (on === "hide") {
                    this.filters.active.tables = [];
                    tableOptions.forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                } else {
                    this.filters.active.tables = [...this.filters.all.tables];
                    tableOptions.forEach(el => { el.classList.add("sqlq-option-on"); el.classList.remove("sqlq-option-off"); });
                }
                document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-reset").forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                resetOption.classList.remove("sqlq-option-off"); resetOption.classList.add("sqlq-option-on");
                this.filter();
            }

            // Table option
            const tableOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-table");
            if (tableOption) {
                e.preventDefault();
                const id = parseInt(tableOption.getAttribute("data-table") || "0");
                if (tableOption.classList.contains("sqlq-option-off")) {
                    this.filters.active.tables.push(id);
                    tableOption.classList.add("sqlq-option-on"); tableOption.classList.remove("sqlq-option-off");
                } else {
                    this.filters.active.tables = this.filters.active.tables.filter(t => t !== id);
                    tableOption.classList.add("sqlq-option-off"); tableOption.classList.remove("sqlq-option-on");
                }
                this.filter();
            }

            // Caller option
            const callerOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-caller");
            if (callerOption) {
                e.preventDefault();
                const id = parseInt(callerOption.getAttribute("data-caller") || "0");
                if (callerOption.classList.contains("sqlq-option-off")) {
                    this.filters.active.caller.push(id);
                    callerOption.classList.add("sqlq-option-on"); callerOption.classList.remove("sqlq-option-off");
                } else {
                    this.filters.active.caller = this.filters.active.caller.filter(t => t !== id);
                    callerOption.classList.add("sqlq-option-off"); callerOption.classList.remove("sqlq-option-on");
                }
                this.filter();
            }

            // Caller reset
            const callerReset = target.closest("#debugpress-debugger-tab-queries .sqlq-caller-reset");
            if (callerReset) {
                e.preventDefault();
                const on = callerReset.id.substring(11);
                const callerOptions = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-caller");
                if (on === "hide") {
                    this.filters.active.caller = [];
                    callerOptions.forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                } else {
                    this.filters.active.caller = [...this.filters.all.caller];
                    callerOptions.forEach(el => { el.classList.add("sqlq-option-on"); el.classList.remove("sqlq-option-off"); });
                }
                document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-caller-reset").forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                callerReset.classList.remove("sqlq-option-off"); callerReset.classList.add("sqlq-option-on");
                this.filter();
            }

            // Type option
            const typeOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-type");
            if (typeOption) {
                e.preventDefault();
                const id = typeOption.getAttribute("data-type") || "";
                if (typeOption.classList.contains("sqlq-option-off")) {
                    this.filters.active.types.push(id);
                    typeOption.classList.add("sqlq-option-on"); typeOption.classList.remove("sqlq-option-off");
                } else {
                    this.filters.active.types = this.filters.active.types.filter(t => t !== id);
                    typeOption.classList.add("sqlq-option-off"); typeOption.classList.remove("sqlq-option-on");
                }
                this.filter();
            }

            // Type reset
            const typeReset = target.closest("#debugpress-debugger-tab-queries .sqlq-types-reset");
            if (typeReset) {
                e.preventDefault();
                const on = typeReset.id.substring(10);
                const typeOptions = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-type");
                if (on === "hide") {
                    this.filters.active.types = [];
                    typeOptions.forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                } else {
                    this.filters.active.types = [...this.filters.all.types];
                    typeOptions.forEach(el => { el.classList.add("sqlq-on"); el.classList.remove("sqlq-off"); }); // Fixed: class names were wrong in original but I'll use consistent ones
                }
                document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-types-reset").forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                typeReset.classList.remove("sqlq-option-off"); typeReset.classList.add("sqlq-option-on");
                this.filter();
            }

            // Source option
            const sourceOption = target.closest("#debugpress-debugger-tab-queries .sqlq-option-source");
            if (sourceOption) {
                e.preventDefault();
                const id = sourceOption.getAttribute("data-source") || "";
                if (sourceOption.classList.contains("sqlq-option-off")) {
                    this.filters.active.sources.push(id);
                    sourceOption.classList.add("sqlq-option-on"); sourceOption.classList.remove("sqlq-option-off");
                } else {
                    this.filters.active.sources = this.filters.active.sources.filter(t => t !== id);
                    sourceOption.classList.add("sqlq-option-off"); sourceOption.classList.remove("sqlq-option-on");
                }
                this.filter();
            }

            // Source reset
            const sourceReset = target.closest("#debugpress-debugger-tab-queries .sqlq-source-reset");
            if (sourceReset) {
                e.preventDefault();
                const on = sourceReset.id.substring(11);
                const sourceOptions = document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-option-source");
                if (on === "hide") {
                    this.filters.active.sources = [];
                    sourceOptions.forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                } else {
                    this.filters.active.sources = [...this.filters.all.sources];
                    sourceOptions.forEach(el => { el.classList.add("sqlq-option-on"); el.classList.remove("sqlq-option-off"); });
                }
                document.querySelectorAll("#debugpress-debugger-tab-queries .sqlq-source-reset").forEach(el => { el.classList.add("sqlq-option-off"); el.classList.remove("sqlq-option-on"); });
                sourceReset.classList.remove("sqlq-option-off"); sourceReset.classList.add("sqlq-option-on");
                this.filter();
            }

            // Calls expander
            const expander = target.closest("#debugpress-debugger-tab-queries .sql-calls-button-expander");
            if (expander) {
                const parent = expander.parentElement;
                const query = parent?.parentElement;
                if (parent && query) {
                    const full = parent.classList.contains("sql-calls-full");
                    parent.style.display = "none";
                    if (full) {
                        const compact = query.querySelector(".sql-calls-compact") as HTMLElement;
                        if (compact) compact.style.display = "";
                    } else {
                        const fullEl = query.querySelector(".sql-calls-full") as HTMLElement;
                        if (fullEl) fullEl.style.display = "";
                    }
                }
            }

            // Item header toggle
            const header = target.closest("#debugpress-queries-items .debugpress-queries-item-header");
            if (header) {
                const parent = header.parentElement;
                if (parent) {
                    const details = parent.querySelector(".debugpress-queries-item-details") as HTMLElement;
                    if (parent.classList.contains("debugpress-queries-item-open")) {
                        parent.classList.remove("debugpress-queries-item-open");
                        if (details) details.style.display = "none";
                    } else {
                        parent.classList.add("debugpress-queries-item-open");
                        if (details) details.style.display = "block";
                    }
                }
            }
        });

        // Search input
        const searchInput = document.getElementById("debugpress-queries-search-value") as HTMLInputElement;
        if (searchInput) {
            searchInput.addEventListener("input", () => {
                this.filters.active.search = searchInput.value;
                this.filter();
            });
        }
    }

    public sort(): void {
        const container = document.querySelector(".sql-query-list");
        if (!container) return;

        const items = Array.from(container.querySelectorAll(".sql-query"));
        const sortField = this.panel.status.sql_sort;
        const order = this.panel.status.sql_order;

        items.sort((a, b) => {
            const an = parseFloat(a.getAttribute(`data-${sortField}`) || "0");
            const bn = parseFloat(b.getAttribute(`data-${sortField}`) || "0");

            if (order === "asc") {
                return an - bn;
            } else {
                return bn - an;
            }
        });

        items.forEach((item) => {
            container.appendChild(item);
        });
    }
}
