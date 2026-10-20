<form method="POST" action="{{ route('logout') }}" class="genz-sidebar-logout-form">
    @csrf
    <button type="submit" class="genz-sidebar-logout">
        <span class="genz-sidebar-logout__icon" aria-hidden="true"><i class="fa-solid fa-right-from-bracket"></i></span>
        <span class="genz-sidebar-logout__label">{{ __('Logout') }}</span>
    </button>
</form>
