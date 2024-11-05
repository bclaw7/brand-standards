document.addEventListener('DOMContentLoaded', function () {
    // Initialize tabs
    const tabContainers = document.querySelectorAll('.wp-block-brand-standards-logo-tabs');

    tabContainers.forEach(container => {
        const tabs = container.querySelectorAll('[role="tab"]');
        const panels = container.querySelectorAll('[role="tabpanel"]');

        tabs.forEach(tab => {
            tab.addEventListener('click', e => {
                e.preventDefault();
                const targetId = tab.getAttribute('aria-controls');

                // Update tab states
                tabs.forEach(t => t.setAttribute('aria-selected', 'false'));
                tab.setAttribute('aria-selected', 'true');

                // Update panel visibility
                panels.forEach(panel => {
                    panel.hidden = panel.id !== targetId;
                });
            });
        });
    });
});