const zlib = require('zlib');
const http = require('https');
const fs = require('fs');

const code = `sequenceDiagram
    actor A
    actor B
    A->>B: Hello`;

const compressed = zlib.deflateSync(code, { level: 9 });
const base64url = compressed.toString('base64url');
const url = `https://kroki.io/mermaid/png/${base64url}`;

console.log('Fetching:', url);

http.get(url, (res) => {
    console.log('Status Code:', res.statusCode);
    if (res.statusCode === 200) {
        const file = fs.createWriteStream('test_mermaid.png');
        res.pipe(file);
        file.on('finish', () => console.log('Saved test_mermaid.png successfully!'));
    } else {
        let data = '';
        res.on('data', chunk => data += chunk);
        res.on('end', () => console.log('Error Response:', data));
    }
}).on('error', (err) => {
    console.error('Error:', err);
});
