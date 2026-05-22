<div class="table-responsive">
    <table class="table table-hover table-bordered align-middle mb-0">
        <thead class="table-light">
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                @php
                    $rowCells = $rows($record);
                @endphp
                <tr>
                    @foreach ($rowCells as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                    <td style="min-width: 220px;">
                        <form method="POST" class="d-flex flex-column gap-2">
                            @csrf
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Shënime verifikimi"></textarea>
                            <div class="d-flex gap-2">
                                <button formaction="{{ route('verifikime.approve', ['type' => $type, 'id' => $record->id]) }}" class="btn btn-sm btn-success flex-fill" type="submit">Prano</button>
                                <button formaction="{{ route('verifikime.reject', ['type' => $type, 'id' => $record->id]) }}" class="btn btn-sm btn-danger flex-fill" type="submit">Refuzo</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + 1 }}" class="text-center py-4">
                        Nuk ka rekorde në pritje për këtë kategori.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if(function_exists('method_exists') && method_exists($records, 'links'))
    <div class="mt-3">
        {{ $records->links() }}
    </div>
@endif
