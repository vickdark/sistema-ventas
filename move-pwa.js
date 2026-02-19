import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const srcDir = path.join(__dirname, 'public/build');
const destDir = path.join(__dirname, 'public');

console.log('Moviendo archivos PWA de build a public...');

if (!fs.existsSync(srcDir)) {
    console.error('Directorio public/build no existe. Ejecuta vite build primero.');
    process.exit(1);
}

const files = fs.readdirSync(srcDir);

files.forEach(file => {
    if (file === 'sw.js' || file === 'manifest.webmanifest' || file.startsWith('workbox-')) {
        const srcPath = path.join(srcDir, file);
        const destPath = path.join(destDir, file);
        
        fs.copyFileSync(srcPath, destPath);
        console.log(`Copiado: ${file}`);
    }
});

console.log('Archivos PWA movidos correctamente.');
