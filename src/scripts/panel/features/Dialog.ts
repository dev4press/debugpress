import { DebugPressPanel } from "../index";
import Flyin from "@dev4press/flyin";

export class Dialog {
    private panel: DebugPressPanel;

    constructor(panel: DebugPressPanel) {
        this.panel = panel;
    }

    init(): void {
        this.create();
        this.events();
    }

    create(): void {
        const self = this;

        this.panel.popup = new Flyin("#debugpress-debugger-content-wrapper", {
            settings: {
                name: "debugpress-popup",
                style: "debugpress-light",
                modal: true,
                effect: "fade",
                closeEscape: true,
                onLoad: false,
                onLoadDelay: 200,
                positionX: "center",
                positionY: "center",
                width: "95%",
                height: "90%",
                maxWidth: "100%",
                maxHeight: "100%",
                minWidth: "20%",
                minHeight: "20%",
                offsetX: "0px",
                offsetY: "0px",
                buttonFooter: false,
                titleTag: "div",
                keysOpen: window.debugpress_data.mousetrap ? window.debugpress_data.mousetrap_sequence : null,
                keysClose: window.debugpress_data.mousetrap ? window.debugpress_data.mousetrap_sequence : null,
                headerContent: document.getElementById("debugpress-debugger-content-header") || undefined,
                footerContent: document.getElementById("debugpress-debugger-content-footer") || undefined,
                buttonXContent: "<i aria-hidden=\"true\" class=\"debugpress-icon debugpress-icon-power-off\"></i>",
                storeCode: "debugpress-settings-" + (this.panel.admin ? "admin" : "frontend"),
                save: this.panel.default_layout_object()
            },
            callbacks: {
                prepared: function(skin: any) {
                    self.panel.layout = { ...self.panel.default_layout_object(), ...skin.settings.save };
                    self.load_state(skin);
                },
                ready: function(core: any) {
                    self.panel.layout.last = core.skinInstance.settings.onLoad ? "open" : "closed";
                },
                afterOpen: function(skin: any) {
                    const activeTab = document.querySelector("#debugpress-debugger-tabs .debugpress-tab-active a") as HTMLElement;

                    if (activeTab) {
                        activeTab.focus();
                    }

                    self.panel.layout.last = "open";
                    self.save_state(skin);
                    self.panel.head.resize();
                },
                afterClose: function(skin: any) {
                    self.panel.layout.last = "closed";
                    self.save_state(skin);
                }
            }
        });
    }

    reposition(skin?: any): void {
        const move: any = {
            positionX: "center",
            positionY: "center",
            offsetX: 0,
            offsetY: 0
        };
        const size: any = {
            width: "95%",
            height: "90%"
        };
        const ratio = parseInt(this.panel.layout.ratio) + "%";

        switch (this.panel.layout.layout) {
            case "right":
                move.positionX = "right";
                size.width = ratio;
                size.height = "100%";
                break;
            case "left":
                move.positionX = "left";
                size.width = ratio;
                size.height = "100%";
                break;
            case "top":
                move.positionY = "top";
                size.width = "100%";
                size.height = ratio;
                break;
            case "bottom":
                move.positionY = "bottom";
                size.width = "100%";
                size.height = ratio;
                break;
        }

        const targetSkin = skin || this.panel.popup;

        if (targetSkin) {
            targetSkin.mod({ settings: { modal: this.panel.layout.modal === "show" } });
            targetSkin.move(move);
            targetSkin.resize(size);
        }

        this.panel.head.resize();
    }

    save_state(skin?: any): void {
        const targetSkin = skin || this.panel.popup;

        if (targetSkin) {
            targetSkin.mod({ settings: { save: this.panel.layout } });
            targetSkin.save();
        }
    }

    load_state(skin: any): void {
        this.tab(this.panel.layout.tab);

        this.reposition(skin);
    }

