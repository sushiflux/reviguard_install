@php
$badgeMap = [
    'new'      => 'badge-new',
    'add'      => 'badge-add',
    'change'   => 'badge-change',
    'fix'      => 'badge-fix',
    'remove'   => 'badge-remove',
    'security' => 'badge-security',
];
@endphp

@foreach(explode("\n", $entry->content) as $line)
    @php
        $line = trim($line);
        $badgeClass = null;
        $text = $line;

        // Zeile mit "- " oder "· " Präfix
        $isBullet = str_starts_with($line, '- ') || str_starts_with($line, '· ');
        if ($isBullet) {
            $text = ltrim($line, '-· ');
        }

        // Badge-Präfix erkennen: "Fix: ..." oder "- Fix: ..."
        if (preg_match('/^([A-Za-z]+):\s+(.+)$/', $text, $m)) {
            $keyword = strtolower($m[1]);
            if (isset($badgeMap[$keyword])) {
                $badgeClass = $badgeMap[$keyword];
                $text = $m[2];
                $isBullet = true;
            }
        }
    @endphp

    @if($line === '')
        <div style="height:.3rem;"></div>
    @elseif($isBullet)
        <div class="cl-line">
            @if($badgeClass)
                <span class="cl-badge {{ $badgeClass }}">{{ explode('-', $badgeClass)[1] }}</span>
            @else
                <div class="cl-line-dot"></div>
            @endif
            <div>{{ $text }}</div>
        </div>
    @else
        <div class="cl-text">{{ $line }}</div>
    @endif
@endforeach
