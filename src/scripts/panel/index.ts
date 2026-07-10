import Flyin from '@dev4press/flyin';
import {DebugLog} from './features/DebugLog';
import {AJAXTrace} from './features/AJAXTrace';
import {Hooks} from './features/Hooks';
import {Queries} from './features/Queries';
import {Dialog} from './features/Dialog';
import {Button} from './features/Button';
import {Header} from './features/Header';
import {DebugPressCounts, DebugPressHeader, DebugPressLayout, DebugPressStatus} from './types/panel';
import './index.scss';

export class DebugPressPanel {
    public layout: DebugPressLayout;
    public header: DebugPressHeader = {
        state: "full"
    };
    public status: DebugPressStatus = {
        sql_sort: "order",
        sql_order: "asc"
    };
    public counts: DebugPressCounts = {
        total: 0,
        errors: 0,
        doingitwrong: 0,
        deprecated: 0,
        http: 0,
        storage: 0
    };
    public stats: Record<string, string> = {};
    public ajax: boolean = false;
    public admin: boolean = false;
    public tab: string = "";

    public tabs: {
        debuglog: DebugLog;
        ajax: AJAXTrace;
        hooks: Hooks;
        queries: Queries;
    };
    public dialog: Dialog;
    public button: Button;
    public head: Header;
    public popup: Flyin | null = null;

    constructor() {
        this.layout = this.default_layout_object();
        this.tabs = {
            debuglog: new DebugLog(),
            ajax: new AJAXTrace(this),
            hooks: new Hooks(),
            queries: new Queries(this)
        };
        this.dialog = new Dialog(this);
        this.button = new Button(this);
        this.head = new Header(this);
    }

    public default_layout_object(): DebugPressLayout {
        return {
            layout: "full",
            modal: "show",
            open: "manual",
            ratio: "40",
            tab: "debugpress-debugger-tab-basics",
            last: "closed",
            close: []
        };
    }

    public init(counts: DebugPressCounts, stats: Record<string, string>, ajax: boolean, admin: boolean): void {
        this.counts = counts;
        this.stats = stats;
        this.ajax = ajax;
        this.admin = admin;

        this.button.init();
        this.dialog.init();

        if (this.ajax) {
            this.tabs.ajax.init();
        }

        this.tabs.debuglog.init();
        this.tabs.queries.init();
        this.tabs.hooks.init();

        this.head.resize();
    }
}

// Global exposure
const DebugPressPanelInstance = new DebugPressPanel();
window.DebugPressPanelInstance = DebugPressPanelInstance;
