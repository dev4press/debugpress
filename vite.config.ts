import {defineConfig} from 'vite';
import {resolve} from 'path';
import {copyFileSync, existsSync, mkdirSync, unlinkSync} from 'node:fs';

export default defineConfig(({mode}) => {
    const isPanel = mode === 'panel';
    const isPrint = mode === 'print';
    const entry = isPrint ? 'print' : (isPanel ? 'panel' : 'admin');
    const ext = isPrint ? 'scss' : 'ts';

    return {
        css: {
            lightningcss: {
                errorRecovery: true,
            },
        },
        build: {
            outDir: 'dist',
            emptyOutDir: mode === 'admin',
            minify: 'terser',
            lib: {
                entry: resolve(__dirname, `src/scripts/${entry}/index.${ext}`),
                formats: ['iife'],
                name: `DebugPress${entry.charAt(0).toUpperCase() + entry.slice(1)}`,
                fileName: () => `${entry}.js`,
            },
            rollupOptions: {
                output: {
                    assetFileNames: `${entry}.[ext]`,
                },
            },
        },
        plugins: [
            {
                name: 'copy-flyin-assets',
                apply: 'build',
                closeBundle() {
                    if (mode === 'admin') {
                        const distDir = resolve(__dirname, 'dist');
                        const flyinDist = resolve(__dirname, 'node_modules/@dev4press/flyin/dist');

                        if (!existsSync(distDir)) {
                            mkdirSync(distDir, {recursive: true});
                        }

                        try {
                            copyFileSync(resolve(flyinDist, 'flyin.css'), resolve(distDir, 'flyin.css'));
                            copyFileSync(resolve(flyinDist, 'flyin.umd.js'), resolve(distDir, 'flyin.js'));
                        } catch (e) {
                        }
                    }
                },
            },
            {
                name: 'remove-js-output',
                apply: 'build',
                writeBundle(options, bundle) {
                    if (isPrint) {
                        for (const fileName in bundle) {
                            if (fileName.endsWith('.js')) {
                                try {
                                    unlinkSync(resolve(options.dir!, fileName));
                                } catch (e) {
                                    // Ignore errors
                                }
                            }
                        }
                    }
                },
            },
        ],
    };
});
