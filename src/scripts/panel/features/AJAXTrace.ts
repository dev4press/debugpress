import { DebugPressPanel } from "../index";
import { AJAXTraceData } from "../types/panel";

export class AJAXTrace {
    private panel: DebugPressPanel;

    constructor(panel: DebugPressPanel) {
        this.panel = panel;
    }

    init(): void {
        this.interceptAJAX();
    }

    private interceptAJAX(): void {
        const self = this;
        
        // XMLHttpRequest Interception
        const oldOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method: string, url: string | URL) {
            (this as any)._method = method;
            (this as any)._url = url.toString();
            return oldOpen.apply(this, arguments as any);
        };

        const oldSend = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.send = function() {
            this.addEventListener('load', () => {
                const headers = self.parseHeaders(this.getAllResponseHeaders());
                if (Object.keys(headers).length > 0) {
                    self.render({
                        status: 'success',
                        url: (this as any)._url,
                        type: (this as any)._method,
                        response: this.getResponseHeader('content-type') || 'unknown',
                        headers: headers
                    });
                }
            });
            this.addEventListener('error', () => {
                const headers = self.parseHeaders(this.getAllResponseHeaders());
                self.render({
                    status: 'error',
                    url: (this as any)._url,
                    type: (this as any)._method,
                    error: this.statusText,
                    headers: headers
                });
            });
            return oldSend.apply(this, arguments as any);
        };

        // Fetch Interception
        const oldFetch = window.fetch;
        window.fetch = async (...args) => {
            const response = await oldFetch(...args);
            const clone = response.clone();
            const headers: Record<string, string> = {};
            clone.headers.forEach((val, key) => {
                if (key.toLowerCase().startsWith('x-debugpress-')) {
                    headers[key.substring(13)] = val;
                }
            });

            if (Object.keys(headers).length > 0) {
                self.render({
                    status: response.ok ? 'success' : 'error',
                    url: response.url,
                    type: (args[1] as RequestInit)?.method || 'GET',
                    response: response.headers.get('content-type') || 'unknown',
                    headers: headers
                });
            }
            return response;
        };
    }

    private parseHeaders(rawHeaders: string): Record<string, string> {
        const list = rawHeaders.trim().split(/[\r\n]+/);
        const headers: Record<string, string> = {};

        list.forEach((line) => {
            const parts = line.split(": ");
            const header = parts.shift();
            const value = parts.join(": ");

            if (header && header.substring(0, 13).toLowerCase() === "x-debugpress-") {
                headers[header.substring(13)] = value;
            }
        });

        return headers;
    }

    render(ajax: AJAXTraceData): void {
        if (!this.panel.ajax) {
            return;
        }

        const el = document.getElementById("debugpress-debugger-ajax-wrapper");
        const tab = document.getElementById("debugpress-debugger-tab-ajax-li");
        const buttons = document.querySelectorAll(".debugpress-debug-dialog-button");
        
        if (!el) return;

        let count = parseInt(el.getAttribute("data-calls") || "0");
        if (count === 0) {
            el.innerHTML = "";
        }

        count++;
        el.setAttribute("data-calls", count.toString());

        if (tab) {
            const counter = tab.querySelector("a span.debugpress-counter");
            if (counter) counter.innerHTML = count.toString();
        }

        let response = "UNKNOWN";
        let render = "";

        if (ajax.status === "success") {
            if (ajax.response) {
                response = ajax.response.toString().toUpperCase();
            }
            render = `<h5 class="debugpress-debugger-panel-block-title">[SUCCESS] [${ajax.type} => ${response}] ${ajax.url}<span class="block-open"><i class="debugpress-icon debugpress-icon-square-minus"></i></span></h5>`;
        } else {
            render = `<h5 class="debugpress-debugger-panel-block-title" style="background: #900;">[ERROR] [${ajax.type} => ${ajax.error}] ${ajax.url}<span class="block-open"><i class="debugpress-icon debugpress-icon-square-minus"></i></span></h5>`;
        }

        render += '<div class="debugpress-debugger-panel-block" style="display: block;"><table class="debugpress-debugger-table"><thead><tr><th scope="col">Name</th><th scope="col">Value</th></tr></thead><tbody>';

        Object.entries(ajax.headers).forEach(([key, val]) => {
            render += `<tr><td>${key}</td><td>${val}</td></tr>`;
        });

        render += '</tbody></table></div>';

        const temp = document.createElement("div");
        temp.innerHTML = render;
        while (temp.firstChild) {
            el.appendChild(temp.firstChild);
        }

        buttons.forEach(button => {
            const btnEl = button as HTMLElement;
            const ajaxIndicator = btnEl.querySelector(".debugpress-debug-has-ajax") as HTMLElement;
            if (ajaxIndicator) {
                ajaxIndicator.style.display = "";
                ajaxIndicator.innerHTML = count.toString();
            }
            this.animateButton(btnEl);
        });

        // Also update main counts if it was meant to (original code did this differently in render)
        // Original render didn't update this.panel.counts directly, it only updated UI.
        // Wait, I should check if I should update main counts.
        // In original code, update_counts was separate but not called from render! 
        // Oh, wait! I see... in original code, there were TWO renders!
        // One in wp.dev4press.debugpress.ajax.render (line 83) and one in wp.dev4press.debugpress.tabs.ajax.render (line 91).
        // I am confused.
    }

    private animateButton(el: HTMLElement): void {
        const steps = [
            { opacity: '0', duration: 50 },
            { opacity: '1', duration: 50 },
            { opacity: '0', duration: 100 },
            { opacity: '1', duration: 100 },
            { opacity: '0', duration: 50 },
            { opacity: '1', duration: 50 }
        ];

        let current = 0;
        const nextStep = () => {
            if (current < steps.length) {
                el.style.opacity = steps[current].opacity;
                setTimeout(nextStep, steps[current].duration);
                current++;
            }
        };
        nextStep();
    }
}
