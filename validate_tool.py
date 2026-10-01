import sys, json, urllib.request, subprocess

def validate_url(url):
    print('='*60)
    print('Validating URL:', url)
    print('='*60)
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req, timeout=15) as resp:
            html = resp.read()
    except Exception as e:
        print('Failed to fetch', url, ':', e)
        return []

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
        messages = data.get('messages', [])
        errors = [m for m in messages if m.get('type') == 'error']
        warnings = [m for m in messages if m.get('subType') == 'warning']
        infos = [m for m in messages if m.get('type') == 'info' and m.get('subType') != 'warning']
        print('Result:', len(errors), 'Errors,', len(warnings), 'Warnings,', len(infos), 'Info (Total:', len(messages), ')')
        
        for i, m in enumerate(messages, 1):
            mtype = m.get('type')
            subType = m.get('subType', '')
            tag = '[' + mtype.upper() + ((':' + subType.upper()) if subType else '') + ']'
            line = m.get('lastLine', '?')
            col = m.get('lastColumn', '?')
            msg = m.get('message', '')
            ext = m.get('extract', '').strip()
            ext = ' '.join(ext.split())
            print('#' + str(i), tag, 'Line', str(line) + ':' + str(col), '-', msg)
            if ext:
                print('   Extract:', ext[:160])
        return messages
    except Exception as e:
        print('Error parsing output:', e)
        print('Raw:', raw[:500])
        return []

if __name__ == '__main__':
    urls = sys.argv[1:] if len(sys.argv) > 1 else ['http://127.0.0.1:8000/en-us']
    for u in urls:
        validate_url(u)
