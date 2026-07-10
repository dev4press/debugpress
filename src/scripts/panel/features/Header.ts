import { DebugPressPanel } from "../index";
import { HeaderSize } from "../types/panel";

export class Header {
    private panel: DebugPressPanel;

    constructor(panel: DebugPressPanel) {
        this.panel = panel;
    }

    size(): HeaderSize {
        const header = document.getElementById("debugpress-debugger-content-header");
        const firstTabIcon = document.querySelector("#debugpress-debugger-content-header li:first-child .debugpress-tab-ctrl-icon") as HTMLElement;
        const firstTabSpan = document.querySelector("#debugpress-debugger-content-header li:first-child .debugpress-tab-ctrl-span") as HTMLElement;
        
        const iVisible = firstTabIcon ? (firstTabIcon.offsetWidth > 0 || firstTabIcon.offsetHeight > 0) : false;
        const sVisible = firstTabSpan ? (firstTabSpan.offsetWidth > 0 || firstTabSpan.offsetHeight > 0) : false;

        const allIcons = document.querySelectorAll("#debugpress-debugger-content-header .debugpress-tab-ctrl-icon");
        const allSpans = document.querySelectorAll("#debugpress-debugger-content-header .debugpress-tab-ctrl-span");

        allIcons.forEach(el => (el as HTMLElement).style.display = "inline-block");
        allSpans.forEach(el => (el as HTMLElement).style.display = "inline");

        const lis = document.querySelectorAll("#debugpress-debugger-content-header ul li");
        let elements = 0;
        let tabs = 0;

        lis.forEach(li => {
            const el = li as HTMLElement;
            tabs++;
            const style = window.getComputedStyle(el);
            const margin = parseFloat(style.marginLeft) + parseFloat(style.marginRight);
            elements += el.offsetWidth + margin;
        });

        if (!iVisible) {
            allIcons.forEach(el => (el as HTMLElement).style.display = "none");
        }
        if (!sVisible) {
            allSpans.forEach(el => (el as HTMLElement).style.display = "none");
        }

        return {
            tabs: tabs,
            icons: 24 * tabs,
            elements: elements,
            labels: elements - 24 * tabs,
            full: (header ? header.clientWidth : 0) - 128
        };
    }

    resize(): void {
        let s = 'full';

        if (window.innerWidth > 800) {
            const size = this.size();

            if (size.full < size.elements) {
                s = 'labels';

                if (size.full < size.labels) {
                    s = 'icons';
                }
            }
        }

        if (this.panel.header.state !== s) {
            this.panel.header.state = s;
            this.update();
        }
    }

    update(): void {
        const icons = document.querySelectorAll("#debugpress-debugger-content-header .debugpress-tab-ctrl-icon");
        const spans = document.querySelectorAll("#debugpress-debugger-content-header .debugpress-tab-ctrl-span");

        icons.forEach(el => {
            const element = el as HTMLElement;
            element.style.display = "inline-block";
            element.style.paddingRight = "5px";
        });
        spans.forEach(el => (el as HTMLElement).style.display = "inline");

        switch (this.panel.header.state) {
            case 'labels':
                icons.forEach(el => (el as HTMLElement).style.display = "none");
                spans.forEach(el => (el as HTMLElement).style.display = "inline");
                break;
            case 'icons':
                icons.forEach(el => {
                    const element = el as HTMLElement;
                    element.style.display = "inline-block";
                    element.style.paddingRight = "0";
                });
                spans.forEach(el => (el as HTMLElement).style.display = "none");
                break;
        }
    }
}
