const fs = require('node:fs');
const path = require('node:path');
const { transformWithEsbuild } = require('vite');
const postcss = require('postcss');

const publicFrontend = path.resolve(__dirname, '..', 'public', 'frontend');
const frontendViews = path.resolve(__dirname, '..', 'resources', 'views', 'frontend');
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

function collectFiles(directory, extensions, files = [], shouldSkip = () => false) {
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
        const entryPath = path.join(directory, entry.name);

        if (shouldSkip(entryPath, entry)) {
            continue;
        }

        if (entry.isDirectory()) {
            collectFiles(entryPath, extensions, files, shouldSkip);
        } else if (extensions.has(path.extname(entry.name).toLowerCase())) {
            files.push(entryPath);
        }
    }

    return files;
}

async function generateFrontendIconStylesheet() {
    const sourceFiles = [
        ...collectFiles(frontendViews, new Set(['.php'])),
        ...collectFiles(path.join(publicFrontend, 'js'), new Set(['.js'])),
        ...collectFiles(publicFrontend, new Set(['.css']), [], (filePath) => (
            filePath !== publicFrontend
            && (path.basename(filePath).toLowerCase().includes('fontawesome')
                || path.basename(filePath).endsWith('.min.css')
                || filePath.split(path.sep).includes('libraries'))
        )),
    ];
    const usedIcons = new Set();

    for (const filePath of sourceFiles) {
        const source = fs.readFileSync(filePath, 'utf8');

        for (const match of source.matchAll(/\bfa-[a-zA-Z0-9-]+\b/g)) {
            usedIcons.add(match[0]);
        }
    }

    const sourcePath = path.join(
        publicFrontend,
        'libraries',
        'fontawesome',
        'css',
        'all.min.css',
    );
    const stylesheet = postcss.parse(fs.readFileSync(sourcePath, 'utf8'), {
        from: sourcePath,
    });

    stylesheet.walkRules((rule) => {
        if (!rule.nodes || !rule.nodes.some((node) => node.type === 'decl' && node.prop === 'content')) {
            return;
        }

        rule.selectors = rule.selectors.filter((selector) => {
            const iconClasses = Array.from(
                selector.matchAll(/\.((?:fa|fab|fas|far|fa-solid|fa-regular|fa-brands)-[\w-]+)(?=:{1,2}before)/g),
                (match) => match[1],
            );

            return iconClasses.length === 0 || iconClasses.some((iconClass) => usedIcons.has(iconClass));
        });

        if (rule.selectors.length === 0) {
            rule.remove();
        }
    });

    const outputPath = path.join(
        publicFrontend,
        'libraries',
        'fontawesome',
        'css',
        'frontend-subset.min.css',
    );
    const { code } = await transformWithEsbuild(stylesheet.toString(), outputPath, {
        loader: 'css',
        minify: true,
    });

    fs.writeFileSync(outputPath, code);
}

async function main() {
    await generateFrontendIconStylesheet();
    await minifyCssDirectory(publicFrontend);
    await minifyFile(path.join(publicFrontend, 'js', 'home.js'), 'js');
}

main().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});
