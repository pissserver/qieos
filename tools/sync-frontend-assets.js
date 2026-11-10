const fs = require('node:fs');
const path = require('node:path');

const root = path.resolve(__dirname, '..');
const packages = path.join(root, 'node_modules');
const output = path.join(root, 'assets', 'vendor');

const files = [
    ['@fortawesome/fontawesome-free/css/all.min.css', 'fontawesome/css/all.min.css'],
    ['@fontsource-variable/inter/files/inter-latin-wght-normal.woff2', 'inter/files/inter-latin-wght-normal.woff2'],
    ['@fontsource-variable/inter/files/inter-latin-ext-wght-normal.woff2', 'inter/files/inter-latin-ext-wght-normal.woff2'],
    ['@popperjs/core/dist/umd/popper.min.js', '@popperjs/popper.min.js'],
    ['bootstrap/dist/js/bootstrap.min.js', 'bootstrap/bootstrap.min.js'],
    ['chartist/dist/chartist.min.js', 'chartist/chartist.min.js'],
    ['chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js', 'chartist-plugin-tooltips/chartist-plugin-tooltip.min.js'],
    ['datatables.net/js/jquery.dataTables.min.js', 'datatables/jquery.dataTables.min.js'],
    ['datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'datatables/dataTables.bootstrap5.min.css'],
    ['datatables.net-bs5/js/dataTables.bootstrap5.min.js', 'datatables/dataTables.bootstrap5.min.js'],
    ['jquery/dist/jquery.min.js', 'jquery/jquery.min.js'],
    ['notyf/notyf.min.css', 'notyf/notyf.min.css'],
    ['select2/dist/css/select2.min.css', 'select2/select2.min.css'],
    ['select2/dist/js/select2.min.js', 'select2/select2.min.js'],
    ['smooth-scroll/dist/smooth-scroll.polyfills.min.js', 'smooth-scroll/smooth-scroll.polyfills.min.js'],
    ['sweetalert2/dist/sweetalert2.min.css', 'sweetalert2/sweetalert2.min.css'],
    ['sweetalert2/dist/sweetalert2.all.min.js', 'sweetalert2/sweetalert2.all.min.js']
];

const licenses = [
    ['@fortawesome/fontawesome-free/LICENSE.txt', 'fontawesome-free.LICENSE.txt'],
    ['@fontsource-variable/inter/LICENSE', 'fontsource-inter.OFL.txt'],
    ['@popperjs/core/LICENSE.md', 'popperjs-core.LICENSE.md'],
    ['bootstrap/LICENSE', 'bootstrap.LICENSE'],
    ['chartist/LICENSE-MIT', 'chartist.LICENSE-MIT'],
    ['chartist-plugin-tooltips/LICENSE', 'chartist-plugin-tooltips.LICENSE'],
    ['datatables.net/License.txt', 'datatables-net.License.txt'],
    ['datatables.net-bs5/License.txt', 'datatables-net-bs5.License.txt'],
    ['jquery/LICENSE.txt', 'jquery.LICENSE.txt'],
    ['notyf/LICENSE.md', 'notyf.LICENSE.md'],
    ['select2/LICENSE.md', 'select2.LICENSE.md'],
    ['smooth-scroll/LICENSE.md', 'smooth-scroll.LICENSE.md'],
    ['sweetalert2/LICENSE', 'sweetalert2.LICENSE']
];

for (const [source, destination] of files) {
    const sourcePath = path.join(packages, source);
    const destinationPath = path.join(output, destination);
    fs.mkdirSync(path.dirname(destinationPath), { recursive: true });
    fs.copyFileSync(sourcePath, destinationPath);
}

for (const [source, destination] of licenses) {
    const sourcePath = path.join(packages, source);
    const destinationPath = path.join(output, 'licenses', destination);
    fs.mkdirSync(path.dirname(destinationPath), { recursive: true });
    fs.copyFileSync(sourcePath, destinationPath);
}

const interCss = `@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-display: swap;
  font-weight: 100 900;
  src: url('./files/inter-latin-ext-wght-normal.woff2') format('woff2');
  unicode-range: U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF;
}

@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-display: swap;
  font-weight: 100 900;
  src: url('./files/inter-latin-wght-normal.woff2') format('woff2');
  unicode-range: U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;
}`;
const interCssPath = path.join(output, 'inter', 'inter.css');
fs.mkdirSync(path.dirname(interCssPath), { recursive: true });
fs.writeFileSync(interCssPath, interCss);

fs.cpSync(
    path.join(packages, '@fortawesome/fontawesome-free/webfonts'),
    path.join(output, 'fontawesome/webfonts'),
    { recursive: true }
);
