import { DebugPressPanel } from "../index";

export class Button {
    private panel: DebugPressPanel;

    constructor(panel: DebugPressPanel) {
        this.panel = panel;
    }

    init(): void {
        this.update();
        this.adminbar();

        document.addEventListener("click", (e: MouseEvent) => {
            const target = e.target as HTMLElement;
            const link = target.closest(".debugpress-debug-dialog-button a");

            if (link) {
                e.preventDefault();

                if (this.panel.popup) {
                    this.panel.popup.open();
                }
            }
        });
    }

    update(): void {
        const buttons = document.querySelectorAll(".debugpress-debug-dialog-button");

        buttons.forEach(button => {
            const btnEl = button as HTMLElement;

            if (this.panel.counts.total > 0) {
                const extra = this.panel.counts.errors > 0 ? "debugpress-debug-has-errors" : "debugpress-debug-has-warnings";
                
                const errorsSpan = btnEl.querySelector(".debugpress-debug-has-errors") as HTMLElement;
                if (errorsSpan) {
                    errorsSpan.style.display = "inline";
                    errorsSpan.innerHTML = this.panel.counts.total.toString();
                }

                btnEl.classList.add(extra);
            }

            if (this.panel.counts.storage !== undefined && this.panel.counts.storage > 0) {
                const storedSpan = btnEl.querySelector(".debugpress-debug-has-stored") as HTMLElement;
                if (storedSpan) {
                    storedSpan.style.display = "inline";
                    storedSpan.innerHTML = this.panel.counts.storage.toString();
                }
            }

            if (this.panel.counts.http !== undefined && this.panel.counts.http > 0) {
                const httpSpan = btnEl.querySelector(".debugpress-debug-has-httpcalls") as HTMLElement;
                if (httpSpan) {
                    httpSpan.style.display = "inline";
                    httpSpan.innerHTML = this.panel.counts.http.toString();
                }
            }
        });
    }

    private adminbar(): void {
        const bar = document.getElementById("wp-admin-bar-debugpress-debugger-button");

        if (bar) {
            const subWrapper = document.createElement("div");
            subWrapper.className = "ab-sub-wrapper";
            subWrapper.innerHTML = "<ul class='ab-sub-primary ab-submenu'></ul><ul class='ab-sub-secondary ab-submenu'></ul>";
            bar.appendChild(subWrapper);

            const statsKeys = Object.keys(this.panel.stats);
            statsKeys.forEach((label, i) => {
                const sel = (i + 1) === statsKeys.length ? ".ab-sub-secondary" : ".ab-sub-primary";
                const ul = subWrapper.querySelector(sel);

                if (ul) {
                    const li = document.createElement("li");
                    li.innerHTML = `<a class='ab-item'>${label}: <span>${this.panel.stats[label]}</span></a>`;
                    ul.appendChild(li);
                }
            });
        }
    }
}
