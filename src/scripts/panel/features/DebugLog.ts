export class DebugLog {
    constructor() {
    }

    init(): void {
        document.addEventListener("click", (e: MouseEvent) => {
            const target = e.target as HTMLElement;
            const button = target.closest("button.debugpress-action-debuglog-load");
            if (button) {
                e.preventDefault();
                this.loadDebugLog();
            }
        });

        window.addEventListener("resize", () => this.resize());
        window.addEventListener("orientationchange", () => this.resize());
    }

    private async loadDebugLog(): Promise<void> {
        const url = `${window.debugpress_data.ajax_endpoint}?action=debugpress_load_debuglog&_ajax_nonce=${window.debugpress_data.call_nonce}`;
        
        try {
            const response = await fetch(url, { cache: "no-cache" });
            if (response.ok) {
                const html = await response.text();
                const contentDiv = document.querySelector("#debugpress-debuglog-content div") as HTMLElement;

                if (contentDiv) {
                    contentDiv.innerHTML = html;

                    this.resize();

                    contentDiv.scrollTop = contentDiv.scrollHeight;
                }
            }
        } catch (error) {
            console.error("DebugPress: Failed to load debug log", error);
        }
    }

    resize(): void {
        const contentDiv = document.querySelector("#debugpress-debuglog-content div") as HTMLElement;
        const flyinContent = document.querySelector(".flyin-style-debugpress-light .flyin-content") as HTMLElement;

        if (contentDiv && flyinContent) {
            contentDiv.style.height = `${flyinContent.offsetHeight - 65}px`;
        }
    }
}
