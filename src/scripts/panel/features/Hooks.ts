import { HookFilters } from "../types/panel";

export class Hooks {
    public filters: HookFilters = {
        all: {
            types: []
        },
        active: {
            show: "all",
            types: []
        }
    };

    constructor() {
    }

    init(): void {
        this.prepare();
        this.events();
    }

    private prepare(): void {
        const types = document.querySelectorAll("#debugpress-debugger-tab-hooks .sqlq-option-type");
        types.forEach((el) => {
            const type = el.getAttribute("data-type");
            if (type) {
                this.filters.active.types.push(type);
                this.filters.all.types.push(type);
            }
        });
    }

    private filter(): void {
        this.filters.all.types.forEach((type) => {
            const selector = "dbg-hook-" + type.replace(/::/g, "--");
            const elements = document.querySelectorAll(`table.dbg-hooks-actions .${selector}, table#dbg-hooks-list > tbody > tr.${selector}`);
            elements.forEach((el) => (el as HTMLElement).style.display = "none");
        });

        this.filters.active.types.forEach((type) => {
            const selector = "dbg-hook-" + type.replace(/::/g, "--");
            const elements = document.querySelectorAll(`table.dbg-hooks-actions .${selector}, table#dbg-hooks-list > tbody > tr.${selector}`);
            elements.forEach((el) => (el as HTMLElement).style.display = "");
        });
    }

    private events(): void {
        document.addEventListener("click", (e: MouseEvent) => {
            const target = e.target as HTMLElement;

            // Search button
            const searchButton = target.closest("button.debugpress-action-hooks-search");
            if (searchButton) {
                e.preventDefault();
                this.performSearch();
            }

            // Item header toggle
            const header = target.closest("#debugpress-hooks-items .debugpress-hooks-item-header");
            if (header) {
                const parent = header.parentElement;
                if (parent) {
                    const details = parent.querySelector(".debugpress-hooks-item-details") as HTMLElement;
                    if (parent.classList.contains("debugpress-hooks-item-open")) {
                        parent.classList.remove("debugpress-hooks-item-open");
                        if (details) details.style.display = "none";
                    } else {
                        parent.classList.add("debugpress-hooks-item-open");
                        if (details) details.style.display = "block";
                    }
                }
            }

            // Callbacks visibility
            const callbackOption = target.closest("#debugpress-debugger-tab-hooks .sqlq-option-callbacks");
            if (callbackOption) {
                e.preventDefault();
                if (callbackOption.classList.contains("sqlq-option-off")) {
                    const on = callbackOption.id.substring(9);
                    const tds = document.querySelectorAll(".dbg-hooks-actions .dbg-hook-column-action");
                    
                    tds.forEach((td) => {
                        if (on === "full") {
                            td.classList.add("dbg-calls-show");
                        } else {
                            td.classList.remove("dbg-calls-show");
                        }
                    });

                    document.querySelectorAll("#debugpress-debugger-tab-hooks .sqlq-option-callbacks").forEach(el => {
                        el.classList.add("sqlq-option-off");
                        el.classList.remove("sqlq-option-on");
                    });
                    callbackOption.classList.remove("sqlq-option-off");
                    callbackOption.classList.add("sqlq-option-on");
                }
            }

            // Type filter
            const typeOption = target.closest("#debugpress-debugger-tab-hooks .sqlq-option-type");
            if (typeOption) {
                e.preventDefault();
                const id = typeOption.getAttribute("data-type");
                if (id) {
                    if (typeOption.classList.contains("sqlq-option-off")) {
                        this.filters.active.types.push(id);
                        typeOption.classList.add("sqlq-option-on");
                        typeOption.classList.remove("sqlq-option-off");
                    } else {
                        this.filters.active.types = this.filters.active.types.filter(t => t !== id);
                        typeOption.classList.add("sqlq-option-off");
                        typeOption.classList.remove("sqlq-option-on");
                    }
                    this.filter();
                }
            }

            // Reset filters
            const resetOption = target.closest("#debugpress-debugger-tab-hooks .sqlq-types-reset");
            if (resetOption) {
                e.preventDefault();
                const on = resetOption.id.substring(10);
                const allTypesOptions = document.querySelectorAll("#debugpress-debugger-tab-hooks .sqlq-option-type");

                if (on === "hide") {
                    this.filters.active.types = [];
                    allTypesOptions.forEach(el => {
                        el.classList.add("sqlq-option-off");
                        el.classList.remove("sqlq-option-on");
                    });
                } else {
                    this.filters.active.types = [...this.filters.all.types];
                    allTypesOptions.forEach(el => {
                        el.classList.add("sqlq-option-on");
                        el.classList.remove("sqlq-option-off");
                    });
                }

                document.querySelectorAll("#debugpress-debugger-tab-hooks .sqlq-types-reset").forEach(el => {
                    el.classList.add("sqlq-option-off");
                    el.classList.remove("sqlq-option-on");
                });
                resetOption.classList.remove("sqlq-option-off");
                resetOption.classList.add("sqlq-option-on");

                this.filter();
            }

            // Callback expander
            const expander = target.closest("#debugpress-debugger-tab-hooks .dbg-callback-button-expander");
            if (expander) {
                const parent = expander.parentElement;
                if (parent) {
                    parent.classList.toggle("dbg-calls-show");
                }
            }
        });
    }

    private performSearch(): void {
        const searchInput = document.getElementById("debugpress-hooks-search-value") as HTMLInputElement;
        const search = searchInput?.value || "";
        const items = document.querySelectorAll("#debugpress-hooks-items .debugpress-hooks-item");

        items.forEach((item) => {
            const htmlItem = item as HTMLElement;
            if (search === "") {
                htmlItem.style.display = "";
            } else {
                const searchAttr = htmlItem.getAttribute("data-search") || "";
                if (searchAttr.includes(search)) {
                    htmlItem.style.display = "";
                } else {
                    htmlItem.style.display = "none";
                }
            }
        });
    }
}
