@php
$rawData = $getState();

// Handle string-encoded JSON (common in database storage)
if (is_string($rawData) && !empty($rawData)) {
    try {
        $jsonData = json_decode($rawData, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
            // Successfully parsed JSON string
            $dataToDisplay = $jsonData;
        } else {
            // Not valid JSON or not an array
            $dataToDisplay = $rawData;
        }
    } catch (\Exception $e) {
        $dataToDisplay = $rawData;
    }
} else {
    $dataToDisplay = $rawData;
}
@endphp

@if(is_array($dataToDisplay) && !empty($dataToDisplay))
    <div class="space-y-4">
        @foreach($dataToDisplay as $key => $value)
            <div class="flex items-start border-b pb-2">
                <div class="font-medium text-gray-600 w-1/3">{{ $key }}:</div>
                <div class="w-2/3">
                    @if(is_array($value))
                        <pre class="text-sm bg-gray-50 p-2 rounded overflow-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                    @else
                        {{ $value }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@elseif(is_string($dataToDisplay))
    <div class="text-gray-700">{{ $dataToDisplay }}</div>
@elseif(is_null($dataToDisplay))
    <div class="text-gray-400 italic">No data</div>
@else
    <pre class="text-sm bg-gray-50 p-2 rounded overflow-auto">{{ json_encode($dataToDisplay, JSON_PRETTY_PRINT) }}</pre>
@endif
