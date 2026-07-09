import './index.scss';

class DebugPressAdmin {
    constructor() {
        document.addEventListener('click', (event: MouseEvent) => {
            const target = event.target as HTMLElement;
            const link = target.closest<HTMLAnchorElement>('.debugpress-panel-settings nav a.nav-tab-change');

            if (link) {
                event.preventDefault();

                const href = link.getAttribute('href');
                if (!href || !href.startsWith('#')) {
                    return;
                }

                const tab = href.substring(1);

                const allLinks = document.querySelectorAll('.debugpress-panel-settings nav a.nav-tab-change');
                allLinks.forEach((el) => el.classList.remove('nav-tab-active'));
                link.classList.add('nav-tab-active');

                const allTabs = document.querySelectorAll('.debugpress-panel-settings .tab-content');
                allTabs.forEach((el) => el.classList.remove('tab-content-active'));

                const activeTab = document.querySelector(`.debugpress-panel-settings .tab-content.nav-tab-content-${tab}`);
                if (activeTab) {
                    activeTab.classList.add('tab-content-active');
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DebugPressAdmin();
});