    events(): void {
        window.addEventListener("resize", () => this.panel.head.resize());
        window.addEventListener("orientationchange", () => this.panel.head.resize());

        document.addEventListener("click", (e: MouseEvent) => {
            const target = e.target as HTMLElement;

            const openButton = target.closest("button.debugpress-action-dialog-open");
            if (openButton) {
                e.preventDefault();
                const tab = openButton.getAttribute("data-tab");
                this.open(tab || undefined);
            }

            const sidebarItem = target.closest("#debugpress-sidebar .debugpress-sidebar-item, #debugpress-debugger-tabs li a");
            if (sidebarItem) {
                e.preventDefault();
                const tab = sidebarItem.getAttribute("data-tab") || (sidebarItem.getAttribute("href") || "").substring(1);

                if (tab && tab !== "debugpress-debugger-tab-close") {
                    this.tab(tab);
                } else if (tab === "debugpress-debugger-tab-close") {
                    this.close();
                }
            }

            // Layout/Modal settings
            const layoutAction = target.closest(".debugpress-action-layout");
            if (layoutAction) {
                e.preventDefault();
                const layout = layoutAction.getAttribute("data-layout");
                if (layout) {
                    this.panel.layout.layout = layout;
                    document.querySelectorAll(".debugpress-action-layout").forEach(el => el.classList.remove("debugpress-option-active"));
                    layoutAction.classList.add("debugpress-option-active");
                    this.reposition();
                    this.save_state();
                }
            }

            const modalAction = target.closest(".debugpress-action-modal");
            if (modalAction) {
                e.preventDefault();
                const modal = modalAction.getAttribute("data-modal");
                if (modal) {
                    this.panel.layout.modal = modal;
                    document.querySelectorAll(".debugpress-action-modal").forEach(el => el.classList.remove("debugpress-option-active"));
                    modalAction.classList.add("debugpress-option-active");
                    this.reposition();
                    this.save_state();
                }
            }

            // Block toggle
            const toggleSpan = target.closest(".debugpress-debugger-panel-block-title span");
            if (toggleSpan) {
                const parent = toggleSpan.parentElement;
                if (parent) {
                    const block = parent.nextElementSibling as HTMLElement;
                    const icon = toggleSpan.querySelector("i");
                    
                    if (block && block.classList.contains("debugpress-debugger-panel-block")) {
                        if (block.style.display === "none") {
                            block.style.display = "block";
                            if (icon) {
                                icon.classList.remove("debugpress-icon-square-plus");
                                icon.classList.add("debugpress-icon-square-minus");
                            }
                        } else {
                            block.style.display = "none";
                            if (icon) {
                                icon.classList.remove("debugpress-icon-square-minus");
                                icon.classList.add("debugpress-icon-square-plus");
                            }
                        }
                    }
                }
            }
        });

        // Ratio slider
        const ratioSlider = document.getElementById("debugpress-debugger-layout-ratio") as HTMLInputElement;
        if (ratioSlider) {
            ratioSlider.addEventListener("input", () => {
                this.panel.layout.ratio = ratioSlider.value;
                const valueDisplay = document.getElementById("debugpress-debugger-layout-ratio-value");
                if (valueDisplay) valueDisplay.innerHTML = ratioSlider.value + "%";
                this.reposition();
            });
            ratioSlider.addEventListener("change", () => {
                this.save_state();
            });
        }
    }

    tab(tab: string): void {
        this.panel.layout.tab = tab;

        const sidebarItems = document.querySelectorAll("#debugpress-sidebar .debugpress-sidebar-item, #debugpress-debugger-tabs li");
        sidebarItems.forEach((item) => {
            item.classList.remove("debugpress-sidebar-item-active");
            item.classList.remove("debugpress-tab-active");
        });

        const activeSidebarItem = document.querySelector(`#debugpress-sidebar .debugpress-sidebar-item[data-tab='${tab}']`);
        if (activeSidebarItem) activeSidebarItem.classList.add("debugpress-sidebar-item-active");
        
        const activeTabItem = document.getElementById(`${tab}-li`);
        if (activeTabItem) activeTabItem.classList.add("debugpress-tab-active");

        const allTabs = document.querySelectorAll(".debugpress-tab-content");
        allTabs.forEach((t) => {
            (t as HTMLElement).style.display = "none";
            t.classList.remove("debugpress-tab-active");
        });

        const currentTab = document.getElementById(tab);
        if (currentTab) {
            currentTab.style.display = "block";
            currentTab.classList.add("debugpress-tab-active");
        }

        if (tab === "debugpress-debugger-tab-debuglog") {
            this.panel.tabs.debuglog.resize();
        }
    }

    open(tab?: string): void {
        if (tab) {
            this.tab(tab);
        }

        if (this.panel.popup) {
            this.panel.popup.open();
        }
    }

    close(): void {
        if (this.panel.popup) {
            this.panel.popup.close();
        }
    }
}
