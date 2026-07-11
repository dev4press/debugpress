import Flyin from "@dev4press/flyin";

export interface DebugPressLayout {
    layout: string;
    modal: string;
    open: string;
    ratio: string;
    tab: string;
    last: string;
    close: Flyin | any[];
}

export interface DebugPressHeader {
    state: string;
}

export interface DebugPressStatus {
    sql_sort: string;
    sql_order: string;
}

export interface DebugPressCounts {
    total: number;
    errors: number;
    doingitwrong: number;
    deprecated: number;
    storage?: number;
    http?: number;
}

export interface DebugPressData {
    ajax_endpoint: string;
    call_nonce: string;
    mousetrap: boolean;
    mousetrap_sequence: string | null;
    events_show_details: string;
    events_hide_details: string;
    icon_right: string;
    icon_down: string;
}

export interface HookFilters {
    all: {
        types: string[];
    };
    active: {
        show: string;
        types: string[];
    };
}

export interface QueryFilters {
    total: {
        queries: number;
        total: number;
    };
    all: {
        caller: number[];
        types: string[];
        tables: number[];
        sources: string[];
    };
    active: {
        show: string;
        caller: number[];
        types: string[];
        tables: number[];
        sources: string[];
        search: string;
    };
}

export interface AJAXTraceData {
    status: 'success' | 'error';
    url: string;
    type: string;
    response?: string;
    error?: string;
    headers: Record<string, string>;
}

export interface HeaderSize {
    tabs: number;
    icons: number;
    elements: number;
    labels: number;
    full: number;
}

declare global {
    interface Window {
        debugpress_data: DebugPressData;
        wp: any;
        DebugPressPanelInstance: any;
    }
}
