@props(['entetes' => []])

<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full min-w-[42rem] text-left">
        @if (! empty($entetes))
            <thead class="border-b border-gray-100 dark:border-gray-800">
                <tr>
                    @foreach ($entetes as $entete)
                        <th scope="col" class="px-5 py-3 text-theme-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ $entete }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            {{ $slot }}
        </tbody>
    </table>
</div>
