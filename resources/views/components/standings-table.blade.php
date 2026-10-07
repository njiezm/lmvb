@props(['standings', 'compact' => false, 'highlight' => null])
<div class="scrollbar-thin -mx-4 overflow-x-auto sm:mx-0">
    <table class="tabular w-full min-w-[20rem] text-sm">
        <thead>
            <tr class="border-b border-slate-200 text-left text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">
                <th class="py-2 pl-4 pr-2 sm:pl-2" scope="col">#</th>
                <th class="px-2 py-2" scope="col">Équipe</th>
                <th class="px-2 py-2 text-center" scope="col" title="Points">Pts</th>
                <th class="px-2 py-2 text-center" scope="col" title="Matchs joués">J</th>
                <th class="px-2 py-2 text-center" scope="col" title="Victoires">G</th>
                <th class="px-2 py-2 text-center" scope="col" title="Défaites">P</th>
                @unless ($compact)
                    <th class="hidden px-2 py-2 text-center md:table-cell" scope="col" title="Forfaits">F</th>
                    <th class="hidden px-2 py-2 text-center sm:table-cell" scope="col" title="Sets pour / contre">Sets</th>
                    <th class="hidden px-2 py-2 text-center lg:table-cell" scope="col" title="Ratio de sets">Ratio S.</th>
                    <th class="hidden px-2 py-2 text-center lg:table-cell" scope="col" title="Points pour / contre">Points</th>
                @endunless
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($standings as $row)
                <tr @class(['transition hover:bg-navy-50/60', 'bg-lavande-100/60' => $highlight && $row->club_id === $highlight])>
                    <td class="py-2.5 pl-4 pr-2 sm:pl-2">
                        <span @class(['inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold',
                                      'bg-navy-800 text-white' => $row->position === 1,
                                      'bg-navy-100 text-navy-800' => $row->position > 1 && $row->position <= 4,
                                      'text-slate-500' => $row->position > 4])>{{ $row->position }}</span>
                    </td>
                    <td class="px-2 py-2.5">
                        @if ($row->club && $row->club->active)
                            <a href="{{ route('clubs.show', $row->club) }}" class="flex items-center gap-2 font-semibold text-slate-800 hover:text-navy-700">
                                <x-club-badge :club="$row->club" size="xs" class="hidden sm:inline-flex" />
                                <span class="truncate">{{ $row->display_name }}</span>
                            </a>
                        @else
                            <span class="font-semibold text-slate-800">{{ $row->display_name }}</span>
                        @endif
                    </td>
                    <td class="px-2 py-2.5 text-center font-display text-base font-extrabold text-navy-800">{{ $row->points }}</td>
                    <td class="px-2 py-2.5 text-center text-slate-600">{{ $row->played }}</td>
                    <td class="px-2 py-2.5 text-center text-slate-600">{{ $row->won }}</td>
                    <td class="px-2 py-2.5 text-center text-slate-600">{{ $row->lost }}</td>
                    @unless ($compact)
                        <td class="hidden px-2 py-2.5 text-center text-slate-500 md:table-cell">{{ $row->forfeits ?: '–' }}</td>
                        <td class="hidden px-2 py-2.5 text-center text-slate-600 sm:table-cell">{{ $row->sets_for }}/{{ $row->sets_against }}</td>
                        <td class="hidden px-2 py-2.5 text-center text-slate-500 lg:table-cell">{{ $row->set_ratio !== null ? number_format($row->set_ratio, 2, ',', '') : '–' }}</td>
                        <td class="hidden px-2 py-2.5 text-center text-slate-500 lg:table-cell">{{ $row->points_for }}/{{ $row->points_against }}</td>
                    @endunless
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
