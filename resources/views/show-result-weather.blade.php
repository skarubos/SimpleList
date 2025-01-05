<x-app-layout>

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 overflow-hidden mb-6 px-6 pb-5 shadow-sm sm:rounded-lg">
            <div class="p-6">
                {{ __("天気データ") }}
            </div>
            <table border="1">
                <thead>
                    <tr>
                        <th>時間</th>
                        <th>温度 (℃)</th>
                        <th>湿度 (%)</th>
                        <th>日射量 (kJ/m²)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weatherData as $data)
                        <tr>
                            <td>{{ $data['time'] }}</td>
                            <td>{{ $data['temperature'] }}</td>
                            <td>{{ $data['humidity'] }}</td>
                            <td>{{ $data['sunlight'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
</script>
</x-app-layout>
