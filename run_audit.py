import sys, json, urllib.request, subprocess

pages = [
    '/en-us',
    '/en-us/web-hosting',
    '/en-us/crimson-leaf-cafe',
    '/en-us/top-rated',
    '/en-us/product-comparison',
    '/en-us/community-guidelines',
    '/en-us/help-center',
    '/en-us/contact',
    '/en-us/cookie-policy',
    '/en-us/privacy-policy',
    '/en-us/legal-notice',
    '/en-us/copyright-dmca-policy',
    '/en-us/expert-guides',
    '/en-us/login',
    '/en-us/register',
    '/en-us/vendor-advertising',
    '/en-us/vendor-get-listed',
    '/en-us/vendor-help',
    '/en-us/vendor-how-it-works',
    '/en-us/vendor-overview',
    '/en-us/vendor-register',
    '/en-us/write-review',
    '/en-us/questions-answers',
]

results = {}

for path in pages:
    url = f'http://127.0.0.1:8000{path}'
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req, timeout=15) as resp:
            html = resp.read()
    except Exception as e:
        results[path] = {'status': 'error', 'error': str(e)}
        print(f'{path}: FAILED ({e})')
        continue

    p = subprocess.Popen(
        ['java', '-jar', '/home/ubuntu/vnu.jar', '--format', 'json', '-'],
        stdin=subprocess.PIPE,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE
    )
    stdout, stderr = p.communicate(input=html)
    raw = stderr.decode('utf-8') if stderr else stdout.decode('utf-8')
    
    try:
        data = json.loads(raw)
        msgs = data.get('messages', [])
        errors = [m for m in msgs if m.get('type') == 'error']
        warnings = [m for m in msgs if m.get('subType') == 'warning']
        results[path] = {'status': 'ok', 'errors': errors, 'warnings': warnings}
        print(f'{path}: {len(errors)} Errors, {len(warnings)} Warnings')
    except Exception as e:
        results[path] = {'status': 'error', 'error': str(e)}
        print(f'{path}: parse error ({e})')

with open('/home/ubuntu/projects/localio-live/audit_results.json', 'w') as f:
    json.dump(results, f, indent=2)
print('Audit finished. Saved to audit_results.json')
