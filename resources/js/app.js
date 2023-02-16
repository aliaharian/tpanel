require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';
import { createInertiaApp } from '@inertiajs/inertia-react';
import { InertiaProgress } from '@inertiajs/progress';
import { setRTLTextPlugin } from "!mapbox-gl";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
window.document.getElementsByTagName('html')[0].setAttribute("dir", "rtl");
// useEffect(() => {
    setRTLTextPlugin(
        "https://www.parsimap.com/scripts/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.0/mapbox-gl-rtl-text.js"
    );
// }, []);
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, App, props }) {
        return render(<App {...props} />, el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
