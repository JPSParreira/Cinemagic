<div {{ $attributes }}>
    <table class="table-auto border-collapse w-full">
        <thead>
        <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
            <th class="px-2 py-2 text-center">Photo</th>
            <th class="px-2 py-2 text-left">Name</th>
            <th class="px-2 py-2 text-left hidden md:table-cell">Email</th>
            <th class="px-2 py-2 text-center hidden xl:table-cell">Type</th>
            @if($showView)
                <th></th>
            @endif
            @if($showEdit)
                <th></th>
            @endif
            @if($showDelete)
                <th></th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            <tr class="border-b border-b-gray-400 dark:border-b-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800">

                <td class="px-2 py-2 text-left w-24 h-24">
                    <img class="object-center object-cover w-full h-full aspect-square rounded-full" src="{{ $user->getPhotoFullUrlAttribute()}}">
                </td>

                <td class="px-2 py-2 text-left">{{ $user->name ?? 'No Name' }}</td>

                <td class="px-2 py-2 text-left underline underline-offset-2 hidden md:table-cell">
                    <a href="mailto:{{ $user->email ?? '#' }}">
                        {{ $user->email ?? 'No email' }}
                    </a>
                </td>

                <td class="px-2 py-2 text-center hidden xl:table-cell">
                    @switch($user->type)
                        @case('A')
                            Admin
                            @break
                        @case('C')
                            Customer
                            @break
                        @case('E')
                            Employee
                            @break
                        @default
                            Unknown
                    @endswitch
                </td>

                @if($showView)
                    @can('view', $user)
                        <td class="px-2 py-2 text-center align-middle">
                            <div class="flex justify-center items-center h-full">
                                <x-table.icon-show class="px-0.5"
                                                   href="{{ route('users.show', ['user' => $user]) }}"/>
                            </div>
                        </td>
                    @else
                        <td></td>
                    @endcan
                @endif
                @if($showEdit)
                    @can('update', $user)
                        <td class="px-2 py-2 text-center align-middle">
                            <div class="flex justify-center items-center h-full">
                                <x-table.icon-edit class="px-0.5"
                                                   href="{{ route('users.edit', ['user' => $user]) }}"/>
                            </div>
                        </td>
                    @else
                        <td></td>
                    @endcan
                @endif
                @if($showDelete)
                    @can('delete', $user)
                        <td class="px-2 py-2 text-center align-middle">
                            <div class="flex justify-center items-center h-full">
                                <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('users.destroy', ['user' => $user]) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <x-table.icon-delete class="px-0.5" href="#"
                                                 onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this user?')) document.getElementById('delete-form-{{ $user->id }}').submit();" />
                        </td>
                    @else
                        <td></td>
                    @endcan
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
