import urllib.request, json

url = "http://127.0.0.1:8000/en-us"
with urllib.request.urlopen(url) as resp:
    html = resp.read()

req_w3c = urllib.request.Request(
    'https://validator.w3.org/nu/?out=json',
    data=html,
    headers={
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        'Content-Type': 'text/html; charset=utf-8'
    },
    method='POST'
)

try:
    with urllib.request.urlopen(req_w3c, timeout=30) as resp:
        data = json.loads(resp.read().decode('utf-8'))
        messages = data.get('messages', [])
        print(f"Total messages: {len(messages)}")
        errors = [m for m in messages if m.get('type') == 'error']
        warnings = [m for m in messages if m.get('subType') == 'warning']
        print(f"Errors: {len(errors)}, Warnings: {len(warnings)}")
        for idx, m in enumerate(errors, 1):
            line = m.get('lastLine', '?')
            col = m.get('lastColumn', '?')
            msg = m.get('message', '')
            extract = m.get('extract', '')
            print(f"[{idx}] Line {line}:{col} - {msg}")
            if extract:
                print(f"    Extract: {repr(extract)}")
except Exception as e:
    print("Error:", e)
