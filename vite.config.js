import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        {
            name: 'copy-manifest',
            closeBundle() {
                try {
                    const buildDir = path.resolve(process.cwd(), 'public/build');
                    const manifestPath = path.resolve(buildDir, 'manifest.json');
                    const viteDir = path.resolve(buildDir, '.vite');
                    const dotViteManifestPath = path.resolve(viteDir, 'manifest.json');

                    if (fs.existsSync(manifestPath)) {
                        if (!fs.existsSync(viteDir)) {
                            fs.mkdirSync(viteDir, { recursive: true });
                        }
                        fs.copyFileSync(manifestPath, dotViteManifestPath);
                    } else if (fs.existsSync(dotViteManifestPath)) {
                        fs.copyFileSync(dotViteManifestPath, manifestPath);
                    }
                } catch (e) {
                    console.error('Error syncing manifest:', e);
                }
            }
        }
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
