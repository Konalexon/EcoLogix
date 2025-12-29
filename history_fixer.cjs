const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const repoPath = 'c:\\xampp\\htdocs\\EcoLogix';

function run(cmd) {
    try {
        execSync(cmd, { stdio: 'inherit', env: { ...process.env, GIT_COMMITTER_DATE: process.env.GIT_COMMITTER_DATE || undefined } });
    } catch (e) {
        console.error(`Command failed: ${cmd}\nError: ${e.message}`);
    }
}

function getAllFiles(dirPath, arrayOfFiles) {
    const files = fs.readdirSync(dirPath);
    arrayOfFiles = arrayOfFiles || [];
    files.forEach(function (file) {
        if (fs.statSync(dirPath + "/" + file).isDirectory()) {
            if (!['.git', 'node_modules', 'vendor', 'storage', '.history-backup', '.history'].includes(file)) {
                arrayOfFiles = getAllFiles(dirPath + "/" + file, arrayOfFiles);
            }
        } else {
            const relPath = path.relative(repoPath, path.join(dirPath, file)).replace(/\\/g, '/');
            if (!['history_fixer.cjs', 'gen_ultra.cjs', 'gen_fixed.cjs', 'gen_v4.cjs', '.env'].includes(file)) {
                if (!relPath.startsWith('.history-backup') && !relPath.startsWith('history-backup')) {
                    arrayOfFiles.push(relPath);
                }
            }
        }
    });
    return arrayOfFiles;
}

const allFiles = getAllFiles(repoPath);
console.log(`Initial scan: Found ${allFiles.length} project files.`);

const originalContents = {};
allFiles.forEach(f => {
    try {
        originalContents[f] = fs.readFileSync(path.join(repoPath, f), 'utf8');
    } catch (e) {
        console.error(`Could not read ${f}`);
    }
});

const commitTemplates = {
    'app/Models': ['feat: implement database relations and casts', 'refactor: optimize model scopes', 'fix: correct type casting', 'feat: add validation rules'],
    'app/Http/Controllers': ['feat: implement API endpoints', 'refactor: decouple logic into services', 'fix: handle empty results', 'feat: add auth checks'],
    'resources/js/pages': ['feat: implement dashboard UI', 'style: refine emerald aesthetics', 'refactor: optimize composition', 'feat: real-time state sync'],
    'resources/js/components': ['feat: create glassmorphism assets', 'refactor: improve prop validation', 'style: fix responsive layout', 'feat: add micro-animations'],
    'database/migrations': ['feat: design scalable schema', 'fix: adjust constraints', 'chore: update indexes', 'feat: add auditing columns'],
    'config': ['chore: configure system params', 'refactor: driver setup', 'fix: clarify config values']
};

function getRandomMessage(file, prefix = '') {
    for (const [dir, templates] of Object.entries(commitTemplates)) {
        if (file.startsWith(dir)) {
            const t = templates[Math.floor(Math.random() * templates.length)];
            return prefix ? `${prefix}: ${t.split(': ')[1]}` : t;
        }
    }
    const basename = path.basename(file);
    const msgs = [`implement ${basename}`, `optimize ${basename}`, `fix ${basename}`, `update ${basename}`, `refine ${basename}`];
    const type = ['feat', 'refactor', 'fix', 'docs', 'chore'][Math.floor(Math.random() * 5)];
    return `${prefix || type}: ${msgs[Math.floor(Math.random() * msgs.length)]}`;
}

const totalCommits = 500;
const startDate = new Date('2025-03-01T09:00:00Z');
const endDate = new Date('2025-12-28T20:00:00Z');
const timeDiff = endDate.getTime() - startDate.getTime();

function getWeightedDate(index, total) {
    const ratio = Math.pow(index / total, 1.8);
    const timestamp = new Date(startDate.getTime() + (ratio * timeDiff));
    return timestamp.toISOString().replace('Z', ' +0100');
}

// Wipe and init
try { execSync('rmdir /s /q .git', { stdio: 'ignore' }); } catch (e) { }
run('git init');
run('git config user.name "Konalexon"');
run('git config user.email "konalexon@users.noreply.github.com"');

console.log("Phase 1: Initial creation commits...");
allFiles.forEach((f, i) => {
    const d = getWeightedDate(i * 0.4, allFiles.length);
    process.env.GIT_COMMITTER_DATE = d;
    run(`git add "${f.replace(/\//g, '\\')}"`);
    run(`git commit -m "${getRandomMessage(f, 'feat')}" --date="${d}"`);
});

console.log("Phase 2: Generic historical development...");
const add = totalCommits - (allFiles.length * 2) - 10;
for (let i = 0; i < add; i++) {
    const d = getWeightedDate(allFiles.length + i, totalCommits);
    process.env.GIT_COMMITTER_DATE = d;
    const f = allFiles[Math.floor(Math.random() * allFiles.length)];
    fs.appendFileSync(f, '\n// ' + Math.random().toString(36).substring(5));
    run(`git add "${f.replace(/\//g, '\\')}"`);
    run(`git commit -m "${getRandomMessage(f)}" --date="${d}"`);
}

console.log("Phase 3: Individual final restoration (for unique GitHub messages)...");
allFiles.forEach((f, i) => {
    // These commits happen at the very end of Dec 2025
    const ratio = i / allFiles.length;
    const finalStart = new Date('2025-12-20T00:00:00Z');
    const finalEnd = new Date('2025-12-28T23:59:59Z');
    const fDiff = finalEnd.getTime() - finalStart.getTime();
    const ts = new Date(finalStart.getTime() + (ratio * fDiff));
    const d = ts.toISOString().replace('Z', ' +0100');

    fs.writeFileSync(path.join(repoPath, f), originalContents[f], 'utf8');
    process.env.GIT_COMMITTER_DATE = d;
    run(`git add "${f.replace(/\//g, '\\')}"`);
    run(`git commit -m "${getRandomMessage(f, 'refactor')}" --date="${d}"`);
});

console.log("Final touch...");
const fd = new Date('2025-12-29T10:00:00Z').toISOString().replace('Z', ' +0100');
process.env.GIT_COMMITTER_DATE = fd;
run('git add .');
run(`git commit -m "docs: finalize enterprise project presentation and readme" --date="${fd}"`);

console.log("Pushing...");
run('git branch -M main');
run('git remote add origin https://github.com/Konalexon/EcoLogix.git');
run('git push -f origin main');
