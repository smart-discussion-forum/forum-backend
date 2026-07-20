{{--
    Drop this inside your existing resources/views/layouts/app.blade.php,
    inside the navigation <div> where your other nav links live. It shows
    different links depending on the logged-in user's role.

    Written with plain <a> tags rather than Breeze's <x-nav-link> component,
    since your project uses a custom AuthController rather than Breeze auth
    — if you do have resources/views/components/nav-link.blade.php, feel
    free to swap these for <x-nav-link> to match your existing nav styling.
--}}

@auth
    @if (auth()->user()->role === 'admin')
        <a href="{{ route('groups.statistics', 1) }}"
           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('groups.statistics') ? 'font-semibold' : '' }}">
            {{ __('Statistics') }}
        </a>
        <a href="{{ route('discussions.index') }}"
           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('discussions.index') ? 'font-semibold' : '' }}">
            {{ __('Moderation') }}
        </a>
    @elseif (auth()->user()->role === 'lecturer')
        <a href="{{ route('groups.create') }}"
           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('groups.create') ? 'font-semibold' : '' }}">
            {{ __('New Group') }}
        </a>
        <a href="{{ route('quizzes.create') }}"
           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('quizzes.create') ? 'font-semibold' : '' }}">
            {{ __('Schedule Quiz') }}
        </a>
    @else
        <a href="{{ route('groups.index') }}"
           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('groups.index') ? 'font-semibold' : '' }}">
            {{ __('Groups') }}
        </a>
        <a href="{{ route('quizzes.index') }}"
           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('quizzes.index') ? 'font-semibold' : '' }}">
            {{ __('My Quizzes') }}
        </a>
    @endif
@endauth
