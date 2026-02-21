export function initSidebar() {
    const sidebarToggles = document.querySelectorAll('[data-toggle="sidebar-mini"]');
    const body = document.body;

    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            if (window.innerWidth <= 991) {
                // En móviles, abrimos/cerramos el sidebar completo
                body.classList.toggle('sidebar-open');
            } else {
                // En desktop, usamos el modo mini
                body.classList.toggle('sidebar-mini');
            }
        });
    });

    // Custom Accordion Logic
    const accordionToggles = document.querySelectorAll('[data-toggle="collapse-custom"]');
    
    accordionToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            
            const targetId = toggle.getAttribute('data-target');
            const target = document.querySelector(targetId);
            
            if (!target) return;
            
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
            
            // Close other groups (Accordion behavior)
            accordionToggles.forEach(otherToggle => {
                if (otherToggle !== toggle) {
                    const otherTargetId = otherToggle.getAttribute('data-target');
                    const otherTarget = document.querySelector(otherTargetId);
                    
                    if (otherTarget) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                        otherToggle.classList.add('collapsed');
                        otherTarget.classList.remove('show');
                    }
                }
            });
            
            // Toggle current group
            if (isExpanded) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.add('collapsed');
                target.classList.remove('show');
            } else {
                toggle.setAttribute('aria-expanded', 'true');
                toggle.classList.remove('collapsed');
                target.classList.add('show');
            }
        });
    });
}
