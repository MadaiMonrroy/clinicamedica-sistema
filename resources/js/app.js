import './bootstrap';

import ApexCharts from 'apexcharts';
import Datepicker from 'flowbite-datepicker/Datepicker';
import es from 'vanillajs-datepicker/locales/es';

import Alpine from 'alpinejs';
import morph from '@alpinejs/morph';
import focus from '@alpinejs/focus';

Alpine.plugin(morph);
Alpine.plugin(focus);

window.Alpine     = Alpine;
window.ApexCharts = ApexCharts;
window.Datepicker = Datepicker;

Object.assign(Datepicker.locales, es);
Datepicker.locales.es.daysMin   = ['D','L','M','M','J','V','S'];
Datepicker.locales.es.daysShort = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[datepicker]').forEach(el => {
        new Datepicker(el, {
            language: 'es',
            autohide: el.hasAttribute('datepicker-autohide'),
            format: el.getAttribute('datepicker-format') ?? 'dd/mm/yyyy',
            maxDate: el.getAttribute('datepicker-max-date') ?? null,
            orientation: 'bottom',
        });
    });
});