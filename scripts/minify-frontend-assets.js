const fs = require('node:fs');
const path = require('node:path');
const { transformWithEsbuild } = require('vite');

const publicFrontend = path.resolve(__dirname, '..', 'public', 'frontend');
const existingMinifiedFiles = new Set([
    'libraries/bootstrap.min.css',
    'libraries/nouislider.min.css',
    'libraries/fontawesome/css/all.min.css',
    'libraries/fontawesome/css/brands.min.css',
    'libraries/fontawesome/css/fontawesome.min.css',
    'libraries/fontawesome/css/regular.min.css',
    'libraries/fontawesome/css/solid.min.css',
    'libraries/fontawesome/css/svg-with-js.min.css',
    'libraries/fontawesome/css/v4-font-face.min.css',
    'libraries/fontawesome/css/v4-shims.min.css',
    'libraries/fontawesome/css/v5-font-face.min.css',
]);

async function minifyFile(filePath, loader) {
    const extension = path.extname(filePath);
    const name = path.basename(filePath, extension);
    const minifiedPath = path.join(path.dirname(filePath), `${name}.min${extension}`);
    const relativeMinifiedPath = path.relative(publicFrontend, minifiedPath).replace(/\\/g, '/');

    if (name.endsWith('.min')) {
        return;
    }

    if (existingMinifiedFiles.has(relativeMinifiedPath)) {
        return;
    }

    const source = fs.readFileSync(filePath, 'utf8');
    const { code } = await transformWithEsbuild(source, filePath, { loader, minify: true });
    fs.writeFileSync(minifiedPath, code);
}

async function minifyCssDirectory(directory) {
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
        const entryPath = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            await minifyCssDirectory(entryPath);
        } else if (entry.isFile() && entry.name.endsWith('.css')) {
            await minifyFile(entryPath, 'css');
        }
    }
}

async function main() {
    await minifyCssDirectory(publicFrontend);
    await minifyFile(path.join(publicFrontend, 'js', 'home.js'), 'js');
}

main().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});
