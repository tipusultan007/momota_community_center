import './bootstrap';
// CoreUI bundle is loaded via <script> tags from public/coreui (copied from coreui/dist).
// No Tabler import — Tabler admin template was replaced by CoreUI.
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
