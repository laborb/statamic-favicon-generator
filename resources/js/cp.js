import FaviconGenerator from './Components/FaviconGenerator.vue';
 
Statamic.booting(() => {
    Statamic.$components.register('favicon-generator', FaviconGenerator);
});