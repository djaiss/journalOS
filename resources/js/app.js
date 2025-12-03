import 'instant.page';

// --- Turbo Drive ---
import * as Turbo from '@hotwired/turbo';
window.Turbo = Turbo;
Turbo.session.drive = false; // explicit (enabled by default)

// --- Alpine ---
import Alpine from 'alpinejs';
import ajax from '@imacrayon/alpine-ajax';
import Popover from './components/popover';

window.Alpine = Alpine;
Alpine.plugin(ajax);
Alpine.data('popover', Popover);

// Start Alpine on the initial load (once)
document.addEventListener('DOMContentLoaded', () => {
  if (!document.documentElement.__alpined) {
    Alpine.start();
    document.documentElement.__alpined = true;
  }
});

// Re-initialize Alpine after every Turbo-driven navigation
addEventListener('turbo:load', () => {
  if (window.Alpine?.initTree) Alpine.initTree(document.body);
});

// If you need page-specific teardown, you can hook before Turbo renders the new DOM:
// addEventListener('turbo:before-render', (event) => { /* cleanup here */ });
