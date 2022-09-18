const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    mode: 'jit',
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Segoe UI'],
                montserrat: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                ms: '0 4px 8px -1px rgb(0 0 0 / 10%)'
            },
            colors: {
                primary: '#be1522',
                dark: {
                    400: '#24292e',
                    500: '#1c2127'
                },
                'ms-gray': {
                    220: '#11100f',
                    210: '#161514',
                    200: '#1b1a19',
                    190: '#201f1e',
                    180: '#252423',
                    170: '#292827',
                    160: '#323130',
                    150: '#3b3a39',
                    140: '#484644',
                    130: '#605e5c',
                    120: '#797775',
                    110: '#8a8886',
                    100: '#979593',
                    90: '#a19f9d',
                    80: '#b3b0ad',
                    70: '#bebbb8',
                    60: '#c8c6c4',
                    50: '#d2d0ce',
                    40: '#e1dfdd',
                    30: '#edebe9',
                    20: '#f3f2f1',
                    10: '#faf9f8',
                },
                'ms-blue': {
                    20: '#00bcf2'
                },
                'ms-orange': {
                    20: "#ca5010",
                    10: "#ffaa44"
                },
                'ms-cyan': {
                    120: "#004e8c",
                    110: "#0078d4",
                    30: "#005b70",
                    20: "#038387",
                    10: "#00b7c3"
                },
                'ms-magenta': {
                    120: "#881798",
                    110: "#c239b3",
                    30: "#5c2e91",
                    20: "#8764b8",
                    10: "#8378de"
                }
            }
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
